<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Agence;
use App\Models\Proprietaire;
use App\Models\Bien;
use App\Models\Unite;
use App\Models\Locataire;
use App\Models\Contrat;
use Spatie\Permission\Models\Role;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Création des Rôles
        $roleAdmin = Role::firstOrCreate(['name' => 'admin']);
        $roleGestionnaire = Role::firstOrCreate(['name' => 'gestionnaire']);
        $roleComptable = Role::firstOrCreate(['name' => 'comptable']);
        $roleProprietaire = Role::firstOrCreate(['name' => 'proprietaire']);
        $roleLocataire = Role::firstOrCreate(['name' => 'locataire']);

        // 2. Création d'une Agence de test
        $agence = Agence::create([
            'nom' => 'E-link Immobilier',
            'adresse' => 'Abidjan, Cocody',
            'telephone' => '+225 0102030405',
            'email' => 'contact@e-link.ci',
            'taux_tva' => 18.00,
            'devise' => 'XOF',
            'statut' => 'actif',
        ]);

        // 3. Création d'un Admin pour l'agence
        $admin = User::create([
            'agence_id' => $agence->id,
            'name' => 'Admin E-link',
            'email' => 'admin@e-link.ci',
            'password' => Hash::make('password'),
            'telephone' => '+225 0707070707',
        ]);
        $admin->assignRole($roleAdmin);

        // 4. Création d'un Propriétaire
        $userProprio = User::create([
            'agence_id' => $agence->id,
            'name' => 'Jean Dupont',
            'email' => 'jean.dupont@email.com',
            'password' => Hash::make('password'),
        ]);
        $userProprio->assignRole($roleProprietaire);

        $proprietaire = Proprietaire::create([
            'agence_id' => $agence->id,
            'user_id' => $userProprio->id,
            'adresse_residence' => 'Plateau, Abidjan',
            'numero_piece_identite' => 'C00123456',
        ]);

        // 5. Création d'un Bien
        $bien = Bien::create([
            'agence_id' => $agence->id,
            'proprietaire_id' => $proprietaire->id,
            'nom_bien' => 'Résidence Horizon',
            'type_bien' => 'Immeuble',
            'adresse' => 'Riviera 3',
            'taux_commission_agence' => 10,
        ]);

        // 6. Création d'Unités
        $unite1 = Unite::create([
            'bien_id' => $bien->id,
            'nom_unite' => 'Appartement A1',
            'type_unite' => 'F3',
            'etage' => '1er',
            'loyer_de_base' => 250000,
            'statut' => 'Loué',
        ]);

        $unite2 = Unite::create([
            'bien_id' => $bien->id,
            'nom_unite' => 'Studio S2',
            'type_unite' => 'Studio',
            'etage' => 'RDC',
            'loyer_de_base' => 100000,
            'statut' => 'Disponible',
        ]);

        // 7. Création d'un Locataire
        $userLocataire = User::create([
            'agence_id' => $agence->id,
            'name' => 'Moussa Traoré',
            'email' => 'moussa@email.com',
            'password' => Hash::make('password'),
        ]);
        $userLocataire->assignRole($roleLocataire);

        $locataire = Locataire::create([
            'agence_id' => $agence->id,
            'user_id' => $userLocataire->id,
            'profession' => 'Développeur',
        ]);

        // 8. Création d'un Contrat
        Contrat::create([
            'agence_id' => $agence->id,
            'locataire_id' => $locataire->id,
            'unite_id' => $unite1->id,
            'date_debut' => now()->startOfMonth(),
            'montant_loyer_actuel' => 250000,
            'montant_caution' => 500000,
            'nombre_mois_avance' => 2,
            'statut' => 'Actif',
        ]);
    }
}
