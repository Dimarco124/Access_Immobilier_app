<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 w-full">
            <div>
                <h2 class="font-black text-4xl text-slate-900 tracking-tight uppercase">
                    Nouveau Contrat
                </h2>
                <p class="text-slate-500 font-medium mt-1">Formalisation d'un bail de location et état des lieux</p>
            </div>
            
        
            <div class="mt-1">
                <a href="{{ route('contrats.index') }}" class="text-xs font-black text-slate-400 uppercase tracking-widest hover:text-slate-600 transition-colors self-start inline-flex">
                    <i class="fas fa-arrow-left mr-2 self-start inline-flex"></i> Annuler et retour
                </a>
            </div>
        </div></x-slot>

    <div class="space-y-8">
        <div class="premium-card p-10 bg-white">
            <form method="POST" action="{{ route('contrats.store') }}" class="space-y-10">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                    <!-- Parties & Unité -->
                    <div class="md:col-span-2 border-b border-slate-50 pb-4">
                        <h3 class="text-sm font-black text-blue-600 uppercase tracking-[0.2em]">Identification du Bail</h3>
                    </div>

                    <div>
                        <label for="locataire_id" class="block text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] mb-2">Locataire Bénéficiaire</label>
                        <select id="locataire_id" name="locataire_id" class="block w-full border-slate-200 focus:border-blue-500 focus:ring-blue-500 rounded-xl shadow-sm font-bold text-slate-700" required>
                            <option value="">Sélectionnez un locataire</option>
                            @foreach($locataires as $locataire)
                            <option value="{{ $locataire->id }}" data-audit="{{ $locataire->statut_audit ?? '' }}" {{ old('locataire_id') == $locataire->id ? 'selected' : '' }}>
                                {{ $locataire->user->name }}
                            </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('locataire_id')" class="mt-2" />
                        <div id="audit-warning" class="hidden mt-2 p-3 bg-amber-50 border border-amber-200 rounded-lg">
                            <p class="text-xs font-bold text-amber-700">
                                <i class="fas fa-exclamation-triangle mr-1"></i> Ce locataire n'a pas encore été audité !
                            </p>
                        </div>
                    </div>

                    <!-- Structure Propriété → Unité Locative -->
                    <div>
                        <label for="famille_de_bien_id" class="block text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] mb-2">Propriété</label>
                        <select id="famille_de_bien_id" name="famille_de_bien_id" class="block w-full border-slate-200 focus:border-blue-500 focus:ring-blue-500 rounded-xl shadow-sm font-bold text-slate-700" required onchange="updateBiens()">
                            <option value="">Sélectionnez une propriété</option>
                            @foreach($familles as $famille)
                            <option value="{{ $famille->id }}" {{ old('famille_de_bien_id') == $famille->id ? 'selected' : '' }}>
                                {{ $famille->nom_famille }} ({{ $famille->nature === 'individuelle' ? 'Bien individuel' : 'Collectif / Immeuble' }})
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Section Unité Locative Dynamique (Auto si individuel, Sélecteur si collectif) -->
                    <div class="md:col-span-2 space-y-3" id="wrapper-unite-locative">
                        <!-- Placeholder quand aucune propriété n'est encore sélectionnée -->
                        <div id="unite-placeholder" class="p-4 rounded-2xl border border-dashed border-slate-200 bg-slate-50/60 text-slate-400 text-xs flex items-center justify-center gap-2.5">
                            <i class="fas fa-arrow-up text-blue-500"></i>
                            <span class="font-bold text-slate-500">Sélectionnez une propriété ci-dessus pour charger l'unité locative</span>
                        </div>

                        <!-- CAS 1: Confirmation automatique pour Bien Individuel (Mono-unité) -->
                        <div id="card-unite-individuelle" class="hidden p-5 bg-gradient-to-r from-blue-50/80 via-white to-blue-50/40 border-2 border-blue-200/90 rounded-2xl shadow-sm">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                <div class="flex items-center gap-3.5">
                                    <div class="w-12 h-12 rounded-2xl bg-blue-600 text-white flex items-center justify-center text-lg font-black shadow-lg shadow-blue-500/25 shrink-0">
                                        <i class="fas fa-home"></i>
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <span class="text-sm font-black text-slate-900" id="nom-unite-auto">—</span>
                                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-blue-100 text-blue-700 border border-blue-200">
                                                Bien individuel (mono-unité)
                                            </span>
                                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-100 text-emerald-700 border border-emerald-200 flex items-center gap-1.5">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                Disponible
                                            </span>
                                        </div>
                                        <p class="text-xs text-blue-700 font-bold mt-1 flex items-center gap-1.5">
                                            <i class="fas fa-check-circle text-blue-600"></i>
                                            <span>Propriété individuelle : unité locative sélectionnée automatiquement sans étape supplémentaire.</span>
                                        </p>
                                    </div>
                                </div>
                                <div class="sm:text-right shrink-0 bg-white sm:bg-transparent p-3 sm:p-0 rounded-xl border sm:border-0 border-blue-100">
                                    <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Loyer mensuel prévu</p>
                                    <p class="text-lg font-black text-blue-600" id="loyer-unite-auto">—</p>
                                </div>
                            </div>
                        </div>

                        <!-- CAS 2: Sélecteur de lot pour Propriété Collective (Multi-unités) -->
                        <div id="card-unite-collective" class="hidden p-5 bg-slate-50/80 border border-slate-200 rounded-2xl">
                            <div class="flex items-center justify-between mb-2">
                                <label for="bien_id" class="block text-[10px] font-black text-slate-600 uppercase tracking-[0.2em]">
                                    Unité Locative à Louer (Lot / Appartement) <span class="text-red-500">*</span>
                                </label>
                                <span class="text-[10px] font-black text-indigo-700 bg-indigo-50 px-2.5 py-0.5 rounded-full border border-indigo-200 uppercase tracking-wider">
                                    <i class="fas fa-building mr-1"></i> Immeuble collectif multi-lots
                                </span>
                            </div>
                            <p class="text-xs text-slate-500 font-medium mb-3">
                                Cet immeuble comporte plusieurs unités locatives. Veuillez choisir le lot à louer pour ce bail :
                            </p>
                            <select id="bien_id" name="bien_id" class="block w-full border-slate-200 focus:border-blue-500 focus:ring-blue-500 rounded-xl shadow-sm font-bold text-slate-700" onchange="updateLoyer()">
                                <option value="">Sélectionnez un lot dans cet immeuble</option>
                            </select>
                            <x-input-error :messages="$errors->get('bien_id')" class="mt-2" />
                        </div>

                        <!-- CAS 3: Alerte Aucune Unité Disponible -->
                        <div id="card-unite-indisponible" class="hidden p-4 bg-amber-50 border border-amber-200 rounded-2xl flex items-center gap-3.5 text-amber-900">
                            <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center shrink-0">
                                <i class="fas fa-exclamation-triangle text-base"></i>
                            </div>
                            <div>
                                <p class="text-xs font-black uppercase tracking-wider text-amber-800">Aucune unité locative disponible</p>
                                <p class="text-xs text-amber-700 font-medium mt-0.5" id="msg-unite-indisponible">Cette propriété ne comporte actuellement aucun lot disponible pour un nouveau contrat.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Calendrier & Période -->
                    <div class="md:col-span-2 border-b border-slate-50 pb-4 mt-4">
                        <div class="flex items-center justify-between flex-wrap gap-2">
                            <div>
                                <h3 class="text-sm font-black text-blue-600 uppercase tracking-[0.2em]">Période de Location</h3>
                                <p class="text-[10px] text-slate-400 font-medium mt-1">Les contrats commencent toujours le 1er du mois</p>
                            </div>
                            <div class="flex items-center gap-1.5 flex-wrap" id="contract-shortcuts-bar">
                                <span class="text-[9px] font-black text-slate-400 uppercase tracking-wider mr-1">Raccourcis :</span>
                                <button type="button" 
                                        id="btn-shortcut-6"
                                        onclick="setContractDuration(6)" 
                                        class="duration-shortcut-btn px-3 py-1.5 text-[10px] font-bold rounded-xl transition-all cursor-pointer bg-slate-100 text-slate-600 hover:bg-slate-200 flex items-center gap-1.5 border border-transparent">
                                    <span class="w-1.5 h-1.5 rounded-full bg-transparent shortcut-dot"></span>
                                    <span>6 mois</span>
                                </button>
                                <button type="button" 
                                        id="btn-shortcut-12"
                                        onclick="setContractDuration(12)" 
                                        class="duration-shortcut-btn px-3 py-1.5 text-[10px] font-black rounded-xl transition-all cursor-pointer bg-blue-600 text-white shadow-md shadow-blue-500/25 ring-2 ring-blue-500/30 flex items-center gap-1.5 border border-blue-600">
                                    <span class="w-1.5 h-1.5 rounded-full bg-white shortcut-dot"></span>
                                    <span>1 an (12 mois)</span>
                                </button>
                                <button type="button" 
                                        id="btn-shortcut-24"
                                        onclick="setContractDuration(24)" 
                                        class="duration-shortcut-btn px-3 py-1.5 text-[10px] font-bold rounded-xl transition-all cursor-pointer bg-slate-100 text-slate-600 hover:bg-slate-200 flex items-center gap-1.5 border border-transparent">
                                    <span class="w-1.5 h-1.5 rounded-full bg-transparent shortcut-dot"></span>
                                    <span>2 ans</span>
                                </button>
                                <button type="button" 
                                        id="btn-shortcut-36"
                                        onclick="setContractDuration(36)" 
                                        class="duration-shortcut-btn px-3 py-1.5 text-[10px] font-bold rounded-xl transition-all cursor-pointer bg-slate-100 text-slate-600 hover:bg-slate-200 flex items-center gap-1.5 border border-transparent">
                                    <span class="w-1.5 h-1.5 rounded-full bg-transparent shortcut-dot"></span>
                                    <span>3 ans</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Champs cachés pour les dates complètes -->
                    <input type="hidden" id="date_debut" name="date_debut" required />
                    <input type="hidden" id="date_fin" name="date_fin" required />

                    <div>
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] mb-2">Mois de début</label>
                        <x-form-month-picker 
                            id="mois_debut" 
                            name="mois_debut" 
                            :value="now()->format('Y-m')" 
                            placeholder="Sélectionnez le mois de début" 
                            onChange="calculateContractDuration" 
                            :required="true" 
                        />
                        <x-input-error :messages="$errors->get('date_debut')" class="mt-2" />
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] mb-2">Mois de fin</label>
                        <x-form-month-picker 
                            id="mois_fin" 
                            name="mois_fin" 
                            :value="now()->addYear()->subMonth()->format('Y-m')" 
                            placeholder="Sélectionnez le mois de fin" 
                            onChange="calculateContractDuration" 
                            :required="true" 
                        />
                        <x-input-error :messages="$errors->get('date_fin')" class="mt-2" />
                    </div>

                    <!-- Durée du contrat -->
                    <div class="md:col-span-2">
                        <div id="duree-contrat" class="hidden p-4 bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-xl">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-[10px] font-black text-blue-500 uppercase tracking-widest">Durée du contrat</p>
                                    <p id="duree-text" class="text-2xl font-black text-slate-900 mt-1"></p>
                                </div>
                                <div class="text-right">
                                    <p class="text-[9px] text-slate-500 font-bold uppercase">Du</p>
                                    <p id="periode-text" class="text-sm font-black text-slate-700"></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Conditions financières -->
                    <div class="md:col-span-2 border-b border-slate-50 pb-4 mt-4">
                        <h3 class="text-sm font-black text-blue-600 uppercase tracking-[0.2em]">Conditions Financières</h3>
                    </div>

                    <div>
                        <label for="montant_loyer_actuel" class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-2">Loyer Mensuel (Base)</label>
                        <x-text-input id="montant_loyer_actuel" name="montant_loyer_actuel" type="number" class="block w-full bg-slate-50 font-black text-slate-700" readonly required />
                        <x-input-error :messages="$errors->get('montant_loyer_actuel')" class="mt-2" />
                    </div>

                    <div>
                        <label for="nombre_mois_caution" class="block text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] mb-2">Nombre de mois de caution</label>
                        <select id="nombre_mois_caution" name="nombre_mois_caution" class="block w-full border-slate-200 focus:border-blue-500 focus:ring-blue-500 rounded-xl shadow-sm font-bold text-slate-700" required onchange="updateCaution()">
                            <option value="">Sélectionnez d'abord la durée</option>
                        </select>
                        <p class="text-[9px] text-slate-400 font-medium mt-1">Maximum = durée du contrat</p>
                    </div>

                    <div>
                        <label for="montant_caution" class="block text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-2">Caution</label>
                        <x-text-input id="montant_caution" name="montant_caution" type="number" class="block w-full bg-slate-100 text-slate-500 font-black cursor-not-allowed" readonly required />
                        <x-input-error :messages="$errors->get('montant_caution')" class="mt-2" />
                    </div>

                    <div>
                        <label for="nombre_mois_avance" class="block text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] mb-2">Avance de loyer</label>
                        <select id="nombre_mois_avance" name="nombre_mois_avance" class="block w-full border-slate-200 focus:border-blue-500 focus:ring-blue-500 rounded-xl shadow-sm font-bold text-slate-700" required onchange="calculateTotal(); updateAvanceExplanation()">
                            <option value="">Sélectionnez d'abord la durée</option>
                        </select>
                        <div id="avance-explanation" class="hidden mt-3 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                            <p class="text-[10px] font-black text-blue-600 uppercase tracking-wide mb-1">
                                <i class="fas fa-info-circle mr-1"></i> Explication du paiement
                            </p>
                            <p id="avance-details" class="text-xs text-slate-700 font-medium leading-relaxed"></p>
                        </div>
                        <x-input-error :messages="$errors->get('nombre_mois_avance')" class="mt-2" />
                    </div>

                    <div>
                        <label for="frais_dossier" class="block text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] mb-2">Honoraires & Frais de dossier</label>
                        <x-text-input id="frais_dossier" name="frais_dossier" type="number" class="block w-full font-black text-blue-600" value="0" oninput="calculateTotal()" />
                        <x-input-error :messages="$errors->get('frais_dossier')" class="mt-2" />
                    </div>

                    <!-- Récapitulatif financier -->
                    <div class="md:col-span-2 p-8 bg-blue-600 rounded-[2rem] shadow-xl shadow-blue-200 flex flex-col md:flex-row justify-between items-center gap-6">
                        <div>
                            <h3 class="text-xs font-black text-blue-200 uppercase tracking-[0.2em] mb-2">Total dû à la signature</h3>
                            <div id="display-total" class="text-4xl font-black text-white tracking-tight">0 {{ Auth::user()->agence->devise }}</div>
                        </div>
                        <div class="text-right">
                            <p class="text-[10px] text-blue-200 font-bold leading-relaxed uppercase tracking-widest">
                                Caution + Frais de dossier + Avance de loyer (si applicable)
                            </p>
                            <p class="text-[9px] text-blue-100 font-medium mt-1 italic">
                                Sans avance, aucun loyer n'est dû à la signature
                            </p>
                        </div>
                    </div>

                    <!-- État des lieux sera sur la page suivante -->
                    <input type="hidden" name="etat_des_lieux_entree" value="À compléter" />
                </div>

                <div class="pt-10 flex justify-between items-center">
                    <p class="text-xs text-slate-500 font-medium">
                        <i class="fas fa-info-circle mr-1"></i> L'état des lieux détaillé sera complété à l'étape suivante
                    </p>
                    <button type="submit" class="btn-premium bg-blue-600 text-white px-12 py-5 uppercase tracking-widest text-sm font-black hover:bg-blue-700 shadow-2xl transition transform hover:-translate-y-1 flex items-center gap-3">
                        Suivant : État des Lieux
                        <i class="fas fa-arrow-right"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Données pour les cascades
        const famillesData = @json($familles);
        let contractDurationMonths = 0;
        let contractStartDate = null;
        let contractEndDate = null;

        // Mise à jour de l'indicateur visuel des raccourcis
        function updateShortcutIndicator(duration) {
            document.querySelectorAll('.duration-shortcut-btn').forEach(btn => {
                btn.className = 'duration-shortcut-btn px-3 py-1.5 text-[10px] font-bold rounded-xl transition-all cursor-pointer bg-slate-100 text-slate-600 hover:bg-slate-200 flex items-center gap-1.5 border border-transparent';
                const dot = btn.querySelector('.shortcut-dot');
                if (dot) dot.className = 'w-1.5 h-1.5 rounded-full bg-transparent shortcut-dot';
            });

            const activeBtn = document.getElementById(`btn-shortcut-${duration}`);
            if (activeBtn) {
                activeBtn.className = 'duration-shortcut-btn px-3 py-1.5 text-[10px] font-black rounded-xl transition-all cursor-pointer bg-blue-600 text-white shadow-md shadow-blue-500/25 ring-2 ring-blue-500/30 flex items-center gap-1.5 border border-blue-600';
                const dot = activeBtn.querySelector('.shortcut-dot');
                if (dot) dot.className = 'w-1.5 h-1.5 rounded-full bg-white shortcut-dot';
            }
        }

        // Raccourci de durée (6 mois, 12 mois, etc.)
        function setContractDuration(nbMonths) {
            let moisDebutInput = document.getElementById('mois_debut');
            let moisDebut = moisDebutInput ? moisDebutInput.value : '';
            if (!moisDebut) {
                const now = new Date();
                const mm = String(now.getMonth() + 1).padStart(2, '0');
                moisDebut = `${now.getFullYear()}-${mm}`;
                if (moisDebutInput) moisDebutInput.value = moisDebut;
                window.dispatchEvent(new CustomEvent('set-month-mois_debut', { detail: moisDebut }));
            }
            
            const [yStr, mStr] = moisDebut.split('-');
            let y = parseInt(yStr, 10);
            let m = parseInt(mStr, 10);
            
            m += (nbMonths - 1);
            while (m > 12) {
                m -= 12;
                y++;
            }
            const endMoisStr = `${y}-${String(m).padStart(2, '0')}`;
            
            // 1. Mettre à jour l'input caché directement
            const moisFinInput = document.getElementById('mois_fin');
            if (moisFinInput) {
                moisFinInput.value = endMoisStr;
            }

            // 2. Notifier le composant interactif Alpine
            window.dispatchEvent(new CustomEvent('set-month-mois_fin', { detail: endMoisStr }));

            // 3. Mettre à jour l'indicateur visuel du bouton
            updateShortcutIndicator(nbMonths);

            // 4. Calculer immédiatement la durée et les conditions
            calculateContractDuration();
        }

        window.setContractDuration = setContractDuration;
        window.updateShortcutIndicator = updateShortcutIndicator;

        function calculateContractDuration() {
            const moisDebut = document.getElementById('mois_debut').value;
            const moisFin = document.getElementById('mois_fin').value;
            const dureeContainer = document.getElementById('duree-contrat');
            const dureeText = document.getElementById('duree-text');
            const periodeText = document.getElementById('periode-text');
            
            if (!moisDebut || !moisFin) {
                dureeContainer.classList.add('hidden');
                contractDurationMonths = 0;
                updateCautionOptions();
                updateAvanceOptions();
                return;
            }
            
            // Calculer la durée en mois
            const debut = new Date(moisDebut + '-01');
            const fin = new Date(moisFin + '-01');
            
            // Vérifier que la date de fin est après le début
            if (fin < debut) {
                alert('La date de fin doit être après la date de début !');
                document.getElementById('mois_fin').value = '';
                dureeContainer.classList.add('hidden');
                return;
            }
            
            // Calculer le nombre de mois (inclus)
            const months = (fin.getFullYear() - debut.getFullYear()) * 12 + (fin.getMonth() - debut.getMonth()) + 1;
            contractDurationMonths = months;

            // Synchroniser l'indicateur visuel des raccourcis
            if ([6, 12, 24, 36].includes(months)) {
                updateShortcutIndicator(months);
            } else {
                updateShortcutIndicator(null);
            }
            
            // Stocker les dates pour le formulaire
            contractStartDate = debut;
            
            // La date de fin est le dernier jour du mois de fin
            const lastDay = new Date(fin.getFullYear(), fin.getMonth() + 1, 0);
            contractEndDate = lastDay;
            
            // Mettre à jour les champs cachés
            document.getElementById('date_debut').value = debut.toISOString().split('T')[0];
            document.getElementById('date_fin').value = lastDay.toISOString().split('T')[0];
            
            // Afficher la durée
            dureeText.textContent = `${months} mois`;
            periodeText.textContent = `${debut.toLocaleDateString('fr-FR', { day: 'numeric', month: 'long', year: 'numeric' })} au ${lastDay.toLocaleDateString('fr-FR', { day: 'numeric', month: 'long', year: 'numeric' })}`;
            dureeContainer.classList.remove('hidden');
            
            // Mettre à jour les options de caution et avance
            updateCautionOptions();
            updateAvanceOptions();
            updateAvanceExplanation();
        }

        function updateCautionOptions() {
            const cautionSelect = document.getElementById('nombre_mois_caution');
            cautionSelect.innerHTML = '';
            
            if (contractDurationMonths === 0) {
                const option = document.createElement('option');
                option.value = '';
                option.textContent = 'Sélectionnez d\'abord la durée';
                cautionSelect.appendChild(option);
                return;
            }
            
            // Les options vont de 0 jusqu'à la durée du contrat
            for (let i = 0; i <= contractDurationMonths; i++) {
                const option = document.createElement('option');
                option.value = i;
                option.textContent = `${i} mois`;
                if (i === 2 && contractDurationMonths >= 2) {
                    option.selected = true; // Par défaut 2 mois si possible
                }
                cautionSelect.appendChild(option);
            }
            
            updateCaution();
        }

        function updateAvanceOptions() {
            const avanceSelect = document.getElementById('nombre_mois_avance');
            avanceSelect.innerHTML = '';
            
            if (contractDurationMonths === 0) {
                const option = document.createElement('option');
                option.value = '';
                option.textContent = 'Sélectionnez d\'abord la durée';
                avanceSelect.appendChild(option);
                return;
            }
            
            // Option 0: Aucune avance (paiement en fin de mois)
            const option0 = document.createElement('option');
            option0.value = 0;
            option0.textContent = 'Aucune avance (paiement en fin de mois)';
            option0.selected = true;
            avanceSelect.appendChild(option0);
            
            // Les options vont de 1 jusqu'à la durée du contrat
            for (let i = 1; i <= contractDurationMonths; i++) {
                const option = document.createElement('option');
                option.value = i;
                option.textContent = `${i} mois d'avance`;
                avanceSelect.appendChild(option);
            }
            
            updateAvanceExplanation();
        }

        function updateAvanceExplanation() {
            const avanceMois = parseInt(document.getElementById('nombre_mois_avance').value || 0);
            const explanationDiv = document.getElementById('avance-explanation');
            const detailsText = document.getElementById('avance-details');
            
            if (!contractStartDate || contractDurationMonths === 0) {
                explanationDiv.classList.add('hidden');
                return;
            }
            
            explanationDiv.classList.remove('hidden');
            
            // Générer les noms des mois
            const getMonthName = (date) => {
                return date.toLocaleDateString('fr-FR', { month: 'long', year: 'numeric' });
            };
            
            if (avanceMois === 0) {
                // Aucune avance: le locataire paie à la fin du premier mois
                const firstMonth = getMonthName(contractStartDate);
                detailsText.innerHTML = `<strong>Aucun loyer payé à la signature.</strong><br>Le locataire devra payer le loyer de <strong class="text-blue-600">${firstMonth}</strong> à la fin de ce mois (le dernier jour).`;
            } else {
                // Avance: le locataire paie UNIQUEMENT les mois d'avance (pas tous les mois)
                const monthsPaid = [];
                for (let i = 0; i < avanceMois; i++) {
                    const monthDate = new Date(contractStartDate);
                    monthDate.setMonth(monthDate.getMonth() + i);
                    monthsPaid.push(getMonthName(monthDate));
                }
                
                // Déterminer les mois restants
                const remainingMonths = contractDurationMonths - avanceMois;
                
                if (remainingMonths > 0) {
                    const nextPaymentDate = new Date(contractStartDate);
                    nextPaymentDate.setMonth(nextPaymentDate.getMonth() + avanceMois);
                    const nextMonth = getMonthName(nextPaymentDate);
                    
                    detailsText.innerHTML = `<strong>À la signature, le locataire paie d'avance:</strong><br>• ${monthsPaid.map(m => `<strong class="text-blue-600">${m}</strong>`).join('<br>• ')}<br><br>Il devra ensuite payer le loyer de <strong class="text-blue-600">${nextMonth}</strong> à la fin de ce mois (${remainingMonths} mois restants).`;
                } else {
                    detailsText.innerHTML = `<strong>À la signature, le locataire paie l'intégralité du contrat:</strong><br>• ${monthsPaid.map(m => `<strong class="text-blue-600">${m}</strong>`).join('<br>• ')}<br><br><strong class="text-emerald-600">✓ Contrat entièrement soldé à la signature</strong>`;
                }
            }
            
            calculateTotal();
        }

        function updateBiens() {
            const familleId = document.getElementById('famille_de_bien_id').value;
            const bienSelect = document.getElementById('bien_id');
            const placeholder = document.getElementById('unite-placeholder');
            const cardIndiv = document.getElementById('card-unite-individuelle');
            const cardColl = document.getElementById('card-unite-collective');
            const cardIndispo = document.getElementById('card-unite-indisponible');
            const nomUniteAuto = document.getElementById('nom-unite-auto');
            const loyerUniteAuto = document.getElementById('loyer-unite-auto');
            const loyerInput = document.getElementById('montant_loyer_actuel');
            const cautionInput = document.getElementById('montant_caution');

            // Réinitialiser les affichages
            if (placeholder) placeholder.classList.add('hidden');
            if (cardIndiv) cardIndiv.classList.add('hidden');
            if (cardColl) cardColl.classList.add('hidden');
            if (cardIndispo) cardIndispo.classList.add('hidden');

            // Réinitialiser le select et les montants
            bienSelect.innerHTML = '<option value="">Sélectionnez une unité locative</option>';
            bienSelect.value = '';
            bienSelect.required = false;
            loyerInput.value = '';
            cautionInput.value = '';

            if (!familleId) {
                if (placeholder) placeholder.classList.remove('hidden');
                calculateTotal();
                return;
            }

            const famille = famillesData.find(f => f.id == familleId);
            if (!famille) {
                if (placeholder) placeholder.classList.remove('hidden');
                calculateTotal();
                return;
            }

            const isIndividuelle = (famille.nature === 'individuelle');
            const devise = '{{ Auth::user()->agence->devise ?? "FCFA" }}';

            if (isIndividuelle) {
                // CAS 1: BIEN INDIVIDUEL (Mono-unité)
                // L'unité locative est la propriété elle-même. Aucun besoin de demander à l'utilisateur de la re-sélectionner !
                if (!famille.biens || famille.biens.length === 0) {
                    if (cardIndispo) {
                        const msg = document.getElementById('msg-unite-indisponible');
                        if (msg) msg.textContent = 'Cette propriété individuelle est actuellement indisponible (déjà louée ou en maintenance).';
                        cardIndispo.classList.remove('hidden');
                    }
                    bienSelect.innerHTML = '<option value="">Propriété indisponible</option>';
                    bienSelect.value = '';
                } else {
                    const bien = famille.biens[0];
                    // Sélection automatique dans le select (qui reste dans le DOM pour la transmission POST)
                    bienSelect.innerHTML = `<option value="${bien.id}" selected data-loyer="${bien.loyer_mensuel}">${bien.nom_bien}</option>`;
                    bienSelect.value = bien.id;
                    bienSelect.required = false;

                    // Afficher la confirmation visuelle
                    if (nomUniteAuto) nomUniteAuto.textContent = bien.nom_bien;
                    if (loyerUniteAuto) {
                        loyerUniteAuto.textContent = parseFloat(bien.loyer_mensuel).toLocaleString('fr-FR') + ' ' + devise;
                    }
                    if (cardIndiv) cardIndiv.classList.remove('hidden');

                    // Remplir automatiquement le loyer et déclencher le calcul de la caution
                    loyerInput.value = bien.loyer_mensuel;
                    updateCaution();
                }
            } else {
                // CAS 2: PROPRIÉTÉ COLLECTIVE (Immeuble multi-lots)
                // L'utilisateur choisit le lot/appartement spécifique dans l'immeuble.
                if (!famille.biens || famille.biens.length === 0) {
                    if (cardIndispo) {
                        const msg = document.getElementById('msg-unite-indisponible');
                        if (msg) msg.textContent = 'Cet immeuble ne comporte actuellement aucun lot disponible pour un nouveau bail.';
                        cardIndispo.classList.remove('hidden');
                    }
                    bienSelect.innerHTML = '<option value="">Aucun lot disponible</option>';
                    bienSelect.value = '';
                } else {
                    if (cardColl) cardColl.classList.remove('hidden');
                    bienSelect.required = true;
                    bienSelect.innerHTML = '<option value="">Sélectionnez l\'appartement ou le lot à louer</option>';
                    famille.biens.forEach(bien => {
                        const option = document.createElement('option');
                        option.value = bien.id;
                        option.setAttribute('data-loyer', bien.loyer_mensuel);
                        option.setAttribute('data-statut', bien.statut);
                        option.textContent = `${bien.nom_bien} — ${parseFloat(bien.loyer_mensuel).toLocaleString('fr-FR')} ${devise} (${bien.statut})`;
                        bienSelect.appendChild(option);
                    });
                }
            }

            calculateTotal();
        }

        function updateCaution() {
            const loyer = parseFloat(document.getElementById('montant_loyer_actuel').value || 0);
            const nombreMoisCaution = parseInt(document.getElementById('nombre_mois_caution').value || 0);
            const caution = loyer * nombreMoisCaution;
            document.getElementById('montant_caution').value = caution;
            calculateTotal();
        }

        function updateLoyer() {
            const select = document.getElementById('bien_id');
            const option = select.options[select.selectedIndex];
            const loyer = (option && option.value) ? parseFloat(option.getAttribute('data-loyer') || 0) : 0;

            if (loyer > 0) {
                document.getElementById('montant_loyer_actuel').value = loyer;
                updateCaution();
            } else {
                document.getElementById('montant_loyer_actuel').value = '';
                document.getElementById('montant_caution').value = '';
            }

            calculateTotal();
        }

        function calculateTotal() {
            const loyer = parseFloat(document.getElementById('montant_loyer_actuel').value || 0);
            const caution = parseFloat(document.getElementById('montant_caution').value || 0);
            const avanceMois = parseInt(document.getElementById('nombre_mois_avance').value || 0);
            const frais = parseFloat(document.getElementById('frais_dossier').value || 0);

            // LOGIQUE CORRECTE:
            // Avance = 0 → Aucun loyer payé
            // Avance = 1 → 1 mois payé (le 1er mois)
            // Avance = 2 → 2 mois payés (1er et 2ème mois)
            // Avance = 3 → 3 mois payés (1er, 2ème et 3ème mois)
            let totalLoyer = loyer * avanceMois;
            
            const grandTotal = caution + frais + totalLoyer;

            document.getElementById('display-total').innerText = grandTotal.toLocaleString('fr-FR') + ' {{ Auth::user()->agence->devise }}';
        }

        document.getElementById('locataire_id').addEventListener('change', function() {
            const option = this.options[this.selectedIndex];
            const auditStatus = option ? option.getAttribute('data-audit') : '';
            const warning = document.getElementById('audit-warning');

            if (auditStatus && auditStatus !== 'Validé') {
                warning.classList.remove('hidden');
            } else {
                warning.classList.add('hidden');
            }
        });

        // Sécurité lors de la soumission du formulaire
        const formContrat = document.querySelector('form');
        if (formContrat) {
            formContrat.addEventListener('submit', function(e) {
                const bienSelect = document.getElementById('bien_id');
                if (!bienSelect || !bienSelect.value) {
                    e.preventDefault();
                    alert('Veuillez sélectionner une propriété et une unité locative disponible avant de continuer.');
                    const familleSelect = document.getElementById('famille_de_bien_id');
                    if (familleSelect) familleSelect.focus();
                    return false;
                }
            });
        }

        window.calculateContractDuration = calculateContractDuration;

        // Initialiser au chargement
        document.addEventListener('DOMContentLoaded', function() {
            calculateContractDuration();

            // Si une propriété est déjà sélectionnée (ex: redirection avec erreurs ou validation)
            const familleSelect = document.getElementById('famille_de_bien_id');
            if (familleSelect && familleSelect.value) {
                updateBiens();
                const oldBienId = '{{ old("bien_id") }}';
                if (oldBienId) {
                    const bienSelect = document.getElementById('bien_id');
                    if (bienSelect) {
                        bienSelect.value = oldBienId;
                        updateLoyer();
                    }
                }
            }
        });
    </script>
</x-app-layout>