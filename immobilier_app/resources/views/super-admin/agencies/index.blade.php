@extends('layouts.super-admin')

@section('title', 'Gestion des Agences')
@section('subtitle', 'Supervision, création, activation et suspension des agences')

@section('content')
    <div class="sa-card p-5">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <form method="GET" action="{{ route('super-admin.agencies.index') }}" class="flex flex-wrap items-center gap-3 flex-1">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Rechercher par nom, email, téléphone..."
                    class="w-full sm:w-72 rounded-xl border border-slate-200 px-3.5 py-2 text-xs focus:border-blue-500 focus:ring-2 focus:ring-blue-100">
                <select name="statut" class="rounded-xl border border-slate-200 px-3.5 py-2 text-xs font-bold text-slate-700">
                    <option value="">Tous les statuts</option>
                    <option value="actif" {{ request('statut') === 'actif' ? 'selected' : '' }}>Actives</option>
                    <option value="suspendu" {{ request('statut') === 'suspendu' ? 'selected' : '' }}>Suspendues</option>
                </select>
                <button type="submit" class="px-4 py-2 rounded-xl bg-slate-900 text-white text-xs font-black uppercase tracking-wider hover:bg-slate-800">
                    Filtrer
                </button>
                @if(request()->hasAny(['search', 'statut']))
                    <a href="{{ route('super-admin.agencies.index') }}" class="text-xs font-bold text-slate-500 hover:text-slate-800">Réinitialiser</a>
                @endif
            </form>

            <div class="flex items-center gap-2.5">
                <a href="{{ route('super-admin.agencies.export-csv') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-slate-200 bg-white text-xs font-bold text-slate-700 hover:bg-slate-50">
                    <i class="fas fa-file-csv text-emerald-600"></i> Export CSV
                </a>
                <a href="{{ route('super-admin.agencies.create') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 text-white text-xs font-black uppercase tracking-wider shadow-sm hover:from-blue-700 hover:to-indigo-700">
                    <i class="fas fa-plus"></i> Nouvelle Agence
                </a>
            </div>
        </div>
    </div>

    <div class="sa-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead>
                    <tr class="bg-slate-50 text-left text-[10px] font-black uppercase tracking-wider text-slate-500 border-b border-slate-200">
                        <th class="px-5 py-3.5">Agence</th>
                        <th class="px-5 py-3.5">Contact</th>
                        <th class="px-5 py-3.5 text-center">Utilisateurs</th>
                        <th class="px-5 py-3.5 text-center">Biens / Contrats</th>
                        <th class="px-5 py-3.5 text-right">Volume encaissé</th>
                        <th class="px-5 py-3.5 text-center">Statut</th>
                        <th class="px-5 py-3.5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($agences as $agence)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="px-5 py-4">
                                <a href="{{ route('super-admin.agencies.show', $agence) }}" class="font-black text-slate-900 hover:text-blue-600 text-sm">{{ $agence->nom }}</a>
                                <p class="text-[11px] text-slate-400">Inscrite le {{ $agence->created_at?->format('d/m/Y') }}</p>
                            </td>
                            <td class="px-5 py-4">
                                <p class="font-semibold text-slate-700">{{ $agence->email }}</p>
                                <p class="text-[11px] text-slate-400">{{ $agence->telephone }}</p>
                            </td>
                            <td class="px-5 py-4 text-center font-bold text-slate-700">{{ $agence->users_count }}</td>
                            <td class="px-5 py-4 text-center font-bold text-slate-700">{{ $agence->biens_count }} / {{ $agence->contrats_count }}</td>
                            <td class="px-5 py-4 text-right font-black text-slate-800">
                                {{ number_format($agence->paiements_sum_montant_verse ?? 0, 0, ',', ' ') }} {{ $agence->devise }}
                            </td>
                            <td class="px-5 py-4 text-center">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase {{ $agence->statut === 'actif' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-rose-50 text-rose-700 border border-rose-200' }}">
                                    {{ $agence->statut }}
                                </span>
                            </td>
                            <td class="px-5 py-4 text-right whitespace-nowrap space-x-1.5">
                                <a href="{{ route('super-admin.agencies.show', $agence) }}" class="inline-flex items-center px-2.5 py-1.5 rounded-lg bg-slate-100 text-slate-700 hover:bg-slate-200 font-bold" title="Détails">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('super-admin.agencies.edit', $agence) }}" class="inline-flex items-center px-2.5 py-1.5 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 font-bold" title="Modifier">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form method="POST" action="{{ route('super-admin.agencies.toggle-status', $agence) }}" class="inline">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center px-2.5 py-1.5 rounded-lg {{ $agence->statut === 'actif' ? 'bg-amber-50 text-amber-700 hover:bg-amber-100' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100' }} font-bold"
                                        title="{{ $agence->statut === 'actif' ? 'Suspendre' : 'Activer' }}">
                                        <i class="fas {{ $agence->statut === 'actif' ? 'fa-ban' : 'fa-check' }}"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-10 text-center text-slate-400">Aucune agence trouvée.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($agences->hasPages())
            <div class="px-5 py-4 border-t border-slate-100">{{ $agences->links() }}</div>
        @endif
    </div>
@endsection
