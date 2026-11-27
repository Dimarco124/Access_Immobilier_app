<?php

namespace App\Http\Middleware;

use App\Models\PlatformSetting;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckPlatformAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        $isSuperAdmin = $user && $user->hasRole('super_admin');
        $isImpersonating = $request->session()->has('impersonator_id');

        // 1. Mode maintenance global (le Super Admin et les pages d'auth restent accessibles)
        if (PlatformSetting::getValue('maintenance_mode') === '1') {
            $allowedPaths = ['login', 'logout', 'csrf-refresh', 'up'];
            $isAllowedPath = $request->is(...$allowedPaths) || $request->is('super-admin*');

            if (!$isSuperAdmin && !$isImpersonating && !$isAllowedPath) {
                $message = PlatformSetting::getValue(
                    'maintenance_message',
                    'La plateforme est actuellement en maintenance pour amélioration. Nous serons de retour très rapidement.'
                );
                return response()->view('errors.maintenance', compact('message'), 503);
            }
        }

        // 2. Vérification compte actif & agence active (sauf Super Admin et impersonation support)
        if ($user && !$isSuperAdmin && !$isImpersonating) {
            if ($user->is_active === false) {
                Auth::guard('web')->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')
                    ->withErrors(['email' => 'Votre compte a été suspendu par l\'administrateur de la plateforme.']);
            }

            if ($user->agence && $user->agence->statut === 'suspendu') {
                Auth::guard('web')->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')
                    ->withErrors(['email' => 'L\'accès de votre agence a été temporairement suspendu. Veuillez contacter le support.']);
            }
        }

        return $next($request);
    }
}
