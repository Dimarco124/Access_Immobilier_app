@extends('prints.list')

@section('title', 'Liste des Factures')

@section('filters')
    @if(request()->filled('numero'))
    <div>
        <span class="font-semibold">Numéro:</span>
        <span>{{ request('numero') }}</span>
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
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
            <div class="text-sm text-gray-600 mb-1">Total factures</div>
            <div class="text-2xl font-bold text-blue-600">{{ $stats['total'] }}</div>
        </div>
        <div class="bg-green-50 p-4 rounded-lg border border-green-200">
            <div class="text-sm text-gray-600 mb-1">Payées</div>
            <div class="text-2xl font-bold text-green-600">{{ $stats['payees'] }}</div>
        </div>
        <div class="bg-red-50 p-4 rounded-lg border border-red-200">
            <div class="text-sm text-gray-600 mb-1">Impayées</div>
            <div class="text-2xl font-bold text-red-600">{{ $stats['impayees'] }}</div>
        </div>
        <div class="bg-indigo-50 p-4 rounded-lg border border-indigo-200">
            <div class="text-sm text-gray-600 mb-1">Montant payé</div>
            <div class="text-2xl font-bold text-indigo-600">{{ number_format($stats['montant_paye'], 0, ',', ' ') }} F</div>
        </div>
    </div>
@endsection

@section('table')
    <table class="min-w-full">
        <thead>
            <tr class="bg-gray-100 border-b-2 border-gray-300">
                <th class="py-3 px-4 text-left text-xs font-bold text-gray-700 uppercase">N° Facture</th>
                <th class="py-3 px-4 text-left text-xs font-bold text-gray-700 uppercase">Locataire</th>
                <th class="py-3 px-4 text-left text-xs font-bold text-gray-700 uppercase">Bien</th>
                <th class="py-3 px-4 text-left text-xs font-bold text-gray-700 uppercase">Mois</th>
                <th class="py-3 px-4 text-right text-xs font-bold text-gray-700 uppercase">Montant</th>
                <th class="py-3 px-4 text-center text-xs font-bold text-gray-700 uppercase">Échéance</th>
                <th class="py-3 px-4 text-center text-xs font-bold text-gray-700 uppercase">Statut</th>
            </tr>
        </thead>
        <tbody>
            @forelse($factures as $facture)
            <tr class="border-b border-gray-200 hover:bg-gray-50">
                <td class="py-3 px-4 text-sm font-semibold">{{ $facture->numero_facture }}</td>
                <td class="py-3 px-4 text-sm">{{ $facture->contrat->locataire->user->name }}</td>
                <td class="py-3 px-4 text-sm">{{ $facture->contrat->bien->nom_bien ?? '-' }}</td>
                <td class="py-3 px-4 text-sm">{{ $facture->mois_facture }}</td>
                <td class="py-3 px-4 text-sm text-right font-semibold">{{ number_format($facture->montant_loyer, 0, ',', ' ') }} F</td>
                <td class="py-3 px-4 text-sm text-center">{{ \Carbon\Carbon::parse($facture->date_echeance)->format('d/m/Y') }}</td>
                <td class="py-3 px-4 text-sm text-center">
                    @if($facture->statut == 'Payée')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-green-100 text-green-800">
                            Payée
                        </span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-800">
                            Impayée
                        </span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="py-8 px-4 text-center text-gray-500 italic">Aucune facture trouvée</td>
            </tr>
            @endforelse
        </tbody>
        <tfoot class="bg-gray-50 border-t-2 border-gray-300">
            <tr>
                <td colspan="4" class="py-3 px-4 text-sm font-bold text-gray-700 text-right">TOTAL:</td>
                <td class="py-3 px-4 text-sm font-bold text-gray-900 text-right">{{ number_format($stats['montant_total'], 0, ',', ' ') }} F</td>
                <td colspan="2"></td>
            </tr>
        </tfoot>
    </table>
@endsection
