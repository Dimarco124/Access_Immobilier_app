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
        Schema::table('factures', function (Blueprint $table) {
            $table->decimal('frais_dossier', 15, 2)->default(0)->after('type_facture');
            $table->decimal('montant_caution', 15, 2)->default(0)->after('frais_dossier');
            $table->decimal('montant_loyer_initial', 15, 2)->default(0)->after('montant_caution');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('factures', function (Blueprint $table) {
            $table->dropColumn(['frais_dossier', 'montant_caution', 'montant_loyer_initial']);
        });
    }
};
