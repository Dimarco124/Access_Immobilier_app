<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Contrat : {{ $contrat->locataire->user->name }}
            </h2>
            <div class="space-x-2">
                <a href="{{ route('contrats.index') }}" class="text-gray-600 hover:text-gray-900">Retour</a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                {{ session('success') }}
            </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Infos Locataire -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Locataire</h3>
                    <div class="space-y-2">
                        <div><span class="text-gray-500">Nom :</span> {{ $contrat->locataire->user->name }}</div>
                        <div><span class="text-gray-500">Email :</span> {{ $contrat->locataire->user->email }}</div>
                        <div><span class="text-gray-500">Téléphone :</span> {{ $contrat->locataire->user->telephone }}</div>
                    </div>
                </div>

                <!-- Infos Bien -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Bien & Unité</h3>
                    <div class="space-y-2">
                        <div><span class="text-gray-500">Bien :</span> {{ $contrat->bien->nom_bien }}</div>
                        <div><span class="text-gray-500">Propriétaire :</span> {{ $contrat->bien->proprietaire->user->name }}</div>
                        <div><span class="text-gray-500">Type :</span> {{ $contrat->bien->type_bien }}</div>
                        <div><span class="text-gray-500">Loyer :</span> {{ number_format($contrat->montant_loyer_actuel, 0, ',', ' ') }} {{ Auth::user()->agence->devise }}</div>
                    </div>
                </div>

                <!-- Infos Contrat -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Contrat</h3>
                    <div class="space-y-2">
                        <div><span class="text-gray-500">Début :</span> {{ $contrat->date_debut }}</div>
                        <div><span class="text-gray-500">Statut :</span> <span class="font-bold">{{ $contrat->statut }}</span></div>
                        <div><span class="text-gray-500">Caution :</span> {{ number_format($contrat->montant_caution, 0, ',', ' ') }}</div>
                    </div>
                </div>
            </div>

            <!-- Facturation -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-medium text-gray-900">Historique des Factures</h3>
                    <button onclick="document.getElementById('modal-facture').classList.remove('hidden')" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-3 rounded text-sm">
                        Générer une Facture
                    </button>
                </div>

                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">N°</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Période</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payé</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($contrat->factures as $facture)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $facture->numero_facture }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $facture->mois_facture }}</td>
                            <td class="px-6 py-4 whitespace-nowrap font-bold">{{ number_format($facture->montant_total_du, 0, ',', ' ') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-green-600">{{ number_format($facture->montant_deja_paye, 0, ',', ' ') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $facture->statut == 'Payée' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $facture->statut }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Facture -->
    <div id="modal-facture" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Générer Facture Mensuelle</h3>
                <form action="{{ route('factures.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="contrat_id" value="{{ $contrat->id }}">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Mois / Période</label>
                            <input type="text" name="mois_facture" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" placeholder="Ex: Juin 2026" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Date d'échéance</label>
                            <input type="date" name="date_echeance" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                        </div>
                    </div>
                    <div class="flex justify-end mt-6 space-x-2">
                        <button type="button" onclick="document.getElementById('modal-facture').classList.add('hidden')" class="bg-gray-200 px-4 py-2 rounded text-gray-800">Annuler</button>
                        <button type="submit" class="bg-blue-500 px-4 py-2 rounded text-white">Générer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>