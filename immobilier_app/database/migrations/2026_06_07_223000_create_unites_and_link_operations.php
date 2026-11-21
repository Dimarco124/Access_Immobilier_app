<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('unites')) {
            Schema::create('unites', function (Blueprint $table) {
                $table->id();
                $table->foreignId('agence_id')->constrained('agences')->onDelete('cascade');
                $table->foreignId('bien_id')->constrained('biens')->onDelete('cascade');
                $table->string('nom_unite');
                $table->string('type_unite');
                $table->string('adresse')->nullable();
                $table->text('description')->nullable();
                $table->decimal('prix_visite', 15, 2)->default(0);
                $table->decimal('loyer_mensuel', 15, 2);
                $table->enum('statut', ['Disponible', 'Loué', 'En maintenance'])->default('Disponible');
                $table->foreignId('migrated_from_bien_id')->nullable()->constrained('biens')->nullOnDelete();
                $table->timestamps();
            });
        }

        foreach (['contrats', 'tickets', 'visites'] as $tableName) {
            if (Schema::hasTable($tableName) && !Schema::hasColumn($tableName, 'unite_id')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->foreignId('unite_id')->nullable()->after('bien_id')->constrained('unites')->nullOnDelete();
                });
            }
        }

        $now = now();
        $biens = DB::table('biens')->orderBy('parent_id')->orderBy('id')->get();

        foreach ($biens as $bien) {
            $isChild = $bien->parent_id !== null;
            $hasChildren = DB::table('biens')->where('parent_id', $bien->id)->exists();
            $shouldCreateUnit = $isChild || (!$hasChildren && $bien->loyer_mensuel !== null);

            if (!$shouldCreateUnit) {
                continue;
            }

            $containerId = $isChild ? $bien->parent_id : $bien->id;
            $existing = DB::table('unites')->where('migrated_from_bien_id', $bien->id)->first();

            if (!$existing) {
                $unitId = DB::table('unites')->insertGetId([
                    'agence_id' => $bien->agence_id,
                    'bien_id' => $containerId,
                    'nom_unite' => $isChild ? $bien->nom_bien : 'Unité principale',
                    'type_unite' => $bien->type_bien,
                    'adresse' => $bien->adresse,
                    'description' => $bien->description,
                    'prix_visite' => $bien->prix_visite ?? 0,
                    'loyer_mensuel' => $bien->loyer_mensuel ?? 0,
                    'statut' => $bien->statut ?? 'Disponible',
                    'migrated_from_bien_id' => $bien->id,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            } else {
                $unitId = $existing->id;
            }

            foreach (['contrats', 'tickets', 'visites'] as $tableName) {
                if (Schema::hasColumn($tableName, 'bien_id') && Schema::hasColumn($tableName, 'unite_id')) {
                    DB::table($tableName)
                        ->where('bien_id', $bien->id)
                        ->whereNull('unite_id')
                        ->update([
                            'bien_id' => $containerId,
                            'unite_id' => $unitId,
                        ]);
                }
            }
        }
    }

    public function down(): void
    {
        foreach (['visites', 'tickets', 'contrats'] as $tableName) {
            if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, 'unite_id')) {
                Schema::table($tableName, function (Blueprint $table) {
                    $table->dropConstrainedForeignId('unite_id');
                });
            }
        }

        Schema::dropIfExists('unites');
    }
};
