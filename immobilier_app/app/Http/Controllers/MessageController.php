<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MessageController extends Controller
{
    // Liste des messages (boîte de réception)
    public function index()
    {
        $user = Auth::user();
        
        // Récupérer tous les utilisateurs avec qui l'utilisateur a échangé
        $conversations = Message::where('agence_id', $user->agence_id)
            ->where(function($query) use ($user) {
                $query->where('expediteur_id', $user->id)
                      ->orWhere('destinataire_id', $user->id);
            })
            ->with(['expediteur', 'destinataire'])
            ->latest()
            ->get()
            ->groupBy(function($message) use ($user) {
                // Grouper par l'autre personne dans la conversation
                return $message->expediteur_id === $user->id 
                    ? $message->destinataire_id 
                    : $message->expediteur_id;
            })
            ->map(function($messages) {
                return $messages->first(); // Prendre le dernier message de chaque conversation
            });

        // Compter les messages non lus
        $messagesNonLus = Message::recusPar($user->id)->nonLus()->count();

        return view('messages.index', compact('conversations', 'messagesNonLus'));
    }

    // Voir une conversation avec un utilisateur
    public function show($userId)
    {
        $user = Auth::user();
        $destinataire = User::findOrFail($userId);

        // Vérifier que l'autre utilisateur est dans la même agence
        if ($destinataire->agence_id !== $user->agence_id) {
            abort(403, 'Non autorisé');
        }

        // Récupérer tous les messages de la conversation
        $messages = Message::conversation($user->id, $userId)
            ->where('agence_id', $user->agence_id)
            ->with(['expediteur', 'destinataire'])
            ->orderBy('created_at', 'asc')
            ->get();

        // Marquer comme lus tous les messages reçus
        Message::recusPar($user->id)
            ->where('expediteur_id', $userId)
            ->nonLus()
            ->get()
            ->each(function($message) {
                $message->marquerCommeLu();
            });

        return view('messages.show', compact('messages', 'destinataire'));
    }

    // Formulaire pour nouveau message
    public function create()
    {
        $user = Auth::user();
        
        // Liste des utilisateurs selon le rôle
        if ($user->hasRole('admin')) {
            // L'agence peut envoyer à tous (propriétaires et locataires)
            $utilisateurs = User::where('agence_id', $user->agence_id)
                ->where('id', '!=', $user->id)
                ->whereHas('roles', function($query) {
                    $query->whereIn('name', ['proprietaire', 'locataire']);
                })
                ->orderBy('name')
                ->get();
        } else {
            // Propriétaires et locataires ne peuvent envoyer qu'à l'agence
            $utilisateurs = User::where('agence_id', $user->agence_id)
                ->whereHas('roles', function($query) {
                    $query->where('name', 'admin');
                })
                ->orderBy('name')
                ->get();
        }

        return view('messages.create', compact('utilisateurs'));
    }

    // Envoyer un nouveau message
    public function store(Request $request)
    {
        $validated = $request->validate([
            'destinataire_id' => 'required|exists:users,id',
            'objet' => 'required|string|max:255',
            'contenu' => 'required|string',
            'piece_jointe' => 'nullable|file|mimes:pdf,jpg,jpeg,png,webp,doc,docx,xls,xlsx|max:10240', // 10MB max
        ]);

        $user = Auth::user();

        // Vérifier que le destinataire est dans la même agence
        $destinataire = User::findOrFail($validated['destinataire_id']);
        if ($destinataire->agence_id !== $user->agence_id) {
            return back()->withErrors(['destinataire_id' => 'Destinataire non valide']);
        }

        // Vérifier les règles de communication
        if ($user->hasRole('proprietaire') || $user->hasRole('locataire')) {
            // Propriétaires et locataires ne peuvent envoyer qu'à l'agence
            if (!$destinataire->hasRole('admin')) {
                return back()->withErrors(['destinataire_id' => 'Vous ne pouvez envoyer des messages qu\'à l\'agence']);
            }
        }

        $data = [
            'agence_id' => $user->agence_id,
            'expediteur_id' => $user->id,
            'destinataire_id' => $validated['destinataire_id'],
            'objet' => $validated['objet'],
            'contenu' => $validated['contenu'],
        ];

        // Gérer la pièce jointe
        if ($request->hasFile('piece_jointe')) {
            $path = $request->file('piece_jointe')->store('messages', 'public');
            $data['piece_jointe_url'] = $path;
        }

        Message::create($data);

        return redirect()->route('messages.show', $validated['destinataire_id'])
            ->with('success', 'Message envoyé avec succès');
    }

    // Répondre à un message (dans une conversation existante)
    public function reply(Request $request, $userId)
    {
        $validated = $request->validate([
            'contenu' => 'required|string',
            'piece_jointe' => 'nullable|file|mimes:pdf,jpg,jpeg,png,webp,doc,docx,xls,xlsx|max:10240',
        ]);

        $user = Auth::user();
        $destinataire = User::findOrFail($userId);

        // Vérifier que le destinataire est dans la même agence
        if ($destinataire->agence_id !== $user->agence_id) {
            abort(403, 'Non autorisé');
        }

        // Vérifier les règles de communication
        if ($user->hasRole('proprietaire') || $user->hasRole('locataire')) {
            // Propriétaires et locataires ne peuvent répondre qu'à l'agence
            if (!$destinataire->hasRole('admin')) {
                abort(403, 'Vous ne pouvez communiquer qu\'avec l\'agence');
            }
        }

        // Récupérer le dernier message de la conversation pour l'objet
        $dernierMessage = Message::conversation($user->id, $userId)
            ->latest()
            ->first();

        $objet = $dernierMessage ? 'RE: ' . $dernierMessage->objet : 'Nouveau message';

        $data = [
            'agence_id' => $user->agence_id,
            'expediteur_id' => $user->id,
            'destinataire_id' => $userId,
            'objet' => $objet,
            'contenu' => $validated['contenu'],
        ];

        if ($request->hasFile('piece_jointe')) {
            $path = $request->file('piece_jointe')->store('messages', 'public');
            $data['piece_jointe_url'] = $path;
        }

        Message::create($data);

        return back()->with('success', 'Réponse envoyée');
    }

    // Supprimer un message
    public function destroy($id)
    {
        $message = Message::findOrFail($id);
        $user = Auth::user();

        // Vérifier que l'utilisateur est l'expéditeur ou le destinataire
        if ($message->expediteur_id !== $user->id && $message->destinataire_id !== $user->id) {
            abort(403, 'Non autorisé');
        }

        // Supprimer la pièce jointe si elle existe
        if ($message->piece_jointe_url) {
            Storage::disk('public')->delete($message->piece_jointe_url);
        }

        $message->delete();

        return back()->with('success', 'Message supprimé');
    }

    // Marquer comme lu
    public function markAsRead($id)
    {
        $message = Message::findOrFail($id);
        $user = Auth::user();

        if ($message->destinataire_id !== $user->id) {
            abort(403, 'Non autorisé');
        }

        $message->marquerCommeLu();

        return response()->json(['success' => true]);
    }
}
