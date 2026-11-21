@extends('prints.list')

@section('title', 'Liste des Locataires')

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
    <div class="grid grid-cols-2 gap-4 mb-6">
        <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
            <div class="text-sm text-gray-600 mb-1">Nombre de locataires</div>
            <div class="text-2xl font-bold text-blue-600">{{ $stats['total'] }}</div>
        </div>
        <div class="bg-green-50 p-4 rounded-lg border border-green-200">
            <div class="text-sm text-gray-600 mb-1">Locataires actifs</div>
            <div class="text-2xl font-bold text-green-600">{{ $stats['actifs'] }}</div>
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
                <th class="py-3 px-4 text-left text-xs font-bold text-gray-700 uppercase">Profession</th>
                <th class="py-3 px-4 text-left text-xs font-bold text-gray-700 uppercase">Ville</th>
                <th class="py-3 px-4 text-center text-xs font-bold text-gray-700 uppercase">Statut</th>
            </tr>
        </thead>
        <tbody>
            @forelse($locataires as $locataire)
            <tr class="border-b border-gray-200 hover:bg-gray-50">
                <td class="py-3 px-4 text-sm">{{ $locataire->civilite ?? '-' }}</td>
                <td class="py-3 px-4 text-sm font-semibold">{{ $locataire->user->name }}</td>
                <td class="py-3 px-4 text-sm">{{ $locataire->user->email }}</td>
                <td class="py-3 px-4 text-sm">{{ $locataire->user->telephone ?? '-' }}</td>
                <td class="py-3 px-4 text-sm">{{ $locataire->profession ?? '-' }}</td>
                <td class="py-3 px-4 text-sm">{{ $locataire->ville ?? '-' }}</td>
                <td class="py-3 px-4 text-sm text-center">
                    @if($locataire->contrats->count() > 0)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-green-100 text-green-800">
                            Actif
                        </span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-gray-100 text-gray-800">
                            Inactif
                        </span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="py-8 px-4 text-center text-gray-500 italic">Aucun locataire trouvé</td>
            </tr>
            @endforelse
        </tbody>
    </table>
@endsection
