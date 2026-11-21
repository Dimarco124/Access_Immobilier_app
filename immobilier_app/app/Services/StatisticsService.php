<?php

namespace App\Services;

use App\Models\Depense;
use App\Models\Facture;
use App\Models\Paiement;
use App\Models\Visite;
use Carbon\Carbon;

class StatisticsService
{
    /**
     * Génère les données pour le graphique revenus vs dépenses (12 mois)
     */
    public function getRevenueExpenseChart(int $agenceId): array
    {
        $revenus_mensuels = [];
        $depenses_mensuels = [];
        $commissions_mensuelles = [];
        $mois_labels = [];

        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $mois_labels[] = $date->translatedFormat('M Y');
            $moisFormat = $date->translatedFormat('F Y');

            // Calculer revenus et commissions
            $result = $this->calculateMonthlyRevenue($agenceId, $moisFormat, $date);
            $revenus_mensuels[] = $result['revenus'];
            $commissions_mensuelles[] = $result['commissions'];

            // Calculer dépenses
            $depenses_mensuels[] = Depense::where('agence_id', $agenceId)
                ->whereMonth('date_depense', $date->month)
                ->whereYear('date_depense', $date->year)
                ->sum('montant');
        }

        return [
            'mois_labels' => $mois_labels,
            'revenus_mensuels' => $revenus_mensuels,
            'commissions_mensuelles' => $commissions_mensuelles,
            'depenses_mensuels' => $depenses_mensuels,
        ];
    }

    /**
     * Calcule les revenus d'un mois
     */
    private function calculateMonthlyRevenue(int $agenceId, string $moisFormat, Carbon $date): array
    {
        $fluxLoyers = 0;
        $commLoyers = 0;
        $fraisDossier = 0;

        // Factures de type Loyer
        $fluxLoyers += Paiement::where('paiements.agence_id', $agenceId)
            ->join('factures', 'paiements.facture_id', '=', 'factures.id')
            ->where('factures.mois_facture', $moisFormat)
            ->where('factures.type_facture', 'Loyer')
            ->sum('paiements.montant_verse');

        $commLoyers += Paiement::where('paiements.agence_id', $agenceId)
            ->join('factures', 'paiements.facture_id', '=', 'factures.id')
            ->join('contrats', 'factures.contrat_id', '=', 'contrats.id')
            ->join('biens', 'contrats.bien_id', '=', 'biens.id')
            ->where('factures.mois_facture', $moisFormat)
            ->where('factures.type_facture', 'Loyer')
            ->selectRaw('SUM(paiements.montant_verse * (biens.taux_commission_agence / 100)) as total_comm')
            ->value('total_comm') ?? 0;

        // Factures Initiales
        $facturesInitiales = Paiement::where('paiements.agence_id', $agenceId)
            ->join('factures', 'paiements.facture_id', '=', 'factures.id')
            ->join('contrats', 'factures.contrat_id', '=', 'contrats.id')
            ->join('biens', 'contrats.bien_id', '=', 'biens.id')
            ->where('factures.mois_facture', $moisFormat)
            ->where('factures.type_facture', 'Initial')
            ->select(
                'paiements.montant_verse',
                'factures.montant_total_du',
                'factures.montant_loyer_initial',
                'factures.frais_dossier',
                'biens.taux_commission_agence'
            )
            ->get();

        foreach ($facturesInitiales as $paiement) {
            if ($paiement->montant_total_du > 0) {
                // Partie loyer
                if ($paiement->montant_loyer_initial > 0) {
                    $propLoyer = $paiement->montant_loyer_initial / $paiement->montant_total_du;
                    $montantLoyer = $paiement->montant_verse * $propLoyer;
                    $fluxLoyers += $montantLoyer;
                    $commLoyers += $montantLoyer * ($paiement->taux_commission_agence / 100);
                }
                // Partie frais de dossier
                if ($paiement->frais_dossier > 0) {
                    $propFrais = $paiement->frais_dossier / $paiement->montant_total_du;
                    $fraisDossier += $paiement->montant_verse * $propFrais;
                }
            }
        }

        // Frais de visites
        $visites = Visite::where('agence_id', $agenceId)
            ->whereMonth('created_at', $date->month)
            ->whereYear('created_at', $date->year)
            ->sum('montant_paye');

        return [
            'revenus' => $fluxLoyers + $fraisDossier + $visites,
            'commissions' => $commLoyers + $fraisDossier + $visites,
        ];
    }

    /**
     * Répartition du CA par type de bien
     */
    public function getRevenueByPropertyType(int $agenceId, string $moisFormat): array
    {
        return Paiement::where('paiements.agence_id', $agenceId)
            ->join('factures', 'paiements.facture_id', '=', 'factures.id')
            ->join('contrats', 'factures.contrat_id', '=', 'contrats.id')
            ->join('biens', 'contrats.bien_id', '=', 'biens.id')
            ->where('factures.mois_facture', $moisFormat)
            ->whereIn('factures.type_facture', ['Loyer', 'Frais Dossier', 'Caution', 'Initial'])
            ->selectRaw('biens.type_bien, SUM(paiements.montant_verse) as total')
            ->groupBy('biens.type_bien')
            ->orderByDesc('total')
            ->pluck('total', 'type_bien')
            ->toArray();
    }

    /**
     * Répartition du parc immobilier
     */
    public function getPropertyDistribution(int $agenceId, int $totalBiens, int $biensLoues): array
    {
        return [
            'loues' => $biensLoues,
            'disponibles' => $totalBiens - $biensLoues,
        ];
    }

    /**
     * Performance des paiements
     */
    public function getPaymentPerformance(int $agenceId): array
    {
        return [
            'payees' => Facture::where('agence_id', $agenceId)->where('statut', 'Payée')->count(),
            'partielles' => Facture::where('agence_id', $agenceId)->where('statut', 'Partielle')->count(),
            'impayees' => Facture::where('agence_id', $agenceId)->where('statut', 'Impayée')->count(),
        ];
    }

    /**
     * Sparklines (7 derniers jours)
     */
    public function getSparklines(int $agenceId, Carbon $dateFiltre, string $moisFormat): array
    {
        $sparkline_ca = [];
        $sparkline_depenses = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = $dateFiltre->copy()->endOfMonth()->subDays($i);

            $sparkline_ca[] = Paiement::where('paiements.agence_id', $agenceId)
                ->join('factures', 'paiements.facture_id', '=', 'factures.id')
                ->where('factures.mois_facture', $moisFormat)
                ->whereDate('paiements.date_paiement', $date->toDateString())
                ->sum('paiements.montant_verse')
                + Visite::where('agence_id', $agenceId)
                ->whereMonth('created_at', $dateFiltre->month)
                ->whereYear('created_at', $dateFiltre->year)
                ->whereDate('created_at', $date->toDateString())
                ->sum('montant_paye');

            $sparkline_depenses[] = Depense::where('agence_id', $agenceId)
                ->whereMonth('date_depense', $dateFiltre->month)
                ->whereYear('date_depense', $dateFiltre->year)
                ->whereDate('date_depense', $date->toDateString())
                ->sum('montant');
        }

        return [
            'sparkline_ca' => $sparkline_ca,
            'sparkline_depenses' => $sparkline_depenses,
        ];
    }

    /**
     * Liste noire : Top 5 locataires avec le plus d'impayés
     */
    public function getBlacklist(int $agenceId)
    {
        return Facture::where('factures.agence_id', $agenceId)
            ->where('factures.statut', '!=', 'Payée')
            ->join('contrats', 'factures.contrat_id', '=', 'contrats.id')
            ->join('locataires', 'contrats.locataire_id', '=', 'locataires.id')
            ->join('users', 'locataires.user_id', '=', 'users.id')
            ->join('biens', 'contrats.bien_id', '=', 'biens.id')
            ->selectRaw('contrats.id as contrat_id, users.name, users.telephone, biens.nom_bien, COUNT(factures.id) as nb_factures, SUM(factures.montant_total_du - factures.montant_deja_paye) as dette_totale')
            ->groupBy('contrats.id', 'users.name', 'users.telephone', 'biens.nom_bien')
            ->orderByDesc('dette_totale')
            ->take(5)
            ->get();
    }

    /**
     * Derniers paiements
     */
    public function getRecentPayments(int $agenceId, string $moisFormat)
    {
        return Paiement::where('paiements.agence_id', $agenceId)
            ->join('factures', 'paiements.facture_id', '=', 'factures.id')
            ->where('factures.mois_facture', $moisFormat)
            ->with(['facture.contrat.locataire.user'])
            ->latest('date_paiement')
            ->take(5)
            ->get();
    }
}
