<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ModulesSeeder extends Seeder
{
    public function run(): void
    {
        $modules = [
            [
                'code' => 'immobilier',
                'nom' => 'Gestion Immobilière',
                'description' => 'Gestion des familles de biens, biens, unités locatives et leur disponibilité',
                'icon' => 'fas fa-building',
                'is_required' => true,
                'ordre' => 1,
            ],
            [
                'code' => 'commercial',
                'nom' => 'Gestion Commerciale',
                'description' => 'Gestion des visites, contrats de location et locataires',
                'icon' => 'fas fa-handshake',
                'is_required' => true,
                'ordre' => 2,
            ],
            [
                'code' => 'finance',
                'nom' => 'Gestion Financière',
                'description' => 'Gestion des factures, paiements, cautions, reversements et comptabilité',
                'icon' => 'fas fa-wallet',
                'is_required' => true,
                'ordre' => 3,
            ],
            [
                'code' => 'maintenance',
                'nom' => 'Maintenance & Interventions',
                'description' => 'Gestion des tickets d\'intervention, demandes de maintenance et dépenses',
                'icon' => 'fas fa-tools',
                'is_required' => false,
                'ordre' => 4,
            ],
            [
                'code' => 'relation_client',
                'nom' => 'Relation Client',
                'description' => 'Gestion de la messagerie, support client et communication',
                'icon' => 'fas fa-user-friends',
                'is_required' => false,
                'ordre' => 5,
            ],
            [
                'code' => 'proprietaires',
                'nom' => 'Gestion des Propriétaires',
                'description' => 'Gestion des propriétaires, leurs biens et rapports financiers',
                'icon' => 'fas fa-user-tie',
                'is_required' => false,
                'ordre' => 6,
            ],
        ];

        foreach ($modules as $module) {
            DB::table('modules')->updateOrInsert(
                ['code' => $module['code']],
                [
                    'nom' => $module['nom'],
                    'description' => $module['description'],
                    'icon' => $module['icon'],
                    'is_required' => $module['is_required'],
                    'ordre' => $module['ordre'],
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
