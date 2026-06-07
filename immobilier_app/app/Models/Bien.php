<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bien extends Model
{
    use HasFactory;

    protected $fillable = [
        'agence_id',
        'parent_id',
        'proprietaire_id',
        'nom_bien',
        'type_bien',
        'adresse',
        'description',
        'taux_commission_agence',
        'prix_visite',
        'loyer_mensuel',
        'statut',
    ];

    public function parent()
    {
        return $this->belongsTo(Bien::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Bien::class, 'parent_id');
    }

    public function visites()
    {
        return $this->hasMany(Visite::class);
    }

    public function agence()
    {
        return $this->belongsTo(Agence::class);
    }

    public function proprietaire()
    {
        return $this->belongsTo(Proprietaire::class);
    }

    public function contrats()
    {
        return $this->hasMany(Contrat::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }
}
