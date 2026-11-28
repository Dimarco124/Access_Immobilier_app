<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Agence;
use App\Models\PlatformActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSupportController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with(['agence', 'roles'])
            ->whereDoesntHave('roles', fn ($q) => $q->where('name', 'super_admin'));

        if ($search = trim((string) $request->input('search'))) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('telephone', 'like', "%{$search}%");
            });
        }

        if ($agenceId = $request->input('agence_id')) {
            $query->where('agence_id', $agenceId);
        }

        if ($role = $request->input('role')) {
            $query->whereHas('roles', fn ($q) => $q->where('name', $role));
        }

        $users = $query->latest()->paginate(20)->withQueryString();
        $agences = Agence::orderBy('nom')->get(['id', 'nom']);

        return view('super-admin.users.index', compact('users', 'agences'));
    }

    public function toggleStatus(User $user)
    {
        if ($user->hasRole('super_admin')) {
            return back()->with('error', 'Impossible de suspendre un compte Super Admin.');
        }

        $user->update(['is_active' => !$user->is_active]);

        PlatformActivityLog::record(
            $user->is_active ? 'user_activated' : 'user_suspended',
            "Compte utilisateur {$user->email} " . ($user->is_active ? 'activé' : 'suspendu'),
            $user->agence_id
        );

        return back()->with('success', "Le compte de {$user->name} a été " . ($user->is_active ? 'activé' : 'suspendu') . '.');
    }

    public function resetPassword(Request $request, User $user)
    {
        if ($user->hasRole('super_admin')) {
            return back()->with('error', 'Action non autorisée sur un Super Admin.');
        }

        $request->validate([
            'new_password' => ['nullable', 'string', 'min:8'],
        ]);

        $plainPassword = $request->filled('new_password')
            ? $request->input('new_password')
            : Str::random(10);

        $user->update(['password' => Hash::make($plainPassword)]);

        PlatformActivityLog::record(
            'user_password_reset',
            "Mot de passe réinitialisé par le Super Admin pour {$user->email}",
            $user->agence_id
        );

        return back()->with('success', "Nouveau mot de passe pour {$user->email} : {$plainPassword}");
    }

    public function impersonate(Request $request, User $user)
    {
        if ($user->hasRole('super_admin')) {
            return back()->with('error', 'Impossible de basculer sur un autre Super Admin.');
        }

        $superAdminId = Auth::id();

        PlatformActivityLog::record(
            'impersonation_started',
            "Mode Support activé : connexion en tant que {$user->name} ({$user->email})",
            $user->agence_id,
            ['target_user_id' => $user->id],
            $superAdminId
        );

        $request->session()->put('impersonator_id', $superAdminId);
        Auth::login($user);
        $request->session()->regenerate();
        $request->session()->put('impersonator_id', $superAdminId);

        return redirect()->route('dashboard')
            ->with('success', "Mode Support activé : vous naviguez en tant que {$user->name}.");
    }

    public function leaveImpersonation(Request $request)
    {
        $impersonatorId = $request->session()->pull('impersonator_id');

        if (!$impersonatorId) {
            return redirect()->route('dashboard');
        }

        $superAdmin = User::find($impersonatorId);
        if (!$superAdmin || !$superAdmin->hasRole('super_admin')) {
            Auth::logout();
            return redirect()->route('login');
        }

        $targetUser = Auth::user();
        PlatformActivityLog::record(
            'impersonation_ended',
            "Fin du mode Support (utilisateur : " . ($targetUser?->email ?? 'N/A') . ")",
            $targetUser?->agence_id,
            [],
            $superAdmin->id
        );

        Auth::login($superAdmin);
        $request->session()->regenerate();

        return redirect()->route('super-admin.dashboard')
            ->with('success', 'Vous êtes revenu dans votre espace Super Admin.');
    }
}

