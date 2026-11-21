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
            $table->string('rccm')->nullable()->after('email');
            $table->string('nif')->nullable()->after('rccm');
            $table->string('site_web')->nullable()->after('nif');
            $table->text('description')->nullable()->after('site_web');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('agences', function (Blueprint $table) {
            $table->dropColumn(['rccm', 'nif', 'site_web', 'description']);
        });
    }
};
