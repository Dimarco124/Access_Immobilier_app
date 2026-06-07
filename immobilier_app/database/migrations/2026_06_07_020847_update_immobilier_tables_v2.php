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
        Schema::table('proprietaires', function (Blueprint $table) {
            $table->enum('civilite', ['M.', 'Mme', 'Mlle'])->after('user_id')->nullable();
            $table->string('contact_secondaire')->after('numero_piece_identite')->nullable();
            $table->string('pays')->after('contact_secondaire')->nullable();
            $table->string('ville')->after('pays')->nullable();
        });

        Schema::table('biens', function (Blueprint $table) {
            $table->foreignId('parent_id')->after('agence_id')->nullable()->constrained('biens')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('proprietaires', function (Blueprint $table) {
            $table->dropColumn(['civilite', 'contact_secondaire', 'pays', 'ville']);
        });

        Schema::table('biens', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropColumn('parent_id');
        });
    }
};
