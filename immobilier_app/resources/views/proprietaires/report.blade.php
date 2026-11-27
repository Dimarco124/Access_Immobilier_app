<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 w-full">
            <div class="flex flex-col gap-2">
                <h2 class="font-black text-2xl text-slate-900 uppercase tracking-tight">
                    Rapport Financier
                </h2>
                <p class="text-[11px] text-slate-500 font-bold uppercase tracking-widest">
                    Suivi exclusif de vos loyers, déductions et reversements • {{ $moisFormat }}
                </p>
                <div class="mt-1">
                    <a href="{{ route('dashboard') }}" class="text-xs font-black text-indigo-600 hover:text-indigo-800 uppercase tracking-widest inline-flex items-center gap-2">
                        <i class="fas fa-arrow-left"></i> Retour au tableau de bord
                    </a>
                </div>
            </div>
            <div class="w-full md:w-auto">
                <x-month-picker
                    :value="$moisInputValue"
                    :label="$moisFormat"
                    :action="route('owner.finance-report')"
                    :resetUrl="route('owner.finance-report')"
                />
            </div>
        </div>
    </x-slot>

    @php $devise = Auth::user()->agence->devise ?? 'XOF'; @endphp

    <div class="space-y-6">
        <!-- KPI Cards (Uniquement les montants qui concernent le propriétaire) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            <div class="premium-card p-5 bg-white overflow-hidden relative group">
                <div class="absolute -top-4 -right-4 w-24 h-24 bg-gradient-to-br from-slate-100 to-slate-50 rounded-full opacity-60 group-hover:scale-125 transition-transform duration-500"></div>
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-3">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Loyers Bruts</p>
                        <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-slate-500 to-slate-600 flex items-center justify-center shadow-lg shadow-slate-200">
                            <i class="fas fa-coins text-white text-sm"></i>
                        </div>
                    </div>
                    <h3 class="text-2xl font-black text-slate-900 mb-1">
                        {{ number_format($totals['brut'] ?? 0, 0, ',', ' ') }}
                        <span class="text-xs font-bold text-slate-400 ml-1">{{ $devise }}</span>
                    </h3>
                    <p class="text-[10px] text-slate-400 font-semibold">Part loyers encaissée</p>
                </div>
            </div>

            <div class="premium-card p-5 bg-white overflow-hidden relative group">
                <div class="absolute -top-4 -right-4 w-24 h-24 bg-gradient-to-br from-amber-100 to-amber-50 rounded-full opacity-60 group-hover:scale-125 transition-transform duration-500"></div>
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-3">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Commissions & Charges</p>
                        <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-amber-500 to-amber-600 flex items-center justify-center shadow-lg shadow-amber-200">
                            <i class="fas fa-percent text-white text-sm"></i>
                        </div>
                    </div>
                    <h3 class="text-2xl font-black text-slate-900 mb-1">
                        {{ number_format(($totals['commission'] ?? 0) + ($totals['depenses'] ?? 0), 0, ',', ' ') }}
                        <span class="text-xs font-bold text-slate-400 ml-1">{{ $devise }}</span>
                    </h3>
                    <p class="text-[10px] text-slate-400 font-semibold">
                        Comm : {{ number_format($totals['commission'] ?? 0, 0, ',', ' ') }} • Dép : {{ number_format($totals['depenses'] ?? 0, 0, ',', ' ') }}
                    </p>
                </div>
            </div>

            <div class="premium-card p-5 bg-white overflow-hidden relative group">
                <div class="absolute -top-4 -right-4 w-24 h-24 bg-gradient-to-br from-emerald-100 to-emerald-50 rounded-full opacity-60 group-hover:scale-125 transition-transform duration-500"></div>
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-3">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Mon Revenu Net</p>
                        <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-600 flex items-center justify-center shadow-lg shadow-emerald-200">
                            <i class="fas fa-check-circle text-white text-sm"></i>
                        </div>
                    </div>
                    <h3 class="text-2xl font-black text-emerald-600 mb-1">
                        {{ number_format($totals['net'] ?? 0, 0, ',', ' ') }}
                        <span class="text-xs font-bold text-slate-400 ml-1">{{ $devise }}</span>
                    </h3>
                    <p class="text-[10px] text-slate-400 font-semibold">
                        Déjà reversé : {{ number_format($totals['reverse'] ?? 0, 0, ',', ' ') }} {{ $devise }}
                    </p>
                </div>
            </div>

            <div class="premium-card p-5 bg-white overflow-hidden relative group">
                <div class="absolute -top-4 -right-4 w-24 h-24 bg-gradient-to-br from-indigo-100 to-indigo-50 rounded-full opacity-60 group-hover:scale-125 transition-transform duration-500"></div>
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-3">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Reste à Percevoir</p>
                        <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-indigo-500 to-indigo-600 flex items-center justify-center shadow-lg shadow-indigo-200">
                            <i class="fas fa-wallet text-white text-sm"></i>
                        </div>
                    </div>
                    <h3 class="text-2xl font-black text-indigo-600 mb-1">
                        {{ number_format($totals['solde_a_recevoir'] ?? 0, 0, ',', ' ') }}
                        <span class="text-xs font-bold text-slate-400 ml-1">{{ $devise }}</span>
                    </h3>
                    <p class="text-[10px] text-slate-400 font-semibold">Solde à vous reverser</p>
                </div>
            </div>
        </div>

        <!-- Tabs Navigation -->
        <div class="premium-card bg-white p-1.5 inline-flex gap-1.5 rounded-2xl shadow-sm border border-slate-100">
            <button id="tab-biens" class="tab-btn-smooth px-5 py-2.5 text-[11px] font-black uppercase tracking-wider rounded-xl transition-all bg-indigo-600 text-white shadow-sm">
                <i class="fas fa-city mr-2"></i> Mon Patrimoine
            </button>
            <button id="tab-paiements" class="tab-btn-smooth px-5 py-2.5 text-[11px] font-black uppercase tracking-wider rounded-xl transition-all text-slate-600 hover:text-slate-900">
                <i class="fas fa-money-bill mr-2"></i> Loyers Perçus
            </button>
            <button id="tab-reversements" class="tab-btn-smooth px-5 py-2.5 text-[11px] font-black uppercase tracking-wider rounded-xl transition-all text-slate-600 hover:text-slate-900">
                <i class="fas fa-exchange-alt mr-2"></i> Reversements
            </button>
        </div>

        <!-- Tab: Mes Biens -->
        <div id="tab-content-biens" class="tab-content tab-content-pane active-tab-pane premium-card bg-white p-6 rounded-2xl">
            <h3 class="font-black text-lg text-slate-900 uppercase tracking-[0.1em] flex items-center gap-2 mb-6">
                <i class="fas fa-city text-indigo-500"></i> Mon Patrimoine Immobilier
            </h3>
            @php
                $proprietaire = Auth::user()->proprietaire;
                $biens = $proprietaire ? $proprietaire->biens()->latest()->get() : collect();
            @endphp
            @if($biens->isEmpty())
                <div class="text-center py-12 text-slate-400">
                    <i class="fas fa-inbox text-4xl mb-3"></i>
                    <p class="text-sm font-medium">Aucune unité locative enregistrée</p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    @foreach($biens as $bien)
                        <div class="border border-slate-100 rounded-xl p-5 hover:border-slate-200 hover:shadow-sm transition-all">
                            <div class="flex items-center justify-between mb-3">
                                <h4 class="font-black text-slate-900">{{ $bien->nom_bien }}</h4>
                                @if($bien->statut == 'Loué')
                                    <span class="px-3 py-1 text-[10px] font-black text-emerald-600 bg-emerald-50 rounded-full uppercase">Loué</span>
                                @elseif($bien->statut == 'Disponible')
                                    <span class="px-3 py-1 text-[10px] font-black text-slate-600 bg-slate-50 rounded-full uppercase">Disponible</span>
                                @else
                                    <span class="px-3 py-1 text-[10px] font-black text-amber-600 bg-amber-50 rounded-full uppercase">{{ $bien->statut }}</span>
                                @endif
                            </div>
                            <p class="text-[10px] text-slate-500 mb-3">{{ $bien->adresse }}</p>
                            <div class="grid grid-cols-2 gap-3 text-center">
                                <div class="p-3 bg-slate-50 rounded-lg">
                                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Type</p>
                                    <p class="text-sm font-semibold text-slate-700">{{ $bien->type_bien }}</p>
                                </div>
                                <div class="p-3 bg-slate-50 rounded-lg">
                                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Loyer Mensuel</p>
                                    <p class="text-sm font-black text-indigo-600">{{ number_format($bien->loyer_mensuel, 0, ',', ' ') }} {{ $devise }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Tab: Loyers Perçus -->
        <div id="tab-content-paiements" class="tab-content tab-content-pane premium-card bg-white p-6 rounded-2xl hidden">
            <div class="flex items-center justify-between mb-5 pb-3 border-b border-slate-100">
                <div>
                    <h3 class="font-black text-lg text-slate-900 uppercase tracking-[0.1em] flex items-center gap-2">
                        <i class="fas fa-history text-indigo-500"></i> Historique des Loyers par Bien
                    </h3>
                    <p class="text-[10px] text-slate-400 font-medium mt-1">Uniquement la part loyer (hors cautions et frais de dossier de l'agence)</p>
                </div>
            </div>
            <div class="space-y-6">
                @foreach($paiementsGroupes as $contratId => $groupe)
                    @php
                        $contrat = $groupe->first()->facture->contrat;
                    @endphp
                    <div class="border border-slate-100 rounded-xl p-5 hover:border-slate-200 hover:shadow-sm transition-all">
                        <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-50">
                            <div class="flex items-center gap-4">
                                <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-indigo-500 to-indigo-600 flex items-center justify-center text-white">
                                    <i class="fas fa-building"></i>
                                </div>
                                <div>
                                    <h4 class="font-black text-slate-900">{{ $contrat->bien->nom_bien }}</h4>
                                    <p class="text-[10px] text-slate-500">Locataire : {{ $contrat->locataire->user->name }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-[10px] font-bold uppercase text-slate-400">Net Propriétaire</p>
                                <p class="text-lg font-black text-emerald-600">{{ number_format($groupe->sum('montant_net_proprietaire'), 0, ',', ' ') }} {{ $devise }}</p>
                            </div>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead>
                                    <tr class="text-left text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-100">
                                        <th class="whitespace-nowrap px-6 py-4">Date Paiement</th>
                                        <th class="whitespace-nowrap px-6 py-4">Type</th>
                                        <th class="whitespace-nowrap px-6 py-4">Mois concerné</th>
                                        <th class="text-right whitespace-nowrap px-6 py-4">Loyer Brut</th>
                                        <th class="text-right whitespace-nowrap px-6 py-4">Commission</th>
                                        <th class="text-right whitespace-nowrap px-6 py-4">Votre Net</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-200">
                                    @foreach($groupe as $paiement)
                                        <tr class="group hover:bg-slate-50 transition-colors">
                                            <td class="whitespace-nowrap px-6 py-4">
                                                <p class="text-sm font-semibold text-slate-700">{{ $paiement->date_paiement->translatedFormat('d/m/Y') }}</p>
                                            </td>
                                            <td class="whitespace-nowrap px-6 py-4">
                                                <p class="text-sm text-slate-600">
                                                    {{ $paiement->facture->type_facture === 'Initial' ? 'Loyer / Avance initiale' : $paiement->facture->type_facture }}
                                                </p>
                                            </td>
                                            <td class="whitespace-nowrap px-6 py-4">
                                                <p class="text-sm text-slate-600">{{ $paiement->facture->mois_facture }}</p>
                                            </td>
                                            <td class="text-right whitespace-nowrap px-6 py-4">
                                                <p class="text-sm font-black text-slate-700">{{ number_format($paiement->montant_loyer_brut, 0, ',', ' ') }}</p>
                                            </td>
                                            <td class="text-right whitespace-nowrap px-6 py-4">
                                                <p class="text-sm font-medium text-amber-600">-{{ number_format($paiement->commission_agence, 0, ',', ' ') }}</p>
                                            </td>
                                            <td class="text-right whitespace-nowrap px-6 py-4">
                                                <p class="text-sm font-bold text-emerald-600">{{ number_format($paiement->montant_net_proprietaire, 0, ',', ' ') }}</p>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endforeach
                @if($paiementsGroupes->isEmpty())
                    <div class="text-center py-12 text-slate-400">
                        <i class="fas fa-inbox text-4xl mb-3"></i>
                        <p class="text-sm font-medium">Aucun loyer encaissé enregistré</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Tab: Reversements -->
        <div id="tab-content-reversements" class="tab-content tab-content-pane premium-card bg-white p-6 rounded-2xl hidden">
            <div class="flex items-center justify-between mb-5 pb-3 border-b border-slate-100">
                <div>
                    <h3 class="font-black text-lg text-slate-900 uppercase tracking-[0.1em] flex items-center gap-2">
                        <i class="fas fa-hand-holding-usd text-indigo-500"></i> Historique des Reversements Reçus
                    </h3>
                </div>
            </div>
            @if($reversements->isEmpty())
                <div class="text-center py-12 text-slate-400">
                    <i class="fas fa-inbox text-4xl mb-3"></i>
                    <p class="text-sm font-medium">Aucun reversement enregistré pour le moment</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="text-left text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-100">
                                <th class="whitespace-nowrap px-6 py-4">Date</th>
                                <th class="whitespace-nowrap px-6 py-4">Période</th>
                                <th class="whitespace-nowrap px-6 py-4">Référence</th>
                                <th class="whitespace-nowrap px-6 py-4">Notes</th>
                                <th class="text-right whitespace-nowrap px-6 py-4">Montant Net Reçu</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            @foreach($reversements as $reversement)
                                <tr class="group hover:bg-slate-50 transition-colors">
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <p class="text-sm font-semibold text-slate-700">{{ $reversement->date_reversement->translatedFormat('d/m/Y') }}</p>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <p class="text-sm text-slate-600">{{ $reversement->periode }}</p>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <p class="text-sm text-slate-600">{{ $reversement->reference_transaction ?? '-' }}</p>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <p class="text-sm text-slate-600">{{ $reversement->notes ?? '-' }}</p>
                                    </td>
                                    <td class="text-right whitespace-nowrap px-6 py-4">
                                        <p class="text-sm font-black text-emerald-600">{{ number_format($reversement->montant_net_verse, 0, ',', ' ') }} {{ $devise }}</p>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tabs = ['biens', 'paiements', 'reversements'];

            tabs.forEach(tabName => {
                const btn = document.getElementById(`tab-${tabName}`);
                if (!btn) return;
                btn.addEventListener('click', function() {
                    tabs.forEach(t => {
                        const otherBtn = document.getElementById(`tab-${t}`);
                        const otherPane = document.getElementById(`tab-content-${t}`);
                        if (otherBtn) {
                            otherBtn.classList.remove('bg-indigo-600', 'text-white', 'shadow-sm');
                            otherBtn.classList.add('text-slate-600', 'hover:text-slate-900');
                        }
                        if (otherPane) {
                            otherPane.classList.remove('active-tab-pane');
                            otherPane.classList.add('hidden');
                        }
                    });
                    this.classList.remove('text-slate-600', 'hover:text-slate-900');
                    this.classList.add('bg-indigo-600', 'text-white', 'shadow-sm');
                    const targetPane = document.getElementById(`tab-content-${tabName}`);
                    if (targetPane) {
                        targetPane.classList.remove('hidden');
                        targetPane.classList.remove('active-tab-pane');
                        void targetPane.offsetWidth;
                        targetPane.classList.add('active-tab-pane');
                    }
                });
            });
        });
    </script>
</x-app-layout>
