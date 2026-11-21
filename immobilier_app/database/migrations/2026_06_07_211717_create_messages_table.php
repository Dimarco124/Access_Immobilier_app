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
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agence_id')->constrained('agences')->onDelete('cascade');
            $table->foreignId('expediteur_id')->constrained('users')->onDelete('cascade'); // Qui envoie
            $table->foreignId('destinataire_id')->constrained('users')->onDelete('cascade'); // Qui reçoit
            $table->string('objet');
            $table->text('contenu');
            $table->string('piece_jointe_url')->nullable();
            $table->boolean('lu')->default(false);
            $table->timestamp('lu_at')->nullable();
            $table->timestamps();
            
            $table->index(['expediteur_id', 'destinataire_id']);
            $table->index('lu');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
