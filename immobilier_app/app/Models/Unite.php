<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unite extends Model
{
    use HasFactory;

    protected $fillable = [
        'agence_id',
        'bien_id',
        'nom_unite',
        'type_unite',
        'adresse',
        'description',
        'prix_visite',
        'loyer_mensuel',
        'statut',
        'migrated_from_bien_id',
    ];

    public function agence()
    {
        return $this->belongsTo(Agence::class);
    }

    public function bien()
    {
        return $this->belongsTo(Bien::class);
    }

    public function contrats()
    {
        return $this->hasMany(Contrat::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function visites()
    {
        return $this->hasMany(Visite::class);
    }

    public function getLibelleCompletAttribute()
    {
        return $this->bien ? "{$this->bien->nom_bien} - {$this->nom_unite}" : $this->nom_unite;
    }
}
