<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 w-full">
            <div>
                <h2 class="font-black text-4xl text-slate-900 tracking-tight uppercase">
                    {{ __('Modifier : ') }} {{ $locataire->user->name }}
                </h2>
                <p class="text-slate-500 font-medium mt-1">Mise à jour du dossier locataire et des justificatifs</p>
            </div>
            
        
            <div class="mt-1">
                <a href="{{ route('locataires.index') }}" class="text-xs font-black text-slate-400 uppercase tracking-widest hover:text-slate-600 transition-colors self-start inline-flex">
                    <i class="fas fa-arrow-left mr-2 self-start inline-flex"></i> Retour à la liste
                </a>
            </div>
        </div></x-slot>

    <div class="space-y-8">
        <div class="premium-card p-10 bg-white">
            <form method="POST" action="{{ route('locataires.update', $locataire) }}" enctype="multipart/form-data" class="space-y-10">
                @csrf
                @method('PATCH')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Section Identité -->
                    <div class="md:col-span-2 border-b border-slate-50 pb-4">
                        <h3 class="text-sm font-black text-blue-600 uppercase tracking-[0.2em]">Identité & Profession</h3>
                    </div>

                    <div>
                        <label for="name" class="block text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] mb-2">Nom Complet</label>
                        <x-text-input id="name" name="name" type="text" class="block w-full" :value="old('name', $locataire->user->name)" required />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <div>
                        <label for="profession" class="block text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] mb-2">Profession</label>
                        <x-text-input id="profession" name="profession" type="text" class="block w-full" :value="old('profession', $locataire->profession)" />
                        <x-input-error :messages="$errors->get('profession')" class="mt-2" />
                    </div>

                    <!-- Section Contact -->
                    <div class="md:col-span-2 border-b border-slate-50 pb-4 mt-4">
                        <h3 class="text-sm font-black text-blue-600 uppercase tracking-[0.2em]">Coordonnées & Contact</h3>
                    </div>

                    <div>
                        <label for="email" class="block text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] mb-2">Adresse Email</label>
                        <x-text-input id="email" name="email" type="email" class="block w-full" :value="old('email', $locataire->user->email)" required />
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <div>
                        <label for="telephone" class="block text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] mb-2">Téléphone</label>
                        <x-text-input id="telephone" name="telephone" type="tel" class="block w-full phone-input" :value="old('telephone', $locataire->user->telephone)" />
                        <x-input-error :messages="$errors->get('telephone')" class="mt-2" />
                    </div>

                    <div>
                        <label for="contact_urgence" class="block text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] mb-2">Contact d'urgence</label>
                        <x-text-input id="contact_urgence" name="contact_urgence" type="tel" class="block w-full phone-input" :value="old('contact_urgence', $locataire->contact_urgence)" />
                        <x-input-error :messages="$errors->get('contact_urgence')" class="mt-2" />
                    </div>

                    <!-- Documents -->
                    <div class="md:col-span-2 border-b border-slate-50 pb-4 mt-4">
                        <h3 class="text-sm font-black text-blue-600 uppercase tracking-[0.2em]">Pièces Justificatives</h3>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] mb-4">Copie pièce d'identité</label>
                        
                        <div class="flex items-start gap-6">
                            @if($locataire->copie_piece_identite)
                            <div class="shrink-0">
                                <a href="{{ $locataire->getDocumentUrl('copie_piece_identite') }}" target="_blank" class="flex flex-col items-center justify-center w-32 h-32 bg-blue-50 border border-blue-100 rounded-2xl group hover:bg-blue-600 transition-all">
                                    <i class="fas fa-file-pdf text-2xl text-blue-600 group-hover:text-white mb-2"></i>
                                    <span class="text-[8px] font-black text-blue-600 group-hover:text-white uppercase">Voir l'actuelle</span>
                                </a>
                            </div>
                            @endif

                            <label for="copie_piece_identite" class="flex-1 flex flex-col items-center justify-center h-32 border-2 border-dashed border-slate-200 rounded-[2rem] cursor-pointer bg-slate-50 hover:bg-slate-100 transition-all group">
                                <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                    <i class="fas fa-cloud-upload-alt text-2xl text-slate-300 group-hover:text-blue-500 transition-colors mb-2"></i>
                                    <p class="text-xs text-slate-400 font-bold uppercase tracking-tighter">Remplacer le document</p>
                                    <p class="text-[10px] text-slate-300 font-medium mt-1">PDF, JPG ou PNG (MAX. 5MB)</p>
                                </div>
                                <input type="file" name="copie_piece_identite" id="copie_piece_identite" class="hidden">
                            </label>
                        </div>
                        <x-input-error :messages="$errors->get('copie_piece_identite')" class="mt-2" />
                    </div>
                </div>

                <div class="pt-10 flex justify-end">
                    <button type="submit" class="btn-premium bg-blue-600 text-white px-12 py-5 uppercase tracking-widest text-sm hover:bg-blue-700 shadow-2xl shadow-blue-200 transition transform hover:-translate-y-1">
                        Mettre à jour le dossier
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>