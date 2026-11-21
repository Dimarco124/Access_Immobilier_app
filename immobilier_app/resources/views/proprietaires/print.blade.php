@extends('prints.list')

@section('title', 'Liste des Propriétaires')

@section('filters')
    @if(request()->filled('nom'))
    <div>
        <span class="font-semibold">Nom:</span>
        <span>{{ request('nom') }}</span>
    </div>
    @endif
    @if(request()->filled('contact'))
    <div>
        <span class="font-semibold">Contact:</span>
        <span>{{ request('contact') }}</span>
    </div>
    @endif
    @if(request()->filled('ville'))
    <div>
        <span class="font-semibold">Ville:</span>
        <span>{{ request('ville') }}</span>
    </div>
    @endif
    @if(request()->filled('civilite'))
    <div>
        <span class="font-semibold">Civilité:</span>
        <span>{{ request('civilite') }}</span>
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
    <div class="grid grid-cols-3 gap-4 mb-6">
        <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
            <div class="text-sm text-gray-600 mb-1">Nombre de propriétaires</div>
            <div class="text-2xl font-bold text-blue-600">{{ $stats['total'] }}</div>
        </div>
        <div class="bg-indigo-50 p-4 rounded-lg border border-indigo-200">
            <div class="text-sm text-gray-600 mb-1">Total Propriétés</div>
            <div class="text-2xl font-bold text-indigo-600">{{ $stats['total_proprietes'] ?? 0 }}</div>
        </div>
        <div class="bg-green-50 p-4 rounded-lg border border-green-200">
            <div class="text-sm text-gray-600 mb-1">Total Unités Locatives</div>
            <div class="text-2xl font-bold text-green-600">{{ $stats['total_biens'] }}</div>
        </div>
    </div>
@endsection

@section('table')
    <table class="min-w-full">
        <thead>
            <tr class="bg-gray-100 border-b-2 border-gray-300">
                <th class="py-3 px-4 text-left text-xs font-bold text-gray-700 uppercase">Civilité</th>
                <th class="py-3 px-4 text-left text-xs font-bold text-gray-700 uppercase">Nom</th>
                <th class="py-3 px-4 text-left text-xs font-bold text-gray-700 uppercase">Email</th>
                <th class="py-3 px-4 text-left text-xs font-bold text-gray-700 uppercase">Téléphone</th>
                <th class="py-3 px-4 text-left text-xs font-bold text-gray-700 uppercase">Ville</th>
                <th class="py-3 px-4 text-center text-xs font-bold text-gray-700 uppercase">Propriétés</th>
                <th class="py-3 px-4 text-center text-xs font-bold text-gray-700 uppercase">Unités</th>
            </tr>
        </thead>
        <tbody>
            @forelse($proprietaires as $proprietaire)
            <tr class="border-b border-gray-200 hover:bg-gray-50">
                <td class="py-3 px-4 text-sm">{{ $proprietaire->civilite ?? '-' }}</td>
                <td class="py-3 px-4 text-sm font-semibold">{{ $proprietaire->user->name }}</td>
                <td class="py-3 px-4 text-sm">{{ $proprietaire->user->email }}</td>
                <td class="py-3 px-4 text-sm">{{ $proprietaire->user->telephone ?? '-' }}</td>
                <td class="py-3 px-4 text-sm">{{ $proprietaire->ville ?? '-' }}</td>
                <td class="py-3 px-4 text-sm text-center font-bold text-indigo-600">
                    {{ $proprietaire->proprietes_count ?? 0 }}
                </td>
                <td class="py-3 px-4 text-sm text-center font-bold text-blue-600">
                    {{ $proprietaire->unites_count ?? $proprietaire->biens_count ?? 0 }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="py-8 px-4 text-center text-gray-500 italic">Aucun propriétaire trouvé</td>
            </tr>
            @endforelse
        </tbody>
    </table>
@endsection
