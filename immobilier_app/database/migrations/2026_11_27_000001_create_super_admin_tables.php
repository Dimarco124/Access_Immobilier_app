<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;
use App\Models\User;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('users', 'is_active')) {
            Schema::table('users', function (Blueprint $table) {
                $table->boolean('is_active')->default(true)->after('password');
            });
        }

        if (!Schema::hasTable('platform_announcements')) {
            Schema::create('platform_announcements', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->text('message');
                $table->string('type', 20)->default('info'); // info, success, warning, danger
                $table->boolean('is_active')->default(true);
                $table->timestamp('starts_at')->nullable();
                $table->timestamp('ends_at')->nullable();
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('platform_activity_logs')) {
            Schema::create('platform_activity_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('agence_id')->nullable()->constrained('agences')->nullOnDelete();
                $table->string('action', 100)->index();
                $table->text('description');
                $table->string('ip_address', 45)->nullable();
                $table->json('metadata')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('platform_settings')) {
            Schema::create('platform_settings', function (Blueprint $table) {
                $table->id();
                $table->string('key')->unique();
                $table->text('value')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('support_tickets')) {
            Schema::create('support_tickets', function (Blueprint $table) {
                $table->id();
                $table->foreignId('agence_id')->nullable()->constrained('agences')->nullOnDelete();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->string('subject');
                $table->text('message');
                $table->string('priority', 20)->default('normal'); // normal, high, urgent
                $table->string('status', 20)->default('open'); // open, in_progress, resolved, closed
                $table->text('admin_reply')->nullable();
                $table->timestamp('replied_at')->nullable();
                $table->foreignId('replied_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
            });
        }

        // Création idempotente du rôle super_admin et du compte Super Admin initial
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        $role = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);

        $email = env('SUPER_ADMIN_EMAIL', 'superadmin@habitat-immo.com');
        $password = env('SUPER_ADMIN_PASSWORD', 'SuperAdmin@2026!');

        $superAdmin = User::firstOrCreate(
            ['email' => $email],
            [
                'name' => 'Valdes Di Marco (Super Admin)',
                'password' => Hash::make($password),
                'agence_id' => null,
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        if (!$superAdmin->hasRole($role)) {
            $superAdmin->assignRole($role);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('support_tickets');
        Schema::dropIfExists('platform_settings');
        Schema::dropIfExists('platform_activity_logs');
        Schema::dropIfExists('platform_announcements');

        if (Schema::hasColumn('users', 'is_active')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('is_active');
            });
        }
    }
};
