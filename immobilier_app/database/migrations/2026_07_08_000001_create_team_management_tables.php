<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Table des membres d'équipe
        Schema::create('team_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agence_id')->constrained('agences')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->string('nom_complet');
            $table->string('email')->unique();
            $table->string('telephone')->nullable();
            $table->string('invitation_token')->nullable();
            $table->boolean('is_invited')->default(true);
            $table->timestamp('invited_at')->nullable();
            $table->timestamp('activated_at')->nullable();
            $table->enum('status', ['pending', 'active', 'suspended', 'revoked'])->default('pending');
            $table->timestamps();
        });

        // Table des modules disponibles
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

        // Table des assignations de modules
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

        // Table d'historique des accès (Audit trail)
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

        // Table de configuration d'équipe par agence
        Schema::create('agency_team_config', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agence_id')->unique()->constrained('agences')->onDelete('cascade');
            $table->enum('mode', ['solo', 'team'])->default('solo');
            $table->boolean('onboarding_completed')->default(false);
            $table->timestamp('team_setup_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('access_histories');
        Schema::dropIfExists('module_assignments');
        Schema::dropIfExists('modules');
        Schema::dropIfExists('team_members');
        Schema::dropIfExists('agency_team_config');
    }
};
