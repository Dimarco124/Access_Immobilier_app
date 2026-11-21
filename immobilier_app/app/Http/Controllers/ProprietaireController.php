<?php

namespace App\Http\Controllers;

use App\Models\Proprietaire;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;

class ProprietaireController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        // Si c'est un propriétaire, rediriger vers ses propriétés
        if ($user->hasRole('proprietaire')) {
            return redirect()->route('proprietaires.mes-proprietes');
        } else {
            // Si c'est l'agence, afficher tous les propriétaires avec recherche optionnelle
            $query = Proprietaire::where('agence_id', Auth::user()->agence_id)->with('user');
            
            if ($request->filled('search')) {
                $search = $request->input('search');
                $query->where(function($q) use ($search) {
                    $q->whereHas('user', function($qUser) use ($search) {
                        $qUser->where('name', 'like', "%{$search}%")
                              ->orWhere('email', 'like', "%{$search}%")
                              ->orWhere('telephone', 'like', "%{$search}%");
                    })
                    ->orWhere('civilite', 'like', "%{$search}%")
                    ->orWhere('ville', 'like', "%{$search}%")
                    ->orWhere('contact_secondaire', 'like', "%{$search}%");
                });
            }

            // Filtres multi-champs
            if ($request->filled('nom')) {
                $query->whereHas('user', function($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->nom . '%');
                });
            }
            if ($request->filled('contact')) {
                $contact = $request->contact;
                $query->whereHas('user', function($q) use ($contact) {
                    $q->where('email', 'like', "%{$contact}%")
                      ->orWhere('telephone', 'like', "%{$contact}%");
                });
            }
            if ($request->filled('ville')) {
                $query->where('ville', 'like', '%' . $request->ville . '%');
            }
            if ($request->filled('civilite')) {
                $query->where('civilite', $request->civilite);
            }

            // AMÉLIORATION: Pagination avec décompte des Propriétés et Unités locatives
            $proprietaires = $query->withCount(['famillesDeBiens as proprietes_count', 'biens as unites_count'])
                ->latest()
                ->paginate(15)
                ->withQueryString();
        }

        return view('proprietaires.index', compact('proprietaires'));
    }

    public function printList(Request $request)
    {
        // Récupérer les mêmes filtres que dans index()
        $query = Proprietaire::where('agence_id', Auth::user()->agence_id)->with('user');
        
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->whereHas('user', function($qUser) use ($search) {
                    $qUser->where('name', 'like', "%{$search}%")
                          ->orWhere('email', 'like', "%{$search}%")
                          ->orWhere('telephone', 'like', "%{$search}%");
                })
                ->orWhere('civilite', 'like', "%{$search}%")
                ->orWhere('ville', 'like', "%{$search}%")
                ->orWhere('contact_secondaire', 'like', "%{$search}%");
            });
        }

        // Filtres multi-champs
        if ($request->filled('nom')) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->nom . '%');
            });
        }
        if ($request->filled('contact')) {
            $contact = $request->contact;
            $query->whereHas('user', function($q) use ($contact) {
                $q->where('email', 'like', "%{$contact}%")
                  ->orWhere('telephone', 'like', "%{$contact}%");
            });
        }
        if ($request->filled('ville')) {
            $query->where('ville', 'like', '%' . $request->ville . '%');
        }
        if ($request->filled('civilite')) {
            $query->where('civilite', $request->civilite);
        }

        $proprietaires = $query->withCount(['famillesDeBiens as proprietes_count', 'biens as unites_count'])->latest()->get();
        
        // Calculer les stats
        $stats = [
            'total' => $proprietaires->count(),
            'total_proprietes' => $proprietaires->sum('proprietes_count'),
            'total_biens' => $proprietaires->sum('unites_count'),
        ];

        return view('proprietaires.print', compact('proprietaires', 'stats'));
    }

    // Nouvelle méthode pour les propriétaires : voir leurs biens
    public function mesProprietes()
    {
        $user = Auth::user();
        if (!$user->hasRole('proprietaire')) {
            abort(403);
        }

        $proprietaire = $user->proprietaire;

        // Récupérer toutes les familles de biens du propriétaire
        $familles = \App\Models\FamilleDeBien::where('proprietaire_id', $proprietaire->id)
            ->with(['biens.contrats' => function ($q) {
                $q->where('statut', 'Actif')->with('locataire.user');
            }])
            ->get();

        // Calculer les stats
        $total_biens = 0;
        $biens_loues = 0;

        foreach ($familles as $famille) {
            foreach ($famille->biens as $bien) {
                $total_biens++;
                if ($bien->statut == 'Loué') {
                    $biens_loues++;
                }
            }
        }

        $biens_disponibles = $total_biens - $biens_loues;
        $taux_occupation = $total_biens > 0 ? round(($biens_loues / $total_biens) * 100, 1) : 0;

        return view('proprietaires.mes-proprietes', compact('familles', 'total_biens', 'biens_loues', 'biens_disponibles', 'taux_occupation'));
    }

    public function create()
    {
        return view('proprietaires.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'telephone' => 'nullable|string',
            'adresse_residence' => 'nullable|string',
            'numero_piece_identite' => 'nullable|string',
            'rib_bancaire' => 'nullable|string',
            'cni_recto' => 'nullable|file|mimes:pdf,jpg,png,jpeg|max:5120',
            'cni_verso' => 'nullable|file|mimes:pdf,jpg,png,jpeg|max:5120',
        ]);

        $user = User::create([
            'agence_id' => Auth::user()->agence_id,
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'telephone' => $request->telephone,
        ]);

        Role::firstOrCreate(['name' => 'proprietaire']);
        $user->assignRole('proprietaire');

        $data = [
            'agence_id' => Auth::user()->agence_id,
            'user_id' => $user->id,
            'civilite' => $request->civilite,
            'adresse_residence' => $request->adresse_residence,
            'numero_piece_identite' => $request->numero_piece_identite,
            'contact_secondaire' => $request->contact_secondaire,
            'pays' => $request->pays,
            'ville' => $request->ville,
            'rib_bancaire' => $request->rib_bancaire,
        ];

        if ($request->hasFile('cni_recto')) {
            $data['cni_recto'] = $request->file('cni_recto')->store('pieces_identite/proprietaires', 'local');
        }
        if ($request->hasFile('cni_verso')) {
            $data['cni_verso'] = $request->file('cni_verso')->store('pieces_identite/proprietaires', 'local');
        }

        Proprietaire::create($data);

        return redirect()->route('proprietaires.index')->with('success', 'Propriétaire créé avec succès.');
    }

    public function edit(Proprietaire $proprietaire)
    {
        if ($proprietaire->agence_id !== Auth::user()->agence_id) abort(403);
        return view('proprietaires.edit', compact('proprietaire'));
    }

    public function show(Proprietaire $proprietaire)
    {
        if ($proprietaire->agence_id !== Auth::user()->agence_id) abort(403);
        $proprietaire->load(['user', 'famillesDeBiens.biens', 'biens']);
        return view('proprietaires.show', compact('proprietaire'));
    }

    public function update(Request $request, Proprietaire $proprietaire)
    {
        if ($proprietaire->agence_id !== Auth::user()->agence_id) abort(403);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $proprietaire->user_id,
            'telephone' => 'nullable|string',
            'adresse_residence' => 'nullable|string',
            'numero_piece_identite' => 'nullable|string',
            'rib_bancaire' => 'nullable|string',
            'cni_recto' => 'nullable|file|mimes:pdf,jpg,png,jpeg|max:5120',
            'cni_verso' => 'nullable|file|mimes:pdf,jpg,png,jpeg|max:5120',
        ]);

        $proprietaire->user->update([
            'name' => $request->name,
            'email' => $request->email,
            'telephone' => $request->telephone,
        ]);

        $data = [
            'civilite' => $request->civilite,
            'adresse_residence' => $request->adresse_residence,
            'numero_piece_identite' => $request->numero_piece_identite,
            'contact_secondaire' => $request->contact_secondaire,
            'pays' => $request->pays,
            'ville' => $request->ville,
            'rib_bancaire' => $request->rib_bancaire,
        ];

        if ($request->hasFile('cni_recto')) {
            $data['cni_recto'] = $request->file('cni_recto')->store('pieces_identite/proprietaires', 'local');
        }
        if ($request->hasFile('cni_verso')) {
            $data['cni_verso'] = $request->file('cni_verso')->store('pieces_identite/proprietaires', 'local');
        }

        $proprietaire->update($data);

        return redirect()->route('proprietaires.show', $proprietaire)->with('success', 'Propriétaire mis à jour avec succès.');
    }

    public function destroy(Proprietaire $proprietaire)
    {
        if ($proprietaire->agence_id !== Auth::user()->agence_id) abort(403);
        $proprietaire->user->delete(); // Cascade delete will handle the Proprietaire record
        return redirect()->route('proprietaires.index')->with('success', 'Propriétaire supprimé.');
    }
}
