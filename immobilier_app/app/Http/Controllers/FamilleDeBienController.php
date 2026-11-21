<?php

namespace App\Http\Controllers;

use App\Models\FamilleDeBien;
use App\Models\Proprietaire;
use App\Models\Bien;
use App\Models\Unite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FamilleDeBienController extends Controller
{
    public function index(Request $request)
    {
        $query = FamilleDeBien::where('agence_id', Auth::user()->agence_id)
            ->with(['proprietaire.user', 'biens.unites']);

        $queryBiens = Bien::where('agence_id', Auth::user()->agence_id)
            ->whereNotNull('famille_de_bien_id')
            ->with(['familleDeBien', 'proprietaire.user', 'contrats']);

        // Recherche globale rapide
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('nom_famille', 'like', "%{$search}%")
                  ->orWhere('adresse', 'like', "%{$search}%")
                  ->orWhere('type_famille', 'like', "%{$search}%")
                  ->orWhereHas('proprietaire.user', function($qUser) use ($search) {
                      $qUser->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('biens', function($qBien) use ($search) {
                      $qBien->where('nom_bien', 'like', "%{$search}%");
                  });
            });

            $queryBiens->where(function($q) use ($search) {
                $q->where('nom_bien', 'like', "%{$search}%")
                  ->orWhere('adresse', 'like', "%{$search}%")
                  ->orWhere('type_bien', 'like', "%{$search}%")
                  ->orWhereHas('proprietaire.user', function($qUser) use ($search) {
                      $qUser->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('familleDeBien', function($qFamille) use ($search) {
                      $qFamille->where('nom_famille', 'like', "%{$search}%");
                  });
            });
        }

        // Filtre par Nom de propriété / unité
        if ($request->filled('famille')) {
            $nom = $request->input('famille');
            $query->where(function($q) use ($nom) {
                $q->where('nom_famille', 'like', "%{$nom}%")
                  ->orWhereHas('biens', function($qb) use ($nom) {
                      $qb->where('nom_bien', 'like', "%{$nom}%");
                  });
            });

            $queryBiens->where(function($q) use ($nom) {
                $q->where('nom_bien', 'like', "%{$nom}%")
                  ->orWhereHas('familleDeBien', function($qFamille) use ($nom) {
                      $qFamille->where('nom_famille', 'like', "%{$nom}%");
                  });
            });
        }

        // Filtre par Propriétaire
        if ($request->filled('proprietaire')) {
            $prop = $request->input('proprietaire');
            $query->whereHas('proprietaire.user', function($q) use ($prop) {
                $q->where('name', 'like', "%{$prop}%");
            });

            $queryBiens->whereHas('proprietaire.user', function($q) use ($prop) {
                $q->where('name', 'like', "%{$prop}%");
            });
        }

        // Filtre par Statut (Disponible, Loué, En rénovation...)
        if ($request->filled('statut')) {
            $statut = $request->input('statut');
            $query->whereHas('biens', function($q) use ($statut) {
                $q->where('statut', $statut);
            });

            $queryBiens->where('statut', $statut);
        }

        // Filtre par Type (Appartement, Villa, Bureau, etc.)
        if ($request->filled('type')) {
            $type = $request->input('type');
            $query->where(function($q) use ($type) {
                $q->where('type_famille', 'like', "%{$type}%")
                  ->orWhereHas('biens', function($qb) use ($type) {
                      $qb->where('type_bien', 'like', "%{$type}%");
                  });
            });

            $queryBiens->where('type_bien', 'like', "%{$type}%");
        }

        $familles = $query->latest()->get();
        $toutesUnites = $queryBiens->latest()->get();

        return view('familles-de-biens.index', compact('familles', 'toutesUnites'));
    }

    public function printList(Request $request)
    {
        $query = FamilleDeBien::where('agence_id', Auth::user()->agence_id)
            ->with('proprietaire.user', 'biens');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('nom_famille', 'like', "%{$search}%")
                  ->orWhere('adresse', 'like', "%{$search}%")
                  ->orWhere('type_famille', 'like', "%{$search}%")
                  ->orWhereHas('proprietaire.user', function($qUser) use ($search) {
                      $qUser->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('famille')) {
            $nom = $request->input('famille');
            $query->where(function($q) use ($nom) {
                $q->where('nom_famille', 'like', "%{$nom}%")
                  ->orWhereHas('biens', function($qb) use ($nom) {
                      $qb->where('nom_bien', 'like', "%{$nom}%");
                  });
            });
        }
        if ($request->filled('proprietaire')) {
            $prop = $request->input('proprietaire');
            $query->whereHas('proprietaire.user', function($q) use ($prop) {
                $q->where('name', 'like', "%{$prop}%");
            });
        }
        if ($request->filled('statut')) {
            $statut = $request->input('statut');
            $query->whereHas('biens', function($q) use ($statut) {
                $q->where('statut', $statut);
            });
        }
        if ($request->filled('type')) {
            $type = $request->input('type');
            $query->where(function($q) use ($type) {
                $q->where('type_famille', 'like', "%{$type}%")
                  ->orWhereHas('biens', function($qb) use ($type) {
                      $qb->where('type_bien', 'like', "%{$type}%");
                  });
            });
        }

        $familles = $query->get();
        
        // Calculer les stats
        $stats = [
            'total' => $familles->count(),
            'total_biens' => $familles->sum(function($f) { return $f->biens->count(); }),
            'biens_disponibles' => $familles->sum(function($f) { return $f->biens->where('statut', 'Disponible')->count(); }),
            'biens_loues' => $familles->sum(function($f) { return $f->biens->where('statut', 'Loué')->count(); }),
        ];

        return view('familles-de-biens.print', compact('familles', 'stats'));
    }

    public function create()
    {
        $proprietaires = Proprietaire::where('agence_id', Auth::user()->agence_id)->with('user')->get();

        return view('familles-de-biens.create', compact('proprietaires'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom_famille' => 'required|string|max:255',
            'type_famille' => 'required|string|max:100',
            'nature' => 'required|in:collective,individuelle',
            'adresse' => 'required|string|max:255',
            'description' => 'nullable|string',
            'proprietaire_id' => 'required|exists:proprietaires,id',
            'loyer_mensuel' => 'nullable|numeric|min:0',
            'taux_commission_agence' => 'nullable|numeric|min:0|max:100',
            'prix_visite' => 'nullable|numeric|min:0',
        ]);

        $proprietaire = Proprietaire::where('agence_id', Auth::user()->agence_id)
            ->findOrFail($request->proprietaire_id);

        $data = $request->only(['nom_famille', 'type_famille', 'nature', 'adresse', 'description']);
        $data['proprietaire_id'] = $proprietaire->id;
        $data['agence_id'] = Auth::user()->agence_id;

        $famille = FamilleDeBien::create($data);

        // Si propriété individuelle (ex: Villa, Maison...), on génère immédiatement l'unité locative prête à louer
        if ($request->nature === 'individuelle') {
            $bien = Bien::create([
                'agence_id' => Auth::user()->agence_id,
                'famille_de_bien_id' => $famille->id,
                'proprietaire_id' => $famille->proprietaire_id,
                'nom_bien' => $famille->nom_famille,
                'type_bien' => $famille->type_famille,
                'adresse' => $famille->adresse,
                'loyer_mensuel' => $request->loyer_mensuel ?? 0,
                'taux_commission_agence' => $request->taux_commission_agence ?? 10.00,
                'prix_visite' => $request->prix_visite ?? 0,
                'statut' => 'Disponible',
                'description' => $famille->description,
            ]);

            Unite::create([
                'agence_id' => Auth::user()->agence_id,
                'bien_id' => $bien->id,
                'nom_unite' => $famille->nom_famille,
                'type_unite' => $famille->type_famille,
                'adresse' => $famille->adresse,
                'prix_visite' => $request->prix_visite ?? 0,
                'loyer_mensuel' => $request->loyer_mensuel ?? 0,
                'statut' => 'Disponible',
                'description' => $famille->description,
                'migrated_from_bien_id' => $bien->id,
            ]);

            return redirect()->route('proprietes.index')->with('success', 'Propriété individuelle créée avec succès et prête à la location !');
        }

        return redirect()->route('proprietes.show', $famille)->with('success', 'Propriété créée avec succès ! Vous pouvez maintenant y ajouter des unités locatives.');
    }

    public function show(FamilleDeBien $familleDeBien)
    {
        // DIAGNOSTIC: Logger les IDs pour débug
        \Log::info('FamilleDeBienController::show - Vérification accès', [
            'famille_id' => $familleDeBien->id,
            'famille_agence_id' => $familleDeBien->agence_id,
            'user_id' => Auth::user()->id,
            'user_agence_id' => Auth::user()->agence_id,
            'match' => $familleDeBien->agence_id === Auth::user()->agence_id
        ]);

        if ($familleDeBien->agence_id !== Auth::user()->agence_id) {
            \Log::error('FamilleDeBienController::show - ACCÈS REFUSÉ 403', [
                'famille_agence_id' => $familleDeBien->agence_id,
                'user_agence_id' => Auth::user()->agence_id
            ]);
            abort(403);
        }

        $familleDeBien->load('proprietaire.user', 'biens.unites');

        return view('familles-de-biens.show', ['familleDeBien' => $familleDeBien]);
    }

    public function edit(FamilleDeBien $familleDeBien)
    {
        if ($familleDeBien->agence_id !== Auth::user()->agence_id) {
            abort(403);
        }

        $proprietaires = Proprietaire::where('agence_id', Auth::user()->agence_id)->with('user')->get();
        $bienIndividuel = $familleDeBien->isIndividuelle() ? $familleDeBien->biens()->first() : null;

        return view('familles-de-biens.edit', [
            'familleDeBien' => $familleDeBien, 
            'proprietaires' => $proprietaires,
            'bienIndividuel' => $bienIndividuel
        ]);
    }

    public function update(Request $request, FamilleDeBien $familleDeBien)
    {
        if ($familleDeBien->agence_id !== Auth::user()->agence_id) {
            abort(403);
        }

        $request->validate([
            'nom_famille' => 'required|string|max:255',
            'type_famille' => 'required|string|max:100',
            'nature' => 'nullable|in:collective,individuelle',
            'adresse' => 'required|string|max:255',
            'description' => 'nullable|string',
            'proprietaire_id' => 'required|exists:proprietaires,id',
            'loyer_mensuel' => 'nullable|numeric|min:0',
            'taux_commission_agence' => 'nullable|numeric|min:0|max:100',
            'prix_visite' => 'nullable|numeric|min:0',
        ]);

        $proprietaire = Proprietaire::where('agence_id', Auth::user()->agence_id)
            ->findOrFail($request->proprietaire_id);

        $data = $request->only(['nom_famille', 'type_famille', 'adresse', 'description']);
        $data['proprietaire_id'] = $proprietaire->id;
        if ($request->filled('nature')) {
            $data['nature'] = $request->nature;
        }

        $familleDeBien->update($data);

        // Si propriété individuelle, synchroniser le Bien et l'Unité associés
        if ($familleDeBien->isIndividuelle()) {
            $bien = $familleDeBien->biens()->first();
            if ($bien) {
                $bienUpdate = [
                    'nom_bien' => $familleDeBien->nom_famille,
                    'type_bien' => $familleDeBien->type_famille,
                    'adresse' => $familleDeBien->adresse,
                    'proprietaire_id' => $familleDeBien->proprietaire_id,
                    'description' => $familleDeBien->description,
                ];
                if ($request->filled('loyer_mensuel')) {
                    $bienUpdate['loyer_mensuel'] = $request->loyer_mensuel;
                }
                if ($request->filled('taux_commission_agence')) {
                    $bienUpdate['taux_commission_agence'] = $request->taux_commission_agence;
                }
                if ($request->has('prix_visite')) {
                    $bienUpdate['prix_visite'] = $request->prix_visite ?? 0;
                }
                $bien->update($bienUpdate);

                // Mettre à jour l'Unité associée
                $unite = Unite::where('migrated_from_bien_id', $bien->id)->first() ?? $bien->unites()->first();
                if ($unite) {
                    $unite->update([
                        'nom_unite' => $familleDeBien->nom_famille,
                        'type_unite' => $familleDeBien->type_famille,
                        'adresse' => $familleDeBien->adresse,
                        'loyer_mensuel' => $bien->loyer_mensuel,
                        'prix_visite' => $bien->prix_visite,
                        'description' => $familleDeBien->description,
                    ]);
                }
            } else {
                // Si pas encore de bien enfant créé
                $bien = Bien::create([
                    'agence_id' => Auth::user()->agence_id,
                    'famille_de_bien_id' => $familleDeBien->id,
                    'proprietaire_id' => $familleDeBien->proprietaire_id,
                    'nom_bien' => $familleDeBien->nom_famille,
                    'type_bien' => $familleDeBien->type_famille,
                    'adresse' => $familleDeBien->adresse,
                    'loyer_mensuel' => $request->loyer_mensuel ?? 0,
                    'taux_commission_agence' => $request->taux_commission_agence ?? 10.00,
                    'prix_visite' => $request->prix_visite ?? 0,
                    'statut' => 'Disponible',
                    'description' => $familleDeBien->description,
                ]);

                Unite::create([
                    'agence_id' => Auth::user()->agence_id,
                    'bien_id' => $bien->id,
                    'nom_unite' => $familleDeBien->nom_famille,
                    'type_unite' => $familleDeBien->type_famille,
                    'adresse' => $familleDeBien->adresse,
                    'prix_visite' => $request->prix_visite ?? 0,
                    'loyer_mensuel' => $request->loyer_mensuel ?? 0,
                    'statut' => 'Disponible',
                    'description' => $familleDeBien->description,
                    'migrated_from_bien_id' => $bien->id,
                ]);
            }
        }

        return redirect()->route('proprietes.show', $familleDeBien)->with('success', 'Propriété mise à jour avec succès !');
    }

    public function destroy(FamilleDeBien $familleDeBien)
    {
        if ($familleDeBien->agence_id !== Auth::user()->agence_id) {
            abort(403);
        }

        $familleDeBien->delete();

        return redirect()->route('proprietes.index')->with('success', 'Propriété supprimée avec succès !');
    }
}
