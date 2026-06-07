<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('visites', function (Blueprint $table) {
            // Ajouter locataire_id et supprimer les colonnes manuelles
            if (!Schema::hasColumn('visites', 'locataire_id')) {
                $table->foreignId('locataire_id')->nullable()->after('bien_id')->constrained('locataires')->onDelete('set null');
            }
            
            // Supprimer les colonnes nom/téléphone si elles existent
            if (Schema::hasColumn('visites', 'nom_visiteur')) {
                $table->dropColumn('nom_visiteur');
            }
            if (Schema::hasColumn('visites', 'telephone_visiteur')) {
                $table->dropColumn('telephone_visiteur');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('visites', function (Blueprint $table) {
            if (Schema::hasColumn('visites', 'locataire_id')) {
                $table->dropForeign(['locataire_id']);
                $table->dropColumn('locataire_id');
            }
            
            // Recréer les colonnes anciennes
            if (!Schema::hasColumn('visites', 'nom_visiteur')) {
                $table->string('nom_visiteur');
            }
            if (!Schema::hasColumn('visites', 'telephone_visiteur')) {
                $table->string('telephone_visiteur');
            }
        });
    }
};
