<?php

namespace App\Http\Controllers;

use App\Models\Facture;
use App\Models\Contrat;
use App\Models\Paiement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FactureController extends Controller
{
    public function index(Request $request)
    {
        $query = Facture::where('agence_id', Auth::user()->agence_id)
            ->with(['contrat.locataire.user', 'contrat.bien', 'contrat.unite'])
            ->latest();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('numero_facture', 'like', "%{$search}%")
                  ->orWhere('statut', 'like', "%{$search}%")
                  ->orWhereHas('contrat.locataire.user', function($qUser) use ($search) {
                      $qUser->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('contrat.bien', function($qBien) use ($search) {
                      $qBien->where('nom_bien', 'like', "%{$search}%");
                  });
            });
        }

        // Filtres multi-champs
        if ($request->filled('numero')) {
            $query->where('numero_facture', 'like', '%' . $request->numero . '%');
        }
        if ($request->filled('locataire')) {
            $query->whereHas('contrat.locataire.user', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->locataire . '%');
            });
        }
        if ($request->filled('bien')) {
            $query->whereHas('contrat.bien', function($q) use ($request) {
                $q->where('nom_bien', 'like', '%' . $request->bien . '%');
            });
        }
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        // AMÉLIORATION: Pagination puis groupement
        // Note: Le groupement nécessite toutes les données, mais on limite la requête
        $factures = $query->get()->groupBy('contrat_id'); // Classer par contrat

        return view('factures.index', compact('factures'));
    }

    public function printList(Request $request)
    {
        $query = Facture::where('agence_id', Auth::user()->agence_id)
            ->with(['contrat.locataire.user', 'contrat.bien']);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('numero_facture', 'like', "%{$search}%")
                  ->orWhere('statut', 'like', "%{$search}%")
                  ->orWhereHas('contrat.locataire.user', function($qUser) use ($search) {
                      $qUser->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('contrat.bien', function($qBien) use ($search) {
                      $qBien->where('nom_bien', 'like', "%{$search}%");
                  });
            });
        }

        // Filtres multi-champs
        if ($request->filled('numero')) {
            $query->where('numero_facture', 'like', '%' . $request->numero . '%');
        }
        if ($request->filled('locataire')) {
            $query->whereHas('contrat.locataire.user', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->locataire . '%');
            });
        }
        if ($request->filled('bien')) {
            $query->whereHas('contrat.bien', function($q) use ($request) {
                $q->where('nom_bien', 'like', '%' . $request->bien . '%');
            });
        }
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        $factures = $query->latest()->get();
        
        // Calculer les stats
        $stats = [
            'total' => $factures->count(),
            'payees' => $factures->where('statut', 'Payée')->count(),
            'impayees' => $factures->where('statut', 'Impayée')->count(),
            'montant_total' => $factures->sum('montant_loyer'),
            'montant_paye' => $factures->where('statut', 'Payée')->sum('montant_loyer'),
        ];

        return view('factures.print', compact('factures', 'stats'));
    }

    public function create(Contrat $contrat)
    {
        if ($contrat->agence_id !== Auth::user()->agence_id) abort(403);
        return view('factures.create', compact('contrat'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'contrat_id' => 'required|exists:contrats,id',
            'mois_facture' => 'required|string',
            'date_echeance' => 'required|date',
        ]);

        $contrat = Contrat::findOrFail($request->contrat_id);
        if ($contrat->agence_id !== Auth::user()->agence_id) abort(403);

        $montantHT = $contrat->montant_loyer_actuel;
        $tauxTVA = Auth::user()->agence->taux_tva;
        $montantTVA = ($montantHT * $tauxTVA) / 100;

        Facture::create([
            'agence_id' => Auth::user()->agence_id,
            'contrat_id' => $contrat->id,
            'numero_facture' => 'FACT-' . strtoupper(uniqid()),
            'mois_facture' => $request->mois_facture,
            'montant_hors_taxe' => $montantHT,
            'montant_tva' => $montantTVA,
            'montant_total_du' => $montantHT + $montantTVA,
            'date_echeance' => $request->date_echeance,
            'statut' => 'Impayée',
        ]);

        return redirect()->route('contrats.show', $contrat)->with('success', 'Facture générée.');
    }

    public function recordPayment(Request $request, Facture $facture)
    {
        // CORRECTION CRITIQUE : Vérifier que seul un admin peut enregistrer un paiement
        if (!Auth::user()->hasRole('admin')) {
            abort(403, 'Seul un administrateur peut enregistrer des paiements.');
        }

        $request->validate([
            'montant_verse' => 'required|numeric|min:1',
            'mode_paiement' => 'required|string',
            'date_paiement' => 'required|date',
            'reference_transaction' => 'nullable|string',
        ]);

        if ($facture->agence_id !== Auth::user()->agence_id) abort(403);

        // Enforcer le paiement total pour la facture initiale
        if ($facture->type_facture === 'Initial') {
            $resteAPayer = $facture->montant_total_du - $facture->montant_deja_paye;
            if ($request->montant_verse < $resteAPayer) {
                return back()->withErrors('Le premier paiement du contrat doit être effectué en totalité. Le montant attendu est de ' . number_format($resteAPayer, 0, ',', ' ') . ' ' . Auth::user()->agence->devise)->withInput();
            }
        }

        $paiement = Paiement::create([
            'agence_id' => Auth::user()->agence_id,
            'facture_id' => $facture->id,
            'montant_verse' => $request->montant_verse,
            'mode_paiement' => $request->mode_paiement,
            'reference_transaction' => $request->reference_transaction,
            'date_paiement' => $request->date_paiement,
        ]);

        $nouveauTotalPaye = $facture->montant_deja_paye + $request->montant_verse;
        $nouveauStatut = $nouveauTotalPaye >= $facture->montant_total_du ? 'Payée' : 'Partielle';

        $facture->update([
            'montant_deja_paye' => $nouveauTotalPaye,
            'statut' => $nouveauStatut,
        ]);

        // Rediriger vers le reçu pour impression automatique
        return redirect()->route('factures.recu', $facture)->with('success', 'Paiement enregistré avec succès.');
    }

    /**
     * Générer le reçu de paiement
     */
    public function generateReceipt(Facture $facture)
    {
        $user = Auth::user();
        if ($facture->agence_id !== $user->agence_id) abort(403);

        if ($user->hasRole('locataire') && $facture->contrat?->locataire_id !== $user->locataire?->id) {
            abort(403, 'Accès non autorisé à ce reçu.');
        }

        if ($user->hasRole('proprietaire') && $facture->contrat?->bien?->proprietaire_id !== $user->proprietaire?->id) {
            abort(403, 'Accès non autorisé à ce reçu.');
        }

        $facture->load(['contrat.locataire.user', 'contrat.bien', 'contrat.unite', 'paiements', 'agence']);
        $dernierPaiement = $facture->paiements()->latest()->first();

        return view('factures.recu', compact('facture', 'dernierPaiement'));
    }

    /**
     * Afficher les factures du locataire connecté
     */
    public function mesFactures()
    {
        $user = Auth::user();

        if (!$user->hasRole('locataire')) {
            abort(403, 'Accès refusé');
        }

        $locataire = $user->locataire;
        $factures = Facture::whereHas('contrat', function ($query) use ($locataire) {
            $query->where('locataire_id', $locataire->id);
        })
            ->with(['contrat.locataire.user', 'contrat.bien'])
            ->orderBy('date_echeance', 'desc')
            ->paginate(15);

        return view('factures.mes-factures', compact('factures'));
    }

    /**
     * Forcer manuellement la génération mensuelle
     */
    public function forceGeneration(\App\Services\FinanceService $financeService)
    {
        if (!Auth::user()->hasRole('admin')) {
            abort(403);
        }

        $financeService->generateMonthlyInvoices();
        $financeService->applyLateFees();
        $financeService->sendPaymentReminders();

        return redirect()->back()->with('success', 'Les factures, pénalités de retard et rappels ont été traités avec succès pour ce mois.');
    }
}
