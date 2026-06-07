<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Agence;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;

class AgencyRegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register-agency');
    }

    public function register(Request $request)
    {
        $request->validate([
            // Agence
            'nom_agence' => 'required|string|max:255',
            'ville' => 'required|string|max:255',
            'telephone_agence' => 'required|string',
            'email_agence' => 'required|email|unique:agences,email',
            'description' => 'nullable|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            // Admin User
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // 1. Gérer le logo avant la création
        $logoPath = null;
        if ($request->hasFile('logo') && $request->file('logo')->isValid()) {
            $logoPath = $request->file('logo')->store('logos', 'public');
        }

        $agence = Agence::create([
            'nom' => $request->nom_agence,
            'adresse' => $request->ville,
            'telephone' => $request->telephone_agence,
            'email' => $request->email_agence,
            'logo' => $logoPath,
            'taux_tva' => 18.00,
            'devise' => 'XOF',
            'statut' => 'actif',
        ]);

        // 2. Créer l'utilisateur admin
        $user = User::create([
            'agence_id' => $agence->id,
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'telephone' => $request->telephone_agence,
        ]);

        // S'assurer que les rôles de base existent
        Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'gestionnaire']);
        Role::firstOrCreate(['name' => 'comptable']);
        Role::firstOrCreate(['name' => 'proprietaire']);
        Role::firstOrCreate(['name' => 'locataire']);

        $user->assignRole('admin');

        // 3. Connecter l'utilisateur
        Auth::login($user);

        return redirect()->route('dashboard')->with('success', 'Votre agence a été configurée avec succès !');
    }
}
