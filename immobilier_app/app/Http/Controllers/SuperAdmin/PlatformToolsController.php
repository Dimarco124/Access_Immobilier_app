<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Agence;
use App\Models\PlatformActivityLog;
use App\Models\PlatformAnnouncement;
use App\Models\PlatformSetting;
use App\Models\SupportTicket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PlatformToolsController extends Controller
{
    // === ANNONCES GLOBALES ===
    public function announcements()
    {
        $announcements = PlatformAnnouncement::with('creator')->latest()->paginate(15);
        return view('super-admin.announcements.index', compact('announcements'));
    }

    public function storeAnnouncement(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:2000'],
            'type' => ['required', 'in:info,success,warning,danger'],
            'ends_at' => ['nullable', 'date'],
        ]);

        $announcement = PlatformAnnouncement::create([
            'title' => $validated['title'],
            'message' => $validated['message'],
            'type' => $validated['type'],
            'is_active' => true,
            'starts_at' => now(),
            'ends_at' => $validated['ends_at'] ?? null,
            'created_by' => Auth::id(),
        ]);

        PlatformActivityLog::record('announcement_created', "Annonce globale publiée : {$announcement->title}");

        return back()->with('success', 'Annonce globale publiée sur les tableaux de bord des agences.');
    }

    public function toggleAnnouncement(PlatformAnnouncement $announcement)
    {
        $announcement->update(['is_active' => !$announcement->is_active]);
        return back()->with('success', 'Statut de l\'annonce mis à jour.');
    }

    public function destroyAnnouncement(PlatformAnnouncement $announcement)
    {
        $announcement->delete();
        return back()->with('success', 'Annonce supprimée.');
    }

    // === TICKETS SUPPORT ===
    public function tickets(Request $request)
    {
        $query = SupportTicket::with(['agence', 'user', 'responder']);

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $tickets = $query->latest()->paginate(15)->withQueryString();
        return view('super-admin.tickets.index', compact('tickets'));
    }

    public function replyTicket(Request $request, SupportTicket $ticket)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:open,in_progress,resolved,closed'],
            'admin_reply' => ['nullable', 'string', 'max:5000'],
        ]);

        $ticket->update([
            'status' => $validated['status'],
            'admin_reply' => $validated['admin_reply'] ?? $ticket->admin_reply,
            'replied_at' => filled($validated['admin_reply'] ?? null) ? now() : $ticket->replied_at,
            'replied_by' => Auth::id(),
        ]);

        PlatformActivityLog::record(
            'support_ticket_updated',
            "Ticket support #{$ticket->id} mis à jour ({$validated['status']})",
            $ticket->agence_id
        );

        return back()->with('success', "Ticket #{$ticket->id} mis à jour.");
    }

    // === JOURNAL D'ACTIVITÉ (LOGS) ===
    public function logs(Request $request)
    {
        $query = PlatformActivityLog::with(['user', 'agence']);

        if ($action = trim((string) $request->input('action'))) {
            $query->where('action', 'like', "%{$action}%");
        }

        if ($agenceId = $request->input('agence_id')) {
            $query->where('agence_id', $agenceId);
        }

        $logs = $query->latest()->paginate(25)->withQueryString();
        $agences = Agence::orderBy('nom')->get(['id', 'nom']);

        return view('super-admin.logs.index', compact('logs', 'agences'));
    }

    // === MAINTENANCE & PARAMÈTRES ===
    public function settings()
    {
        $maintenanceMode = PlatformSetting::getValue('maintenance_mode', '0') === '1';
        $maintenanceMessage = PlatformSetting::getValue(
            'maintenance_message',
            'La plateforme est actuellement en maintenance pour amélioration. Nous serons de retour très rapidement.'
        );

        return view('super-admin.settings.index', compact('maintenanceMode', 'maintenanceMessage'));
    }

    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'maintenance_mode' => ['nullable', 'boolean'],
            'maintenance_message' => ['required', 'string', 'max:1000'],
        ]);

        $enabled = $request->boolean('maintenance_mode') ? '1' : '0';
        PlatformSetting::setValue('maintenance_mode', $enabled);
        PlatformSetting::setValue('maintenance_message', $validated['maintenance_message']);

        PlatformActivityLog::record(
            'maintenance_updated',
            $enabled === '1' ? 'Mode maintenance activé sur la plateforme' : 'Mode maintenance désactivé'
        );

        return back()->with('success', $enabled === '1' ? 'Mode maintenance activé.' : 'Mode maintenance désactivé.');
    }
}
