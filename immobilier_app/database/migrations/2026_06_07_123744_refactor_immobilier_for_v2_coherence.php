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
        // 1. Mettre à jour contrats
        Schema::table('contrats', function (Blueprint $table) {
            $table->dropForeign(['unite_id']);
            $table->dropColumn('unite_id');
            $table->foreignId('bien_id')->after('locataire_id')->nullable()->constrained('biens')->onDelete('cascade');
        });

        // 2. Mettre à jour tickets
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropForeign(['unite_id']);
            $table->dropColumn('unite_id');
            $table->foreignId('bien_id')->after('locataire_id')->nullable()->constrained('biens')->onDelete('cascade');
        });

        // 3. Supprimer la table unites (obsolète avec l'architecture V2 des sous-biens)
        Schema::dropIfExists('unites');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recréer la table unites
        Schema::create('unites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bien_id')->constrained('biens')->onDelete('cascade');
            $table->string('nom_unite');
            $table->string('type_unite');
            $table->string('etage')->nullable();
            $table->decimal('surface', 10, 2)->nullable();
            $table->decimal('loyer_de_base', 15, 2);
            $table->enum('statut', ['Disponible', 'Loué', 'En maintenance'])->default('Disponible');
            $table->timestamps();
        });

        // Restaurer contrats
        Schema::table('contrats', function (Blueprint $table) {
            $table->dropForeign(['bien_id']);
            $table->dropColumn('bien_id');
            $table->foreignId('unite_id')->after('locataire_id')->constrained('unites')->onDelete('cascade');
        });

        // Restaurer tickets
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropForeign(['bien_id']);
            $table->dropColumn('bien_id');
            $table->foreignId('unite_id')->after('locataire_id')->nullable()->constrained('unites')->onDelete('set null');
        });
    }
};
