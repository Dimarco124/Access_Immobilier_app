<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Suivi de la Facturation et Paiements') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">N° Facture</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Locataire</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mois</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Dû</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Déjà Payé</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($factures as $facture)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap font-bold">{{ $facture->numero_facture }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $facture->contrat->locataire->user->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $facture->mois_facture }}</td>
                            <td class="px-6 py-4 whitespace-nowrap font-bold">{{ number_format($facture->montant_total_du, 0, ',', ' ') }} {{ Auth::user()->agence->devise }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-green-600">{{ number_format($facture->montant_deja_paye, 0, ',', ' ') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $facture->statut == 'Payée' ? 'bg-green-100 text-green-800' : ($facture->statut == 'Partielle' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                    {{ $facture->statut }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <button onclick="openPaymentModal({{ $facture->id }}, '{{ $facture->numero_facture }}', {{ $facture->montant_total_du - $facture->montant_deja_paye }})" class="text-blue-600 hover:text-blue-900">Encaisser</button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Paiement -->
    <div id="modal-paiement" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4" id="modal-title">Encaisser Paiement</h3>
                <form id="payment-form" method="POST">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Montant versé</label>
                            <input type="number" name="montant_verse" id="montant_verse" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Mode de paiement</label>
                            <select name="mode_paiement" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                                <option value="Espèces">Espèces</option>
                                <option value="Chèque">Chèque</option>
                                <option value="Virement">Virement</option>
                                <option value="Mobile Money">Mobile Money (Manuel)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Date du règlement</label>
                            <input type="date" name="date_paiement" value="{{ date('Y-m-d') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                        </div>
                    </div>
                    <div class="flex justify-end mt-6 space-x-2">
                        <button type="button" onclick="document.getElementById('modal-paiement').classList.add('hidden')" class="bg-gray-200 px-4 py-2 rounded text-gray-800">Annuler</button>
                        <button type="submit" class="bg-blue-500 px-4 py-2 rounded text-white">Valider le paiement</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openPaymentModal(id, numero, reste) {
            document.getElementById('modal-paiement').classList.remove('hidden');
            document.getElementById('modal-title').innerText = 'Paiement : ' + numero;
            document.getElementById('montant_verse').value = reste;
            document.getElementById('payment-form').action = '/factures/' + id + '/payer';
        }
    </script>
</x-app-layout>
