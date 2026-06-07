<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Facture extends Model
{
    use HasFactory;

    protected $fillable = [
        'agence_id',
        'contrat_id',
        'numero_facture',
        'mois_facture',
        'montant_hors_taxe',
        'montant_tva',
        'montant_total_du',
        'montant_deja_paye',
        'date_echeance',
        'statut',
    ];

    public function agence()
    {
        return $this->belongsTo(Agence::class);
    }

    public function contrat()
    {
        return $this->belongsTo(Contrat::class);
    }

    public function paiements()
    {
        return $this->hasMany(Paiement::class);
    }
}
