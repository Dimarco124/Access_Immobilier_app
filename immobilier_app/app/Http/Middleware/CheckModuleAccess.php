<?php

namespace App\Http\Middleware;

use App\Models\TeamMember;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class CheckModuleAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $moduleCode
     * @param  string  $minimumLevel
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string $moduleCode, string $minimumLevel = 'viewer')
    {
        $user = Auth::user();

        // CRITIQUE: Admin a TOUJOURS accès à TOUT - ne jamais bloquer l'admin
        if ($user && $user->hasRole('admin')) {
            return $next($request);
        }

        // Si ce n'est pas un admin, vérifier que c'est un team_member
        if (!$user || !$user->hasRole('team_member')) {
            abort(403, 'Accès refusé. Vous devez être administrateur ou membre de l\'équipe.');
        }

        // Pour les team_members, vérifier les modules
        $teamMemberId = Cache::remember('team_member_id_' . $user->id, 60, function () use ($user) {
            $tm = TeamMember::where('user_id', $user->id)
                            ->where('agence_id', $user->agence_id)
                            ->where('status', 'active')
                            ->value('id');
            return $tm;
        });

        if (!$teamMemberId) {
            abort(403, 'Vous n\'avez pas accès à cette section.');
        }

        $teamMember = TeamMember::with('modules')
                                ->where('id', $teamMemberId)
                                ->where('status', 'active')
                                ->first();

        if (!$teamMember) {
            Cache::forget('team_member_id_' . $user->id);
            abort(403, 'Votre compte a été suspendu ou supprimé.');
        }

        if (!$teamMember->hasModuleAccess($moduleCode, $minimumLevel)) {
            abort(403, 'Vous n\'avez pas les permissions nécessaires pour accéder à ce module.');
        }

        return $next($request);
    }
}
