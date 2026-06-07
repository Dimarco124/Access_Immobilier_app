<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contrat extends Model
{
    use HasFactory;

    protected $fillable = [
        'agence_id',
        'locataire_id',
        'bien_id',
        'date_debut',
        'date_fin',
        'montant_loyer_actuel',
        'montant_caution',
        'nombre_mois_avance',
        'frais_dossier',
        'statut',
        'date_cloture',
        'montant_caution_retenu',
        'montant_caution_rembourse',
        'notes_cloture',
    ];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin' => 'date',
        'date_cloture' => 'date',
    ];

    public function agence()
    {
        return $this->belongsTo(Agence::class);
    }

    public function locataire()
    {
        return $this->belongsTo(Locataire::class);
    }

    public function bien()
    {
        return $this->belongsTo(Bien::class);
    }

    public function factures()
    {
        return $this->hasMany(Facture::class);
    }
}
