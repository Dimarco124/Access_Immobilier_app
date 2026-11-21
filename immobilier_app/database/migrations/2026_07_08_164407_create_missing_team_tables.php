<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Créer la table modules si elle n'existe pas
        if (!Schema::hasTable('modules')) {
            Schema::create('modules', function (Blueprint $table) {
                $table->id();
                $table->string('code')->unique(); // ex: 'immobilier', 'commercial', 'finance'
                $table->string('nom');
                $table->text('description');
                $table->string('icon')->nullable();
                $table->boolean('is_required')->default(false); // Si le module est obligatoire
                $table->integer('ordre')->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // Créer la table module_assignments si elle n'existe pas
        if (!Schema::hasTable('module_assignments')) {
            Schema::create('module_assignments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('team_member_id')->constrained('team_members')->onDelete('cascade');
                $table->foreignId('module_id')->constrained('modules')->onDelete('cascade');
                $table->enum('access_level', ['manager', 'collaborator', 'viewer'])->default('manager');
                $table->foreignId('assigned_by')->nullable()->constrained('users')->onDelete('set null');
                $table->timestamp('expires_at')->nullable(); // Pour accès temporaire
                $table->timestamps();

                $table->unique(['team_member_id', 'module_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('module_assignments');
        Schema::dropIfExists('modules');
    }
};
