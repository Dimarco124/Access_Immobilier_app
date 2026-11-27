<?php

namespace App\Http\Controllers;

use App\Models\PlatformActivityLog;
use App\Models\SupportTicket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupportController extends Controller
{
    public function index()
    {
        $tickets = SupportTicket::where('user_id', Auth::id())
            ->orWhere(function ($q) {
                if (Auth::user()->agence_id) {
                    $q->where('agence_id', Auth::user()->agence_id);
                }
            })
            ->latest()
            ->paginate(10);

        return view('support.index', compact('tickets'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string', 'max:4000'],
            'priority' => ['required', 'in:normal,high,urgent'],
        ]);

        $ticket = SupportTicket::create([
            'agence_id' => Auth::user()->agence_id,
            'user_id' => Auth::id(),
            'subject' => $validated['subject'],
            'message' => $validated['message'],
            'priority' => $validated['priority'],
            'status' => 'open',
        ]);

        PlatformActivityLog::record(
            'support_ticket_created',
            "Nouveau ticket support #{$ticket->id} : {$ticket->subject}",
            Auth::user()->agence_id
        );

        return back()->with('success', 'Votre demande a bien été envoyée au support de la plateforme.');
    }
}
