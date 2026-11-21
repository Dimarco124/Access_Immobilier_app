<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 w-full">
            <div class="flex items-center justify-between flex-wrap gap-4">
                <div>
                    <h2 class="font-black text-2xl text-slate-900 tracking-tight uppercase">Gestion Financière</h2>
                    <p class="text-[11px] text-slate-500 font-bold uppercase tracking-widest mt-1">
                        {{ Auth::user()->agence->nom }} <span class="mx-2 text-slate-300">•</span> {{ Auth::user()->name }}
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <x-month-picker 
                        :value="$moisInputValue" 
                        :label="$moisFormat" 
                        :action="route('finance.index')" 
                        :resetUrl="route('finance.index')" 
                    />
                </div>
            </div>
        </div>
    </x-slot>

    @if(session('success'))
    <div class="mb-4 p-4 bg-green-50 text-green-800 border border-green-200 rounded-lg">
        <p class="text-sm font-medium">{{ session('success') }}</p>
    </div>
    @endif

    @if($errors->any())
    <div class="mb-4 p-4 bg-red-50 text-red-800 border border-red-200 rounded-lg">
        <ul class="list-disc list-inside text-sm">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="space-y-6">
        <!-- KPI Cards Grid (8 Cards: Recouvrement Locatif & Performance Agence) -->
        <div class="space-y-4">
            <!-- Bloc 1: Recouvrement Locatif -->
            <div>
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span>
                        <h3 class="text-xs font-black text-slate-700 uppercase tracking-widest">Recouvrement des Loyers ({{ $moisFormat }})</h3>
                    </div>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                        Factures : {{ $nbFacturesTotal }} émises
                    </span>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- 1. Loyers & Charges Émis -->
                    <div class="premium-card p-5 bg-white overflow-hidden relative group border border-slate-100 hover:border-blue-200 transition-all duration-300">
                        <div class="absolute -top-4 -right-4 w-20 h-20 bg-gradient-to-br from-blue-100 to-blue-50 rounded-full opacity-60 group-hover:scale-125 transition-transform duration-500"></div>
                        <div class="relative z-10">
                            <div class="flex items-center justify-between mb-3">
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Loyers Émis</p>
                                <div class="w-9 h-9 rounded-xl flex items-center justify-center shadow-lg shadow-blue-200" style="background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); color: #ffffff;">
                                    <i class="fas fa-file-invoice-dollar text-white text-sm"></i>
                                </div>
                            </div>
                            <h3 class="text-2xl font-black text-slate-900 mb-1">
                                {{ number_format($loyersEmis, 0, ',', ' ') }}
                                <span class="text-xs font-bold text-slate-400 ml-1">{{ Auth::user()->agence->devise }}</span>
                            </h3>
                            <div class="flex items-center justify-between text-[11px] text-slate-500 font-medium">
                                <span>Total facturé</span>
                                <span class="font-bold text-blue-600">{{ $nbFacturesTotal }} facture(s)</span>
                            </div>
                        </div>
                    </div>

                    <!-- 2. Loyers Encaissés -->
                    <div class="premium-card p-5 bg-white overflow-hidden relative group border border-slate-100 hover:border-emerald-200 transition-all duration-300">
                        <div class="absolute -top-4 -right-4 w-20 h-20 bg-gradient-to-br from-emerald-100 to-emerald-50 rounded-full opacity-60 group-hover:scale-125 transition-transform duration-500"></div>
                        <div class="relative z-10">
                            <div class="flex items-center justify-between mb-3">
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Loyers Encaissés</p>
                                <div class="w-9 h-9 rounded-xl flex items-center justify-center shadow-lg shadow-emerald-200" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); color: #ffffff;">
                                    <i class="fas fa-hand-holding-usd text-white text-sm"></i>
                                </div>
                            </div>
                            <h3 class="text-2xl font-black text-slate-900 mb-1">
                                {{ number_format($loyersEncaisses, 0, ',', ' ') }}
                                <span class="text-xs font-bold text-slate-400 ml-1">{{ Auth::user()->agence->devise }}</span>
                            </h3>
                            <div class="flex items-center justify-between text-[11px] text-slate-500 font-medium">
                                <span>Soldées à 100%</span>
                                <span class="font-bold text-emerald-600">{{ $nbFacturesPayees }} facture(s)</span>
                            </div>
                        </div>
                    </div>

                    <!-- 3. Taux de Recouvrement -->
                    <div class="premium-card p-5 bg-white overflow-hidden relative group border border-slate-100 hover:border-indigo-200 transition-all duration-300">
                        <div class="absolute -top-4 -right-4 w-20 h-20 bg-gradient-to-br from-indigo-100 to-indigo-50 rounded-full opacity-60 group-hover:scale-125 transition-transform duration-500"></div>
                        <div class="relative z-10">
                            <div class="flex items-center justify-between mb-3">
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Taux Recouvrement</p>
                                <div class="w-9 h-9 rounded-xl flex items-center justify-center shadow-lg shadow-indigo-200" style="background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); color: #ffffff;">
                                    <i class="fas fa-chart-line text-white text-sm"></i>
                                </div>
                            </div>
                            <h3 class="text-2xl font-black text-slate-900 mb-1">
                                {{ $tauxRecouvrement }}%
                            </h3>
                            <div class="w-full bg-slate-100 rounded-full h-1.5 mt-2 mb-1.5 overflow-hidden">
                                <div class="h-1.5 rounded-full {{ $tauxRecouvrement >= 90 ? 'bg-emerald-500' : ($tauxRecouvrement >= 50 ? 'bg-amber-500' : 'bg-red-500') }}" style="width: {{ min(100, $tauxRecouvrement) }}%"></div>
                            </div>
                            <div class="flex items-center justify-between text-[10px]">
                                <span class="text-slate-400 font-medium">Performance globale</span>
                                @if($tauxRecouvrement >= 90)
                                <span class="font-bold text-emerald-600 uppercase">Optimal</span>
                                @elseif($tauxRecouvrement >= 50)
                                <span class="font-bold text-amber-600 uppercase">Vigilance</span>
                                @else
                                <span class="font-bold text-red-500 uppercase">Critique</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- 4. Reste à Recouvrer / Créances -->
                    <div class="premium-card p-5 bg-white overflow-hidden relative group border border-slate-100 hover:border-amber-200 transition-all duration-300">
                        <div class="absolute -top-4 -right-4 w-20 h-20 bg-gradient-to-br from-amber-100 to-amber-50 rounded-full opacity-60 group-hover:scale-125 transition-transform duration-500"></div>
                        <div class="relative z-10">
                            <div class="flex items-center justify-between mb-3">
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Reste à Recouvrer</p>
                                <div class="w-9 h-9 rounded-xl flex items-center justify-center shadow-lg shadow-amber-200" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); color: #ffffff;">
                                    <i class="fas fa-clock text-white text-sm"></i>
                                </div>
                            </div>
                            <h3 class="text-2xl font-black text-slate-900 mb-1">
                                {{ number_format($resteARecouvrer, 0, ',', ' ') }}
                                <span class="text-xs font-bold text-slate-400 ml-1">{{ Auth::user()->agence->devise }}</span>
                            </h3>
                            <div class="flex items-center justify-between text-[11px] text-slate-500 font-medium">
                                <span>Créances en attente</span>
                                <span class="font-bold text-amber-600">{{ $nbFacturesImpayees + $nbFacturesPartielles }} non soldée(s)</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bloc 2: Performance Agence & Trésorerie Tiers -->
            <div>
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                        <h3 class="text-xs font-black text-slate-700 uppercase tracking-widest">Performance Agence & Trésorerie ({{ $moisFormat }})</h3>
                    </div>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                        Baux actifs : {{ $contratsActifsCount }}
                    </span>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- 5. Chiffre d'Affaires Agence -->
                    <div class="premium-card p-5 bg-white overflow-hidden relative group border border-slate-100 hover:border-purple-200 transition-all duration-300">
                        <div class="absolute -top-4 -right-4 w-20 h-20 bg-gradient-to-br from-purple-100 to-purple-50 rounded-full opacity-60 group-hover:scale-125 transition-transform duration-500"></div>
                        <div class="relative z-10">
                            <div class="flex items-center justify-between mb-3">
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Revenus Agence</p>
                                <div class="w-9 h-9 rounded-xl flex items-center justify-center shadow-lg shadow-purple-200" style="background: linear-gradient(135deg, #7c3aed 0%, #4f46e5 100%); color: #ffffff;">
                                    <i class="fas fa-wallet text-white text-sm"></i>
                                </div>
                            </div>
                            <h3 class="text-2xl font-black text-slate-900 mb-1">
                                {{ number_format($revenusAgence, 0, ',', ' ') }}
                                <span class="text-xs font-bold text-slate-400 ml-1">{{ Auth::user()->agence->devise }}</span>
                            </h3>
                            <div class="flex items-center justify-between text-[11px] text-slate-500 font-medium">
                                <span>Commissions loyers</span>
                                <span class="font-bold text-purple-600">{{ number_format($commissionsLoyers, 0, ',', ' ') }} {{ Auth::user()->agence->devise }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- 6. Dépenses d'Exploitation Agence -->
                    <div class="premium-card p-5 bg-white overflow-hidden relative group border border-slate-100 hover:border-rose-200 transition-all duration-300">
                        <div class="absolute -top-4 -right-4 w-20 h-20 bg-gradient-to-br from-rose-100 to-rose-50 rounded-full opacity-60 group-hover:scale-125 transition-transform duration-500"></div>
                        <div class="relative z-10">
                            <div class="flex items-center justify-between mb-3">
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Charges d'Exploitation</p>
                                <div class="w-9 h-9 rounded-xl flex items-center justify-center shadow-lg shadow-rose-200" style="background: linear-gradient(135deg, #f43f5e 0%, #e11d48 100%); color: #ffffff;">
                                    <i class="fas fa-receipt text-white text-sm"></i>
                                </div>
                            </div>
                            <h3 class="text-2xl font-black text-slate-900 mb-1">
                                {{ number_format($depensesAgence, 0, ',', ' ') }}
                                <span class="text-xs font-bold text-slate-400 ml-1">{{ Auth::user()->agence->devise }}</span>
                            </h3>
                            <div class="flex items-center justify-between text-[11px] text-slate-500 font-medium">
                                <span>Frais généraux</span>
                                <span class="font-bold text-rose-500">{{ $moisFormat }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- 7. Résultat Net Agence -->
                    <div class="premium-card p-5 bg-white overflow-hidden relative group border border-slate-100 hover:border-teal-200 transition-all duration-300">
                        <div class="absolute -top-4 -right-4 w-20 h-20 bg-gradient-to-br from-teal-100 to-teal-50 rounded-full opacity-60 group-hover:scale-125 transition-transform duration-500"></div>
                        <div class="relative z-10">
                            <div class="flex items-center justify-between mb-3">
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Résultat Net Agence</p>
                                <div class="w-9 h-9 rounded-xl flex items-center justify-center shadow-lg shadow-teal-200" style="background: linear-gradient(135deg, #0d9488 0%, #059669 100%); color: #ffffff;">
                                    <i class="fas fa-balance-scale text-white text-sm"></i>
                                </div>
                            </div>
                            <h3 class="text-2xl font-black text-slate-900 mb-1">
                                {{ number_format($soldeAgence, 0, ',', ' ') }}
                                <span class="text-xs font-bold text-slate-400 ml-1">{{ Auth::user()->agence->devise }}</span>
                            </h3>
                            <div class="flex items-center justify-between text-[11px] font-bold">
                                @if($soldeAgence >= 0)
                                <span class="text-emerald-600 inline-flex items-center gap-1">
                                    <i class="fas fa-arrow-trend-up text-[10px]"></i> Positif
                                </span>
                                <span class="text-emerald-600 font-semibold">{{ $tauxMargeAgence }}% marge</span>
                                @else
                                <span class="text-red-500 inline-flex items-center gap-1">
                                    <i class="fas fa-arrow-trend-down text-[10px]"></i> Déficit
                                </span>
                                <span class="text-red-500 font-semibold">{{ $tauxMargeAgence }}%</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- 8. Cautions Séquestrées & Trésorerie Tiers -->
                    <div class="premium-card p-5 bg-white overflow-hidden relative group border border-slate-100 hover:border-cyan-200 transition-all duration-300">
                        <div class="absolute -top-4 -right-4 w-20 h-20 bg-gradient-to-br from-cyan-100 to-cyan-50 rounded-full opacity-60 group-hover:scale-125 transition-transform duration-500"></div>
                        <div class="relative z-10">
                            <div class="flex items-center justify-between mb-3">
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Cautions Disponibles</p>
                                <div class="w-9 h-9 rounded-xl flex items-center justify-center shadow-lg shadow-cyan-200" style="background: linear-gradient(135deg, #0284c7 0%, #2563eb 100%); color: #ffffff;">
                                    <i class="fas fa-shield-alt text-white text-sm"></i>
                                </div>
                            </div>
                            <h3 class="text-2xl font-black text-slate-900 mb-1">
                                {{ number_format($totalCautionsDisponibles, 0, ',', ' ') }}
                                <span class="text-xs font-bold text-slate-400 ml-1">{{ Auth::user()->agence->devise }}</span>
                            </h3>
                            <div class="flex items-center justify-between text-[11px] text-slate-500 font-medium">
                                <span>Séquestre cumulé</span>
                                <span class="font-bold text-cyan-600">{{ number_format($totalCautions, 0, ',', ' ') }} {{ Auth::user()->agence->devise }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section Graphiques Stratégiques: Évolution Mensuelle des Flux & Rentabilité Agence (12 Derniers Mois) -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
            <!-- Graphe 1: Évolution Mensuelle des Flux Locatifs -->
            <div class="premium-card p-6 bg-white rounded-2xl border border-slate-100 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between pb-3 mb-4 border-b border-slate-100 flex-wrap gap-2">
                        <div class="flex items-center gap-2.5">
                            <div class="w-9 h-9 rounded-xl flex items-center justify-center shadow-sm" style="background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%); color: #ffffff;">
                                <i class="fas fa-chart-area text-white text-sm"></i>
                            </div>
                            <div>
                                <h4 class="font-black text-sm text-slate-800 uppercase tracking-wider">Évolution Mensuelle des Flux</h4>
                                <p class="text-[10px] text-slate-400 font-medium">Facturation, encaissement & reversements (12 derniers mois)</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-[11px] font-bold text-slate-500 bg-slate-50 px-2.5 py-1 rounded-lg border border-slate-100">
                                Recouvrement 12m : <span class="font-black {{ $analytics['totaux']['taux_recouvrement_global'] >= 90 ? 'text-emerald-600' : 'text-amber-600' }}">{{ $analytics['totaux']['taux_recouvrement_global'] }}%</span>
                            </span>
                        </div>
                    </div>

                    <!-- 3 Mini Indicateurs -->
                    <div class="grid grid-cols-3 gap-2 mb-4 text-center">
                        <div class="p-2.5 rounded-xl bg-blue-50/50 border border-blue-100/60">
                            <div class="flex items-center justify-center gap-1.5 mb-0.5">
                                <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                                <span class="text-[9px] font-black text-slate-500 uppercase tracking-wider">Total Émis</span>
                            </div>
                            <p class="text-xs sm:text-sm font-black text-blue-700">
                                {{ number_format($analytics['totaux']['loyers_emis'], 0, ',', ' ') }}
                                <span class="text-[10px] font-semibold text-slate-400 ml-0.5">{{ Auth::user()->agence->devise }}</span>
                            </p>
                        </div>
                        <div class="p-2.5 rounded-xl bg-emerald-50/50 border border-emerald-100/60">
                            <div class="flex items-center justify-center gap-1.5 mb-0.5">
                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                <span class="text-[9px] font-black text-slate-500 uppercase tracking-wider">Total Encaissé</span>
                            </div>
                            <p class="text-xs sm:text-sm font-black text-emerald-700">
                                {{ number_format($analytics['totaux']['loyers_encaisses'], 0, ',', ' ') }}
                                <span class="text-[10px] font-semibold text-slate-400 ml-0.5">{{ Auth::user()->agence->devise }}</span>
                            </p>
                        </div>
                        <div class="p-2.5 rounded-xl bg-amber-50/50 border border-amber-100/60">
                            <div class="flex items-center justify-center gap-1.5 mb-0.5">
                                <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                                <span class="text-[9px] font-black text-slate-500 uppercase tracking-wider">Total Reversé</span>
                            </div>
                            <p class="text-xs sm:text-sm font-black text-amber-700">
                                {{ number_format($analytics['totaux']['reversements'], 0, ',', ' ') }}
                                <span class="text-[10px] font-semibold text-slate-400 ml-0.5">{{ Auth::user()->agence->devise }}</span>
                            </p>
                        </div>
                    </div>

                    <!-- Graphe ApexCharts -->
                    <div id="chartEvolutionFlux" class="w-full h-72"></div>
                </div>
            </div>

            <!-- Graphe 2: Rentabilité & Résultat Net Agence -->
            <div class="premium-card p-6 bg-white rounded-2xl border border-slate-100 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between pb-3 mb-4 border-b border-slate-100 flex-wrap gap-2">
                        <div class="flex items-center gap-2.5">
                            <div class="w-9 h-9 rounded-xl flex items-center justify-center shadow-sm" style="background: linear-gradient(135deg, #7c3aed 0%, #4f46e5 100%); color: #ffffff;">
                                <i class="fas fa-chart-line text-white text-sm"></i>
                            </div>
                            <div>
                                <h4 class="font-black text-sm text-slate-800 uppercase tracking-wider">Rentabilité Agence</h4>
                                <p class="text-[10px] text-slate-400 font-medium">Revenus, charges & marge nette (12 mois)</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-[11px] font-bold text-slate-500 bg-purple-50 px-2.5 py-1 rounded-lg border border-purple-100">
                                Marge 12m : <span class="font-black text-purple-700">{{ $analytics['totaux']['taux_marge_global'] }}%</span>
                            </span>
                        </div>
                    </div>

                    <!-- 3 Mini Indicateurs -->
                    <div class="grid grid-cols-3 gap-2 mb-4 text-center">
                        <div class="p-2.5 rounded-xl bg-indigo-50/50 border border-indigo-100/60">
                            <div class="flex items-center justify-center gap-1.5 mb-0.5">
                                <span class="w-2 h-2 rounded-full bg-indigo-600"></span>
                                <span class="text-[9px] font-black text-slate-500 uppercase tracking-wider">Revenus Agence</span>
                            </div>
                            <p class="text-xs sm:text-sm font-black text-indigo-700">
                                {{ number_format($analytics['totaux']['revenus_agence'], 0, ',', ' ') }}
                                <span class="text-[10px] font-semibold text-slate-400 ml-0.5">{{ Auth::user()->agence->devise }}</span>
                            </p>
                        </div>
                        <div class="p-2.5 rounded-xl bg-rose-50/50 border border-rose-100/60">
                            <div class="flex items-center justify-center gap-1.5 mb-0.5">
                                <span class="w-2 h-2 rounded-full bg-rose-500"></span>
                                <span class="text-[9px] font-black text-slate-500 uppercase tracking-wider">Dépenses</span>
                            </div>
                            <p class="text-xs sm:text-sm font-black text-rose-700">
                                {{ number_format($analytics['totaux']['depenses_agence'], 0, ',', ' ') }}
                                <span class="text-[10px] font-semibold text-slate-400 ml-0.5">{{ Auth::user()->agence->devise }}</span>
                            </p>
                        </div>
                        <div class="p-2.5 rounded-xl bg-emerald-50/50 border border-emerald-100/60">
                            <div class="flex items-center justify-center gap-1.5 mb-0.5">
                                <span class="w-2 h-2 rounded-full bg-emerald-600"></span>
                                <span class="text-[9px] font-black text-slate-500 uppercase tracking-wider">Marge Nette</span>
                            </div>
                            <p class="text-xs sm:text-sm font-black {{ $analytics['totaux']['marge_nette'] >= 0 ? 'text-emerald-700' : 'text-rose-700' }}">
                                {{ number_format($analytics['totaux']['marge_nette'], 0, ',', ' ') }}
                                <span class="text-[10px] font-semibold text-slate-400 ml-0.5">{{ Auth::user()->agence->devise }}</span>
                            </p>
                        </div>
                    </div>

                    <!-- Graphe ApexCharts -->
                    <div id="chartRentabiliteAgence" class="w-full h-72"></div>
                </div>
            </div>
        </div>

        <!-- Section Analytique Détaillée: Structure Revenus Agence & Canaux d'Encaissement -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
            <!-- Panneau 1: Structure des Revenus Agence -->
            <div class="premium-card p-6 bg-white rounded-2xl border border-slate-100 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between pb-3 mb-4 border-b border-slate-100">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center text-sm font-bold">
                                <i class="fas fa-layer-group"></i>
                            </div>
                            <div>
                                <h4 class="font-black text-sm text-slate-800 uppercase tracking-wider">Sources de Revenus Agence</h4>
                                <p class="text-[10px] text-slate-400 font-medium">Ventilation des honoraires perçus en {{ $moisFormat }}</p>
                            </div>
                        </div>
                        <span class="text-xs font-black text-purple-600 bg-purple-50 px-2.5 py-1 rounded-full">
                            {{ number_format($revenusAgence, 0, ',', ' ') }} {{ Auth::user()->agence->devise }}
                        </span>
                    </div>

                    <div class="space-y-3.5">
                        @php
                            $pctCommissions = $revenusAgence > 0 ? round(($commissionsLoyers / $revenusAgence) * 100, 1) : 0;
                            $pctDossier = $revenusAgence > 0 ? round(($fraisDossier / $revenusAgence) * 100, 1) : 0;
                            $pctVisites = $revenusAgence > 0 ? round(($visites / $revenusAgence) * 100, 1) : 0;
                            $pctRetenues = $revenusAgence > 0 ? round(($retenuesCaution / $revenusAgence) * 100, 1) : 0;
                        @endphp

                        <!-- 1. Commissions Loyers -->
                        <div>
                            <div class="flex items-center justify-between text-xs mb-1">
                                <span class="font-semibold text-slate-700 flex items-center gap-1.5">
                                    <i class="fas fa-percent text-[10px] text-purple-500"></i> Commissions de Gestion Locative
                                </span>
                                <span class="font-bold text-slate-800">{{ number_format($commissionsLoyers, 0, ',', ' ') }} {{ Auth::user()->agence->devise }} <span class="text-slate-400 font-normal">({{ $pctCommissions }}%)</span></span>
                            </div>
                            <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                                <div class="bg-purple-600 h-1.5 rounded-full" style="width: {{ $pctCommissions }}%"></div>
                            </div>
                        </div>

                        <!-- 2. Frais de dossier -->
                        <div>
                            <div class="flex items-center justify-between text-xs mb-1">
                                <span class="font-semibold text-slate-700 flex items-center gap-1.5">
                                    <i class="fas fa-folder-open text-[10px] text-blue-500"></i> Frais de Dossier & Rédaction de Bail
                                </span>
                                <span class="font-bold text-slate-800">{{ number_format($fraisDossier, 0, ',', ' ') }} {{ Auth::user()->agence->devise }} <span class="text-slate-400 font-normal">({{ $pctDossier }}%)</span></span>
                            </div>
                            <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                                <div class="bg-blue-500 h-1.5 rounded-full" style="width: {{ $pctDossier }}%"></div>
                            </div>
                        </div>

                        <!-- 3. Visites de biens -->
                        <div>
                            <div class="flex items-center justify-between text-xs mb-1">
                                <span class="font-semibold text-slate-700 flex items-center gap-1.5">
                                    <i class="fas fa-eye text-[10px] text-amber-500"></i> Honoraires de Visite de Biens
                                </span>
                                <span class="font-bold text-slate-800">{{ number_format($visites, 0, ',', ' ') }} {{ Auth::user()->agence->devise }} <span class="text-slate-400 font-normal">({{ $pctVisites }}%)</span></span>
                            </div>
                            <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                                <div class="bg-amber-500 h-1.5 rounded-full" style="width: {{ $pctVisites }}%"></div>
                            </div>
                        </div>

                        <!-- 4. Retenues Caution -->
                        <div>
                            <div class="flex items-center justify-between text-xs mb-1">
                                <span class="font-semibold text-slate-700 flex items-center gap-1.5">
                                    <i class="fas fa-hand-holding text-[10px] text-emerald-500"></i> Retenues & Dédommagements Cautions
                                </span>
                                <span class="font-bold text-slate-800">{{ number_format($retenuesCaution, 0, ',', ' ') }} {{ Auth::user()->agence->devise }} <span class="text-slate-400 font-normal">({{ $pctRetenues }}%)</span></span>
                            </div>
                            <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                                <div class="bg-emerald-500 h-1.5 rounded-full" style="width: {{ $pctRetenues }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-500">
                    <span class="font-semibold"><i class="fas fa-circle-info text-slate-400 mr-1"></i> Rémunération agence</span>
                    <span class="font-black text-slate-700">Marge d'exploitation : {{ $tauxMargeAgence }}%</span>
                </div>
            </div>

            <!-- Panneau 2: Canaux de Règlements & Trésorerie Tiers -->
            <div class="premium-card p-6 bg-white rounded-2xl border border-slate-100 flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between pb-3 mb-4 border-b border-slate-100">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm font-bold">
                                <i class="fas fa-money-check-dollar"></i>
                            </div>
                            <div>
                                <h4 class="font-black text-sm text-slate-800 uppercase tracking-wider">Canaux de Règlements Locataires</h4>
                                <p class="text-[10px] text-slate-400 font-medium">Répartition des encaissements enregistrés en {{ $moisFormat }}</p>
                            </div>
                        </div>
                        <span class="text-xs font-black text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-full">
                            {{ number_format($totalPaiementsMois, 0, ',', ' ') }} {{ Auth::user()->agence->devise }}
                        </span>
                    </div>

                    @if($paiementsModes->isEmpty() || $totalPaiementsMois == 0)
                    <div class="py-6 text-center text-slate-400">
                        <i class="fas fa-cash-register text-2xl mb-2 text-slate-300"></i>
                        <p class="text-xs font-semibold">Aucun encaissement validé sur cette période</p>
                    </div>
                    @else
                    <div class="space-y-3">
                        @foreach($paiementsModes as $mode)
                        @php
                            $pctMode = $totalPaiementsMois > 0 ? round(($mode->total / $totalPaiementsMois) * 100, 1) : 0;
                            $modeIcon = match(strtolower($mode->mode)) {
                                'mobile money' => 'fas fa-mobile-screen-button text-amber-500',
                                'espèces', 'especes' => 'fas fa-money-bill-wave text-emerald-500',
                                'virement' => 'fas fa-building-columns text-blue-500',
                                'chèque', 'cheque' => 'fas fa-money-check-alt text-indigo-500',
                                default => 'fas fa-credit-card text-slate-500',
                            };
                            $modeBarColor = match(strtolower($mode->mode)) {
                                'mobile money' => 'bg-amber-500',
                                'espèces', 'especes' => 'bg-emerald-500',
                                'virement' => 'bg-blue-500',
                                'chèque', 'cheque' => 'bg-indigo-500',
                                default => 'bg-slate-500',
                            };
                        @endphp
                        <div>
                            <div class="flex items-center justify-between text-xs mb-1">
                                <span class="font-semibold text-slate-700 flex items-center gap-1.5">
                                    <i class="{{ $modeIcon }} text-[11px]"></i> {{ $mode->mode }}
                                    <span class="text-[10px] font-bold text-slate-400 bg-slate-100 px-1.5 py-0.2 rounded">{{ $mode->nb }} trans.</span>
                                </span>
                                <span class="font-bold text-slate-800">{{ number_format($mode->total, 0, ',', ' ') }} {{ Auth::user()->agence->devise }} <span class="text-slate-400 font-normal">({{ $pctMode }}%)</span></span>
                            </div>
                            <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                                <div class="{{ $modeBarColor }} h-1.5 rounded-full" style="width: {{ $pctMode }}%"></div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>

                <!-- Synthèse Bailleurs & Patrimoine -->
                <div class="mt-4 pt-3 border-t border-slate-100 grid grid-cols-3 gap-2 text-center">
                    <div class="p-2 rounded-lg bg-slate-50">
                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-wider">Reversé Bailleurs</p>
                        <p class="text-xs font-black text-blue-600 mt-0.5">{{ number_format($totalReversementsMois, 0, ',', ' ') }}</p>
                    </div>
                    <div class="p-2 rounded-lg bg-slate-50">
                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-wider">Solde en Attente</p>
                        <p class="text-xs font-black text-amber-600 mt-0.5">{{ number_format($totalSoldeEnAttente, 0, ',', ' ') }}</p>
                    </div>
                    <div class="p-2 rounded-lg bg-slate-50">
                        <p class="text-[9px] font-black text-slate-400 uppercase tracking-wider">Valeur Locative</p>
                        <p class="text-xs font-black text-slate-800 mt-0.5">{{ number_format($valeurLocativeActive, 0, ',', ' ') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabs Navigation -->
        <div class="w-full overflow-x-auto scrollbar-none pb-2">
            <div class="premium-card bg-white p-1.5 inline-flex gap-1.5 rounded-2xl min-w-max shadow-sm border border-slate-100">
                <button id="tab-cautions" class="tab-btn-smooth px-5 py-2.5 text-[11px] font-black uppercase tracking-wider rounded-xl transition-all bg-blue-600 text-white shadow-sm whitespace-nowrap">
                    <i class="fas fa-shield-alt mr-2"></i> Cautions
                </button>
                <button id="tab-proprietaires" class="tab-btn-smooth px-5 py-2.5 text-[11px] font-black uppercase tracking-wider rounded-xl transition-all text-slate-600 hover:text-slate-900 whitespace-nowrap">
                    <i class="fas fa-users mr-2"></i> Propriétaires
                </button>
                <button id="tab-creances" class="tab-btn-smooth px-5 py-2.5 text-[11px] font-black uppercase tracking-wider rounded-xl transition-all text-slate-600 hover:text-slate-900 whitespace-nowrap">
                    <i class="fas fa-clock mr-2"></i> Balance Âgée
                    @if($nbCreances > 0)
                    <span class="ml-1.5 px-2 py-0.5 text-[9px] font-black rounded-full bg-red-100 text-red-600">
                        {{ $nbCreances }}
                    </span>
                    @endif
                </button>
                <button id="tab-reversements" class="tab-btn-smooth px-5 py-2.5 text-[11px] font-black uppercase tracking-wider rounded-xl transition-all text-slate-600 hover:text-slate-900 whitespace-nowrap">
                    <i class="fas fa-exchange-alt mr-2"></i> Reversements
                </button>
                <button id="tab-transactions" class="tab-btn-smooth px-5 py-2.5 text-[11px] font-black uppercase tracking-wider rounded-xl transition-all text-slate-600 hover:text-slate-900 whitespace-nowrap">
                    <i class="fas fa-history mr-2"></i> Transactions
                </button>
            </div>
        </div>

        <!-- Tab: Cautions -->
        <div id="tab-content-cautions" class="tab-content tab-content-pane active-tab-pane premium-card bg-white p-6 rounded-2xl">
            <div class="flex items-center justify-between mb-5 pb-3 border-b border-slate-100">
                <div>
                    <h3 class="font-black text-lg text-slate-900 uppercase tracking-[0.1em] flex items-center gap-2">
                        <i class="fas fa-shield-alt text-blue-600"></i> Gestion des Cautions
                    </h3>
                    <p class="text-[10px] text-slate-400 font-medium mt-1">Cautions par contrat (Total cumulé, pas filtré par mois)</p>
                </div>
            </div>
            <div class="overflow-x-auto scrollbar-none">
                <table class="w-full">
                    <thead>
                        <tr class="text-left text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-100">
                            <th class="whitespace-nowrap px-6 py-5">Locataire</th>
                            <th class="whitespace-nowrap px-6 py-5">Contrat</th>
                            <th class="whitespace-nowrap px-6 py-5">Caution Totale</th>
                            <th class="whitespace-nowrap px-6 py-5">Remboursé</th>
                            <th class="whitespace-nowrap px-6 py-5">Retenu</th>
                            <th class="whitespace-nowrap px-6 py-5">Disponible</th>
                            <th class="whitespace-nowrap px-6 py-5">Statut</th>
                            <th class="text-right whitespace-nowrap px-6 py-5">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @foreach($cautions as $contrat)
                        <tr class="group hover:bg-slate-50 transition-colors">
                            <td class="whitespace-nowrap px-6 py-5">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-black text-sm">
                                        {{ strtoupper(substr($contrat->locataire->user->name ?? 'L', 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-slate-800">{{ $contrat->locataire->user->name ?? 'Locataire' }}</p>
                                        <p class="text-[10px] text-slate-400 font-medium">{{ $contrat->locataire->user->email ?? '' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="whitespace-nowrap px-6 py-5">
                                <p class="text-sm font-semibold text-slate-700">{{ $contrat->bien->nom_bien ?? '-' }}</p>
                                <p class="text-[10px] text-slate-400">{{ $contrat->bien->familleDeBien->nom_famille ?? '' }}</p>
                            </td>
                            <td class="whitespace-nowrap px-6 py-5">
                                <p class="text-sm font-black text-slate-700">{{ number_format($contrat->montant_caution, 0, ',', ' ') }} {{ Auth::user()->agence->devise }}</p>
                            </td>
                            <td class="whitespace-nowrap px-6 py-5">
                                <p class="text-sm font-medium text-emerald-600">{{ number_format($contrat->montant_caution_rembourse, 0, ',', ' ') }}</p>
                            </td>
                            <td class="whitespace-nowrap px-6 py-5">
                                <p class="text-sm font-medium text-amber-600">{{ number_format($contrat->montant_caution_retenu, 0, ',', ' ') }}</p>
                            </td>
                            <td class="whitespace-nowrap px-6 py-5">
                                <p class="text-sm font-bold text-blue-600">{{ number_format($contrat->caution_disponible, 0, ',', ' ') }}</p>
                            </td>
                            <td class="whitespace-nowrap px-6 py-5">
                                @if($contrat->statut == 'Actif')
                                <span class="px-3 py-1 text-[10px] font-black text-emerald-600 bg-emerald-50 rounded-full uppercase">Actif</span>
                                @else
                                <span class="px-3 py-1 text-[10px] font-black text-slate-500 bg-slate-100 rounded-full uppercase">Clos</span>
                                @endif
                            </td>
                            <td class="text-right whitespace-nowrap px-6 py-5">
                                @if($contrat->caution_disponible > 0)
                                <button onclick="openRemboursementModal({{ $contrat->id }}, {{ $contrat->caution_disponible }})" class="px-3.5 py-1.5 text-xs font-black text-blue-600 bg-blue-50 rounded-xl hover:bg-blue-600 hover:text-white transition-all uppercase">
                                    Rembourser
                                </button>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Tab: Propriétaires -->
        <div id="tab-content-proprietaires" class="tab-content tab-content-pane premium-card bg-white p-6 rounded-2xl hidden">
            <div class="flex items-center justify-between mb-5 pb-3 border-b border-slate-100 flex-wrap gap-3">
                <div>
                    <h3 class="font-black text-lg text-slate-900 uppercase tracking-[0.1em] flex items-center gap-2">
                        <i class="fas fa-users text-blue-600"></i> Soldes Propriétaires
                    </h3>
                    <p class="text-[10px] text-slate-400 font-medium mt-1">Pour {{ $moisFormat }}</p>
                </div>
                @if($proprietairesEnAttenteCount > 0)
                <div class="flex items-center gap-3">
                    <label class="flex items-center gap-2 cursor-pointer text-xs font-bold text-slate-600 select-none">
                        <input type="checkbox" id="selectAllProprietairesCheckbox" onchange="toggleSelectAllProprietaires(this)" class="w-4 h-4 rounded text-blue-600 focus:ring-blue-500 border-slate-300">
                        <span>Tout cocher ({{ $proprietairesEnAttenteCount }})</span>
                    </label>
                    <button type="button" id="btnBatchReversement" onclick="openBatchReversementModal()" disabled class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-black uppercase tracking-wider rounded-xl transition-all inline-flex items-center gap-2 opacity-50 cursor-not-allowed shadow-sm">
                        <i class="fas fa-layer-group"></i> Reversement Groupé (<span id="batchSelectedCount">0</span>)
                    </button>
                </div>
                @endif
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($proprietaires as $proprietaire)
                <div class="p-5 rounded-xl border border-slate-100 hover:border-slate-200 transition-all bg-white">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-center gap-3">
                            @if($proprietaire->solde > 0)
                            <input type="checkbox" class="batch-proprio-checkbox w-4 h-4 rounded text-blue-600 focus:ring-blue-500 border-slate-300 cursor-pointer"
                                data-id="{{ $proprietaire->id }}"
                                data-nom="{{ addslashes($proprietaire->user->name) }}"
                                data-solde="{{ $proprietaire->solde }}"
                                data-loyers="{{ $proprietaire->loyers_collectes }}"
                                data-comm="{{ $proprietaire->commissions }}"
                                data-dep="{{ $proprietaire->depenses }}"
                                data-rib="{{ addslashes($proprietaire->rib_bancaire ?? '') }}"
                                onchange="updateBatchSelection()"
                                title="Sélectionner pour le reversement groupé">
                            @endif
                            <div>
                                <p class="text-sm font-bold text-slate-800">{{ $proprietaire->user->name }}</p>
                                <p class="text-[10px] text-slate-400 font-medium">{{ count($proprietaire->biens) }} bien(s)</p>
                            </div>
                        </div>
                        @if($proprietaire->solde > 0)
                        <span class="px-3 py-1 text-[10px] font-black text-emerald-600 bg-emerald-50 rounded-full whitespace-nowrap">{{ number_format($proprietaire->solde, 0, ',', ' ') }} {{ Auth::user()->agence->devise }}</span>
                        @else
                        <span class="px-3 py-1 text-[10px] font-black text-slate-500 bg-slate-50 rounded-full whitespace-nowrap">{{ number_format($proprietaire->solde, 0, ',', ' ') }}</span>
                        @endif
                    </div>
                    <div class="mt-4 pt-4 border-t border-slate-50 grid grid-cols-4 gap-2 text-center">
                        <div>
                            <p class="text-[9px] text-slate-400 font-black uppercase">Loyers</p>
                            <p class="text-xs font-bold text-slate-700">{{ number_format($proprietaire->loyers_collectes, 0, ',', ' ') }}</p>
                        </div>
                        <div>
                            <p class="text-[9px] text-slate-400 font-black uppercase">Comm.</p>
                            <p class="text-xs font-bold text-amber-600">{{ number_format($proprietaire->commissions, 0, ',', ' ') }}</p>
                        </div>
                        <div>
                            <p class="text-[9px] text-slate-400 font-black uppercase">Dépenses</p>
                            <p class="text-xs font-bold text-red-500">{{ number_format($proprietaire->depenses, 0, ',', ' ') }}</p>
                        </div>
                        <div>
                            <p class="text-[9px] text-slate-400 font-black uppercase">Reversés</p>
                            <p class="text-xs font-bold text-blue-600">{{ number_format($proprietaire->reversements_effectues, 0, ',', ' ') }}</p>
                        </div>
                    </div>
                    @if($proprietaire->solde > 0)
                    <button onclick="openReversementModal({{ $proprietaire->id }}, {{ $proprietaire->solde }}, '{{ addslashes($proprietaire->user->name) }}', {{ $proprietaire->loyers_collectes }}, {{ $proprietaire->commissions }}, {{ $proprietaire->depenses }})" class="mt-4 w-full py-2.5 text-xs font-black text-blue-600 bg-blue-50 rounded-xl hover:bg-blue-600 hover:text-white transition-all uppercase">
                        Créer un reversement
                    </button>
                    @endif
                </div>
                @endforeach
            </div>
        </div>

        <!-- Tab: Balance Âgée & Impayés -->
        <div id="tab-content-creances" class="tab-content tab-content-pane premium-card bg-white p-6 rounded-2xl hidden">
            <div class="flex items-center justify-between mb-5 pb-3 border-b border-slate-100 flex-wrap gap-3">
                <div>
                    <h3 class="font-black text-lg text-slate-900 uppercase tracking-[0.1em] flex items-center gap-2">
                        <i class="fas fa-clock text-blue-600"></i> Balance Âgée & Recouvrement
                    </h3>
                    <p class="text-[10px] text-slate-400 font-medium mt-1">Suivi des impayés locatifs et relances par tranche d'arriéré</p>
                </div>
                @if($nbCreances > 0)
                <div class="flex items-center gap-2">
                    <span class="text-xs font-medium text-slate-500">Total impayé :</span>
                    <span class="px-3 py-1 bg-red-50 text-red-600 rounded-full text-xs font-black">
                        {{ number_format($totalCreances, 0, ',', ' ') }} {{ Auth::user()->agence->devise }}
                    </span>
                </div>
                @endif
            </div>

            <!-- Filtres de tranches d'arriérés (Style unifié avec le reste de l'application) -->
            <div class="flex items-center gap-2 flex-wrap mb-5">
                <button type="button" onclick="filterCreances('all')" id="filter-btn-all" class="creance-filter-btn px-4 py-2 rounded-xl text-xs font-black uppercase tracking-wider transition-all bg-blue-600 text-white shadow-sm">
                    Toutes ({{ $nbCreances }})
                </button>
                <button type="button" onclick="filterCreances('0_15')" id="filter-btn-0_15" class="creance-filter-btn px-4 py-2 rounded-xl text-xs font-black uppercase tracking-wider transition-all bg-slate-100 hover:bg-slate-200 text-slate-600">
                    0 - 15 jours ({{ $tranchesBalanceAgee['0_15']['nb'] }})
                </button>
                <button type="button" onclick="filterCreances('16_30')" id="filter-btn-16_30" class="creance-filter-btn px-4 py-2 rounded-xl text-xs font-black uppercase tracking-wider transition-all bg-slate-100 hover:bg-slate-200 text-slate-600">
                    16 - 30 jours ({{ $tranchesBalanceAgee['16_30']['nb'] }})
                </button>
                <button type="button" onclick="filterCreances('31_60')" id="filter-btn-31_60" class="creance-filter-btn px-4 py-2 rounded-xl text-xs font-black uppercase tracking-wider transition-all bg-slate-100 hover:bg-slate-200 text-slate-600">
                    31 - 60 jours ({{ $tranchesBalanceAgee['31_60']['nb'] }})
                </button>
                <button type="button" onclick="filterCreances('60_plus')" id="filter-btn-60_plus" class="creance-filter-btn px-4 py-2 rounded-xl text-xs font-black uppercase tracking-wider transition-all bg-slate-100 hover:bg-slate-200 text-slate-600">
                    + 60 jours ({{ $tranchesBalanceAgee['60_plus']['nb'] }})
                </button>
            </div>

            <!-- Tableau des créances & relances -->
            @if(empty($creances))
            <div class="text-center py-10 text-slate-400">
                <i class="fas fa-check-circle text-4xl mb-3 text-emerald-500"></i>
                <p class="text-sm font-medium text-slate-700">Aucun impayé en cours</p>
                <p class="text-xs text-slate-400 mt-1">Tous les loyers et factures sont intégralement réglés.</p>
            </div>
            @else
            <div class="overflow-x-auto scrollbar-none">
                <table class="w-full">
                    <thead>
                        <tr class="text-left text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-100">
                            <th class="whitespace-nowrap px-6 py-5">Locataire</th>
                            <th class="whitespace-nowrap px-6 py-5">Propriété</th>
                            <th class="whitespace-nowrap px-6 py-5">Facture</th>
                            <th class="whitespace-nowrap px-6 py-5">Tranche & Retard</th>
                            <th class="text-right whitespace-nowrap px-6 py-5">Reste Dû</th>
                            <th class="text-right whitespace-nowrap px-6 py-5">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @foreach($creances as $item)
                        @php
                            $f = $item->facture;
                            $loc = $f->contrat?->locataire;
                            $userLoc = $loc?->user;
                            $bien = $f->contrat?->bien;
                            $proprio = $bien?->proprietaire?->user;
                        @endphp
                        <tr class="creance-row group hover:bg-slate-50 transition-colors" data-tranche="{{ $item->tranche }}">
                            <td class="whitespace-nowrap px-6 py-5">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-black text-sm">
                                        {{ strtoupper(substr($userLoc->name ?? 'L', 0, 1)) }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-slate-800">{{ $userLoc->name ?? 'Locataire' }}</p>
                                        <p class="text-[10px] text-slate-400 font-medium">{{ $userLoc->telephone ?? $userLoc->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="whitespace-nowrap px-6 py-5">
                                <p class="text-sm font-semibold text-slate-700">{{ $bien->nom_bien ?? '-' }}</p>
                                <p class="text-[10px] text-slate-400">{{ $proprio->name ?? 'Propriétaire' }}</p>
                            </td>
                            <td class="whitespace-nowrap px-6 py-5">
                                <p class="text-sm font-semibold text-slate-700">#{{ $f->numero_facture }}</p>
                                <span class="px-2.5 py-0.5 text-[10px] font-black bg-blue-50 text-blue-600 rounded-full uppercase">{{ $f->type_facture }}</span>
                            </td>
                            <td class="whitespace-nowrap px-6 py-5">
                                @if($item->tranche === '0_15')
                                <span class="px-3 py-1 text-[10px] font-black text-emerald-600 bg-emerald-50 rounded-full uppercase">0 - 15 jours</span>
                                @elseif($item->tranche === '16_30')
                                <span class="px-3 py-1 text-[10px] font-black text-amber-600 bg-amber-50 rounded-full uppercase">16 - 30 jours</span>
                                @elseif($item->tranche === '31_60')
                                <span class="px-3 py-1 text-[10px] font-black text-orange-600 bg-orange-50 rounded-full uppercase">31 - 60 jours</span>
                                @else
                                <span class="px-3 py-1 text-[10px] font-black text-red-600 bg-red-50 rounded-full uppercase">+ 60 jours</span>
                                @endif
                                <p class="text-[10px] text-slate-400 mt-1">Échéance : {{ $item->date_echeance->format('d/m/Y') }} ({{ $item->jours_retard }}j)</p>
                            </td>
                            <td class="text-right whitespace-nowrap px-6 py-5">
                                <p class="text-sm font-black text-red-600">{{ number_format($item->reste_a_payer, 0, ',', ' ') }} {{ Auth::user()->agence->devise }}</p>
                                @if($f->montant_deja_paye > 0)
                                <p class="text-[10px] text-emerald-600 font-medium">Payé : {{ number_format($f->montant_deja_paye, 0, ',', ' ') }}</p>
                                @endif
                            </td>
                            <td class="text-right whitespace-nowrap px-6 py-5">
                                <div class="flex items-center justify-end gap-1.5">
                                    @if($item->whatsapp_url)
                                    <a href="{{ $item->whatsapp_url }}" target="_blank" class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 hover:bg-emerald-600 hover:text-white flex items-center justify-center transition-all shadow-sm" title="Relance WhatsApp">
                                        <i class="fab fa-whatsapp text-sm"></i>
                                    </a>
                                    @endif
                                    @if($userLoc?->telephone)
                                    <a href="tel:{{ $userLoc->telephone }}" class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white flex items-center justify-center transition-all shadow-sm" title="Appeler {{ $userLoc->telephone }}">
                                        <i class="fas fa-phone text-xs"></i>
                                    </a>
                                    @endif
                                    <button onclick="copyRelanceMessage('{{ addslashes($item->message_relance) }}', this)" class="w-8 h-8 rounded-lg bg-slate-50 text-slate-600 hover:bg-slate-900 hover:text-white flex items-center justify-center transition-all shadow-sm" title="Copier le message de relance">
                                        <i class="fas fa-copy text-xs"></i>
                                    </button>
                                    <a href="{{ route('factures.index', ['numero' => $f->numero_facture]) }}" class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 hover:bg-indigo-600 hover:text-white flex items-center justify-center transition-all shadow-sm" title="Voir la facture">
                                        <i class="fas fa-file-invoice text-xs"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>

        <!-- Tab: Reversements -->
        <div id="tab-content-reversements" class="tab-content tab-content-pane premium-card bg-white p-6 rounded-2xl hidden">
            <div class="flex items-center justify-between mb-5 pb-3 border-b border-slate-100">
                <div>
                    <h3 class="font-black text-lg text-slate-900 uppercase tracking-[0.1em] flex items-center gap-2">
                        <i class="fas fa-exchange-alt text-blue-600"></i> Historique des Reversements
                    </h3>
                    <p class="text-[10px] text-slate-400 font-medium mt-1">Tous les reversements effectués</p>
                </div>
            </div>
            @if($reversements->isEmpty())
            <div class="text-center py-10 text-slate-400">
                <i class="fas fa-inbox text-4xl mb-3"></i>
                <p class="text-sm font-medium">Aucun reversement</p>
            </div>
            @else
            <div class="overflow-x-auto scrollbar-none">
                <table class="w-full">
                    <thead>
                        <tr class="text-left text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-100">
                            <th class="whitespace-nowrap px-6 py-5">Date</th>
                            <th class="whitespace-nowrap px-6 py-5">Propriétaire</th>
                            <th class="whitespace-nowrap px-6 py-5">Période</th>
                            <th class="whitespace-nowrap px-6 py-5">Référence</th>
                            <th class="whitespace-nowrap px-6 py-5">Compte</th>
                            <th class="text-right whitespace-nowrap px-6 py-5">Montant</th>
                            <th class="text-right whitespace-nowrap px-6 py-5">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @foreach($reversements as $reversement)
                        <tr class="group hover:bg-slate-50 transition-colors">
                            <td class="whitespace-nowrap px-6 py-5">
                                <p class="text-sm font-semibold text-slate-700">{{ $reversement->date_reversement->translatedFormat('d/m/Y') }}</p>
                            </td>
                            <td class="whitespace-nowrap px-6 py-5">
                                <p class="text-sm font-bold text-slate-800">{{ $reversement->proprietaire->user->name ?? '-' }}</p>
                            </td>
                            <td class="whitespace-nowrap px-6 py-5">
                                <span class="px-2.5 py-1 text-[10px] font-black bg-blue-50 text-blue-600 rounded-full uppercase">{{ $reversement->periode }}</span>
                            </td>
                            <td class="whitespace-nowrap px-6 py-5">
                                <p class="text-sm font-semibold text-slate-700">{{ $reversement->reference_transaction ?? '-' }}</p>
                            </td>
                            <td class="whitespace-nowrap px-6 py-5">
                                <p class="text-sm text-slate-600">{{ $reversement->compteBancaire->nom ?? '-' }}</p>
                            </td>
                            <td class="text-right whitespace-nowrap px-6 py-5">
                                <p class="text-sm font-black text-slate-800">{{ number_format($reversement->montant_net_verse, 0, ',', ' ') }} {{ Auth::user()->agence->devise }}</p>
                            </td>
                            <td class="text-right whitespace-nowrap px-6 py-5">
                                <form method="POST" action="{{ route('finance.supprimer-reversement', $reversement->id) }}" onsubmit="return confirm('Êtes-vous sûr de vouloir annuler ce reversement de {{ number_format($reversement->montant_net_verse, 0, ',', ' ') }} {{ Auth::user()->agence->devise }} ?');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-2.5 py-1 text-[10px] font-black text-red-600 bg-red-50 hover:bg-red-600 hover:text-white rounded-lg transition-colors uppercase inline-flex items-center gap-1" title="Annuler ce reversement">
                                        <i class="fas fa-trash-alt text-[9px]"></i> Annuler
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>

        <!-- Tab: Transactions -->
        <div id="tab-content-transactions" class="tab-content tab-content-pane premium-card bg-white p-6 rounded-2xl hidden">
            <div class="flex items-center justify-between mb-5 pb-3 border-b border-slate-100">
                <div>
                    <h3 class="font-black text-lg text-slate-900 uppercase tracking-[0.1em] flex items-center gap-2">
                        <i class="fas fa-history text-blue-600"></i> Historique des Transactions
                    </h3>
                    <p class="text-[10px] text-slate-400 font-medium mt-1">Toutes les opérations de {{ $moisFormat }}</p>
                </div>
            </div>
            <div class="overflow-x-auto scrollbar-none">
                @if($transactions->isEmpty())
                <div class="text-center py-10 text-slate-400">
                    <i class="fas fa-inbox text-4xl mb-3"></i>
                    <p class="text-sm font-medium">Aucune transaction pour ce mois</p>
                </div>
                @else
                <table class="w-full">
                    <thead>
                        <tr class="text-left text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-100">
                            <th class="whitespace-nowrap px-6 py-5">Date</th>
                            <th class="whitespace-nowrap px-6 py-5">Type</th>
                            <th class="whitespace-nowrap px-6 py-5">Compte</th>
                            <th class="whitespace-nowrap px-6 py-5">Référence</th>
                            <th class="whitespace-nowrap px-6 py-5">Description</th>
                            <th class="text-right whitespace-nowrap px-6 py-5">Montant</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @foreach($transactions as $transaction)
                        <tr class="group hover:bg-slate-50 transition-colors">
                            <td class="whitespace-nowrap px-6 py-5">
                                <p class="text-sm font-semibold text-slate-700">{{ $transaction->date_transaction->translatedFormat('d/m/Y') }}</p>
                            </td>
                            <td class="whitespace-nowrap px-6 py-5">
                                <span class="px-3 py-1 text-[10px] font-black uppercase rounded-full
                                    @if(in_array($transaction->type_transaction, ['depot_caution', 'loyer', 'frais_dossier', 'retenue_caution']))
                                        bg-emerald-50 text-emerald-600
                                    @elseif(in_array($transaction->type_transaction, ['remboursement_caution', 'reversement', 'depense']))
                                        bg-red-50 text-red-600
                                    @else
                                        bg-slate-50 text-slate-600
                                    @endif
                                ">
                                    {{ str_replace('_', ' ', $transaction->type_transaction) }}
                                </span>
                            </td>
                            <td class="whitespace-nowrap px-6 py-5">
                                <span class="text-[10px] font-medium text-slate-500 uppercase">{{ $transaction->type_compte }}</span>
                            </td>
                            <td class="whitespace-nowrap px-6 py-5">
                                <p class="text-sm font-semibold text-slate-700">{{ $transaction->reference }}</p>
                            </td>
                            <td class="whitespace-nowrap px-6 py-5">
                                <p class="text-sm text-slate-600">{{ $transaction->description }}</p>
                            </td>
                            <td class="text-right whitespace-nowrap px-6 py-5">
                                <p class="text-sm font-black {{ $transaction->sens == 'credit' ? 'text-emerald-600' : 'text-red-600' }}">
                                    {{ $transaction->sens == 'credit' ? '+' : '-' }}{{ number_format($transaction->montant, 0, ',', ' ') }}
                                    <span class="text-xs font-bold text-slate-400">{{ $transaction->devise }}</span>
                                </p>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @endif
            </div>
        </div>
    </div>

    <!-- Modal 1: Remboursement Caution (Style uniforme avec le reste de l'application) -->
    <div id="remboursementModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden items-center justify-center p-4 overflow-y-auto">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full mx-auto my-auto overflow-hidden relative">
            <div class="p-6 bg-gradient-to-r from-blue-600 to-indigo-600 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-[10px] font-black uppercase tracking-widest text-blue-200">Restitution de caution</span>
                        <h3 class="text-lg font-black uppercase tracking-tight mt-0.5">Rembourser la caution</h3>
                    </div>
                    <button type="button" onclick="closeRemboursementModal()" class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center text-white transition-colors cursor-pointer">
                        <i class="fas fa-times text-sm"></i>
                    </button>
                </div>
                <div class="mt-3 p-3 bg-white/10 backdrop-blur-xs rounded-xl flex items-center justify-between text-xs font-bold">
                    <span class="text-blue-100">Caution disponible :</span>
                    <span id="modalCautionDisponible" class="text-white text-sm font-black"></span>
                </div>
            </div>
            <form id="remboursementForm" method="POST" action="" class="p-6">
                @csrf
                <input type="hidden" id="montantRembourseHidden" name="montant_rembourse">
                <div class="space-y-4">
                    <div>
                        <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest block mb-1">Montant retenu par l'agence</label>
                        <input type="number" step="0.01" id="montantRetenu" name="montant_retenu" min="0" class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all" oninput="updateMontantRembourse()" required>
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest block mb-1">Montant à rendre au locataire</label>
                        <input type="number" step="0.01" id="montantRembourse" min="0" disabled class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-black text-slate-800">
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest block mb-1">Date de remboursement</label>
                        <input type="date" id="dateRemboursement" name="date_remboursement" class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all" required>
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest block mb-1">Notes (facultatif)</label>
                        <textarea id="notesCaution" name="notes" rows="2" placeholder="Commentaire optionnel..." class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl text-sm font-medium focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"></textarea>
                    </div>
                </div>
                <div class="mt-6 flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                    <button type="button" onclick="closeRemboursementModal()" class="px-4 py-2.5 text-xs font-black text-slate-500 hover:text-slate-800 transition-colors uppercase cursor-pointer">Annuler</button>
                    <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-black rounded-xl shadow-lg shadow-blue-500/20 hover:shadow-xl transition-all uppercase flex items-center gap-2 cursor-pointer">
                        <i class="fas fa-check"></i>
                        <span>Enregistrer</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal 2: Créer un Reversement Individuel (Style uniforme avec le reste de l'application) -->
    <div id="reversementModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden items-center justify-center p-4 overflow-y-auto">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full mx-auto my-auto overflow-hidden relative">
            <div class="p-6 bg-gradient-to-r from-blue-600 to-indigo-600 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-[10px] font-black uppercase tracking-widest text-blue-200">{{ $moisFormat }}</span>
                        <h3 class="text-lg font-black uppercase tracking-tight mt-0.5" id="modalProprietaireNom">Reversement Propriétaire</h3>
                    </div>
                    <button type="button" onclick="closeReversementModal()" class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center text-white transition-colors cursor-pointer">
                        <i class="fas fa-times text-sm"></i>
                    </button>
                </div>
            </div>
            <form id="reversementForm" method="POST" action="" class="p-6">
                @csrf
                <input type="hidden" name="periode" value="{{ $moisFormat }}">
                <input type="hidden" id="montantTotalLoyersHidden" name="montant_total_loyers" value="0">
                <input type="hidden" id="commissionAgenceHidden" name="commission_agence" value="0">
                <input type="hidden" id="montantDepensesHidden" name="montant_depenses" value="0">

                <!-- Récapitulatif financier du mois pour ce propriétaire -->
                <div class="p-3.5 bg-slate-50 border border-slate-100 rounded-xl mb-4 text-xs space-y-1.5">
                    <div class="flex justify-between text-slate-600 font-medium">
                        <span>Loyers collectés ({{ $moisFormat }}) :</span>
                        <span class="font-bold text-slate-800" id="modalLoyersCollectes">0</span>
                    </div>
                    <div class="flex justify-between text-slate-600 font-medium">
                        <span>Commissions agence :</span>
                        <span class="font-bold text-amber-600" id="modalCommissions">0</span>
                    </div>
                    <div class="flex justify-between text-slate-600 font-medium">
                        <span>Dépenses déduites :</span>
                        <span class="font-bold text-red-500" id="modalDepenses">0</span>
                    </div>
                    <div class="flex justify-between text-slate-800 font-black pt-1.5 border-t border-slate-200">
                        <span>Solde net restant dû :</span>
                        <span class="text-emerald-600 font-black" id="modalSoldeNet">0</span>
                    </div>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest block mb-1">Montant du reversement ({{ Auth::user()->agence->devise }})</label>
                        <input type="number" step="0.01" id="montantReversement" name="montant" min="0.01" class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl text-sm font-black text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all" required>
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest block mb-1">Date effective du reversement</label>
                        <input type="date" id="dateReversement" name="date_reversement" class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all" required>
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest block mb-1">Compte bancaire / Caisse débité</label>
                        <select name="compte_bancaire_id" class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                            <option value="">Compte principal de l'agence / Caisse</option>
                            @foreach($comptesBancaires as $cb)
                            <option value="{{ $cb->id }}">{{ $cb->nom_banque }} - {{ $cb->numero_compte }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest block mb-1">Référence / Mode (facultatif)</label>
                        <input type="text" id="referenceReversement" name="reference" placeholder="Ex: Virement bancaire #REF123" class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl text-sm font-medium focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest block mb-1">Notes & Observations (facultatif)</label>
                        <textarea id="notesReversement" name="notes" rows="2" placeholder="Commentaire optionnel..." class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl text-sm font-medium focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"></textarea>
                    </div>
                </div>
                <div class="mt-6 flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                    <button type="button" onclick="closeReversementModal()" class="px-4 py-2.5 text-xs font-black text-slate-500 hover:text-slate-800 transition-colors uppercase cursor-pointer">Annuler</button>
                    <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-black rounded-xl shadow-lg shadow-blue-500/20 hover:shadow-xl transition-all uppercase flex items-center gap-2 cursor-pointer">
                        <i class="fas fa-check"></i>
                        <span>Enregistrer</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal 3: Reversement en Masse (Style uniforme avec le reste de l'application) -->
    <div id="batchReversementModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden items-center justify-center p-4 overflow-y-auto">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full mx-auto my-auto overflow-hidden relative">
            <div class="p-6 bg-gradient-to-r from-blue-600 to-indigo-600 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <span class="text-[10px] font-black uppercase tracking-widest text-blue-200">{{ $moisFormat }}</span>
                        <h3 class="text-lg font-black uppercase tracking-tight mt-0.5 flex items-center gap-2">
                            <i class="fas fa-layer-group"></i> Reversement Groupé
                        </h3>
                    </div>
                    <button type="button" onclick="closeBatchReversementModal()" class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center text-white transition-colors cursor-pointer">
                        <i class="fas fa-times text-sm"></i>
                    </button>
                </div>
            </div>

            <form id="batchReversementForm" method="POST" action="{{ route('finance.reversements.batch') }}" class="p-6">
                @csrf
                <div id="batchHiddenInputsContainer"></div>

                <!-- Récapitulatif du lot -->
                <div class="p-3.5 bg-slate-50 border border-slate-100 rounded-xl mb-4 text-xs space-y-1.5">
                    <div class="flex justify-between text-slate-600 font-medium">
                        <span>Période concernée :</span>
                        <span class="font-bold text-slate-800">{{ $moisFormat }}</span>
                    </div>
                    <div class="flex justify-between text-slate-600 font-medium">
                        <span>Bénéficiaires sélectionnés :</span>
                        <span class="font-bold text-slate-800" id="batchModalCount">0</span>
                    </div>
                    <div class="flex justify-between text-slate-800 font-black pt-1.5 border-t border-slate-200">
                        <span>Montant total du lot :</span>
                        <span class="text-blue-600 font-black text-sm" id="batchModalFooterTotal">0</span>
                    </div>
                </div>

                <!-- Liste compacte des propriétaires -->
                <div class="mb-4">
                    <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest block mb-1">Détail des bénéficiaires</label>
                    <div class="max-h-28 overflow-y-auto rounded-xl border border-slate-200 divide-y divide-slate-100 bg-slate-50/50 p-2 text-xs" id="batchModalList"></div>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest block mb-1">Date effective du reversement</label>
                        <input type="date" id="batchDateReversement" name="date_reversement" class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all" required>
                    </div>
                    <input type="hidden" name="periode" value="{{ $moisFormat }}">
                    <div>
                        <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest block mb-1">Compte bancaire / Caisse de débit</label>
                        <select name="compte_bancaire_id" class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                            <option value="">Compte principal de l'agence / Caisse</option>
                            @foreach($comptesBancaires as $cb)
                            <option value="{{ $cb->id }}">{{ $cb->nom_banque }} - {{ $cb->numero_compte }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest block mb-1">Référence du lot (facultatif)</label>
                        <input type="text" name="reference" value="LOT-REV-{{ date('Ymd-His') }}" class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl text-sm font-medium focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                    </div>
                    <div>
                        <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest block mb-1">Notes & Observations (facultatif)</label>
                        <textarea name="notes" rows="2" placeholder="Commentaire optionnel..." class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl text-sm font-medium focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"></textarea>
                    </div>
                </div>

                <div class="mt-6 flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                    <button type="button" onclick="closeBatchReversementModal()" class="px-4 py-2.5 text-xs font-black text-slate-500 hover:text-slate-800 transition-colors uppercase cursor-pointer">Annuler</button>
                    <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-black rounded-xl shadow-lg shadow-blue-500/20 hover:shadow-xl transition-all uppercase flex items-center gap-2 cursor-pointer">
                        <i class="fas fa-check-double"></i>
                        <span>Exécuter le lot</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Tab System
            const tabs = ['cautions', 'proprietaires', 'creances', 'reversements', 'transactions'];

            tabs.forEach(tabName => {
                const btn = document.getElementById(`tab-${tabName}`);
                if (!btn) return;
                btn.addEventListener('click', function() {
                    tabs.forEach(t => {
                        const otherBtn = document.getElementById(`tab-${t}`);
                        const otherPane = document.getElementById(`tab-content-${t}`);
                        if (otherBtn) {
                            otherBtn.classList.remove('bg-blue-600', 'text-white', 'shadow-sm');
                            otherBtn.classList.add('text-slate-600', 'hover:text-slate-900');
                        }
                        if (otherPane) {
                            otherPane.classList.remove('active-tab-pane');
                            otherPane.classList.add('hidden');
                        }
                    });
                    this.classList.remove('text-slate-600', 'hover:text-slate-900');
                    this.classList.add('bg-blue-600', 'text-white', 'shadow-sm');
                    const targetPane = document.getElementById(`tab-content-${tabName}`);
                    if (targetPane) {
                        targetPane.classList.remove('hidden');
                        targetPane.classList.add('active-tab-pane');
                    }
                });
            });

            // Filtre par mois
            document.getElementById('monthSelect')?.addEventListener('change', function() {
                document.getElementById('monthFilterForm')?.submit();
            });

            // Base URLs
            const rembourserCautionBaseUrl = "{{ url('finance/contrats') }}";
            const creerReversementBaseUrl = "{{ url('finance/proprietaires') }}";
            const devise = "{{ Auth::user()->agence->devise }}";

            // ========================================================
            // INITIALISATION DES GRAPHIQUES APEXCHARTS
            // ========================================================
            const labels12m = @json($analytics['labels']);

            // 1. Évolution Mensuelle des Flux Locatifs (Area Chart)
            const chartFluxEl = document.querySelector("#chartEvolutionFlux");
            if (chartFluxEl && window.ApexCharts) {
                new ApexCharts(chartFluxEl, {
                    series: [
                        {
                            name: 'Loyers Émis',
                            data: @json($analytics['loyers_emis'])
                        },
                        {
                            name: 'Loyers Encaissés',
                            data: @json($analytics['loyers_encaisses'])
                        },
                        {
                            name: 'Reversements Bailleurs',
                            data: @json($analytics['reversements'])
                        }
                    ],
                    chart: {
                        type: 'area',
                        height: 280,
                        toolbar: { show: false },
                        fontFamily: 'Inter, sans-serif',
                        zoom: { enabled: false }
                    },
                    colors: ['#2563eb', '#10b981', '#f59e0b'],
                    stroke: {
                        curve: 'smooth',
                        width: 2.5
                    },
                    fill: {
                        type: 'gradient',
                        gradient: {
                            shadeIntensity: 1,
                            opacityFrom: 0.35,
                            opacityTo: 0.05,
                            stops: [0, 90, 100]
                        }
                    },
                    dataLabels: { enabled: false },
                    xaxis: {
                        categories: labels12m,
                        axisBorder: { show: false },
                        axisTicks: { show: false },
                        labels: {
                            style: {
                                colors: '#94a3b8',
                                fontSize: '11px',
                                fontWeight: 600
                            }
                        }
                    },
                    yaxis: {
                        labels: {
                            style: {
                                colors: '#94a3b8',
                                fontSize: '11px',
                                fontWeight: 600
                            },
                            formatter: function(val) {
                                if (val >= 1000000) return (val / 1000000).toFixed(1) + 'M';
                                if (val >= 1000) return (val / 1000).toFixed(0) + 'k';
                                return val ? val.toLocaleString() : '0';
                            }
                        }
                    },
                    grid: {
                        borderColor: '#f1f5f9',
                        strokeDashArray: 4,
                        padding: { left: 10, right: 10, top: 0, bottom: 0 }
                    },
                    legend: {
                        position: 'top',
                        horizontalAlign: 'right',
                        fontSize: '11px',
                        fontWeight: 600,
                        markers: { radius: 12 }
                    },
                    tooltip: {
                        theme: 'light',
                        y: {
                            formatter: function(val) {
                                return parseInt(val || 0).toLocaleString('fr-FR') + ' ' + devise;
                            }
                        }
                    }
                }).render();
            }

            // 2. Rentabilité & Résultat Net Agence (Grouped Bar Chart)
            const chartRentabiliteEl = document.querySelector("#chartRentabiliteAgence");
            if (chartRentabiliteEl && window.ApexCharts) {
                new ApexCharts(chartRentabiliteEl, {
                    series: [
                        {
                            name: 'Revenus Agence',
                            data: @json($analytics['revenus_agence'])
                        },
                        {
                            name: 'Dépenses Agence',
                            data: @json($analytics['depenses_agence'])
                        },
                        {
                            name: 'Marge Nette',
                            data: @json($analytics['marge_nette'])
                        }
                    ],
                    chart: {
                        type: 'bar',
                        height: 280,
                        toolbar: { show: false },
                        fontFamily: 'Inter, sans-serif'
                    },
                    plotOptions: {
                        bar: {
                            horizontal: false,
                            columnWidth: '55%',
                            borderRadius: 4
                        }
                    },
                    colors: ['#4f46e5', '#f43f5e', '#10b981'],
                    dataLabels: { enabled: false },
                    stroke: {
                        show: true,
                        width: 2,
                        colors: ['transparent']
                    },
                    xaxis: {
                        categories: labels12m,
                        axisBorder: { show: false },
                        axisTicks: { show: false },
                        labels: {
                            style: {
                                colors: '#94a3b8',
                                fontSize: '11px',
                                fontWeight: 600
                            }
                        }
                    },
                    yaxis: {
                        labels: {
                            style: {
                                colors: '#94a3b8',
                                fontSize: '11px',
                                fontWeight: 600
                            },
                            formatter: function(val) {
                                if (val >= 1000000) return (val / 1000000).toFixed(1) + 'M';
                                if (val >= 1000) return (val / 1000).toFixed(0) + 'k';
                                return val ? val.toLocaleString() : '0';
                            }
                        }
                    },
                    grid: {
                        borderColor: '#f1f5f9',
                        strokeDashArray: 4,
                        padding: { left: 10, right: 10, top: 0, bottom: 0 }
                    },
                    legend: {
                        position: 'top',
                        horizontalAlign: 'right',
                        fontSize: '11px',
                        fontWeight: 600,
                        markers: { radius: 12 }
                    },
                    tooltip: {
                        theme: 'light',
                        y: {
                            formatter: function(val) {
                                return parseInt(val || 0).toLocaleString('fr-FR') + ' ' + devise;
                            }
                        }
                    }
                }).render();
            }

            // Modal Remboursement Caution
            let currentCautionDisponible = 0;

            window.openRemboursementModal = function(contratId, cautionDisponible) {
                currentCautionDisponible = cautionDisponible;
                document.getElementById('modalCautionDisponible').textContent = cautionDisponible.toLocaleString('fr-FR') + ' ' + devise;
                document.getElementById('montantRetenu').value = 0;
                document.getElementById('montantRembourse').value = cautionDisponible;
                document.getElementById('montantRembourseHidden').value = cautionDisponible;
                document.getElementById('dateRemboursement').value = new Date().toISOString().split('T')[0];
                document.getElementById('remboursementForm').action = rembourserCautionBaseUrl + '/' + contratId + '/rembourser-caution';
                document.getElementById('remboursementModal').classList.remove('hidden');
                document.getElementById('remboursementModal').classList.add('flex');
            };

            window.closeRemboursementModal = function() {
                document.getElementById('remboursementModal').classList.add('hidden');
                document.getElementById('remboursementModal').classList.remove('flex');
            };

            window.updateMontantRembourse = function() {
                const retenu = parseFloat(document.getElementById('montantRetenu').value) || 0;
                const montantRembourse = Math.max(0, currentCautionDisponible - retenu).toFixed(2);
                document.getElementById('montantRembourse').value = montantRembourse;
                document.getElementById('montantRembourseHidden').value = montantRembourse;
            };

            if (document.getElementById('montantRembourseHidden')) {
                document.getElementById('montantRembourseHidden').value = currentCautionDisponible || 0;
            }

            // Modal Reversement Propriétaire Individuel
            window.openReversementModal = function(proprietaireId, solde, nom, loyers, comm, dep) {
                document.getElementById('modalProprietaireNom').textContent = nom || ('Propriétaire #' + proprietaireId);
                document.getElementById('modalLoyersCollectes').textContent = (loyers || 0).toLocaleString('fr-FR') + ' ' + devise;
                document.getElementById('modalCommissions').textContent = (comm || 0).toLocaleString('fr-FR') + ' ' + devise;
                document.getElementById('modalDepenses').textContent = (dep || 0).toLocaleString('fr-FR') + ' ' + devise;
                document.getElementById('modalSoldeNet').textContent = (solde || 0).toLocaleString('fr-FR') + ' ' + devise;
                
                document.getElementById('montantReversement').value = solde || 0;
                document.getElementById('montantTotalLoyersHidden').value = loyers || 0;
                document.getElementById('commissionAgenceHidden').value = comm || 0;
                document.getElementById('montantDepensesHidden').value = dep || 0;

                document.getElementById('dateReversement').value = new Date().toISOString().split('T')[0];
                document.getElementById('reversementForm').action = creerReversementBaseUrl + '/' + proprietaireId + '/reversement';
                document.getElementById('reversementModal').classList.remove('hidden');
                document.getElementById('reversementModal').classList.add('flex');
            };

            window.closeReversementModal = function() {
                document.getElementById('reversementModal').classList.add('hidden');
                document.getElementById('reversementModal').classList.remove('flex');
            };

            // Reversement en Masse (Batch Payout)
            window.updateBatchSelection = function() {
                const checkboxes = document.querySelectorAll('.batch-proprio-checkbox:checked');
                const totalCheckboxes = document.querySelectorAll('.batch-proprio-checkbox');
                const masterCheckbox = document.getElementById('selectAllProprietairesCheckbox');
                const batchBtn = document.getElementById('btnBatchReversement');
                const countSpan = document.getElementById('batchSelectedCount');

                let totalAmount = 0;
                checkboxes.forEach(cb => {
                    totalAmount += parseFloat(cb.dataset.solde) || 0;
                });

                if (countSpan) countSpan.textContent = checkboxes.length;

                if (masterCheckbox && totalCheckboxes.length > 0) {
                    masterCheckbox.checked = (checkboxes.length === totalCheckboxes.length);
                    masterCheckbox.indeterminate = (checkboxes.length > 0 && checkboxes.length < totalCheckboxes.length);
                }

                if (checkboxes.length > 0) {
                    if (batchBtn) {
                        batchBtn.disabled = false;
                        batchBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                        batchBtn.classList.add('hover:shadow-md');
                    }
                } else {
                    if (batchBtn) {
                        batchBtn.disabled = true;
                        batchBtn.classList.add('opacity-50', 'cursor-not-allowed');
                        batchBtn.classList.remove('hover:shadow-md');
                    }
                }
            };

            window.toggleSelectAllProprietaires = function(master) {
                const checkboxes = document.querySelectorAll('.batch-proprio-checkbox');
                checkboxes.forEach(cb => {
                    cb.checked = master.checked;
                });
                updateBatchSelection();
            };

            window.openBatchReversementModal = function() {
                const checkboxes = document.querySelectorAll('.batch-proprio-checkbox:checked');
                if (checkboxes.length === 0) return;

                const hiddenContainer = document.getElementById('batchHiddenInputsContainer');
                const listContainer = document.getElementById('batchModalList');
                hiddenContainer.innerHTML = '';
                listContainer.innerHTML = '';

                let grandTotal = 0;

                checkboxes.forEach((cb, index) => {
                    const id = cb.dataset.id;
                    const nom = cb.dataset.nom;
                    const solde = parseFloat(cb.dataset.solde) || 0;
                    const loyers = parseFloat(cb.dataset.loyers) || 0;
                    const comm = parseFloat(cb.dataset.comm) || 0;
                    const dep = parseFloat(cb.dataset.dep) || 0;

                    grandTotal += solde;

                    hiddenContainer.innerHTML += `
                        <input type="hidden" name="reversements[${index}][proprietaire_id]" value="${id}">
                        <input type="hidden" name="reversements[${index}][montant]" value="${solde}">
                        <input type="hidden" name="reversements[${index}][loyers]" value="${loyers}">
                        <input type="hidden" name="reversements[${index}][commissions]" value="${comm}">
                        <input type="hidden" name="reversements[${index}][depenses]" value="${dep}">
                    `;

                    const row = document.createElement('div');
                    row.className = 'py-1 flex items-center justify-between text-xs';
                    row.innerHTML = `
                        <span class="font-bold text-slate-800">${nom}</span>
                        <span class="font-black text-blue-600">${solde.toLocaleString('fr-FR')} ${devise}</span>
                    `;
                    listContainer.appendChild(row);
                });

                document.getElementById('batchModalCount').textContent = checkboxes.length + ' propriétaire(s)';
                document.getElementById('batchModalFooterTotal').textContent = grandTotal.toLocaleString('fr-FR') + ' ' + devise;
                document.getElementById('batchDateReversement').value = new Date().toISOString().split('T')[0];

                document.getElementById('batchReversementModal').classList.remove('hidden');
                document.getElementById('batchReversementModal').classList.add('flex');
            };

            window.closeBatchReversementModal = function() {
                document.getElementById('batchReversementModal').classList.add('hidden');
                document.getElementById('batchReversementModal').classList.remove('flex');
            };

            // Filtrage des créances par tranche d'arriéré
            window.filterCreances = function(tranche) {
                document.querySelectorAll('.creance-filter-btn').forEach(btn => {
                    btn.classList.remove('bg-blue-600', 'text-white', 'shadow-sm');
                    btn.classList.add('bg-slate-100', 'text-slate-600');
                });
                const activeBtn = document.getElementById(`filter-btn-${tranche}`);
                if (activeBtn) {
                    activeBtn.classList.remove('bg-slate-100', 'text-slate-600');
                    activeBtn.classList.add('bg-blue-600', 'text-white', 'shadow-sm');
                }

                const rows = document.querySelectorAll('.creance-row');
                rows.forEach(row => {
                    if (tranche === 'all' || row.dataset.tranche === tranche) {
                        row.classList.remove('hidden');
                    } else {
                        row.classList.add('hidden');
                    }
                });
            };

            // Copie fluide du message de relance
            window.copyRelanceMessage = function(text, btnElement) {
                navigator.clipboard.writeText(text).then(() => {
                    if (btnElement) {
                        const originalHtml = btnElement.innerHTML;
                        btnElement.innerHTML = '<i class="fas fa-check text-emerald-600 text-xs"></i>';
                        setTimeout(() => {
                            btnElement.innerHTML = originalHtml;
                        }, 1500);
                    }
                }).catch(err => {
                    alert("Message de relance :\n\n" + text);
                });
            };

            // Fermeture des modals au clic extérieur
            document.getElementById('remboursementModal')?.addEventListener('click', function(e) {
                if (e.target === this) closeRemboursementModal();
            });
            document.getElementById('reversementModal')?.addEventListener('click', function(e) {
                if (e.target === this) closeReversementModal();
            });
            document.getElementById('batchReversementModal')?.addEventListener('click', function(e) {
                if (e.target === this) closeBatchReversementModal();
            });
        });
    </script>
</x-app-layout>
