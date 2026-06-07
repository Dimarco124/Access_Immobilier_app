<?php

namespace App\Http\Controllers;

use App\Models\Proprietaire;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class ProprietaireController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Si c'est un propriétaire, afficher seulement ses infos
        if ($user->hasRole('proprietaire')) {
            $proprietaires = collect([$user->proprietaire]);
        } else {
            // Si c'est l'agence, afficher tous les propriétaires
            $proprietaires = Proprietaire::where('agence_id', Auth::user()->agence_id)
                ->with('user')
                ->get();
        }

        return view('proprietaires.index', compact('proprietaires'));
    }

    public function create()
    {
        if (!Auth::user()->hasRole('admin')) abort(403);
        return view('proprietaires.create');
    }

    public function store(Request $request)
    {
        if (!Auth::user()->hasRole('admin')) abort(403);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'telephone' => 'nullable|string',
            'adresse_residence' => 'nullable|string',
            'numero_piece_identite' => 'nullable|string',
            'rib_bancaire' => 'nullable|string',
        ]);

        $user = User::create([
            'agence_id' => Auth::user()->agence_id,
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'telephone' => $request->telephone,
        ]);

        Role::firstOrCreate(['name' => 'proprietaire']);
        $user->assignRole('proprietaire');

        Proprietaire::create([
            'agence_id' => Auth::user()->agence_id,
            'user_id' => $user->id,
            'civilite' => $request->civilite,
            'adresse_residence' => $request->adresse_residence,
            'numero_piece_identite' => $request->numero_piece_identite,
            'contact_secondaire' => $request->contact_secondaire,
            'pays' => $request->pays,
            'ville' => $request->ville,
            'rib_bancaire' => $request->rib_bancaire,
        ]);

        return redirect()->route('proprietaires.index')->with('success', 'Propriétaire créé avec succès.');
    }

    public function edit(Proprietaire $proprietaire)
    {
        if ($proprietaire->agence_id !== Auth::user()->agence_id) abort(403);
        if (!Auth::user()->hasRole('admin')) abort(403);
        return view('proprietaires.edit', compact('proprietaire'));
    }

    public function show(Proprietaire $proprietaire)
    {
        if ($proprietaire->agence_id !== Auth::user()->agence_id) abort(403);
        $proprietaire->load(['user', 'biens']);
        return view('proprietaires.show', compact('proprietaire'));
    }

    public function update(Request $request, Proprietaire $proprietaire)
    {
        if ($proprietaire->agence_id !== Auth::user()->agence_id) abort(403);
        if (!Auth::user()->hasRole('admin')) abort(403);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $proprietaire->user_id,
            'telephone' => 'nullable|string',
            'adresse_residence' => 'nullable|string',
            'numero_piece_identite' => 'nullable|string',
            'rib_bancaire' => 'nullable|string',
        ]);

        $proprietaire->user->update([
            'name' => $request->name,
            'email' => $request->email,
            'telephone' => $request->telephone,
        ]);

        $proprietaire->update([
            'civilite' => $request->civilite,
            'adresse_residence' => $request->adresse_residence,
            'numero_piece_identite' => $request->numero_piece_identite,
            'contact_secondaire' => $request->contact_secondaire,
            'pays' => $request->pays,
            'ville' => $request->ville,
            'rib_bancaire' => $request->rib_bancaire,
        ]);

        return redirect()->route('proprietaires.index')->with('success', 'Propriétaire mis à jour.');
    }

    public function destroy(Proprietaire $proprietaire)
    {
        if ($proprietaire->agence_id !== Auth::user()->agence_id) abort(403);
        if (!Auth::user()->hasRole('admin')) abort(403);
        $proprietaire->user->delete(); // Cascade delete will handle the Proprietaire record
        return redirect()->route('proprietaires.index')->with('success', 'Propriétaire supprimé.');
    }
}
