<?php

namespace App\Http\Controllers;

use App\Models\Locataire;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class LocataireController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        // Si c'est un locataire, afficher seulement ses infos
        if ($user->hasRole('locataire')) {
            $locataires = collect([$user->locataire]);
        } else {
            // Si c'est l'agence, afficher tous les locataires avec recherche optionnelle
            $query = Locataire::where('agence_id', Auth::user()->agence_id)->with('user');

            if ($request->filled('search')) {
                $search = $request->input('search');
                $query->where(function($q) use ($search) {
                    $q->whereHas('user', function($qUser) use ($search) {
                        $qUser->where('name', 'like', "%{$search}%")
                              ->orWhere('email', 'like', "%{$search}%")
                              ->orWhere('telephone', 'like', "%{$search}%");
                    })
                    ->orWhere('civilite', 'like', "%{$search}%")
                    ->orWhere('profession', 'like', "%{$search}%")
                    ->orWhere('ville', 'like', "%{$search}%");
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

            // AMÉLIORATION: Pagination au lieu de get()
            $locataires = $query->latest()->paginate(15)->withQueryString();
        }

        return view('locataires.index', compact('locataires'));
    }

    public function printList(Request $request)
    {
        // Récupérer les mêmes filtres que dans index()
        $query = Locataire::where('agence_id', Auth::user()->agence_id)->with(['user', 'contrats' => function($q) {
            $q->where('statut', 'Actif');
        }]);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->whereHas('user', function($qUser) use ($search) {
                    $qUser->where('name', 'like', "%{$search}%")
                          ->orWhere('email', 'like', "%{$search}%")
                          ->orWhere('telephone', 'like', "%{$search}%");
                })
                ->orWhere('civilite', 'like', "%{$search}%")
                ->orWhere('profession', 'like', "%{$search}%")
                ->orWhere('ville', 'like', "%{$search}%");
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

        $locataires = $query->latest()->get();
        
        // Calculer les stats
        $stats = [
            'total' => $locataires->count(),
            'actifs' => $locataires->filter(function($l) { return $l->contrats->count() > 0; })->count(),
        ];

        return view('locataires.print', compact('locataires', 'stats'));
    }

    public function create()
    {
        return view('locataires.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'telephone' => 'nullable|string',
            'contact_urgence' => 'nullable|string',
            'profession' => 'nullable|string',
            'adresse_residence' => 'nullable|string',
            'numero_piece_identite' => 'nullable|string',
            'pays' => 'nullable|string',
            'ville' => 'nullable|string',
            'cni_recto' => 'nullable|file|mimes:pdf,jpg,png,jpeg|max:5120',
            'cni_verso' => 'nullable|file|mimes:pdf,jpg,png,jpeg|max:5120',
            'copie_piece_identite' => 'nullable|file|mimes:pdf,jpg,png,jpeg|max:5120',
        ]);

        $user = User::create([
            'agence_id' => Auth::user()->agence_id,
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'telephone' => $request->telephone,
        ]);

        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'locataire']);
        $user->assignRole('locataire');

        $data = $request->only(['civilite', 'contact_urgence', 'profession', 'adresse_residence', 'numero_piece_identite', 'pays', 'ville']);
        $data['agence_id'] = Auth::user()->agence_id;
        $data['user_id'] = $user->id;
        $data['statut_audit'] = 'Validé'; // Automatiquement validé comme demandé

        if ($request->hasFile('copie_piece_identite')) {
            $data['copie_piece_identite'] = $request->file('copie_piece_identite')->store('pieces_identite/locataires', 'local');
        }
        if ($request->hasFile('cni_recto')) {
            $data['cni_recto'] = $request->file('cni_recto')->store('pieces_identite/locataires', 'local');
        }
        if ($request->hasFile('cni_verso')) {
            $data['cni_verso'] = $request->file('cni_verso')->store('pieces_identite/locataires', 'local');
        }

        Locataire::create($data);

        return redirect()->route('locataires.index')->with('success', 'Locataire créé avec succès.');
    }

    public function edit(Locataire $locataire)
    {
        if ($locataire->agence_id !== Auth::user()->agence_id) abort(403);
        return view('locataires.edit', compact('locataire'));
    }

    public function show(Locataire $locataire)
    {
        if ($locataire->agence_id !== Auth::user()->agence_id) abort(403);
        $locataire->load(['user', 'contrats.bien']);
        return view('locataires.show', compact('locataire'));
    }

    /**
     * Valider ou Rejeter le dossier du locataire (Phase 1 : Audit)
     */
    public function audit(Request $request, Locataire $locataire)
    {
        // CORRECTION CRITIQUE : Seul un admin peut valider/rejeter un dossier
        if (!Auth::user()->hasRole('admin')) {
            abort(403, 'Seul un administrateur peut effectuer l\'audit des locataires.');
        }

        if ($locataire->agence_id !== Auth::user()->agence_id) abort(403);

        $request->validate([
            'statut_audit' => 'required|in:Validé,Rejeté',
            'notes_audit' => 'nullable|string',
        ]);

        $locataire->update([
            'statut_audit' => $request->statut_audit,
            'notes_audit' => $request->notes_audit,
        ]);

        return back()->with('success', 'Dossier locataire mis à jour.');
    }

    public function update(Request $request, Locataire $locataire)
    {
        if ($locataire->agence_id !== Auth::user()->agence_id) abort(403);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $locataire->user_id,
            'telephone' => 'nullable|string',
            'contact_urgence' => 'nullable|string',
            'profession' => 'nullable|string',
            'copie_piece_identite' => 'nullable|file|mimes:pdf,jpg,png|max:2048',
        ]);

        $locataire->user->update([
            'name' => $request->name,
            'email' => $request->email,
            'telephone' => $request->telephone,
        ]);

        $data = $request->only(['contact_urgence', 'profession']);

        if ($request->hasFile('copie_piece_identite')) {
            if ($locataire->copie_piece_identite) {
                Storage::disk('local')->delete($locataire->copie_piece_identite);
                Storage::disk('public')->delete($locataire->copie_piece_identite);
            }
            $data['copie_piece_identite'] = $request->file('copie_piece_identite')->store('pieces_identite/locataires', 'local');
        }

        $locataire->update($data);

        return redirect()->route('locataires.index')->with('success', 'Locataire mis à jour.');
    }

    public function destroy(Locataire $locataire)
    {
        if ($locataire->agence_id !== Auth::user()->agence_id) abort(403);
        $locataire->user->delete();
        return redirect()->route('locataires.index')->with('success', 'Locataire supprimé.');
    }
}
