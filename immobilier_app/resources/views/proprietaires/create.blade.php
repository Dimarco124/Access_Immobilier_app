<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 w-full">
            <div>
                <h2 class="font-black text-4xl text-slate-900 tracking-tight uppercase">{{ __('Nouveau Propriétaire') }}</h2>
                <p class="text-slate-500 font-medium mt-1">Enregistrez un nouveau propriétaire et ses coordonnées de gestion</p>
            </div>
            
        
            <div class="mt-1">
                <a href="{{ route('proprietaires.index') }}" class="text-xs font-black text-slate-400 uppercase tracking-widest hover:text-slate-600 transition-colors self-start inline-flex">
                    <i class="fas fa-arrow-left mr-2 self-start inline-flex"></i> Annuler et retourner
                </a>
            </div>
        </div></x-slot>

    <div class="space-y-8">
        <div class="premium-card p-10 bg-white">
            <form method="POST" action="{{ route('proprietaires.store') }}" enctype="multipart/form-data" class="space-y-10">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Section Identité -->
                    <div class="md:col-span-2 border-b border-slate-50 pb-4">
                        <h3 class="text-sm font-black text-blue-600 uppercase tracking-[0.2em]">Identité du Propriétaire</h3>
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
                        <label for="numero_piece_identite" class="block text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] mb-2">N° Pièce d'identité</label>
                        <input id="numero_piece_identite" name="numero_piece_identite" type="text" class="block w-full px-5 py-4 bg-slate-50 border-slate-100 rounded-2xl focus:ring-2 focus:ring-blue-500 focus:border-transparent font-bold text-slate-700 transition-all" placeholder="Ex: CNI, Passeport..." />
                        <x-input-error :messages="$errors->get('numero_piece_identite')" class="mt-2" />
                    </div>

                    <div>
                        <label for="rib_bancaire" class="block text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] mb-2">RIB Bancaire</label>
                        <input id="rib_bancaire" name="rib_bancaire" type="text" class="block w-full px-5 py-4 bg-slate-50 border-slate-100 rounded-2xl focus:ring-2 focus:ring-blue-500 focus:border-transparent font-bold text-slate-700 transition-all" placeholder="Ex: FR76..." />
                        <x-input-error :messages="$errors->get('rib_bancaire')" class="mt-2" />
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
                        <label for="contact_secondaire" class="block text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] mb-2">Contact Secondaire</label>
                        <input id="contact_secondaire" name="contact_secondaire" type="tel" class="block w-full px-5 py-4 bg-slate-50 border-slate-100 rounded-2xl focus:ring-2 focus:ring-blue-500 focus:border-transparent font-bold text-slate-700 transition-all phone-input" />
                        <x-input-error :messages="$errors->get('contact_secondaire')" class="mt-2" />
                    </div>

                    <!-- Localisation -->
                    <div class="md:col-span-2 border-b border-slate-50 pb-4 mt-4">
                        <h3 class="text-sm font-black text-blue-600 uppercase tracking-[0.2em]">Résidence du Propriétaire</h3>
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
                    <div class="md:col-span-2 border-b border-slate-100 pb-4 mt-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-sm font-black text-blue-600 uppercase tracking-[0.2em]">Pièces Justificatives</h3>
                                <p class="text-xs text-slate-400 font-medium mt-0.5">Prenez en photo ou importez le recto et le verso de la pièce d'identité</p>
                            </div>
                            <span class="text-[10px] font-black uppercase tracking-widest px-3 py-1 bg-slate-100 text-slate-600 rounded-full">
                                Images & PDF (Max 5 Mo)
                            </span>
                        </div>
                    </div>

                    <!-- CNI Recto -->
                    <div class="space-y-2">
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">
                            Carte d'identité / Passeport (Recto)
                        </label>
                        
                        <div id="cni_recto_container" class="relative group">
                            <!-- Zone de sélection / capture initiale -->
                            <label for="cni_recto" id="cni_recto_dropzone" class="flex flex-col items-center justify-center w-full min-h-[170px] border-2 border-dashed border-slate-200 rounded-2xl cursor-pointer bg-slate-50 hover:bg-blue-50/50 hover:border-blue-400 transition-all p-6 text-center">
                                <div class="w-14 h-14 bg-white shadow-sm border border-slate-100 rounded-2xl flex items-center justify-center text-blue-600 mb-3 group-hover:scale-110 transition-transform">
                                    <i class="fas fa-camera text-xl"></i>
                                </div>
                                <span class="text-xs font-black text-slate-700 uppercase tracking-wider mb-1">Prendre une photo ou importer</span>
                                <span class="text-[10px] font-semibold text-slate-400">JPG, PNG ou PDF (Recto)</span>
                            </label>
                            
                            <!-- Zone d'aperçu de l'image (masquée au départ) -->
                            <div id="cni_recto_preview_box" class="hidden relative overflow-hidden rounded-2xl border-2 border-blue-500/20 bg-slate-900 shadow-md">
                                <img id="cni_recto_img" src="" alt="Aperçu CNI Recto" class="w-full h-48 object-cover cursor-pointer hover:opacity-95 transition-opacity" onclick="openPhotoModal(this.src, 'Pièce d\'identité (Recto)')">
                                
                                <div id="cni_recto_pdf_icon" class="hidden flex flex-col items-center justify-center h-48 bg-slate-50 text-slate-700">
                                    <i class="fas fa-file-pdf text-5xl text-rose-500 mb-2"></i>
                                    <span id="cni_recto_pdf_name" class="text-xs font-bold text-slate-700 px-4 truncate max-w-full">document.pdf</span>
                                </div>

                                <div class="absolute top-3 left-3 bg-emerald-500 text-white text-[10px] font-black uppercase tracking-wider px-3 py-1 rounded-full shadow-lg flex items-center gap-1.5 backdrop-blur-sm">
                                    <i class="fas fa-check-circle"></i> Photo prête
                                </div>

                                <div class="absolute bottom-3 inset-x-3 flex items-center justify-between gap-2 bg-slate-900/80 backdrop-blur-md p-2 rounded-xl">
                                    <button type="button" onclick="openPhotoModal(document.getElementById('cni_recto_img').src, 'Pièce d\'identité (Recto)')" class="text-[11px] font-black text-white hover:text-blue-300 uppercase tracking-wider px-2 py-1 flex items-center gap-1.5 transition">
                                        <i class="fas fa-expand"></i> Agrandir
                                    </button>
                                    <div class="flex items-center gap-1.5">
                                        <button type="button" onclick="document.getElementById('cni_recto').click()" class="px-3 py-1 bg-white text-slate-800 text-[10px] font-black uppercase rounded-lg hover:bg-slate-100 transition">
                                            <i class="fas fa-redo-alt mr-1"></i> Changer
                                        </button>
                                        <button type="button" onclick="removePhoto('cni_recto')" class="px-2.5 py-1 bg-rose-500/80 text-white text-[10px] font-black uppercase rounded-lg hover:bg-rose-600 transition">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <input type="file" name="cni_recto" id="cni_recto" class="hidden" accept="image/*,application/pdf" capture="environment" onchange="previewIdCard(event, 'cni_recto')">
                        </div>
                        <x-input-error :messages="$errors->get('cni_recto')" class="mt-2" />
                    </div>

                    <!-- CNI Verso -->
                    <div class="space-y-2">
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-[0.2em]">
                            Carte d'identité / Passeport (Verso)
                        </label>
                        
                        <div id="cni_verso_container" class="relative group">
                            <!-- Zone de sélection / capture initiale -->
                            <label for="cni_verso" id="cni_verso_dropzone" class="flex flex-col items-center justify-center w-full min-h-[170px] border-2 border-dashed border-slate-200 rounded-2xl cursor-pointer bg-slate-50 hover:bg-blue-50/50 hover:border-blue-400 transition-all p-6 text-center">
                                <div class="w-14 h-14 bg-white shadow-sm border border-slate-100 rounded-2xl flex items-center justify-center text-blue-600 mb-3 group-hover:scale-110 transition-transform">
                                    <i class="fas fa-camera text-xl"></i>
                                </div>
                                <span class="text-xs font-black text-slate-700 uppercase tracking-wider mb-1">Prendre une photo ou importer</span>
                                <span class="text-[10px] font-semibold text-slate-400">JPG, PNG ou PDF (Verso)</span>
                            </label>
                            
                            <!-- Zone d'aperçu de l'image (masquée au départ) -->
                            <div id="cni_verso_preview_box" class="hidden relative overflow-hidden rounded-2xl border-2 border-blue-500/20 bg-slate-900 shadow-md">
                                <img id="cni_verso_img" src="" alt="Aperçu CNI Verso" class="w-full h-48 object-cover cursor-pointer hover:opacity-95 transition-opacity" onclick="openPhotoModal(this.src, 'Pièce d\'identité (Verso)')">
                                
                                <div id="cni_verso_pdf_icon" class="hidden flex flex-col items-center justify-center h-48 bg-slate-50 text-slate-700">
                                    <i class="fas fa-file-pdf text-5xl text-rose-500 mb-2"></i>
                                    <span id="cni_verso_pdf_name" class="text-xs font-bold text-slate-700 px-4 truncate max-w-full">document.pdf</span>
                                </div>

                                <div class="absolute top-3 left-3 bg-emerald-500 text-white text-[10px] font-black uppercase tracking-wider px-3 py-1 rounded-full shadow-lg flex items-center gap-1.5 backdrop-blur-sm">
                                    <i class="fas fa-check-circle"></i> Photo prête
                                </div>

                                <div class="absolute bottom-3 inset-x-3 flex items-center justify-between gap-2 bg-slate-900/80 backdrop-blur-md p-2 rounded-xl">
                                    <button type="button" onclick="openPhotoModal(document.getElementById('cni_verso_img').src, 'Pièce d\'identité (Verso)')" class="text-[11px] font-black text-white hover:text-blue-300 uppercase tracking-wider px-2 py-1 flex items-center gap-1.5 transition">
                                        <i class="fas fa-expand"></i> Agrandir
                                    </button>
                                    <div class="flex items-center gap-1.5">
                                        <button type="button" onclick="document.getElementById('cni_verso').click()" class="px-3 py-1 bg-white text-slate-800 text-[10px] font-black uppercase rounded-lg hover:bg-slate-100 transition">
                                            <i class="fas fa-redo-alt mr-1"></i> Changer
                                        </button>
                                        <button type="button" onclick="removePhoto('cni_verso')" class="px-2.5 py-1 bg-rose-500/80 text-white text-[10px] font-black uppercase rounded-lg hover:bg-rose-600 transition">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <input type="file" name="cni_verso" id="cni_verso" class="hidden" accept="image/*,application/pdf" capture="environment" onchange="previewIdCard(event, 'cni_verso')">
                        </div>
                        <x-input-error :messages="$errors->get('cni_verso')" class="mt-2" />
                    </div>
                </div>

                <div class="pt-10 flex justify-end">
                    <button type="submit" class="btn-premium bg-blue-600 text-white px-12 py-5 uppercase tracking-widest text-sm hover:bg-blue-700 shadow-2xl shadow-blue-200 transition transform hover:-translate-y-1 font-black">
                        Enregistrer le Propriétaire
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal d'aperçu d'image en plein écran dans l'application -->
    <div id="photoModal" class="fixed inset-0 z-50 bg-slate-900/90 backdrop-blur-md hidden items-center justify-center p-4">
        <div class="relative max-w-4xl w-full bg-white rounded-3xl overflow-hidden shadow-2xl flex flex-col max-h-[90vh]">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between bg-slate-50">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center">
                        <i class="fas fa-id-card"></i>
                    </div>
                    <div>
                        <h4 id="photoModalTitle" class="text-sm font-black text-slate-900 uppercase tracking-tight">Pièce d'identité</h4>
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Aperçu direct de la photo capturée</p>
                    </div>
                </div>
                <button type="button" onclick="closePhotoModal()" class="w-10 h-10 rounded-xl bg-slate-200/60 hover:bg-slate-200 text-slate-700 flex items-center justify-center transition">
                    <i class="fas fa-times text-base"></i>
                </button>
            </div>
            <div class="p-4 flex-1 overflow-auto flex items-center justify-center bg-slate-950">
                <img id="photoModalImg" src="" alt="Aperçu grand format" class="max-w-full max-h-[70vh] object-contain rounded-xl shadow-lg">
            </div>
            <div class="p-4 border-t border-slate-100 bg-white flex justify-end">
                <button type="button" onclick="closePhotoModal()" class="px-6 py-2.5 bg-slate-900 text-white text-xs font-black uppercase rounded-xl hover:bg-black transition">
                    Fermer
                </button>
            </div>
        </div>
    </div>

    <script>
        function previewIdCard(event, fieldName) {
            const file = event.target.files[0];
            if (!file) return;

            const dropzone = document.getElementById(`${fieldName}_dropzone`);
            const previewBox = document.getElementById(`${fieldName}_preview_box`);
            const img = document.getElementById(`${fieldName}_img`);
            const pdfIcon = document.getElementById(`${fieldName}_pdf_icon`);
            const pdfName = document.getElementById(`${fieldName}_pdf_name`);

            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    img.src = e.target.result;
                    img.classList.remove('hidden');
                    pdfIcon.classList.add('hidden');
                    dropzone.classList.add('hidden');
                    previewBox.classList.remove('hidden');
                };
                reader.readAsDataURL(file);
            } else if (file.type === 'application/pdf') {
                pdfName.textContent = file.name;
                img.classList.add('hidden');
                pdfIcon.classList.remove('hidden');
                dropzone.classList.add('hidden');
                previewBox.classList.remove('hidden');
            }
        }

        function removePhoto(fieldName) {
            const input = document.getElementById(fieldName);
            input.value = '';
            document.getElementById(`${fieldName}_dropzone`).classList.remove('hidden');
            document.getElementById(`${fieldName}_preview_box`).classList.add('hidden');
            document.getElementById(`${fieldName}_img`).src = '';
        }

        function openPhotoModal(src, title) {
            if (!src) return;
            document.getElementById('photoModalImg').src = src;
            document.getElementById('photoModalTitle').textContent = title;
            const modal = document.getElementById('photoModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.style.overflow = 'hidden';
        }

        function closePhotoModal() {
            const modal = document.getElementById('photoModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.getElementById('photoModalImg').src = '';
            document.body.style.overflow = '';
        }

        // Fermeture sur clic arrière-plan ou Échap
        window.addEventListener('click', function(e) {
            const modal = document.getElementById('photoModal');
            if (e.target === modal) {
                closePhotoModal();
            }
        });
        window.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closePhotoModal();
            }
        });
    </script>
</x-app-layout>