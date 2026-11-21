<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Agence;
use App\Models\Proprietaire;
use App\Models\Bien;
use App\Models\Locataire;
use App\Models\Contrat;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        try {
            // 0. Créer les modules d'abord
            $this->call(ModulesSeeder::class);

            // 1. Création des Rôles
            $roleAdmin = Role::firstOrCreate(['name' => 'admin']);
            $roleGestionnaire = Role::firstOrCreate(['name' => 'gestionnaire']);
            $roleComptable = Role::firstOrCreate(['name' => 'comptable']);
            $roleProprietaire = Role::firstOrCreate(['name' => 'proprietaire']);
            $roleLocataire = Role::firstOrCreate(['name' => 'locataire']);

            // 2. Création d'une Agence de test
            $agence = Agence::firstOrCreate(
                ['email' => 'contact@e-link.ci'],
                [
                    'nom' => 'E-link Immobilier',
                    'adresse' => 'Abidjan, Cocody',
                    'telephone' => '+225 0102030405',
                    'taux_tva' => 18.00,
                    'devise' => 'XOF',
                    'statut' => 'actif',
                ]
            );

            // 3. Création d'un Admin pour l'agence
            $admin = User::firstOrCreate(
                ['email' => 'admin@e-link.ci'],
                [
                    'agence_id' => $agence->id,
                    'name' => 'Admin E-link',
                    'password' => Hash::make('password'),
                    'telephone' => '+225 0707070707',
                ]
            );
            $admin->assignRole($roleAdmin);

            // 4. Création d'un Propriétaire
            $userProprio = User::firstOrCreate(
                ['email' => 'jean.dupont@email.com'],
                [
                    'agence_id' => $agence->id,
                    'name' => 'Jean Dupont',
                    'password' => Hash::make('password'),
                ]
            );
            $userProprio->assignRole($roleProprietaire);

            $proprietaire = Proprietaire::firstOrCreate(
                ['user_id' => $userProprio->id],
                [
                    'agence_id' => $agence->id,
                    'adresse_residence' => 'Plateau, Abidjan',
                    'numero_piece_identite' => 'C00123456',
                ]
            );

            // 5. Création d'un Bien
            $bien = Bien::firstOrCreate(
                ['nom_bien' => 'Résidence Horizon'],
                [
                    'agence_id' => $agence->id,
                    'proprietaire_id' => $proprietaire->id,
                    'type_bien' => 'Immeuble',
                    'adresse' => 'Riviera 3',
                    'taux_commission_agence' => 10,
                ]
            );

            // 6. Création d'un Locataire
            $userLocataire = User::firstOrCreate(
                ['email' => 'moussa@email.com'],
                [
                    'agence_id' => $agence->id,
                    'name' => 'Moussa Traoré',
                    'password' => Hash::make('password'),
                ]
            );
            $userLocataire->assignRole($roleLocataire);

            $locataire = Locataire::firstOrCreate(
                ['user_id' => $userLocataire->id],
                [
                    'agence_id' => $agence->id,
                    'profession' => 'Développeur',
                ]
            );

            // 7. Création d'un Contrat
            Contrat::firstOrCreate(
                ['locataire_id' => $locataire->id, 'bien_id' => $bien->id],
                [
                    'agence_id' => $agence->id,
                    'date_debut' => now()->startOfMonth(),
                    'montant_loyer_actuel' => 250000,
                    'montant_caution' => 500000,
                    'nombre_mois_avance' => 2,
                    'statut' => 'Actif',
                ]
            );
        } catch (\Exception $e) {
            $this->command->error('Error in DatabaseSeeder: ' . $e->getMessage());
            throw $e;
        }
    }
}
