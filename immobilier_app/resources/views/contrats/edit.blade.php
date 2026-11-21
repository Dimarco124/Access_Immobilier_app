<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 w-full">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <span class="px-2.5 py-1 bg-emerald-100 text-emerald-700 text-[10px] font-black rounded-lg uppercase tracking-wider inline-flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            Contrat Actif
                        </span>
                        <span class="text-xs font-bold text-slate-400">#{{ $contrat->numero_contrat ?? $contrat->id }}</span>
                    </div>
                    <h2 class="font-black text-3xl sm:text-4xl text-slate-900 tracking-tight uppercase">
                        Modifier le Contrat
                    </h2>
                    <p class="text-slate-500 font-medium text-xs sm:text-sm mt-0.5">
                        Prolongation, révision de loyer ou mise à jour des observations du bail
                    </p>
                </div>
                
                <div class="flex items-center gap-2">
                    <a href="{{ route('contrats.show', $contrat) }}" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-black uppercase tracking-wider rounded-xl transition inline-flex items-center gap-2">
                        <i class="fas fa-eye text-xs"></i> Voir le dossier
                    </a>
                    <a href="{{ route('contrats.bail', $contrat) }}" class="px-4 py-2.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 text-xs font-black uppercase tracking-wider rounded-xl transition inline-flex items-center gap-2">
                        <i class="fas fa-file-contract text-xs"></i> Voir le bail
                    </a>
                </div>
            </div>
            
            <div class="mt-1">
                <a href="{{ route('contrats.show', $contrat) }}" class="text-xs font-black text-slate-400 uppercase tracking-widest hover:text-slate-600 transition-colors self-start inline-flex items-center gap-2">
                    <i class="fas fa-arrow-left"></i> Annuler et retourner au contrat
                </a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-8">
        {{-- Bannière d'information sur les règles d'intégrité --}}
        <div class="p-5 bg-blue-50/70 border border-blue-100 rounded-3xl flex items-start gap-4">
            <div class="w-10 h-10 rounded-2xl bg-blue-600 text-white flex items-center justify-center shrink-0 shadow-md shadow-blue-200">
                <i class="fas fa-shield-alt text-base"></i>
            </div>
            <div class="text-xs text-blue-900 leading-relaxed">
                <p class="font-black uppercase tracking-wider text-[11px] text-blue-700 mb-1">Règles d'intégrité du bail en cours</p>
                <p class="font-medium text-slate-600">
                    Pour garantir la traçabilité juridique et comptable de votre agence, <strong>le locataire bénéficiaire</strong>, <strong>le bien loué</strong>, <strong>la date de prise d'effet</strong> ainsi que <strong>la caution et les avances initiales</strong> sont scellés et ne peuvent pas être altérés.
                    Vous pouvez ajuster l'échéance du bail (prolongation), procéder à une révision du loyer, enregistrer un préavis ou compléter les observations.
                </p>
            </div>
        </div>

        <div class="premium-card p-6 sm:p-10 bg-white">
            <form method="POST" action="{{ route('contrats.update', $contrat) }}" class="space-y-10">
                @csrf
                @method('PATCH')

                {{-- SECTION 1 : INFORMATIONS IMMUABLES DU BAIL --}}
                <div class="space-y-6">
                    <div class="border-b border-slate-100 pb-3 flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <i class="fas fa-lock text-slate-400 text-xs"></i>
                            <h3 class="text-xs font-black text-slate-700 uppercase tracking-[0.2em]">Données Contractuelles Scellées (Non Modifiables)</h3>
                        </div>
                        <span class="text-[9px] font-black uppercase px-2.5 py-0.5 bg-slate-100 text-slate-500 rounded-full tracking-wider">
                            Fixées à la signature
                        </span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                        {{-- Carte Locataire --}}
                        <div class="p-5 bg-slate-50/80 rounded-2xl border border-slate-100 relative group">
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Locataire Bénéficiaire</span>
                                <span class="text-[9px] font-black text-slate-400"><i class="fas fa-lock text-[10px]"></i></span>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center font-black text-xs shrink-0">
                                    {{ strtoupper(substr($contrat->locataire->user->name ?? 'L', 0, 2)) }}
                                </div>
                                <div class="min-w-0">
                                    <p class="font-black text-slate-900 text-sm truncate">{{ $contrat->locataire->user->name ?? '-' }}</p>
                                    <p class="text-[11px] font-bold text-slate-500 truncate">{{ $contrat->locataire->user->telephone ?? '-' }}</p>
                                </div>
                            </div>
                            <div class="mt-3 pt-2.5 border-t border-slate-200/60 flex items-center justify-between text-[10px] text-slate-500 font-bold">
                                <span>Audit locataire :</span>
                                <span class="px-2 py-0.5 rounded-md font-black uppercase text-[9px] {{ ($contrat->locataire->statut_audit ?? '') === 'Validé' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                                    {{ $contrat->locataire->statut_audit ?? 'Non audité' }}
                                </span>
                            </div>
                        </div>

                        {{-- Carte Bien Loué --}}
                        <div class="p-5 bg-slate-50/80 rounded-2xl border border-slate-100 relative group">
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Propriété & Unité Locative Louée</span>
                                <span class="text-[9px] font-black text-slate-400"><i class="fas fa-lock text-[10px]"></i></span>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-purple-100 text-purple-700 flex items-center justify-center shrink-0">
                                    <i class="fas fa-door-open text-sm"></i>
                                </div>
                                <div class="min-w-0">
                                    <p class="font-black text-slate-900 text-sm truncate">{{ $contrat->bien->nom_bien ?? '-' }}</p>
                                    <p class="text-[11px] font-bold text-slate-500 truncate">
                                        {{ $contrat->bien->familleDeBien->nom_famille ?? ($contrat->bien->parent->nom_bien ?? 'Propriété') }}
                                    </p>
                                </div>
                            </div>
                            <div class="mt-3 pt-2.5 border-t border-slate-200/60 flex items-center justify-between text-[10px] text-slate-500 font-bold">
                                <span>Type :</span>
                                <span class="font-black text-slate-700 uppercase">{{ $contrat->bien->type_bien ?? '-' }}</span>
                            </div>
                        </div>

                        {{-- Carte Conditions d'Entrée & Caution --}}
                        <div class="p-5 bg-slate-50/80 rounded-2xl border border-slate-100 relative group md:col-span-2 lg:col-span-1">
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Caution & Prise d'effet</span>
                                <span class="text-[9px] font-black text-slate-400"><i class="fas fa-lock text-[10px]"></i></span>
                            </div>
                            <div class="space-y-1.5">
                                <div class="flex items-center justify-between text-xs">
                                    <span class="text-slate-500 font-bold">Date de début :</span>
                                    <span class="font-black text-slate-900">{{ $contrat->date_debut ? $contrat->date_debut->format('d/m/Y') : '-' }}</span>
                                </div>
                                <div class="flex items-center justify-between text-xs">
                                    <span class="text-slate-500 font-bold">Caution consignée :</span>
                                    <span class="font-black text-emerald-700">{{ number_format($contrat->montant_caution ?? 0, 0, ',', ' ') }} {{ Auth::user()->agence->devise }}</span>
                                </div>
                                <div class="flex items-center justify-between text-xs">
                                    <span class="text-slate-500 font-bold">Avance réglée :</span>
                                    <span class="font-black text-slate-700">{{ $contrat->nombre_mois_avance ?? 0 }} mois</span>
                                </div>
                            </div>
                            <div class="mt-3 pt-2.5 border-t border-slate-200/60 text-[9px] text-slate-400 font-semibold italic">
                                Dépôt consigné en grand-livre comptable
                            </div>
                        </div>
                    </div>
                </div>

                {{-- SECTION 2 : PARAMÈTRES MODIFIABLES DU BAIL --}}
                <div class="space-y-6 pt-4">
                    <div class="border-b border-slate-100 pb-3 flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <i class="fas fa-pen text-blue-600 text-xs"></i>
                            <h3 class="text-xs font-black text-blue-600 uppercase tracking-[0.2em]">Paramètres Modifiables du Contrat</h3>
                        </div>
                        <span class="text-[9px] font-black uppercase px-2.5 py-0.5 bg-blue-50 text-blue-700 rounded-full tracking-wider">
                            Modifications autorisées
                        </span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        {{-- Loyer mensuel révisé --}}
                        <div class="space-y-2">
                            <label for="montant_loyer_actuel" class="block text-[10px] font-black text-slate-700 uppercase tracking-[0.2em]">
                                Loyer Mensuel Révisé ({{ Auth::user()->agence->devise }}) <span class="text-rose-500">*</span>
                            </label>
                            <div class="relative">
                                <input id="montant_loyer_actuel" name="montant_loyer_actuel" type="number" step="0.01" min="0" required 
                                    class="block w-full px-5 py-4 bg-slate-50 border-slate-200 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-transparent rounded-2xl font-black text-base text-slate-900 transition-all"
                                    value="{{ old('montant_loyer_actuel', $contrat->montant_loyer_actuel) }}" />
                                <div class="absolute inset-y-0 right-4 flex items-center pointer-events-none text-xs font-black text-slate-400">
                                    {{ Auth::user()->agence->devise }} / mois
                                </div>
                            </div>
                            <p class="text-[10px] text-slate-400 font-medium">
                                En cas de révision de loyer, ce montant s'appliquera automatiquement aux prochaines factures générées.
                            </p>
                            <x-input-error :messages="$errors->get('montant_loyer_actuel')" class="mt-2" />
                        </div>

                        {{-- Date de fin / Échéance du bail --}}
                        <div class="space-y-2">
                            <label for="date_fin" class="block text-[10px] font-black text-slate-700 uppercase tracking-[0.2em]">
                                Date de Fin / Échéance du Bail
                            </label>
                            <input id="date_fin" name="date_fin" type="date" 
                                min="{{ $contrat->date_debut ? $contrat->date_debut->format('Y-m-d') : '' }}"
                                class="block w-full px-5 py-4 bg-slate-50 border-slate-200 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-transparent rounded-2xl font-bold text-sm text-slate-900 transition-all"
                                value="{{ old('date_fin', $contrat->date_fin ? $contrat->date_fin->format('Y-m-d') : '') }}" />
                            <p class="text-[10px] text-slate-400 font-medium">
                                Prolongez cette date pour renouveler le bail, ou laissez vide pour un bail à durée indéterminée.
                            </p>
                            <x-input-error :messages="$errors->get('date_fin')" class="mt-2" />
                        </div>

                        {{-- Date d'avis de congé / Préavis --}}
                        <div class="space-y-2">
                            <label for="date_avis_conge" class="block text-[10px] font-black text-slate-700 uppercase tracking-[0.2em]">
                                Date de Réception du Préavis de Départ (Optionnel)
                            </label>
                            <input id="date_avis_conge" name="date_avis_conge" type="date"
                                class="block w-full px-5 py-4 bg-slate-50 border-slate-200 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-transparent rounded-2xl font-bold text-sm text-slate-900 transition-all"
                                value="{{ old('date_avis_conge', $contrat->date_avis_conge ? $contrat->date_avis_conge->format('Y-m-d') : '') }}" />
                            <p class="text-[10px] text-slate-400 font-medium">
                                Renseignez la date si le locataire ou le propriétaire a signifié formellement son préavis de départ.
                            </p>
                            <x-input-error :messages="$errors->get('date_avis_conge')" class="mt-2" />
                        </div>

                        {{-- Frais de dossier / Honoraires --}}
                        <div class="space-y-2">
                            <label for="frais_dossier" class="block text-[10px] font-black text-slate-700 uppercase tracking-[0.2em]">
                                Frais de dossier / Honoraires d'agence
                            </label>
                            <div class="relative">
                                <input id="frais_dossier" name="frais_dossier" type="number" step="0.01" min="0"
                                    class="block w-full px-5 py-4 bg-slate-50 border-slate-200 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-transparent rounded-2xl font-bold text-sm text-slate-900 transition-all"
                                    value="{{ old('frais_dossier', $contrat->frais_dossier) }}" />
                                <div class="absolute inset-y-0 right-4 flex items-center pointer-events-none text-xs font-black text-slate-400">
                                    {{ Auth::user()->agence->devise }}
                                </div>
                            </div>
                            <x-input-error :messages="$errors->get('frais_dossier')" class="mt-2" />
                        </div>

                        {{-- État des lieux d'entrée / Remarques --}}
                        <div class="md:col-span-2 space-y-2">
                            <label for="etat_des_lieux_entree" class="block text-[10px] font-black text-slate-700 uppercase tracking-[0.2em]">
                                Observations Techniques & État des Lieux d'Entrée
                            </label>
                            <textarea id="etat_des_lieux_entree" name="etat_des_lieux_entree" rows="4"
                                class="block w-full px-5 py-4 bg-slate-50 border-slate-200 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-transparent rounded-2xl font-medium text-xs text-slate-700 transition-all leading-relaxed"
                                placeholder="Notes techniques, avenants, compteurs ou spécificités du logement...">{{ old('etat_des_lieux_entree', $contrat->etat_des_lieux_entree) }}</textarea>
                            <x-input-error :messages="$errors->get('etat_des_lieux_entree')" class="mt-2" />
                        </div>
                    </div>
                </div>

                {{-- BOUTONS D'ACTION --}}
                <div class="pt-6 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <a href="{{ route('contrats.show', $contrat) }}" class="text-xs font-black text-slate-500 hover:text-slate-800 uppercase tracking-widest transition">
                        Annuler les modifications
                    </a>
                    <button type="submit" class="btn-premium bg-slate-900 text-white px-10 py-4 uppercase tracking-widest text-xs font-black hover:bg-black shadow-xl transition transform hover:-translate-y-0.5">
                        <i class="fas fa-save mr-2"></i> Enregistrer les modifications
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>