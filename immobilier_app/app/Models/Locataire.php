<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Locataire extends Model
{
    use HasFactory;

    protected $fillable = [
        'agence_id',
        'user_id',
        'contact_urgence',
        'profession',
        'copie_piece_identite',
    ];

    public function agence()
    {
        return $this->belongsTo(Agence::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
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
}
