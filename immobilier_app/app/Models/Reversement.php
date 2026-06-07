<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reversement extends Model
{
    use HasFactory;

    protected $fillable = [
        'agence_id',
        'proprietaire_id',
        'compte_bancaire_id',
        'montant_total_loyers',
        'commission_agence',
        'montant_depenses',
        'montant_net_verse',
        'periode',
        'date_reversement',
        'reference_transaction',
        'notes',
    ];

    protected $casts = [
        'date_reversement' => 'date',
    ];

    public function agence()
    {
        return $this->belongsTo(Agence::class);
    }

    public function proprietaire()
    {
        return $this->belongsTo(Proprietaire::class);
    }

    public function compteBancaire()
    {
        return $this->belongsTo(CompteBancaire::class);
    }
}
