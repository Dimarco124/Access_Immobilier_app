<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 w-full">
            <div>
                <h2 class="font-black text-3xl md:text-4xl text-slate-900 tracking-tight uppercase">{{ __('Propriétés') }}</h2>
                <p class="text-slate-500 font-medium mt-1">Gérez vos propriétés (immeubles, villas, résidences) et leurs unités locatives</p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('proprietes.create') }}" class="btn-premium bg-blue-600 hover:bg-blue-700 text-white px-5 py-3 inline-flex items-center gap-2 rounded-xl shadow-lg shadow-blue-500/20 transition-all font-black uppercase text-xs tracking-wider">
                    <i class="fas fa-plus-circle"></i>
                    <span>Nouvelle Propriété</span>
                </a>
                <a href="{{ route('biens.create') }}" class="btn-premium bg-slate-900 hover:bg-slate-800 text-white px-5 py-3 inline-flex items-center gap-2 rounded-xl shadow-lg shadow-slate-900/20 transition-all font-black uppercase text-xs tracking-wider">
                    <i class="fas fa-door-open"></i>
                    <span>Nouvelle Unité Locative</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-8">
        <!-- Filtres de recherche -->
        <div class="bg-white p-4 md:p-6 rounded-2xl shadow-sm border border-slate-100">
            <form action="{{ route('proprietes.index') }}" method="GET" id="filterForm">
                <input type="hidden" name="tab" id="active-tab-input" value="{{ request('tab', 'familles') }}">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Nom / Référence</label>
                        <input type="text" name="famille" value="{{ request('famille') }}" placeholder="Ex: Résidence Akwaba, Immeuble..." 
                            class="w-full px-4 py-2.5 border border-slate-200 rounded-xl bg-slate-50 placeholder-slate-400 focus:outline-none focus:bg-white focus:ring-2 focus:ring-blue-500 text-sm font-semibold transition-all">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Propriétaire</label>
                        <input type="text" name="proprietaire" value="{{ request('proprietaire') }}" placeholder="Nom du bailleur..." 
                            class="w-full px-4 py-2.5 border border-slate-200 rounded-xl bg-slate-50 placeholder-slate-400 focus:outline-none focus:bg-white focus:ring-2 focus:ring-blue-500 text-sm font-semibold transition-all">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Statut</label>
                        <select name="statut" class="w-full px-4 py-2.5 border border-slate-200 rounded-xl bg-slate-50 focus:outline-none focus:bg-white focus:ring-2 focus:ring-blue-500 text-sm font-semibold transition-all">
                            <option value="">Tous les statuts</option>
                            <option value="Disponible" {{ request('statut') === 'Disponible' ? 'selected' : '' }}>Disponible</option>
                            <option value="Loué" {{ request('statut') === 'Loué' ? 'selected' : '' }}>Loué</option>
                            <option value="En rénovation" {{ request('statut') === 'En rénovation' ? 'selected' : '' }}>En rénovation</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Type de bien</label>
                        <input type="text" name="type" value="{{ request('type') }}" placeholder="Ex: Villa, Appartement, Magasin..." 
                            class="w-full px-4 py-2.5 border border-slate-200 rounded-xl bg-slate-50 placeholder-slate-400 focus:outline-none focus:bg-white focus:ring-2 focus:ring-blue-500 text-sm font-semibold transition-all">
                    </div>
                </div>
                <div class="flex flex-wrap items-center gap-3 mt-4">
                    <button type="submit" class="px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-black uppercase tracking-wider rounded-xl transition-all shadow-sm inline-flex items-center gap-2">
                        <i class="fas fa-filter"></i> Filtrer
                    </button>
                    @if(request()->hasAny(['famille', 'proprietaire', 'statut', 'type', 'search']))
                    <a href="{{ route('proprietes.index', ['tab' => request('tab', 'familles')]) }}" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-black uppercase tracking-wider rounded-xl transition-colors inline-flex items-center gap-2">
                        <i class="fas fa-times"></i> Réinitialiser
                    </a>
                    @endif
                    <a href="{{ route('proprietes.print', request()->query()) }}" target="_blank" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-black uppercase tracking-wider rounded-xl transition-colors inline-flex items-center gap-2 ml-auto">
                        <i class="fas fa-print"></i> Imprimer la liste
                    </a>
                </div>
            </form>
        </div>

        @if (session('success'))
        <div class="bg-emerald-50 border border-emerald-200 p-4 rounded-xl flex items-center gap-3 text-emerald-800">
            <div class="w-8 h-8 bg-emerald-500 rounded-lg flex items-center justify-center text-white flex-shrink-0">
                <i class="fas fa-check text-sm"></i>
            </div>
            <p class="font-bold text-xs">{{ session('success') }}</p>
        </div>
        @endif

        @php
            $currentTab = request('tab', 'familles');
        @endphp

        <!-- Tabs Navigation -->
        <div class="flex gap-2 border-b border-slate-200 overflow-x-auto">
            <button type="button" id="tab-familles" onclick="switchTab('familles')" class="px-5 py-3 border-b-2 text-xs font-black uppercase tracking-wider transition-all {{ $currentTab === 'familles' ? 'border-blue-600 text-blue-600' : 'border-transparent text-slate-400 hover:text-slate-700' }} whitespace-nowrap shrink-0 flex items-center gap-2">
                <i class="fas fa-city"></i>
                <span>Toutes les Propriétés ({{ $familles->count() }})</span>
            </button>
            <button type="button" id="tab-biens" onclick="switchTab('biens')" class="px-5 py-3 border-b-2 text-xs font-black uppercase tracking-wider transition-all {{ $currentTab === 'biens' ? 'border-blue-600 text-blue-600' : 'border-transparent text-slate-400 hover:text-slate-700' }} whitespace-nowrap shrink-0 flex items-center gap-2">
                <i class="fas fa-door-open"></i>
                <span>Toutes les Unités Locatives ({{ $toutesUnites->count() }})</span>
            </button>
        </div>

        <!-- TAB 1 : PROPRIÉTÉS -->
        <div id="content-familles" class="tab-content-pane {{ $currentTab === 'familles' ? 'active-tab-pane' : 'hidden' }} space-y-6">
            @if($familles->count() > 0)
                @foreach($familles as $famille)
                @php
                    $isIndiv = $famille->isIndividuelle();
                    $uniteIndiv = $isIndiv ? $famille->biens->first() : null;
                @endphp
                <div class="premium-card overflow-hidden bg-white border border-slate-100 rounded-2xl shadow-xs hover:shadow-md transition-all">
                    <!-- En-tête de la Propriété -->
                    <div class="p-6 border-b border-slate-100 bg-gradient-to-r from-slate-50/80 to-white">
                        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-2xl {{ $isIndiv ? 'bg-blue-100 text-blue-600' : 'bg-indigo-100 text-indigo-600' }} flex items-center justify-center flex-shrink-0">
                                    <i class="fas {{ $isIndiv ? 'fa-home' : 'fa-city' }} text-lg"></i>
                                </div>
                                <div>
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <a href="{{ route('proprietes.show', $famille) }}" class="text-base font-black text-slate-900 hover:text-blue-600 transition-colors uppercase tracking-tight">
                                            {{ $famille->nom_famille }}
                                        </a>
                                        @if($isIndiv)
                                            <span class="px-2.5 py-0.5 bg-blue-50 text-blue-700 text-[9px] font-black rounded-full uppercase tracking-wider border border-blue-100">
                                                Bien Individuel
                                            </span>
                                        @else
                                            <span class="px-2.5 py-0.5 bg-indigo-50 text-indigo-700 text-[9px] font-black rounded-full uppercase tracking-wider border border-indigo-100">
                                                Collectif
                                            </span>
                                        @endif
                                    </div>
                                    <div class="flex flex-wrap items-center gap-3 mt-1.5 text-xs text-slate-500 font-medium">
                                        <span>
                                            <i class="fas fa-map-marker-alt text-slate-400 mr-1"></i> {{ $famille->adresse }}
                                        </span>
                                        <span class="text-slate-300">•</span>
                                        <span>
                                            <i class="fas fa-tag text-slate-400 mr-1"></i> {{ $famille->type_famille }}
                                        </span>
                                        <span class="text-slate-300">•</span>
                                        <span>
                                            <i class="fas fa-user-tie text-slate-400 mr-1"></i> {{ $famille->proprietaire->user->name ?? 'N/A' }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Statut & Actions -->
                            <div class="flex items-center gap-3 w-full sm:w-auto justify-between sm:justify-end border-t sm:border-t-0 pt-3 sm:pt-0 border-slate-100">
                                @if($isIndiv && $uniteIndiv)
                                    <div class="text-right">
                                        <p class="text-sm font-black text-slate-900">
                                            {{ number_format($uniteIndiv->loyer_mensuel, 0, ',', ' ') }} {{ Auth::user()->agence->devise }}
                                            <span class="text-[10px] text-slate-400 font-bold">/mois</span>
                                        </p>
                                        <span class="inline-block mt-0.5 px-2 py-0.5 text-[9px] font-black rounded-md uppercase tracking-wider
                                            @if($uniteIndiv->statut === 'Disponible') bg-emerald-50 text-emerald-700 border border-emerald-200
                                            @elseif($uniteIndiv->statut === 'Loué') bg-blue-50 text-blue-700 border border-blue-200
                                            @else bg-amber-50 text-amber-700 border border-amber-200 @endif">
                                            {{ $uniteIndiv->statut }}
                                        </span>
                                    </div>
                                @else
                                    <span class="px-3 py-1.5 bg-slate-100 text-slate-700 text-xs font-black rounded-xl uppercase tracking-wider">
                                        {{ $famille->biens->count() }} unité(s)
                                    </span>
                                @endif

                                <div class="flex items-center gap-1.5 ml-2">
                                    <a href="{{ route('proprietes.show', $famille) }}" class="w-9 h-9 rounded-xl bg-slate-50 hover:bg-blue-600 text-slate-600 hover:text-white flex items-center justify-center transition-all" title="Détails">
                                        <i class="fas fa-eye text-xs"></i>
                                    </a>
                                    <a href="{{ route('proprietes.edit', $famille) }}" class="w-9 h-9 rounded-xl bg-slate-50 hover:bg-slate-900 text-slate-600 hover:text-white flex items-center justify-center transition-all" title="Modifier">
                                        <i class="fas fa-edit text-xs"></i>
                                    </a>
                                    <form method="POST" action="{{ route('proprietes.destroy', $famille) }}" onsubmit="return confirm('Supprimer cette propriété et ses unités ?')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-9 h-9 rounded-xl bg-slate-50 hover:bg-red-600 text-slate-400 hover:text-white flex items-center justify-center transition-all" title="Supprimer">
                                            <i class="fas fa-trash text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Si collectif : afficher la liste des unités à l'intérieur -->
                    @if(!$isIndiv)
                        @if($famille->biens->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-slate-100 text-sm">
                                <thead class="bg-slate-50/60">
                                    <tr>
                                        <th class="text-left text-[10px] font-black text-slate-400 uppercase tracking-widest px-6 py-3.5">Unité Locative</th>
                                        <th class="text-left text-[10px] font-black text-slate-400 uppercase tracking-widest px-6 py-3.5">Type</th>
                                        <th class="text-left text-[10px] font-black text-slate-400 uppercase tracking-widest px-6 py-3.5">Loyer</th>
                                        <th class="text-left text-[10px] font-black text-slate-400 uppercase tracking-widest px-6 py-3.5">Statut</th>
                                        <th class="text-right text-[10px] font-black text-slate-400 uppercase tracking-widest px-6 py-3.5">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @foreach($famille->biens as $bien)
                                    <tr class="hover:bg-slate-50/70 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center gap-3">
                                                <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center text-xs">
                                                    <i class="fas fa-door-closed"></i>
                                                </div>
                                                <a href="{{ route('biens.show', $bien) }}" class="font-bold text-slate-800 hover:text-blue-600 transition-colors">
                                                    {{ $bien->nom_bien }}
                                                </a>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-xs font-semibold text-slate-500">
                                            {{ $bien->type_bien }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-xs font-black text-slate-900">
                                            {{ number_format($bien->loyer_mensuel, 0, ',', ' ') }} {{ Auth::user()->agence->devise }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2.5 py-1 text-[9px] font-black rounded-lg uppercase tracking-wider
                                                @if($bien->statut === 'Disponible') bg-emerald-50 text-emerald-700
                                                @elseif($bien->statut === 'Loué') bg-blue-50 text-blue-700
                                                @else bg-amber-50 text-amber-700 @endif">
                                                {{ $bien->statut }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right">
                                            <a href="{{ route('biens.show', $bien) }}" class="inline-flex items-center gap-1 px-2.5 py-1 bg-slate-100 hover:bg-blue-600 text-slate-600 hover:text-white rounded-lg text-[10px] font-black uppercase transition-colors">
                                                <i class="fas fa-cog text-xs"></i>
                                                <span>Gérer</span>
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="p-4 bg-slate-50/50 border-t border-slate-100 flex items-center justify-between text-xs">
                            <span class="text-slate-400 font-bold uppercase tracking-wider text-[10px]">Total unités : {{ $famille->biens->count() }}</span>
                            <a href="{{ route('biens.create') }}?famille={{ $famille->id }}" class="text-indigo-600 hover:text-indigo-800 font-black uppercase tracking-wider text-[11px] inline-flex items-center gap-1.5">
                                <i class="fas fa-plus"></i>
                                <span>Ajouter une unité locative</span>
                            </a>
                        </div>
                        @else
                        <div class="p-8 text-center bg-slate-50/30">
                            <p class="text-slate-400 text-xs font-bold uppercase tracking-wider">Aucune unité locative dans cet immeuble pour l'instant</p>
                            <a href="{{ route('biens.create') }}?famille={{ $famille->id }}" class="inline-flex items-center gap-2 mt-3 px-4 py-2 bg-indigo-50 hover:bg-indigo-600 text-indigo-600 hover:text-white rounded-xl text-xs font-black uppercase tracking-wider transition-all">
                                <i class="fas fa-plus"></i>
                                <span>Ajouter la première unité locative</span>
                            </a>
                        </div>
                        @endif
                    @endif
                </div>
                @endforeach
            @else
                <div class="premium-card p-16 flex flex-col items-center justify-center text-center bg-white rounded-2xl border border-slate-100">
                    <div class="w-20 h-20 rounded-3xl bg-blue-50 flex items-center justify-center text-blue-500 mb-5">
                        <i class="fas fa-city text-3xl"></i>
                    </div>
                    <h3 class="text-lg font-black text-slate-900 uppercase tracking-tight mb-1">Aucune propriété enregistrée</h3>
                    <p class="text-slate-500 text-xs font-medium mb-6 max-w-sm mx-auto">Commencez par enregistrer vos immeubles ou vos biens individuels (villas, magasins).</p>
                    <a href="{{ route('proprietes.create') }}" class="btn-premium bg-blue-600 hover:bg-blue-700 text-white px-8 py-3.5 uppercase tracking-widest text-xs font-black rounded-xl shadow-xl shadow-blue-500/20 transition-all">
                        Créer une Propriété
                    </a>
                </div>
            @endif
        </div>

        <!-- TAB 2 : TOUTES LES UNITÉS LOCATIVES -->
        <div id="content-biens" class="tab-content-pane {{ $currentTab === 'biens' ? 'active-tab-pane' : 'hidden' }} space-y-6">
            @if($toutesUnites->count() > 0)
            <div class="premium-card overflow-hidden bg-white border border-slate-100 rounded-2xl shadow-xs">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-100 text-sm">
                        <thead class="bg-slate-50/80">
                            <tr>
                                <th class="text-left text-[10px] font-black text-slate-400 uppercase tracking-widest px-6 py-4">Unité Locative</th>
                                <th class="text-left text-[10px] font-black text-slate-400 uppercase tracking-widest px-6 py-4">Propriété / Bâtiment</th>
                                <th class="text-left text-[10px] font-black text-slate-400 uppercase tracking-widest px-6 py-4">Type</th>
                                <th class="text-left text-[10px] font-black text-slate-400 uppercase tracking-widest px-6 py-4">Propriétaire</th>
                                <th class="text-left text-[10px] font-black text-slate-400 uppercase tracking-widest px-6 py-4">Loyer</th>
                                <th class="text-left text-[10px] font-black text-slate-400 uppercase tracking-widest px-6 py-4">Statut</th>
                                <th class="text-right text-[10px] font-black text-slate-400 uppercase tracking-widest px-6 py-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @foreach($toutesUnites as $bien)
                            <tr class="hover:bg-slate-50/70 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xs flex-shrink-0">
                                            <i class="fas fa-door-closed"></i>
                                        </div>
                                        <div>
                                            <a href="{{ route('biens.show', $bien) }}" class="font-black text-slate-900 hover:text-blue-600 transition-colors text-sm">
                                                {{ $bien->nom_bien }}
                                            </a>
                                            <p class="text-[10px] text-slate-400">{{ $bien->adresse }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($bien->familleDeBien)
                                        <a href="{{ route('proprietes.show', $bien->familleDeBien) }}" class="text-xs font-bold text-indigo-600 hover:underline">
                                            {{ $bien->familleDeBien->nom_famille }}
                                        </a>
                                        <span class="block text-[10px] text-slate-400">{{ $bien->familleDeBien->type_famille }}</span>
                                    @else
                                        <span class="text-xs text-slate-400 italic">Indépendant</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-xs font-semibold text-slate-600">
                                    {{ $bien->type_bien }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-xs font-bold text-slate-700">
                                    {{ $bien->proprietaire->user->name ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-xs font-black text-slate-900">
                                    {{ number_format($bien->loyer_mensuel, 0, ',', ' ') }} {{ Auth::user()->agence->devise }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2.5 py-1 text-[9px] font-black rounded-lg uppercase tracking-wider
                                        @if($bien->statut === 'Disponible') bg-emerald-50 text-emerald-700
                                        @elseif($bien->statut === 'Loué') bg-blue-50 text-blue-700
                                        @else bg-amber-50 text-amber-700 @endif">
                                        {{ $bien->statut }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <a href="{{ route('biens.show', $bien) }}" class="w-8 h-8 rounded-lg bg-slate-50 hover:bg-blue-600 text-slate-600 hover:text-white flex items-center justify-center transition-all" title="Gérer">
                                            <i class="fas fa-cog text-xs"></i>
                                        </a>
                                        <a href="{{ route('biens.edit', $bien) }}" class="w-8 h-8 rounded-lg bg-slate-50 hover:bg-slate-900 text-slate-600 hover:text-white flex items-center justify-center transition-all" title="Modifier">
                                            <i class="fas fa-edit text-xs"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @else
            <div class="premium-card p-16 flex flex-col items-center justify-center text-center bg-white rounded-2xl border border-slate-100">
                <div class="w-20 h-20 rounded-3xl bg-indigo-50 flex items-center justify-center text-indigo-500 mb-5">
                    <i class="fas fa-door-open text-3xl"></i>
                </div>
                <h3 class="text-lg font-black text-slate-900 uppercase tracking-tight mb-1">Aucune unité locative</h3>
                <p class="text-slate-500 text-xs font-medium mb-6 max-w-sm mx-auto">Créez votre première unité locative ou enregistrez un bien individuel.</p>
                <a href="{{ route('biens.create') }}" class="btn-premium bg-blue-600 hover:bg-blue-700 text-white px-8 py-3.5 uppercase tracking-widest text-xs font-black rounded-xl shadow-xl shadow-blue-500/20 transition-all">
                    Ajouter une Unité Locative
                </a>
            </div>
            @endif
        </div>
    </div>

    @push('scripts')
    <script>
        function switchTab(tab) {
            const tabFamilles = document.getElementById('tab-familles');
            const tabBiens = document.getElementById('tab-biens');
            const contentFamilles = document.getElementById('content-familles');
            const contentBiens = document.getElementById('content-biens');
            const activeTabInput = document.getElementById('active-tab-input');

            if (!tabFamilles || !tabBiens || !contentFamilles || !contentBiens) return;

            if (activeTabInput) {
                activeTabInput.value = tab;
            }

            // Conserver l'onglet sélectionné dans l'URL sans rechargement
            try {
                const url = new URL(window.location.href);
                url.searchParams.set('tab', tab);
                window.history.replaceState({}, '', url.toString());
            } catch (e) {
                console.error(e);
            }

            if (tab === 'familles') {
                tabFamilles.classList.add('border-blue-600', 'text-blue-600');
                tabFamilles.classList.remove('border-transparent', 'text-slate-400');
                
                tabBiens.classList.remove('border-blue-600', 'text-blue-600');
                tabBiens.classList.add('border-transparent', 'text-slate-400');

                // Masquer le panneau unités locatives
                contentBiens.classList.remove('active-tab-pane');
                contentBiens.classList.add('hidden');

                // Afficher le panneau propriétés avec animation fluide
                contentFamilles.classList.remove('hidden');
                contentFamilles.classList.remove('active-tab-pane');
                void contentFamilles.offsetWidth; // Forcer le reflow
                contentFamilles.classList.add('active-tab-pane');
            } else {
                tabBiens.classList.add('border-blue-600', 'text-blue-600');
                tabBiens.classList.remove('border-transparent', 'text-slate-400');
                
                tabFamilles.classList.remove('border-blue-600', 'text-blue-600');
                tabFamilles.classList.add('border-transparent', 'text-slate-400');

                // Masquer le panneau propriétés
                contentFamilles.classList.remove('active-tab-pane');
                contentFamilles.classList.add('hidden');

                // Afficher le panneau unités locatives avec animation fluide
                contentBiens.classList.remove('hidden');
                contentBiens.classList.remove('active-tab-pane');
                void contentBiens.offsetWidth; // Forcer le reflow
                contentBiens.classList.add('active-tab-pane');
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const currentTab = urlParams.get('tab') || (window.location.hash === '#biens' ? 'biens' : null);
            if (currentTab === 'biens') {
                switchTab('biens');
            } else if (currentTab === 'familles') {
                switchTab('familles');
            }
        });
    </script>
    @endpush
</x-app-layout>
