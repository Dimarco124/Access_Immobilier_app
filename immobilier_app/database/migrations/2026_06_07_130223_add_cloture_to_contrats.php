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
            $table->date('date_cloture')->nullable()->after('statut');
            $table->decimal('montant_caution_retenu', 15, 2)->default(0)->after('date_cloture');
            $table->decimal('montant_caution_rembourse', 15, 2)->default(0)->after('montant_caution_retenu');
            $table->text('notes_cloture')->nullable()->after('montant_caution_rembourse');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contrats', function (Blueprint $table) {
            $table->dropColumn([
                'date_cloture',
                'montant_caution_retenu',
                'montant_caution_rembourse',
                'notes_cloture'
            ]);
        });
    }
};
