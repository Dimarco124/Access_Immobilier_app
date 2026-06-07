<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-bold text-3xl text-gray-900">{{ __('Mes Factures') }}</h2>
            <p class="text-gray-600 mt-1">Gestion de vos loyers et paiements</p>
        </div>
    </x-slot>

    <div class="py-12 bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 rounded-r-lg">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if($factures->count() > 0)
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-gradient-to-r from-blue-600 to-blue-700 text-white">
                                <th class="px-6 py-4 text-left text-sm font-semibold">N° Facture</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold">Bien</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold">Mois</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold">Montant Dû</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold">Payé</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold">Reste</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold">Échéance</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold">Statut</th>
                                <th class="px-6 py-4 text-right text-sm font-semibold">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($factures as $facture)
                            <tr class="hover:bg-blue-50 transition duration-150">
                                <td class="px-6 py-4 whitespace-nowrap font-bold text-blue-600">{{ $facture->numero_facture }}</td>
                                <td class="px-6 py-4">
                                    <div class="font-semibold text-gray-900">{{ $facture->contrat->bien->nom_bien }}</div>
                                    <div class="text-xs text-gray-500">{{ $facture->contrat->bien->type_bien }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $facture->mois_facture }}</td>
                                <td class="px-6 py-4 whitespace-nowrap font-bold text-gray-900">
                                    {{ number_format($facture->montant_total_du, 0, ',', ' ') }} {{ Auth::user()->agence->devise }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap font-semibold text-green-600">
                                    {{ number_format($facture->montant_deja_paye, 0, ',', ' ') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap font-bold
                                    @if($facture->montant_total_du - $facture->montant_deja_paye > 0) text-red-600 @else text-green-600 @endif">
                                    {{ number_format($facture->montant_total_du - $facture->montant_deja_paye, 0, ',', ' ') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ \Carbon\Carbon::parse($facture->date_echeance)->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold
                                        @if($facture->statut === 'Payée') bg-green-100 text-green-800
                                        @elseif($facture->statut === 'Partielle') bg-yellow-100 text-yellow-800
                                        @else bg-red-100 text-red-800 @endif">
                                        {{ $facture->statut }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    @if($facture->statut !== 'Payée')
                                    <button onclick="openPaymentModal({{ $facture->id }}, '{{ $facture->numero_facture }}', {{ $facture->montant_total_du - $facture->montant_deja_paye }})" 
                                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-semibold transition duration-200">
                                        Payer
                                    </button>
                                    @else
                                    <span class="text-gray-500 text-sm font-semibold">✓ Payée</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                    {{ $factures->links() }}
                </div>
            </div>
            @else
            <div class="bg-white rounded-2xl shadow-lg p-12 text-center">
                <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="text-xl text-gray-600 font-semibold">Aucune facture enregistrée</p>
                <p class="text-gray-500 mt-2">Vos factures apparaîtront ici une fois créées</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Modal Paiement -->
    <div id="modal-paiement" class="hidden fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full z-50 flex items-center justify-center">
        <div class="relative bg-white w-full max-w-md rounded-2xl shadow-2xl p-8">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-2xl font-bold text-gray-900">Effectuer un Paiement</h3>
                <button onclick="closePaymentModal()" class="text-gray-400 hover:text-gray-600 text-3xl">&times;</button>
            </div>

            <form id="payment-form" method="POST" class="space-y-5">
                @csrf
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Facture</label>
                    <input type="text" id="facture-numero" disabled class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-900">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Montant à Payer</label>
                    <input type="text" id="montant-max" disabled class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50 font-bold text-lg text-red-600">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Montant Versé *</label>
                    <input type="number" name="montant_verse" id="montant_verse" step="0.01" class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200" required>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Mode de Paiement *</label>
                    <select name="mode_paiement" class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200" required>
                        <option value="">-- Sélectionner --</option>
                        <option value="Espèces">Espèces</option>
                        <option value="Chèque">Chèque</option>
                        <option value="Virement">Virement Bancaire</option>
                        <option value="Mobile Money">Mobile Money</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Date du Paiement *</label>
                    <input type="date" name="date_paiement" value="{{ date('Y-m-d') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200" required>
                </div>

                <div class="flex gap-3 justify-end pt-4">
                    <button type="button" onclick="closePaymentModal()" class="px-6 py-3 text-gray-700 bg-gray-200 hover:bg-gray-300 rounded-lg font-semibold transition duration-200">
                        Annuler
                    </button>
                    <button type="submit" class="px-6 py-3 text-white bg-blue-600 hover:bg-blue-700 rounded-lg font-semibold transition duration-200">
                        Payer Maintenant
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openPaymentModal(factureId, factureNumero, montantDu) {
            document.getElementById('facture-numero').value = factureNumero;
            document.getElementById('montant-max').value = montantDu.toLocaleString('fr-FR') + ' {{ Auth::user()->agence->devise }}';
            document.getElementById('montant_verse').max = montantDu;
            document.getElementById('payment-form').action = '/factures/' + factureId + '/payer';
            document.getElementById('modal-paiement').classList.remove('hidden');
        }

        function closePaymentModal() {
            document.getElementById('modal-paiement').classList.add('hidden');
        }
    </script>
</x-app-layout>
