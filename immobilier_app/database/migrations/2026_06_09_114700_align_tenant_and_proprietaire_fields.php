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
        // 1. Aligner les locataires sur les propriétaires
        Schema::table('locataires', function (Blueprint $table) {
            $table->enum('civilite', ['M.', 'Mme', 'Mlle'])->after('user_id')->nullable();
            $table->string('adresse_residence')->after('profession')->nullable();
            $table->string('numero_piece_identite')->after('adresse_residence')->nullable();
            $table->string('pays')->after('numero_piece_identite')->nullable();
            $table->string('ville')->after('pays')->nullable();
            // Champs pour CNI Recto/Verso (on renomme l'ancien champ ou on en ajoute de nouveaux)
            $table->string('cni_recto')->nullable()->after('ville');
            $table->string('cni_verso')->nullable()->after('cni_recto');
        });

        // 2. Ajouter CNI Recto/Verso aux propriétaires
        Schema::table('proprietaires', function (Blueprint $table) {
            $table->string('cni_recto')->nullable()->after('rib_bancaire');
            $table->string('cni_verso')->nullable()->after('cni_recto');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('locataires', function (Blueprint $table) {
            $table->dropColumn(['civilite', 'adresse_residence', 'numero_piece_identite', 'pays', 'ville', 'cni_recto', 'cni_verso']);
        });

        Schema::table('proprietaires', function (Blueprint $table) {
            $table->dropColumn(['cni_recto', 'cni_verso']);
        });
    }
};
