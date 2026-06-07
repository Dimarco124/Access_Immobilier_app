<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\MessageTicket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $query = Ticket::where('agence_id', $user->agence_id);

        if ($user->hasRole('locataire')) {
            $query->where('locataire_id', $user->locataire->id);
        }

        $tickets = $query->with(['locataire.user', 'bien'])->latest()->get();
        return view('tickets.index', compact('tickets'));
    }

    public function create()
    {
        return view('tickets.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'objet' => 'required|string|max:255',
            'description_probleme' => 'required|string',
            'priorite' => 'required|in:Faible,Urgent,Critique',
        ]);

        $user = Auth::user();

        Ticket::create([
            'agence_id' => $user->agence_id,
            'locataire_id' => $user->locataire->id,
            'bien_id' => $user->locataire->contrats()->where('statut', 'Actif')->first()?->bien_id,
            'objet' => $request->objet,
            'description_probleme' => $request->description_probleme,
            'priorite' => $request->priorite,
            'statut' => 'Nouveau',
        ]);

        return redirect()->route('tickets.index')->with('success', 'Votre demande a été envoyée.');
    }

    public function show(Ticket $ticket)
    {
        if ($ticket->agence_id !== Auth::user()->agence_id) abort(403);
        $ticket->load(['messages.user', 'locataire.user', 'bien']);
        return view('tickets.show', compact('ticket'));
    }

    public function addMessage(Request $request, Ticket $ticket)
    {
        $request->validate(['contenu_message' => 'required|string']);

        $ticket->messages()->create([
            'user_id' => Auth::id(),
            'contenu_message' => $request->contenu_message,
        ]);

        // Si l'agence répond, on passe le ticket en "En cours"
        if (!Auth::user()->hasRole('locataire')) {
            $ticket->update(['statut' => 'En cours']);
        }

        return redirect()->back()->with('success', 'Message envoyé.');
    }

    public function close(Ticket $ticket)
    {
        if ($ticket->agence_id !== Auth::user()->agence_id) abort(403);
        $ticket->update(['statut' => 'Résolu']);
        return redirect()->back()->with('success', 'Ticket marqué comme résolu.');
    }
}
