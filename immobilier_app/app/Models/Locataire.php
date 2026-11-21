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
        'civilite',
        'contact_urgence',
        'profession',
        'adresse_residence',
        'numero_piece_identite',
        'pays',
        'ville',
        'cni_recto',
        'cni_verso',
        'copie_piece_identite',
        'statut_audit',
        'notes_audit',
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

    /**
     * URL sécurisée pour consulter ou télécharger une pièce d'identité
     */
    public function getDocumentUrl(string $type, bool $download = false): ?string
    {
        if (empty($this->{$type})) {
            return null;
        }

        return route('documents.locataire', [
            'locataire' => $this->id,
            'type' => $type,
            'download' => $download ? 1 : null,
        ]);
    }
}
