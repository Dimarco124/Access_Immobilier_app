@extends('prints.list')

@section('title', 'Parc Immobilier - Propriétés & Unités')

@section('filters')
    @if(request()->filled('famille'))
    <div>
        <span class="font-semibold">Propriété:</span>
        <span>{{ request('famille') }}</span>
    </div>
    @endif
    @if(request()->filled('proprietaire'))
    <div>
        <span class="font-semibold">Propriétaire:</span>
        <span>{{ request('proprietaire') }}</span>
    </div>
    @endif
    @if(request()->filled('statut'))
    <div>
        <span class="font-semibold">Statut:</span>
        <span>{{ request('statut') }}</span>
    </div>
    @endif
    @if(request()->filled('type'))
    <div>
        <span class="font-semibold">Type:</span>
        <span>{{ request('type') }}</span>
    </div>
    @endif
    @if(request()->filled('search'))
    <div>
        <span class="font-semibold">Recherche:</span>
        <span>{{ request('search') }}</span>
    </div>
    @endif
@endsection

@section('summary')
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
            <div class="text-sm text-gray-600 mb-1">Total Propriétés</div>
            <div class="text-2xl font-bold text-blue-600">{{ $stats['total'] }}</div>
        </div>
        <div class="bg-purple-50 p-4 rounded-lg border border-purple-200">
            <div class="text-sm text-gray-600 mb-1">Total Unités Locatives</div>
            <div class="text-2xl font-bold text-purple-600">{{ $stats['total_biens'] }}</div>
        </div>
        <div class="bg-green-50 p-4 rounded-lg border border-green-200">
            <div class="text-sm text-gray-600 mb-1">Unités disponibles</div>
            <div class="text-2xl font-bold text-green-600">{{ $stats['biens_disponibles'] }}</div>
        </div>
        <div class="bg-orange-50 p-4 rounded-lg border border-orange-200">
            <div class="text-sm text-gray-600 mb-1">Unités louées</div>
            <div class="text-2xl font-bold text-orange-600">{{ $stats['biens_loues'] }}</div>
        </div>
    </div>
@endsection

@section('table')
    <table class="min-w-full">
        <thead>
            <tr class="bg-gray-100 border-b-2 border-gray-300">
                <th class="py-3 px-4 text-left text-xs font-bold text-gray-700 uppercase">Nom de la Propriété</th>
                <th class="py-3 px-4 text-left text-xs font-bold text-gray-700 uppercase">Catégorie</th>
                <th class="py-3 px-4 text-left text-xs font-bold text-gray-700 uppercase">Propriétaire</th>
                <th class="py-3 px-4 text-left text-xs font-bold text-gray-700 uppercase">Adresse</th>
                <th class="py-3 px-4 text-center text-xs font-bold text-gray-700 uppercase">Nb. Unités</th>
                <th class="py-3 px-4 text-center text-xs font-bold text-gray-700 uppercase">Disponibles</th>
                <th class="py-3 px-4 text-center text-xs font-bold text-gray-700 uppercase">Louées</th>
            </tr>
        </thead>
        <tbody>
            @forelse($familles as $famille)
            <tr class="border-b border-gray-200 hover:bg-gray-50">
                <td class="py-3 px-4 text-sm font-semibold">{{ $famille->nom_famille }}</td>
                <td class="py-3 px-4 text-sm">{{ $famille->type_famille }}</td>
                <td class="py-3 px-4 text-sm">{{ $famille->proprietaire->user->name ?? '-' }}</td>
                <td class="py-3 px-4 text-sm">{{ $famille->adresse }}</td>
                <td class="py-3 px-4 text-sm text-center">
                    <span class="inline-flex items-center justify-center w-8 h-8 bg-blue-100 text-blue-700 rounded-full font-bold text-xs">
                        {{ $famille->biens->count() }}
                    </span>
                </td>
                <td class="py-3 px-4 text-sm text-center">
                    <span class="inline-flex items-center justify-center w-8 h-8 bg-green-100 text-green-700 rounded-full font-bold text-xs">
                        {{ $famille->biens->where('statut', 'Disponible')->count() }}
                    </span>
                </td>
                <td class="py-3 px-4 text-sm text-center">
                    <span class="inline-flex items-center justify-center w-8 h-8 bg-orange-100 text-orange-700 rounded-full font-bold text-xs">
                        {{ $famille->biens->where('statut', 'Loué')->count() }}
                    </span>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="py-8 px-4 text-center text-gray-500 italic">Aucune propriété trouvée</td>
            </tr>
            @endforelse
        </tbody>
    </table>
@endsection
