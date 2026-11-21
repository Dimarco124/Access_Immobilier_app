<?php

namespace App\Helpers;

use App\Models\TeamMember;
use Illuminate\Support\Facades\Auth;

class ModuleHelper
{
    /**
     * Vérifie si l'utilisateur a accès à un module
     */
    public static function hasAccess(string $moduleCode, string $minimumLevel = 'viewer'): bool
    {
        $user = Auth::user();

        // Admin a toujours accès
        if ($user->hasRole('admin')) {
            return true;
        }

        // Vérifier si c'est un membre d'équipe
        $teamMember = TeamMember::where('user_id', $user->id)
                                ->where('agence_id', $user->agence_id)
                                ->where('status', 'active')
                                ->first();

        if (!$teamMember) {
            return false;
        }

        return $teamMember->hasModuleAccess($moduleCode, $minimumLevel);
    }

    /**
     * Récupère tous les modules accessibles par l'utilisateur
     */
    public static function getUserModules(): array
    {
        $user = Auth::user();

        // Admin a accès à tous les modules
        if ($user->hasRole('admin')) {
            return ['immobilier', 'commercial', 'finance', 'maintenance', 'relation_client', 'proprietaires'];
        }

        // Récupérer les modules du membre d'équipe
        $teamMember = TeamMember::where('user_id', $user->id)
                                ->where('agence_id', $user->agence_id)
                                ->where('status', 'active')
                                ->with('modules')
                                ->first();

        if (!$teamMember) {
            return [];
        }

        return $teamMember->modules->pluck('code')->toArray();
    }

    /**
     * Vérifie si l'utilisateur peut voir un KPI spécifique
     */
    public static function canViewKPI(string $kpiModule): bool
    {
        return in_array($kpiModule, self::getUserModules());
    }
}
