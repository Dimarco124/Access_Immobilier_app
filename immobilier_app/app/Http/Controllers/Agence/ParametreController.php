<?php

namespace App\Http\Controllers\Agence;

use App\Http\Controllers\Controller;
use App\Models\Agence;
use App\Models\CompteBancaire;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ParametreController extends Controller
{
    public function index()
    {
        $agence = Auth::user()->agence;
        return view('agence.parametres', compact('agence'));
    }

    public function update(Request $request)
    {
        $agence = Auth::user()->agence;

        $request->validate([
            'nom' => 'required|string|max:255',
            'email' => 'required|email|unique:agences,email,' . $agence->id,
            'telephone' => 'required|string',
            'adresse' => 'required|string',
            'taux_tva' => 'required|numeric|min:0|max:100',
            'devise' => 'required|string|max:10',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = $request->except(['logo', '_token', '_method']);

        if ($request->hasFile('logo')) {
            if ($agence->logo) {
                Storage::disk('public')->delete($agence->logo);
            }
            $data['logo'] = $request->file('logo')->store('logos', 'public');
        }

        $agence->update($data);

        return redirect()->back()->with('success', 'Paramètres mis à jour avec succès.');
    }

    public function addCompteBancaire(Request $request)
    {
        $request->validate([
            'nom_banque' => 'required|string',
            'numero_compte' => 'required|string',
            'rib' => 'nullable|string',
        ]);

        Auth::user()->agence->comptesBancaires()->create($request->all());

        return redirect()->back()->with('success', 'Compte bancaire ajouté.');
    }

    public function deleteCompteBancaire(CompteBancaire $compte)
    {
        if ($compte->agence_id !== Auth::user()->agence_id) {
            abort(403);
        }

        $compte->delete();

        return redirect()->back()->with('success', 'Compte bancaire supprimé.');
    }
}
