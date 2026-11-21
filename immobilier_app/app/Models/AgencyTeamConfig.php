<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgencyTeamConfig extends Model
{
    use HasFactory;

    protected $table = 'agency_team_config';

    protected $fillable = [
        'agence_id',
        'mode',
        'onboarding_completed',
        'team_setup_at',
    ];

    protected $casts = [
        'onboarding_completed' => 'boolean',
        'team_setup_at' => 'datetime',
    ];

    // Relations
    public function agence()
    {
        return $this->belongsTo(Agence::class);
    }

    // Méthodes
    public function isSoloMode()
    {
        return $this->mode === 'solo';
    }

    public function isTeamMode()
    {
        return $this->mode === 'team';
    }

    public function switchToTeamMode()
    {
        $this->mode = 'team';
        $this->team_setup_at = now();
        $this->save();
    }

    public function switchToSoloMode()
    {
        $this->mode = 'solo';
        $this->save();
    }

    public function completeOnboarding()
    {
        $this->onboarding_completed = true;
        $this->save();
    }
}
