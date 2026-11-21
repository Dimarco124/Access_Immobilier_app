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
        Schema::table('biens', function (Blueprint $table) {
            $table->foreignId('famille_de_bien_id')->nullable()->constrained('familles_de_biens')->onDelete('cascade')->after('agence_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('biens', function (Blueprint $table) {
            $table->dropForeign(['famille_de_bien_id']);
            $table->dropColumn('famille_de_bien_id');
        });
    }
};
