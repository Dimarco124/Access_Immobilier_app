<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 w-full">
            <div>
                <h2 class="font-black text-3xl md:text-4xl text-slate-900 tracking-tight uppercase">
                    Modifier l'Unité Locative
                </h2>
                <p class="text-slate-500 font-medium mt-1">Mise à jour de : {{ $bien->nom_bien }}</p>
            </div>
            
            <div class="mt-1">
                <a href="{{ route('proprietes.show', $bien->famille_de_bien_id) }}" class="text-xs font-black text-slate-400 uppercase tracking-widest hover:text-slate-600 transition-colors self-start inline-flex items-center gap-2">
                    <i class="fas fa-arrow-left"></i> Annuler et retourner
                </a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-8 w-full">
        <div class="premium-card p-6 md:p-10 bg-white rounded-3xl border border-slate-100 shadow-sm w-full">
            <form method="POST" action="{{ route('biens.update', $bien) }}" class="space-y-10">
                @csrf
                @method('PATCH')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Section Propriété -->
                    <div class="md:col-span-2 border-b border-slate-100 pb-4">
                        <h3 class="text-xs font-black text-indigo-600 uppercase tracking-[0.2em] flex items-center gap-2">
                            <i class="fas fa-city text-indigo-500"></i>
                            <span>Propriété de Rattachement</span>
                        </h3>
                    </div>

                    <div class="md:col-span-2">
                        <label for="famille_de_bien_id" class="block text-[11px] font-black text-slate-500 uppercase tracking-wider mb-2">
                            Propriété (Immeuble / Résidence) <span class="text-red-500">*</span>
                        </label>
                        <select id="famille_de_bien_id" name="famille_de_bien_id" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl font-bold text-slate-800 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all shadow-xs" required>
                            @foreach($familles as $famille)
                            <option value="{{ $famille->id }}" @selected(old('famille_de_bien_id', $bien->famille_de_bien_id) == $famille->id)>
                                {{ $famille->nom_famille }} ({{ $famille->type_famille }}) - {{ $famille->proprietaire->user->name ?? 'Sans propriétaire' }}
                            </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('famille_de_bien_id')" class="mt-2" />
                    </div>

                    <!-- Section Principale -->
                    <div class="md:col-span-2 border-b border-slate-100 pb-4 mt-2">
                        <h3 class="text-xs font-black text-blue-600 uppercase tracking-[0.2em] flex items-center gap-2">
                            <i class="fas fa-door-open text-blue-500"></i>
                            <span>Caractéristiques de l'Unité</span>
                        </h3>
                    </div>

                    <div>
                        <label for="nom_bien" class="block text-[11px] font-black text-slate-500 uppercase tracking-wider mb-2">Nom / Identifiant de l'Unité <span class="text-red-500">*</span></label>
                        <input id="nom_bien" name="nom_bien" type="text" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl font-bold text-slate-800 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all shadow-xs" value="{{ old('nom_bien', $bien->nom_bien) }}" required />
                        <x-input-error :messages="$errors->get('nom_bien')" class="mt-2" />
                    </div>

                    <div>
                        <label for="type_bien" class="block text-[11px] font-black text-slate-500 uppercase tracking-wider mb-2">Type d'Unité <span class="text-red-500">*</span></label>
                        <select id="type_bien" name="type_bien" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl font-bold text-slate-800 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all shadow-xs" required>
                            <option value="Appartement" @selected(old('type_bien', $bien->type_bien) == 'Appartement')>Appartement</option>
                            <option value="Studio" @selected(old('type_bien', $bien->type_bien) == 'Studio')>Studio</option>
                            <option value="Chambre" @selected(old('type_bien', $bien->type_bien) == 'Chambre')>Chambre</option>
                            <option value="Villa" @selected(old('type_bien', $bien->type_bien) == 'Villa')>Villa</option>
                            <option value="Maison" @selected(old('type_bien', $bien->type_bien) == 'Maison')>Maison</option>
                            <option value="Magasin" @selected(old('type_bien', $bien->type_bien) == 'Magasin')>Magasin</option>
                            <option value="Entrepôt" @selected(old('type_bien', $bien->type_bien) == 'Entrepôt')>Entrepôt</option>
                            <option value="Bureau" @selected(old('type_bien', $bien->type_bien) == 'Bureau')>Bureau</option>
                            <option value="Local commercial" @selected(old('type_bien', $bien->type_bien) == 'Local commercial')>Local commercial</option>
                        </select>
                        <x-input-error :messages="$errors->get('type_bien')" class="mt-2" />
                    </div>

                    <div class="md:col-span-2">
                        <label for="adresse" class="block text-[11px] font-black text-slate-500 uppercase tracking-wider mb-2">Adresse précise <span class="text-red-500">*</span></label>
                        <input id="adresse" name="adresse" type="text" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl font-bold text-slate-800 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all shadow-xs" value="{{ old('adresse', $bien->adresse) }}" required />
                        <x-input-error :messages="$errors->get('adresse')" class="mt-2" />
                    </div>

                    <!-- Finances -->
                    <div class="md:col-span-2 border-b border-slate-100 pb-4 mt-2">
                        <h3 class="text-xs font-black text-emerald-600 uppercase tracking-[0.2em] flex items-center gap-2">
                            <i class="fas fa-coins text-emerald-500"></i>
                            <span>Paramètres Financiers</span>
                        </h3>
                    </div>

                    <div>
                        <label for="taux_commission_agence" class="block text-[11px] font-black text-slate-500 uppercase tracking-wider mb-2">Commission agence (%) <span class="text-red-500">*</span></label>
                        <input id="taux_commission_agence" name="taux_commission_agence" type="number" step="0.01" min="0" max="100" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl font-bold text-slate-800 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white transition-all shadow-xs" value="{{ old('taux_commission_agence', $bien->taux_commission_agence) }}" required />
                        <x-input-error :messages="$errors->get('taux_commission_agence')" class="mt-2" />
                    </div>

                    <div>
                        <label for="loyer_mensuel" class="block text-[11px] font-black text-slate-500 uppercase tracking-wider mb-2">Loyer mensuel <span class="text-red-500">*</span></label>
                        <input id="loyer_mensuel" name="loyer_mensuel" type="number" step="0.01" min="0" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl font-black text-slate-900 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white transition-all shadow-xs" value="{{ old('loyer_mensuel', $bien->loyer_mensuel) }}" required />
                        <x-input-error :messages="$errors->get('loyer_mensuel')" class="mt-2" />
                    </div>

                    <div class="md:col-span-2">
                        <label for="statut" class="block text-[11px] font-black text-slate-500 uppercase tracking-wider mb-2">Statut d'occupation <span class="text-red-500">*</span></label>
                        <select id="statut" name="statut" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl font-bold text-slate-800 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all shadow-xs" required>
                            <option value="Disponible" @selected(old('statut', $bien->statut) == 'Disponible')>Disponible</option>
                            <option value="Loué" @selected(old('statut', $bien->statut) == 'Loué')>Loué</option>
                            <option value="En maintenance" @selected(old('statut', $bien->statut) == 'En maintenance')>En maintenance</option>
                        </select>
                        <x-input-error :messages="$errors->get('statut')" class="mt-2" />
                    </div>

                    <div class="md:col-span-2">
                        <label for="description" class="block text-[11px] font-black text-slate-500 uppercase tracking-wider mb-2">Description & Notes (Optionnel)</label>
                        <textarea id="description" name="description" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl font-medium text-slate-700 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all shadow-xs" rows="3">{{ old('description', $bien->description) }}</textarea>
                        <x-input-error :messages="$errors->get('description')" class="mt-2" />
                    </div>
                </div>

                <div class="pt-6 border-t border-slate-100 flex items-center justify-between">
                    <a href="{{ route('proprietes.show', $bien->famille_de_bien_id) }}" class="px-5 py-3 text-xs font-black text-slate-500 hover:text-slate-800 uppercase tracking-wider transition-colors">
                        Annuler
                    </a>
                    <button type="submit" class="btn-premium bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white px-8 py-4 uppercase tracking-widest text-xs font-black rounded-xl shadow-xl shadow-blue-500/25 transition-all inline-flex items-center gap-2">
                        <i class="fas fa-save"></i>
                        <span>Enregistrer les modifications</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>