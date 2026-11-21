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
        'unite_id',
        'visite_id',
        'numero_contrat',
        'date_debut',
        'date_fin',
        'date_avis_conge',
        'montant_loyer_actuel',
        'montant_caution',
        'nombre_mois_avance',
        'frais_dossier',
        'etat_des_lieux_entree',
        'etat_des_lieux_sortie',
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
        'date_avis_conge' => 'date',
    ];

    // Accessor pour calculer la caution disponible
    public function getCautionDisponibleAttribute()
    {
        $caution = $this->montant_caution ?? 0;
        $rembourse = $this->montant_caution_rembourse ?? 0;
        $retenu = $this->montant_caution_retenu ?? 0;
        return $caution - $rembourse - $retenu;
    }

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

    public function unite()
    {
        return $this->belongsTo(Unite::class);
    }

    public function visite()
    {
        return $this->belongsTo(Visite::class);
    }

    public function factures()
    {
        return $this->hasMany(Facture::class);
    }
}
