<?php

namespace App\Http\Controllers;

use App\Models\Contrat;
use App\Models\Locataire;
use App\Models\Bien;
use App\Models\Unite;
use App\Models\FamilleDeBien;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContratController extends Controller
{
    public function index(Request $request)
    {
        $query = Contrat::where('agence_id', Auth::user()->agence_id)
            ->with(['locataire.user', 'bien.familleDeBien']);
            
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('numero_contrat', 'like', "%{$search}%")
                  ->orWhere('statut', 'like', "%{$search}%")
                  ->orWhereHas('locataire.user', function($qUser) use ($search) {
                      $qUser->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('bien', function($qBien) use ($search) {
                      $qBien->where('nom_bien', 'like', "%{$search}%");
                  });
            });
        }

        // Filtres multi-champs
        if ($request->filled('numero')) {
            $query->where('numero_contrat', 'like', '%' . $request->numero . '%');
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
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        // AMÉLIORATION: Pagination au lieu de get() pour éviter de charger tous les records
        $contrats = $query->latest()->paginate(15)->withQueryString();
        
        return view('contrats.index', compact('contrats'));
    }

    public function show(Contrat $contrat)
    {
        $user = Auth::user();

        // Vérifications d'accès :
        // 1. Admin de l'agence → ok
        // 2. Locataire du contrat → ok
        // 3. Propriétaire du bien lié → ok
        $hasAccess = false;

        if ($user->agence_id === $contrat->agence_id && ($user->hasRole('admin') || $user->hasRole('team_member'))) {
            $hasAccess = true;
        } elseif ($user->hasRole('locataire') && $contrat->locataire_id === $user->locataire->id) {
            $hasAccess = true;
        } elseif ($user->hasRole('proprietaire') && $contrat->bien && $contrat->bien->proprietaire_id === $user->proprietaire->id) {
            $hasAccess = true;
        }

        if (!$hasAccess) {
            abort(403);
        }

        $contrat->load(['locataire.user', 'bien.proprietaire.user', 'factures' => function ($q) {
            $q->orderBy('date_echeance', 'desc');
        }]);

        // Générer le dossier historique des mois
        $historiqueMois = [];
        $dateDebut = $contrat->date_debut->startOfMonth();

        // Déterminer la date de fin :
        // - Si contrat terminé : date de clôture
        // - Sinon : le maximum entre le mois en cours et la fin de la période d'avance
        $dateFinCourante = now()->startOfMonth();
        if ($contrat->statut === 'Terminé') {
            $dateFin = $contrat->date_cloture->startOfMonth();
        } else {
            $dateFin = $dateFinCourante;
        }

        // Calculer la période couverte par l'avance initiale
        $dateDebutAvance = $contrat->date_debut->startOfMonth();
        $dateFinAvance = null;
        if ($contrat->nombre_mois_avance > 0) {
            // Nombre mois avance = mois courant + n mois d'avance → total (n+1) mois
            $dateFinAvance = $dateDebutAvance->copy()->addMonths($contrat->nombre_mois_avance);

            // Si la fin de l'avance est dans le futur, on étend la date de fin pour inclure ces mois
            if ($contrat->statut !== 'Terminé' && $dateFinAvance->greaterThan($dateFin)) {
                $dateFin = $dateFinAvance;
            }
        } else {
            // Pas de mois d'avance
            $dateFinAvance = $dateDebutAvance->copy()->subMonth(); // Pour que la condition ne soit jamais vraie
        }

        // Vérifier si la facture initiale est payée (pour valider l'avance)
        $factureInitiale = $contrat->factures->first(fn($f) => $f->type_facture === 'Initial');
        $avanceValidee = $factureInitiale && $factureInitiale->statut === 'Payée';

        $current = $dateFin->copy();
        while ($current >= $dateDebut) {
            $moisFormat = $current->translatedFormat('F Y');

            // Chercher la facture de loyer pour ce mois
            $facture = $contrat->factures->first(function ($f) use ($moisFormat) {
                return $f->mois_facture === $moisFormat && ($f->type_facture === 'Loyer' || $f->type_facture === 'Initial');
            });

            // Vérifier si ce mois est couvert par l'avance
            $isAvance = false;
            if ($current->greaterThanOrEqualTo($dateDebutAvance) && $current->lessThanOrEqualTo($dateFinAvance)) {
                $isAvance = true;
            }

            // Déterminer le statut final
            $statutFinal = null;
            $considererCommeSolde = false;

            if ($isAvance && $avanceValidee) {
                $considererCommeSolde = true;
                $statutFinal = 'solde_avance';
            } elseif ($facture) {
                if ($facture->statut === 'Payée') {
                    $statutFinal = 'paye';
                } elseif ($facture->statut === 'Partielle') {
                    $statutFinal = 'partiel';
                } else {
                    $statutFinal = 'impaye';
                }
            } else {
                $statutFinal = 'non_facture';
            }

            $historiqueMois[] = [
                'mois' => $moisFormat,
                'date' => $current->copy(),
                'facture' => $facture,
                'is_current' => $current->isCurrentMonth(),
                'is_avance' => $isAvance,
                'avance_validee' => $avanceValidee,
                'statut_final' => $statutFinal,
                'considerer_comme_solde' => $considererCommeSolde,
            ];

            $current->subMonth();
        }

        return view('contrats.show', compact('contrat', 'historiqueMois'));
    }

    public function create()
    {
        $locataires = Locataire::where('agence_id', Auth::user()->agence_id)->with('user')->get();
        
        // IMPORTANT: Charger seulement les biens DISPONIBLES dans chaque famille
        $familles = FamilleDeBien::where('agence_id', Auth::user()->agence_id)
            ->with(['biens' => function($query) {
                $query->where('statut', 'Disponible');
            }])
            ->get();

        return view('contrats.create', compact('locataires', 'familles'));
    }

    public function store(Request $request)
    {
        $agence = Auth::user()->agence;

        $request->validate([
            'locataire_id' => 'required|exists:locataires,id',
            'bien_id' => 'required|exists:biens,id',
            'date_debut' => 'required|date',
            'date_fin' => 'nullable|date|after:date_debut',
            'montant_loyer_actuel' => 'required|numeric',
            'montant_caution' => 'required|numeric|min:0',
            'nombre_mois_avance' => 'required|integer|min:0',
            'frais_dossier' => 'nullable|numeric',
        ]);

        $bien = Bien::where('agence_id', Auth::user()->agence_id)->findOrFail($request->bien_id);
        $locataire = Locataire::where('agence_id', Auth::user()->agence_id)->findOrFail($request->locataire_id);

        // Vérification de l'audit locataire (Phase 1)
        if ($locataire->statut_audit !== 'Validé') {
            return back()->withErrors('Le dossier de ce locataire n\'a pas encore été validé par l\'audit.')->withInput();
        }

        // VÉRIFICATION: Le bien ne doit pas avoir de contrat actif
        $contratActifExistant = Contrat::where('bien_id', $request->bien_id)
            ->where('statut', 'Actif')
            ->exists();

        if ($contratActifExistant) {
            return back()->withErrors('Ce bien est déjà loué. Un contrat actif existe déjà pour ce bien.')->withInput();
        }

        // Vérifier que le bien est disponible
        if ($bien->statut !== 'Disponible') {
            return back()->withErrors('Ce bien n\'est pas disponible pour la location.')->withInput();
        }

        $data = $request->all();
        $data['agence_id'] = Auth::user()->agence_id;
        $data['statut'] = 'Actif';
        $data['unite_id'] = null;

        // On crée d'abord le contrat pour avoir l'ID
        $contrat = Contrat::create($data);

        // Générer et mettre à jour le numéro de contrat
        $contrat->update([
            'numero_contrat' => 'CB-' . str_pad($contrat->id, 6, '0', STR_PAD_LEFT)
        ]);

        // Créer la transaction pour le dépôt de caution
        if ($request->montant_caution > 0) {
            \App\Models\Transaction::create([
                'agence_id' => $agence->id,
                'type_compte' => 'caution',
                'contrat_id' => $contrat->id,
                'type_transaction' => 'depot_caution',
                'sens' => 'credit',
                'montant' => $request->montant_caution,
                'devise' => $agence->devise ?? 'XOF',
                'reference' => 'CAU-' . strtoupper(uniqid()),
                'description' => 'Dépôt de caution pour le contrat #' . $contrat->id,
                'date_transaction' => $request->date_debut,
                'user_id' => Auth::id(),
            ]);
        }

        // Mettre à jour le statut du bien
        $bien->update(['statut' => 'Loué']);

        // Rediriger vers la page d'état des lieux
        return redirect()->route('contrats.etat-des-lieux', $contrat)
            ->with('success', 'Contrat créé ! Complétez maintenant l\'état des lieux d\'entrée.');
    }

    public function edit(Contrat $contrat)
    {
        if ($contrat->agence_id !== Auth::user()->agence_id)
            abort(403);

        // Surveillance des statuts : impossible d'éditer un contrat clôturé ou terminé
        if ($contrat->statut !== 'Actif') {
            return redirect()->route('contrats.show', $contrat)
                ->with('error', "Ce contrat est {$contrat->statut} et ne peut plus être modifié.");
        }

        $contrat->load(['locataire.user', 'bien.parent', 'bien.proprietaire.user', 'bien.familleDeBien']);

        return view('contrats.edit', compact('contrat'));
    }

    public function update(Request $request, Contrat $contrat)
    {
        if ($contrat->agence_id !== Auth::user()->agence_id)
            abort(403);

        // Surveillance des statuts : impossible de modifier un contrat terminé ou résilié
        if ($contrat->statut !== 'Actif') {
            return redirect()->route('contrats.show', $contrat)
                ->with('error', "Impossible de modifier un contrat {$contrat->statut}.");
        }

        $minDate = $contrat->date_debut ? $contrat->date_debut->format('Y-m-d') : 'yesterday';

        $request->validate([
            'date_fin' => 'nullable|date|after:' . $minDate,
            'montant_loyer_actuel' => 'required|numeric|min:0',
            'date_avis_conge' => 'nullable|date',
            'frais_dossier' => 'nullable|numeric|min:0',
            'etat_des_lieux_entree' => 'nullable|string',
        ]);

        // Seuls les champs modifiables sont mis à jour
        // (Le locataire, le bien, la caution, l'avance et la date de début sont protégés et immuables)
        $contrat->update([
            'date_fin' => $request->date_fin,
            'montant_loyer_actuel' => $request->montant_loyer_actuel,
            'date_avis_conge' => $request->date_avis_conge,
            'frais_dossier' => $request->filled('frais_dossier') ? $request->frais_dossier : $contrat->frais_dossier,
            'etat_des_lieux_entree' => $request->etat_des_lieux_entree,
        ]);

        return redirect()->route('contrats.show', $contrat)->with('success', 'Contrat mis à jour avec succès.');
    }

    public function showBail(Contrat $contrat)
    {
        $user = Auth::user();

        $hasAccess = false;
        if ($user->agence_id === $contrat->agence_id && ($user->hasRole('admin') || $user->hasRole('team_member'))) {
            $hasAccess = true;
        } elseif ($user->hasRole('locataire') && $contrat->locataire_id === $user->locataire->id) {
            $hasAccess = true;
        } elseif ($user->hasRole('proprietaire') && $contrat->bien && $contrat->bien->proprietaire_id === $user->proprietaire->id) {
            $hasAccess = true;
        }

        if (!$hasAccess) {
            abort(403);
        }

        $contrat->load(['locataire.user', 'bien.proprietaire.user', 'bien.familleDeBien', 'agence']);
        return view('contrats.bail', compact('contrat'));
    }



    public function destroy(Contrat $contrat)
    {
        if ($contrat->agence_id !== Auth::user()->agence_id)
            abort(403);

        // Libérer le bien
        if ($contrat->bien) {
            $contrat->bien->update(['statut' => 'Disponible']);
        }

        // Nettoyer les charges impayées (Factures non payées) avant suppression
        $contrat->factures()->where('statut', '!=', 'Payée')->delete();

        $contrat->delete();

        return redirect()->route('contrats.index')->with('success', 'Contrat supprimé et charges impayées annulées.');
    }

    /**
     * Mettre à jour l'état des lieux (Phase 1 ou 5)
     */
    public function updateInventory(Request $request, Contrat $contrat)
    {
        if ($contrat->agence_id !== Auth::user()->agence_id) abort(403);

        $request->validate([
            'type' => 'required|in:entree,sortie',
            'description' => 'required|string',
        ]);

        if ($request->type === 'entree') {
            $contrat->update(['etat_des_lieux_entree' => $request->description]);
        } else {
            $contrat->update(['etat_des_lieux_sortie' => $request->description]);
        }

        return back()->with('success', 'État des lieux mis à jour.');
    }

    /**
     * Enregistrer la réception du préavis (Phase 5)
     */
    public function registerNotice(Request $request, Contrat $contrat)
    {
        if ($contrat->agence_id !== Auth::user()->agence_id) abort(403);

        $request->validate(['date_avis_conge' => 'required|date']);

        $contrat->update(['date_avis_conge' => $request->date_avis_conge]);

        return back()->with('success', 'Préavis de départ enregistré.');
    }

    /**
     * Clôturer définitivement le bail (Phase 5 : Fin)
     */
    public function close(Request $request, Contrat $contrat)
    {
        if ($contrat->agence_id !== Auth::user()->agence_id) abort(403);

        $request->validate([
            'date_cloture' => 'required|date',
            'montant_caution_retenu' => 'required|numeric|min:0',
            'notes_cloture' => 'nullable|string',
        ]);

        // Calcul du remboursement
        $remboursement = max(0, $contrat->montant_caution - $request->montant_caution_retenu);

        $contrat->update([
            'statut' => 'Terminé',
            'date_cloture' => $request->date_cloture,
            'montant_caution_retenu' => $request->montant_caution_retenu,
            'montant_caution_rembourse' => $remboursement,
            'notes_cloture' => $request->notes_cloture,
        ]);

        // Libérer le bien
        if ($contrat->bien) {
            $contrat->bien->update(['statut' => 'Disponible']);
        }

        // Nettoyer les charges impayées (Factures non payées)
        $contrat->factures()->where('statut', '!=', 'Payée')->delete();

        return redirect()->route('contrats.index')->with('success', 'Le contrat a été clôturé, les charges impayées ont été annulées et le bien est de nouveau disponible.');
    }

    /**
     * Afficher les contrats du locataire connecté
     */
    public function mesContrats()
    {
        $user = Auth::user();

        if (!$user->hasRole('locataire')) {
            abort(403, 'Accès refusé');
        }

        $locataire = $user->locataire;
        $contrats = Contrat::where('locataire_id', $locataire->id)
            ->with(['locataire.user', 'bien.parent', 'bien.proprietaire.user'])
            ->orderBy('date_debut', 'desc')
            ->paginate(15);

        return view('contrats.mes-contrats', compact('contrats'));
    }

    /**
     * Imprimer la liste des contrats (avec filtres)
     */
    public function printList(Request $request)
    {
        $query = Contrat::where('agence_id', Auth::user()->agence_id)
            ->with(['locataire.user', 'bien.familleDeBien']);
            
        // Recherche globale
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('numero_contrat', 'like', "%{$search}%")
                  ->orWhere('statut', 'like', "%{$search}%")
                  ->orWhereHas('locataire.user', function($qUser) use ($search) {
                      $qUser->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('bien', function($qBien) use ($search) {
                      $qBien->where('nom_bien', 'like', "%{$search}%");
                  });
            });
        }

        // Appliquer les mêmes filtres que dans index()
        if ($request->filled('numero')) {
            $query->where('numero_contrat', 'like', '%' . $request->numero . '%');
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
        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        $contrats = $query->latest()->get();
        $agence = Auth::user()->agence;
        $filters = $request->except(['_token', 'page']);

        // Calcul des statistiques pour la page d'impression
        $stats = [
            'total' => $contrats->count(),
            'actifs' => $contrats->where('statut', 'Actif')->count(),
            'clotures' => $contrats->filter(fn($c) => in_array($c->statut, ['Terminé', 'Clôturé']))->count(),
            'total_loyers' => $contrats->where('statut', 'Actif')->sum('montant_loyer_actuel'),
        ];
        
        return view('contrats.print', compact('contrats', 'agence', 'filters', 'stats'));
    }

    /**
     * Afficher le formulaire d'état des lieux
     */
    public function etatDesLieux(Contrat $contrat)
    {
        if ($contrat->agence_id !== Auth::user()->agence_id) {
            abort(403);
        }

        $contrat->load(['locataire.user', 'bien.proprietaire.user']);
        
        // Charger les visites effectuées pour ce bien et ce locataire
        $visites = \App\Models\Visite::where('agence_id', Auth::user()->agence_id)
            ->where('bien_id', $contrat->bien_id)
            ->where('locataire_id', $contrat->locataire_id)
            ->where('statut', 'Effectué')
            ->orderBy('date_heure_visite', 'desc')
            ->get();

        return view('contrats.etat-des-lieux', compact('contrat', 'visites'));
    }

    /**
     * Enregistrer l'état des lieux
     */
    public function storeEtatDesLieux(Request $request, Contrat $contrat)
    {
        if ($contrat->agence_id !== Auth::user()->agence_id) {
            abort(403);
        }

        $agence = Auth::user()->agence;

        // Valider les données
        $request->validate([
            'visite_id' => 'nullable|exists:visites,id',
            'date_visite' => 'required|date',
            'heure_visite' => 'required',
            'nombre_cles' => 'nullable|integer|min:0',
            'nombre_telecommandes' => 'nullable|integer|min:0',
            'sections' => 'required|array',
            'compteur_eau' => 'nullable|string',
            'compteur_electricite' => 'nullable|string',
            'observations_generales' => 'nullable|string',
        ]);

        // Construire le JSON de l'état des lieux
        $etatDesLieux = [
            'date_visite' => $request->date_visite,
            'heure_visite' => $request->heure_visite,
            'nombre_cles' => $request->nombre_cles,
            'nombre_telecommandes' => $request->nombre_telecommandes,
            'sections' => $request->sections,
            'compteurs' => [
                'eau' => $request->compteur_eau,
                'electricite' => $request->compteur_electricite,
            ],
            'observations_generales' => $request->observations_generales,
            'rempli_par' => Auth::user()->name,
            'date_enregistrement' => now()->format('Y-m-d H:i:s'),
        ];

        // Mettre à jour le contrat avec l'état des lieux ET la visite liée
        $contrat->update([
            'etat_des_lieux_entree' => json_encode($etatDesLieux, JSON_UNESCAPED_UNICODE),
            'visite_id' => $request->visite_id, // Lier la visite au contrat
        ]);

        // PHASE 2 : Génération de la FACTURE UNIQUE INITIALE
        $tauxTVA = $agence->taux_tva ?? 0;

        // Calcul du premier loyer (avec avance)
        $montantLoyerInitial = $contrat->montant_loyer_actuel * $contrat->nombre_mois_avance;

        // Calcul des totaux pour la facture unique
        $caution = $contrat->montant_caution;
        $fraisDossier = $contrat->frais_dossier ?? 0;

        $totalHT = $montantLoyerInitial + $fraisDossier + $caution;

        // TVA appliquée sur Loyer et Frais (pas sur la caution)
        $montantTVA = (($montantLoyerInitial + $fraisDossier) * $tauxTVA) / 100;
        $totalTTC = $totalHT + $montantTVA;

        \App\Models\Facture::create([
            'agence_id' => $agence->id,
            'contrat_id' => $contrat->id,
            'type_facture' => 'Initial',
            'numero_facture' => 'INIT-' . strtoupper(str_pad($contrat->id, 5, '0', STR_PAD_LEFT)),
            'mois_facture' => \Carbon\Carbon::parse($contrat->date_debut)->translatedFormat('F Y'),
            'frais_dossier' => $fraisDossier,
            'montant_caution' => $caution,
            'montant_loyer_initial' => $montantLoyerInitial,
            'montant_hors_taxe' => $totalHT,
            'montant_tva' => $montantTVA,
            'montant_total_du' => $totalTTC,
            'date_echeance' => $contrat->date_debut,
            'statut' => 'Impayée',
        ]);

        return redirect()->route('contrats.bail', $contrat)
            ->with('success', 'État des lieux enregistré et contrat finalisé !');
    }

    /**
     * Afficher le rapport imprimable de l'état des lieux
     */
    public function printEtatDesLieux(Contrat $contrat)
    {
        $user = Auth::user();

        // Vérifications d'accès : Admin, Locataire du contrat, ou Propriétaire du bien
        $hasAccess = false;

        if ($user->agence_id === $contrat->agence_id && ($user->hasRole('admin') || $user->hasRole('team_member'))) {
            $hasAccess = true;
        } elseif ($user->hasRole('locataire') && $contrat->locataire_id === $user->locataire->id) {
            $hasAccess = true;
        } elseif ($user->hasRole('proprietaire') && $contrat->bien && $contrat->bien->proprietaire_id === $user->proprietaire->id) {
            $hasAccess = true;
        }

        if (!$hasAccess) {
            abort(403);
        }

        // Vérifier que l'état des lieux existe
        if (empty($contrat->etat_des_lieux_entree)) {
            return back()->with('error', 'Aucun état des lieux n\'a été enregistré pour ce contrat.');
        }

        // Décoder le JSON de l'état des lieux
        $etatDesLieux = json_decode($contrat->etat_des_lieux_entree, true);

        $contrat->load(['locataire.user', 'bien.proprietaire.user', 'agence']);

        return view('contrats.etat-des-lieux-print', compact('contrat', 'etatDesLieux'));
    }

    /**
     * Afficher le formulaire d'état des lieux de SORTIE
     */
    public function etatDesLieuxSortie(Contrat $contrat)
    {
        $user = Auth::user();

        // Vérifications d'accès :
        // 1. Admin/team_member de l'agence → ok
        // 2. Propriétaire du bien lié → ok (consultation uniquement)
        $hasAccess = false;

        if ($user->agence_id === $contrat->agence_id && ($user->hasRole('admin') || $user->hasRole('team_member'))) {
            $hasAccess = true;
        } elseif ($user->hasRole('proprietaire') && $contrat->bien && $contrat->bien->proprietaire_id === $user->proprietaire->id) {
            $hasAccess = true;
        }

        if (!$hasAccess) {
            abort(403);
        }

        // Vérifier que le contrat est actif
        if ($contrat->statut !== 'Actif') {
            return back()->with('error', 'Ce contrat n\'est plus actif.');
        }

        $contrat->load(['locataire.user', 'bien.proprietaire.user']);
        
        // Décoder l'état des lieux d'entrée pour comparaison
        $etatEntree = [];
        if (!empty($contrat->etat_des_lieux_entree)) {
            $etatEntree = json_decode($contrat->etat_des_lieux_entree, true);
        }

        return view('contrats.etat-des-lieux-sortie', compact('contrat', 'etatEntree'));
    }

    /**
     * Enregistrer l'état des lieux de SORTIE et CLÔTURER automatiquement le contrat
     */
    public function storeEtatDesLieuxSortie(Request $request, Contrat $contrat)
    {
        if ($contrat->agence_id !== Auth::user()->agence_id) {
            abort(403);
        }

        $agence = Auth::user()->agence;

        // Valider les données
        $request->validate([
            'date_visite' => 'required|date',
            'heure_visite' => 'required',
            'nombre_cles_restituees' => 'nullable|integer|min:0',
            'nombre_telecommandes_restituees' => 'nullable|integer|min:0',
            'sections' => 'required|array',
            'compteur_eau' => 'nullable|string',
            'compteur_electricite' => 'nullable|string',
            'observations_generales' => 'nullable|string',
            'montant_retenue_suggere' => 'nullable|numeric|min:0',
        ]);

        // Décoder l'état d'entrée
        $etatEntree = json_decode($contrat->etat_des_lieux_entree, true);

        // Analyser les dégradations automatiquement
        $degradations = $this->analyserDegradations($etatEntree['sections'] ?? [], $request->sections);

        // Construire le JSON de l'état des lieux de sortie
        $etatDesLieuxSortie = [
            'date_visite' => $request->date_visite,
            'heure_visite' => $request->heure_visite,
            'nombre_cles_restituees' => $request->nombre_cles_restituees,
            'nombre_telecommandes_restituees' => $request->nombre_telecommandes_restituees,
            'sections' => $request->sections,
            'compteurs' => [
                'eau' => $request->compteur_eau,
                'electricite' => $request->compteur_electricite,
            ],
            'observations_generales' => $request->observations_generales,
            'degradations' => $degradations,
            'montant_retenue_suggere' => $request->montant_retenue_suggere ?? 0,
            'rempli_par' => Auth::user()->name,
            'date_enregistrement' => now()->format('Y-m-d H:i:s'),
        ];

        // Calculer le montant de caution à retenir et à rembourser
        $montantRetenu = floatval($request->montant_retenue_suggere ?? 0);
        
        // S'assurer que le montant retenu ne dépasse pas la caution
        if ($montantRetenu > $contrat->montant_caution) {
            $montantRetenu = $contrat->montant_caution;
        }
        
        $montantRembourse = $contrat->montant_caution - $montantRetenu;

        // CLÔTURE AUTOMATIQUE DU CONTRAT
        $contrat->update([
            'etat_des_lieux_sortie' => json_encode($etatDesLieuxSortie, JSON_UNESCAPED_UNICODE),
            'statut' => 'Terminé',
            'date_cloture' => $request->date_visite, // Date de l'état des lieux = date de clôture
            'montant_caution_retenu' => $montantRetenu,
            'montant_caution_rembourse' => $montantRembourse,
            'notes_cloture' => count($degradations) > 0 
                ? count($degradations) . ' dégradation(s) détectée(s) lors de l\'état des lieux de sortie.' 
                : 'Aucune dégradation constatée. Logement rendu en bon état.',
        ]);

        // Libérer le bien (le rendre disponible)
        if ($contrat->bien) {
            $contrat->bien->update(['statut' => 'Disponible']);
        }

        // Nettoyer les factures impayées (optionnel, selon votre logique métier)
        $contrat->factures()->where('statut', '!=', 'Payée')->delete();

        // Créer une transaction pour le remboursement de caution (si applicable)
        if ($montantRembourse > 0) {
            \App\Models\Transaction::create([
                'agence_id' => $agence->id,
                'type_compte' => 'caution',
                'contrat_id' => $contrat->id,
                'type_transaction' => 'remboursement_caution',
                'sens' => 'debit',
                'montant' => $montantRembourse,
                'devise' => $agence->devise ?? 'XOF',
                'reference' => 'REMB-' . strtoupper(uniqid()),
                'description' => 'Remboursement de caution - Contrat #' . $contrat->numero_contrat,
                'date_transaction' => $request->date_visite,
                'user_id' => Auth::id(),
            ]);
        }

        // Créer une transaction pour la retenue (si applicable)
        if ($montantRetenu > 0) {
            \App\Models\Transaction::create([
                'agence_id' => $agence->id,
                'type_compte' => 'caution',
                'contrat_id' => $contrat->id,
                'type_transaction' => 'retenue_caution',
                'sens' => 'credit',
                'montant' => $montantRetenu,
                'devise' => $agence->devise ?? 'XOF',
                'reference' => 'RET-' . strtoupper(uniqid()),
                'description' => 'Retenue sur caution pour dégradations - Contrat #' . $contrat->numero_contrat,
                'date_transaction' => $request->date_visite,
                'user_id' => Auth::id(),
            ]);
        }

        return redirect()->route('contrats.show', $contrat)
            ->with('success', 'Contrat clôturé avec succès ! Le bien est maintenant disponible. ' . 
                   ($montantRembourse > 0 ? 'Caution remboursée : ' . number_format($montantRembourse, 0, ',', ' ') . ' ' . $agence->devise : '') .
                   ($montantRetenu > 0 ? ' - Retenue : ' . number_format($montantRetenu, 0, ',', ' ') . ' ' . $agence->devise : ''));
    }

    /**
     * Analyser les dégradations entre l'état d'entrée et de sortie
     */
    private function analyserDegradations(array $sectionsEntree, array $sectionsSortie)
    {
        $degradations = [];
        $etatsValeur = [
            'excellent' => 4,
            'bon' => 3,
            'correct' => 2,
            'mauvais' => 1,
        ];

        foreach ($sectionsSortie as $sectionId => $elementsSortie) {
            if (!isset($sectionsEntree[$sectionId])) {
                continue;
            }

            $elementsEntree = $sectionsEntree[$sectionId];

            foreach ($elementsSortie as $elementCode => $dataSortie) {
                // Vérifier que l'élément existe et était présent à l'entrée
                if (!isset($elementsEntree[$elementCode]) || !is_array($dataSortie)) {
                    continue;
                }

                $dataEntree = $elementsEntree[$elementCode];

                // Comparer uniquement si l'élément était présent à l'entrée
                if (isset($dataEntree['present']) && $dataEntree['present']) {
                    $etatEntree = $dataEntree['etat'] ?? 'bon';
                    $etatSortie = $dataSortie['etat'] ?? 'bon';

                    $valeurEntree = $etatsValeur[$etatEntree] ?? 3;
                    $valeurSortie = $etatsValeur[$etatSortie] ?? 3;

                    // Si l'état de sortie est PIRE que l'état d'entrée
                    if ($valeurSortie < $valeurEntree) {
                        $degradations[] = [
                            'section' => $sectionId,
                            'element' => $elementCode,
                            'label' => ucfirst(str_replace('_', ' ', $elementCode)),
                            'etat_entree' => $etatEntree,
                            'etat_sortie' => $etatSortie,
                            'gravite' => $valeurEntree - $valeurSortie, // 1, 2 ou 3
                        ];
                    }
                }
            }
        }

        return $degradations;
    }

    /**
     * Afficher le rapport imprimable de l'état des lieux de SORTIE
     */
    public function printEtatDesLieuxSortie(Contrat $contrat)
    {
        $user = Auth::user();

        // Vérifications d'accès : Admin, Locataire du contrat, ou Propriétaire du bien
        $hasAccess = false;

        if ($user->agence_id === $contrat->agence_id && ($user->hasRole('admin') || $user->hasRole('team_member'))) {
            $hasAccess = true;
        } elseif ($user->hasRole('locataire') && $contrat->locataire_id === $user->locataire->id) {
            $hasAccess = true;
        } elseif ($user->hasRole('proprietaire') && $contrat->bien && $contrat->bien->proprietaire_id === $user->proprietaire->id) {
            $hasAccess = true;
        }

        if (!$hasAccess) {
            abort(403);
        }

        // Vérifier que l'état des lieux de sortie existe
        if (empty($contrat->etat_des_lieux_sortie)) {
            return back()->with('error', 'Aucun état des lieux de sortie n\'a été enregistré pour ce contrat.');
        }

        // Décoder le JSON de l'état des lieux de sortie
        $etatDesLieuxSortie = json_decode($contrat->etat_des_lieux_sortie, true);
        
        // Décoder aussi l'état d'entrée pour comparaison
        $etatDesLieuxEntree = json_decode($contrat->etat_des_lieux_entree, true);

        $contrat->load(['locataire.user', 'bien.proprietaire.user', 'agence']);

        return view('contrats.etat-des-lieux-sortie-print', compact('contrat', 'etatDesLieuxSortie', 'etatDesLieuxEntree'));
    }
}
