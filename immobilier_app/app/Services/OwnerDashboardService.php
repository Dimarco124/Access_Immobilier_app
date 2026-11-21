<?php

namespace App\Services;

use App\Models\Bien;
use App\Models\FamilleDeBien;
use App\Models\Facture;
use App\Models\Paiement;
use App\Models\Proprietaire;
use App\Models\Reversement;
use App\Models\Ticket;

class OwnerDashboardService
{
    /**
     * Récupère les statistiques du propriétaire
     */
    public function getOwnerStats(Proprietaire $proprietaire): array
    {
        $totalBiens = Bien::where('proprietaire_id', $proprietaire->id)
            ->whereNotNull('famille_de_bien_id')
            ->count();

        $biensLoues = Bien::where('proprietaire_id', $proprietaire->id)
            ->whereNotNull('famille_de_bien_id')
            ->where('statut', 'Loué')
            ->count();

        return [
            'total_familles' => FamilleDeBien::where('proprietaire_id', $proprietaire->id)->count(),
            'total_biens' => $totalBiens,
            'total_unites' => $biensLoues, // Pour compatibilité
            'total_loyers_percus' => Paiement::whereHas('facture.contrat.bien', function ($q) use ($proprietaire) {
                $q->where('proprietaire_id', $proprietaire->id);
            })->sum('montant_verse'),
            'loyers_en_attente' => Facture::whereHas('contrat.bien', function ($q) use ($proprietaire) {
                $q->where('proprietaire_id', $proprietaire->id);
            })->where('statut', '!=', 'Payée')
                ->get()
                ->sum(function ($f) {
                    return $f->montant_total_du - $f->montant_deja_paye;
                }),
        ];
    }

    /**
     * Récupère les derniers paiements du propriétaire
     */
    public function getRecentPayments(Proprietaire $proprietaire, int $limit = 15)
    {
        return Paiement::whereHas('facture.contrat.bien', function ($q) use ($proprietaire) {
            $q->where('proprietaire_id', $proprietaire->id);
        })
            ->with(['facture.contrat.locataire.user', 'facture.contrat.bien'])
            ->latest()
            ->take($limit)
            ->get();
    }

    /**
     * Récupère les tickets en cours sur les biens du propriétaire
     */
    public function getOpenTickets(Proprietaire $proprietaire, int $limit = 5)
    {
        $bienIds = Bien::where('proprietaire_id', $proprietaire->id)->pluck('id');

        return Ticket::whereIn('bien_id', $bienIds)
            ->where('statut', '!=', 'Résolu')
            ->with(['locataire.user', 'bien'])
            ->latest()
            ->take($limit)
            ->get();
    }

    /**
     * Récupère les familles de biens avec revenus
     */
    public function getFamiliesWithRevenues(Proprietaire $proprietaire)
    {
        return FamilleDeBien::where('proprietaire_id', $proprietaire->id)
            ->with(['biens'])
            ->get()
            ->map(function ($famille) {
                $famille->revenus_total = Paiement::whereHas('facture.contrat.bien', function ($q) use ($famille) {
                    $q->where('famille_de_bien_id', $famille->id);
                })->sum('montant_verse');
                return $famille;
            });
    }

    /**
     * Génère le rapport financier du propriétaire
     */
    public function getFinanceReport(Proprietaire $proprietaire): array
    {
        // Tous les paiements liés aux biens du propriétaire
        $paiements = Paiement::whereHas('facture.contrat.bien', function ($q) use ($proprietaire) {
            $q->where('proprietaire_id', $proprietaire->id);
        })
            ->with(['facture.contrat.bien', 'facture.contrat.locataire.user'])
            ->latest()
            ->get()
            ->map(function ($p) {
                // Calcul de la commission agence
                $tauxCommission = $p->facture->contrat->bien->taux_commission_agence ?? 0;
                $p->commission_agence = ($p->montant_verse * $tauxCommission) / 100;
                $p->montant_net_proprietaire = $p->montant_verse - $p->commission_agence;
                return $p;
            });

        // Totaux
        $totals = [
            'brut' => $paiements->sum('montant_verse'),
            'commission' => $paiements->sum('commission_agence'),
            'net' => $paiements->sum('montant_net_proprietaire'),
        ];

        // Graphiques 12 mois
        $moisLabels = [];
        $revenusMensuels = [];
        $commissionsMensuelles = [];
        $netsMensuels = [];

        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $moisLabels[] = $date->translatedFormat('F Y');

            $paiementsMois = $paiements->filter(function ($p) use ($date) {
                return $p->date_paiement->year == $date->year 
                    && $p->date_paiement->month == $date->month;
            });

            $revenusMensuels[] = floatval($paiementsMois->sum('montant_verse'));
            $commissionsMensuelles[] = floatval($paiementsMois->sum('commission_agence'));
            $netsMensuels[] = floatval($paiementsMois->sum('montant_net_proprietaire'));
        }

        // Groupement par contrat
        $paiementsGroupes = $paiements->groupBy('facture.contrat_id');

        // Reversements
        $reversements = Reversement::where('proprietaire_id', $proprietaire->id)
            ->latest()
            ->get();

        return [
            'paiementsGroupes' => $paiementsGroupes,
            'totals' => $totals,
            'moisLabels' => $moisLabels,
            'revenusMensuels' => $revenusMensuels,
            'commissionsMensuelles' => $commissionsMensuelles,
            'netsMensuels' => $netsMensuels,
            'reversements' => $reversements,
        ];
    }
}
