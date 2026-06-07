<?php

namespace App\Http\Controllers;

use App\Models\Bien;
use App\Models\Proprietaire;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BienController extends Controller
{
    /**
     * Afficher tous les biens groupés par propriétaire
     */
    public function index()
    {
        $proprietaires = Proprietaire::where('agence_id', Auth::user()->agence_id)
            ->with(['user', 'biens' => function($query) {
                $query->whereNull('parent_id')->with('children', 'visites');
            }])
            ->get();
        
        return view('biens.index', compact('proprietaires'));
    }

    /**
     * Formulaire pour créer un bien principal
     */
    public function create()
    {
        $proprietaires = Proprietaire::where('agence_id', Auth::user()->agence_id)->with('user')->get();
        return view('biens.create', compact('proprietaires'));
    }

    /**
     * Créer un bien principal
     */
    public function store(Request $request)
    {
        $request->validate([
            'proprietaire_id' => 'required|exists:proprietaires,id',
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
        $data['parent_id'] = null; // Toujours un bien principal

        Bien::create($data);

        return redirect()->route('biens.index')->with('success', 'Bien créé avec succès.');
    }

    /**
     * Afficher détails d'un bien avec ses sous-biens
     */
    public function show(Bien $bien)
    {
        if ($bien->agence_id !== Auth::user()->agence_id) abort(403);
        $bien->load(['children', 'proprietaire.user', 'visites']);
        return view('biens.show', compact('bien'));
    }

    /**
     * Éditer un bien principal
     */
    public function edit(Bien $bien)
    {
        if ($bien->agence_id !== Auth::user()->agence_id) abort(403);
        $bien->load('proprietaire');
        return view('biens.edit', compact('bien'));
    }

    /**
     * Mettre à jour un bien principal
     */
    public function update(Request $request, Bien $bien)
    {
        if ($bien->agence_id !== Auth::user()->agence_id) abort(403);

        $request->validate([
            'nom_bien' => 'required|string|max:255',
            'type_bien' => 'required|string',
            'adresse' => 'required|string',
            'taux_commission_agence' => 'required|numeric|min:0|max:100',
            'prix_visite' => 'nullable|numeric|min:0',
            'loyer_mensuel' => 'nullable|numeric|min:0',
            'description' => 'nullable|string',
            'statut' => 'required|in:Disponible,Loué,En maintenance',
        ]);

        $bien->update($request->all());

        return redirect()->route('biens.index')->with('success', 'Bien mis à jour.');
    }

    /**
     * Supprimer un bien (et ses sous-biens/visites en cascade)
     */
    public function destroy(Bien $bien)
    {
        if ($bien->agence_id !== Auth::user()->agence_id) abort(403);
        $bien->delete();
        return redirect()->route('biens.index')->with('success', 'Bien supprimé.');
    }

    /**
     * Créer un sous-bien pour un bien principal
     */
    public function storeSubBien(Request $request, Bien $bien)
    {
        if ($bien->agence_id !== Auth::user()->agence_id) abort(403);
        if ($bien->parent_id !== null) abort(403, 'Impossible d\'ajouter des sous-biens à un sous-bien');

        $request->validate([
            'nom_bien' => 'required|string|max:255',
            'type_bien' => 'required|string',
            'adresse' => 'nullable|string',
            'prix_visite' => 'nullable|numeric|min:0',
            'loyer_mensuel' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        $subBien = new Bien([
            'agence_id' => Auth::user()->agence_id,
            'proprietaire_id' => $bien->proprietaire_id,
            'parent_id' => $bien->id,
            'nom_bien' => $request->nom_bien,
            'type_bien' => $request->type_bien,
            'adresse' => $request->adresse ?? $bien->adresse,
            'taux_commission_agence' => $bien->taux_commission_agence,
            'prix_visite' => $request->prix_visite ?? $bien->prix_visite,
            'loyer_mensuel' => $request->loyer_mensuel,
            'statut' => 'Disponible',
            'description' => $request->description,
        ]);

        $subBien->save();

        return back()->with('success', 'Sous-bien créé avec succès.');
    }

    /**
     * Éditer un sous-bien
     */
    public function editSubBien(Bien $bien)
    {
        if ($bien->agence_id !== Auth::user()->agence_id) abort(403);
        if ($bien->parent_id === null) abort(403, 'Ce n\'est pas un sous-bien');
        
        $parent = $bien->parent;
        return view('biens.edit-subbien', compact('bien', 'parent'));
    }

    /**
     * Mettre à jour un sous-bien
     */
    public function updateSubBien(Request $request, Bien $bien)
    {
        if ($bien->agence_id !== Auth::user()->agence_id) abort(403);
        if ($bien->parent_id === null) abort(403, 'Ce n\'est pas un sous-bien');

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

        return back()->with('success', 'Sous-bien mis à jour.');
    }

    /**
     * Supprimer un sous-bien
     */
    public function destroySubBien(Bien $bien)
    {
        if ($bien->agence_id !== Auth::user()->agence_id) abort(403);
        if ($bien->parent_id === null) abort(403, 'Ce n\'est pas un sous-bien');
        
        $parent = $bien->parent;
        $bien->delete();
        
        return redirect()->route('biens.show', $parent)->with('success', 'Sous-bien supprimé.');
    }
}
