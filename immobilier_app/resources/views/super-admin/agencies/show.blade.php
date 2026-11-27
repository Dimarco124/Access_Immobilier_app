@extends('layouts.super-admin')

@section('title', $agence->nom)
@section('subtitle', 'Fiche détaillée de l\'agence #' . $agence->id)

@section('content')
    <div class="flex flex-wrap items-center justify-between gap-3">
        <a href="{{ route('super-admin.agencies.index') }}" class="inline-flex items-center gap-2 text-xs font-bold text-slate-600 hover:text-blue-600">
            <i class="fas fa-arrow-left"></i> Retour à la liste
        </a>
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('super-admin.agencies.edit', $agence) }}" class="px-4 py-2 rounded-xl bg-blue-50 text-blue-700 text-xs font-black uppercase hover:bg-blue-100">
                <i class="fas fa-edit mr-1"></i> Modifier
            </a>
            <form method="POST" action="{{ route('super-admin.agencies.toggle-status', $agence) }}">
                @csrf
                <button type="submit" class="px-4 py-2 rounded-xl text-xs font-black uppercase {{ $agence->statut === 'actif' ? 'bg-amber-50 text-amber-700 hover:bg-amber-100' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100' }}">
                    <i class="fas {{ $agence->statut === 'actif' ? 'fa-ban' : 'fa-check' }} mr-1"></i>
                    {{ $agence->statut === 'actif' ? 'Suspendre l\'agence' : 'Réactiver l\'agence' }}
                </button>
            </form>
            <form method="POST" action="{{ route('super-admin.agencies.destroy', $agence) }}" onsubmit="return confirm('Supprimer définitivement cette agence et TOUTES ses données ?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-4 py-2 rounded-xl bg-rose-50 text-rose-700 text-xs font-black uppercase hover:bg-rose-100">
                    <i class="fas fa-trash mr-1"></i> Supprimer
                </button>
            </form>
        </div>
    </div>

    {{-- Résumé KPI --}}
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
        <div class="sa-card p-4">
            <p class="text-[10px] font-black uppercase text-slate-400">Statut</p>
            <p class="text-base font-black mt-1 {{ $agence->statut === 'actif' ? 'text-emerald-600' : 'text-rose-600' }} uppercase">{{ $agence->statut }}</p>
        </div>
        <div class="sa-card p-4">
            <p class="text-[10px] font-black uppercase text-slate-400">Utilisateurs</p>
            <p class="text-2xl font-black text-slate-900 mt-1">{{ $agence->users_count }}</p>
        </div>
        <div class="sa-card p-4">
            <p class="text-[10px] font-black uppercase text-slate-400">Biens</p>
            <p class="text-2xl font-black text-slate-900 mt-1">{{ $agence->biens_count }}</p>
        </div>
        <div class="sa-card p-4">
            <p class="text-[10px] font-black uppercase text-slate-400">Contrats</p>
            <p class="text-2xl font-black text-slate-900 mt-1">{{ $agence->contrats_count }}</p>
        </div>
        <div class="sa-card p-4">
            <p class="text-[10px] font-black uppercase text-slate-400">Volume encaissé</p>
            <p class="text-lg font-black text-indigo-600 mt-1">{{ number_format($agence->paiements_sum_montant_verse ?? 0, 0, ',', ' ') }} {{ $agence->devise }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="sa-card p-6 space-y-3 text-xs">
            <h2 class="text-sm font-black uppercase text-slate-900 border-b border-slate-100 pb-2">Coordonnées</h2>
            <p><span class="text-slate-400 font-bold">Email :</span> {{ $agence->email }}</p>
            <p><span class="text-slate-400 font-bold">Téléphone :</span> {{ $agence->telephone }}</p>
            <p><span class="text-slate-400 font-bold">Adresse :</span> {{ $agence->adresse }}</p>
            <p><span class="text-slate-400 font-bold">Devise :</span> {{ $agence->devise }} (TVA {{ $agence->taux_tva }}%)</p>
            <p><span class="text-slate-400 font-bold">Propriétaires :</span> {{ $agence->proprietaires_count }} • <span class="text-slate-400 font-bold">Locataires :</span> {{ $agence->locataires_count }}</p>
        </div>

        <div class="lg:col-span-2 sa-card p-6">
            <h2 class="text-sm font-black uppercase text-slate-900 border-b border-slate-100 pb-2 mb-4">Comptes utilisateurs de l'agence</h2>
            <div class="overflow-x-auto">
                <table class="w-full text-xs">
                    <thead>
                        <tr class="text-left text-[10px] font-black uppercase text-slate-400 border-b border-slate-100">
                            <th class="pb-2">Nom</th>
                            <th class="pb-2">Email</th>
                            <th class="pb-2">Rôle</th>
                            <th class="pb-2 text-right">Support</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($users as $u)
                            <tr>
                                <td class="py-2.5 font-bold text-slate-800">{{ $u->name }}</td>
                                <td class="py-2.5 text-slate-600">{{ $u->email }}</td>
                                <td class="py-2.5"><span class="px-2 py-0.5 rounded-full bg-slate-100 text-[10px] font-bold uppercase">{{ $u->roles->pluck('name')->join(', ') }}</span></td>
                                <td class="py-2.5 text-right">
                                    <form method="POST" action="{{ route('super-admin.users.impersonate', $u) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="px-2.5 py-1 rounded-lg bg-indigo-50 text-indigo-700 font-bold hover:bg-indigo-100">
                                            <i class="fas fa-user-secret mr-1"></i> Connecter en tant que
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
