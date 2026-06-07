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
    public function index()
    {
        $user = Auth::user();
        $agenceId = $user->agence_id;

        if ($user->hasRole('admin')) {
            // Vue complète de toutes les visites
            $visites = Visite::where('agence_id', $agenceId)
                ->with(['bien', 'locataire.user'])
                ->orderBy('date_heure_visite', 'desc')
                ->paginate(15);
        } elseif ($user->hasRole('proprietaire')) {
            // Visites des biens du propriétaire
            $proprietaire = $user->proprietaire;
            $visites = Visite::where('agence_id', $agenceId)
                ->whereIn('bien_id', $proprietaire->biens()->pluck('id'))
                ->with(['bien', 'locataire.user'])
                ->orderBy('date_heure_visite', 'desc')
                ->paginate(15);
        } elseif ($user->hasRole('locataire')) {
            // Visites du locataire uniquement
            $locataire = $user->locataire;
            $visites = Visite::where('agence_id', $agenceId)
                ->where('locataire_id', $locataire->id)
                ->with(['bien', 'locataire.user'])
                ->orderBy('date_heure_visite', 'desc')
                ->paginate(15);
        } else {
            abort(403, 'Accès non autorisé');
        }
        
        return view('visites.index', compact('visites'));
    }

    /**
     * Afficher le formulaire de création
     */
    public function create()
    {
        $user = Auth::user();
        
        // Seule l'agence peut créer des visites
        if (!$user->hasRole('admin')) {
            abort(403, 'Seule l\'agence peut créer des visites');
        }

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
            'locataire_id' => 'required|exists:locataires,id',
            'date_heure_visite' => 'required|date_format:Y-m-d\TH:i',
            'montant_paye' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $bien = Bien::find($request->bien_id);
        $locataire = Locataire::find($request->locataire_id);

        // Vérifier que le bien et le locataire appartiennent à l'agence
        if ($bien->agence_id !== Auth::user()->agence_id || $locataire->agence_id !== Auth::user()->agence_id) {
            abort(403);
        }

        Visite::create([
            'agence_id' => Auth::user()->agence_id,
            'bien_id' => $request->bien_id,
            'locataire_id' => $request->locataire_id,
            'date_heure_visite' => $request->date_heure_visite,
            'montant_paye' => $request->montant_paye ?? $bien->prix_visite,
            'notes' => $request->notes,
            'statut' => 'Programmé',
        ]);

        return back()->with('success', 'Visite enregistrée avec succès.');
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
     * Annuler une visite
     */
    public function cancel(Visite $visite)
    {
        if ($visite->agence_id !== Auth::user()->agence_id) {
            abort(403);
        }

        $visite->update(['statut' => 'Annulé']);

        return back()->with('success', 'Visite annulée.');
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
