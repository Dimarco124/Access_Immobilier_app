<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 w-full">
            <div>
                <div class="flex items-center gap-2 text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">
                    <a href="{{ route('proprietes.index') }}" class="hover:text-blue-600 transition-colors">Propriétés</a>
                    <span>/</span>
                    <span class="text-blue-600">Nouvelle Propriété</span>
                </div>
                <h2 class="font-black text-2xl sm:text-3xl text-slate-900 tracking-tight uppercase">{{ __('Ajouter une Propriété') }}</h2>
                <p class="text-slate-500 font-medium text-xs mt-0.5">Enregistrez un bien individuel à louer en direct ou un immeuble collectif multi-unités</p>
            </div>
            
            <a href="{{ route('proprietes.index') }}" class="px-4 py-2 bg-white border border-slate-200 hover:bg-slate-50 text-slate-600 text-xs font-black uppercase tracking-wider rounded-xl transition-all inline-flex items-center gap-2 shadow-xs">
                <i class="fas fa-arrow-left"></i>
                <span>Retour aux propriétés</span>
            </a>
        </div>
    </x-slot>

    <div class="w-full space-y-6">
        <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden p-6 sm:p-10 w-full">
            <form method="POST" action="{{ route('proprietes.store') }}" id="formPropriete" class="space-y-8">
                @csrf

                <!-- ============================================== -->
                <!-- ÉTAPE 1 : NATURE DE LA PROPRIÉTÉ (CARTES INTERACTIVES) -->
                <!-- ============================================== -->
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <label class="text-[11px] font-black text-slate-500 uppercase tracking-wider block">
                            1. Choisissez la nature de la propriété <span class="text-red-500">*</span>
                        </label>
                        <span id="badge-nature-active" class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-blue-50 text-blue-600 border border-blue-100">
                            Mono-unité en direct
                        </span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Option 1 : Bien Individuel -->
                        <div onclick="selectNature('individuelle')" id="card-individuelle" 
                             class="nature-card cursor-pointer p-5 rounded-2xl border-2 transition-all duration-200 relative bg-blue-50/40 border-blue-600 shadow-sm ring-4 ring-blue-500/10">
                            <input type="radio" id="nature_individuelle" name="nature" value="individuelle" class="sr-only" checked>
                            <div class="flex items-start gap-4">
                                <div id="icon-individuelle" class="w-12 h-12 rounded-xl flex items-center justify-center text-lg bg-blue-600 text-white shadow-sm flex-shrink-0 transition-all">
                                    <i class="fas fa-home"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between">
                                        <h4 class="text-sm font-black text-slate-900 uppercase tracking-tight">Bien Individuel</h4>
                                        <div id="dot-individuelle" class="w-5 h-5 rounded-full border-2 border-blue-600 flex items-center justify-center">
                                            <div class="w-2.5 h-2.5 rounded-full bg-blue-600"></div>
                                        </div>
                                    </div>
                                    <p class="text-[10px] font-black text-blue-600 uppercase tracking-wider mt-0.5">Loué en un seul bloc</p>
                                    <p class="text-xs text-slate-500 mt-2 font-medium leading-relaxed">
                                        Villa, maison, studio isolé, magasin ou terrain loué directement. Vous fixez le loyer dès maintenant !
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Option 2 : Propriété Collective -->
                        <div onclick="selectNature('collective')" id="card-collective" 
                             class="nature-card cursor-pointer p-5 rounded-2xl border-2 transition-all duration-200 relative bg-slate-50/60 border-slate-200 hover:border-slate-300">
                            <input type="radio" id="nature_collective" name="nature" value="collective" class="sr-only">
                            <div class="flex items-start gap-4">
                                <div id="icon-collective" class="w-12 h-12 rounded-xl flex items-center justify-center text-lg bg-slate-100 text-slate-400 shadow-sm flex-shrink-0 transition-all">
                                    <i class="fas fa-city"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between">
                                        <h4 class="text-sm font-black text-slate-900 uppercase tracking-tight">Immeuble / Collectif</h4>
                                        <div id="dot-collective" class="w-5 h-5 rounded-full border-2 border-slate-300 flex items-center justify-center">
                                            <div class="w-2.5 h-2.5 rounded-full bg-transparent"></div>
                                        </div>
                                    </div>
                                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider mt-0.5">Multi-unités locatives</p>
                                    <p class="text-xs text-slate-500 mt-2 font-medium leading-relaxed">
                                        Immeuble ou résidence abritant plusieurs appartements ou commerces. Les unités s'ajoutent après la création.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ============================================== -->
                <!-- ÉTAPE 2 : INFORMATIONS GÉNÉRALES -->
                <!-- ============================================== -->
                <div class="pt-6 border-t border-slate-100 space-y-6">
                    <div class="flex items-center gap-2">
                        <span class="w-6 h-6 rounded-lg bg-slate-100 text-slate-600 flex items-center justify-center text-[10px] font-black">2</span>
                        <h3 class="text-xs font-black text-slate-700 uppercase tracking-[0.15em]">Informations Générales</h3>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <!-- Nom de la propriété -->
                        <div>
                            <label for="nom_famille" class="block text-[11px] font-black text-slate-500 uppercase tracking-wider mb-2">
                                Nom de la Propriété <span class="text-red-500">*</span>
                            </label>
                            <input id="nom_famille" name="nom_famille" type="text" 
                                class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl font-bold text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white focus:border-blue-500 transition-all shadow-xs" 
                                required placeholder="Ex: Villa Akwaba, Immeuble Emmanuella..." value="{{ old('nom_famille') }}" />
                            <x-input-error :messages="$errors->get('nom_famille')" class="mt-1" />
                        </div>

                        <!-- Catégorie (filtrée dynamiquement selon la nature) -->
                        <div>
                            <label for="type_famille" class="block text-[11px] font-black text-slate-500 uppercase tracking-wider mb-2">
                                Catégorie de Propriété <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <select id="type_famille" name="type_famille" 
                                    class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl font-bold text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white focus:border-blue-500 transition-all shadow-xs appearance-none pr-10" required>
                                    <!-- Rempli dynamiquement par JS selon la nature -->
                                </select>
                                <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-slate-400">
                                    <i class="fas fa-chevron-down text-xs"></i>
                                </div>
                            </div>
                            <x-input-error :messages="$errors->get('type_famille')" class="mt-1" />
                        </div>

                        <!-- Propriétaire -->
                        <div>
                            <label for="proprietaire_id" class="block text-[11px] font-black text-slate-500 uppercase tracking-wider mb-2">
                                Propriétaire Bailleur <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <select id="proprietaire_id" name="proprietaire_id" 
                                    class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl font-bold text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white focus:border-blue-500 transition-all shadow-xs appearance-none pr-10" required>
                                    <option value="">Sélectionnez un propriétaire...</option>
                                    @foreach($proprietaires as $proprietaire)
                                    <option value="{{ $proprietaire->id }}" @selected(old('proprietaire_id') == $proprietaire->id)>
                                        {{ $proprietaire->user->name ?? 'Propriétaire #' . $proprietaire->id }}
                                    </option>
                                    @endforeach
                                </select>
                                <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-slate-400">
                                    <i class="fas fa-chevron-down text-xs"></i>
                                </div>
                            </div>
                            <x-input-error :messages="$errors->get('proprietaire_id')" class="mt-1" />
                        </div>

                        <!-- Adresse -->
                        <div>
                            <label for="adresse" class="block text-[11px] font-black text-slate-500 uppercase tracking-wider mb-2">
                                Adresse / Localisation <span class="text-red-500">*</span>
                            </label>
                            <input id="adresse" name="adresse" type="text" 
                                class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl font-bold text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white focus:border-blue-500 transition-all shadow-xs" 
                                required placeholder="Ex: Cocody Angré 8ème Tranche, Abidjan" value="{{ old('adresse') }}" />
                            <x-input-error :messages="$errors->get('adresse')" class="mt-1" />
                        </div>

                        <!-- Description -->
                        <div class="sm:col-span-2">
                            <label for="description" class="block text-[11px] font-black text-slate-500 uppercase tracking-wider mb-2">
                                Description & Particularités (optionnel)
                            </label>
                            <textarea id="description" name="description" rows="3" 
                                class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl font-medium text-slate-700 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white focus:border-blue-500 transition-all shadow-xs resize-none" 
                                placeholder="Détails, équipements, sécurité, points de repère...">{{ old('description') }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-1" />
                        </div>
                    </div>
                </div>

                <!-- ============================================== -->
                <!-- ÉTAPE 3 : CONFIGURATION LOCATIVE DIRECTE (SI BIEN INDIVIDUEL) -->
                <!-- ============================================== -->
                <div id="section-locative-directe" class="pt-6 border-t border-slate-100 space-y-5 transition-all">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-6 h-6 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center text-[10px] font-black">3</span>
                            <h3 class="text-xs font-black text-emerald-700 uppercase tracking-[0.15em] flex items-center gap-2">
                                <i class="fas fa-coins text-emerald-500"></i> Paramètres Locatifs Directs
                            </h3>
                        </div>
                        <span class="px-2.5 py-0.5 bg-emerald-100 text-emerald-800 text-[10px] font-black rounded-full uppercase tracking-wider">
                            Mise en location immédiate
                        </span>
                    </div>

                    <div class="p-6 bg-gradient-to-br from-emerald-50/50 to-teal-50/30 border border-emerald-100 rounded-2xl">
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                            <!-- Loyer Mensuel -->
                            <div>
                                <label for="loyer_mensuel" class="block text-[11px] font-black text-emerald-900 uppercase tracking-wider mb-2">
                                    Loyer Mensuel <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <input id="loyer_mensuel" name="loyer_mensuel" type="number" step="0.01" min="0" 
                                        class="w-full pl-4 pr-14 py-3 bg-white border border-emerald-200 rounded-xl font-black text-slate-900 text-base focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 shadow-xs transition-all" 
                                        placeholder="0" value="{{ old('loyer_mensuel') }}" />
                                    <span class="absolute inset-y-0 right-0 pr-4 flex items-center text-xs font-black text-emerald-600 pointer-events-none">
                                        {{ Auth::user()->agence->devise }}
                                    </span>
                                </div>
                                <x-input-error :messages="$errors->get('loyer_mensuel')" class="mt-1" />
                            </div>

                            <!-- Commission agence -->
                            <div>
                                <label for="taux_commission_agence" class="block text-[11px] font-black text-slate-600 uppercase tracking-wider mb-2">
                                    Commission Agence (%)
                                </label>
                                <div class="relative">
                                    <input id="taux_commission_agence" name="taux_commission_agence" type="number" step="0.01" min="0" max="100" 
                                        class="w-full pl-4 pr-10 py-3 bg-white border border-slate-200 rounded-xl font-bold text-slate-800 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 shadow-xs transition-all" 
                                        value="{{ old('taux_commission_agence', '10.00') }}" />
                                    <span class="absolute inset-y-0 right-0 pr-4 flex items-center text-sm font-black text-slate-400 pointer-events-none">%</span>
                                </div>
                                <x-input-error :messages="$errors->get('taux_commission_agence')" class="mt-1" />
                            </div>

                            <!-- Prix de visite -->
                            <div>
                                <label for="prix_visite" class="block text-[11px] font-black text-slate-600 uppercase tracking-wider mb-2">
                                    Frais de Visite
                                </label>
                                <div class="relative">
                                    <input id="prix_visite" name="prix_visite" type="number" step="0.01" min="0" 
                                        class="w-full pl-4 pr-14 py-3 bg-white border border-slate-200 rounded-xl font-bold text-slate-800 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 shadow-xs transition-all" 
                                        placeholder="0" value="{{ old('prix_visite', '0') }}" />
                                    <span class="absolute inset-y-0 right-0 pr-4 flex items-center text-xs font-black text-slate-400 pointer-events-none">
                                        {{ Auth::user()->agence->devise }}
                                    </span>
                                </div>
                                <x-input-error :messages="$errors->get('prix_visite')" class="mt-1" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- AVIS EN MODE COLLECTIF -->
                <div id="notice-collective" class="hidden pt-6 border-t border-slate-100">
                    <div class="p-5 bg-gradient-to-r from-indigo-50/60 to-purple-50/40 border border-indigo-100 rounded-2xl flex items-start gap-3.5">
                        <div class="w-9 h-9 rounded-xl bg-indigo-100 text-indigo-600 flex items-center justify-center flex-shrink-0 mt-0.5 font-bold">
                            <i class="fas fa-layer-group text-sm"></i>
                        </div>
                        <div class="text-xs text-indigo-950 leading-relaxed font-medium">
                            <p class="font-black uppercase tracking-wider text-indigo-900">Structure multi-unités prête</p>
                            <p class="mt-0.5 text-indigo-800">
                                Une fois cet immeuble enregistré, vous pourrez y ajouter ses appartements, studios ou magasins, chacun avec son propre loyer et son contrat.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- ============================================== -->
                <!-- BOUTONS D'ACTION -->
                <!-- ============================================== -->
                <div class="pt-6 border-t border-slate-100 flex items-center justify-between">
                    <a href="{{ route('proprietes.index') }}" class="px-5 py-3 text-xs font-black text-slate-500 hover:text-slate-800 uppercase tracking-wider transition-colors">
                        Annuler
                    </a>
                    <button type="submit" id="btn-submit" 
                        class="btn-premium bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-black text-xs uppercase tracking-widest px-8 py-4 rounded-xl shadow-lg shadow-blue-500/25 transition-all inline-flex items-center gap-2.5">
                        <i class="fas fa-check"></i>
                        <span id="btn-submit-text">Enregistrer et Rendre Disponible</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        // Listes strictes et distinctes selon la nature
        const categoriesIndividuelles = [
            { value: 'Villa', label: 'Villa' },
            { value: 'Maison individuelle', label: 'Maison individuelle' },
            { value: 'Studio indépendant', label: 'Studio indépendant' },
            { value: 'Magasin / Boutique', label: 'Magasin / Boutique' },
            { value: 'Bureau', label: 'Bureau' },
            { value: 'Entrepôt / Hangar', label: 'Entrepôt / Hangar' },
            { value: 'Terrain', label: 'Terrain' },
            { value: 'Autre', label: 'Autre' }
        ];

        const categoriesCollectives = [
            { value: 'Immeuble', label: 'Immeuble' },
            { value: 'Résidence', label: 'Résidence' },
            { value: 'Complexe résidentiel', label: 'Complexe résidentiel' },
            { value: 'Complexe commercial', label: 'Complexe commercial' },
            { value: 'Concession / Cour commune', label: 'Concession / Cour commune' },
            { value: 'Villa avec dépendances', label: 'Villa avec dépendances' },
            { value: 'Autre', label: 'Autre' }
        ];

        const savedOldType = @json(old('type_famille', ''));

        function populateCategories(isIndividuelle) {
            const select = document.getElementById('type_famille');
            select.innerHTML = '';

            const list = isIndividuelle ? categoriesIndividuelles : categoriesCollectives;
            
            list.forEach(item => {
                const opt = document.createElement('option');
                opt.value = item.value;
                opt.textContent = item.label;
                if (savedOldType && savedOldType === item.value) {
                    opt.selected = true;
                }
                select.appendChild(opt);
            });
        }

        function selectNature(nature) {
            const isIndiv = nature === 'individuelle';
            
            // Radios
            document.getElementById('nature_individuelle').checked = isIndiv;
            document.getElementById('nature_collective').checked = !isIndiv;

            // Cartes UI
            const cardIndiv = document.getElementById('card-individuelle');
            const cardColl = document.getElementById('card-collective');
            const iconIndiv = document.getElementById('icon-individuelle');
            const iconColl = document.getElementById('icon-collective');
            const dotIndiv = document.getElementById('dot-individuelle');
            const dotColl = document.getElementById('dot-collective');
            const badgeNature = document.getElementById('badge-nature-active');

            if (isIndiv) {
                // Active Individuel
                cardIndiv.className = 'nature-card cursor-pointer p-5 rounded-2xl border-2 transition-all duration-200 relative bg-blue-50/40 border-blue-600 shadow-sm ring-4 ring-blue-500/10';
                iconIndiv.className = 'w-12 h-12 rounded-xl flex items-center justify-center text-lg bg-blue-600 text-white shadow-sm flex-shrink-0 transition-all';
                dotIndiv.className = 'w-5 h-5 rounded-full border-2 border-blue-600 flex items-center justify-center';
                dotIndiv.innerHTML = '<div class="w-2.5 h-2.5 rounded-full bg-blue-600"></div>';

                // Inactive Collectif
                cardColl.className = 'nature-card cursor-pointer p-5 rounded-2xl border-2 transition-all duration-200 relative bg-slate-50/60 border-slate-200 hover:border-slate-300';
                iconColl.className = 'w-12 h-12 rounded-xl flex items-center justify-center text-lg bg-slate-100 text-slate-400 shadow-sm flex-shrink-0 transition-all';
                dotColl.className = 'w-5 h-5 rounded-full border-2 border-slate-300 flex items-center justify-center';
                dotColl.innerHTML = '<div class="w-2.5 h-2.5 rounded-full bg-transparent"></div>';

                badgeNature.textContent = 'Mono-unité en direct';
                badgeNature.className = 'px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-blue-50 text-blue-600 border border-blue-100';

                // Section locative
                document.getElementById('section-locative-directe').classList.remove('hidden');
                document.getElementById('notice-collective').classList.add('hidden');
                document.getElementById('loyer_mensuel').setAttribute('required', 'required');
                document.getElementById('btn-submit-text').textContent = 'Enregistrer et Rendre Disponible';
            } else {
                // Inactive Individuel
                cardIndiv.className = 'nature-card cursor-pointer p-5 rounded-2xl border-2 transition-all duration-200 relative bg-slate-50/60 border-slate-200 hover:border-slate-300';
                iconIndiv.className = 'w-12 h-12 rounded-xl flex items-center justify-center text-lg bg-slate-100 text-slate-400 shadow-sm flex-shrink-0 transition-all';
                dotIndiv.className = 'w-5 h-5 rounded-full border-2 border-slate-300 flex items-center justify-center';
                dotIndiv.innerHTML = '<div class="w-2.5 h-2.5 rounded-full bg-transparent"></div>';

                // Active Collectif
                cardColl.className = 'nature-card cursor-pointer p-5 rounded-2xl border-2 transition-all duration-200 relative bg-indigo-50/40 border-indigo-600 shadow-sm ring-4 ring-indigo-500/10';
                iconColl.className = 'w-12 h-12 rounded-xl flex items-center justify-center text-lg bg-indigo-600 text-white shadow-sm flex-shrink-0 transition-all';
                dotColl.className = 'w-5 h-5 rounded-full border-2 border-indigo-600 flex items-center justify-center';
                dotColl.innerHTML = '<div class="w-2.5 h-2.5 rounded-full bg-indigo-600"></div>';

                badgeNature.textContent = 'Multi-unités (Collectif)';
                badgeNature.className = 'px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-indigo-50 text-indigo-600 border border-indigo-100';

        
                // Section locative
                document.getElementById('section-locative-directe').classList.add('hidden');
                document.getElementById('notice-collective').classList.remove('hidden');
                document.getElementById('loyer_mensuel').removeAttribute('required');
                document.getElementById('btn-submit-text').textContent = 'Créer la Propriété Collective';
            }

            // Mettre à jour le select des catégories
            populateCategories(isIndiv);
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Initialisation selon la valeur radio cochée
            const isIndiv = document.getElementById('nature_individuelle').checked;
            selectNature(isIndiv ? 'individuelle' : 'collective');
        });
    </script>
    @endpush
</x-app-layout>