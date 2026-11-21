@extends('prints.list')

@section('title', 'Liste des Contrats')

@section('filters')
    @if(request()->filled('numero'))
    <div class="text-yellow-800">
        <span class="font-semibold">Numéro:</span>
        <span class="font-medium">{{ request('numero') }}</span>
    </div>
    @endif
    @if(request()->filled('locataire'))
    <div class="text-yellow-800">
        <span class="font-semibold">Locataire:</span>
        <span class="font-medium">{{ request('locataire') }}</span>
    </div>
    @endif
    @if(request()->filled('bien'))
    <div class="text-yellow-800">
        <span class="font-semibold">Bien:</span>
        <span class="font-medium">{{ request('bien') }}</span>
    </div>
    @endif
    @if(request()->filled('statut'))
    <div class="text-yellow-800">
        <span class="font-semibold">Statut:</span>
        <span class="font-medium">{{ request('statut') }}</span>
    </div>
    @endif
@endsection

@section('summary')
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-blue-50/50 p-6 rounded-xl border border-blue-100">
            <p class="text-xs font-black text-blue-400 uppercase mb-2 tracking-widest">Total contrats</p>
            <p class="text-3xl font-black text-blue-600">{{ $stats['total'] }}</p>
        </div>
        <div class="bg-green-50/50 p-6 rounded-xl border border-green-100">
            <p class="text-xs font-black text-green-400 uppercase mb-2 tracking-widest">Actifs</p>
            <p class="text-3xl font-black text-green-600">{{ $stats['actifs'] }}</p>
        </div>
        <div class="bg-gray-50/50 p-6 rounded-xl border border-gray-100">
            <p class="text-xs font-black text-gray-400 uppercase mb-2 tracking-widest">Clôturés</p>
            <p class="text-3xl font-black text-gray-600">{{ $stats['clotures'] }}</p>
        </div>
        <div class="bg-purple-50/50 p-6 rounded-xl border border-purple-100">
            <p class="text-xs font-black text-purple-400 uppercase mb-2 tracking-widest">Total loyers</p>
            <p class="text-2xl font-black text-purple-600">{{ number_format($stats['total_loyers'], 0, ',', ' ') }} {{ $agence->devise ?? 'F' }}</p>
        </div>
    </div>
@endsection

@section('table')
    <table class="w-full">
        <thead>
            <tr class="bg-slate-50 border-b border-slate-200">
                <th class="text-left text-xs font-black text-slate-500 uppercase tracking-widest whitespace-nowrap px-6 py-5">Numéro</th>
                <th class="text-left text-xs font-black text-slate-500 uppercase tracking-widest whitespace-nowrap px-6 py-5">Locataire</th>
                <th class="text-left text-xs font-black text-slate-500 uppercase tracking-widest whitespace-nowrap px-6 py-5">Bien</th>
                <th class="text-center text-xs font-black text-slate-500 uppercase tracking-widest whitespace-nowrap px-6 py-5">Début</th>
                <th class="text-center text-xs font-black text-slate-500 uppercase tracking-widest whitespace-nowrap px-6 py-5">Fin</th>
                <th class="text-right text-xs font-black text-slate-500 uppercase tracking-widest whitespace-nowrap px-6 py-5">Loyer</th>
                <th class="text-center text-xs font-black text-slate-500 uppercase tracking-widest whitespace-nowrap px-6 py-5">Statut</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-200">
            @forelse($contrats as $contrat)
            <tr class="bg-white hover:bg-slate-50">
                <td class="px-6 py-5">
                    <span class="font-black text-slate-900">{{ $contrat->numero_contrat }}</span>
                </td>
                <td class="px-6 py-5">
                    <span class="text-slate-700 font-medium">{{ $contrat->locataire->user->name ?? '-' }}</span>
                </td>
                <td class="px-6 py-5">
                    <span class="text-slate-700 font-medium">{{ $contrat->bien->nom_bien ?? '-' }}</span>
                </td>
                <td class="px-6 py-5 text-center">
                    <span class="text-slate-600 text-sm">{{ $contrat->date_debut ? \Carbon\Carbon::parse($contrat->date_debut)->format('d/m/Y') : '-' }}</span>
                </td>
                <td class="px-6 py-5 text-center">
                    <span class="text-slate-600 text-sm">{{ $contrat->date_fin ? \Carbon\Carbon::parse($contrat->date_fin)->format('d/m/Y') : '-' }}</span>
                </td>
                <td class="px-6 py-5 text-right">
                    <span class="font-black text-slate-900">{{ number_format($contrat->montant_loyer_actuel ?? $contrat->montant_loyer ?? 0, 0, ',', ' ') }}</span>
                    <span class="text-xs text-slate-500 ml-1">{{ $agence->devise ?? 'F' }}</span>
                </td>
                <td class="px-6 py-5 text-center">
                    @if($contrat->statut == 'Actif')
                        <span class="bg-emerald-100 text-emerald-700 px-2 py-0.5 rounded text-xs font-black uppercase tracking-wider">
                            Actif
                        </span>
                    @elseif(in_array($contrat->statut, ['Clôturé', 'Terminé']))
                        <span class="bg-gray-100 text-gray-700 px-2 py-0.5 rounded text-xs font-black uppercase tracking-wider">
                            {{ $contrat->statut }}
                        </span>
                    @else
                        <span class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded text-xs font-black uppercase tracking-wider">
                            {{ $contrat->statut }}
                        </span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="py-12 px-6 text-center">
                    <div class="text-slate-400 text-lg">
                        <i class="fas fa-inbox text-4xl mb-3"></i>
                        <p class="font-medium">Aucun contrat trouvé</p>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
@endsection
