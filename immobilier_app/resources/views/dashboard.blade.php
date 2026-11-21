<x-app-layout>
    @php
        $userModules = $userModules ?? [];
        $isAdmin = Auth::user()->hasRole('admin');
        $isTeamMember = Auth::user()->hasRole('team_member');
        $showImmobilier = $isAdmin || in_array('immobilier', $userModules, true);
        $showCommercial = $isAdmin || in_array('commercial', $userModules, true);
        $showFinance = $isAdmin || in_array('finance', $userModules, true);
        $showMaintenance = $isAdmin || in_array('maintenance', $userModules, true);
        $showRelationClient = $isAdmin || in_array('relation_client', $userModules, true);
        $showProprietaires = $isAdmin || in_array('proprietaires', $userModules, true);
        $showReversements = $showImmobilier || $showProprietaires;
        $showCautions = $showFinance || $showImmobilier;
        $showRevenusAgence = $showFinance || $showImmobilier || $showCommercial;
        $showDepenses = $showFinance || $showCommercial;
        $showFinancierSection = $showReversements || $showCautions || $showRevenusAgence || $showDepenses;
        $showParcSection = $showImmobilier || $showProprietaires;
        $showSupportSection = $showMaintenance;
        $showCommercialSection = $showCommercial;
        $showRevenueStructureChart = $showRevenusAgence;
        $showReversementsChart = $showReversements;
        $showRevenueVsDepenses = $showDepenses;
        $showCAByType = $showImmobilier || $showCommercial || $showFinance;
        $showChartsSection = $showReversementsChart || $showRevenueStructureChart || $showRevenueVsDepenses || $showCAByType;
        $showMonthFilter = $showFinancierSection || $showParcSection || $showChartsSection;

        $moduleBadges = [
            'immobilier'     => ['label' => 'Immobilier', 'color' => 'bg-blue-100 text-blue-700'],
            'commercial'     => ['label' => 'Commercial', 'color' => 'bg-purple-100 text-purple-700'],
            'finance'        => ['label' => 'Finance', 'color' => 'bg-emerald-100 text-emerald-700'],
            'maintenance'    => ['label' => 'Maintenance', 'color' => 'bg-orange-100 text-orange-700'],
            'relation_client'=> ['label' => 'Relation Client', 'color' => 'bg-cyan-100 text-cyan-700'],
            'proprietaires'  => ['label' => 'Propriétaires', 'color' => 'bg-amber-100 text-amber-700'],
        ];
    @endphp

    <x-slot name="header">
        <div class="flex flex-col gap-2 w-full">
            <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-3">
                <div>
                    <h2 class="font-black text-xl md:text-2xl text-slate-900 tracking-tight uppercase">Tableau de Bord</h2>
                    <p class="text-[10px] text-slate-500 font-bold uppercase tracking-widest mt-1">
                        {{ Auth::user()->agence->nom }} <span class="mx-2 text-slate-300">•</span>
                        {{ Auth::user()->name }}
                    </p>
                    {{-- Badges des modules pour les membres d'équipe --}}
                    @if($isTeamMember && !empty($userModules))
                        <div class="flex flex-wrap gap-1 mt-2">
                            @foreach($userModules as $mod)
                                @if(isset($moduleBadges[$mod]))
                                    <span class="px-2 py-0.5 text-[9px] font-black rounded-full uppercase {{ $moduleBadges[$mod]['color'] }}">
                                        {{ $moduleBadges[$mod]['label'] }}
                                    </span>
                                @endif
                            @endforeach
                        </div>
                    @endif
                </div>
                @if($showMonthFilter)
                    <div class="w-full md:w-auto">
                        <x-month-picker 
                            :value="$moisInputValue" 
                            :label="$moisFormat" 
                            :action="route('dashboard')" 
                            :resetUrl="route('dashboard')" 
                        />
                    </div>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">

        @if(session('success'))
            <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-xl flex items-center justify-between gap-3 text-emerald-800">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600 flex-shrink-0">
                        <i class="fas fa-check text-sm"></i>
                    </div>
                    <p class="text-xs font-bold">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @if($errors->any())
            <div class="p-4 bg-red-50 border border-red-200 rounded-xl text-red-800">
                <div class="flex items-center gap-2 font-black text-xs uppercase tracking-wider mb-2 text-red-700">
                    <i class="fas fa-exclamation-triangle"></i>
                    <span>Erreur</span>
                </div>
                <ul class="text-xs font-medium space-y-1 list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if(!$isAdmin && empty($userModules))
            <div class="p-6 bg-white border border-slate-200 rounded-xl text-slate-700">
                <h3 class="font-black text-lg text-slate-900 mb-2">Aucun accès au tableau de bord</h3>
                <p class="text-sm text-slate-500">Votre compte n'est actuellement assigné à aucun module. Contactez votre
                    administrateur pour obtenir un accès.</p>
            </div>
        @endif

        @if($showFinancierSection)
            {{-- ================================================== --}}
            {{-- SECTION 1 : BANDEAU KPIs FINANCIERS (4 cartes) --}}
            {{-- ================================================== --}}
            <div class="grid grid-cols-2 xl:grid-cols-4 gap-3 md:gap-5">

                @if($showReversements)
                    {{-- Reversements Propriétaires --}}
                    <div class="premium-card p-5 bg-white overflow-hidden relative group">
                        <div
                            class="absolute -top-4 -right-4 w-24 h-24 bg-gradient-to-br from-amber-100 to-amber-50 rounded-full opacity-60 group-hover:scale-125 transition-transform duration-500">
                        </div>
                        <div class="relative z-10">
                            <div class="flex items-center justify-between mb-3">
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">A Reverser aux
                                    Propriétaires</p>
                                <div
                                    class="w-9 h-9 rounded-xl bg-gradient-to-br from-amber-500 to-amber-600 flex items-center justify-center shadow-lg shadow-amber-200">
                                    <i class="fas fa-hand-holding-usd text-white text-sm"></i>
                                </div>
                            </div>
                            <h3 class="text-2xl font-black text-slate-900 mb-1">
                                {{ number_format($stats['total_a_reverser'], 0, ',', ' ') }}
                                <span class="text-xs font-bold text-slate-400 ml-1">{{ Auth::user()->agence->devise }}</span>
                            </h3>
                            <div class="flex items-center gap-2">
                                <span
                                    class="px-2 py-0.5 bg-amber-50 text-amber-600 text-[9px] font-black rounded-full uppercase">{{ $moisFormat }}</span>
                                <p class="text-[10px] text-slate-400 font-medium">Loyers seulement</p>
                            </div>
                            <div class="mt-4 -mb-2">
                                <div id="sparklineReversements" class="w-full h-12"></div>
                            </div>
                        </div>
                    </div>
                @endif

                @if($showCautions)
                    {{-- Cautions Détenues --}}
                    <div class="premium-card p-5 bg-white overflow-hidden relative group">
                        <div
                            class="absolute -top-4 -right-4 w-24 h-24 bg-gradient-to-br from-indigo-100 to-indigo-50 rounded-full opacity-60 group-hover:scale-125 transition-transform duration-500">
                        </div>
                        <div class="relative z-10">
                            <div class="flex items-center justify-between mb-3">
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Cautions Détenues</p>
                                <div
                                    class="w-9 h-9 rounded-xl bg-gradient-to-br from-indigo-500 to-indigo-600 flex items-center justify-center shadow-lg shadow-indigo-200">
                                    <i class="fas fa-shield-alt text-white text-sm"></i>
                                </div>
                            </div>
                            <h3 class="text-2xl font-black text-slate-900 mb-1">
                                {{ number_format($stats['total_caution_detenue'], 0, ',', ' ') }}
                                <span class="text-xs font-bold text-slate-400 ml-1">{{ Auth::user()->agence->devise }}</span>
                            </h3>
                            <div class="flex items-center gap-2">
                                <span
                                    class="px-2 py-0.5 bg-indigo-50 text-indigo-600 text-[9px] font-black rounded-full uppercase">Fiduciaire</span>
                                @if($cautionsARendre->count() > 0)
                                    <span class="px-2 py-0.5 bg-red-50 text-red-600 text-[9px] font-black rounded-full uppercase">
                                        {{ $cautionsARendre->count() }} à rendre
                                    </span>
                                @endif
                            </div>
                            <div class="mt-4 pt-3 border-t border-slate-50">
                                <p class="text-[8px] font-black text-slate-300 uppercase tracking-tighter">Reçu ce mois :
                                    {{ number_format($stats['flux_caution_mois'], 0, ',', ' ') }}
                                    {{ Auth::user()->agence->devise }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                @if($showRevenusAgence)
                    {{-- Revenus Agence --}}
                    <div class="premium-card p-5 bg-white overflow-hidden relative group">
                        <div
                            class="absolute -top-4 -right-4 w-24 h-24 bg-gradient-to-br from-emerald-100 to-emerald-50 rounded-full opacity-60 group-hover:scale-125 transition-transform duration-500">
                        </div>
                        <div class="relative z-10">
                            <div class="flex items-center justify-between mb-3">
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Revenus Agence</p>
                                <div
                                    class="w-9 h-9 rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-600 flex items-center justify-center shadow-lg shadow-emerald-200">
                                    <i class="fas fa-wallet text-white text-sm"></i>
                                </div>
                            </div>
                            <h3 class="text-2xl font-black text-emerald-700 mb-1">
                                {{ number_format($stats['ca_mensuel'], 0, ',', ' ') }}
                                <span class="text-xs font-bold text-slate-400 ml-1">{{ Auth::user()->agence->devise }}</span>
                            </h3>
                            <div class="flex flex-col gap-1">
                                <div class="flex items-center justify-between">
                                    <span class="text-[9px] font-bold text-slate-400 uppercase">Commissions:</span>
                                    <span
                                        class="text-[9px] font-black text-emerald-600">{{ number_format($stats['ca_loyers_mois'], 0, ',', ' ') }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-[9px] font-bold text-slate-400 uppercase">Frais Dossier:</span>
                                    <span
                                        class="text-[9px] font-black text-cyan-600">{{ number_format($stats['ca_frais_dossier_mois'], 0, ',', ' ') }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-[9px] font-bold text-slate-400 uppercase">Visites:</span>
                                    <span
                                        class="text-[9px] font-black text-purple-600">{{ number_format($stats['ca_visites_mois'], 0, ',', ' ') }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-[9px] font-bold text-slate-400 uppercase">Retenues Caution:</span>
                                    <span
                                        class="text-[9px] font-black text-orange-600">{{ number_format($stats['ca_retenues_caution_mois'], 0, ',', ' ') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                @if($showDepenses)
                    {{-- Dépenses --}}
                    <div class="premium-card p-5 bg-white overflow-hidden relative group">
                        <div
                            class="absolute -top-4 -right-4 w-24 h-24 bg-gradient-to-br from-red-100 to-red-50 rounded-full opacity-60 group-hover:scale-125 transition-transform duration-500">
                        </div>
                        <div class="relative z-10">
                            <div class="flex items-center justify-between mb-3">
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Dépenses du Mois</p>
                                <div
                                    class="w-9 h-9 rounded-xl bg-gradient-to-br from-red-500 to-red-600 flex items-center justify-center shadow-lg shadow-red-200">
                                    <i class="fas fa-money-bill-wave text-white text-sm"></i>
                                </div>
                            </div>
                            <h3 class="text-2xl font-black text-red-600 mb-1">
                                {{ number_format($stats['depenses_mois'], 0, ',', ' ') }}
                                <span class="text-xs font-bold text-slate-400 ml-1">{{ Auth::user()->agence->devise }}</span>
                            </h3>
                            <div class="flex items-center gap-2">
                                <span
                                    class="px-2 py-0.5 bg-slate-100 text-slate-600 text-[9px] font-black rounded-full uppercase">Ce
                                    mois</span>
                                <p class="text-[10px] text-slate-400 font-medium">{{ $stats['factures_impayees'] }} impayées</p>
                            </div>
                            <div class="mt-4 -mb-2">
                                <div id="sparklineDepenses" class="w-full h-12"></div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>





            {{-- ================================================== --}}
            {{-- SECTION 1.5 : Cautions à Rendre (Si existantes) --}}
            {{-- ================================================== --}}
            @if($showFinancierSection && $cautionsARendre->count() > 0)
                <div class="premium-card p-6 bg-white">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="font-black text-base text-red-700 uppercase tracking-tight">⚠️ Cautions à Rendre</h3>
                            <p class="text-[10px] text-slate-400 font-bold mt-1">Contrats terminés, caution non remboursée</p>
                        </div>
                        <span class="px-3 py-1 bg-red-100 text-red-700 text-xs font-black rounded-full">
                            {{ $cautionsARendre->count() }} locataires
                        </span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-slate-100 text-left">
                                    <th class="text-xs font-black text-slate-500 uppercase whitespace-nowrap px-6 py-5">
                                        Locataire</th>
                                    <th class="text-xs font-black text-slate-500 uppercase whitespace-nowrap px-6 py-5">Bien
                                    </th>
                                    <th class="text-xs font-black text-slate-500 uppercase whitespace-nowrap px-6 py-5">Fin
                                        Contrat</th>
                                    <th class="text-xs font-black text-slate-500 uppercase whitespace-nowrap px-6 py-5">Montant
                                        à Rendre</th>
                                    <th class="text-xs font-black text-slate-500 uppercase whitespace-nowrap px-6 py-5 text-right">
                                        Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200">
                                @foreach($cautionsARendre as $contrat)
                                    <tr class="hover:bg-slate-50 transition-colors">
                                        <td class="font-bold text-slate-800 whitespace-nowrap px-6 py-5">
                                            {{ $contrat->locataire->user->name ?? 'N/A' }}
                                        </td>
                                        <td class="text-slate-600 whitespace-nowrap px-6 py-5">
                                            {{ $contrat->bien->nom_bien ?? 'N/A' }}
                                        </td>
                                        <td class="text-slate-500 font-medium whitespace-nowrap px-6 py-5">
                                            {{ $contrat->date_fin ? $contrat->date_fin->format('d/m/Y') : 'N/A' }}
                                        </td>
                                        <td class="font-black text-red-600 whitespace-nowrap px-6 py-5">
                                            {{ number_format($contrat->a_rendre, 0, ',', ' ') }} {{ Auth::user()->agence->devise }}
                                        </td>
                                        <td class="text-right whitespace-nowrap px-6 py-5">
                                            <button type="button"
                                                onclick="openRemboursementModal({{ $contrat->id }}, {{ $contrat->a_rendre }}, '{{ addslashes($contrat->locataire->user->name ?? 'Locataire') }}', '{{ addslashes($contrat->bien->nom_bien ?? 'Bien') }}')"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-red-50 hover:bg-red-600 text-red-600 hover:text-white rounded-lg text-[10px] font-black uppercase tracking-wider transition-all duration-200 shadow-xs cursor-pointer group">
                                                <i class="fas fa-hand-holding-usd text-xs group-hover:scale-110 transition-transform"></i>
                                                <span>Rendre la caution</span>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Modal: Remboursement Caution (Dashboard) -->
                <div id="remboursementModal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden items-center justify-center p-4 overflow-y-auto">
                    <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full mx-auto my-auto overflow-hidden relative">
                        <div class="p-6 bg-gradient-to-r from-red-600 to-rose-700 text-white">
                            <div class="flex items-center justify-between">
                                <div>
                                    <span class="text-[10px] font-black uppercase tracking-widest text-red-200">Restitution de caution</span>
                                    <h3 class="text-lg font-black uppercase tracking-tight mt-0.5">Rembourser la caution</h3>
                                </div>
                                <button type="button" onclick="closeRemboursementModal()" class="w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center text-white transition-colors cursor-pointer">
                                    <i class="fas fa-times text-sm"></i>
                                </button>
                            </div>
                            <div class="mt-3 p-3 bg-white/10 backdrop-blur-xs rounded-xl flex items-center justify-between text-xs font-bold">
                                <span class="text-red-100">Caution disponible :</span>
                                <span id="modalCautionDisponible" class="text-white text-sm font-black"></span>
                            </div>
                            <div id="modalLocataireInfo" class="mt-2 text-[11px] text-red-100/90 hidden font-bold"></div>
                        </div>
                        <form id="remboursementForm" method="POST" action="" class="p-6">
                            @csrf
                            <input type="hidden" id="montantRembourseHidden" name="montant_rembourse">
                            <div class="space-y-4">
                                <div>
                                    <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest block mb-1">Montant retenu par l'agence</label>
                                    <input type="number" step="0.01" id="montantRetenu" name="montant_retenu" min="0" class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all" oninput="updateMontantRembourse()" required>
                                    <p class="text-[10px] text-slate-400 mt-1 font-medium">Dégradations ou réparations (0 si aucun montant retenu)</p>
                                </div>
                                <div>
                                    <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest block mb-1">Montant à reverser au locataire</label>
                                    <input type="number" step="0.01" id="montantRembourse" min="0" disabled class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-black text-slate-800">
                                </div>
                                <div>
                                    <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest block mb-1">Date de remboursement</label>
                                    <input type="date" id="dateRemboursement" name="date_remboursement" class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl text-sm font-semibold focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all" required>
                                </div>
                                <div>
                                    <label class="text-[10px] font-black text-slate-500 uppercase tracking-widest block mb-1">Notes / Motif (facultatif)</label>
                                    <textarea id="notesCaution" name="notes" rows="2" placeholder="Ex: État des lieux de sortie conforme..." class="w-full px-3.5 py-2.5 border border-slate-200 rounded-xl text-sm font-medium focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-all"></textarea>
                                </div>
                            </div>
                            <div class="mt-6 flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                                <button type="button" onclick="closeRemboursementModal()" class="px-4 py-2.5 text-xs font-black text-slate-500 hover:text-slate-800 transition-colors uppercase cursor-pointer">Annuler</button>
                                <button type="submit" class="px-5 py-2.5 bg-gradient-to-r from-red-600 to-rose-700 hover:from-red-700 hover:to-rose-800 text-white text-xs font-black rounded-xl shadow-lg shadow-red-500/20 hover:shadow-xl transition-all uppercase flex items-center gap-2 cursor-pointer">
                                    <i class="fas fa-check"></i>
                                    <span>Confirmer le remboursement</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            @endif
        @endif

        {{-- ================================================== --}}
        {{-- SECTION 2 : JAUGES RADIALES (3 indicateurs) --}}
        {{-- ================================================== --}}
        @if($showParcSection || $showImmobilier || $showFinance || $showCommercial)
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">

                @if($showParcSection)
                    {{-- Taux d'Occupation --}}
                    <div class="premium-card p-6 bg-white">
                        <div class="flex items-center justify-between mb-2">
                            <div>
                                <h3 class="font-black text-sm text-slate-900 uppercase tracking-tight">Occupation du Parc</h3>
                                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-0.5">Disponibilité
                                    des biens</p>
                            </div>
                            <span class="text-[10px] font-black text-blue-600 bg-blue-50 px-2 py-1 rounded uppercase">
                                {{ $stats['total_louees'] }}/{{ $stats['total_unites'] }} biens
                            </span>
                        </div>
                        <div id="gaugeOccupation" class="w-full" style="min-height: 220px;"></div>
                        <div class="grid grid-cols-2 gap-2 mt-2">
                            <div class="text-center p-2 bg-blue-50 rounded-lg border border-blue-100">
                                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Loués</p>
                                <p class="text-sm font-black text-blue-600">{{ $stats['total_louees'] }}</p>
                            </div>
                            <div class="text-center p-2 bg-slate-50 rounded-lg border border-slate-100">
                                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Disponibles</p>
                                <p class="text-sm font-black text-slate-500">
                                    {{ $stats['total_unites'] - $stats['total_louees'] }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                @if($showImmobilier || $showFinance)
                    {{-- Taux de Recouvrement --}}
                    <div class="premium-card p-6 bg-white">
                        <div class="flex items-center justify-between mb-2">
                            <div>
                                <h3 class="font-black text-sm text-slate-900 uppercase tracking-tight">Recouvrement</h3>
                                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-0.5">Encaissements
                                    des loyers</p>
                            </div>
                            <span
                                class="text-[10px] font-black {{ $stats['taux_recouvrement'] >= 80 ? 'text-emerald-600 bg-emerald-50' : 'text-red-600 bg-red-50' }} px-2 py-1 rounded uppercase">
                                Ce mois
                            </span>
                        </div>
                        <div id="gaugeRecouvrement" class="w-full" style="min-height: 220px;"></div>
                        <div class="grid grid-cols-3 gap-2 mt-2">
                            <div class="text-center p-2 bg-emerald-50 rounded-lg border border-emerald-100">
                                <p class="text-[9px] font-black text-slate-400 uppercase">Payées</p>
                                <p class="text-sm font-black text-emerald-600">{{ $performance_paiement['payees'] }}</p>
                            </div>
                            <div class="text-center p-2 bg-amber-50 rounded-lg border border-amber-100">
                                <p class="text-[9px] font-black text-slate-400 uppercase">Partielles</p>
                                <p class="text-sm font-black text-amber-600">{{ $performance_paiement['partielles'] }}</p>
                            </div>
                            <div class="text-center p-2 bg-red-50 rounded-lg border border-red-100">
                                <p class="text-[9px] font-black text-slate-400 uppercase">Impayées</p>
                                <p class="text-sm font-black text-red-600">{{ $performance_paiement['impayees'] }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                @if($showCommercial)
                    {{-- Taux de Conversion --}}
                    <div class="premium-card p-6 bg-white">
                        <div class="flex items-center justify-between mb-2">
                            <div>
                                <h3 class="font-black text-sm text-slate-900 uppercase tracking-tight">Performance Commerciale
                                </h3>
                                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-0.5">Efficacité des
                                    visites</p>
                            </div>
                            <span class="text-[10px] font-black text-purple-600 bg-purple-50 px-2 py-1 rounded uppercase">
                                Ce mois
                            </span>
                        </div>
                        <div id="gaugeConversion" class="w-full" style="min-height: 220px;"></div>
                        <div class="grid grid-cols-2 gap-2 mt-2">
                            <div class="text-center p-2 bg-purple-50 rounded-lg border border-purple-100">
                                <p class="text-[9px] font-black text-slate-400 uppercase">Visites</p>
                                <p class="text-sm font-black text-purple-600">{{ $stats['visites_mois'] }}</p>
                            </div>
                            <div class="text-center p-2 bg-emerald-50 rounded-lg border border-emerald-100">
                                <p class="text-[9px] font-black text-slate-400 uppercase">Contrats signés</p>
                                <p class="text-sm font-black text-emerald-600">{{ $stats['contrats_signes_mois'] }}</p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        @endif

        {{-- ================================================== --}}

        {{-- ================================================== --}}
        {{-- SECTION 3 : GRAPHIQUES PRINCIPAUX --}}
        {{-- ================================================== --}}
        @if($showChartsSection)
            @if($showReversementsChart || $showRevenueStructureChart)
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    @if($showReversementsChart)
                        {{-- Graphe des Reversements Propriétaires --}}
                        <div class="premium-card p-6 bg-white">
                            <div class="flex items-center justify-between mb-6">
                                <div>
                                    <h3 class="font-black text-sm text-slate-900 uppercase tracking-tight">Reversements par
                                        Propriétaire</h3>
                                    <p class="text-[10px] text-slate-400 font-bold uppercase mt-0.5">Sommes à reverser (Loyer -
                                        Commission)</p>
                                </div>
                                <span
                                    class="text-[10px] font-black text-amber-600 bg-amber-50 px-2 py-1 rounded uppercase">{{ \Carbon\Carbon::now()->translatedFormat('F Y') }}</span>
                            </div>
                            <div id="proprietaireReversementChart" class="w-full" style="min-height: 350px;"></div>
                        </div>
                    @endif

                    @if($showRevenueStructureChart)
                        {{-- Graphe des Revenus Agence (Détails) --}}
                        <div class="premium-card p-6 bg-white">
                            <div class="flex items-center justify-between mb-6">
                                <div>
                                    <h3 class="font-black text-sm text-slate-900 uppercase tracking-tight">Structure des Revenus
                                        Agence</h3>
                                    <p class="text-[10px] text-slate-400 font-bold uppercase mt-0.5">Commissions + Frais de Visites
                                    </p>
                                </div>
                                <span
                                    class="text-[10px] font-black text-blue-600 bg-blue-50 px-2 py-1 rounded uppercase">Performance</span>
                            </div>
                            <div id="agenceRevenueStructureChart" class="w-full" style="min-height: 350px;"></div>
                        </div>
                    @endif
                </div>
            @endif

            @if($showRevenueVsDepenses || $showCAByType)
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    @if($showRevenueVsDepenses)
                        {{-- Revenus vs Dépenses (Barres empilées) --}}
                        <div class="lg:col-span-2 premium-card p-6 bg-white">
                            <div class="flex items-center justify-between mb-6">
                                <h3 class="font-black text-sm text-slate-900 uppercase tracking-tight">Revenus vs Dépenses</h3>
                                <span class="text-[10px] font-black text-blue-600 bg-blue-50 px-2 py-1 rounded uppercase">12
                                    derniers mois</span>
                            </div>
                            <div id="revenueVsExpenseChart" class="w-full" style="min-height: 320px;"></div>
                        </div>
                    @endif

                    @if($showCAByType)
                        {{-- Répartition du CA par Type de Bien (Donut) --}}
                        <div class="premium-card p-6 bg-white">
                            <div class="flex items-center justify-between mb-6">
                                <h3 class="font-black text-sm text-slate-900 uppercase tracking-tight">CA par Type de Bien</h3>
                            </div>
                            <div id="caByTypeChart" class="w-full" style="min-height: 300px;"></div>
                            @if(!empty($caParType))
                                <div class="space-y-2 mt-4">
                                    @foreach($caParType as $type => $montant)
                                        <div
                                            class="flex justify-between items-center px-3 py-2 bg-slate-50 rounded-lg border border-slate-100">
                                            <span class="text-xs font-bold text-slate-600 uppercase">{{ $type }}</span>
                                            <span class="text-xs font-black text-slate-900">{{ number_format($montant, 0, ',', ' ') }}
                                                {{ Auth::user()->agence->devise }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-center text-slate-400 text-sm italic mt-4">Aucun paiement enregistré pour l'instant.</p>
                            @endif
                        </div>
                    @endif
                </div>
            @endif
        @endif

        {{-- ================================================== --}}
        {{-- SECTION 4 : PARC IMMOBILIER & PARTENAIRES --}}
        {{-- ================================================== --}}
        @if($showParcSection || $showProprietaires)
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                @if($showParcSection)
                    {{-- Parc Immobilier --}}
                    <div class="premium-card p-6 bg-white">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="font-black text-sm text-slate-900 uppercase tracking-tight">Parc Immobilier</h3>
                            <a href="{{ route('biens.index') }}"
                                class="text-[10px] font-black text-slate-400 uppercase tracking-widest hover:text-slate-900 transition-colors">Détails</a>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="p-4 bg-slate-50 rounded-xl border border-slate-100 text-center">
                                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Familles</p>
                                <p class="text-xl font-black text-slate-900">{{ $stats['total_familles'] }}</p>
                            </div>
                            <div class="p-4 bg-slate-50 rounded-xl border border-slate-100 text-center">
                                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Biens</p>
                                <p class="text-xl font-black text-slate-900">{{ $stats['total_biens'] }}</p>
                            </div>
                            <div class="p-4 bg-blue-50 rounded-xl border border-blue-100 text-center">
                                <p class="text-[9px] font-black text-blue-400 uppercase tracking-widest mb-1">Contrats Actifs
                                </p>
                                <p class="text-xl font-black text-blue-600">{{ $stats['contrats_actifs'] }}</p>
                            </div>
                            <div class="p-4 bg-emerald-50 rounded-xl border border-emerald-100 text-center">
                                <p class="text-[9px] font-black text-emerald-400 uppercase tracking-widest mb-1">Total Contrats
                                </p>
                                <p class="text-xl font-black text-emerald-600">{{ $stats['total_contrats'] }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                @if($showProprietaires)
                    {{-- Communauté --}}
                    <div class="premium-card p-6 bg-white">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="font-black text-sm text-slate-900 uppercase tracking-tight">Partenaires & Clients</h3>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="p-4 bg-slate-50 rounded-xl border border-slate-100 text-center">
                                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Propriétaires</p>
                                <p class="text-xl font-black text-slate-900">{{ $stats['total_proprietaires'] }}</p>
                            </div>
                            <div class="p-4 bg-slate-50 rounded-xl border border-slate-100 text-center">
                                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Locataires</p>
                                <p class="text-xl font-black text-slate-900">{{ $stats['total_locataires'] }}</p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        @endif

        {{-- ================================================== --}}
        {{-- SECTION 5 : LISTE NOIRE & DERNIERS PAIEMENTS --}}
        {{-- ================================================== --}}
        @if($showImmobilier || $showFinance || $showCommercial)
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                    {{-- Liste Noire : Locataires avec les plus gros impayés --}}
                    <div class="premium-card p-6 bg-white">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="font-black text-sm text-slate-900 uppercase tracking-tight flex items-center gap-2">
                                <i class="fas fa-exclamation-triangle text-red-500"></i> Alertes Impayés
                            </h3>
                            <span class="text-[10px] font-black text-red-600 bg-red-50 px-2 py-1 rounded uppercase">Top 5</span>
                        </div>
                        @if($listeNoire->isNotEmpty())
                            <div class="space-y-3">
                                @foreach($listeNoire as $index => $item)
                                    <a href="{{ route('contrats.show', $item->contrat_id) }}"
                                        class="flex items-center gap-4 p-3 rounded-xl border {{ $index === 0 ? 'border-red-200 bg-red-50/50' : 'border-slate-100 bg-slate-50/50' }} hover:shadow-md transition-all group">
                                        <div
                                            class="w-9 h-9 rounded-full {{ $index === 0 ? 'bg-red-500' : 'bg-slate-400' }} flex items-center justify-center text-white font-black text-sm flex-shrink-0 group-hover:scale-110 transition-transform">
                                            {{ $index + 1 }}
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-black text-slate-900 truncate">{{ $item->name }}</p>
                                            <p class="text-[10px] text-slate-400 truncate">{{ $item->nom_bien }}
                                                {{ $item->telephone ? '• ' . $item->telephone : '' }}</p>
                                        </div>
                                        <div class="text-right flex-shrink-0">
                                            <p class="text-sm font-black text-red-600">
                                                {{ number_format($item->dette_totale, 0, ',', ' ') }} {{ Auth::user()->agence->devise }}
                                            </p>
                                            <p class="text-[9px] text-slate-400 font-bold">{{ $item->nb_factures }} facture(s)</p>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-12">
                                <i class="fas fa-check-circle text-5xl text-emerald-200 mb-4"></i>
                                <p class="text-slate-400 font-bold text-sm">Aucun impayé en cours 🎉</p>
                                <p class="text-slate-300 text-xs mt-1">Tous les locataires sont à jour !</p>
                            </div>
                        @endif
                    </div>

                    {{-- Derniers Paiements --}}
                    <div class="premium-card p-6 bg-white">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="font-black text-sm text-slate-900 uppercase tracking-tight flex items-center gap-2">
                                <i class="fas fa-history text-blue-500"></i> Derniers Paiements
                            </h3>
                            @if(Auth::user()->hasRole('admin'))
                                <a href="{{ route('agency.finance-report') }}"
                                    class="text-[10px] font-black text-blue-600 bg-blue-50 px-2 py-1 rounded uppercase hover:bg-blue-100 transition-colors">Voir
                                    tout</a>
                            @endif
                        </div>
                        @if($derniers_paiements->isNotEmpty())
                            <div class="space-y-3">
                                @foreach($derniers_paiements as $p)
                                    <a href="{{ route('contrats.show', $p->facture->contrat_id) }}"
                                        class="flex items-center gap-4 p-3 rounded-xl border border-slate-100 bg-slate-50/50 hover:shadow-md transition-all group">
                                        <div
                                            class="w-9 h-9 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600 flex-shrink-0 group-hover:bg-emerald-500 group-hover:text-white transition-colors">
                                            <i class="fas fa-check text-sm"></i>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-bold text-slate-900 truncate">
                                                {{ $p->facture->contrat->locataire->user->name ?? '-' }}</p>
                                            <p class="text-[10px] text-slate-400">
                                                {{ \Carbon\Carbon::parse($p->date_paiement)->format('d/m/Y') }} •
                                                {{ $p->mode_paiement }}</p>
                                        </div>
                                        <div class="text-right flex-shrink-0">
                                            <p class="text-sm font-black text-emerald-600">
                                                +{{ number_format($p->montant_verse, 0, ',', ' ') }}</p>
                                            <p class="text-[9px] text-slate-400 font-bold uppercase">{{ $p->facture->mois_facture }}</p>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-12">
                                <i class="fas fa-inbox text-5xl text-slate-200 mb-4"></i>
                                <p class="text-slate-400 font-bold text-sm">Aucun paiement pour l'instant</p>
                            </div>
                        @endif
                    </div>
                </div>

            </div>
        @endif

    @push('scripts')
        <script>
            // Modal remboursement caution
            const rembourserCautionBaseUrl = "{{ url('finance/contrats') }}";
            let currentCautionDisponible = 0;

            window.openRemboursementModal = function(contratId, cautionDisponible, locataireName, bienName) {
                currentCautionDisponible = parseFloat(cautionDisponible) || 0;
                const devise = '{{ Auth::user()->agence->devise ?? "XOF" }}';
                
                const cautionDispEl = document.getElementById('modalCautionDisponible');
                if (cautionDispEl) {
                    cautionDispEl.textContent = currentCautionDisponible.toLocaleString('fr-FR') + ' ' + devise;
                }
                
                const infoEl = document.getElementById('modalLocataireInfo');
                if (infoEl) {
                    if (locataireName || bienName) {
                        infoEl.innerHTML = '<i class="fas fa-user mr-1"></i> ' + (locataireName || '') + (bienName ? ' &bull; <i class="fas fa-home ml-1 mr-1"></i> ' + bienName : '');
                        infoEl.classList.remove('hidden');
                    } else {
                        infoEl.classList.add('hidden');
                    }
                }
                
                const retenuInput = document.getElementById('montantRetenu');
                if (retenuInput) {
                    retenuInput.value = 0;
                    retenuInput.max = currentCautionDisponible;
                }
                
                const rembInput = document.getElementById('montantRembourse');
                if (rembInput) {
                    rembInput.value = currentCautionDisponible;
                }
                
                const rembHidden = document.getElementById('montantRembourseHidden');
                if (rembHidden) {
                    rembHidden.value = currentCautionDisponible;
                }
                
                const dateInput = document.getElementById('dateRemboursement');
                if (dateInput) {
                    dateInput.value = new Date().toISOString().split('T')[0];
                }
                
                const form = document.getElementById('remboursementForm');
                if (form) {
                    form.action = rembourserCautionBaseUrl + '/' + contratId + '/rembourser-caution';
                }
                
                const modal = document.getElementById('remboursementModal');
                if (modal) {
                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                }
            };

            window.closeRemboursementModal = function() {
                const modal = document.getElementById('remboursementModal');
                if (modal) {
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                }
            };

            window.updateMontantRembourse = function() {
                const retenu = parseFloat(document.getElementById('montantRetenu')?.value) || 0;
                const reste = Math.max(0, currentCautionDisponible - retenu);
                const rembInput = document.getElementById('montantRembourse');
                if (rembInput) rembInput.value = reste;
                const rembHidden = document.getElementById('montantRembourseHidden');
                if (rembHidden) rembHidden.value = reste;
            };

            document.addEventListener('DOMContentLoaded', function() {
                const rModal = document.getElementById('remboursementModal');
                if (rModal) {
                    rModal.addEventListener('click', function(e) {
                        if (e.target === this) closeRemboursementModal();
                    });
                }

                document.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape') {
                        closeRemboursementModal();
                    }
                });

                const devise = '{{ Auth::user()->agence->devise }}';

                // Auto-submit le formulaire quand le mois change
                const monthSelect = document.getElementById('monthSelect');
                if (monthSelect) {
                    monthSelect.addEventListener('change', function() {
                        document.getElementById('monthFilterForm').submit();
                    });
                }

                // Debugging
                console.log("Dashboard Stats:", @json($stats));

                const sparklineOptions = {
                    chart: {
                        type: 'area',
                        height: 50,
                        sparkline: {
                            enabled: true
                        },
                        animations: {
                            enabled: true
                        }
                    },
                    stroke: {
                        curve: 'smooth',
                        width: 2
                    },
                    fill: {
                        opacity: 0.3
                    },
                    tooltip: {
                        fixed: {
                            enabled: false
                        },
                        x: {
                            show: false
                        },
                        y: {
                            title: {
                                formatter: function() {
                                    return '';
                                }
                            }
                        },
                        marker: {
                            show: false
                        }
                    }
                };

                const sCA = document.querySelector("#sparklineCA");
                if (sCA) new ApexCharts(sCA, {
                    ...sparklineOptions,
                    series: [{
                        data: @json($sparkline_ca)
                    }],
                    colors: ['#3b82f6']
                }).render();

                const sDep = document.querySelector("#sparklineDepenses");
                if (sDep) new ApexCharts(sDep, {
                    ...sparklineOptions,
                    series: [{
                        data: @json($sparkline_depenses)
                    }],
                    colors: ['#ef4444']
                }).render();

                const sBen = document.querySelector("#sparklineReversements");
                if (sBen) new ApexCharts(sBen, {
                    ...sparklineOptions,
                    series: [{
                        data: [0, 0, 0, 0, 0, 0, parseFloat("{{ $stats['total_a_reverser'] }}")]
                    }],
                    colors: ['#f59e0b']
                }).render();

                // GRAPHE : REVERSEMENTS PROPRIÉTAIRES
                const pRevChart = document.querySelector("#proprietaireReversementChart");
                if (pRevChart) {
                    const pData = @json($reversementsProprietaire);
                    const chartHeight = Math.max(350, pData.length * 45); // Ajuste la hauteur selon le nombre de propriétaires

                    new ApexCharts(pRevChart, {
                        series: [{
                            name: 'A reverser',
                            data: pData.map(i => parseFloat(i.a_reverser))
                        }],
                        chart: {
                            type: 'bar',
                            height: chartHeight,
                            toolbar: {
                                show: false
                            },
                            fontFamily: 'Inter, sans-serif'
                        },
                        plotOptions: {
                            bar: {
                                borderRadius: 4,
                                horizontal: true,
                                barHeight: '70%',
                                dataLabels: {
                                    position: 'top',
                                }
                            }
                        },
                        colors: ['#f59e0b'],
                        dataLabels: {
                            enabled: true,
                            offsetX: 30,
                            style: {
                                fontSize: '11px',
                                fontWeight: 800,
                                colors: ['#0f172a']
                            },
                            formatter: function(val) {
                                return val.toLocaleString() + ' ' + devise;
                            }
                        },
                        xaxis: {
                            categories: pData.map(i => i.proprietaire),
                            labels: {
                                show: false
                            }, // On cache l'axe X car les labels sont sur les barres
                            axisBorder: {
                                show: false
                            },
                            axisTicks: {
                                show: false
                            }
                        },
                        grid: {
                            show: false
                        },
                        tooltip: {
                            theme: 'light',
                            y: {
                                formatter: function(val) {
                                    return val.toLocaleString() + ' ' + devise;
                                }
                            }
                        }
                    }).render();
                }

                // GRAPHE : STRUCTURE REVENUS AGENCE
                const aRevChart = document.querySelector("#agenceRevenueStructureChart");
                if (aRevChart) {
                    new ApexCharts(aRevChart, {
                        series: [
                            parseFloat("{{ $stats['ca_loyers_mois'] }}"),
                            parseFloat("{{ $stats['ca_frais_dossier_mois'] }}"),
                            parseFloat("{{ $stats['ca_visites_mois'] }}"),
                            parseFloat("{{ $stats['ca_retenues_caution_mois'] }}")
                        ],
                        chart: {
                            type: 'donut',
                            height: 350,
                            fontFamily: 'Inter, sans-serif'
                        },
                        labels: ['Commissions Loyers', 'Frais de Dossier', 'Frais de Visites', 'Retenues Caution'],
                        colors: ['#2563eb', '#0891b2', '#7c3aed', '#ea580c'],
                        stroke: {
                            show: true,
                            width: 2,
                            colors: ['#fff']
                        },
                        plotOptions: {
                            pie: {
                                donut: {
                                    size: '75%',
                                    labels: {
                                        show: true,
                                        name: {
                                            show: true,
                                            fontSize: '12px',
                                            fontWeight: 600,
                                            color: '#64748b',
                                            offsetY: -10
                                        },
                                        value: {
                                            show: true,
                                            fontSize: '22px',
                                            fontWeight: 800,
                                            color: '#0f172a',
                                            offsetY: 10,
                                            formatter: function(val) {
                                                return parseInt(val).toLocaleString() + ' ' + devise;
                                            }
                                        },
                                        total: {
                                            show: true,
                                            label: 'Revenu Agence',
                                            fontSize: '11px',
                                            fontWeight: 600,
                                            color: '#64748b',
                                            formatter: function(w) {
                                                return w.globals.seriesTotals.reduce((a, b) => a + b, 0).toLocaleString() + ' ' + devise;
                                            }
                                        }
                                    }
                                }
                            }
                        },
                        legend: {
                            position: 'bottom',
                            fontWeight: 600
                        },
                        tooltip: {
                            y: {
                                formatter: function(val) {
                                    return val.toLocaleString() + ' ' + devise;
                                }
                            }
                        }
                    }).render();
                }

                const gOcc = document.querySelector("#gaugeOccupation");
                if (gOcc) new ApexCharts(gOcc, {
                    series: [parseFloat("{{ $stats['taux_occupation'] }}")],
                    chart: {
                        height: 220,
                        type: 'radialBar'
                    },
                    plotOptions: {
                        radialBar: {
                            startAngle: -135,
                            endAngle: 135,
                            hollow: {
                                size: '65%'
                            },
                            track: {
                                background: '#f1f5f9'
                            },
                            dataLabels: {
                                name: {
                                    show: true,
                                    fontSize: '11px',
                                    color: '#94a3b8'
                                },
                                value: {
                                    show: true,
                                    fontSize: '28px',
                                    color: '#0f172a',
                                    formatter: function(val) {
                                        return val + '%';
                                    }
                                }
                            }
                        }
                    },
                    colors: ['#3b82f6'],
                    labels: ['Biens Loués']
                }).render();

                const gRec = document.querySelector("#gaugeRecouvrement");
                if (gRec) {
                    const recVal = parseFloat("{{ $stats['taux_recouvrement'] }}");
                    const recCol = recVal >= 80 ? '#10b981' : (recVal >= 50 ? '#f59e0b' : '#ef4444');
                    new ApexCharts(gRec, {
                        series: [recVal],
                        chart: {
                            height: 220,
                            type: 'radialBar'
                        },
                        plotOptions: {
                            radialBar: {
                                startAngle: -135,
                                endAngle: 135,
                                hollow: {
                                    size: '65%'
                                },
                                track: {
                                    background: '#f1f5f9'
                                },
                                dataLabels: {
                                    name: {
                                        show: true,
                                        fontSize: '11px',
                                        color: '#94a3b8'
                                    },
                                    value: {
                                        show: true,
                                        fontSize: '28px',
                                        color: '#0f172a',
                                        formatter: function(val) {
                                            return val + '%';
                                        }
                                    }
                                }
                            }
                        },
                        colors: [recCol],
                        labels: ['Loyers Encaissés']
                    }).render();
                }

                const gConv = document.querySelector("#gaugeConversion");
                if (gConv) new ApexCharts(gConv, {
                    series: [parseFloat("{{ $stats['taux_conversion'] }}")],
                    chart: {
                        height: 220,
                        type: 'radialBar'
                    },
                    plotOptions: {
                        radialBar: {
                            startAngle: -135,
                            endAngle: 135,
                            hollow: {
                                size: '65%'
                            },
                            track: {
                                background: '#f1f5f9'
                            },
                            dataLabels: {
                                name: {
                                    show: true,
                                    fontSize: '11px',
                                    color: '#94a3b8'
                                },
                                value: {
                                    show: true,
                                    fontSize: '28px',
                                    color: '#0f172a',
                                    formatter: function(val) {
                                        return val + '%';
                                    }
                                }
                            }
                        }
                    },
                    colors: ['#a855f7'],
                    labels: ['Visites Conclues']
                }).render();

                // 4. GRAPHIQUE : Revenus vs Dépenses
                const rChart = document.querySelector("#revenueVsExpenseChart");
                if (rChart) new ApexCharts(rChart, {
                    series: [{                     name: 'Flux total (Loyers)',                     data: @json($revenus_mensuels)                 },                 {                     name: 'Revenu Agence (Comms)',                     data: @json($commissions_mensuelles)                 },                 {                     name: 'Dépenses Agence',                     data: @json($depenses_mensuels)                 }
                    ],
                    chart: {
                        height: 350,
                        type: 'area',
                        toolbar: {
                            show: false
                        },
                        fontFamily: 'Inter, sans-serif'
                    },
                    plotOptions: {
                        bar: {
                            borderRadius: 6,
                            columnWidth: '55%'
                        }
                    },
                    colors: ['#94a3b8', '#2563eb', '#ef4444'],
                    fill: {
                        type: 'gradient',
                        gradient: {
                            shadeIntensity: 1,
                            opacityFrom: 0.4,
                            opacityTo: 0.1
                        }
                    },
                    dataLabels: {
                        enabled: false
                    },
                    xaxis: {
                        categories: @json($mois_labels),
                        axisBorder: {
                            show: false
                        },
                        axisTicks: {
                            show: false
                        },
                        labels: {
                            style: {
                                colors: '#94a3b8',
                                fontSize: '10px',
                                fontWeight: 600
                            }
                        }
                    },
                    yaxis: {
                        labels: {
                            style: {
                                colors: '#94a3b8',
                                fontSize: '10px',
                                fontWeight: 600
                            },
                            formatter: function(val) {
                                return val.toLocaleString();
                            }
                        }
                    },
                    grid: {
                        borderColor: '#f1f5f9',
                        strokeDashArray: 4
                    },
                    tooltip: {
                        theme: 'light',
                        x: {
                            show: true
                        },
                        y: {
                            formatter: function(val) {
                                return val.toLocaleString() + ' ' + devise;
                            }
                        }
                    },
                    legend: {
                        position: 'top',
                        horizontalAlign: 'right',
                        fontWeight: 700,
                        fontSize: '11px'
                    }
                }).render();

                @if(!empty($caParType))
                    const cType = document.querySelector("#caByTypeChart");
                    if (cType) {
                        const caLabels = @json(array_keys($caParType));
                        const caValues = @json(array_values($caParType)).map(v => parseFloat(v));

                        new ApexCharts(cType, {
                            series: caValues,
                            chart: {
                                height: 350,
                                type: 'donut',
                                fontFamily: 'Inter, sans-serif',
                                animations: {
                                    enabled: true,
                                    easing: 'easeinout',
                                    speed: 800
                                }
                            },
                            labels: caLabels,
                            colors: ['#2563eb', '#7c3aed', '#059669', '#ea580c', '#d946ef', '#0891b2', '#ca8a04', '#64748b'],
                            plotOptions: {
                                pie: {
                                    donut: {
                                        size: '75%',
                                        labels: {
                                            show: true,
                                            name: {
                                                show: true,
                                                fontSize: '12px',
                                                fontWeight: 600,
                                                color: '#64748b',
                                                offsetY: -10
                                            },
                                            value: {
                                                show: true,
                                                fontSize: '22px',
                                                fontWeight: 800,
                                                color: '#0f172a',
                                                offsetY: 10,
                                                formatter: function(val) {
                                                    return parseInt(val).toLocaleString() + ' ' + devise;
                                                }
                                            },
                                            total: {
                                                show: true,
                                                label: 'Total CA',
                                                fontSize: '12px',
                                                fontWeight: 600,
                                                color: '#64748b',
                                                formatter: function(w) {
                                                    return w.globals.seriesTotals.reduce((a, b) => a + b, 0).toLocaleString() + ' ' + devise;
                                                }
                                            }
                                        }
                                    }
                                }
                            },
                            dataLabels: {
                                enabled: false
                            },
                            legend: {
                                position: 'bottom',
                                horizontalAlign: 'center',
                                fontSize: '12px',
                                fontWeight: 600,
                                markers: {
                                    radius: 12,
                                    width: 10,
                                    height: 10
                                },
                                itemMargin: {
                                    horizontal: 10,
                                    vertical: 5
                                }
                            },
                            stroke: {
                                show: true,
                                width: 2,
                                colors: ['#fff']
                            },
                            tooltip: {
                                y: {
                                    formatter: function(val) {
                                        return val.toLocaleString() + ' ' + devise;
                                    }
                                }
                            }
                        }).render();
                    }
                @else
                    document.querySelector("#caByTypeChart").innerHTML = '<div class="flex flex-col items-center justify-center h-full py-12 text-slate-400"><i class="fas fa-chart-pie text-4xl mb-2 opacity-20"></i><p class="text-xs font-bold uppercase tracking-widest">Aucune donnée</p></div>';
                @endif
            });
        </script>
    @endpush
</x-app-layout>
