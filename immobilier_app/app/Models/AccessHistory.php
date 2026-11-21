<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccessHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'team_member_id',
        'changed_by',
        'action',
        'module_code',
        'old_level',
        'new_level',
        'notes',
    ];

    // Relations
    public function teamMember()
    {
        return $this->belongsTo(TeamMember::class);
    }

    public function changedBy()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    // Méthodes
    public function getActionLabelAttribute()
    {
        return match($this->action) {
            'assigned' => 'Accès attribué',
            'revoked' => 'Accès révoqué',
            'modified' => 'Accès modifié',
            'suspended' => 'Accès suspendu',
            'activated' => 'Accès activé',
            default => 'Action inconnue',
        };
    }
}
