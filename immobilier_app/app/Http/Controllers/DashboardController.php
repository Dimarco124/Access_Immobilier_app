<?php

namespace App\Http\Controllers;

use App\Models\Bien;
use App\Models\Contrat;
use App\Models\Facture;
use App\Models\Paiement;
use App\Models\Ticket;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $agenceId = $user->agence_id;

        if ($user->hasRole('locataire')) {
            return $this->tenantDashboard($user);
        }

        if ($user->hasRole('proprietaire')) {
            return $this->ownerDashboard($user);
        }

        $stats = [
            'total_biens' => Bien::where('agence_id', $agenceId)->whereNull('parent_id')->count(),
            'total_sous_biens' => Bien::where('agence_id', $agenceId)->whereNotNull('parent_id')->count(),
            'total_contrats' => Contrat::where('agence_id', $agenceId)->count(),
            'factures_impayees' => Facture::where('agence_id', $agenceId)->where('statut', 'Impayée')->count(),
            'ca_mensuel' => Paiement::where('agence_id', $agenceId)
                ->whereMonth('date_paiement', now()->month)
                ->sum('montant_verse'),
            'tickets_ouverts' => Ticket::where('agence_id', $agenceId)->where('statut', '!=', 'Résolu')->count(),
        ];

        $derniers_paiements = Paiement::where('agence_id', $agenceId)
            ->with(['facture.contrat.locataire.user'])
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard', compact('stats', 'derniers_paiements'));
    }

    private function tenantDashboard($user)
    {
        $locataire = $user->locataire;
        $stats = [
            'contrats_actifs' => $locataire->contrats()->where('statut', 'Actif')->count(),
            'factures_a_payer' => Facture::whereHas('contrat', function ($q) use ($locataire) {
                $q->where('locataire_id', $locataire->id);
            })->where('statut', '!=', 'Payée')->count(),
            'tickets_en_cours' => Ticket::where('locataire_id', $locataire->id)->where('statut', '!=', 'Résolu')->count(),
        ];

        return view('dashboard_tenant', compact('stats'));
    }

    private function ownerDashboard($user)
    {
        $proprietaire = $user->proprietaire;
        $stats = [
            'total_biens' => $proprietaire->biens()->count(),
            'total_loyers_percus' => Facture::whereHas('contrat.bien', function ($q) use ($proprietaire) {
                $q->where('proprietaire_id', $proprietaire->id);
            })->where('statut', 'Payée')->sum('montant_total_du'),
        ];

        return view('dashboard_owner', compact('stats'));
    }
}
