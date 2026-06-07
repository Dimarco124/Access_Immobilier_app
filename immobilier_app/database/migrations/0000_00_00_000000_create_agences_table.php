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
        Schema::create('agences', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('logo')->nullable();
            $table->string('adresse');
            $table->string('telephone');
            $table->string('email')->unique();
            $table->decimal('taux_tva', 5, 2)->default(18.00);
            $table->string('devise')->default('XOF'); // FCFA
            $table->json('parametres_paiement')->nullable();
            $table->enum('statut', ['actif', 'suspendu'])->default('actif');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agences');
    }
};
