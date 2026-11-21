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
            'adresse' => 'required|string|max:255',
            'telephone_agence' => 'required|string',
            'email_agence' => 'required|email|unique:agences,email',
            'numero_agrement' => 'nullable|string|max:255',
            'compte_contribuable' => 'nullable|string|max:255',
            'site_web' => 'nullable|url|max:255',
            'description' => 'required|string',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120', // 5 Mo max, optionnel
            // Admin User
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'logo.max' => 'Le logo ne doit pas dépasser 5 Mo.',
            'logo.image' => 'Le logo doit être une image.',
            'logo.mimes' => 'Le logo doit être au format : jpeg, png, jpg, gif ou webp.',
        ]);

        // 1. Gérer le logo avant la création
        $logoPath = null;
        if ($request->hasFile('logo') && $request->file('logo')->isValid()) {
            $logoPath = $request->file('logo')->store('logos', 'public');
        }

        $agence = Agence::create([
            'nom' => $request->nom_agence,
            'adresse' => $request->adresse . ', ' . $request->ville,
            'telephone' => $request->telephone_agence,
            'email' => $request->email_agence,
            'numero_agrement' => $request->numero_agrement,
            'compte_contribuable' => $request->compte_contribuable,
            'site_web' => $request->site_web,
            'description' => $request->description,
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
        Role::firstOrCreate(['name' => 'team_member']);

        $user->assignRole('admin');

        // 3. Connecter l'utilisateur
        Auth::login($user);

        // 4. Rediriger vers la configuration d'équipe
        return redirect()->route('team-setup.choose-mode')->with('success', 'Votre agence a été créée ! Configurons maintenant votre équipe.');
    }
}
