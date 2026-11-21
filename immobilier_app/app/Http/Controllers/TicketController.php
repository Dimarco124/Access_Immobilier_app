<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\MessageTicket;
use App\Models\AppNotification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TicketController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Ticket::where('agence_id', $user->agence_id);

        if ($user->hasRole('locataire')) {
            $query->where('locataire_id', $user->locataire->id);
        } elseif ($user->hasRole('proprietaire')) {
            // Pour les propriétaires : tickets liés à leurs biens
            $proprietaire = $user->proprietaire;
            $bienIds = \App\Models\Bien::where('proprietaire_id', $proprietaire->id)->pluck('id');
            $query->whereIn('bien_id', $bienIds);
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('objet', 'like', "%{$search}%")
                  ->orWhere('statut', 'like', "%{$search}%")
                  ->orWhere('priorite', 'like', "%{$search}%")
                  ->orWhereHas('locataire.user', function($qUser) use ($search) {
                      $qUser->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Filtres multi-champs
        if ($request->filled('objet')) {
            $query->where('objet', 'like', '%' . $request->objet . '%');
        }
        if ($request->filled('locataire')) {
            $query->whereHas('locataire.user', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->locataire . '%');
            });
        }
        if ($request->filled('bien')) {
            $query->whereHas('bien', function($q) use ($request) {
                $q->where('nom_bien', 'like', '%' . $request->bien . '%');
            });
        }
        if ($request->filled('priorite')) {
            $query->where('priorite', $request->priorite);
        }
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        // AMÉLIORATION: Pagination au lieu de get()
        $tickets = $query->with(['locataire.user', 'bien.parent'])->latest()->paginate(15)->withQueryString();
        
        return view('tickets.index', compact('tickets'));
    }

    public function printList(Request $request)
    {
        $user = Auth::user();
        $query = Ticket::where('agence_id', $user->agence_id);

        if ($user->hasRole('locataire')) {
            $query->where('locataire_id', $user->locataire->id);
        } elseif ($user->hasRole('proprietaire')) {
            $proprietaire = $user->proprietaire;
            $bienIds = \App\Models\Bien::where('proprietaire_id', $proprietaire->id)->pluck('id');
            $query->whereIn('bien_id', $bienIds);
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('objet', 'like', "%{$search}%")
                  ->orWhere('statut', 'like', "%{$search}%")
                  ->orWhere('priorite', 'like', "%{$search}%")
                  ->orWhereHas('locataire.user', function($qUser) use ($search) {
                      $qUser->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Filtres multi-champs
        if ($request->filled('objet')) {
            $query->where('objet', 'like', '%' . $request->objet . '%');
        }
        if ($request->filled('locataire')) {
            $query->whereHas('locataire.user', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->locataire . '%');
            });
        }
        if ($request->filled('bien')) {
            $query->whereHas('bien', function($q) use ($request) {
                $q->where('nom_bien', 'like', '%' . $request->bien . '%');
            });
        }
        if ($request->filled('priorite')) {
            $query->where('priorite', $request->priorite);
        }
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        $tickets = $query->with(['locataire.user', 'bien.parent'])->latest()->get();
        
        // Calculer les stats
        $stats = [
            'total' => $tickets->count(),
            'ouverts' => $tickets->where('statut', 'Ouvert')->count(),
            'en_cours' => $tickets->where('statut', 'En cours')->count(),
            'resolus' => $tickets->where('statut', 'Résolu')->count(),
        ];

        return view('tickets.print', compact('tickets', 'stats'));
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
            'type_intervention' => 'required|in:Maintenance,Réparation,Plomberie,Électricité',
        ]);

        $user = Auth::user();
        $contratActif = $user->locataire->contrats()->where('statut', 'Actif')->first();

        $ticket = Ticket::create([
            'agence_id' => $user->agence_id,
            'locataire_id' => $user->locataire->id,
            'bien_id' => $contratActif?->bien_id,
            'unite_id' => $contratActif?->unite_id,
            'objet' => $request->objet,
            'description_probleme' => $request->description_probleme,
            'priorite' => $request->priorite,
            'type_intervention' => $request->type_intervention,
            'statut' => 'Nouveau',
        ]);

        // Notifier tous les utilisateurs de l'agence (admin)
        $agenceUsers = User::where('agence_id', $user->agence_id)
            ->whereDoesntHave('roles', function($q) {
                $q->whereIn('name', ['locataire', 'proprietaire']);
            })
            ->get();

        foreach ($agenceUsers as $agenceUser) {
            AppNotification::create([
                'user_id' => $agenceUser->id,
                'agence_id' => $user->agence_id,
                'title' => 'Nouvelle demande d\'intervention',
                'message' => $user->name . ' a envoyé une demande d\'intervention : ' . $ticket->objet,
                'type' => 'ticket',
                'action_url' => route('tickets.show', $ticket),
                'action_text' => 'Voir le ticket',
                'read' => false,
            ]);
        }

        return redirect()->route('tickets.index')->with('success', 'Votre demande d\'intervention a été envoyée avec succès !');
    }

    public function show(Ticket $ticket)
    {
        $user = Auth::user();
        if ($ticket->agence_id !== $user->agence_id) {
            abort(403);
        }

        if ($user->hasRole('locataire') && $ticket->locataire_id !== $user->locataire?->id) {
            abort(403, 'Accès non autorisé à ce ticket.');
        }

        if ($user->hasRole('proprietaire') && $ticket->bien?->proprietaire_id !== $user->proprietaire?->id) {
            abort(403, 'Accès non autorisé à ce ticket.');
        }

        $ticket->load(['messages.user', 'locataire.user', 'bien.parent']);
        return view('tickets.show', compact('ticket'));
    }

    public function addMessage(Request $request, Ticket $ticket)
    {
        $user = Auth::user();
        if ($ticket->agence_id !== $user->agence_id) {
            abort(403);
        }

        if ($user->hasRole('locataire') && $ticket->locataire_id !== $user->locataire?->id) {
            abort(403, 'Accès non autorisé à ce ticket.');
        }

        if ($user->hasRole('proprietaire') && $ticket->bien?->proprietaire_id !== $user->proprietaire?->id) {
            abort(403, 'Accès non autorisé à ce ticket.');
        }

        $request->validate(['contenu_message' => 'required|string']);

        $message = $ticket->messages()->create([
            'user_id' => Auth::id(),
            'contenu_message' => $request->contenu_message,
        ]);

        // Si l'agence répond, on passe le ticket en "En cours"
        if (!Auth::user()->hasRole('locataire')) {
            $ticket->update(['statut' => 'En cours']);
            // Notifier le locataire
            AppNotification::create([
                'user_id' => $ticket->locataire->user_id,
                'agence_id' => $ticket->agence_id,
                'title' => 'Réponse à votre demande',
                'message' => 'L\'agence a répondu à votre demande : ' . $ticket->objet,
                'type' => 'ticket',
                'action_url' => route('tickets.show', $ticket),
                'action_text' => 'Voir la réponse',
                'read' => false,
            ]);
        } else {
            // Notifier l'agence
            $agenceUsers = User::where('agence_id', $ticket->agence_id)
                ->whereDoesntHave('roles', function($q) {
                    $q->whereIn('name', ['locataire', 'proprietaire']);
                })
                ->get();
            foreach ($agenceUsers as $agenceUser) {
                AppNotification::create([
                    'user_id' => $agenceUser->id,
                    'agence_id' => $ticket->agence_id,
                    'title' => 'Nouveau message sur un ticket',
                    'message' => $ticket->locataire->user->name . ' a envoyé un message sur le ticket : ' . $ticket->objet,
                    'type' => 'ticket',
                    'action_url' => route('tickets.show', $ticket),
                    'action_text' => 'Voir le message',
                    'read' => false,
                ]);
            }
        }

        return redirect()->back()->with('success', 'Message envoyé.');
    }

    public function close(Ticket $ticket)
    {
        if ($ticket->agence_id !== Auth::user()->agence_id)
            abort(403);
        $ticket->update(['statut' => 'Résolu']);

        // Notifier le locataire
        AppNotification::create([
            'user_id' => $ticket->locataire->user_id,
            'agence_id' => $ticket->agence_id,
            'title' => 'Demande résolue',
            'message' => 'Votre demande d\'intervention a été marquée comme résolue : ' . $ticket->objet,
            'type' => 'ticket',
            'action_url' => route('tickets.show', $ticket),
            'action_text' => 'Voir le ticket',
            'read' => false,
        ]);

        return redirect()->back()->with('success', 'Ticket marqué comme résolu.');
    }
}
