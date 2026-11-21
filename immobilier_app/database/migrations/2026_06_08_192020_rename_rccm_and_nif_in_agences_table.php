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
        Schema::table('agences', function (Blueprint $table) {
            $table->renameColumn('rccm', 'numero_agrement');
            $table->renameColumn('nif', 'compte_contribuable');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('agences', function (Blueprint $table) {
            $table->renameColumn('numero_agrement', 'rccm');
            $table->renameColumn('compte_contribuable', 'nif');
        });
    }
};
