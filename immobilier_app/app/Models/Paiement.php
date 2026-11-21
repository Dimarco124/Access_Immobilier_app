<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Paiement extends Model
{
    use HasFactory;

    protected $fillable = [
        'agence_id',
        'facture_id',
        'compte_bancaire_id',
        'montant_verse',
        'mode_paiement',
        'reference_transaction',
        'date_paiement',
    ];

    protected $casts = [
        'date_paiement' => 'date',
    ];

    public function agence()
    {
        return $this->belongsTo(Agence::class);
    }

    public function facture()
    {
        return $this->belongsTo(Facture::class);
    }

    public function compteBancaire()
    {
        return $this->belongsTo(CompteBancaire::class);
    }
}
