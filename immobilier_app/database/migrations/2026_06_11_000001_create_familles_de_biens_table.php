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
        Schema::create('familles_de_biens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agence_id')->constrained('agences')->onDelete('cascade');
            $table->foreignId('proprietaire_id')->constrained('proprietaires')->onDelete('cascade');
            $table->string('nom_famille'); // Ex: Immeuble A, Complexe B
            $table->string('type_famille'); // Immeuble, Complexe résidentiel, Villa, etc.
            $table->string('adresse');
            $table->text('description')->nullable();
            $table->decimal('taux_commission_agence', 5, 2)->default(10.00);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('familles_de_biens');
    }
};
