<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 w-full">
            <div>
                <h2 class="font-black text-2xl text-slate-900 tracking-tight uppercase">Tableau de bord</h2>
                <p class="text-[11px] text-slate-500 font-bold uppercase tracking-widest mt-1">Bienvenue, {{ Auth::user()->name }}</p>
            </div>

            {{-- Sélecteur de mois --}}
            <form method="GET" action="{{ route('dashboard') }}" class="flex items-center gap-2 bg-white px-3 py-1.5 rounded-xl border border-slate-200 shadow-xs self-start md:self-auto">
                @php
                    $prevMois = \Carbon\Carbon::parse($moisInputValue)->subMonth()->format('Y-m');
                    $nextMois = \Carbon\Carbon::parse($moisInputValue)->addMonth()->format('Y-m');
                @endphp
                <a href="{{ route('dashboard', ['mois' => $prevMois]) }}" class="w-7 h-7 rounded-lg flex items-center justify-center text-slate-500 hover:bg-slate-100 hover:text-indigo-600 transition" title="Mois précédent">
                    <i class="fas fa-chevron-left text-xs"></i>
                </a>
                <select name="mois" onchange="this.form.submit()" class="border-0 bg-transparent text-xs font-black text-slate-700 uppercase tracking-wider focus:ring-0 py-1 pr-6 cursor-pointer">
                    @foreach($listeMois as $m)
                        <option value="{{ $m['value'] }}" {{ $m['value'] === $moisInputValue ? 'selected' : '' }}>
                            {{ $m['label'] }}
                        </option>
                    @endforeach
                </select>
                <a href="{{ route('dashboard', ['mois' => $nextMois]) }}" class="w-7 h-7 rounded-lg flex items-center justify-center text-slate-500 hover:bg-slate-100 hover:text-indigo-600 transition" title="Mois suivant">
                    <i class="fas fa-chevron-right text-xs"></i>
                </a>
            </form>
        </div>
    </x-slot>

    @php $devise = Auth::user()->agence->devise ?? 'XOF'; @endphp

    <div class="space-y-6">
        <!-- KPIs strictement liés au propriétaire -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-5">
            <!-- Mes Biens -->
            <div class="premium-card p-5 bg-white border-l-4 border-l-indigo-600">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-slate-400 text-[9px] uppercase font-black tracking-widest mb-1">Mes Biens</p>
                        <p class="text-3xl font-black text-slate-900">{{ $stats['total_biens'] ?? 0 }}</p>
                        <p class="text-[10px] font-bold text-emerald-600 mt-1">
                            {{ $stats['total_unites'] ?? 0 }} loué(s)
                        </p>
                    </div>
                    <div class="w-11 h-11 bg-indigo-50 rounded-xl flex items-center justify-center text-indigo-600">
                        <i class="fas fa-building text-lg"></i>
                    </div>
                </div>
            </div>

            <!-- À recevoir ce mois (Net après commission et dépenses) -->
            <div class="premium-card p-5 bg-white border-l-4 border-l-emerald-600">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-slate-400 text-[9px] uppercase font-black tracking-widest mb-1">À recevoir ({{ $moisFormat }})</p>
                        <p class="text-2xl font-black text-emerald-600">
                            {{ number_format($stats['reste_a_percevoir_mois'] ?? 0, 0, ',', ' ') }}
                            <span class="text-xs">{{ $devise }}</span>
                        </p>
                        <p class="text-[10px] font-bold text-slate-400 mt-1">
                            Net du mois : {{ number_format($stats['net_mois'] ?? 0, 0, ',', ' ') }} {{ $devise }}
                        </p>
                    </div>
                    <div class="w-11 h-11 bg-emerald-50 rounded-xl flex items-center justify-center text-emerald-600">
                        <i class="fas fa-hand-holding-usd text-lg"></i>
                    </div>
                </div>
            </div>

            <!-- Solde Global Disponible (Cumul Net - Déjà Reversé) -->
            <div class="premium-card p-5 bg-white border-l-4 border-l-amber-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-slate-400 text-[9px] uppercase font-black tracking-widest mb-1">Solde Disponible</p>
                        <p class="text-2xl font-black text-amber-600">
                            {{ number_format($stats['solde_a_recevoir'] ?? 0, 0, ',', ' ') }}
                            <span class="text-xs">{{ $devise }}</span>
                        </p>
                        <p class="text-[10px] font-bold text-slate-400 mt-1">
                            Déjà reversé : {{ number_format($stats['total_reverse'] ?? 0, 0, ',', ' ') }} {{ $devise }}
                        </p>
                    </div>
                    <div class="w-11 h-11 bg-amber-50 rounded-xl flex items-center justify-center text-amber-600">
                        <i class="fas fa-wallet text-lg"></i>
                    </div>
                </div>
            </div>

            <!-- Loyers en attente chez les locataires (Part loyer uniquement) -->
            <div class="premium-card p-5 bg-white border-l-4 border-l-red-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-slate-400 text-[9px] uppercase font-black tracking-widest mb-1">Loyers Impayés (Locataires)</p>
                        <p class="text-2xl font-black text-red-600">
                            {{ number_format($stats['loyers_en_attente'] ?? 0, 0, ',', ' ') }}
                            <span class="text-xs">{{ $devise }}</span>
                        </p>
                        <p class="text-[10px] font-bold text-slate-400 mt-1">
                            En attente d'encaissement
                        </p>
                    </div>
                    <div class="w-11 h-11 bg-red-50 rounded-xl flex items-center justify-center text-red-600">
                        <i class="fas fa-clock text-lg"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Détail du mois sélectionné -->
        <div class="premium-card bg-white p-5">
            <div class="flex flex-wrap items-center justify-between gap-4">
                <div>
                    <p class="text-[10px] font-black uppercase tracking-widest text-indigo-600">Détail de vos revenus • {{ $moisFormat }}</p>
                    <p class="text-xs text-slate-500 font-medium mt-0.5">Calculé uniquement sur la part loyer de vos biens (hors cautions et frais d'agence).</p>
                </div>
                <div class="flex flex-wrap items-center gap-6 text-xs">
                    <div>
                        <span class="text-slate-400 font-bold uppercase text-[9px] block">Loyers encaissés</span>
                        <span class="font-black text-slate-800">{{ number_format($stats['loyers_bruts_mois'] ?? 0, 0, ',', ' ') }} {{ $devise }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 font-bold uppercase text-[9px] block">Commission agence</span>
                        <span class="font-black text-amber-600">- {{ number_format($stats['commissions_mois'] ?? 0, 0, ',', ' ') }} {{ $devise }}</span>
                    </div>
                    <div>
                        <span class="text-slate-400 font-bold uppercase text-[9px] block">Dépenses sur vos biens</span>
                        <span class="font-black text-red-500">- {{ number_format($stats['depenses_mois'] ?? 0, 0, ',', ' ') }} {{ $devise }}</span>
                    </div>
                    <div class="pl-4 border-l border-slate-200">
                        <span class="text-emerald-600 font-black uppercase text-[9px] block">Votre Net du mois</span>
                        <span class="font-black text-emerald-600 text-sm">{{ number_format($stats['net_mois'] ?? 0, 0, ',', ' ') }} {{ $devise }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Colonne principale -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Mes biens -->
                <div class="premium-card bg-white overflow-hidden">
                    <div class="p-6 border-b border-slate-100 flex items-center justify-between bg-slate-50/30">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-indigo-100 rounded-xl flex items-center justify-center text-indigo-600">
                                <i class="fas fa-city"></i>
                            </div>
                            <h3 class="text-sm font-black text-slate-900 uppercase tracking-tight">Mes Propriétés</h3>
                        </div>
                        <a href="{{ route('proprietaires.mes-proprietes') }}" class="text-xs text-indigo-600 font-bold hover:text-indigo-700">
                            Voir tout <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                    <div class="p-6">
                        @if($mes_familles && $mes_familles->count() > 0)
                        <div class="space-y-4">
                            @foreach($mes_familles->take(3) as $famille)
                            <div class="p-5 border border-slate-100 rounded-xl hover:border-slate-300 transition-all bg-slate-50/30">
                                <div class="flex items-center justify-between mb-3">
                                    <h4 class="font-black text-slate-900">{{ $famille->nom_famille }}</h4>
                                    <span class="px-2 py-0.5 text-[8px] font-black rounded-full uppercase bg-slate-100 text-slate-600">
                                        {{ $famille->type_famille }}
                                    </span>
                                </div>
                                <p class="text-sm text-slate-500 mb-4"><i class="fas fa-map-marker-alt mr-1"></i> {{ $famille->adresse }}</p>

                                @if($famille->biens && $famille->biens->count() > 0)
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    @foreach($famille->biens as $bien)
                                    <div class="p-3 bg-white rounded-lg border border-slate-100 flex items-center justify-between">
                                        <div class="flex items-center gap-2">
                                            <i class="fas fa-door-closed text-slate-400 text-xs"></i>
                                            <p class="text-xs font-bold text-slate-900">{{ $bien->nom_bien }}</p>
                                        </div>
                                        @if($bien->statut == 'Loué')
                                        <p class="text-xs text-emerald-600 font-bold">
                                            <i class="fas fa-check mr-1"></i>Loué
                                        </p>
                                        @else
                                        <p class="text-xs text-slate-500 font-medium">
                                            {{ $bien->statut }}
                                        </p>
                                        @endif
                                    </div>
                                    @endforeach
                                </div>
                                @endif
                            </div>
                            @endforeach
                        </div>
                        @else
                        <div class="text-center py-8">
                            <i class="fas fa-city text-4xl text-slate-200 mb-3"></i>
                            <p class="text-sm text-slate-400 font-bold">Aucune propriété enregistrée</p>
                            <p class="text-xs text-slate-300 mt-1">Contactez votre agence pour ajouter vos propriétés</p>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Derniers loyers perçus (Part loyer net du propriétaire uniquement) -->
                <div class="premium-card bg-white overflow-hidden">
                    <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-emerald-100 rounded-xl flex items-center justify-center text-emerald-600">
                                <i class="fas fa-history"></i>
                            </div>
                            <div>
                                <h3 class="text-sm font-black text-slate-900 uppercase tracking-tight">Derniers loyers perçus</h3>
                                <p class="text-[10px] text-slate-400 font-medium">Montants nets après commission agence</p>
                            </div>
                        </div>
                        <a href="{{ route('owner.finance-report') }}" class="text-xs text-indigo-600 font-bold hover:text-indigo-700">
                            Détails <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
                    <div class="p-6">
                        @if($derniers_paiements && $derniers_paiements->count() > 0)
                        <div class="space-y-3">
                            @foreach($derniers_paiements->take(5) as $paiement)
                            <div class="flex items-center justify-between p-4 bg-slate-50 rounded-xl hover:bg-slate-100 transition-all">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center text-emerald-600">
                                        <i class="fas fa-check"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-black text-slate-900">{{ $paiement->facture?->contrat?->bien?->nom_bien ?? 'N/A' }}</p>
                                        <p class="text-xs text-slate-500">
                                            {{ $paiement->date_paiement?->format('d/m/Y') ?? 'N/A' }}
                                            • Loyer brut : {{ number_format($paiement->montant_loyer_brut ?? 0, 0, ',', ' ') }} {{ $devise }}
                                        </p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-lg font-black text-emerald-600">
                                        +{{ number_format($paiement->montant_net_proprietaire ?? 0, 0, ',', ' ') }} {{ $devise }}
                                    </p>
                                    @if($paiement->facture)
                                    <p class="text-[10px] font-bold text-slate-400 uppercase">{{ $paiement->facture->mois_facture ?? 'N/A' }}</p>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <div class="text-center py-8">
                            <i class="fas fa-receipt text-4xl text-slate-200 mb-3"></i>
                            <p class="text-sm text-slate-400 font-bold">Aucun loyer encaissé pour le moment</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Colonne latérale -->
            <div class="space-y-6">
                <!-- Actions rapides -->
                <div class="premium-card bg-white p-6">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 bg-slate-100 rounded-xl flex items-center justify-center text-slate-600">
                            <i class="fas fa-bolt"></i>
                        </div>
                        <h3 class="text-sm font-black text-slate-900 uppercase tracking-tight">Actions rapides</h3>
                    </div>
                    <div class="space-y-3">
                        <a href="{{ route('proprietaires.mes-proprietes') }}" class="flex items-center gap-3 p-4 bg-indigo-50 text-indigo-700 rounded-xl hover:bg-indigo-100 transition-all group">
                            <div class="w-10 h-10 bg-indigo-600 rounded-lg flex items-center justify-center text-white group-hover:scale-110 transition-transform">
                                <i class="fas fa-building"></i>
                            </div>
                            <div>
                                <p class="text-sm font-black">Mes propriétés</p>
                                <p class="text-xs text-indigo-600">Voir tous mes biens</p>
                            </div>
                        </a>
                        <a href="{{ route('owner.finance-report') }}" class="flex items-center gap-3 p-4 bg-emerald-50 text-emerald-700 rounded-xl hover:bg-emerald-100 transition-all group">
                            <div class="w-10 h-10 bg-emerald-600 rounded-lg flex items-center justify-center text-white group-hover:scale-110 transition-transform">
                                <i class="fas fa-chart-line"></i>
                            </div>
                            <div>
                                <p class="text-sm font-black">Rapport financier</p>
                                <p class="text-xs text-emerald-600">Mes loyers & reversements</p>
                            </div>
                        </a>
                        <a href="{{ route('messages.index') }}" class="flex items-center gap-3 p-4 bg-blue-50 text-blue-700 rounded-xl hover:bg-blue-100 transition-all group">
                            <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center text-white group-hover:scale-110 transition-transform">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div>
                                <p class="text-sm font-black">Messages</p>
                                <p class="text-xs text-blue-600">Contacter l'agence</p>
                            </div>
                        </a>
                    </div>
                </div>

                <!-- Section contact agence -->
                <div class="premium-card p-6 bg-slate-50">
                    <p class="text-[8px] font-black uppercase tracking-widest text-slate-400 mb-2">MON AGENCE GESTIONNAIRE</p>
                    <p class="text-sm font-bold text-blue-600 mb-3">{{ optional(Auth::user()->agence)->nom ?? 'Agence' }}</p>
                    <div class="space-y-2 mb-4">
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
                    <a href="{{ route('messages.index') }}" class="w-full block px-4 py-2.5 bg-indigo-50 text-indigo-700 text-[10px] font-black uppercase tracking-widest rounded-lg hover:bg-indigo-100 transition-all text-center">
                        <i class="fas fa-envelope mr-1"></i> Envoyer un message
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
