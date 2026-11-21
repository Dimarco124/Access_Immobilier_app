<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 w-full">
            <div class="flex  flex-col items-start gap-4 w-full">
                <h2 class="font-black text-2xl text-slate-900 uppercase tracking-tight">
                    Rapport Financier
                </h2>
                
            </div>
            <p class="text-[11px] text-slate-500 font-bold uppercase tracking-widest">Suivi de vos paiements, factures et contrats</p>
        
            <div class="mt-1">
                <a href="{{ route('dashboard') }}" class="text-xs font-black text-indigo-600 hover:text-indigo-800 uppercase tracking-widest flex  items-start  gap-2 self-start inline-flex">
                    <i class="fas fa-arrow-left self-start inline-flex"></i> Retour au tableau de bord
                </a>
            </div>
        </div></x-slot>

    <div class="space-y-6">
        <!-- KPI Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
            <div class="premium-card p-5 bg-white overflow-hidden relative group">
                <div class="absolute -top-4 -right-4 w-24 h-24 bg-gradient-to-br from-emerald-100 to-emerald-50 rounded-full opacity-60 group-hover:scale-125 transition-transform duration-500"></div>
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-3">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Total Payé</p>
                        <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-600 flex items-center justify-center shadow-lg shadow-emerald-200">
                            <i class="fas fa-check-circle text-white text-sm"></i>
                        </div>
                    </div>
                    <h3 class="text-2xl font-black text-slate-900 mb-1">
                        {{ number_format($stats['total_paye'], 0, ',', ' ') }}
                        <span class="text-xs font-bold text-slate-400 ml-1">{{ Auth::user()->agence->devise }}</span>
                    </h3>
                </div>
            </div>
            <div class="premium-card p-5 bg-white overflow-hidden relative group">
                <div class="absolute -top-4 -right-4 w-24 h-24 bg-gradient-to-br from-slate-100 to-slate-50 rounded-full opacity-60 group-hover:scale-125 transition-transform duration-500"></div>
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-3">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Total Dû</p>
                        <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-slate-500 to-slate-600 flex items-center justify-center shadow-lg shadow-slate-200">
                            <i class="fas fa-file-invoice text-white text-sm"></i>
                        </div>
                    </div>
                    <h3 class="text-2xl font-black text-slate-900 mb-1">
                        {{ number_format($stats['total_du'], 0, ',', ' ') }}
                        <span class="text-xs font-bold text-slate-400 ml-1">{{ Auth::user()->agence->devise }}</span>
                    </h3>
                </div>
            </div>
            <div class="premium-card p-5 bg-white overflow-hidden relative group">
                <div class="absolute -top-4 -right-4 w-24 h-24 bg-gradient-to-br from-amber-100 to-amber-50 rounded-full opacity-60 group-hover:scale-125 transition-transform duration-500"></div>
                <div class="relative z-10">
                    <div class="flex items-center justify-between mb-3">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Reste à Payer</p>
                        <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-amber-500 to-amber-600 flex items-center justify-center shadow-lg shadow-amber-200">
                            <i class="fas fa-clock text-white text-sm"></i>
                        </div>
                    </div>
                    <h3 class="text-2xl font-black text-slate-900 mb-1">
                        {{ number_format($stats['reste_a_payer'], 0, ',', ' ') }}
                        <span class="text-xs font-bold text-slate-400 ml-1">{{ Auth::user()->agence->devise }}</span>
                    </h3>
                </div>
            </div>
        </div>

        <!-- Tabs Navigation -->
        <div class="premium-card bg-white p-1.5 inline-flex gap-1.5 rounded-2xl shadow-sm border border-slate-100">
            <button id="tab-contrats" class="tab-btn-smooth px-5 py-2.5 text-[11px] font-black uppercase tracking-wider rounded-xl transition-all bg-indigo-600 text-white shadow-sm">
                <i class="fas fa-file-contract mr-2"></i> Mes Contrats
            </button>
            <button id="tab-factures" class="tab-btn-smooth px-5 py-2.5 text-[11px] font-black uppercase tracking-wider rounded-xl transition-all text-slate-600 hover:text-slate-900">
                <i class="fas fa-file-invoice mr-2"></i> Factures
            </button>
            <button id="tab-paiements" class="tab-btn-smooth px-5 py-2.5 text-[11px] font-black uppercase tracking-wider rounded-xl transition-all text-slate-600 hover:text-slate-900">
                <i class="fas fa-money-bill mr-2"></i> Paiements & Reçus
            </button>
        </div>

        <!-- Tab: Contrats -->
        <div id="tab-content-contrats" class="tab-content tab-content-pane active-tab-pane premium-card bg-white p-6 rounded-2xl">
            <h3 class="font-black text-lg text-slate-900 uppercase tracking-[0.1em] flex items-center gap-2 mb-6">
                <i class="fas fa-file-contract text-indigo-500"></i> Mes Contrats de Bail
            </h3>
            @php
                $locataire = Auth::user()->locataire;
                $contrats = $locataire ? $locataire->contrats()->latest()->get() : collect();
            @endphp
            @if($contrats->isEmpty())
                <div class="text-center py-12 text-slate-400">
                    <i class="fas fa-inbox text-4xl mb-3"></i>
                    <p class="text-sm font-medium">Aucun contrat enregistré</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="text-left text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-100 ">
                                <th class="whitespace-nowrap px-6 py-5">Contrat</th>
                                <th class="whitespace-nowrap px-6 py-5">Bien Immobilier</th>
                                <th class="whitespace-nowrap px-6 py-5">Période</th>
                                <th class="text-right whitespace-nowrap px-6 py-5">Loyer Mensuel</th>
                                <th class="text-center whitespace-nowrap px-6 py-5">Statut</th>
                                <th class="text-right whitespace-nowrap px-6 py-5">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            @foreach($contrats as $contrat)
                                <tr class="group hover:bg-slate-50 transition-colors ">
                                    <td class="whitespace-nowrap px-6 py-5">
                                        <p class="text-sm font-semibold text-slate-900">{{ $contrat->numero_contrat ?? 'CB-' . str_pad($contrat->id, 6, '0', STR_PAD_LEFT) }}</p>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-5">
                                        <p class="text-sm text-slate-700">{{ $contrat->bien->nom_bien }}</p>
                                        <p class="text-[10px] text-slate-500">{{ $contrat->bien->adresse }}</p>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-5">
                                        <p class="text-sm text-slate-700">
                                            Du {{ $contrat->date_debut ? $contrat->date_debut->translatedFormat('d/m/Y') : '-' }}
                                            @if($contrat->date_fin)
                                                <br>Au {{ $contrat->date_fin->translatedFormat('d/m/Y') }}
                                            @endif
                                        </p>
                                    </td>
                                    <td class="text-right whitespace-nowrap px-6 py-5">
                                        <p class="text-sm font-black text-slate-700">{{ number_format($contrat->loyer_mensuel, 0, ',', ' ') }} {{ Auth::user()->agence->devise }}</p>
                                    </td>
                                    <td class="text-center whitespace-nowrap px-6 py-5">
                                        @if($contrat->statut == 'Actif')
                                            <span class="px-3 py-1 text-[10px] font-black text-emerald-600 bg-emerald-50 rounded-full uppercase">Actif</span>
                                        @else
                                            <span class="px-3 py-1 text-[10px] font-black text-slate-600 bg-slate-50 rounded-full uppercase">{{ $contrat->statut }}</span>
                                        @endif
                                    </td>
                                    <td class="text-right whitespace-nowrap px-6 py-5">
                                        <a href="{{ route('contrats.show', $contrat) }}" class="inline-flex items-center gap-1 text-[10px] font-bold text-indigo-600 hover:text-indigo-800 uppercase mr-2">
                                            <i class="fas fa-eye"></i> Voir
                                        </a>
                                        <a href="{{ route('contrats.bail', $contrat) }}" target="_blank" class="inline-flex items-center gap-1 text-[10px] font-bold text-emerald-600 hover:text-emerald-800 uppercase">
                                            <i class="fas fa-download"></i> Bail
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <!-- Tab: Factures -->
        <div id="tab-content-factures" class="tab-content tab-content-pane premium-card bg-white p-6 rounded-2xl hidden">
            <div class="flex items-center justify-between mb-5 pb-3 border-b border-slate-100">
                <div>
                    <h3 class="font-black text-lg text-slate-900 uppercase tracking-[0.1em] flex items-center gap-2">
                        <i class="fas fa-file-invoice text-indigo-500"></i> Mes Factures
                    </h3>
                </div>
            </div>
            @if($factures->isEmpty())
                <div class="text-center py-12 text-slate-400">
                    <i class="fas fa-inbox text-4xl mb-3"></i>
                    <p class="text-sm font-medium">Aucune facture enregistrée</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="text-left text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-100 ">
                                <th class="whitespace-nowrap px-6 py-5">N° Facture</th>
                                <th class="whitespace-nowrap px-6 py-5">Type</th>
                                <th class="whitespace-nowrap px-6 py-5">Mois</th>
                                <th class="whitespace-nowrap px-6 py-5">Échéance</th>
                                <th class="text-right whitespace-nowrap px-6 py-5">Total Dû</th>
                                <th class="text-right whitespace-nowrap px-6 py-5">Payé</th>
                                <th class="text-right whitespace-nowrap px-6 py-5">Reste</th>
                                <th class="text-center whitespace-nowrap px-6 py-5">Statut</th>
                                <th class="text-right whitespace-nowrap px-6 py-5">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            @foreach($factures as $facture)
                                <tr class="group hover:bg-slate-50 transition-colors ">
                                    <td class="whitespace-nowrap px-6 py-5">
                                        <div class="text-sm font-semibold text-slate-900">{{ $facture->numero_facture }}</div>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-5">
                                        <p class="text-sm text-slate-700">{{ $facture->type_facture }}</p>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-5">
                                        <p class="text-sm text-slate-700">{{ $facture->mois_facture }}</p>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-5">
                                        <p class="text-sm text-slate-700">{{ $facture->date_echeance ? $facture->date_echeance->translatedFormat('d/m/Y') : '-' }}</p>
                                    </td>
                                    <td class="text-right whitespace-nowrap px-6 py-5">
                                        <p class="text-sm font-black text-slate-700">{{ number_format($facture->montant_total_du, 0, ',', ' ') }}</p>
                                    </td>
                                    <td class="text-right whitespace-nowrap px-6 py-5">
                                        <p class="text-sm font-medium text-emerald-600">{{ number_format($facture->montant_deja_paye, 0, ',', ' ') }}</p>
                                    </td>
                                    <td class="text-right whitespace-nowrap px-6 py-5">
                                        <p class="text-sm font-bold text-amber-600">{{ number_format($facture->montant_total_du - $facture->montant_deja_paye, 0, ',', ' ') }}</p>
                                    </td>
                                    <td class="text-center whitespace-nowrap px-6 py-5">
                                        @if($facture->statut == 'Payée')
                                            <span class="px-3 py-1 text-[10px] font-black text-emerald-600 bg-emerald-50 rounded-full uppercase">Payée</span>
                                        @elseif($facture->statut == 'Partielle')
                                            <span class="px-3 py-1 text-[10px] font-black text-amber-600 bg-amber-50 rounded-full uppercase">Partielle</span>
                                        @else
                                            <span class="px-3 py-1 text-[10px] font-black text-rose-600 bg-rose-50 rounded-full uppercase">En Retard</span>
                                        @endif
                                    </td>
                                    <td class="text-right whitespace-nowrap px-6 py-5">
                                        @if($facture->statut == 'Payée' && $facture->paiements()->exists())
                                            <a href="{{ route('factures.recu', $facture) }}" target="_blank" class="inline-flex items-center gap-1 text-[10px] font-bold text-emerald-600 hover:text-emerald-800 uppercase">
                                                <i class="fas fa-receipt"></i> Reçu
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <!-- Tab: Paiements & Reçus -->
        <div id="tab-content-paiements" class="tab-content tab-content-pane premium-card bg-white p-6 rounded-2xl hidden">
            <div class="flex items-center justify-between mb-5 pb-3 border-b border-slate-100">
                <div>
                    <h3 class="font-black text-lg text-slate-900 uppercase tracking-[0.1em] flex items-center gap-2">
                        <i class="fas fa-history text-indigo-500"></i> Historique des Paiements & Reçus
                    </h3>
                </div>
            </div>
            @php
                $allPayments = collect();
                foreach($factures as $facture) {
                    $allPayments = $allPayments->merge($facture->paiements()->with(['facture.contrat.bien'])->get());
                }
                $allPayments = $allPayments->sortByDesc('date_paiement');
            @endphp
            @if($allPayments->isEmpty())
                <div class="text-center py-12 text-slate-400">
                    <i class="fas fa-inbox text-4xl mb-3"></i>
                    <p class="text-sm font-medium">Aucun paiement enregistré</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="text-left text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-100 ">
                                <th class="whitespace-nowrap px-6 py-5">Date</th>
                                <th class="whitespace-nowrap px-6 py-5">Référence</th>
                                <th class="whitespace-nowrap px-6 py-5">Mode</th>
                                <th class="whitespace-nowrap px-6 py-5">Facture</th>
                                <th class="whitespace-nowrap px-6 py-5">Bien</th>
                                <th class="text-right whitespace-nowrap px-6 py-5">Montant</th>
                                <th class="text-right whitespace-nowrap px-6 py-5">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            @foreach($allPayments as $paiement)
                                <tr class="group hover:bg-slate-50 transition-colors ">
                                    <td class="whitespace-nowrap px-6 py-5">
                                        <p class="text-sm font-semibold text-slate-700">{{ $paiement->date_paiement ? $paiement->date_paiement->translatedFormat('d/m/Y') : '-' }}</p>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-5">
                                        <p class="text-sm text-slate-700">{{ $paiement->reference_transaction ?? '-' }}</p>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-5">
                                        <p class="text-sm text-slate-700">{{ $paiement->mode_paiement }}</p>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-5">
                                        <p class="text-sm font-semibold text-slate-700">{{ $paiement->facture->numero_facture }}</p>
                                        <p class="text-[10px] text-slate-500">{{ $paiement->facture->mois_facture }}</p>
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-5">
                                        <p class="text-sm text-slate-700">{{ $paiement->facture->contrat->bien->nom_bien }}</p>
                                    </td>
                                    <td class="text-right whitespace-nowrap px-6 py-5">
                                        <p class="text-sm font-black text-emerald-600">{{ number_format($paiement->montant_verse, 0, ',', ' ') }} {{ Auth::user()->agence->devise }}</p>
                                    </td>
                                    <td class="text-right whitespace-nowrap px-6 py-5">
                                        <a href="{{ route('factures.recu', $paiement->facture) }}" target="_blank" class="inline-flex items-center gap-1 text-[10px] font-bold text-emerald-600 hover:text-emerald-800 uppercase">
                                            <i class="fas fa-download"></i> Reçu
                                        </a>
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
        document.addEventListener('DOMContentLoaded', function () {
            // Tab System with Smooth Fluid Transitions
            const tabs = ['contrats', 'factures', 'paiements'];

            tabs.forEach(tabName => {
                const btn = document.getElementById(`tab-${tabName}`);
                if (!btn) return;
                btn.addEventListener('click', function () {
                    // Deactivate all
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
                    // Activate current with smooth animation
                    this.classList.remove('text-slate-600', 'hover:text-slate-900');
                    this.classList.add('bg-indigo-600', 'text-white', 'shadow-sm');
                    const targetPane = document.getElementById(`tab-content-${tabName}`);
                    if (targetPane) {
                        targetPane.classList.remove('hidden');
                        targetPane.classList.remove('active-tab-pane');
                        void targetPane.offsetWidth; // Force reflow
                        targetPane.classList.add('active-tab-pane');
                    }
                });
            });
        });
    </script>
</x-app-layout>
