<?php

namespace App\Http\Controllers;

use App\Models\AgencyTeamConfig;
use App\Models\Module;
use App\Models\TeamMember;
use App\Models\ModuleAssignment;
use App\Models\AccessHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TeamSetupController extends Controller
{
    /**
     * Étape 1 : Choix du mode (Solo ou Équipe)
     */
    public function chooseMode()
    {
        $agence = Auth::user()->agence;
        
        // Vérifier si déjà configuré
        $config = AgencyTeamConfig::firstOrCreate(
            ['agence_id' => $agence->id],
            ['mode' => 'solo', 'onboarding_completed' => false]
        );

        if ($config->onboarding_completed) {
            return redirect()->route('dashboard');
        }

        return view('team-setup.choose-mode', compact('config'));
    }

    /**
     * Enregistrer le choix du mode
     */
    public function storeMode(Request $request)
    {
        $request->validate([
            'mode' => 'required|in:solo,team',
        ]);

        $agence = Auth::user()->agence;
        $config = AgencyTeamConfig::firstOrCreate(['agence_id' => $agence->id]);
        
        $config->mode = $request->mode;
        $config->save();

        if ($request->mode === 'solo') {
            // Mode solo : terminer l'onboarding
            $config->completeOnboarding();
            return redirect()->route('dashboard')->with('success', 'Configuration terminée ! Bienvenue sur votre tableau de bord.');
        }

        // Mode équipe : continuer vers la sélection des modules
        return redirect()->route('team-setup.select-modules');
    }

    /**
     * Étape 2 : Sélection des modules à déléguer
     */
    public function selectModules()
    {
        $agence = Auth::user()->agence;
        $config = AgencyTeamConfig::where('agence_id', $agence->id)->first();

        if ($config && $config->onboarding_completed) {
            return redirect()->route('team.index');
        }

        if (!$config || $config->mode !== 'team') {
            return redirect()->route('team-setup.choose-mode');
        }

        $modules = Module::active()->ordered()->get();

        return view('team-setup.select-modules', compact('modules', 'config'));
    }

    /**
     * Étape 3 : Attribution des modules (formulaire dynamique)
     */
    public function assignModules(Request $request)
    {
        $request->validate([
            'selected_modules' => 'required|array|min:1',
            'selected_modules.*' => 'exists:modules,id',
        ]);

        $agence = Auth::user()->agence;
        $selectedModules = Module::whereIn('id', $request->selected_modules)->orderBy('ordre')->get();

        // Stocker les modules sélectionnés en session pour l'étape suivante
        $request->session()->put('selected_modules', $request->selected_modules);

        return view('team-setup.assign-modules', compact('selectedModules'));
    }

    /**
     * Étape 4 : Traiter l'assignation et afficher le récapitulatif
     */
    public function review(Request $request)
    {
        $agence = Auth::user()->agence;
        $moduleIds = $request->session()->get('selected_modules');

        if (!$moduleIds) {
            return redirect()->route('team-setup.select-modules')
                           ->with('error', 'Veuillez d\'abord sélectionner les modules.');
        }

        // Validation : chaque module doit être assigné + champs des assignations (y compris mot de passe)
        $request->validate([
            'modules' => 'required|array',
            'modules.*.nom_complet' => 'required|string|max:255',
            'modules.*.email' => 'required|email',
            'modules.*.telephone' => 'nullable|string|max:20',
            'modules.*.access_level' => 'required|in:manager,collaborator,viewer',
            'modules.*.password' => 'required|string|min:8',
        ]);

        $modulesData = $request->input('modules', []);

        // Construire la liste des membres uniques avec leurs modules
        $membersData = [];
        $usedModules = [];

        foreach ($modulesData as $moduleId => $data) {
            // Validation : un module ne peut être assigné qu'une seule fois
            if (in_array($moduleId, $usedModules)) {
                return back()->withErrors(['modules' => 'Un module ne peut pas être assigné plusieurs fois.'])->withInput();
            }
            $usedModules[] = $moduleId;

            $module = Module::find($data['module_id']);
            
            if (!empty($data['existing_member_id'])) {
                // Utiliser un membre existant
                $memberKey = 'existing_' . $data['existing_member_id'];
                
                if (!isset($membersData[$memberKey])) {
                    return back()->withErrors(['modules' => 'Membre existant invalide.'])->withInput();
                }
                
                $membersData[$memberKey]['modules'][] = $moduleId;
                $membersData[$memberKey]['access_levels'][] = $data['access_level'];
                $membersData[$memberKey]['modules_details'][] = $module;
            } else {
                // Nouveau membre
                $email = $data['email'] ?? null;
                $nomComplet = $data['nom_complet'] ?? null;

                if (!$email || !$nomComplet) {
                    return back()->withErrors(['modules' => 'Chaque assignation doit avoir un nom et un email.'])->withInput();
                }
                
                // Vérifier si ce membre existe déjà dans la liste (par email)
                $existingKey = null;
                foreach ($membersData as $key => $member) {
                    if ($member['email'] === $email) {
                        $existingKey = $key;
                        break;
                    }
                }

                if ($existingKey) {
                    // Ajouter le module au membre existant
                    $membersData[$existingKey]['modules'][] = $moduleId;
                    $membersData[$existingKey]['access_levels'][] = $data['access_level'];
                    $membersData[$existingKey]['modules_details'][] = $module;
                    // Keep password if not set on existing aggregate
                    if (empty($membersData[$existingKey]['password']) && !empty($data['password'])) {
                        $membersData[$existingKey]['password'] = $data['password'];
                    }
                } else {
                    // Créer un nouveau membre
                    $memberKey = 'new_' . uniqid();
                    $membersData[$memberKey] = [
                        'nom_complet' => $nomComplet,
                        'email' => $email,
                        'telephone' => $data['telephone'] ?? null,
                        'password' => $data['password'] ?? null,
                        'modules' => [$moduleId],
                        'access_levels' => [$data['access_level']],
                        'modules_details' => [$module],
                    ];
                }
            }
        }

        // Validation : tous les modules doivent être assignés
        if (count($usedModules) !== count($moduleIds)) {
            return back()->withErrors(['modules' => 'Tous les modules doivent être assignés.'])->withInput();
        }

        // Validation email unique
        $emails = array_column($membersData, 'email');
        if (count($emails) !== count(array_unique($emails))) {
            return back()->withErrors(['modules' => 'Chaque email doit être unique.'])->withInput();
        }

        // Sauvegarder en session
        $request->session()->put('members_data', array_values($membersData));

        return view('team-setup.review', [
            'membersData' => array_values($membersData)
        ]);
    }

    /**
     * Étape 5 : Validation finale et envoi des invitations
     */
    public function finalize(Request $request)
    {
        $membersData = $request->session()->get('members_data');

        if (!$membersData) {
            return redirect()->route('team-setup.select-modules')
                           ->with('error', 'Session expirée. Veuillez recommencer.');
        }

        $agence = Auth::user()->agence;

        DB::transaction(function () use ($membersData, $agence) {
            foreach ($membersData as $memberData) {
                // Créer le compte utilisateur directement (mot de passe fourni par l'admin)
                $user = \App\Models\User::create([
                    'name' => $memberData['nom_complet'],
                    'email' => $memberData['email'],
                    'password' => bcrypt($memberData['password'] ?? str()->random(12)),
                    'agence_id' => $agence->id,
                ]);

                // Assigner le rôle team_member
                $user->assignRole('team_member');

                // Créer le membre d'équipe lié et le marquer actif
                $teamMember = TeamMember::create([
                    'agence_id' => $agence->id,
                    'user_id' => $user->id,
                    'nom_complet' => $memberData['nom_complet'],
                    'email' => $memberData['email'],
                    'telephone' => $memberData['telephone'] ?? null,
                    'is_invited' => false,
                    'status' => 'active',
                ]);

                // Assigner les modules
                foreach ($memberData['modules'] as $index => $moduleId) {
                    $accessLevel = $memberData['access_levels'][$index] ?? 'viewer';

                    ModuleAssignment::create([
                        'team_member_id' => $teamMember->id,
                        'module_id' => $moduleId,
                        'access_level' => $accessLevel,
                        'assigned_by' => Auth::id(),
                    ]);

                    // Enregistrer dans l'historique
                    $module = Module::find($moduleId);
                    AccessHistory::create([
                        'team_member_id' => $teamMember->id,
                        'changed_by' => Auth::id(),
                        'action' => 'assigned',
                        'module_code' => $module->code,
                        'new_level' => $accessLevel,
                        'notes' => 'Attribution initiale lors de la configuration de l\'équipe',
                    ]);
                }
            }

            // Marquer l'onboarding comme terminé
            $config = AgencyTeamConfig::where('agence_id', $agence->id)->first();
            $config->completeOnboarding();
            $config->team_setup_at = now();
            $config->save();
        });

        // Nettoyer la session
        $request->session()->forget(['selected_modules', 'members_data']);

        return redirect()->route('dashboard')->with('success', 'Votre équipe a été configurée avec succès.');
    }

}
