@extends('prints.list')

@section('title', 'Liste des Tickets')

@section('filters')
    @if(request()->filled('objet'))
    <div>
        <span class="font-semibold">Objet:</span>
        <span>{{ request('objet') }}</span>
    </div>
    @endif
    @if(request()->filled('locataire'))
    <div>
        <span class="font-semibold">Locataire:</span>
        <span>{{ request('locataire') }}</span>
    </div>
    @endif
    @if(request()->filled('bien'))
    <div>
        <span class="font-semibold">Bien:</span>
        <span>{{ request('bien') }}</span>
    </div>
    @endif
    @if(request()->filled('priorite'))
    <div>
        <span class="font-semibold">Priorité:</span>
        <span>{{ request('priorite') }}</span>
    </div>
    @endif
    @if(request()->filled('statut'))
    <div>
        <span class="font-semibold">Statut:</span>
        <span>{{ request('statut') }}</span>
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
    <div class="grid grid-cols-4 gap-4 mb-6">
        <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
            <div class="text-sm text-gray-600 mb-1">Total tickets</div>
            <div class="text-2xl font-bold text-blue-600">{{ $stats['total'] }}</div>
        </div>
        <div class="bg-yellow-50 p-4 rounded-lg border border-yellow-200">
            <div class="text-sm text-gray-600 mb-1">Ouverts</div>
            <div class="text-2xl font-bold text-yellow-600">{{ $stats['ouverts'] }}</div>
        </div>
        <div class="bg-orange-50 p-4 rounded-lg border border-orange-200">
            <div class="text-sm text-gray-600 mb-1">En cours</div>
            <div class="text-2xl font-bold text-orange-600">{{ $stats['en_cours'] }}</div>
        </div>
        <div class="bg-green-50 p-4 rounded-lg border border-green-200">
            <div class="text-sm text-gray-600 mb-1">Résolus</div>
            <div class="text-2xl font-bold text-green-600">{{ $stats['resolus'] }}</div>
        </div>
    </div>
@endsection

@section('table')
    <table class="min-w-full">
        <thead>
            <tr class="bg-gray-100 border-b-2 border-gray-300">
                <th class="py-3 px-4 text-left text-xs font-bold text-gray-700 uppercase">Objet</th>
                <th class="py-3 px-4 text-left text-xs font-bold text-gray-700 uppercase">Locataire</th>
                <th class="py-3 px-4 text-left text-xs font-bold text-gray-700 uppercase">Bien</th>
                <th class="py-3 px-4 text-center text-xs font-bold text-gray-700 uppercase">Priorité</th>
                <th class="py-3 px-4 text-center text-xs font-bold text-gray-700 uppercase">Statut</th>
                <th class="py-3 px-4 text-center text-xs font-bold text-gray-700 uppercase">Date</th>
            </tr>
        </thead>
        <tbody>
            @forelse($tickets as $ticket)
            <tr class="border-b border-gray-200 hover:bg-gray-50">
                <td class="py-3 px-4 text-sm font-semibold">{{ $ticket->objet }}</td>
                <td class="py-3 px-4 text-sm">{{ $ticket->locataire->user->name }}</td>
                <td class="py-3 px-4 text-sm">
                    @if($ticket->bien)
                        {{ $ticket->bien->parent->nom_famille ?? '' }} - {{ $ticket->bien->nom_bien }}
                    @else
                        -
                    @endif
                </td>
                <td class="py-3 px-4 text-sm text-center">
                    @if($ticket->priorite == 'Critique')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-800">
                            Critique
                        </span>
                    @elseif($ticket->priorite == 'Urgent')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-orange-100 text-orange-800">
                            Urgent
                        </span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-100 text-blue-800">
                            Faible
                        </span>
                    @endif
                </td>
                <td class="py-3 px-4 text-sm text-center">
                    @if($ticket->statut == 'Résolu')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-green-100 text-green-800">
                            Résolu
                        </span>
                    @elseif($ticket->statut == 'En cours')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-yellow-100 text-yellow-800">
                            En cours
                        </span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-gray-100 text-gray-800">
                            Ouvert
                        </span>
                    @endif
                </td>
                <td class="py-3 px-4 text-sm text-center">{{ $ticket->created_at->format('d/m/Y') }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="py-8 px-4 text-center text-gray-500 italic">Aucun ticket trouvé</td>
            </tr>
            @endforelse
        </tbody>
    </table>
@endsection
