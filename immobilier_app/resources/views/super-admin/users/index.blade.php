@extends('layouts.super-admin')

@section('title', 'Annuaire Utilisateurs & Support')
@section('subtitle', 'Rechercher, suspendre, réinitialiser un mot de passe ou se connecter en mode support')

@section('content')
    <div class="sa-card p-5">
        <form method="GET" action="{{ route('super-admin.users.index') }}" class="flex flex-wrap items-center gap-3">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Nom, email ou téléphone..."
                class="w-full sm:w-64 rounded-xl border border-slate-200 px-3.5 py-2 text-xs">
            <select name="agence_id" class="rounded-xl border border-slate-200 px-3.5 py-2 text-xs font-bold text-slate-700">
                <option value="">Toutes les agences</option>
                @foreach($agences as $ag)
                    <option value="{{ $ag->id }}" {{ (string) request('agence_id') === (string) $ag->id ? 'selected' : '' }}>{{ $ag->nom }}</option>
                @endforeach
            </select>
            <select name="role" class="rounded-xl border border-slate-200 px-3.5 py-2 text-xs font-bold text-slate-700">
                <option value="">Tous les rôles</option>
                <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin Agence</option>
                <option value="team_member" {{ request('role') === 'team_member' ? 'selected' : '' }}>Membre d'équipe</option>
                <option value="proprietaire" {{ request('role') === 'proprietaire' ? 'selected' : '' }}>Propriétaire</option>
                <option value="locataire" {{ request('role') === 'locataire' ? 'selected' : '' }}>Locataire</option>
            </select>
            <button type="submit" class="px-4 py-2 rounded-xl bg-slate-900 text-white text-xs font-black uppercase">Filtrer</button>
            @if(request()->hasAny(['search', 'agence_id', 'role']))
                <a href="{{ route('super-admin.users.index') }}" class="text-xs font-bold text-slate-500">Réinitialiser</a>
            @endif
        </form>
    </div>

    <div class="sa-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead>
                    <tr class="bg-slate-50 text-left text-[10px] font-black uppercase text-slate-500 border-b border-slate-200">
                        <th class="px-5 py-3.5">Utilisateur</th>
                        <th class="px-5 py-3.5">Agence</th>
                        <th class="px-5 py-3.5">Rôle</th>
                        <th class="px-5 py-3.5 text-center">Statut</th>
                        <th class="px-5 py-3.5 text-right">Actions Support</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($users as $user)
                        <tr class="hover:bg-slate-50/70">
                            <td class="px-5 py-3.5">
                                <p class="font-black text-slate-900">{{ $user->name }}</p>
                                <p class="text-[11px] text-slate-500">{{ $user->email }} @if($user->telephone) • {{ $user->telephone }} @endif</p>
                            </td>
                            <td class="px-5 py-3.5 font-semibold text-slate-700">{{ $user->agence->nom ?? '—' }}</td>
                            <td class="px-5 py-3.5">
                                <span class="px-2.5 py-1 rounded-full bg-slate-100 text-[10px] font-black uppercase text-slate-600">
                                    {{ $user->roles->pluck('name')->join(', ') ?: 'Aucun' }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-center">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase {{ $user->is_active !== false ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }}">
                                    {{ $user->is_active !== false ? 'Actif' : 'Suspendu' }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 text-right whitespace-nowrap space-x-1.5">
                                <form method="POST" action="{{ route('super-admin.users.impersonate', $user) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="px-2.5 py-1.5 rounded-lg bg-indigo-50 text-indigo-700 hover:bg-indigo-100 font-bold" title="Se connecter en tant que">
                                        <i class="fas fa-user-secret mr-1"></i> Support
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('super-admin.users.reset-password', $user) }}" class="inline" onsubmit="return confirm('Générer un nouveau mot de passe temporaire pour {{ $user->email }} ?');">
                                    @csrf
                                    <button type="submit" class="px-2.5 py-1.5 rounded-lg bg-blue-50 text-blue-700 hover:bg-blue-100 font-bold" title="Réinitialiser mot de passe">
                                        <i class="fas fa-key"></i>
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('super-admin.users.toggle-status', $user) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="px-2.5 py-1.5 rounded-lg {{ $user->is_active !== false ? 'bg-amber-50 text-amber-700 hover:bg-amber-100' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100' }} font-bold"
                                        title="{{ $user->is_active !== false ? 'Suspendre' : 'Réactiver' }}">
                                        <i class="fas {{ $user->is_active !== false ? 'fa-ban' : 'fa-check' }}"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-5 py-10 text-center text-slate-400">Aucun utilisateur trouvé.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($users->hasPages())
            <div class="px-5 py-4 border-t border-slate-100">{{ $users->links() }}</div>
        @endif
    </div>
@endsection
