<?php

namespace App\Http\Controllers;

use App\Models\Locataire;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class LocataireController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Si c'est un locataire, afficher seulement ses infos
        if ($user->hasRole('locataire')) {
            $locataires = collect([$user->locataire]);
        } else {
            // Si c'est l'agence, afficher tous les locataires
            $locataires = Locataire::where('agence_id', Auth::user()->agence_id)
                ->with('user')
                ->get();
        }

        return view('locataires.index', compact('locataires'));
    }

    public function create()
    {
        if (!Auth::user()->hasRole('admin')) abort(403);
        return view('locataires.create');
    }

    public function store(Request $request)
    {
        if (!Auth::user()->hasRole('admin')) abort(403);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'telephone' => 'nullable|string',
            'contact_urgence' => 'nullable|string',
            'profession' => 'nullable|string',
            'copie_piece_identite' => 'nullable|file|mimes:pdf,jpg,png|max:2048',
        ]);

        $user = User::create([
            'agence_id' => Auth::user()->agence_id,
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'telephone' => $request->telephone,
        ]);

        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'locataire']);
        $user->assignRole('locataire');

        $data = $request->only(['contact_urgence', 'profession']);
        $data['agence_id'] = Auth::user()->agence_id;
        $data['user_id'] = $user->id;

        if ($request->hasFile('copie_piece_identite')) {
            $data['copie_piece_identite'] = $request->file('copie_piece_identite')->store('pieces_identite', 'public');
        }

        Locataire::create($data);

        return redirect()->route('locataires.index')->with('success', 'Locataire créé avec succès.');
    }

    public function edit(Locataire $locataire)
    {
        if ($locataire->agence_id !== Auth::user()->agence_id) abort(403);
        if (!Auth::user()->hasRole('admin')) abort(403);
        return view('locataires.edit', compact('locataire'));
    }

    public function show(Locataire $locataire)
    {
        if ($locataire->agence_id !== Auth::user()->agence_id) abort(403);
        $locataire->load(['user', 'contrats', 'tickets']);
        return view('locataires.show', compact('locataire'));
    }

    public function update(Request $request, Locataire $locataire)
    {
        if ($locataire->agence_id !== Auth::user()->agence_id) abort(403);
        if (!Auth::user()->hasRole('admin')) abort(403);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $locataire->user_id,
            'telephone' => 'nullable|string',
            'contact_urgence' => 'nullable|string',
            'profession' => 'nullable|string',
            'copie_piece_identite' => 'nullable|file|mimes:pdf,jpg,png|max:2048',
        ]);

        $locataire->user->update([
            'name' => $request->name,
            'email' => $request->email,
            'telephone' => $request->telephone,
        ]);

        $data = $request->only(['contact_urgence', 'profession']);

        if ($request->hasFile('copie_piece_identite')) {
            if ($locataire->copie_piece_identite) {
                Storage::disk('public')->delete($locataire->copie_piece_identite);
            }
            $data['copie_piece_identite'] = $request->file('copie_piece_identite')->store('pieces_identite', 'public');
        }

        $locataire->update($data);

        return redirect()->route('locataires.index')->with('success', 'Locataire mis à jour.');
    }

    public function destroy(Locataire $locataire)
    {
        if ($locataire->agence_id !== Auth::user()->agence_id) abort(403);
        if (!Auth::user()->hasRole('admin')) abort(403);
        $locataire->user->delete();
        return redirect()->route('locataires.index')->with('success', 'Locataire supprimé.');
    }
}
