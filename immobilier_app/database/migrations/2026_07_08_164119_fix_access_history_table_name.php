<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Renommer la table access_history en access_histories
        if (Schema::hasTable('access_history')) {
            Schema::rename('access_history', 'access_histories');
        }
        
        // Si la table n'existe pas du tout, la créer
        if (!Schema::hasTable('access_histories')) {
            Schema::create('access_histories', function (Blueprint $table) {
                $table->id();
                $table->foreignId('team_member_id')->constrained('team_members')->onDelete('cascade');
                $table->foreignId('changed_by')->nullable()->constrained('users')->onDelete('set null');
                $table->enum('action', ['assigned', 'revoked', 'modified', 'suspended', 'activated'])->default('assigned');
                $table->string('module_code')->nullable();
                $table->string('old_level')->nullable();
                $table->string('new_level')->nullable();
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        // Renommer en sens inverse si nécessaire
        if (Schema::hasTable('access_histories') && !Schema::hasTable('access_history')) {
            Schema::rename('access_histories', 'access_history');
        }
    }
};
