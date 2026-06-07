<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Depense extends Model
{
    use HasFactory;

    protected $fillable = [
        'agence_id',
        'bien_id',
        'compte_bancaire_id',
        'libelle',
        'categorie',
        'montant',
        'date_depense',
    ];

    public function agence()
    {
        return $this->belongsTo(Agence::class);
    }

    public function bien()
    {
        return $this->belongsTo(Bien::class);
    }

    public function compteBancaire()
    {
        return $this->belongsTo(CompteBancaire::class);
    }
}
