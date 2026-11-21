<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 w-full">
            <div>
                <div class="flex items-center gap-2 text-xs font-bold text-slate-400 uppercase tracking-widest mb-1">
                    <a href="{{ route('proprietes.index') }}" class="hover:text-blue-600 transition-colors">Propriétés</a>
                    <span>/</span>
                    <a href="{{ route('proprietes.show', $familleDeBien) }}" class="hover:text-blue-600 transition-colors">{{ $familleDeBien->nom_famille }}</a>
                    <span>/</span>
                    <span class="text-blue-600">Modifier</span>
                </div>
                <h2 class="font-black text-2xl sm:text-3xl text-slate-900 tracking-tight uppercase">{{ __('Modifier la Propriété') }}</h2>
                <p class="text-slate-500 font-medium text-xs mt-0.5">Mettre à jour les informations de {{ $familleDeBien->nom_famille }}</p>
            </div>
            
            <a href="{{ route('proprietes.show', $familleDeBien) }}" class="px-4 py-2 bg-white border border-slate-200 hover:bg-slate-50 text-slate-600 text-xs font-black uppercase tracking-wider rounded-xl transition-all inline-flex items-center gap-2 shadow-xs">
                <i class="fas fa-arrow-left"></i>
                <span>Retour aux détails</span>
            </a>
        </div>
    </x-slot>

    <div class="w-full space-y-6">
        <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden p-6 sm:p-10 w-full">
            <form method="POST" action="{{ route('proprietes.update', $familleDeBien) }}" class="space-y-8">
                @csrf
                @method('PATCH')

                <!-- Badge rappel nature -->
                <div class="flex items-center gap-4 p-5 {{ $familleDeBien->isIndividuelle() ? 'bg-blue-50/50 border-blue-200' : 'bg-indigo-50/50 border-indigo-200' }} rounded-2xl border">
                    <div class="w-12 h-12 rounded-xl {{ $familleDeBien->isIndividuelle() ? 'bg-blue-600 text-white' : 'bg-indigo-600 text-white' }} flex items-center justify-center text-lg flex-shrink-0 shadow-sm">
                        <i class="fas {{ $familleDeBien->isIndividuelle() ? 'fa-home' : 'fa-city' }}"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-sm font-black uppercase text-slate-900">
                                {{ $familleDeBien->isIndividuelle() ? 'Bien Individuel (Mono-unité)' : 'Propriété Collective (Multi-unités)' }}
                            </span>
                            <span class="px-2 py-0.5 text-[9px] font-black uppercase rounded-md {{ $familleDeBien->isIndividuelle() ? 'bg-blue-100 text-blue-700' : 'bg-indigo-100 text-indigo-700' }}">
                                Fixé à la création
                            </span>
                        </div>
                        <p class="text-xs text-slate-500 mt-1 font-medium">
                            {{ $familleDeBien->isIndividuelle() ? 'Loué en direct en un seul bloc avec son contrat dédié.' : 'Immeuble structuré pour accueillir des unités locatives indépendantes.' }}
                        </p>
                    </div>
                </div>

                <div class="pt-6 border-t border-slate-100 space-y-6">
                    <div class="flex items-center gap-2">
                        <span class="w-6 h-6 rounded-lg bg-slate-100 text-slate-600 flex items-center justify-center text-[10px] font-black">1</span>
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
                                required value="{{ old('nom_famille', $familleDeBien->nom_famille) }}" />
                            <x-input-error :messages="$errors->get('nom_famille')" class="mt-1" />
                        </div>

                        <!-- Catégorie (filtrée strictement selon la nature) -->
                        <div>
                            <label for="type_famille" class="block text-[11px] font-black text-slate-500 uppercase tracking-wider mb-2">
                                Catégorie de Propriété <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <select id="type_famille" name="type_famille" 
                                    class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl font-bold text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white focus:border-blue-500 transition-all shadow-xs appearance-none pr-10" required>
                                    @if($familleDeBien->isIndividuelle())
                                        <option value="Villa" @selected(old('type_famille', $familleDeBien->type_famille) === 'Villa')>Villa</option>
                                        <option value="Maison individuelle" @selected(old('type_famille', $familleDeBien->type_famille) === 'Maison individuelle')>Maison individuelle</option>
                                        <option value="Studio indépendant" @selected(old('type_famille', $familleDeBien->type_famille) === 'Studio indépendant')>Studio indépendant</option>
                                        <option value="Magasin / Boutique" @selected(old('type_famille', $familleDeBien->type_famille) === 'Magasin / Boutique' || old('type_famille', $familleDeBien->type_famille) === 'Magasin')>Magasin / Boutique</option>
                                        <option value="Bureau" @selected(old('type_famille', $familleDeBien->type_famille) === 'Bureau')>Bureau</option>
                                        <option value="Entrepôt / Hangar" @selected(old('type_famille', $familleDeBien->type_famille) === 'Entrepôt / Hangar' || old('type_famille', $familleDeBien->type_famille) === 'Entrepôt')>Entrepôt / Hangar</option>
                                        <option value="Terrain" @selected(old('type_famille', $familleDeBien->type_famille) === 'Terrain')>Terrain</option>
                                        <option value="Autre" @selected(old('type_famille', $familleDeBien->type_famille) === 'Autre')>Autre</option>
                                    @else
                                        <option value="Immeuble" @selected(old('type_famille', $familleDeBien->type_famille) === 'Immeuble')>Immeuble</option>
                                        <option value="Résidence" @selected(old('type_famille', $familleDeBien->type_famille) === 'Résidence')>Résidence</option>
                                        <option value="Complexe résidentiel" @selected(old('type_famille', $familleDeBien->type_famille) === 'Complexe résidentiel')>Complexe résidentiel</option>
                                        <option value="Complexe commercial" @selected(old('type_famille', $familleDeBien->type_famille) === 'Complexe commercial')>Complexe commercial</option>
                                        <option value="Concession / Cour commune" @selected(old('type_famille', $familleDeBien->type_famille) === 'Concession / Cour commune')>Concession / Cour commune</option>
                                        <option value="Villa avec dépendances" @selected(old('type_famille', $familleDeBien->type_famille) === 'Villa avec dépendances')>Villa avec dépendances</option>
                                        <option value="Autre" @selected(old('type_famille', $familleDeBien->type_famille) === 'Autre')>Autre</option>
                                    @endif
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
                                    @foreach($proprietaires as $proprietaire)
                                    <option value="{{ $proprietaire->id }}" @selected(old('proprietaire_id', $familleDeBien->proprietaire_id) == $proprietaire->id)>
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
                                required value="{{ old('adresse', $familleDeBien->adresse) }}" />
                            <x-input-error :messages="$errors->get('adresse')" class="mt-1" />
                        </div>

                        <!-- Description -->
                        <div class="sm:col-span-2">
                            <label for="description" class="block text-[11px] font-black text-slate-500 uppercase tracking-wider mb-2">
                                Description & Particularités
                            </label>
                            <textarea id="description" name="description" rows="3" 
                                class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl font-medium text-slate-700 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white focus:border-blue-500 transition-all shadow-xs resize-none">{{ old('description', $familleDeBien->description) }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-1" />
                        </div>
                    </div>
                </div>

                @if($familleDeBien->isIndividuelle() && $bienIndividuel)
                <!-- PARAMÈTRES LOCATIFS DIRECTS SI BIEN INDIVIDUEL -->
                <div class="pt-6 border-t border-slate-100 space-y-5">
                    <div class="flex items-center gap-2">
                        <span class="w-6 h-6 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center text-[10px] font-black">2</span>
                        <h3 class="text-xs font-black text-emerald-700 uppercase tracking-[0.15em] flex items-center gap-2">
                            <i class="fas fa-coins text-emerald-500"></i> Paramètres Locatifs Directs
                        </h3>
                    </div>

                    <div class="p-6 bg-gradient-to-br from-emerald-50/50 to-teal-50/30 border border-emerald-100 rounded-2xl">
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                            <div>
                                <label for="loyer_mensuel" class="block text-[11px] font-black text-emerald-900 uppercase tracking-wider mb-2">
                                    Loyer Mensuel <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <input id="loyer_mensuel" name="loyer_mensuel" type="number" step="0.01" min="0" 
                                        class="w-full pl-4 pr-14 py-3 bg-white border border-emerald-200 rounded-xl font-black text-slate-900 text-base focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 shadow-xs transition-all" 
                                        value="{{ old('loyer_mensuel', $bienIndividuel->loyer_mensuel) }}" required />
                                    <span class="absolute inset-y-0 right-0 pr-4 flex items-center text-xs font-black text-emerald-600 pointer-events-none">
                                        {{ Auth::user()->agence->devise }}
                                    </span>
                                </div>
                            </div>
                            <div>
                                <label for="taux_commission_agence" class="block text-[11px] font-black text-slate-600 uppercase tracking-wider mb-2">
                                    Commission Agence (%)
                                </label>
                                <div class="relative">
                                    <input id="taux_commission_agence" name="taux_commission_agence" type="number" step="0.01" min="0" max="100" 
                                        class="w-full pl-4 pr-10 py-3 bg-white border border-slate-200 rounded-xl font-bold text-slate-800 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 shadow-xs transition-all" 
                                        value="{{ old('taux_commission_agence', $bienIndividuel->taux_commission_agence) }}" />
                                    <span class="absolute inset-y-0 right-0 pr-4 flex items-center text-sm font-black text-slate-400 pointer-events-none">%</span>
                                </div>
                            </div>
                            <div>
                                <label for="prix_visite" class="block text-[11px] font-black text-slate-600 uppercase tracking-wider mb-2">
                                    Frais de Visite
                                </label>
                                <div class="relative">
                                    <input id="prix_visite" name="prix_visite" type="number" step="0.01" min="0" 
                                        class="w-full pl-4 pr-14 py-3 bg-white border border-slate-200 rounded-xl font-bold text-slate-800 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 shadow-xs transition-all" 
                                        value="{{ old('prix_visite', $bienIndividuel->prix_visite ?? 0) }}" />
                                    <span class="absolute inset-y-0 right-0 pr-4 flex items-center text-xs font-black text-slate-400 pointer-events-none">
                                        {{ Auth::user()->agence->devise }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <div class="pt-6 border-t border-slate-100 flex items-center justify-between">
                    <a href="{{ route('proprietes.show', $familleDeBien) }}" class="px-5 py-3 text-xs font-black text-slate-500 hover:text-slate-800 uppercase tracking-wider transition-colors">
                        Annuler
                    </a>
                    <button type="submit" 
                        class="btn-premium bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-black text-xs uppercase tracking-widest px-8 py-4 rounded-xl shadow-lg shadow-blue-500/25 transition-all inline-flex items-center gap-2.5">
                        <i class="fas fa-save"></i>
                        <span>Enregistrer les modifications</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>