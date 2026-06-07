<?php

namespace App\Http\Controllers;

use App\Models\Facture;
use App\Models\Contrat;
use App\Models\Paiement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FactureController extends Controller
{
    public function index()
    {
        $factures = Facture::where('agence_id', Auth::user()->agence_id)
            ->with(['contrat.locataire.user', 'contrat.bien'])
            ->latest()
            ->get();
        return view('factures.index', compact('factures'));
    }

    public function create(Contrat $contrat)
    {
        if ($contrat->agence_id !== Auth::user()->agence_id) abort(403);
        return view('factures.create', compact('contrat'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'contrat_id' => 'required|exists:contrats,id',
            'mois_facture' => 'required|string',
            'date_echeance' => 'required|date',
        ]);

        $contrat = Contrat::findOrFail($request->contrat_id);
        if ($contrat->agence_id !== Auth::user()->agence_id) abort(403);

        $montantHT = $contrat->montant_loyer_actuel;
        $tauxTVA = Auth::user()->agence->taux_tva;
        $montantTVA = ($montantHT * $tauxTVA) / 100;

        Facture::create([
            'agence_id' => Auth::user()->agence_id,
            'contrat_id' => $contrat->id,
            'numero_facture' => 'FACT-' . strtoupper(uniqid()),
            'mois_facture' => $request->mois_facture,
            'montant_hors_taxe' => $montantHT,
            'montant_tva' => $montantTVA,
            'montant_total_du' => $montantHT + $montantTVA,
            'date_echeance' => $request->date_echeance,
            'statut' => 'Impayée',
        ]);

        return redirect()->route('contrats.show', $contrat)->with('success', 'Facture générée.');
    }

    public function recordPayment(Request $request, Facture $facture)
    {
        $request->validate([
            'montant_verse' => 'required|numeric|min:1',
            'mode_paiement' => 'required|string',
            'date_paiement' => 'required|date',
        ]);

        if ($facture->agence_id !== Auth::user()->agence_id) abort(403);

        Paiement::create([
            'agence_id' => Auth::user()->agence_id,
            'facture_id' => $facture->id,
            'montant_verse' => $request->montant_verse,
            'mode_paiement' => $request->mode_paiement,
            'date_paiement' => $request->date_paiement,
        ]);

        $nouveauTotalPaye = $facture->montant_deja_paye + $request->montant_verse;
        $nouveauStatut = $nouveauTotalPaye >= $facture->montant_total_du ? 'Payée' : 'Partielle';

        $facture->update([
            'montant_deja_paye' => $nouveauTotalPaye,
            'statut' => $nouveauStatut,
        ]);

        return redirect()->back()->with('success', 'Paiement enregistré.');
    }

    /**
     * Afficher les factures du locataire connecté
     */
    public function mesFactures()
    {
        $user = Auth::user();

        if (!$user->hasRole('locataire')) {
            abort(403, 'Accès refusé');
        }

        $locataire = $user->locataire;
        $factures = Facture::whereHas('contrat', function ($query) use ($locataire) {
            $query->where('locataire_id', $locataire->id);
        })
            ->with(['contrat.locataire.user', 'contrat.bien'])
            ->orderBy('date_echeance', 'desc')
            ->paginate(15);

        return view('factures.mes-factures', compact('factures'));
    }
}
