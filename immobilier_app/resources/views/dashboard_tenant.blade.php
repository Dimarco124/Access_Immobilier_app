<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 w-full">
            <h2 class="font-black text-2xl text-slate-900 tracking-tight uppercase">
                {{ __('Mon Espace Locataire') }}
            </h2>
            <p class="text-[11px] text-slate-500 font-bold uppercase tracking-widest mt-1">Bienvenue, {{ Auth::user()->name }} ! Gérez votre location en quelques clics</p>
        </div>
    </x-slot>

    <div class="space-y-6">
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 md:gap-6">
            <div class="premium-card p-6 bg-white border-l-4 border-l-blue-600">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-slate-400 text-[9px] uppercase font-black tracking-widest mb-1">Contrats Actifs</p>
                        <p class="text-3xl font-black text-slate-900">{{ $stats['contrats_actifs'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center text-blue-600">
                        <i class="fas fa-file-contract text-xl"></i>
                    </div>
                </div>
            </div>
            <div class="premium-card p-6 bg-white border-l-4 border-l-red-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-red-500 text-[9px] uppercase font-black tracking-widest mb-1">Factures à Régler</p>
                        <p class="text-3xl font-black text-red-600">{{ $stats['factures_a_payer'] }}</p>
                    </div>
                    <div class="w-12 h-12 bg-red-100 rounded-xl flex items-center justify-center text-red-600">
                        <i class="fas fa-receipt text-xl"></i>
                    </div>
                </div>
            </div>
            <div class="premium-card p-6 bg-white border-l-4 border-l-emerald-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-emerald-500 text-[9px] uppercase font-black tracking-widest mb-1">Total Payé</p>
                        <p class="text-2xl font-black text-emerald-600">{{ number_format($stats['total_paye'], 0, ',', ' ') }} <span class="text-xs">{{ Auth::user()->agence->devise }}</span></p>
                    </div>
                    <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center text-emerald-600">
                        <i class="fas fa-wallet text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Colonne 1 & 2 -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Mon Logement -->
                @if($contratActif)
                <div class="premium-card bg-white overflow-hidden">
                    <div class="p-6 border-b border-slate-100 flex items-center gap-3 bg-slate-50/30">
                        <div class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center text-white">
                            <i class="fas fa-home"></i>
                        </div>
                        <h3 class="text-sm font-black text-slate-900 uppercase tracking-tight">Mon Logement Actuel</h3>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 md:gap-6">
                            <div>
                                <p class="text-slate-400 text-[9px] uppercase font-black tracking-widest mb-1">Bien Immobilier</p>
                                <p class="text-lg md:text-xl font-black text-slate-900">{{ $contratActif->bien->nom_bien }}</p>
                                <p class="text-sm text-slate-500 mt-1"><i class="fas fa-map-marker-alt mr-1"></i> {{ $contratActif->bien->adresse }}</p>
                            </div>
                            <div class="sm:text-right">
                                <p class="text-slate-400 text-[9px] uppercase font-black tracking-widest mb-1">Loyer Mensuel</p>
                                <p class="text-2xl md:text-3xl font-black text-slate-900">{{ number_format($contratActif->montant_loyer_actuel, 0, ',', ' ') }} {{ Auth::user()->agence->devise }}</p>
                                <p class="text-sm text-slate-500 mt-1">
                                    <i class="fas fa-calendar mr-1"></i>
                                    Depuis {{ \Carbon\Carbon::parse($contratActif->date_debut)->format('d/m/Y') }}
                                </p>
                            </div>
                        </div>
                        <div class="flex flex-col sm:flex-row gap-3 mt-5">
                            <a href="{{ route('contrats.show', $contratActif) }}" class="flex-1 sm:flex-none px-5 py-2.5 bg-blue-600 text-white text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-blue-700 transition-all text-center">
                                <i class="fas fa-eye mr-1"></i> Voir le contrat
                            </a>
                            <a href="{{ route('factures.mes-factures') }}" class="flex-1 sm:flex-none px-5 py-2.5 bg-slate-100 text-slate-700 text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-slate-200 transition-all text-center">
                                <i class="fas fa-file-invoice-dollar mr-1"></i> Mes factures
                            </a>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Prochain Paiement -->
                @if($prochaineFacture)
                <div class="premium-card bg-white p-6">
                    <div class="flex items-center gap-3 mb-6 pb-4 border-b border-slate-100">
                        <div class="w-10 h-10 bg-red-100 rounded-xl flex items-center justify-center text-red-600">
                            <i class="fas fa-bell"></i>
                        </div>
                        <div>
                            <h3 class="text-sm font-black text-slate-900 uppercase tracking-tight">Prochain Paiement</h3>
                            <p class="text-xs text-slate-500">Échéance : {{ \Carbon\Carbon::parse($prochaineFacture->date_echeance)->format('d/m/Y') }}</p>
                        </div>
                    </div>
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                        <div>
                            <p class="text-xs text-slate-500 font-bold uppercase tracking-wider">{{ $prochaineFacture->mois_facture }}</p>
                            <p class="text-2xl font-black text-slate-900">{{ number_format($prochaineFacture->montant_total_du - $prochaineFacture->montant_deja_paye, 0, ',', ' ') }} {{ Auth::user()->agence->devise }}</p>
                        </div>
                        <button onclick="openPaymentModal({{ $prochaineFacture->id }}, '{{ $prochaineFacture->numero_facture }}', {{ $prochaineFacture->montant_total_du - $prochaineFacture->montant_deja_paye }})" class="w-full sm:w-auto px-6 py-3 bg-red-600 hover:bg-red-700 text-white text-[10px] font-black uppercase tracking-widest rounded-xl transition-all shadow-lg shadow-red-200">
                            <i class="fas fa-credit-card mr-1"></i> Payer maintenant
                        </button>
                    </div>
                </div>
                @endif

                <!-- Derniers Paiements -->
                <div class="premium-card bg-white overflow-hidden">
                    <div class="p-6 border-b border-slate-100 flex items-center gap-3">
                        <div class="w-10 h-10 bg-emerald-100 rounded-xl flex items-center justify-center text-emerald-600">
                            <i class="fas fa-history"></i>
                        </div>
                        <h3 class="text-sm font-black text-slate-900 uppercase tracking-tight">Derniers Paiements</h3>
                    </div>
                    <div class="p-6">
                        @if($derniersPaiements->count() > 0)
                        <div class="space-y-4">
                            @foreach($derniersPaiements as $paiement)
                            <div class="flex items-center justify-between p-4 bg-slate-50 rounded-xl hover:bg-slate-100 transition-all">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center text-emerald-600">
                                        <i class="fas fa-check"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-black text-slate-900">{{ $paiement->facture->mois_facture }}</p>
                                        <p class="text-xs text-slate-500">{{ $paiement->date_paiement->format('d/m/Y') }} • {{ $paiement->mode_paiement }}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-lg font-black text-emerald-600">{{ number_format($paiement->montant_verse, 0, ',', ' ') }} {{ Auth::user()->agence->devise }}</p>
                                    <a href="{{ route('factures.recu', $paiement->facture) }}" class="text-xs text-blue-600 hover:text-blue-700 font-bold"><i class="fas fa-download mr-1"></i> Reçu</a>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <div class="text-center py-8">
                            <i class="fas fa-receipt text-4xl text-slate-200 mb-3"></i>
                            <p class="text-sm text-slate-400 font-bold">Aucun paiement enregistré</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Colonne 3 -->
            <div class="space-y-6">
                <!-- Actions Rapides -->
                <div class="premium-card bg-white p-6">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 bg-slate-100 rounded-xl flex items-center justify-center text-slate-600">
                            <i class="fas fa-bolt"></i>
                        </div>
                        <h3 class="text-sm font-black text-slate-900 uppercase tracking-tight">Actions Rapides</h3>
                    </div>
                    <div class="space-y-3">
                        <a href="{{ route('messages.index') }}" class="flex items-center gap-3 p-4 bg-blue-50 text-blue-700 rounded-xl hover:bg-blue-100 transition-all group">
                            <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center text-white group-hover:scale-110 transition-transform">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div>
                                <p class="text-sm font-black">Contacter l'agence</p>
                                <p class="text-xs text-blue-600">Envoyer un message</p>
                            </div>
                        </a>
                        <a href="{{ route('contrats.mes-contrats') }}" class="flex items-center gap-3 p-4 bg-slate-50 text-slate-700 rounded-xl hover:bg-slate-100 transition-all group">
                            <div class="w-10 h-10 bg-slate-500 rounded-lg flex items-center justify-center text-white group-hover:scale-110 transition-transform">
                                <i class="fas fa-file-alt"></i>
                            </div>
                            <div>
                                <p class="text-sm font-black">Mes documents</p>
                                <p class="text-xs text-slate-600">Contrats, quittances</p>
                            </div>
                        </a>
                    </div>
                </div>

                <!-- Contact Agence -->
                <div class="premium-card p-6 bg-slate-50">
                    <p class="text-[8px] font-black uppercase tracking-widest text-slate-400 mb-2">AGENCE</p>
                    <p class="text-sm font-bold text-blue-600 mb-3">{{ optional(Auth::user()->agence)->nom ?? 'Agence' }}</p>
                    <div class="space-y-2">
                        @if(optional(Auth::user()->agence)->telephone)
                        <div class="flex items-center gap-2">
                            <div class="w-6 h-6 bg-blue-100 rounded-lg flex items-center justify-center text-blue-600">
                                <i class="fas fa-phone text-xs"></i>
                            </div>
                            <span class="text-xs text-slate-600 font-medium">{{ Auth::user()->agence->telephone }}</span>
                        </div>
                        @endif
                        @if(optional(Auth::user()->agence)->email)
                        <div class="flex items-center gap-2">
                            <div class="w-6 h-6 bg-blue-100 rounded-lg flex items-center justify-center text-blue-600">
                                <i class="fas fa-envelope text-xs"></i>
                            </div>
                            <span class="text-xs text-slate-600 font-medium">{{ Auth::user()->agence->email }}</span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Paiement (copié depuis mes-factures) -->
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
            document.getElementById('montant_verse').value = montantDu;
            document.getElementById('payment-form').action = '/factures/' + factureId + '/payer';
            document.getElementById('modal-paiement').classList.remove('hidden');
        }

        function closePaymentModal() {
            document.getElementById('modal-paiement').classList.add('hidden');
        }
    </script>
</x-app-layout>