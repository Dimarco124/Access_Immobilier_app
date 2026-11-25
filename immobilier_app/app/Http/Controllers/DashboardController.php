<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use App\Services\OwnerDashboardService;
use App\Services\StatisticsService;
use App\Services\TenantDashboardService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    protected $dashboardService;
    protected $statisticsService;
    protected $tenantDashboardService;
    protected $ownerDashboardService;

    public function __construct(
        DashboardService $dashboardService,
        StatisticsService $statisticsService,
        TenantDashboardService $tenantDashboardService,
        OwnerDashboardService $ownerDashboardService
    ) {
        $this->dashboardService = $dashboardService;
        $this->statisticsService = $statisticsService;
        $this->tenantDashboardService = $tenantDashboardService;
        $this->ownerDashboardService = $ownerDashboardService;
    }

    public function index()
    {
        $user = Auth::user();
        $agenceId = $user->agence_id;

        // Dashboards spécifiques
        if ($user->hasRole('locataire')) {
            return $this->tenantDashboard($user);
        }

        if ($user->hasRole('proprietaire')) {
            return $this->ownerDashboard($user);
        }

        if ($user->hasRole('team_member')) {
            $userModules = getUserModules();
            return $this->teamMemberDashboard($user, $agenceId, $userModules);
        }

        // Dashboard Admin
        return $this->adminDashboard($user, $agenceId);
    }

    /**
     * Dashboard Admin
     */
    private function adminDashboard($user, int $agenceId, array $userModules = [])
    {
        if ($user->hasRole('admin')) {
            $userModules = ['immobilier', 'commercial', 'finance', 'maintenance', 'relation_client', 'proprietaires'];
        }

        // Gestion du filtre par mois
        $moisSelectionne = request('mois');
        $dateFiltre = $moisSelectionne ? Carbon::parse($moisSelectionne) : now();
        $moisFormat = $dateFiltre->translatedFormat('F Y');
        $moisInputValue = $dateFiltre->format('Y-m');

        // Liste des mois pour le sélecteur
        $listeMois = [];
        for ($i = 0; $i < 24; $i++) {
            $d = now()->subMonths($i);
            $listeMois[] = [
                'value' => $d->format('Y-m'),
                'label' => $d->translatedFormat('F Y')
            ];
        }

        // 1. Statistiques de base
        $baseStats = $this->dashboardService->getBaseStats($agenceId);

        // 2. Flux financiers
        $financialFlows = $this->dashboardService->getFinancialFlows($agenceId, $moisFormat, $dateFiltre);

        // 3. Reversements propriétaires
        $reversements = $this->dashboardService->getOwnerReversals($agenceId, $moisFormat, $dateFiltre);

        // 4. Taux de recouvrement
        $tauxRecouvrement = $this->dashboardService->getCollectionRate($agenceId, $moisFormat, $dateFiltre);

        // 5. Taux d'occupation
        $occupation = $this->dashboardService->getOccupancyRate($agenceId);

        // 6. Taux de conversion
        $conversion = $this->dashboardService->getConversionRate($agenceId, $dateFiltre);

        // 7. Performance tickets
        $ticketsPerf = $this->dashboardService->getTicketsPerformance($agenceId);

        // 8. Graphiques et statistiques
        $chartData = $this->statisticsService->getRevenueExpenseChart($agenceId);
        $caParType = $this->statisticsService->getRevenueByPropertyType($agenceId, $moisFormat, $dateFiltre);
        $repartition_parc = $this->statisticsService->getPropertyDistribution(
            $agenceId,
            $occupation['total_biens'],
            $occupation['biens_loues']
        );
        $performance_paiement = $this->statisticsService->getPaymentPerformance($agenceId);
        $sparklines = $this->statisticsService->getSparklines($agenceId, $dateFiltre, $moisFormat);
        $listeNoire = $this->statisticsService->getBlacklist($agenceId);
        $derniers_paiements = $this->statisticsService->getRecentPayments($agenceId, $moisFormat, $dateFiltre);

        // Compilation des stats
        $stats = array_merge($baseStats, [
            'total_sous_biens' => 0, // Ancien champ non utilisé (pour compatibilité)
            'ca_mensuel' => $financialFlows['ca_reel_agence'],
            'ca_loyers_mois' => $financialFlows['commission_loyers_mois'],
            'ca_frais_dossier_mois' => $financialFlows['flux_frais_dossier_mois'],
            'ca_visites_mois' => $financialFlows['ca_visites_mois'],
            'ca_retenues_caution_mois' => $financialFlows['ca_retenues_caution_mois'],
            'flux_loyers_mois' => $financialFlows['flux_loyers_mois'],
            'flux_caution_mois' => $financialFlows['flux_caution_mois'],
            'total_caution_detenue' => $financialFlows['total_caution_detenue'],
            'depenses_mois' => $financialFlows['depenses_mois'],
            'total_a_reverser' => $reversements['total'],
            'taux_recouvrement' => $tauxRecouvrement,
            'taux_occupation' => $occupation['taux_occupation'],
            'total_unites' => $occupation['total_biens'], // Renommé mais garde la clé pour compatibilité vue
            'total_louees' => $occupation['biens_loues'], // Renommé mais garde la clé pour compatibilité vue
            'taux_conversion' => $conversion['taux_conversion'],
            'visites_mois' => $conversion['visites_total'],
            'contrats_signes_mois' => $conversion['contrats_signes'],
            'temps_moyen_resolution' => $ticketsPerf['temps_moyen_resolution'],
        ]);

        // Variables pour la vue
        $reversementsProprietaire = $reversements['reversements'];
        $cautionsARendre = $financialFlows['cautions_a_rendre'];

        return view('dashboard', compact(
            'stats',
            'derniers_paiements',
            'reversementsProprietaire',
            'listeMois',
            'moisInputValue',
            'moisFormat',
            'cautionsARendre',
            'repartition_parc',
            'performance_paiement',
            'caParType',
            'listeNoire',
            'userModules'
        ))->with([
            'revenus_mensuels' => $chartData['revenus_mensuels'],
            'commissions_mensuelles' => $chartData['commissions_mensuelles'],
            'depenses_mensuels' => $chartData['depenses_mensuels'],
            'mois_labels' => $chartData['mois_labels'],
            'sparkline_ca' => $sparklines['sparkline_ca'],
            'sparkline_depenses' => $sparklines['sparkline_depenses'],
        ]);
    }

    private function teamMemberDashboard($user, int $agenceId, array $userModules)
    {
        return $this->adminDashboard($user, $agenceId, $userModules);
    }

    /**
     * Dashboard Locataire
     */
    private function tenantDashboard($user)
    {
        $locataire = $user->locataire;

        $stats = $this->tenantDashboardService->getTenantStats($locataire);
        $contratActif = $this->tenantDashboardService->getActiveContract($locataire);
        $prochaineFacture = $this->tenantDashboardService->getNextInvoice($locataire);
        $derniersPaiements = $this->tenantDashboardService->getRecentPayments($locataire);
        $ticketsEnCours = $this->tenantDashboardService->getOpenTickets($locataire);

        return view('dashboard_tenant', compact(
            'stats',
            'contratActif',
            'prochaineFacture',
            'derniersPaiements',
            'ticketsEnCours'
        ));
    }

    /**
     * Dashboard Propriétaire
     */
    private function ownerDashboard($user)
    {
        $proprietaire = $user->proprietaire;

        $stats = $this->ownerDashboardService->getOwnerStats($proprietaire);
        $derniers_paiements = $this->ownerDashboardService->getRecentPayments($proprietaire);
        $tickets_en_cours = $this->ownerDashboardService->getOpenTickets($proprietaire);
        $mes_familles = $this->ownerDashboardService->getFamiliesWithRevenues($proprietaire);

        return view('dashboard_owner', compact(
            'stats',
            'derniers_paiements',
            'mes_familles',
            'tickets_en_cours'
        ));
    }

    /**
     * Rapport financier propriétaire
     */
    public function ownerFinanceReport()
    {
        $user = Auth::user();
        if (!$user->hasRole('proprietaire')) abort(403);

        $proprietaire = $user->proprietaire;
        $reportData = $this->ownerDashboardService->getFinanceReport($proprietaire);

        return view('proprietaires.report', $reportData);
    }

    /**
     * Rapport financier agence (Admin)
     */
    public function agencyFinanceReport()
    {
        $user = Auth::user();
        if (!$user->hasRole('admin')) abort(403);

        // Cette méthode peut rester inchangée ou être refactorisée plus tard
        $agenceId = $user->agence_id;

        $paiements = \App\Models\Paiement::where('agence_id', $agenceId)
            ->with(['facture.contrat.bien', 'facture.contrat.locataire.user'])
            ->latest()
            ->get()
            ->map(function ($p) {
                $tauxCommission = $p->facture->contrat->bien->taux_commission_agence ?? 0;
                $p->commission_agence = ($p->montant_verse * $tauxCommission) / 100;
                $p->montant_net_proprietaire = $p->montant_verse - $p->commission_agence;
                return $p;
            });

        $totals = [
            'chiffre_affaires' => $paiements->sum('montant_verse') + \App\Models\Visite::where('agence_id', $agenceId)->sum('montant_paye'),
            'commissions_loyers' => $paiements->sum('commission_agence'),
            'frais_visites' => \App\Models\Visite::where('agence_id', $agenceId)->sum('montant_paye'),
            'commissions_total' => $paiements->sum('commission_agence') + \App\Models\Visite::where('agence_id', $agenceId)->sum('montant_paye'),
            'nombre_paiements' => $paiements->count(),
        ];

        $paiementsParMois = $paiements->groupBy(function ($p) {
            return $p->date_paiement->format('F Y');
        });

        return view('agence.report', compact('paiementsParMois', 'totals'));
    }

    /**
     * Rapport financier locataire
     */
    public function tenantFinanceReport()
    {
        $user = Auth::user();
        if (!$user->hasRole('locataire')) abort(403);

        $locataire = $user->locataire;
        $reportData = $this->tenantDashboardService->getFinanceReport($locataire);

        return view('locataires.report', $reportData);
    }
}
