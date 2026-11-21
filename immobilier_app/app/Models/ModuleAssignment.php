<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModuleAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'team_member_id',
        'module_id',
        'access_level',
        'assigned_by',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    // Relations
    public function teamMember()
    {
        return $this->belongsTo(TeamMember::class);
    }

    public function module()
    {
        return $this->belongsTo(Module::class);
    }

    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    // Méthodes
    public function isExpired()
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function isManager()
    {
        return $this->access_level === 'manager';
    }

    public function isCollaborator()
    {
        return $this->access_level === 'collaborator';
    }

    public function isViewer()
    {
        return $this->access_level === 'viewer';
    }

    public function getAccessLevelLabelAttribute()
    {
        return match($this->access_level) {
            'manager' => 'Gestionnaire',
            'collaborator' => 'Collaborateur',
            'viewer' => 'Consultant',
            default => 'Inconnu',
        };
    }
}
