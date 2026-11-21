<?php

namespace App\Http\Controllers;

use App\Models\Visite;
use App\Models\Bien;
use App\Models\Locataire;
use App\Models\Proprietaire;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VisiteController extends Controller
{
    /**
     * Afficher tous les visites avec filtres
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $agenceId = $user->agence_id;
        $search = $request->input('search');

        if ($user->hasRole('admin') || $user->hasRole('team_member')) {
            // Vue complète de toutes les visites
            $query = Visite::where('agence_id', $agenceId)
                ->with(['bien', 'locataire.user'])
                ->orderBy('date_heure_visite', 'desc');
        } elseif ($user->hasRole('proprietaire')) {
            // Visites des biens du propriétaire
            $proprietaire = $user->proprietaire;
            $query = Visite::where('agence_id', $agenceId)
                ->whereIn('bien_id', $proprietaire->biens()->pluck('id'))
                ->with(['bien', 'locataire.user'])
                ->orderBy('date_heure_visite', 'desc');
        } elseif ($user->hasRole('locataire')) {
            // Visites du locataire uniquement
            $locataire = $user->locataire;
            $query = Visite::where('agence_id', $agenceId)
                ->where('locataire_id', $locataire->id)
                ->with(['bien', 'locataire.user'])
                ->orderBy('date_heure_visite', 'desc');
        } else {
            abort(403, 'Accès non autorisé');
        }

        if ($request->filled('search')) {
            $query->where(function($q) use ($search) {
                $q->where('statut', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%")
                  ->orWhereHas('bien', function($qBien) use ($search) {
                      $qBien->where('nom_bien', 'like', "%{$search}%");
                  })
                  ->orWhereHas('locataire.user', function($qUser) use ($search) {
                      $qUser->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Filtres multi-champs
        if ($request->filled('bien')) {
            $query->whereHas('bien', function($q) use ($request) {
                $q->where('nom_bien', 'like', '%' . $request->bien . '%');
            });
        }
        if ($request->filled('locataire')) {
            $query->whereHas('locataire.user', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->locataire . '%');
            });
        }
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        $visites = $query->paginate(15)->appends($request->all());
        
        return view('visites.index', compact('visites'));
    }

    /**
     * Afficher le formulaire de création
     */
    public function create()
    {
        $user = Auth::user();

        $locataires = Locataire::where('agence_id', Auth::user()->agence_id)
            ->with('user')
            ->get();
        
        $proprietaires = Proprietaire::where('agence_id', Auth::user()->agence_id)
            ->with(['user', 'biens' => function($query) {
                $query->whereNull('parent_id')->with('children');
            }])
            ->get();
        
        return view('visites.create', compact('locataires', 'proprietaires'));
    }

    /**
     * Enregistrer une nouvelle visite pour un bien
     */
    public function store(Request $request)
    {
        $request->validate([
            'bien_id' => 'required|exists:biens,id',
            'unite_id' => 'nullable|exists:unites,id',
            'locataire_id' => 'required|exists:locataires,id',
            'date_heure_visite' => 'required|date_format:Y-m-d\TH:i',
            'montant_paye' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $bien = Bien::find($request->bien_id);
        $locataire = Locataire::find($request->locataire_id);

        // Vérifier que le bien et le locataire appartiennent à l'agence
        if ($bien->agence_id !== Auth::user()->agence_id || $locataire->agence_id !== Auth::user()->agence_id) {
            abort(403);
        }

        $visite = Visite::create([
            'agence_id' => Auth::user()->agence_id,
            'bien_id' => $request->bien_id,
            'unite_id' => $request->unite_id,
            'locataire_id' => $request->locataire_id,
            'date_heure_visite' => $request->date_heure_visite,
            'montant_paye' => $request->montant_paye,
            'notes' => $request->notes,
            'statut' => 'Programmé',
        ]);

        return redirect()->route('visites.recu', $visite)->with('success', 'Visite enregistrée et paiement encaissé.');
    }

    /**
     * Marquer une visite comme effectuée
     */
    public function markAsCompleted(Visite $visite)
    {
        if ($visite->agence_id !== Auth::user()->agence_id) {
            abort(403);
        }

        $visite->update(['statut' => 'Effectué']);

        return back()->with('success', 'Visite marquée comme effectuée.');
    }

    /**
     * Annuler une visite (et rembourser)
     */
    public function cancel(Visite $visite)
    {
        if ($visite->agence_id !== Auth::user()->agence_id) {
            abort(403);
        }

        // Remettre le montant payé à 0 pour annuler la transaction financière
        $visite->update([
            'statut' => 'Annulé',
            'montant_paye' => 0
        ]);

        return back()->with('success', 'Visite annulée et montant retiré des encaissements.');
    }

    /**
     * Générer le reçu de la visite
     */
    public function generateReceipt(Visite $visite)
    {
        $user = Auth::user();
        if ($visite->agence_id !== $user->agence_id) {
            abort(403);
        }

        if ($user->hasRole('locataire') && $visite->locataire_id !== $user->locataire?->id) {
            abort(403, 'Accès non autorisé à ce reçu.');
        }

        if ($user->hasRole('proprietaire') && $visite->bien?->proprietaire_id !== $user->proprietaire?->id) {
            abort(403, 'Accès non autorisé à ce reçu.');
        }

        // Charger les relations nécessaires
        $visite->load(['agence', 'locataire.user', 'bien', 'unite']);

        return view('visites.recu', compact('visite'));
    }

    /**
     * Supprimer une visite
     */
    public function destroy(Visite $visite)
    {
        if ($visite->agence_id !== Auth::user()->agence_id) {
            abort(403);
        }

        $visite->delete();

        return back()->with('success', 'Visite supprimée.');
    }
}
