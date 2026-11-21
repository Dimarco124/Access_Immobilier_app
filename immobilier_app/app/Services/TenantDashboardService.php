<?php

namespace App\Services;

use App\Models\Facture;
use App\Models\Locataire;
use App\Models\Paiement;
use App\Models\Ticket;

class TenantDashboardService
{
    /**
     * Récupère les statistiques du locataire
     */
    public function getTenantStats(Locataire $locataire): array
    {
        return [
            'contrats_actifs' => $locataire->contrats()->where('statut', 'Actif')->count(),
            'factures_a_payer' => Facture::whereHas('contrat', function ($q) use ($locataire) {
                $q->where('locataire_id', $locataire->id);
            })->where('statut', '!=', 'Payée')->count(),
            'tickets_en_cours' => Ticket::where('locataire_id', $locataire->id)
                ->where('statut', '!=', 'Résolu')
                ->count(),
            'total_paye' => Facture::whereHas('contrat', function ($q) use ($locataire) {
                $q->where('locataire_id', $locataire->id);
            })->sum('montant_deja_paye'),
        ];
    }

    /**
     * Récupère le contrat actif du locataire
     */
    public function getActiveContract(Locataire $locataire)
    {
        return $locataire->contrats()->where('statut', 'Actif')->latest()->first();
    }

    /**
     * Récupère la prochaine facture à payer
     */
    public function getNextInvoice(Locataire $locataire)
    {
        $contratActif = $this->getActiveContract($locataire);

        if (!$contratActif) {
            return null;
        }

        return Facture::where('contrat_id', $contratActif->id)
            ->where('statut', '!=', 'Payée')
            ->orderBy('date_echeance')
            ->first();
    }

    /**
     * Récupère les derniers paiements du locataire
     */
    public function getRecentPayments(Locataire $locataire, int $limit = 5)
    {
        return Paiement::whereHas('facture.contrat', function ($q) use ($locataire) {
            $q->where('locataire_id', $locataire->id);
        })
            ->with('facture.contrat.bien')
            ->latest()
            ->take($limit)
            ->get();
    }

    /**
     * Récupère les tickets en cours du locataire
     */
    public function getOpenTickets(Locataire $locataire, int $limit = 3)
    {
        return Ticket::where('locataire_id', $locataire->id)
            ->where('statut', '!=', 'Résolu')
            ->latest()
            ->take($limit)
            ->get();
    }

    /**
     * Génère le rapport financier du locataire
     */
    public function getFinanceReport(Locataire $locataire): array
    {
        // Toutes les factures
        $factures = Facture::whereHas('contrat', function ($q) use ($locataire) {
            $q->where('locataire_id', $locataire->id);
        })
            ->with(['paiements', 'contrat.bien'])
            ->latest()
            ->get();

        // Statistiques
        $stats = [
            'total_paye' => $factures->sum('montant_deja_paye'),
            'total_du' => $factures->sum('montant_total_du'),
            'reste_a_payer' => $factures->sum('montant_total_du') - $factures->sum('montant_deja_paye'),
        ];

        // Tous les paiements pour les graphiques
        $paiements = Paiement::whereHas('facture.contrat', function ($q) use ($locataire) {
            $q->where('locataire_id', $locataire->id);
        })->latest()->get();

        // Graphique 12 mois
        $moisLabels = [];
        $paiementsMensuels = [];

        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $moisLabels[] = $date->translatedFormat('F Y');

            $paiementsMois = $paiements->filter(function ($p) use ($date) {
                return $p->date_paiement->year == $date->year 
                    && $p->date_paiement->month == $date->month;
            });

            $paiementsMensuels[] = floatval($paiementsMois->sum('montant_verse'));
        }

        return [
            'factures' => $factures,
            'stats' => $stats,
            'moisLabels' => $moisLabels,
            'paiementsMensuels' => $paiementsMensuels,
        ];
    }
}
