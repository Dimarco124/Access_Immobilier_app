<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'agence_id',
        'expediteur_id',
        'destinataire_id',
        'objet',
        'contenu',
        'piece_jointe_url',
        'lu',
        'lu_at',
    ];

    protected $casts = [
        'lu' => 'boolean',
        'lu_at' => 'datetime',
    ];

    public function agence()
    {
        return $this->belongsTo(Agence::class);
    }

    public function expediteur()
    {
        return $this->belongsTo(User::class, 'expediteur_id');
    }

    public function destinataire()
    {
        return $this->belongsTo(User::class, 'destinataire_id');
    }

    // Marquer comme lu
    public function marquerCommeLu()
    {
        $this->update([
            'lu' => true,
            'lu_at' => now(),
        ]);
    }

    // Scope pour les messages non lus
    public function scopeNonLus($query)
    {
        return $query->where('lu', false);
    }

    // Scope pour les messages reçus par un utilisateur
    public function scopeRecusPar($query, $userId)
    {
        return $query->where('destinataire_id', $userId);
    }

    // Scope pour les messages envoyés par un utilisateur
    public function scopeEnvoyesPar($query, $userId)
    {
        return $query->where('expediteur_id', $userId);
    }

    // Scope pour une conversation entre deux utilisateurs
    public function scopeConversation($query, $userId1, $userId2)
    {
        return $query->where(function($q) use ($userId1, $userId2) {
            $q->where(function($q2) use ($userId1, $userId2) {
                $q2->where('expediteur_id', $userId1)
                   ->where('destinataire_id', $userId2);
            })->orWhere(function($q2) use ($userId1, $userId2) {
                $q2->where('expediteur_id', $userId2)
                   ->where('destinataire_id', $userId1);
            });
        });
    }
}
