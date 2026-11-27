<?php

namespace App\Services;

use App\Models\Bien;
use App\Models\Depense;
use App\Models\FamilleDeBien;
use App\Models\Facture;
use App\Models\Paiement;
use App\Models\Proprietaire;
use App\Models\Reversement;
use App\Models\Ticket;
use Carbon\Carbon;

class OwnerDashboardService
{
    /**
     * Calcule la part strictement liée au LOYER d'un paiement (exclut caution et frais de dossier).
     */
    private function extractRentAmount(Paiement $paiement): float
    {
        $facture = $paiement->facture;
        if (!$facture) {
            return 0.0;
        }

        if ($facture->type_facture === 'Loyer') {
            return (float) $paiement->montant_verse;
        }

        if ($facture->type_facture === 'Initial') {
            $totalDu = (float) $facture->montant_total_du;
            $loyerInitial = (float) $facture->montant_loyer_initial;
            if ($totalDu > 0 && $loyerInitial > 0) {
                return (float) $paiement->montant_verse * ($loyerInitial / $totalDu);
            }
            return 0.0;
        }

        // Caution ou Frais Dossier purs : ne concernent pas les revenus du propriétaire
        return 0.0;
    }

    /**
     * Enrichit un paiement avec les montants liés au propriétaire (Loyer brut, Commission, Net).
     */
    private function enrichPaymentForOwner(Paiement $p): Paiement
    {
        $brutLoyer = $this->extractRentAmount($p);
        $tauxCommission = (float) ($p->facture->contrat->bien->taux_commission_agence ?? 0);
        $commission = $brutLoyer * ($tauxCommission / 100);

        $p->montant_loyer_brut = round($brutLoyer, 2);
        $p->commission_agence = round($commission, 2);
        $p->montant_net_proprietaire = round($brutLoyer - $commission, 2);

        return $p;
    }

    /**
     * Récupère les statistiques du propriétaire (exclusivement ses biens et ses loyers nets, sans cautions ni frais agence).
     */
    public function getOwnerStats(Proprietaire $proprietaire, ?Carbon $dateFiltre = null): array
    {
        $dateFiltre = $dateFiltre ?? Carbon::now();
        $moisFormat = $dateFiltre->translatedFormat('F Y');
        $moisLower = mb_strtolower($moisFormat);

        $totalBiens = Bien::where('proprietaire_id', $proprietaire->id)
            ->whereNotNull('famille_de_bien_id')
            ->count();

        $biensLoues = Bien::where('proprietaire_id', $proprietaire->id)
            ->whereNotNull('famille_de_bien_id')
            ->where('statut', 'Loué')
            ->count();

        // Tous les paiements liés aux biens du propriétaire
        $tousPaiements = Paiement::whereHas('facture.contrat.bien', function ($q) use ($proprietaire) {
            $q->where('proprietaire_id', $proprietaire->id);
        })
            ->with(['facture.contrat.bien'])
            ->get()
            ->map(fn ($p) => $this->enrichPaymentForOwner($p))
            ->filter(fn ($p) => $p->montant_loyer_brut > 0);

        // Paiements du mois sélectionné (Option B : strictement par mois_facture)
        $paiementsMois = $tousPaiements->filter(function ($p) use ($moisLower) {
            return mb_strtolower($p->facture->mois_facture ?? '') === $moisLower;
        });

        $loyersBrutsMois = (float) $paiementsMois->sum('montant_loyer_brut');
        $commissionsMois = (float) $paiementsMois->sum('commission_agence');

        // Dépenses imputées aux biens du propriétaire ce mois-ci
        $depensesMois = (float) Depense::whereHas('bien', function ($q) use ($proprietaire) {
            $q->where('proprietaire_id', $proprietaire->id);
        })
            ->whereMonth('date_depense', $dateFiltre->month)
            ->whereYear('date_depense', $dateFiltre->year)
            ->sum('montant');

        $netMois = max(0, $loyersBrutsMois - $commissionsMois - $depensesMois);

        // Reversements déjà effectués pour ce mois
        $reverseMois = (float) Reversement::where('proprietaire_id', $proprietaire->id)
            ->where(function ($q) use ($moisLower, $dateFiltre) {
                $q->whereRaw('LOWER(periode) = ?', [$moisLower])
                  ->orWhere(function ($sub) use ($dateFiltre) {
                      $sub->whereMonth('date_reversement', $dateFiltre->month)
                          ->whereYear('date_reversement', $dateFiltre->year);
                  });
            })
            ->sum('montant_net_verse');

        $resteAPercevoirMois = max(0, $netMois - $reverseMois);

        // Cumul global (tout temps)
        $totalBrutAll = (float) $tousPaiements->sum('montant_loyer_brut');
        $totalCommAll = (float) $tousPaiements->sum('commission_agence');
        $totalDepensesAll = (float) Depense::whereHas('bien', function ($q) use ($proprietaire) {
            $q->where('proprietaire_id', $proprietaire->id);
        })->sum('montant');
        $totalNetAll = max(0, $totalBrutAll - $totalCommAll - $totalDepensesAll);
        $totalReverseAll = (float) Reversement::where('proprietaire_id', $proprietaire->id)->sum('montant_net_verse');
        $soldeARecevoirGlobal = max(0, $totalNetAll - $totalReverseAll);

        // Loyers impayés / en attente chez les locataires (part loyer uniquement)
        $loyersEnAttenteLocataires = Facture::whereHas('contrat.bien', function ($q) use ($proprietaire) {
            $q->where('proprietaire_id', $proprietaire->id);
        })
            ->whereIn('type_facture', ['Loyer', 'Initial'])
            ->where('statut', '!=', 'Payée')
            ->get()
            ->sum(function ($f) {
                $reste = max(0, (float) $f->montant_total_du - (float) $f->montant_deja_paye);
                if ($f->type_facture === 'Loyer') {
                    return $reste;
                }
                if ($f->montant_total_du > 0 && $f->montant_loyer_initial > 0) {
                    return $reste * ($f->montant_loyer_initial / $f->montant_total_du);
                }
                return 0;
            });

        return [
            'total_familles' => FamilleDeBien::where('proprietaire_id', $proprietaire->id)->count(),
            'total_biens' => $totalBiens,
            'total_unites' => $biensLoues,
            // Données du mois sélectionné
            'loyers_bruts_mois' => round($loyersBrutsMois),
            'commissions_mois' => round($commissionsMois),
            'depenses_mois' => round($depensesMois),
            'net_mois' => round($netMois),
            'reverse_mois' => round($reverseMois),
            'reste_a_percevoir_mois' => round($resteAPercevoirMois),
            // Données globales (strictement loyers du propriétaire)
            'total_loyers_percus' => round($totalNetAll),
            'total_reverse' => round($totalReverseAll),
            'solde_a_recevoir' => round($soldeARecevoirGlobal),
            'loyers_en_attente' => round($loyersEnAttenteLocataires),
        ];
    }

    /**
     * Récupère les derniers paiements de loyers du propriétaire (part loyer uniquement).
     */
    public function getRecentPayments(Proprietaire $proprietaire, int $limit = 15)
    {
        return Paiement::whereHas('facture.contrat.bien', function ($q) use ($proprietaire) {
            $q->where('proprietaire_id', $proprietaire->id);
        })
            ->with(['facture.contrat.locataire.user', 'facture.contrat.bien'])
            ->latest()
            ->take($limit * 2)
            ->get()
            ->map(fn ($p) => $this->enrichPaymentForOwner($p))
            ->filter(fn ($p) => $p->montant_loyer_brut > 0)
            ->take($limit)
            ->values();
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
     * Récupère les familles de biens avec leurs revenus nets de loyers (sans cautions ni frais de dossier)
     */
    public function getFamiliesWithRevenues(Proprietaire $proprietaire)
    {
        return FamilleDeBien::where('proprietaire_id', $proprietaire->id)
            ->with(['biens'])
            ->get()
            ->map(function ($famille) {
                $paiements = Paiement::whereHas('facture.contrat.bien', function ($q) use ($famille) {
                    $q->where('famille_de_bien_id', $famille->id);
                })
                    ->with(['facture.contrat.bien'])
                    ->get()
                    ->map(fn ($p) => $this->enrichPaymentForOwner($p))
                    ->filter(fn ($p) => $p->montant_loyer_brut > 0);

                $famille->revenus_total = round($paiements->sum('montant_net_proprietaire'));
                return $famille;
            });
    }

    /**
     * Génère le rapport financier du propriétaire (strictement ses loyers, commissions sur loyers, dépenses et reversements).
     */
    public function getFinanceReport(Proprietaire $proprietaire): array
    {
        // Tous les paiements liés aux biens du propriétaire (part loyer uniquement)
        $paiements = Paiement::whereHas('facture.contrat.bien', function ($q) use ($proprietaire) {
            $q->where('proprietaire_id', $proprietaire->id);
        })
            ->with(['facture.contrat.bien', 'facture.contrat.locataire.user'])
            ->latest()
            ->get()
            ->map(fn ($p) => $this->enrichPaymentForOwner($p))
            ->filter(fn ($p) => $p->montant_loyer_brut > 0)
            ->values();

        // Dépenses imputées aux biens du propriétaire
        $depenses = Depense::whereHas('bien', function ($q) use ($proprietaire) {
            $q->where('proprietaire_id', $proprietaire->id);
        })
            ->with(['bien'])
            ->latest('date_depense')
            ->get();

        // Reversements reçus par le propriétaire
        $reversements = Reversement::where('proprietaire_id', $proprietaire->id)
            ->latest('date_reversement')
            ->get();

        $totalBrut = (float) $paiements->sum('montant_loyer_brut');
        $totalComm = (float) $paiements->sum('commission_agence');
        $totalDepenses = (float) $depenses->sum('montant');
        $totalNet = max(0, $totalBrut - $totalComm - $totalDepenses);
        $totalReverse = (float) $reversements->sum('montant_net_verse');
        $soldeARecevoir = max(0, $totalNet - $totalReverse);

        $totals = [
            'brut' => round($totalBrut),
            'commission' => round($totalComm),
            'depenses' => round($totalDepenses),
            'net' => round($totalNet),
            'reverse' => round($totalReverse),
            'solde_a_recevoir' => round($soldeARecevoir),
        ];

        // Graphiques 12 mois (Option B : par mois_facture)
        $moisLabels = [];
        $revenusMensuels = [];
        $commissionsMensuelles = [];
        $netsMensuels = [];

        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $moisFormat = $date->translatedFormat('F Y');
            $moisLower = mb_strtolower($moisFormat);
            $moisLabels[] = $moisFormat;

            $paiementsMois = $paiements->filter(function ($p) use ($moisLower) {
                return mb_strtolower($p->facture->mois_facture ?? '') === $moisLower;
            });

            $revenusMensuels[] = floatval($paiementsMois->sum('montant_loyer_brut'));
            $commissionsMensuelles[] = floatval($paiementsMois->sum('commission_agence'));
            $netsMensuels[] = floatval($paiementsMois->sum('montant_net_proprietaire'));
        }

        // Groupement par contrat
        $paiementsGroupes = $paiements->groupBy('facture.contrat_id');

        return [
            'paiementsGroupes' => $paiementsGroupes,
            'totals' => $totals,
            'moisLabels' => $moisLabels,
            'revenusMensuels' => $revenusMensuels,
            'commissionsMensuelles' => $commissionsMensuelles,
            'netsMensuels' => $netsMensuels,
            'reversements' => $reversements,
            'depenses' => $depenses,
        ];
    }
}
