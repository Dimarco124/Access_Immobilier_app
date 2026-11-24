<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2">
            <h2 class="font-black text-4xl text-slate-900 tracking-tight uppercase">
                {{ __('Configuration de l\'Agence') }}
            </h2>
            <p class="text-slate-500 font-medium">Gérez les paramètres globaux, les finances et les comptes de votre agence</p>
        </div>
    </x-slot>

    <div class="space-y-8">
        @if (session('success'))
            <div class="premium-card bg-green-50 border-green-100 p-6 flex items-center gap-4 animate-bounce">
                <div class="w-10 h-10 bg-green-500 rounded-xl flex items-center justify-center text-white">
                    <i class="fas fa-check"></i>
                </div>
                <p class="text-green-700 font-bold">{{ session('success') }}</p>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Informations Générales -->
            <div class="premium-card p-8 bg-white">
                <header class="mb-8">
                    <h3 class="text-lg font-black text-slate-900 uppercase tracking-tight">Informations Générales</h3>
                    <p class="text-sm text-slate-500 font-medium mt-1">Identité et coordonnées de l'agence.</p>
                </header>

                <form method="post" action="{{ route('agence.parametres.update') }}" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    @method('patch')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <x-input-label for="nom" :value="__('Nom de l\'agence')" class="text-[10px] font-black uppercase text-slate-400 tracking-widest mb-2" />
                            <x-text-input id="nom" name="nom" type="text" class="block w-full" :value="old('nom', $agence->nom)" required />
                            <x-input-error class="mt-2" :messages="$errors->get('nom')" />
                        </div>

                        <div>
                            <x-input-label for="email" :value="__('Email Professionnel')" class="text-[10px] font-black uppercase text-slate-400 tracking-widest mb-2" />
                            <x-text-input id="email" name="email" type="email" class="block w-full" :value="old('email', $agence->email)" required />
                            <x-input-error class="mt-2" :messages="$errors->get('email')" />
                        </div>

                        <div>
                            <x-input-label for="telephone" :value="__('Téléphone')" class="text-[10px] font-black uppercase text-slate-400 tracking-widest mb-2" />
                            <x-text-input id="telephone" name="telephone" type="tel" class="block w-full phone-input" :value="old('telephone', $agence->telephone)" required />
                            <x-input-error class="mt-2" :messages="$errors->get('telephone')" />
                        </div>

                        <div class="md:col-span-2">
                            <x-input-label for="adresse" :value="__('Adresse Physique')" class="text-[10px] font-black uppercase text-slate-400 tracking-widest mb-2" />
                            <x-text-input id="adresse" name="adresse" type="text" class="block w-full" :value="old('adresse', $agence->adresse)" required />
                            <x-input-error class="mt-2" :messages="$errors->get('adresse')" />
                        </div>

                        <div>
                            <x-input-label for="taux_tva" :value="__('Taux TVA (%)')" class="text-[10px] font-black uppercase text-slate-400 tracking-widest mb-2" />
                            <x-text-input id="taux_tva" name="taux_tva" type="number" step="0.01" class="block w-full" :value="old('taux_tva', $agence->taux_tva)" required />
                            <x-input-error class="mt-2" :messages="$errors->get('taux_tva')" />
                        </div>

                        <div>
                            <x-input-label for="devise" :value="__('Devise')" class="text-[10px] font-black uppercase text-slate-400 tracking-widest mb-2" />
                            <select id="devise" name="devise" class="block w-full border-slate-200 focus:border-blue-500 focus:ring-blue-500 rounded-xl shadow-sm">
                                <option value="XOF" @selected($agence->devise == 'XOF')>Franc CFA (XOF)</option>
                                <option value="EUR" @selected($agence->devise == 'EUR')>Euro (€)</option>
                                <option value="USD" @selected($agence->devise == 'USD')>Dollar ($)</option>
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('devise')" />
                        </div>

                        <div class="md:col-span-2">
                            <x-input-label for="logo" :value="__('Logo de l\'agence')" class="text-[10px] font-black uppercase text-slate-400 tracking-widest mb-2" />
                            <div class="flex items-center gap-4 mt-2">
                                @if($agence->logo_url)
                                    <img src="{{ $agence->logo_url }}" alt="Logo" class="h-16 w-16 object-contain rounded-lg border border-slate-100 p-1 bg-slate-50">
                                @endif
                                <input type="file" id="logo" name="logo" class="block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-black file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 transition-all" />
                            </div>
                            <x-input-error class="mt-2" :messages="$errors->get('logo')" />
                        </div>
                    </div>

                    <div class="pt-4">
                        <x-primary-button class="w-full justify-center py-4 bg-blue-600 hover:bg-blue-700 text-xs font-black uppercase tracking-widest rounded-xl transition-all shadow-lg shadow-blue-100">
                            {{ __('Enregistrer les modifications') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>

            <div class="space-y-8">
                <!-- Paramètres de Paiement -->
                <div class="premium-card p-8 bg-white border-2 border-blue-50">
                    <header class="mb-8">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center text-blue-600">
                                <i class="fas fa-credit-card"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-black text-slate-900 uppercase tracking-tight">Paiement & Retards</h3>
                                <p class="text-sm text-slate-500 font-medium">Automatisation des frais de retard.</p>
                            </div>
                        </div>
                    </header>

                    <form method="post" action="{{ route('agence.parametres.update_paiement') }}" class="space-y-6">
                        @csrf
                        @method('patch')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <x-input-label for="jour_echeance_loyer" :value="__('Jour d\'échéance du loyer')" class="text-[10px] font-black uppercase text-slate-400 tracking-widest mb-2" />
                                <div class="relative">
                                    <x-text-input id="jour_echeance_loyer" name="jour_echeance_loyer" type="number" min="1" max="31" class="block w-full pr-12" :value="old('jour_echeance_loyer', $agence->parametres_paiement['jour_echeance_loyer'] ?? 5)" required />
                                    <span class="absolute right-4 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400 uppercase">du mois</span>
                                </div>
                            </div>

                            <div>
                                <x-input-label for="jours_grace_retard" :value="__('Période de grâce')" class="text-[10px] font-black uppercase text-slate-400 tracking-widest mb-2" />
                                <div class="relative">
                                    <x-text-input id="jours_grace_retard" name="jours_grace_retard" type="number" min="0" class="block w-full pr-12" :value="old('jours_grace_retard', $agence->parametres_paiement['jours_grace_retard'] ?? 3)" required />
                                    <span class="absolute right-4 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400 uppercase">jour(s)</span>
                                </div>
                            </div>

                            <div>
                                <x-input-label for="frais_retard_type" :value="__('Type de frais')" class="text-[10px] font-black uppercase text-slate-400 tracking-widest mb-2" />
                                <select id="frais_retard_type" name="frais_retard_type" class="block w-full border-slate-200 focus:border-blue-500 focus:ring-blue-500 rounded-xl shadow-sm">
                                    <option value="pourcentage" @selected(($agence->parametres_paiement['frais_retard_type'] ?? '') == 'pourcentage')>Pourcentage (%)</option>
                                    <option value="fixe" @selected(($agence->parametres_paiement['frais_retard_type'] ?? '') == 'fixe')>Montant Fixe</option>
                                </select>
                            </div>

                            <div>
                                <x-input-label for="frais_retard_valeur" :value="__('Valeur des frais')" class="text-[10px] font-black uppercase text-slate-400 tracking-widest mb-2" />
                                <x-text-input id="frais_retard_valeur" name="frais_retard_valeur" type="number" step="0.01" class="block w-full" :value="old('frais_retard_valeur', $agence->parametres_paiement['frais_retard_valeur'] ?? 5)" required />
                            </div>


                        </div>

                        <x-primary-button class="w-full justify-center py-4 bg-slate-900 hover:bg-black text-xs font-black uppercase tracking-widest rounded-xl transition-all">
                            {{ __('Mettre à jour les frais') }}
                        </x-primary-button>
                    </form>
                </div>

                <!-- Comptes Bancaires -->
                <div class="premium-card p-8 bg-white">
                    <header class="mb-8">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-slate-100 rounded-xl flex items-center justify-center text-slate-600">
                                <i class="fas fa-university"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-black text-slate-900 uppercase tracking-tight">Comptes Bancaires</h3>
                                <p class="text-sm text-slate-500 font-medium">Comptes pour les virements locataires.</p>
                            </div>
                        </div>
                    </header>

                    <div class="space-y-4">
                        @foreach($agence->comptesBancaires as $compte)
                            <div class="flex justify-between items-center p-4 bg-slate-50 rounded-2xl border border-slate-100 group hover:border-blue-200 transition-all">
                                <div>
                                    <div class="font-black text-slate-900 uppercase text-xs tracking-wider">{{ $compte->nom_banque }}</div>
                                    <div class="text-sm font-bold text-blue-600 mt-1">{{ $compte->numero_compte }}</div>
                                    <div class="text-[10px] text-slate-400 font-medium mt-1">{{ $compte->titulaire_compte }}</div>
                                </div>
                                <form method="post" action="{{ route('agence.comptes.delete', $compte) }}" onsubmit="return confirm('Supprimer ce compte ?')">
                                    @csrf
                                    @method('delete')
                                    <button type="submit" class="w-8 h-8 rounded-lg bg-red-50 text-red-500 flex items-center justify-center hover:bg-red-500 hover:text-white transition-all">
                                        <i class="fas fa-trash-alt text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        @endforeach

                        <button onclick="document.getElementById('add-bank-modal').classList.remove('hidden')" class="w-full py-4 border-2 border-dashed border-slate-200 rounded-2xl text-slate-400 font-black text-[10px] uppercase tracking-widest hover:border-blue-300 hover:text-blue-500 transition-all">
                            <i class="fas fa-plus mr-2"></i> Ajouter un compte
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Ajout Compte (Simplifié) -->
    <div id="add-bank-modal" class="fixed inset-0 bg-slate-900/60 z-50 flex items-center justify-center hidden backdrop-blur-sm p-4 overflow-y-auto">
        <div class="bg-white rounded-[2rem] p-8 md:p-10 max-w-md w-full mx-auto my-auto shadow-2xl relative">
            <h3 class="text-xl font-black text-slate-900 uppercase tracking-tight mb-6 text-center">Nouveau Compte Bancaire</h3>
            <form method="post" action="{{ route('agence.comptes.add') }}" class="space-y-4">
                @csrf
                <div>
                    <x-input-label for="nom_banque" :value="__('Nom de la Banque')" class="text-[10px] font-black uppercase text-slate-400 mb-2" />
                    <x-text-input id="nom_banque" name="nom_banque" type="text" class="block w-full" required />
                </div>
                <div>
                    <x-input-label for="numero_compte" :value="__('Numéro de Compte / IBAN')" class="text-[10px] font-black uppercase text-slate-400 mb-2" />
                    <x-text-input id="numero_compte" name="numero_compte" type="text" class="block w-full" required />
                </div>
                <div>
                    <x-input-label for="titulaire_compte" :value="__('Titulaire du Compte')" class="text-[10px] font-black uppercase text-slate-400 mb-2" />
                    <x-text-input id="titulaire_compte" name="titulaire_compte" type="text" class="block w-full" required />
                </div>
                <div class="flex gap-4 pt-6">
                    <button type="button" onclick="document.getElementById('add-bank-modal').classList.add('hidden')" class="flex-1 py-4 bg-slate-100 text-slate-600 font-black text-[10px] uppercase tracking-widest rounded-xl hover:bg-slate-200 transition-all">Annuler</button>
                    <button type="submit" class="flex-1 py-4 bg-blue-600 text-white font-black text-[10px] uppercase tracking-widest rounded-xl hover:bg-blue-700 shadow-lg shadow-blue-100 transition-all">Ajouter</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
