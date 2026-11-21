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
        // Check if old 'notifications' table exists and has the custom app notification schema
        // If it does, rename it to 'app_notifications'
        if (Schema::hasTable('notifications') && !Schema::hasTable('app_notifications')) {
            // Check if the table has agence_id (our custom field)
            if (Schema::hasColumn('notifications', 'agence_id')) {
                Schema::rename('notifications', 'app_notifications');
                return;
            }
        }
        
        // If app_notifications doesn't exist and notifications doesn't have agence_id, create app_notifications
        if (!Schema::hasTable('app_notifications')) {
            Schema::create('app_notifications', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->foreignId('agence_id')->constrained()->onDelete('cascade');
                $table->string('title');
                $table->text('message');
                $table->string('type')->default('info');
                $table->string('action_url')->nullable();
                $table->string('action_text')->nullable();
                $table->boolean('read')->default(false);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Don't rename app_notifications back - just drop it if we created it
        // This is safer than renaming since notifications table might be used by Laravel
        if (Schema::hasTable('app_notifications')) {
            Schema::dropIfExists('app_notifications');
        }
    }
};
