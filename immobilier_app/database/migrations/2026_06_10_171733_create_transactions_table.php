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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agence_id')->constrained('agences')->onDelete('cascade');
            $table->string('type_compte'); // 'caution', 'proprietaire', 'agence'
            $table->foreignId('proprietaire_id')->nullable()->constrained('proprietaires')->onDelete('cascade');
            $table->foreignId('contrat_id')->nullable()->constrained('contrats')->onDelete('cascade');
            $table->string('type_transaction'); // 'depot_caution', 'remboursement_caution', 'loyer', 'reversement', 'commission', 'depense', 'frais_dossier'
            $table->string('sens'); // 'debit', 'credit'
            $table->decimal('montant', 15, 2);
            $table->string('devise', 10)->default('XOF');
            $table->string('reference');
            $table->text('description')->nullable();
            $table->date('date_transaction');
            $table->foreignId('paiement_id')->nullable()->constrained('paiements')->onDelete('set null');
            $table->foreignId('reversement_id')->nullable()->constrained('reversements')->onDelete('set null');
            $table->foreignId('depense_id')->nullable()->constrained('depenses')->onDelete('set null');
            $table->foreignId('facture_id')->nullable()->constrained('factures')->onDelete('set null');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
