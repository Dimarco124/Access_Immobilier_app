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
        // 1. Propriétaires
        Schema::create('proprietaires', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agence_id')->constrained('agences')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('adresse_residence')->nullable();
            $table->string('numero_piece_identite')->nullable();
            $table->string('rib_bancaire')->nullable();
            $table->timestamps();
        });

        // 2. Biens
        Schema::create('biens', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agence_id')->constrained('agences')->onDelete('cascade');
            $table->foreignId('proprietaire_id')->constrained('proprietaires')->onDelete('cascade');
            $table->string('nom_bien');
            $table->string('type_bien'); // Immeuble, Villa, Magasin
            $table->string('adresse');
            $table->text('description')->nullable();
            $table->decimal('taux_commission_agence', 5, 2)->default(10.00);
            $table->timestamps();
        });

        // 3. Unités
        Schema::create('unites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bien_id')->constrained('biens')->onDelete('cascade');
            $table->string('nom_unite'); // Ex: Appt A1
            $table->string('type_unite'); // Studio, F2, F3
            $table->string('etage')->nullable();
            $table->decimal('surface', 10, 2)->nullable();
            $table->decimal('loyer_de_base', 15, 2);
            $table->enum('statut', ['Disponible', 'Loué', 'En maintenance'])->default('Disponible');
            $table->timestamps();
        });

        // 4. Locataires
        Schema::create('locataires', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agence_id')->constrained('agences')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('contact_urgence')->nullable();
            $table->string('profession')->nullable();
            $table->string('copie_piece_identite')->nullable();
            $table->timestamps();
        });

        // 5. Contrats
        Schema::create('contrats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agence_id')->constrained('agences')->onDelete('cascade');
            $table->foreignId('locataire_id')->constrained('locataires')->onDelete('cascade');
            $table->foreignId('unite_id')->constrained('unites')->onDelete('cascade');
            $table->date('date_debut');
            $table->date('date_fin')->nullable();
            $table->decimal('montant_loyer_actuel', 15, 2);
            $table->decimal('montant_caution', 15, 2)->default(0);
            $table->integer('nombre_mois_avance')->default(0);
            $table->decimal('frais_dossier', 15, 2)->default(0);
            $table->enum('statut', ['Actif', 'Terminé', 'Contentieux'])->default('Actif');
            $table->timestamps();
        });

        // 6. Factures
        Schema::create('factures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agence_id')->constrained('agences')->onDelete('cascade');
            $table->foreignId('contrat_id')->constrained('contrats')->onDelete('cascade');
            $table->string('numero_facture')->unique();
            $table->string('mois_facture'); // Ex: Juin 2026
            $table->decimal('montant_hors_taxe', 15, 2);
            $table->decimal('montant_tva', 15, 2)->default(0);
            $table->decimal('montant_total_du', 15, 2);
            $table->decimal('montant_deja_paye', 15, 2)->default(0);
            $table->date('date_echeance');
            $table->enum('statut', ['Impayée', 'Partielle', 'Payée'])->default('Impayée');
            $table->timestamps();
        });

        // 7. Paiements
        Schema::create('paiements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agence_id')->constrained('agences')->onDelete('cascade');
            $table->foreignId('facture_id')->constrained('factures')->onDelete('cascade');
            $table->decimal('montant_verse', 15, 2);
            $table->string('mode_paiement'); // Espèces, Chèque, Mobile Money, Virement
            $table->string('reference_transaction')->nullable();
            $table->date('date_paiement');
            $table->timestamps();
        });

        // 8. Tickets
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agence_id')->constrained('agences')->onDelete('cascade');
            $table->foreignId('locataire_id')->constrained('locataires')->onDelete('cascade');
            $table->foreignId('unite_id')->nullable()->constrained('unites')->onDelete('set null');
            $table->string('objet');
            $table->text('description_probleme');
            $table->enum('priorite', ['Faible', 'Urgent', 'Critique'])->default('Faible');
            $table->enum('statut', ['Nouveau', 'En cours', 'Résolu'])->default('Nouveau');
            $table->timestamps();
        });

        // 9. Messages de Tickets
        Schema::create('messages_ticket', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained('tickets')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->text('contenu_message');
            $table->string('piece_jointe_url')->nullable();
            $table->timestamps();
        });

        // 10. Dépenses
        Schema::create('depenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agence_id')->constrained('agences')->onDelete('cascade');
            $table->foreignId('bien_id')->nullable()->constrained('biens')->onDelete('set null');
            $table->string('libelle');
            $table->string('categorie'); // Réparation, Impôt, Salaire
            $table->decimal('montant', 15, 2);
            $table->date('date_depense');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('depenses');
        Schema::dropIfExists('messages_ticket');
        Schema::dropIfExists('tickets');
        Schema::dropIfExists('paiements');
        Schema::dropIfExists('factures');
        Schema::dropIfExists('contrats');
        Schema::dropIfExists('locataires');
        Schema::dropIfExists('unites');
        Schema::dropIfExists('biens');
        Schema::dropIfExists('proprietaires');
    }
};
