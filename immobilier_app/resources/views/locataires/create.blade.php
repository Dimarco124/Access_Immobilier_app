<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 w-full">
            <div>
                <h2 class="font-black text-4xl text-slate-900 tracking-tight uppercase">{{ __('Nouveau Locataire') }}</h2>
                <p class="text-slate-500 font-medium mt-1">Créez un compte pour un nouveau résident ou client</p>
            </div>
            
        
            <div class="mt-1">
                <a href="{{ route('locataires.index') }}" class="text-xs font-black text-slate-400 uppercase tracking-widest hover:text-slate-600 transition-colors self-start inline-flex">
                    <i class="fas fa-arrow-left mr-2 self-start inline-flex"></i> Annuler et retourner
                </a>
            </div>
        </div></x-slot>

    <div class="space-y-8">
        <div class="premium-card p-10 bg-white">
            <form method="POST" action="{{ route('locataires.store') }}" enctype="multipart/form-data" class="space-y-10">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Section Identité -->
                    <div class="md:col-span-2 border-b border-slate-50 pb-4">
                        <h3 class="text-sm font-black text-blue-600 uppercase tracking-[0.2em]">Identité du Locataire</h3>
                    </div>

                    <div>
                        <label for="civilite" class="block text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] mb-2">Civilité <span class="text-red-500">*</span></label>
                        <select id="civilite" name="civilite" class="block w-full p-4 bg-slate-50 border-slate-100 rounded-2xl focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm font-bold text-slate-700 transition-all">
                            <option value="M.">Monsieur (M.)</option>
                            <option value="Mme">Madame (Mme)</option>
                            <option value="Mlle">Mademoiselle (Mlle)</option>
                        </select>
                        <x-input-error :messages="$errors->get('civilite')" class="mt-2" />
                    </div>

                    <div>
                        <label for="name" class="block text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] mb-2">Nom Complet <span class="text-red-500">*</span></label>
                        <input id="name" name="name" type="text" class="block w-full px-5 py-4 bg-slate-50 border-slate-100 rounded-2xl focus:ring-2 focus:ring-blue-500 focus:border-transparent font-bold text-slate-700 transition-all" placeholder="Ex: Jean Dupont" required />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <div>
                        <label for="profession" class="block text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] mb-2">Profession / Activité</label>
                        <input id="profession" name="profession" type="text" class="block w-full px-5 py-4 bg-slate-50 border-slate-100 rounded-2xl focus:ring-2 focus:ring-blue-500 focus:border-transparent font-bold text-slate-700 transition-all" placeholder="Ex: Ingénieur, Commerçant..." />
                        <x-input-error :messages="$errors->get('profession')" class="mt-2" />
                    </div>

                    <div>
                        <label for="numero_piece_identite" class="block text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] mb-2">N° Pièce d'identité</label>
                        <input id="numero_piece_identite" name="numero_piece_identite" type="text" class="block w-full px-5 py-4 bg-slate-50 border-slate-100 rounded-2xl focus:ring-2 focus:ring-blue-500 focus:border-transparent font-bold text-slate-700 transition-all" placeholder="Ex: CNI, Passeport..." />
                        <x-input-error :messages="$errors->get('numero_piece_identite')" class="mt-2" />
                    </div>

                    <!-- Section Contact -->
                    <div class="md:col-span-2 border-b border-slate-50 pb-4 mt-4">
                        <h3 class="text-sm font-black text-blue-600 uppercase tracking-[0.2em]">Coordonnées & Accès</h3>
                    </div>

                    <div>
                        <label for="email" class="block text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] mb-2">Adresse Email <span class="text-red-500">*</span></label>
                        <input id="email" name="email" type="email" class="block w-full px-5 py-4 bg-slate-50 border-slate-100 rounded-2xl focus:ring-2 focus:ring-blue-500 focus:border-transparent font-bold text-slate-700 transition-all" placeholder="email@exemple.com" required />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <div>
                        <label for="password" class="block text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] mb-2">Mot de passe provisoire <span class="text-red-500">*</span></label>
                        <input id="password" name="password" type="password" class="block w-full px-5 py-4 bg-slate-50 border-slate-100 rounded-2xl focus:ring-2 focus:ring-blue-500 focus:border-transparent font-bold text-slate-700 transition-all" placeholder="••••••••" required />
                        <x-input-error :messages="$errors->get('password')" class="mt-2" />
                    </div>

                    <div>
                        <label for="telephone" class="block text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] mb-2">N° Téléphone Principal</label>
                        <input id="telephone" name="telephone" type="tel" class="block w-full px-5 py-4 bg-slate-50 border-slate-100 rounded-2xl focus:ring-2 focus:ring-blue-500 focus:border-transparent font-bold text-slate-700 transition-all phone-input" />
                        <x-input-error :messages="$errors->get('telephone')" class="mt-2" />
                    </div>

                    <div>
                        <label for="contact_urgence" class="block text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] mb-2">Contact d'urgence</label>
                        <input id="contact_urgence" name="contact_urgence" type="tel" class="block w-full px-5 py-4 bg-slate-50 border-slate-100 rounded-2xl focus:ring-2 focus:ring-blue-500 focus:border-transparent font-bold text-slate-700 transition-all phone-input" />
                        <x-input-error :messages="$errors->get('contact_urgence')" class="mt-2" />
                    </div>

                    <!-- Localisation -->
                    <div class="md:col-span-2 border-b border-slate-50 pb-4 mt-4">
                        <h3 class="text-sm font-black text-blue-600 uppercase tracking-[0.2em]">Résidence du Locataire</h3>
                    </div>

                    <div>
                        <label for="pays" class="block text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] mb-2">Pays</label>
                        <input id="pays" name="pays" type="text" class="block w-full px-5 py-4 bg-slate-50 border-slate-100 rounded-2xl focus:ring-2 focus:ring-blue-500 focus:border-transparent font-bold text-slate-700 transition-all" placeholder="Ex: Côte d'Ivoire" />
                        <x-input-error :messages="$errors->get('pays')" class="mt-2" />
                    </div>

                    <div>
                        <label for="ville" class="block text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] mb-2">Ville</label>
                        <input id="ville" name="ville" type="text" class="block w-full px-5 py-4 bg-slate-50 border-slate-100 rounded-2xl focus:ring-2 focus:ring-blue-500 focus:border-transparent font-bold text-slate-700 transition-all" placeholder="Ex: Abidjan" />
                        <x-input-error :messages="$errors->get('ville')" class="mt-2" />
                    </div>

                    <div class="md:col-span-2">
                        <label for="adresse_residence" class="block text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] mb-2">Adresse de résidence</label>
                        <input id="adresse_residence" name="adresse_residence" type="text" class="block w-full px-5 py-4 bg-slate-50 border-slate-100 rounded-2xl focus:ring-2 focus:ring-blue-500 focus:border-transparent font-bold text-slate-700 transition-all" placeholder="Ex: Cocody, Rue des jardins..." />
                        <x-input-error :messages="$errors->get('adresse_residence')" class="mt-2" />
                    </div>

                    <!-- Documents -->
                    <div class="md:col-span-2 border-b border-slate-50 pb-4 mt-4">
                        <h3 class="text-sm font-black text-blue-600 uppercase tracking-[0.2em]">Pièces Justificatives</h3>
                    </div>

                    <div>
                        <label for="cni_recto" class="block text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] mb-2">Carte d'identité (Recto)</label>
                        <label for="cni_recto" class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed border-slate-200 rounded-[2rem] cursor-pointer bg-slate-50 hover:bg-slate-100 transition-all group">
                            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                <i class="fas fa-id-card text-2xl text-slate-300 group-hover:text-blue-500 transition-colors mb-2"></i>
                                <p class="text-[9px] text-slate-400 font-bold uppercase tracking-tighter">Recto de la pièce</p>
                            </div>
                            <input type="file" name="cni_recto" id="cni_recto" class="hidden" accept="image/*,.pdf">
                        </label>
                        <x-input-error :messages="$errors->get('cni_recto')" class="mt-2" />
                    </div>

                    <div>
                        <label for="cni_verso" class="block text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] mb-2">Carte d'identité (Verso)</label>
                        <label for="cni_verso" class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed border-slate-200 rounded-[2rem] cursor-pointer bg-slate-50 hover:bg-slate-100 transition-all group">
                            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                <i class="fas fa-id-card text-2xl text-slate-300 group-hover:text-blue-500 transition-colors mb-2"></i>
                                <p class="text-[9px] text-slate-400 font-bold uppercase tracking-tighter">Verso de la pièce</p>
                            </div>
                            <input type="file" name="cni_verso" id="cni_verso" class="hidden" accept="image/*,.pdf">
                        </label>
                        <x-input-error :messages="$errors->get('cni_verso')" class="mt-2" />
                    </div>
                </div>

                <div class="pt-10 flex justify-end">
                    <button type="submit" class="btn-premium bg-blue-600 text-white px-12 py-5 uppercase tracking-widest text-sm hover:bg-blue-700 shadow-2xl shadow-blue-200 transition transform hover:-translate-y-1">
                        Enregistrer le Locataire
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>