<?php

namespace App\Http\Controllers;

use App\Models\TeamMember;
use App\Models\Module;
use App\Models\ModuleAssignment;
use App\Models\AccessHistory;
use App\Models\AgencyTeamConfig;
use App\Notifications\AccessChangedNotification;
use App\Notifications\TeamPasswordResetNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TeamManagementController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:admin');
    }

    /**
     * Show admin form to reset a team member's password
     */
    public function showResetPassword(\App\Models\TeamMember $teamMember)
    {
        if ($teamMember->agence_id !== Auth::user()->agence_id) abort(403);

        return view('team.reset-password', compact('teamMember'));
    }

    /**
     * Reset a team member's password (admin action)
     */
    public function resetPassword(Request $request, \App\Models\TeamMember $teamMember)
    {
        if ($teamMember->agence_id !== Auth::user()->agence_id) abort(403);

        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Ensure the user exists for this team member
        $user = $teamMember->user;
        if (!$user) {
            return back()->withErrors('Utilisateur lié introuvable.');
        }

        $user->password = bcrypt($request->password);
        $user->save();

        // Log in access history
        AccessHistory::create([
            'team_member_id' => $teamMember->id,
            'changed_by' => Auth::id(),
            'action' => 'password_reset',
            'notes' => 'Mot de passe réinitialisé par l\'administrateur',
        ]);

        // Notify the user (in-app and optionally by email)
        $adminName = Auth::user()->name ?? Auth::user()->nom_complet ?? null;
        if ($teamMember->user) {
            $teamMember->user->notify(new TeamPasswordResetNotification($adminName));
        }

        return redirect()->route('team.index')->with('success', 'Mot de passe réinitialisé avec succès.');
    }

    /**
     * Vue d'ensemble de l'équipe (Matrice)
     */
    public function index()
    {
        $agence = Auth::user()->agence;
        $config = AgencyTeamConfig::firstOrCreate(['agence_id' => $agence->id]);
        
        $teamMembers = TeamMember::where('agence_id', $agence->id)
                                ->with(['moduleAssignments', 'user'])
                                ->get();
        
        $modules = Module::active()->ordered()->get();

        // Créer la matrice
        $matrix = [];
        foreach ($teamMembers as $member) {
            $matrix[$member->id] = [];
            foreach ($modules as $module) {
                $assignment = $member->moduleAssignments
                                    ->where('module_id', $module->id)
                                    ->first();
                $matrix[$member->id][$module->id] = $assignment;
            }
        }

        return view('team.index', compact('teamMembers', 'modules', 'matrix', 'config'));
    }

    /**
     * Ajouter un nouveau membre
     */
    public function create()
    {
        $modules = Module::active()->ordered()->get();
        return view('team.create', compact('modules'));
    }

    /**
     * Enregistrer un nouveau membre
     */
    public function store(Request $request)
    {
        $request->validate([
            'nom_complet' => 'required|string|max:255',
            'email' => 'required|email|unique:team_members,email|unique:users,email',
            'telephone' => 'nullable|string|max:20',
            'password' => 'required|string|min:8|confirmed',
            'modules' => 'required|array|min:1',
            'modules.*' => 'exists:modules,id',
            'access_levels' => 'required|array',
            'access_levels.*' => 'in:manager,collaborator,viewer',
        ]);

        $agence = Auth::user()->agence;

        DB::transaction(function () use ($request, $agence) {
            // Passer automatiquement en mode équipe si en mode solo
            $config = AgencyTeamConfig::firstOrCreate(['agence_id' => $agence->id]);
            if ($config->isSoloMode()) {
                $config->switchToTeamMode();
            }

            // Créer le compte utilisateur directement
            $user = \App\Models\User::create([
                'name' => $request->nom_complet,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'agence_id' => $agence->id,
            ]);

            // Assigner le rôle team_member
            $user->assignRole('team_member');

            $teamMember = TeamMember::create([
                'agence_id' => $agence->id,
                'user_id' => $user->id,
                'nom_complet' => $request->nom_complet,
                'email' => $request->email,
                'telephone' => $request->telephone,
                'is_invited' => false,
                'status' => 'active',
            ]);

            foreach ($request->modules as $moduleId) {
                $accessLevel = $request->input("access_levels.{$moduleId}", 'viewer');
                
                ModuleAssignment::create([
                    'team_member_id' => $teamMember->id,
                    'module_id' => $moduleId,
                    'access_level' => $accessLevel,
                    'assigned_by' => Auth::id(),
                ]);

                $module = Module::find($moduleId);
                AccessHistory::create([
                    'team_member_id' => $teamMember->id,
                    'changed_by' => Auth::id(),
                    'action' => 'assigned',
                    'module_code' => $module->code,
                    'new_level' => $accessLevel,
                ]);
            }
        });

        return redirect()->route('team.index')->with('success', 'Membre ajouté avec succès !');
    }

    /**
     * Modifier un membre
     */
    public function edit(TeamMember $teamMember)
    {
        if ($teamMember->agence_id !== Auth::user()->agence_id) {
            abort(403);
        }

        $modules = Module::active()->ordered()->get();
        $currentAssignments = $teamMember->moduleAssignments()->with('module')->get();

        return view('team.edit', compact('teamMember', 'modules', 'currentAssignments'));
    }

    /**
     * Mettre à jour un membre
     */
    public function update(Request $request, TeamMember $teamMember)
    {
        if ($teamMember->agence_id !== Auth::user()->agence_id) {
            abort(403);
        }

        $rules = [
            'nom_complet' => 'required|string|max:255',
            'email' => 'required|email|unique:team_members,email,'.$teamMember->id.'|unique:users,email,'.$teamMember->user_id,
            'telephone' => 'nullable|string|max:20',
            'modules' => 'required|array|min:1',
            'modules.*' => 'exists:modules,id',
            'access_levels' => 'required|array',
            'access_levels.*' => 'in:manager,collaborator,viewer',
        ];

        // Ne valider le mot de passe que si LES DEUX champs sont remplis.
        // Cela empêche l'auto-remplissage du navigateur sur 'password' d'échouer la validation 'confirmed'.
        if ($request->filled('password') && $request->filled('password_confirmation')) {
            $rules['password'] = 'required|string|min:8|confirmed';
        }

        $request->validate($rules, [
            'nom_complet.required' => 'Le nom complet est obligatoire.',
            'email.required' => 'L\'adresse email est obligatoire.',
            'email.email' => 'L\'adresse email n\'est pas valide.',
            'email.unique' => 'Cette adresse email est déjà utilisée.',
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
            'password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
            'modules.required' => 'Veuillez assigner au moins un module.',
            'modules.min' => 'Veuillez assigner au moins un module.',
        ]);

        DB::transaction(function () use ($request, $teamMember) {
            $teamMember->update([
                'nom_complet' => $request->nom_complet,
                'email' => $request->email,
                'telephone' => $request->telephone,
            ]);

            $user = $teamMember->user;
            if ($user) {
                $user->name = $request->nom_complet;
                $user->email = $request->email;
                if ($request->filled('password') && $request->filled('password_confirmation')) {
                    $user->password = bcrypt($request->password);
                }
                $user->save();
            }

            // Récupérer les anciennes assignations
            $oldAssignments = $teamMember->moduleAssignments()->with('module')->get();

            // Supprimer toutes les anciennes assignations
            $teamMember->moduleAssignments()->delete();

            // Créer les nouvelles assignations
            foreach ($request->modules as $moduleId) {
                $accessLevel = $request->input("access_levels.{$moduleId}", 'viewer');
                
                ModuleAssignment::create([
                    'team_member_id' => $teamMember->id,
                    'module_id' => $moduleId,
                    'access_level' => $accessLevel,
                    'assigned_by' => Auth::id(),
                ]);

                // Logger le changement
                $module = Module::find($moduleId);
                $oldAssignment = $oldAssignments->where('module_id', $moduleId)->first();
                
                AccessHistory::create([
                    'team_member_id' => $teamMember->id,
                    'changed_by' => Auth::id(),
                    'action' => 'modified',
                    'module_code' => $module->code,
                    'old_level' => $oldAssignment?->access_level,
                    'new_level' => $accessLevel,
                ]);
            }

            // Notifier le membre si son compte est actif
            if ($teamMember->isActive()) {
                $teamMember->user->notify(new AccessChangedNotification($teamMember));
            }
        });

        return redirect()->route('team.index')->with('success', 'Membre mis à jour avec succès !');
    }

    /**
     * Suspendre un membre
     */
    public function suspend(TeamMember $teamMember)
    {
        if ($teamMember->agence_id !== Auth::user()->agence_id) {
            abort(403);
        }

        $teamMember->update(['status' => 'suspended']);

        AccessHistory::create([
            'team_member_id' => $teamMember->id,
            'changed_by' => Auth::id(),
            'action' => 'suspended',
            'notes' => 'Accès suspendu par l\'administrateur',
        ]);

        if ($teamMember->isActive() && $teamMember->user) {
            $teamMember->user->notify(new AccessChangedNotification($teamMember));
        }

        return back()->with('success', 'Membre suspendu avec succès.');
    }

    /**
     * Réactiver un membre
     */
    public function reactivate(TeamMember $teamMember)
    {
        if ($teamMember->agence_id !== Auth::user()->agence_id) {
            abort(403);
        }

        $teamMember->update(['status' => 'active']);

        AccessHistory::create([
            'team_member_id' => $teamMember->id,
            'changed_by' => Auth::id(),
            'action' => 'activated',
            'notes' => 'Accès réactivé par l\'administrateur',
        ]);

        if ($teamMember->user) {
            $teamMember->user->notify(new AccessChangedNotification($teamMember));
        }

        return back()->with('success', 'Membre réactivé avec succès.');
    }

    /**
     * Supprimer un membre
     */
    public function destroy(TeamMember $teamMember)
    {
        if ($teamMember->agence_id !== Auth::user()->agence_id) {
            abort(403);
        }

        $teamMember->delete();

        return redirect()->route('team.index')->with('success', 'Membre supprimé avec succès.');
    }


    /**
     * Historique des accès (Audit)
     */
    public function history(TeamMember $teamMember)
    {
        if ($teamMember->agence_id !== Auth::user()->agence_id) {
            abort(403);
        }

        $history = AccessHistory::where('team_member_id', $teamMember->id)
                                ->with('changedBy')
                                ->latest()
                                ->paginate(20);

        return view('team.history', compact('teamMember', 'history'));
    }

    /**
     * Passer en mode équipe (depuis mode solo)
     */
    public function switchToTeamMode()
    {
        $agence = Auth::user()->agence;
        $config = AgencyTeamConfig::firstOrCreate(['agence_id' => $agence->id]);

        $config->switchToTeamMode();

        return redirect()->route('team.index')->with('success', 'Mode Équipe activé avec succès ! Vous pouvez maintenant ajouter et configurer vos collaborateurs.');
    }

    /**
     * Passer en mode solo (depuis mode équipe)
     */
    public function switchToSoloMode()
    {
        $agence = Auth::user()->agence;
        $config = AgencyTeamConfig::firstOrCreate(['agence_id' => $agence->id]);

        $config->switchToSoloMode();

        return redirect()->route('team.index')->with('success', 'Mode Solo activé avec succès.');
    }
}
