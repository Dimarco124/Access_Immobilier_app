<?php

namespace App\Services;

use App\Models\Contrat;
use App\Models\Facture;
use App\Models\Agence;
use App\Models\Message;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

class FinanceService
{
    public function __construct()
    {
        $this->ensureColumnsExist();
    }

    private function ensureColumnsExist()
    {
        if (!Schema::hasColumn('factures', 'montant_frais_retard')) {
            Schema::table('factures', function (Blueprint $table) {
                $table->decimal('montant_frais_retard', 15, 2)->default(0)->after('montant_total_du');
            });
        }
    }

    /**
     * Génère automatiquement les factures de loyer pour le mois en cours
     */
    public function generateMonthlyInvoices()
    {
        $contratsActifs = Contrat::where('statut', 'Actif')->get();
        $maintenant = Carbon::now();
        $moisActuelLabel = $maintenant->translatedFormat('F Y');
        
        foreach ($contratsActifs as $contrat) {
            $agence = $contrat->agence;
            $parametres = $agence->parametres_paiement ?? [];
            $jourEcheance = $parametres['jour_echeance_loyer'] ?? 5;
            
            // Calculer la fin de la période couverte par l'avance initiale
            // Exemple : Début 01/01 + 2 mois avance = Couvert jusqu'au 31/03. 
            // La prochaine facture doit être générée en Avril.
            $moisDeDebut = Carbon::parse($contrat->date_debut)->startOfMonth();
            $moisSuivantAvance = $moisDeDebut->copy()->addMonths(1 + $contrat->nombre_mois_avance);
            
            // Si on est encore dans la période couverte par l'avance, on ne génère rien
            if ($maintenant->startOfMonth()->lessThan($moisSuivantAvance)) {
                continue;
            }

            // Vérifier si une facture de loyer existe déjà pour ce mois spécifique
            $existe = Facture::where('contrat_id', $contrat->id)
                ->where('type_facture', 'Loyer')
                ->where('mois_facture', $moisActuelLabel)
                ->exists();

            if (!$existe) {
                $dateEcheance = $maintenant->copy()->day($jourEcheance);
                
                Facture::create([
                    'agence_id' => $contrat->agence_id,
                    'contrat_id' => $contrat->id,
                    'type_facture' => 'Loyer',
                    'numero_facture' => 'LOY-' . strtoupper(uniqid()),
                    'mois_facture' => $moisActuelLabel,
                    'montant_hors_taxe' => $contrat->montant_loyer_actuel,
                    'montant_tva' => ($contrat->montant_loyer_actuel * $agence->taux_tva) / 100,
                    'montant_total_du' => $contrat->montant_loyer_actuel * (1 + $agence->taux_tva / 100),
                    'montant_deja_paye' => 0,
                    'date_echeance' => $dateEcheance,
                    'statut' => 'Impayée',
                ]);
            }
        }
    }

    /**
     * Applique les frais de retard sur les factures impayées
     */
    public function applyLateFees()
    {
        $facturesEnRetard = Facture::whereIn('statut', ['Impayée', 'Partielle'])
            ->where('date_echeance', '<', Carbon::now())
            ->get();

        foreach ($facturesEnRetard as $facture) {
            $agence = $facture->agence;
            $parametres = $agence->parametres_paiement ?? [];
            $joursGrace = $parametres['jours_grace_retard'] ?? 3;
            $typeFrais = $parametres['frais_retard_type'] ?? 'pourcentage';
            $valeurFrais = $parametres['frais_retard_valeur'] ?? 0;

            $dateLimiteGrace = $facture->date_echeance->addDays($joursGrace);

            if (Carbon::now()->greaterThan($dateLimiteGrace)) {
                $nouveauxFrais = 0;
                if ($typeFrais === 'pourcentage') {
                    $nouveauxFrais = ($facture->montant_total_du * $valeurFrais) / 100;
                } else {
                    $nouveauxFrais = $valeurFrais;
                }

                // Appliquer les frais si ils ont changé (on peut les recalculer chaque jour ou une seule fois)
                // Ici on les applique une seule fois dès que le retard est constaté
                if ($facture->montant_frais_retard == 0 && $nouveauxFrais > 0) {
                    $facture->montant_frais_retard = $nouveauxFrais;
                    $facture->montant_total_du += $nouveauxFrais;
                    $facture->save();

                    // Notification au locataire
                    $this->notifyTenant($facture, "Des frais de retard de " . number_format($nouveauxFrais, 0, ',', ' ') . " " . $agence->devise . " ont été appliqués à votre facture de " . $facture->mois_facture . ".");
                }
            }
        }
    }

    /**
     * Envoie des rappels de paiement avant l'échéance
     */
    public function sendPaymentReminders()
    {
        $dansDeuxJours = Carbon::now()->addDays(2)->toDateString();
        
        $facturesProches = Facture::whereIn('statut', ['Impayée', 'Partielle'])
            ->whereDate('date_echeance', $dansDeuxJours)
            ->get();

        foreach ($facturesProches as $facture) {
            $this->notifyTenant($facture, "Rappel : Votre loyer de " . $facture->mois_facture . " arrive à échéance le " . $facture->date_echeance->format('d/m/Y') . ". Montant dû : " . number_format($facture->montant_total_du - $facture->montant_deja_paye, 0, ',', ' ') . " " . $facture->agence->devise . ".");
        }
    }

    private function notifyTenant(Facture $facture, $messageText)
    {
        $locataireUser = $facture->contrat->locataire->user;
        $adminUser = Agence::find($facture->agence_id)->users()->whereHas('roles', function($q){ $q->where('name', 'admin'); })->first();

        if ($locataireUser && $adminUser) {
            Message::create([
                'sender_id' => $adminUser->id,
                'receiver_id' => $locataireUser->id,
                'content' => $messageText,
                'is_read' => false,
            ]);
        }
    }
}
