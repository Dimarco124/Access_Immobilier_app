<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bien extends Model
{
    use HasFactory;

    protected $fillable = [
        'agence_id',
        'famille_de_bien_id',
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

    public function familleDeBien()
    {
        return $this->belongsTo(FamilleDeBien::class);
    }

    public function parent()
    {
        return $this->belongsTo(Bien::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Bien::class, 'parent_id');
    }

    public function unites()
    {
        return $this->hasMany(Unite::class);
    }

    public function unite()
    {
        return $this->hasOne(Unite::class, 'migrated_from_bien_id');
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

    /**
     * Synchronise le statut du bien en fonction de ses unités
     */
    public function syncStatus()
    {
        // Si le bien a des unités, son statut dépend d'elles
        if ($this->unites()->exists()) {
            $totalUnites = $this->unites()->count();
            $unitesLouees = $this->unites()->where('statut', 'Loué')->count();

            if ($unitesLouees === 0) {
                $this->update(['statut' => 'Disponible']);
            } elseif ($unitesLouees === $totalUnites) {
                $this->update(['statut' => 'Loué']);
            } else {
                // Si partiellement loué, on garde 'Disponible' ou on pourrait ajouter un statut 
                // mais restons sur les enums existants pour éviter les erreurs SQL.
                // On considère que l'immeuble est toujours disponible tant qu'il reste une unité.
                $this->update(['statut' => 'Disponible']);
            }
        }
    }
}
