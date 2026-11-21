<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-black text-2xl md:text-3xl text-slate-900 tracking-tight uppercase">{{ __('Mes Factures') }}</h2>
            <p class="text-slate-500 text-sm mt-1">Gestion de vos loyers et paiements</p>
        </div>
    </x-slot>

    <div class="space-y-4 md:space-y-6">
        @if (session('success'))
        <div class="premium-card bg-green-50 border-l-4 border-green-500 p-4 flex items-center gap-3">
            <i class="fas fa-check-circle text-green-600 text-lg shrink-0"></i>
            <span class="text-green-700 font-bold text-sm">{{ session('success') }}</span>
        </div>
        @endif

        @if($factures->count() > 0)
        <div class="premium-card overflow-hidden">

            {{-- VUE MOBILE : cards --}}
            <div class="md:hidden divide-y divide-slate-200">
                @foreach($factures as $facture)
                <div class="p-4 space-y-3">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <span class="text-sm font-black text-blue-600">#{{ $facture->numero_facture }}</span>
                            <p class="text-xs font-bold text-slate-800 mt-0.5">{{ $facture->contrat->bien->nom_bien }}</p>
                            <p class="text-[10px] text-slate-400 uppercase">{{ $facture->mois_facture }}</p>
                        </div>
                        <span class="px-2 py-1 shrink-0 text-[10px] font-black rounded-lg uppercase inline-flex
                            @if($facture->statut === 'Payée') bg-green-100 text-green-700
                            @elseif($facture->statut === 'Partielle') bg-yellow-100 text-yellow-700
                            @else bg-red-100 text-red-700 @endif">
                            {{ $facture->statut }}
                        </span>
                    </div>
                    <div class="grid grid-cols-3 gap-2 text-xs">
                        <div class="bg-slate-50 rounded-lg p-2 text-center">
                            <p class="text-[10px] text-slate-400 uppercase font-black">Dû</p>
                            <p class="font-black text-slate-900">{{ number_format($facture->montant_total_du, 0, ',', ' ') }}</p>
                        </div>
                        <div class="bg-slate-50 rounded-lg p-2 text-center">
                            <p class="text-[10px] text-slate-400 uppercase font-black">Payé</p>
                            <p class="font-black text-green-600">{{ number_format($facture->montant_deja_paye, 0, ',', ' ') }}</p>
                        </div>
                        <div class="bg-slate-50 rounded-lg p-2 text-center">
                            <p class="text-[10px] text-slate-400 uppercase font-black">Reste</p>
                            <p class="font-black {{ ($facture->montant_total_du - $facture->montant_deja_paye) > 0 ? 'text-red-600' : 'text-green-600' }}">
                                {{ number_format($facture->montant_total_du - $facture->montant_deja_paye, 0, ',', ' ') }}
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center justify-between text-xs text-slate-500">
                        <span>Échéance : {{ \Carbon\Carbon::parse($facture->date_echeance)->format('d/m/Y') }}</span>
                        <span>{{ Auth::user()->agence->devise }}</span>
                    </div>
                    @if($facture->statut !== 'Payée')
                    <button onclick="openPaymentModal({{ $facture->id }}, '{{ $facture->numero_facture }}', {{ $facture->montant_total_du - $facture->montant_deja_paye }})"
                        class="w-full h-10 rounded-xl bg-blue-600 text-white text-xs font-black uppercase tracking-widest hover:bg-blue-700 transition-all">
                        Payer maintenant
                    </button>
                    @endif
                </div>
                @endforeach
            </div>

            {{-- VUE DESKTOP : tableau --}}
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gradient-to-r from-blue-600 to-blue-700 text-white ">
                            <th class="text-left text-sm font-semibold whitespace-nowrap px-6 py-5">N° Facture</th>
                            <th class="text-left text-sm font-semibold whitespace-nowrap px-6 py-5">Bien</th>
                            <th class="text-left text-sm font-semibold whitespace-nowrap px-6 py-5">Mois</th>
                            <th class="text-left text-sm font-semibold whitespace-nowrap px-6 py-5">Montant Dû</th>
                            <th class="text-left text-sm font-semibold whitespace-nowrap px-6 py-5">Payé</th>
                            <th class="text-left text-sm font-semibold whitespace-nowrap px-6 py-5">Reste</th>
                            <th class="text-left text-sm font-semibold whitespace-nowrap px-6 py-5">Échéance</th>
                            <th class="text-left text-sm font-semibold whitespace-nowrap px-6 py-5">Statut</th>
                            <th class="text-right text-sm font-semibold whitespace-nowrap px-6 py-5">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($factures as $facture)
                        <tr class="hover:bg-blue-50 transition duration-150 ">
                            <td class="whitespace-nowrap font-bold text-blue-600 whitespace-nowrap px-6 py-5">{{ $facture->numero_facture }}</td>
                            <td class="whitespace-nowrap px-6 py-5">
                                <div class="font-semibold text-gray-900">{{ $facture->contrat->bien->nom_bien }}</div>
                                <div class="text-xs text-gray-500">{{ $facture->contrat->bien->type_bien }}</div>
                            </td>
                            <td class="whitespace-nowrap text-sm text-gray-900 whitespace-nowrap px-6 py-5">{{ $facture->mois_facture }}</td>
                            <td class="whitespace-nowrap font-bold text-gray-900 whitespace-nowrap px-6 py-5">
                                {{ number_format($facture->montant_total_du, 0, ',', ' ') }} {{ Auth::user()->agence->devise }}
                            </td>
                            <td class="whitespace-nowrap font-semibold text-green-600 whitespace-nowrap px-6 py-5">
                                {{ number_format($facture->montant_deja_paye, 0, ',', ' ') }}
                            </td>
                            <td class="whitespace-nowrap font-bold
                                @if($facture->montant_total_du - $facture->montant_deja_paye > 0) text-red-600 @else text-green-600 @endif whitespace-nowrap px-6 py-5">
                                {{ number_format($facture->montant_total_du - $facture->montant_deja_paye, 0, ',', ' ') }}
                            </td>
                            <td class="whitespace-nowrap text-sm text-gray-900 whitespace-nowrap px-6 py-5">
                                {{ \Carbon\Carbon::parse($facture->date_echeance)->format('d/m/Y') }}
                            </td>
                            <td class="whitespace-nowrap px-6 py-5">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold
                                    @if($facture->statut === 'Payée') bg-green-100 text-green-800
                                    @elseif($facture->statut === 'Partielle') bg-yellow-100 text-yellow-800
                                    @else bg-red-100 text-red-800 @endif">
                                    {{ $facture->statut }}
                                </span>
                            </td>
                            <td class="whitespace-nowrap text-right whitespace-nowrap px-6 py-5">
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

            <div class="px-4 md:px-6 py-4 border-t border-slate-100 bg-slate-50">
                {{ $factures->links() }}
            </div>
        </div>
        @else
        <div class="premium-card p-12 flex flex-col items-center justify-center text-center">
            <div class="w-16 h-16 rounded-2xl bg-blue-50 flex items-center justify-center text-blue-200 mb-4">
                <i class="fas fa-file-invoice-dollar text-3xl"></i>
            </div>
            <p class="text-lg font-black text-slate-600">Aucune facture enregistrée</p>
            <p class="text-slate-400 text-sm mt-2">Vos factures apparaîtront ici une fois créées</p>
        </div>
        @endif
    </div>

    <!-- Modal Paiement -->
    <div id="modal-paiement" class="hidden fixed inset-0 bg-black bg-opacity-50 overflow-y-auto h-full w-full z-50 flex items-end sm:items-center justify-center p-4">
        <div class="relative bg-white w-full max-w-md rounded-2xl shadow-2xl p-6 md:p-8">
            <div class="flex justify-between items-center mb-5">
                <h3 class="text-xl font-bold text-gray-900">Effectuer un Paiement</h3>
                <button onclick="closePaymentModal()" class="w-9 h-9 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 hover:bg-slate-200 text-lg font-bold">&times;</button>
            </div>

            <form id="payment-form" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Facture</label>
                    <input type="text" id="facture-numero" disabled class="w-full px-4 py-2.5 border border-gray-300 rounded-xl bg-gray-50 text-gray-900 font-bold">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Montant à Payer</label>
                    <input type="text" id="montant-max" disabled class="w-full px-4 py-2.5 border border-gray-300 rounded-xl bg-gray-50 font-black text-lg text-red-600">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-700 mb-1.5">Montant Versé *</label>
                    <input type="number" name="montant_verse" id="montant_verse" step="0.01" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-200" required>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">Mode de Paiement *</label>
                        <select name="mode_paiement" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl shadow-sm focus:border-blue-500" required>
                            <option value="">-- Sélectionner --</option>
                            <option value="Espèces">Espèces</option>
                            <option value="Chèque">Chèque</option>
                            <option value="Virement">Virement Bancaire</option>
                            <option value="Mobile Money">Mobile Money</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">Date du Paiement *</label>
                        <input type="date" name="date_paiement" value="{{ date('Y-m-d') }}" class="w-full px-4 py-2.5 border border-gray-300 rounded-xl shadow-sm focus:border-blue-500" required>
                    </div>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="button" onclick="closePaymentModal()" class="flex-1 px-4 py-3 text-gray-700 bg-gray-200 hover:bg-gray-300 rounded-xl font-semibold transition duration-200">
                        Annuler
                    </button>
                    <button type="submit" class="flex-1 px-4 py-3 text-white bg-blue-600 hover:bg-blue-700 rounded-xl font-semibold transition duration-200">
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
