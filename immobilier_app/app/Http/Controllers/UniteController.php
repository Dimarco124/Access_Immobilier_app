<?php

namespace App\Http\Controllers;

use App\Models\Bien;
use App\Models\Unite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UniteController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'bien_id' => 'required|exists:biens,id',
            'nom_unite' => 'required|string|max:255',
            'type_unite' => 'required|string|max:255',
            'adresse' => 'nullable|string|max:255',
            'prix_visite' => 'nullable|numeric|min:0',
            'loyer_mensuel' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ]);

        $bien = Bien::where('agence_id', Auth::user()->agence_id)->findOrFail($data['bien_id']);

        $data['agence_id'] = Auth::user()->agence_id;
        $data['adresse'] = $data['adresse'] ?: $bien->adresse;
        $data['prix_visite'] = $data['prix_visite'] ?? $bien->prix_visite ?? 0;
        $data['statut'] = 'Disponible';

        $unite = Unite::create($data);
        $unite->bien->syncStatus();

        return back()->with('success', 'Unité créée avec succès.');
    }

    public function update(Request $request, Unite $unite)
    {
        if ($unite->agence_id !== Auth::user()->agence_id) {
            abort(403);
        }

        $data = $request->validate([
            'nom_unite' => 'required|string|max:255',
            'type_unite' => 'required|string|max:255',
            'adresse' => 'nullable|string|max:255',
            'prix_visite' => 'nullable|numeric|min:0',
            'loyer_mensuel' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'statut' => 'required|in:Disponible,Loué,En maintenance',
        ]);

        $unite->update($data);
        $unite->bien->syncStatus();

        return back()->with('success', 'Unité mise à jour.');
    }

    public function destroy(Unite $unite)
    {
        if ($unite->agence_id !== Auth::user()->agence_id) {
            abort(403);
        }

        if ($unite->contrats()->where('statut', 'Actif')->exists()) {
            return back()->withErrors('Impossible de supprimer une unité avec un contrat actif.');
        }

        $bien = $unite->bien;
        $unite->delete();
        $bien->syncStatus();

        return back()->with('success', 'Unité supprimée.');
    }
}
