<?php

namespace App\Http\Controllers;

use App\Models\Contrat;
use App\Models\Locataire;
use App\Models\Bien;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContratController extends Controller
{
    public function index()
    {
        $contrats = Contrat::where('agence_id', Auth::user()->agence_id)
            ->with(['locataire.user', 'bien'])
            ->get();
        return view('contrats.index', compact('contrats'));
    }

    public function create()
    {
        $locataires = Locataire::where('agence_id', Auth::user()->agence_id)->with('user')->get();
        // Un bien est louable s'il n'a pas d'enfants (sous-biens)
        $biens = Bien::where('agence_id', Auth::user()->agence_id)
            ->doesntHave('children')
            ->where('statut', 'Disponible')
            ->get();

        return view('contrats.create', compact('locataires', 'biens'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'locataire_id' => 'required|exists:locataires,id',
            'bien_id' => 'required|exists:biens,id',
            'date_debut' => 'required|date',
            'date_fin' => 'nullable|date|after:date_debut',
            'montant_loyer_actuel' => 'required|numeric',
            'montant_caution' => 'required|numeric',
            'nombre_mois_avance' => 'required|integer|min:0',
            'frais_dossier' => 'nullable|numeric',
        ]);

        $bien = Bien::findOrFail($request->bien_id);
        if ($bien->children()->count() > 0) {
            return back()->withErrors('Vous devez sélectionner un sous-bien (ex: appartement) et non le bien conteneur (ex: immeuble).')->withInput();
        }

        $data = $request->all();
        $data['agence_id'] = Auth::user()->agence_id;

        $contrat = Contrat::create($data);

        // Mettre à jour le statut du bien
        $bien->update(['statut' => 'Loué']);

        return redirect()->route('contrats.index')->with('success', 'Contrat créé avec succès.');
    }

    public function show(Contrat $contrat)
    {
        if ($contrat->agence_id !== Auth::user()->agence_id) abort(403);
        $contrat->load(['locataire.user', 'bien.proprietaire.user', 'factures']);
        return view('contrats.show', compact('contrat'));
    }

    public function destroy(Contrat $contrat)
    {
        if ($contrat->agence_id !== Auth::user()->agence_id) abort(403);

        // Libérer le bien
        $contrat->bien->update(['statut' => 'Disponible']);
        $contrat->delete();

        return redirect()->route('contrats.index')->with('success', 'Contrat supprimé.');
    }

    /**
     * Afficher les contrats du locataire connecté
     */
    public function mesContrats()
    {
        $user = Auth::user();

        if (!$user->hasRole('locataire')) {
            abort(403, 'Accès refusé');
        }

        $locataire = $user->locataire;
        $contrats = Contrat::where('locataire_id', $locataire->id)
            ->with(['locataire.user', 'bien.proprietaire.user'])
            ->orderBy('date_debut', 'desc')
            ->paginate(15);

        return view('contrats.mes-contrats', compact('contrats'));
    }
}
