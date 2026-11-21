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
        Schema::table('contrats', function (Blueprint $table) {
            $table->string('numero_contrat')->nullable()->after('id');
        });

        // Mettre à jour les contrats existants
        \App\Models\Contrat::all()->each(function ($contrat) {
            $contrat->update(['numero_contrat' => 'CB-' . str_pad($contrat->id, 6, '0', STR_PAD_LEFT)]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contrats', function (Blueprint $table) {
            $table->dropColumn('numero_contrat');
        });
    }
};
