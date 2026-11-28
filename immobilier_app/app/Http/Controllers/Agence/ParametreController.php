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

        if ($request->hasFile('logo') && $request->file('logo')->isValid()) {
            $file = $request->file('logo');
            $mime = $file->getMimeType();
            $data['logo'] = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($file->getRealPath()));
        }

        $agence->update($data);

        return redirect()->back()->with('success', 'Paramètres mis à jour avec succès.');
    }

    public function updatePaiement(Request $request)
    {
        $agence = Auth::user()->agence;

        $request->validate([
            'jour_echeance_loyer' => 'required|integer|min:1|max:31',
            'jours_grace_retard' => 'required|integer|min:0',
            'frais_retard_type' => 'required|string|in:pourcentage,fixe',
            'frais_retard_valeur' => 'required|numeric|min:0',
        ]);

        $parametres = $agence->parametres_paiement ?? [];
        $parametres['jour_echeance_loyer'] = $request->jour_echeance_loyer;
        $parametres['jours_grace_retard'] = $request->jours_grace_retard;
        $parametres['frais_retard_type'] = $request->frais_retard_type;
        $parametres['frais_retard_valeur'] = $request->frais_retard_valeur;

        $agence->update(['parametres_paiement' => $parametres]);

        return redirect()->back()->with('success', 'Paramètres de paiement mis à jour.');
    }

    public function addCompteBancaire(Request $request)
    {
        $request->validate([
            'nom_banque' => 'required|string',
            'numero_compte' => 'required|string',
            'rib' => 'nullable|string',
        ]);

        Auth::user()->agence->comptesBancaires()->create($request->only([
            'nom_banque',
            'numero_compte',
            'rib',
        ]));

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
