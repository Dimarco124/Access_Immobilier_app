<?php

namespace App\Http\Controllers;

use App\Models\Bien;
use App\Models\FamilleDeBien;
use App\Models\Proprietaire;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BienController extends Controller
{
    /**
     * Rediriger vers la nouvelle page avec onglets
     */
    public function index()
    {
        return redirect()->route('proprietes.index');
    }

    /**
     * Formulaire pour créer un bien
     */
    public function create()
    {
        $proprietaires = Proprietaire::where('agence_id', Auth::user()->agence_id)->with('user')->get();
        $familles = FamilleDeBien::where('agence_id', Auth::user()->agence_id)->with('proprietaire')->get();
        $selectedFamille = request('famille');
        return view('biens.create', compact('proprietaires', 'familles', 'selectedFamille'));
    }

    /**
     * Créer un bien
     */
    public function store(Request $request)
    {
        // DIAGNOSTIC COMPLET
        \Log::info('BienController::store - DÉBUT DIAGNOSTIC COMPLET', [
            'user_id' => Auth::user()->id,
            'user_email' => Auth::user()->email,
            'user_agence_id' => Auth::user()->agence_id,
            'request_famille_id' => $request->famille_de_bien_id,
        ]);

        $request->validate([
            'famille_de_bien_id' => 'required|exists:familles_de_biens,id',
            'nom_bien' => 'required|string|max:255',
            'type_bien' => 'required|string',
            'adresse' => 'required|string',
            'taux_commission_agence' => 'required|numeric|min:0|max:100',
            'prix_visite' => 'nullable|numeric|min:0',
            'loyer_mensuel' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        $data = $request->all();
        $data['agence_id'] = Auth::user()->agence_id;

        // Récupérer la famille pour obtenir le propriétaire
        $famille = FamilleDeBien::where('agence_id', Auth::user()->agence_id)->findOrFail($request->famille_de_bien_id);
        
        \Log::info('BienController::store - Famille récupérée', [
            'famille_id' => $famille->id,
            'famille_nom' => $famille->nom_famille,
            'famille_agence_id' => $famille->agence_id,
            'user_agence_id' => Auth::user()->agence_id,
            'match' => $famille->agence_id === Auth::user()->agence_id
        ]);
        
        $data['proprietaire_id'] = $famille->proprietaire_id;
        $data['statut'] = 'Disponible';

        $bien = Bien::create($data);
        
        \Log::info('BienController::store - Bien créé', [
            'bien_id' => $bien->id,
            'bien_agence_id' => $bien->agence_id,
            'redirection_vers_famille' => $request->famille_de_bien_id
        ]);

        if ($request->filled('loyer_mensuel')) {
            // Création de l'Unité
            \App\Models\Unite::create([
                'agence_id' => Auth::user()->agence_id,
                'bien_id' => $bien->id,
                'nom_unite' => 'Unité principale',
                'type_unite' => $request->type_bien,
                'adresse' => $request->adresse,
                'description' => $request->description,
                'prix_visite' => $request->prix_visite ?? 0,
                'loyer_mensuel' => $request->loyer_mensuel,
                'statut' => 'Disponible',
            ]);
        }

        return redirect()->route('familles-de-biens.show', $request->famille_de_bien_id)->with('success', 'Bien créé avec succès.');
    }

    /**
     * Afficher détails d'un bien avec ses unités
     */
    public function show(Bien $bien)
    {
        if ($bien->agence_id !== Auth::user()->agence_id)
            abort(403);
        $bien->load(['unites', 'familleDeBien', 'proprietaire.user', 'visites.locataire.user']);
        return view('biens.show', compact('bien'));
    }

    /**
     * Éditer un bien
     */
    public function edit(Bien $bien)
    {
        if ($bien->agence_id !== Auth::user()->agence_id)
            abort(403);
        $familles = FamilleDeBien::where('agence_id', Auth::user()->agence_id)->with('proprietaire')->get();
        return view('biens.edit', compact('bien', 'familles'));
    }

    /**
     * Mettre à jour un bien
     */
    public function update(Request $request, Bien $bien)
    {
        if ($bien->agence_id !== Auth::user()->agence_id)
            abort(403);

        $request->validate([
            'famille_de_bien_id' => 'required|exists:familles_de_biens,id',
            'nom_bien' => 'required|string|max:255',
            'type_bien' => 'required|string',
            'adresse' => 'required|string',
            'taux_commission_agence' => 'required|numeric|min:0|max:100',
            'prix_visite' => 'nullable|numeric|min:0',
            'loyer_mensuel' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'statut' => 'required|in:Disponible,Loué,En maintenance',
        ]);

        $famille = FamilleDeBien::where('agence_id', Auth::user()->agence_id)->findOrFail($request->famille_de_bien_id);
        $data = $request->except(['agence_id']);
        $data['proprietaire_id'] = $famille->proprietaire_id;

        $bien->update($data);

        return redirect()->route('familles-de-biens.show', $request->famille_de_bien_id)->with('success', 'Bien mis à jour.');
    }

    /**
     * Supprimer un bien
     */
    public function destroy(Bien $bien)
    {
        if ($bien->agence_id !== Auth::user()->agence_id)
            abort(403);
        $familleId = $bien->famille_de_bien_id;
        $bien->delete();
        return redirect()->route('familles-de-biens.show', $familleId)->with('success', 'Bien supprimé.');
    }

    /**
     * Créer un sous-bien pour un bien principal
     */
    public function storeSubBien(Request $request, Bien $bien)
    {
        if ($bien->agence_id !== Auth::user()->agence_id)
            abort(403);
        if ($bien->parent_id !== null)
            abort(403, 'Impossible d\'ajouter des sous-biens à un sous-bien');

        $request->validate([
            'nom_bien' => 'required|string|max:255',
            'type_bien' => 'required|string',
            'adresse' => 'nullable|string',
            'prix_visite' => 'nullable|numeric|min:0',
            'loyer_mensuel' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        // Création du sous-bien (pour compatibilité descendante)
        $subBien = Bien::create([
            'agence_id' => Auth::user()->agence_id,
            'proprietaire_id' => $bien->proprietaire_id,
            'parent_id' => $bien->id,
            'nom_bien' => $request->nom_bien,
            'type_bien' => $request->type_bien,
            'adresse' => $request->adresse ?? $bien->adresse,
            'prix_visite' => $request->prix_visite ?? $bien->prix_visite,
            'loyer_mensuel' => $request->loyer_mensuel,
            'statut' => 'Disponible',
            'description' => $request->description,
        ]);

        // Création de l'Unité (nouvelle logique recommandée)
        // CORRECTION BUG CRITIQUE : Associer l'unité au sous-bien créé, pas au bien parent
        \App\Models\Unite::create([
            'agence_id' => Auth::user()->agence_id,
            'bien_id' => $subBien->id, // FIX: Utiliser $subBien->id au lieu de $bien->id
            'nom_unite' => $request->nom_bien,
            'type_unite' => $request->type_bien,
            'adresse' => $request->adresse ?? $bien->adresse,
            'prix_visite' => $request->prix_visite ?? $bien->prix_visite,
            'loyer_mensuel' => $request->loyer_mensuel,
            'statut' => 'Disponible',
            'description' => $request->description,
            'migrated_from_bien_id' => $subBien->id,
        ]);

        // Synchroniser le statut du bien parent
        $bien->syncStatus();

        return back()->with('success', 'Sous-bien et unité créés avec succès.');
    }

    /**
     * Éditer un sous-bien
     */
    public function editSubBien(Bien $bien)
    {
        if ($bien->agence_id !== Auth::user()->agence_id)
            abort(403);

        $parent = $bien->parent;
        return view('biens.edit-subbien', compact('bien', 'parent'));
    }

    /**
     * Mettre à jour un sous-bien
     */
    public function updateSubBien(Request $request, Bien $bien)
    {
        if ($bien->agence_id !== Auth::user()->agence_id)
            abort(403);

        $request->validate([
            'nom_bien' => 'required|string|max:255',
            'type_bien' => 'required|string',
            'adresse' => 'nullable|string',
            'prix_visite' => 'nullable|numeric|min:0',
            'loyer_mensuel' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'statut' => 'required|in:Disponible,Loué,En maintenance',
        ]);

        $bien->update($request->all());

        return back()->with('success', 'Sous-bien mis à jour avec succès.');
    }

    /**
     * Supprimer un sous-bien
     */
    public function destroySubBien(Bien $bien)
    {
        if ($bien->agence_id !== Auth::user()->agence_id)
            abort(403);
        $bien->delete();
        return back()->with('success', 'Sous-bien supprimé.');
    }
}
