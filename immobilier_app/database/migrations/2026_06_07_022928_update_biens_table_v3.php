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
        Schema::table('biens', function (Blueprint $table) {
            $table->decimal('prix_visite', 15, 2)->default(0)->after('taux_commission_agence');
            $table->decimal('loyer_mensuel', 15, 2)->nullable()->after('prix_visite');
            $table->enum('statut', ['Disponible', 'Loué', 'En maintenance'])->default('Disponible')->after('loyer_mensuel');
        });

        Schema::create('visites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agence_id')->constrained('agences')->onDelete('cascade');
            $table->foreignId('bien_id')->constrained('biens')->onDelete('cascade');
            $table->string('nom_visiteur');
            $table->string('telephone_visiteur');
            $table->dateTime('date_heure_visite');
            $table->decimal('montant_paye', 15, 2)->default(0);
            $table->enum('statut', ['Programmé', 'Effectué', 'Annulé'])->default('Programmé');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visites');
        Schema::table('biens', function (Blueprint $table) {
            $table->dropColumn(['prix_visite', 'loyer_mensuel', 'statut']);
        });
    }
};
