<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Enrichir les Locataires pour l'Audit (Phase 1)
        Schema::table('locataires', function (Blueprint $table) {
            $table->enum('statut_audit', ['En attente', 'Validé', 'Rejeté'])->default('En attente')->after('copie_piece_identite');
            $table->text('notes_audit')->nullable()->after('statut_audit');
        });

        // 2. Enrichir les Contrats pour les États des Lieux et Frais (Phase 2 & 5)
        Schema::table('contrats', function (Blueprint $table) {
            $table->string('etat_des_lieux_entree')->nullable()->after('frais_dossier');
            $table->string('etat_des_lieux_sortie')->nullable()->after('etat_des_lieux_entree');
            $table->date('date_avis_conge')->nullable()->after('date_fin'); // Préavis
        });

        // 3. Enrichir les Factures pour distinguer les types (Phase 2 & 3)
        Schema::table('factures', function (Blueprint $table) {
            $table->enum('type_facture', ['Loyer', 'Caution', 'Frais Dossier', 'Autre'])->default('Loyer')->after('contrat_id');
        });

        // 4. Enrichir les Reversements (Phase 4)
        Schema::table('reversements', function (Blueprint $table) {
            $table->enum('statut', ['Brouillon', 'Validé', 'Payé'])->default('Brouillon')->after('montant_net_verse');
        });
    }

    public function down(): void
    {
        Schema::table('locataires', function (Blueprint $table) {
            $table->dropColumn(['statut_audit', 'notes_audit']);
        });

        Schema::table('contrats', function (Blueprint $table) {
            $table->dropColumn(['etat_des_lieux_entree', 'etat_des_lieux_sortie', 'date_avis_conge']);
        });

        Schema::table('factures', function (Blueprint $table) {
            $table->dropColumn('type_facture');
        });

        Schema::table('reversements', function (Blueprint $table) {
            $table->dropColumn('statut');
        });
    }
};
