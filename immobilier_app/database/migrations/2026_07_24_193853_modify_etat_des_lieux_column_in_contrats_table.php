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
            // Changer de TEXT à LONGTEXT pour supporter de gros JSON
            $table->longText('etat_des_lieux_entree')->nullable()->change();
            $table->longText('etat_des_lieux_sortie')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contrats', function (Blueprint $table) {
            $table->text('etat_des_lieux_entree')->nullable()->change();
            $table->text('etat_des_lieux_sortie')->nullable()->change();
        });
    }
};
