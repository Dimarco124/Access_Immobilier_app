<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'agence_id',
        'locataire_id',
        'bien_id',
        'objet',
        'description_probleme',
        'priorite',
        'statut',
    ];

    public function agence()
    {
        return $this->belongsTo(Agence::class);
    }

    public function locataire()
    {
        return $this->belongsTo(Locataire::class);
    }

    public function bien()
    {
        return $this->belongsTo(Bien::class);
    }

    public function messages()
    {
        return $this->hasMany(MessageTicket::class);
    }
}
