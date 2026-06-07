<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompteBancaire extends Model
{
    use HasFactory;

    protected $table = 'comptes_bancaires';

    protected $fillable = [
        'agence_id',
        'nom_banque',
        'numero_compte',
        'rib',
        'est_principal',
    ];

    public function agence()
    {
        return $this->belongsTo(Agence::class);
    }

    public function paiements()
    {
        return $this->hasMany(Paiement::class);
    }

    public function depenses()
    {
        return $this->hasMany(Depense::class);
    }

    public function reversements()
    {
        return $this->hasMany(Reversement::class);
    }
}
