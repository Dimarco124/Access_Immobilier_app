<?php

namespace App\Services;

use App\Models\Bien;
use App\Models\Contrat;
use App\Models\Depense;
use App\Models\Facture;
use App\Models\Locataire;
use App\Models\Paiement;
use App\Models\Proprietaire;
use App\Models\Ticket;
use App\Models\Transaction;
use App\Models\Visite;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    /**
     * Récupère les statistiques de base de l'agence
     */
    public function getBaseStats(int $agenceId): array
    {
        return [
            'total_familles' => \App\Models\FamilleDeBien::where('agence_id', $agenceId)->count(),
            'total_biens' => Bien::where('agence_id', $agenceId)->whereNotNull('famille_de_bien_id')->count(),
            'total_proprietaires' => Proprietaire::where('agence_id', $agenceId)->count(),
            'total_locataires' => Locataire::where('agence_id', $agenceId)->count(),
            'total_contrats' => Contrat::where('agence_id', $agenceId)->count(),
            'contrats_actifs' => Contrat::where('agence_id', $agenceId)->where('statut', 'Actif')->count(),
            'factures_impayees' => Facture::where('agence_id', $agenceId)->where('statut', 'Impayée')->count(),
            'tickets_ouverts' => Ticket::where('agence_id', $agenceId)->where('statut', '!=', 'Résolu')->count(),
        ];
    }

    /**
     * Calcule les flux financiers pour un mois donné
     */
    public function getFinancialFlows(int $agenceId, string $moisFormat, Carbon $dateFiltre): array
    {
        // Flux Loyers
        $fluxLoyersMois = $this->calculateRentFlows($agenceId, $moisFormat);
        $commissionLoyersMois = $this->calculateRentCommissions($agenceId, $moisFormat);

        // Flux Cautions
        $fluxCautionMois = $this->calculateDepositFlows($agenceId, $moisFormat);
        $totalCautionDetenue = $this->calculateTotalDepositsHeld($agenceId);
        $cautionsARendre = $this->getDepositsToReturn($agenceId);

        // Frais et Revenus Agence
        $fluxFraisDossierMois = $this->calculateFileFeesFlows($agenceId, $moisFormat);
        $caVisitesMois = $this->calculateVisitRevenues($agenceId, $dateFiltre);
        $caRetenuesCautionMois = $this->calculateDepositRetentions($agenceId, $dateFiltre);

        // Dépenses
        $depensesMois = $this->calculateMonthlyExpenses($agenceId, $dateFiltre);

        // Revenu réel agence
        $caReelAgence = $commissionLoyersMois + $caVisitesMois + $fluxFraisDossierMois + $caRetenuesCautionMois;

        return [
            'flux_loyers_mois' => $fluxLoyersMois,
            'commission_loyers_mois' => $commissionLoyersMois,
            'flux_caution_mois' => $fluxCautionMois,
            'total_caution_detenue' => $totalCautionDetenue,
            'cautions_a_rendre' => $cautionsARendre,
            'flux_frais_dossier_mois' => $fluxFraisDossierMois,
            'ca_visites_mois' => $caVisitesMois,
            'ca_retenues_caution_mois' => $caRetenuesCautionMois,
            'depenses_mois' => $depensesMois,
            'ca_reel_agence' => $caReelAgence,
        ];
    }

    /**
     * Calcule les flux de loyers
     */
    private function calculateRentFlows(int $agenceId, string $moisFormat): float
    {
        $fluxLoyers = 0;

        // Factures de type Loyer
        $fluxLoyers += Paiement::where('paiements.agence_id', $agenceId)
            ->join('factures', 'paiements.facture_id', '=', 'factures.id')
            ->where('factures.mois_facture', $moisFormat)
            ->where('factures.type_facture', 'Loyer')
            ->sum('paiements.montant_verse');

        // Factures Initiales (partie loyer)
        $facturesInitiales = Paiement::where('paiements.agence_id', $agenceId)
            ->join('factures', 'paiements.facture_id', '=', 'factures.id')
            ->where('factures.mois_facture', $moisFormat)
            ->where('factures.type_facture', 'Initial')
            ->select('factures.montant_loyer_initial', 'factures.montant_total_du', 'paiements.montant_verse')
            ->get();

        foreach ($facturesInitiales as $paiement) {
            if ($paiement->montant_total_du > 0 && $paiement->montant_loyer_initial > 0) {
                $proportion = $paiement->montant_loyer_initial / $paiement->montant_total_du;
                $fluxLoyers += $paiement->montant_verse * $proportion;
            }
        }

        return $fluxLoyers;
    }

    /**
     * Calcule les commissions sur loyers
     */
    private function calculateRentCommissions(int $agenceId, string $moisFormat): float
    {
        $commission = 0;

        // Commission sur loyers normaux
        $commission += Paiement::where('paiements.agence_id', $agenceId)
            ->join('factures', 'paiements.facture_id', '=', 'factures.id')
            ->join('contrats', 'factures.contrat_id', '=', 'contrats.id')
            ->join('biens', 'contrats.bien_id', '=', 'biens.id')
            ->where('factures.mois_facture', $moisFormat)
            ->where('factures.type_facture', 'Loyer')
            ->selectRaw('SUM(paiements.montant_verse * (biens.taux_commission_agence / 100)) as total_comm')
            ->value('total_comm') ?? 0;

        // Commission sur loyers initiaux
        $facturesInitiales = Paiement::where('paiements.agence_id', $agenceId)
            ->join('factures', 'paiements.facture_id', '=', 'factures.id')
            ->join('contrats', 'factures.contrat_id', '=', 'contrats.id')
            ->join('biens', 'contrats.bien_id', '=', 'biens.id')
            ->where('factures.mois_facture', $moisFormat)
            ->where('factures.type_facture', 'Initial')
            ->select('factures.montant_loyer_initial', 'factures.montant_total_du', 'paiements.montant_verse', 'biens.taux_commission_agence')
            ->get();

        foreach ($facturesInitiales as $paiement) {
            if ($paiement->montant_total_du > 0 && $paiement->montant_loyer_initial > 0) {
                $proportion = $paiement->montant_loyer_initial / $paiement->montant_total_du;
                $montantLoyerPaye = $paiement->montant_verse * $proportion;
                $commission += $montantLoyerPaye * ($paiement->taux_commission_agence / 100);
            }
        }

        return $commission;
    }

    /**
     * Calcule les flux de cautions
     */
    private function calculateDepositFlows(int $agenceId, string $moisFormat): float
    {
        $fluxCaution = 0;

        // Cautions factures séparées
        $fluxCaution += Paiement::where('paiements.agence_id', $agenceId)
            ->join('factures', 'paiements.facture_id', '=', 'factures.id')
            ->where('factures.mois_facture', $moisFormat)
            ->where('factures.type_facture', 'Caution')
            ->sum('paiements.montant_verse');

        // Cautions depuis factures Initiales
        $facturesInitiales = Paiement::where('paiements.agence_id', $agenceId)
            ->join('factures', 'paiements.facture_id', '=', 'factures.id')
            ->where('factures.mois_facture', $moisFormat)
            ->where('factures.type_facture', 'Initial')
            ->select('factures.montant_caution', 'factures.montant_total_du', 'paiements.montant_verse')
            ->get();

        foreach ($facturesInitiales as $paiement) {
            if ($paiement->montant_total_du > 0 && $paiement->montant_caution > 0) {
                $proportion = $paiement->montant_caution / $paiement->montant_total_du;
                $fluxCaution += $paiement->montant_verse * $proportion;
            }
        }

        return $fluxCaution;
    }

    /**
     * Calcule le total des cautions réellement détenues (uniquement encaissées)
     */
    private function calculateTotalDepositsHeld(int $agenceId): float
    {
        $contrats = Contrat::where('agence_id', $agenceId)
            ->with(['factures'])
            ->get();

        $totalDetenu = 0;
        foreach ($contrats as $contrat) {
            $factureInitiale = $contrat->factures->first(function ($f) {
                return in_array($f->type_facture, ['Initial', 'Caution']);
            });

            if ($factureInitiale) {
                if ($factureInitiale->statut === 'Payée') {
                    $cautionRecue = (float)$contrat->montant_caution;
                } elseif ($factureInitiale->montant_deja_paye > 0 && $factureInitiale->montant_total_du > 0) {
                    $ratio = (float)$contrat->montant_caution / (float)$factureInitiale->montant_total_du;
                    $cautionRecue = min((float)$contrat->montant_caution, (float)$factureInitiale->montant_deja_paye * $ratio);
                } else {
                    $cautionRecue = 0;
                }
            } else {
                $cautionRecue = 0;
            }

            $cautionDisponible = max(0, $cautionRecue - (float)$contrat->montant_caution_rembourse - (float)$contrat->montant_caution_retenu);
            $totalDetenu += $cautionDisponible;
        }

        return $totalDetenu;
    }

    /**
     * Récupère les cautions à rendre
     */
    private function getDepositsToReturn(int $agenceId)
    {
        return Contrat::where('agence_id', $agenceId)
            ->whereNotNull('date_fin')
            ->where('date_fin', '<=', now())
            ->whereColumn('montant_caution', '>', 'montant_caution_rembourse')
            ->with(['locataire.user', 'bien'])
            ->get()
            ->map(function ($contrat) {
                $contrat->a_rendre = $contrat->montant_caution - $contrat->montant_caution_rembourse - $contrat->montant_caution_retenu;
                return $contrat;
            })
            ->filter(function ($contrat) {
                return $contrat->a_rendre > 0;
            });
    }

    /**
     * Calcule les frais de dossier
     */
    private function calculateFileFeesFlows(int $agenceId, string $moisFormat): float
    {
        $fraisDossier = 0;

        // Frais Dossier factures séparées
        $fraisDossier += Paiement::where('paiements.agence_id', $agenceId)
            ->join('factures', 'paiements.facture_id', '=', 'factures.id')
            ->where('factures.mois_facture', $moisFormat)
            ->where('factures.type_facture', 'Frais Dossier')
            ->sum('paiements.montant_verse');

        // Frais Dossier depuis factures Initiales
        $facturesInitiales = Paiement::where('paiements.agence_id', $agenceId)
            ->join('factures', 'paiements.facture_id', '=', 'factures.id')
            ->where('factures.mois_facture', $moisFormat)
            ->where('factures.type_facture', 'Initial')
            ->select('factures.frais_dossier', 'factures.montant_total_du', 'paiements.montant_verse')
            ->get();

        foreach ($facturesInitiales as $paiement) {
            if ($paiement->montant_total_du > 0 && $paiement->frais_dossier > 0) {
                $proportion = $paiement->frais_dossier / $paiement->montant_total_du;
                $fraisDossier += $paiement->montant_verse * $proportion;
            }
        }

        return $fraisDossier;
    }

    /**
     * Calcule les revenus de visites
     */
    private function calculateVisitRevenues(int $agenceId, Carbon $date): float
    {
        return Visite::where('agence_id', $agenceId)
            ->whereMonth('created_at', $date->month)
            ->whereYear('created_at', $date->year)
            ->sum('montant_paye');
    }

    /**
     * Calcule les retenues sur caution
     */
    private function calculateDepositRetentions(int $agenceId, Carbon $date): float
    {
        return Transaction::where('agence_id', $agenceId)
            ->where('type_transaction', 'retenue_caution')
            ->whereMonth('date_transaction', $date->month)
            ->whereYear('date_transaction', $date->year)
            ->sum('montant');
    }

    /**
     * Calcule les dépenses du mois
     */
    private function calculateMonthlyExpenses(int $agenceId, Carbon $date): float
    {
        return Depense::where('agence_id', $agenceId)
            ->whereMonth('date_depense', $date->month)
            ->whereYear('date_depense', $date->year)
            ->sum('montant');
    }

    /**
     * Calcule les reversements aux propriétaires pour un mois
     */
    public function getOwnerReversals(int $agenceId, string $moisFormat): array
    {
        $reversementsArray = [];

        // Factures de type Loyer
        $paiementsLoyer = Paiement::where('paiements.agence_id', $agenceId)
            ->join('factures', 'paiements.facture_id', '=', 'factures.id')
            ->join('contrats', 'factures.contrat_id', '=', 'contrats.id')
            ->join('biens', 'contrats.bien_id', '=', 'biens.id')
            ->join('proprietaires', 'biens.proprietaire_id', '=', 'proprietaires.id')
            ->join('users', 'proprietaires.user_id', '=', 'users.id')
            ->where('factures.mois_facture', $moisFormat)
            ->where('factures.type_facture', 'Loyer')
            ->selectRaw('users.name as proprietaire, SUM(paiements.montant_verse * (1 - (biens.taux_commission_agence / 100))) as a_reverser')
            ->groupBy('users.name')
            ->get();

        foreach ($paiementsLoyer as $p) {
            $reversementsArray[$p->proprietaire] = ($reversementsArray[$p->proprietaire] ?? 0) + $p->a_reverser;
        }

        // Factures Initiales (partie loyer)
        $paiementsInitial = Paiement::where('paiements.agence_id', $agenceId)
            ->join('factures', 'paiements.facture_id', '=', 'factures.id')
            ->join('contrats', 'factures.contrat_id', '=', 'contrats.id')
            ->join('biens', 'contrats.bien_id', '=', 'biens.id')
            ->join('proprietaires', 'biens.proprietaire_id', '=', 'proprietaires.id')
            ->join('users', 'proprietaires.user_id', '=', 'users.id')
            ->where('factures.mois_facture', $moisFormat)
            ->where('factures.type_facture', 'Initial')
            ->select(
                'users.name as proprietaire',
                'factures.montant_loyer_initial',
                'factures.montant_total_du',
                'paiements.montant_verse',
                'biens.taux_commission_agence'
            )
            ->get();

        foreach ($paiementsInitial as $p) {
            if (!isset($reversementsArray[$p->proprietaire])) {
                $reversementsArray[$p->proprietaire] = 0;
            }
            if ($p->montant_total_du > 0 && $p->montant_loyer_initial > 0) {
                $proportion = $p->montant_loyer_initial / $p->montant_total_du;
                $montantLoyerPaye = $p->montant_verse * $proportion;
                $montantPourProprio = $montantLoyerPaye * (1 - ($p->taux_commission_agence / 100));
                $reversementsArray[$p->proprietaire] += $montantPourProprio;
            }
        }

        // Convertir en collection
        $reversementsCollection = collect();
        foreach ($reversementsArray as $nom => $montant) {
            $reversementsCollection->push((object)[
                'proprietaire' => $nom,
                'a_reverser' => $montant
            ]);
        }

        return [
            'reversements' => $reversementsCollection->sortByDesc('a_reverser')->values(),
            'total' => $reversementsCollection->sum('a_reverser'),
        ];
    }

    /**
     * Calcule le taux de recouvrement pour un mois
     */
    public function getCollectionRate(int $agenceId, string $moisFormat): float
    {
        $totalFacture = 0;
        $totalPaye = 0;

        // Factures de type Loyer
        $facturesLoyer = Facture::where('agence_id', $agenceId)
            ->where('mois_facture', $moisFormat)
            ->where('type_facture', 'Loyer')
            ->select('montant_total_du', 'montant_deja_paye')
            ->get();

        foreach ($facturesLoyer as $f) {
            $totalFacture += $f->montant_total_du;
            $totalPaye += $f->montant_deja_paye;
        }

        // Partie loyer des factures Initiales
        $facturesInitiales = Facture::where('agence_id', $agenceId)
            ->where('mois_facture', $moisFormat)
            ->where('type_facture', 'Initial')
            ->select('montant_total_du', 'montant_deja_paye', 'montant_loyer_initial')
            ->get();

        foreach ($facturesInitiales as $f) {
            if ($f->montant_total_du > 0 && $f->montant_loyer_initial > 0) {
                $proportion = $f->montant_loyer_initial / $f->montant_total_du;
                $totalFacture += $f->montant_loyer_initial;
                $totalPaye += $f->montant_deja_paye * $proportion;
            }
        }

        return $totalFacture > 0 ? round(($totalPaye / $totalFacture) * 100, 1) : 100;
    }

    /**
     * Calcule le taux d'occupation
     * Les biens louables sont tous les biens avec une famille
     * Les biens loués sont ceux avec statut "Loué"
     */
    public function getOccupancyRate(int $agenceId): array
    {
        $totalBiens = Bien::where('agence_id', $agenceId)
            ->whereNotNull('famille_de_bien_id')
            ->count();
            
        $biensLoues = Bien::where('agence_id', $agenceId)
            ->whereNotNull('famille_de_bien_id')
            ->where('statut', 'Loué')
            ->count();
            
        $tauxOccupation = $totalBiens > 0 ? round(($biensLoues / $totalBiens) * 100, 1) : 0;

        return [
            'total_biens' => $totalBiens,
            'biens_loues' => $biensLoues,
            'taux_occupation' => $tauxOccupation,
        ];
    }

    /**
     * Calcule le taux de conversion des visites
     */
    public function getConversionRate(int $agenceId, Carbon $date): array
    {
        $visitesTotal = Visite::where('agence_id', $agenceId)
            ->whereMonth('created_at', $date->month)
            ->whereYear('created_at', $date->year)
            ->count();

        $contratsSignes = Contrat::where('agence_id', $agenceId)
            ->whereMonth('created_at', $date->month)
            ->whereYear('created_at', $date->year)
            ->count();

        $tauxConversion = $visitesTotal > 0 ? round(($contratsSignes / $visitesTotal) * 100, 1) : 0;

        return [
            'visites_total' => $visitesTotal,
            'contrats_signes' => $contratsSignes,
            'taux_conversion' => $tauxConversion,
        ];
    }

    /**
     * Récupère la performance des tickets
     */
    public function getTicketsPerformance(int $agenceId): array
    {
        $ticketsResolus = Ticket::where('agence_id', $agenceId)
            ->where('statut', 'Résolu')
            ->get();

        $tempsResolutionTotal = 0;
        foreach ($ticketsResolus as $t) {
            $tempsResolutionTotal += $t->created_at->diffInDays($t->updated_at);
        }

        $tempsMoyenResolution = $ticketsResolus->count() > 0 
            ? round($tempsResolutionTotal / $ticketsResolus->count(), 1) 
            : 0;

        return [
            'temps_moyen_resolution' => $tempsMoyenResolution,
            'tickets_resolus' => $ticketsResolus->count(),
        ];
    }
}
