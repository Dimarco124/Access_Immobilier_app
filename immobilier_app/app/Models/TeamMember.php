<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class TeamMember extends Model
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'agence_id',
        'user_id',
        'nom_complet',
        'email',
        'telephone',
        'invitation_token',
        'is_invited',
        'invited_at',
        'activated_at',
        'status',
    ];

    protected $casts = [
        'is_invited' => 'boolean',
        'invited_at' => 'datetime',
        'activated_at' => 'datetime',
    ];

    // Relations
    public function agence()
    {
        return $this->belongsTo(Agence::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function moduleAssignments()
    {
        return $this->hasMany(ModuleAssignment::class);
    }

    public function modules()
    {
        return $this->belongsToMany(Module::class, 'module_assignments')
                    ->withPivot('access_level', 'assigned_by', 'expires_at')
                    ->withTimestamps();
    }

    public function accessHistory()
    {
        return $this->hasMany(AccessHistory::class);
    }

    // Méthodes utiles
    public function isActive()
    {
        return $this->status === 'active';
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function hasModuleAccess($moduleCode, $minimumLevel = 'viewer')
    {
        $module = $this->modules->where('code', $moduleCode)->first();
        
        if (!$module) {
            return false;
        }

        $levels = ['viewer' => 1, 'collaborator' => 2, 'manager' => 3];
        $userLevel = $levels[$module->pivot->access_level] ?? 0;
        $requiredLevel = $levels[$minimumLevel] ?? 0;

        return $userLevel >= $requiredLevel;
    }



    public function getModulesListAttribute()
    {
        return $this->modules->pluck('nom')->join(', ');
    }
}
