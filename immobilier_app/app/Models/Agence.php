<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agence extends Model
{
    use HasFactory;
    protected $table = 'agences';
    protected $fillable = [
        'nom',
        'logo',
        'adresse',
        'telephone',
        'email',
        'taux_tva',
        'devise',
        'parametres_paiement',
        'statut'
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function proprietaires()
    {
        return $this->hasMany(Proprietaire::class);
    }

    public function biens()
    {
        return $this->hasMany(Bien::class);
    }

    public function locataires()
    {
        return $this->hasMany(Locataire::class);
    }

    public function contrats()
    {
        return $this->hasMany(Contrat::class);
    }

    public function factures()
    {
        return $this->hasMany(Facture::class);
    }

    public function paiements()
    {
        return $this->hasMany(Paiement::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function depenses()
    {
        return $this->hasMany(Depense::class);
    }

    public function comptesBancaires()
    {
        return $this->hasMany(CompteBancaire::class);
    }
}
