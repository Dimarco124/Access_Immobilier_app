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
        // Récupérer toutes les factures de type Initial
        $factures = \App\Models\Facture::where('type_facture', 'Initial')
            ->where('mois_facture', 'Premier versement (Caution + Frais + Loyer)')
            ->get();

        foreach ($factures as $facture) {
            // Si la facture a une date_echeance, utiliser ça pour le mois_facture
            if ($facture->date_echeance) {
                $facture->update([
                    'mois_facture' => \Carbon\Carbon::parse($facture->date_echeance)->translatedFormat('F Y')
                ]);
            } else {
                // Sinon, utiliser la date de création de la facture
                $facture->update([
                    'mois_facture' => $facture->created_at->translatedFormat('F Y')
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Pas de rollback, car c'est une correction
    }
};
