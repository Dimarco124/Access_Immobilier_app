<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Models\AppNotification;
use Illuminate\Notifications\Notifiable;

use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'agence_id',
        'telephone',
        'is_active',
    ];

    public function agence()
    {
        return $this->belongsTo(Agence::class);
    }

    public function locataire()
    {
        return $this->hasOne(Locataire::class);
    }

    public function proprietaire()
    {
        return $this->hasOne(Proprietaire::class);
    }

    public function appNotifications()
    {
        return $this->hasMany(AppNotification::class);
    }

    public function unreadAppNotifications()
    {
        return $this->hasMany(AppNotification::class)->where('read', false);
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Vérifie si l'utilisateur a accès à un module au niveau spécifié.
     * Pour les admins, retourne toujours true.
     * Pour les membres d'équipe, vérifie leurs autorisations.
     */
    public function hasModuleAccess(string $moduleCode, string $minimumLevel = 'viewer'): bool
    {
        if ($this->hasRole('admin')) {
            return true;
        }

        $teamMember = \App\Models\TeamMember::with('modules')
                        ->where('user_id', $this->id)
                        ->where('status', 'active')
                        ->first();

        if (!$teamMember) {
            return false;
        }

        return $teamMember->hasModuleAccess($moduleCode, $minimumLevel);
    }
}
