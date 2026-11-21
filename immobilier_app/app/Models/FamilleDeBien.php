<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FamilleDeBien extends Model
{
    use HasFactory;

    protected $table = 'familles_de_biens';

    protected $fillable = [
        'agence_id',
        'proprietaire_id',
        'nom_famille',
        'type_famille', // Immeuble, Complexe, Villa avec dépendances, etc.
        'nature',       // collective (multi-unités) ou individuelle (mono-unité)
        'adresse',
        'description',
    ];

    public function isIndividuelle(): bool
    {
        return $this->nature === 'individuelle';
    }

    public function isCollective(): bool
    {
        return $this->nature !== 'individuelle';
    }

    public function agence()
    {
        return $this->belongsTo(Agence::class);
    }

    public function proprietaire()
    {
        return $this->belongsTo(Proprietaire::class);
    }

    public function biens()
    {
        return $this->hasMany(Bien::class);
    }

    public function depenses()
    {
        return $this->hasMany(Depense::class);
    }
}
