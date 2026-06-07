<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Proprietaire extends Model
{
    use HasFactory;

    protected $fillable = [
        'agence_id',
        'user_id',
        'civilite',
        'adresse_residence',
        'numero_piece_identite',
        'contact_secondaire',
        'pays',
        'ville',
        'rib_bancaire',
    ];

    public function agence()
    {
        return $this->belongsTo(Agence::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function biens()
    {
        return $this->hasMany(Bien::class);
    }

    public function reversements()
    {
        return $this->hasMany(Reversement::class);
    }
}
