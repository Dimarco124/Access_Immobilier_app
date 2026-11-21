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
        'cni_recto',
        'cni_verso',
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

    public function famillesDeBiens()
    {
        return $this->hasMany(FamilleDeBien::class);
    }

    public function proprietes()
    {
        return $this->hasMany(FamilleDeBien::class);
    }

    public function reversements()
    {
        return $this->hasMany(Reversement::class);
    }

    /**
     * URL sécurisée pour consulter ou télécharger une pièce d'identité
     */
    public function getDocumentUrl(string $type, bool $download = false): ?string
    {
        if (empty($this->{$type})) {
            return null;
        }

        return route('documents.proprietaire', [
            'proprietaire' => $this->id,
            'type' => $type,
            'download' => $download ? 1 : null,
        ]);
    }
}
