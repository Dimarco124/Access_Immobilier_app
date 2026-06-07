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
        Schema::create('reversements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agence_id')->constrained('agences')->onDelete('cascade');
            $table->foreignId('proprietaire_id')->constrained('proprietaires')->onDelete('cascade');
            $table->foreignId('compte_bancaire_id')->nullable()->constrained('comptes_bancaires')->onDelete('set null');
            $table->decimal('montant_total_loyers', 15, 2)->default(0);
            $table->decimal('commission_agence', 15, 2)->default(0);
            $table->decimal('montant_depenses', 15, 2)->default(0);
            $table->decimal('montant_net_verse', 15, 2);
            $table->string('periode');
            $table->date('date_reversement');
            $table->string('reference_transaction')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reversements');
    }
};
