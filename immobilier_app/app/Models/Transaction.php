<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'agence_id',
        'type_compte',
        'proprietaire_id',
        'contrat_id',
        'type_transaction',
        'sens',
        'montant',
        'devise',
        'reference',
        'description',
        'date_transaction',
        'paiement_id',
        'reversement_id',
        'depense_id',
        'facture_id',
        'user_id',
    ];

    protected $casts = [
        'date_transaction' => 'date',
        'montant' => 'decimal:2',
    ];

    public function agence()
    {
        return $this->belongsTo(Agence::class);
    }

    public function proprietaire()
    {
        return $this->belongsTo(Proprietaire::class);
    }

    public function contrat()
    {
        return $this->belongsTo(Contrat::class);
    }

    public function paiement()
    {
        return $this->belongsTo(Paiement::class);
    }

    public function reversement()
    {
        return $this->belongsTo(Reversement::class);
    }

    public function depense()
    {
        return $this->belongsTo(Depense::class);
    }

    public function facture()
    {
        return $this->belongsTo(Facture::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
