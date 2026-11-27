<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Agence;
use App\Models\Bien;
use App\Models\Contrat;
use App\Models\Locataire;
use App\Models\Paiement;
use App\Models\PlatformActivityLog;
use App\Models\PlatformAnnouncement;
use App\Models\PlatformSetting;
use App\Models\Proprietaire;
use App\Models\SupportTicket;
use App\Models\User;
use Carbon\Carbon;

class SuperAdminDashboardController extends Controller
{
    public function index()
    {
        $now = Carbon::now();

        $stats = [
            'total_agences' => Agence::count(),
            'agences_actives' => Agence::where('statut', 'actif')->count(),
            'agences_suspendues' => Agence::where('statut', 'suspendu')->count(),
            'nouvelles_agences_mois' => Agence::whereYear('created_at', $now->year)->whereMonth('created_at', $now->month)->count(),
            'total_users' => User::whereDoesntHave('roles', fn ($q) => $q->where('name', 'super_admin'))->count(),
            'users_suspendus' => User::where('is_active', false)->count(),
            'total_biens' => Bien::count(),
            'total_contrats_actifs' => Contrat::where('statut', 'Actif')->count(),
            'total_locataires' => Locataire::count(),
            'total_proprietaires' => Proprietaire::count(),
            'volume_mois' => (float) Paiement::whereYear('date_paiement', $now->year)->whereMonth('date_paiement', $now->month)->sum('montant_verse'),
            'volume_total' => (float) Paiement::sum('montant_verse'),
            'tickets_ouverts' => SupportTicket::whereIn('status', ['open', 'in_progress'])->count(),
            'tickets_urgents' => SupportTicket::whereIn('status', ['open', 'in_progress'])->where('priority', 'urgent')->count(),
            'annonces_actives' => PlatformAnnouncement::visible()->count(),
            'maintenance_mode' => PlatformSetting::getValue('maintenance_mode') === '1',
        ];

        // Graphique 12 mois : nouvelles agences + volume encaissé
        $moisLabels = [];
        $agencesParMois = [];
        $volumeParMois = [];

        for ($i = 11; $i >= 0; $i--) {
            $d = $now->copy()->subMonths($i);
            $moisLabels[] = ucfirst($d->copy()->locale('fr')->translatedFormat('M Y'));
            $agencesParMois[] = Agence::whereYear('created_at', $d->year)->whereMonth('created_at', $d->month)->count();
            $volumeParMois[] = (float) Paiement::whereYear('date_paiement', $d->year)->whereMonth('date_paiement', $d->month)->sum('montant_verse');
        }

        // Top agences (par nombre de biens et volume)
        $topAgences = Agence::withCount(['biens', 'contrats', 'users'])
            ->withSum('paiements', 'montant_verse')
            ->orderByDesc('biens_count')
            ->take(6)
            ->get();

        // Dernières agences inscrites
        $recentAgences = Agence::withCount(['users', 'biens'])
            ->latest()
            ->take(5)
            ->get();

        // Alertes plateforme
        $agencesSuspendues = Agence::where('statut', 'suspendu')->latest()->take(5)->get();
        $ticketsRecents = SupportTicket::with(['agence', 'user'])
            ->whereIn('status', ['open', 'in_progress'])
            ->latest()
            ->take(5)
            ->get();

        $recentLogs = PlatformActivityLog::with(['user', 'agence'])
            ->latest()
            ->take(8)
            ->get();

        return view('super-admin.dashboard', compact(
            'stats',
            'moisLabels',
            'agencesParMois',
            'volumeParMois',
            'topAgences',
            'recentAgences',
            'agencesSuspendues',
            'ticketsRecents',
            'recentLogs'
        ));
    }
}
