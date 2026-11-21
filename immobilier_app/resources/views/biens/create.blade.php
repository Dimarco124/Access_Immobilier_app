<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 w-full">
            <div>
                <h2 class="font-black text-3xl md:text-4xl text-slate-900 tracking-tight uppercase">{{ __('Nouvelle Unité Locative') }}</h2>
                <p class="text-slate-500 font-medium mt-1">Ajoutez un appartement, un studio ou un local commercial à une propriété</p>
            </div>
            
            <div class="mt-1">
                <a href="{{ route('proprietes.index') }}" class="text-xs font-black text-slate-400 uppercase tracking-widest hover:text-slate-600 transition-colors self-start inline-flex items-center">
                    <i class="fas fa-arrow-left mr-2"></i> Annuler et retourner
                </a>
            </div>
        </div>
    </x-slot>

    <div class="w-full space-y-6">
        <div class="premium-card p-6 md:p-10 bg-white rounded-3xl border border-slate-100 shadow-sm w-full">
            <form method="POST" action="{{ route('biens.store') }}" class="space-y-8">
                @csrf

                <!-- Section 1 : Propriété de Rattachement -->
                <div>
                    <div class="border-b border-slate-100 pb-3 mb-5">
                        <h3 class="text-xs font-black text-indigo-600 uppercase tracking-[0.2em] flex items-center gap-2">
                            <i class="fas fa-city text-indigo-500"></i>
                            <span>1. Propriété de Rattachement</span>
                        </h3>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
                        <div class="lg:col-span-2">
                            <label for="famille_de_bien_id" class="block text-[11px] font-black text-slate-500 uppercase tracking-wider mb-2">
                                Sélectionnez la Propriété (Immeuble / Résidence) <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <select id="famille_de_bien_id" name="famille_de_bien_id" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl font-bold text-slate-800 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:bg-white transition-all shadow-xs appearance-none pr-10" required>
                                    <option value="">Choisissez une propriété...</option>
                                    @foreach($familles as $famille)
                                    <option value="{{ $famille->id }}" @selected(($selectedFamille == $famille->id) || old('famille_de_bien_id') == $famille->id)>
                                        {{ $famille->nom_famille }} ({{ $famille->type_famille }}) - Propriétaire : {{ $famille->proprietaire->user->name ?? 'Sans propriétaire' }}
                                    </option>
                                    @endforeach
                                </select>
                                <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-slate-400">
                                    <i class="fas fa-chevron-down text-xs"></i>
                                </div>
                            </div>
                            <x-input-error :messages="$errors->get('famille_de_bien_id')" class="mt-1" />
                        </div>

                        <div class="p-4 bg-indigo-50/60 border border-indigo-100 rounded-2xl">
                            <div class="flex items-center gap-2 text-indigo-800 font-black text-[11px] uppercase tracking-wider mb-1">
                                <i class="fas fa-link text-indigo-600"></i>
                                <span>Rattachement automatique</span>
                            </div>
                            <p class="text-xs text-indigo-950/75 leading-relaxed font-medium">
                                L'adresse et le propriétaire bailleur seront rattachés directement à cette unité locative.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Section 2 : Caractéristiques de l'Unité -->
                <div class="pt-6 border-t border-slate-100">
                    <div class="border-b border-slate-100 pb-3 mb-5">
                        <h3 class="text-xs font-black text-blue-600 uppercase tracking-[0.2em] flex items-center gap-2">
                            <i class="fas fa-door-open text-blue-500"></i>
                            <span>2. Caractéristiques de l'Unité</span>
                        </h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div class="lg:col-span-2">
                            <label for="nom_bien" class="block text-[11px] font-black text-slate-500 uppercase tracking-wider mb-2">
                                Nom / Identifiant de l'Unité <span class="text-red-500">*</span>
                            </label>
                            <input id="nom_bien" name="nom_bien" type="text" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl font-bold text-slate-800 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all shadow-xs" placeholder="Ex: Appartement 101, Studio B, Magasin 3..." value="{{ old('nom_bien') }}" required />
                            <x-input-error :messages="$errors->get('nom_bien')" class="mt-1" />
                        </div>

                        <div>
                            <label for="type_bien" class="block text-[11px] font-black text-slate-500 uppercase tracking-wider mb-2">
                                Type d'Unité <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <select id="type_bien" name="type_bien" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl font-bold text-slate-800 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all shadow-xs appearance-none pr-10" required>
                                    <option value="">Sélectionnez le type...</option>
                                    <option value="Appartement" @selected(old('type_bien') == 'Appartement')>Appartement</option>
                                    <option value="Studio" @selected(old('type_bien') == 'Studio')>Studio</option>
                                    <option value="Chambre" @selected(old('type_bien') == 'Chambre')>Chambre</option>
                                    <option value="Bureau" @selected(old('type_bien') == 'Bureau')>Bureau</option>
                                    <option value="Magasin" @selected(old('type_bien') == 'Magasin')>Magasin</option>
                                    <option value="Local commercial" @selected(old('type_bien') == 'Local commercial')>Local commercial</option>
                                    <option value="Entrepôt" @selected(old('type_bien') == 'Entrepôt')>Entrepôt</option>
                                    <option value="Villa" @selected(old('type_bien') == 'Villa')>Villa</option>
                                    <option value="Maison" @selected(old('type_bien') == 'Maison')>Maison</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-slate-400">
                                    <i class="fas fa-chevron-down text-xs"></i>
                                </div>
                            </div>
                            <x-input-error :messages="$errors->get('type_bien')" class="mt-1" />
                        </div>

                        <div class="md:col-span-2 lg:col-span-3">
                            <label for="adresse" class="block text-[11px] font-black text-slate-500 uppercase tracking-wider mb-2">
                                Adresse spécifique (ou pré-remplie selon la propriété) <span class="text-red-500">*</span>
                            </label>
                            <input id="adresse" name="adresse" type="text" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl font-bold text-slate-800 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all shadow-xs" placeholder="Adresse complète..." value="{{ old('adresse') }}" required />
                            <x-input-error :messages="$errors->get('adresse')" class="mt-1" />
                        </div>
                    </div>
                </div>

                <!-- Section 3 : Conditions Financières -->
                <div class="pt-6 border-t border-slate-100">
                    <div class="border-b border-slate-100 pb-3 mb-5">
                        <h3 class="text-xs font-black text-emerald-600 uppercase tracking-[0.2em] flex items-center gap-2">
                            <i class="fas fa-coins text-emerald-500"></i>
                            <span>3. Paramètres Financiers</span>
                        </h3>
                    </div>

                    <div class="p-6 bg-gradient-to-br from-emerald-50/40 via-teal-50/20 to-transparent border border-emerald-100/80 rounded-2xl">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label for="loyer_mensuel" class="block text-[11px] font-black text-emerald-900 uppercase tracking-wider mb-2">
                                    Loyer Mensuel <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <input id="loyer_mensuel" name="loyer_mensuel" type="number" step="0.01" min="0" class="w-full pl-4 pr-16 py-3 bg-white border border-emerald-200 rounded-xl font-black text-slate-900 text-base focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white transition-all shadow-xs" value="{{ old('loyer_mensuel') }}" placeholder="0.00" required />
                                    <span class="absolute inset-y-0 right-0 pr-4 flex items-center text-xs font-black text-emerald-600 pointer-events-none">
                                        {{ Auth::user()->agence->devise }}
                                    </span>
                                </div>
                                <x-input-error :messages="$errors->get('loyer_mensuel')" class="mt-1" />
                            </div>

                            <div>
                                <label for="taux_commission_agence" class="block text-[11px] font-black text-slate-600 uppercase tracking-wider mb-2">
                                    Commission Agence (%) <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <input id="taux_commission_agence" name="taux_commission_agence" type="number" step="0.01" min="0" max="100" class="w-full pl-4 pr-10 py-3 bg-white border border-slate-200 rounded-xl font-bold text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white transition-all shadow-xs" value="{{ old('taux_commission_agence', '10.00') }}" required />
                                    <span class="absolute inset-y-0 right-0 pr-4 flex items-center text-sm font-black text-slate-400 pointer-events-none">%</span>
                                </div>
                                <x-input-error :messages="$errors->get('taux_commission_agence')" class="mt-1" />
                            </div>

                            <div>
                                <label for="prix_visite" class="block text-[11px] font-black text-slate-600 uppercase tracking-wider mb-2">
                                    Frais de Visite (optionnel)
                                </label>
                                <div class="relative">
                                    <input id="prix_visite" name="prix_visite" type="number" step="0.01" min="0" class="w-full pl-4 pr-16 py-3 bg-white border border-slate-200 rounded-xl font-bold text-sm text-slate-800 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:bg-white transition-all shadow-xs" placeholder="0" value="{{ old('prix_visite', '0') }}" />
                                    <span class="absolute inset-y-0 right-0 pr-4 flex items-center text-xs font-black text-slate-400 pointer-events-none">
                                        {{ Auth::user()->agence->devise }}
                                    </span>
                                </div>
                                <x-input-error :messages="$errors->get('prix_visite')" class="mt-1" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 4 : Description -->
                <div class="pt-6 border-t border-slate-100">
                    <label for="description" class="block text-[11px] font-black text-slate-500 uppercase tracking-wider mb-2">
                        Description / Équipements de l'unité
                    </label>
                    <textarea id="description" name="description" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all shadow-xs" rows="3" placeholder="Étage, superficie, climatisation, balcon, particularités...">{{ old('description') }}</textarea>
                    <x-input-error :messages="$errors->get('description')" class="mt-1" />
                </div>

                <div class="pt-6 border-t border-slate-100 flex items-center justify-between">
                    <a href="{{ route('proprietes.index') }}" class="px-5 py-3 text-xs font-black text-slate-500 hover:text-slate-800 uppercase tracking-wider transition-colors">
                        Annuler
                    </a>
                    <button type="submit" class="btn-premium bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white px-8 py-4 uppercase tracking-widest text-xs font-black rounded-xl shadow-xl shadow-blue-500/25 transition-all inline-flex items-center gap-2">
                        <i class="fas fa-check"></i>
                        <span>Enregistrer l'Unité Locative</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        const famillesData = @json($familles);
        document.addEventListener('DOMContentLoaded', function() {
            const familleSelect = document.getElementById('famille_de_bien_id');
            const adresseInput = document.getElementById('adresse');

            familleSelect.addEventListener('change', function() {
                if (this.value) {
                    const selectedFamille = famillesData.find(f => f.id == this.value);
                    if (selectedFamille && (!adresseInput.value || adresseInput.value.trim() === '')) {
                        adresseInput.value = selectedFamille.adresse;
                    }
                }
            });
        });
    </script>
    @endpush
</x-app-layout>