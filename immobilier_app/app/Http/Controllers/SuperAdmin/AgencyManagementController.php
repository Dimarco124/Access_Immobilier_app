<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Agence;
use App\Models\AgencyTeamConfig;
use App\Models\PlatformActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AgencyManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = Agence::withCount(['users', 'biens', 'contrats', 'locataires', 'proprietaires'])
            ->withSum('paiements', 'montant_verse');

        if ($search = trim((string) $request->input('search'))) {
            $query->where(function ($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('telephone', 'like', "%{$search}%");
            });
        }

        if ($statut = $request->input('statut')) {
            if (in_array($statut, ['actif', 'suspendu'], true)) {
                $query->where('statut', $statut);
            }
        }

        $agences = $query->latest()->paginate(15)->withQueryString();

        return view('super-admin.agencies.index', compact('agences'));
    }

    public function create()
    {
        return view('super-admin.agencies.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:agences,email'],
            'telephone' => ['required', 'string', 'max:50'],
            'adresse' => ['required', 'string', 'max:255'],
            'devise' => ['required', 'string', 'max:10'],
            'taux_tva' => ['required', 'numeric', 'min:0', 'max:100'],
            'admin_name' => ['required', 'string', 'max:255'],
            'admin_email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'admin_telephone' => ['nullable', 'string', 'max:50'],
            'admin_password' => ['required', 'string', 'min:8'],
        ]);

        $agence = DB::transaction(function () use ($validated) {
            $agence = Agence::create([
                'nom' => $validated['nom'],
                'email' => $validated['email'],
                'telephone' => $validated['telephone'],
                'adresse' => $validated['adresse'],
                'devise' => strtoupper($validated['devise']),
                'taux_tva' => $validated['taux_tva'],
                'statut' => 'actif',
            ]);

            $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);

            $admin = User::create([
                'agence_id' => $agence->id,
                'name' => $validated['admin_name'],
                'email' => $validated['admin_email'],
                'telephone' => $validated['admin_telephone'] ?? $validated['telephone'],
                'password' => Hash::make($validated['admin_password']),
                'is_active' => true,
                'email_verified_at' => now(),
            ]);
            $admin->assignRole($adminRole);

            AgencyTeamConfig::firstOrCreate(
                ['agence_id' => $agence->id],
                ['mode' => 'solo', 'setup_completed' => true]
            );

            return $agence;
        });

        PlatformActivityLog::record(
            'agency_created',
            "Agence créée manuellement par le Super Admin : {$agence->nom}",
            $agence->id
        );

        return redirect()->route('super-admin.agencies.show', $agence)
            ->with('success', "L'agence « {$agence->nom} » et son administrateur ont été créés avec succès.");
    }

    public function show(Agence $agence)
    {
        $agence->loadCount(['users', 'biens', 'contrats', 'locataires', 'proprietaires', 'tickets']);
        $agence->loadSum('paiements', 'montant_verse');

        $users = $agence->users()->with('roles')->latest()->get();
        $recentBiens = $agence->biens()->with('proprietaire.user')->latest()->take(6)->get();
        $recentContrats = $agence->contrats()->with(['locataire.user', 'bien'])->latest()->take(6)->get();

        return view('super-admin.agencies.show', compact('agence', 'users', 'recentBiens', 'recentContrats'));
    }

    public function edit(Agence $agence)
    {
        return view('super-admin.agencies.edit', compact('agence'));
    }

    public function update(Request $request, Agence $agence)
    {
        $validated = $request->validate([
            'nom' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:agences,email,' . $agence->id],
            'telephone' => ['required', 'string', 'max:50'],
            'adresse' => ['required', 'string', 'max:255'],
            'devise' => ['required', 'string', 'max:10'],
            'taux_tva' => ['required', 'numeric', 'min:0', 'max:100'],
            'statut' => ['required', 'in:actif,suspendu'],
        ]);

        $agence->update($validated);

        PlatformActivityLog::record(
            'agency_updated',
            "Informations mises à jour pour l'agence : {$agence->nom}",
            $agence->id
        );

        return redirect()->route('super-admin.agencies.show', $agence)
            ->with('success', "Les informations de l'agence ont été mises à jour.");
    }

    public function toggleStatus(Agence $agence)
    {
        $newStatus = $agence->statut === 'actif' ? 'suspendu' : 'actif';
        $agence->update(['statut' => $newStatus]);

        PlatformActivityLog::record(
            $newStatus === 'suspendu' ? 'agency_suspended' : 'agency_activated',
            "Statut de l'agence « {$agence->nom} » passé à : {$newStatus}",
            $agence->id
        );

        $label = $newStatus === 'actif' ? 'activée' : 'suspendue';
        return back()->with('success', "L'agence « {$agence->nom} » a été {$label}.");
    }

    public function destroy(Agence $agence)
    {
        $nom = $agence->nom;
        $id = $agence->id;

        $agence->delete();

        PlatformActivityLog::record(
            'agency_deleted',
            "Agence supprimée définitivement par le Super Admin : {$nom} (ID #{$id})"
        );

        return redirect()->route('super-admin.agencies.index')
            ->with('success', "L'agence « {$nom} » et ses données ont été supprimées.");
    }

    public function exportCsv()
    {
        $agences = Agence::withCount(['users', 'biens', 'contrats'])
            ->withSum('paiements', 'montant_verse')
            ->orderBy('nom')
            ->get();

        $filename = 'agences_' . now()->format('Y-m-d_His') . '.csv';

        return response()->streamDownload(function () use ($agences) {
            $handle = fopen('php://output', 'w');
            // BOM UTF-8 pour Excel
            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, ['ID', 'Nom', 'Email', 'Téléphone', 'Adresse', 'Devise', 'Statut', 'Utilisateurs', 'Biens', 'Contrats', 'Volume encaissé', 'Date inscription'], ';');

            foreach ($agences as $a) {
                fputcsv($handle, [
                    $a->id,
                    $a->nom,
                    $a->email,
                    $a->telephone,
                    $a->adresse,
                    $a->devise,
                    $a->statut,
                    $a->users_count,
                    $a->biens_count,
                    $a->contrats_count,
                    $a->paiements_sum_montant_verse ?? 0,
                    $a->created_at?->format('d/m/Y H:i'),
                ], ';');
            }
            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
