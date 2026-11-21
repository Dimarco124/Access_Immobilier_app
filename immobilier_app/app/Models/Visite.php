<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Visite extends Model
{
    use HasFactory;

    protected $fillable = [
        'agence_id',
        'bien_id',
        'unite_id',
        'locataire_id',
        'date_heure_visite',
        'montant_paye',
        'statut',
        'notes',
    ];

    protected $casts = [
        'date_heure_visite' => 'datetime',
    ];

    public function agence()
    {
        return $this->belongsTo(Agence::class);
    }

    public function bien()
    {
        return $this->belongsTo(Bien::class);
    }

    public function unite()
    {
        return $this->belongsTo(Unite::class);
    }

    public function locataire()
    {
        return $this->belongsTo(Locataire::class);
    }
}
