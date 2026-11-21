<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'nom',
        'description',
        'icon',
        'is_required',
        'ordre',
        'is_active',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'is_active' => 'boolean',
    ];

    // Relations
    public function teamMembers()
    {
        return $this->belongsToMany(TeamMember::class, 'module_assignments')
                    ->withPivot('access_level', 'assigned_by', 'expires_at')
                    ->withTimestamps();
    }

    public function assignments()
    {
        return $this->hasMany(ModuleAssignment::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeRequired($query)
    {
        return $query->where('is_required', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('ordre');
    }
}
