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
        Schema::table('paiements', function (Blueprint $table) {
            $table->foreignId('compte_bancaire_id')->nullable()->after('facture_id')->constrained('comptes_bancaires')->onDelete('set null');
        });

        Schema::table('depenses', function (Blueprint $table) {
            $table->foreignId('compte_bancaire_id')->nullable()->after('bien_id')->constrained('comptes_bancaires')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('depenses', function (Blueprint $table) {
            $table->dropForeign(['compte_bancaire_id']);
            $table->dropColumn('compte_bancaire_id');
        });

        Schema::table('paiements', function (Blueprint $table) {
            $table->dropForeign(['compte_bancaire_id']);
            $table->dropColumn('compte_bancaire_id');
        });
    }
};
