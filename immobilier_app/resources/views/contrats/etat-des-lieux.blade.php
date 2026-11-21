<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 w-full">
            <div>
                <h2 class="font-black text-4xl text-slate-900 tracking-tight uppercase">
                    État des Lieux d'Entrée
                </h2>
                <p class="text-slate-500 font-medium mt-1">Contrat {{ $contrat->numero_contrat }} - {{ $contrat->bien->nom_bien }}</p>
                <p class="text-xs text-slate-400 font-bold mt-2">
                    Locataire: {{ $contrat->locataire->user->name }} | 
                    Propriétaire: {{ $contrat->bien->proprietaire->user->name }}
                </p>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        <!-- Instructions -->
        <div class="premium-card p-6 bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-xl bg-blue-600 flex items-center justify-center shrink-0">
                    <i class="fas fa-clipboard-check text-white text-xl"></i>
                </div>
                <div>
                    <h3 class="font-black text-lg text-blue-900 uppercase tracking-tight">Instructions</h3>
                    <p class="text-sm text-slate-700 mt-2 leading-relaxed">
                        Cochez chaque élément présent dans le logement et indiquez son état. 
                        <strong>Si un élément n'existe pas, décochez "Présent".</strong>
                        Les photos peuvent être prises et jointes au rapport final.
                    </p>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('contrats.etat-des-lieux.store', $contrat) }}" id="etatDesLieuxForm" class="space-y-6">
            @csrf

            <!-- 1. INFORMATIONS GÉNÉRALES -->
            <div class="premium-card p-8 bg-white">
                <div class="flex items-center gap-3 mb-6 border-b border-slate-100 pb-4">
                    <div class="w-10 h-10 rounded-lg bg-slate-900 text-white flex items-center justify-center font-black">
                        1
                    </div>
                    <h3 class="text-lg font-black text-slate-900 uppercase tracking-tight">Informations Générales</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Lien avec visite (si applicable) -->
                    @if($visites && $visites->count() > 0)
                    <div class="md:col-span-2 p-4 bg-blue-50 border border-blue-200 rounded-xl">
                        <label class="block text-[10px] font-black text-blue-700 uppercase tracking-wider mb-3">
                            <i class="fas fa-link mr-1"></i> Lier à une visite effectuée (optionnel)
                        </label>
                        <select name="visite_id" class="w-full border-blue-300 rounded-xl font-semibold text-sm">
                            <option value="">Aucune visite liée</option>
                            @foreach($visites as $visite)
                            <option value="{{ $visite->id }}">
                                Visite du {{ $visite->date_heure_visite->format('d/m/Y à H:i') }} 
                                - {{ $visite->bien->nom_bien }}
                                @if($visite->notes)
                                    - {{ Str::limit($visite->notes, 50) }}
                                @endif
                            </option>
                            @endforeach
                        </select>
                        <p class="text-xs text-blue-600 mt-2 font-medium">
                            <i class="fas fa-info-circle mr-1"></i> 
                            Si une visite a déjà été effectuée pour ce bien et ce locataire, vous pouvez la lier ici pour référence.
                        </p>
                    </div>
                    @endif

                    <div>
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-wider mb-2">Date de visite</label>
                        <input type="date" name="date_visite" required value="{{ old('date_visite', now()->format('Y-m-d')) }}" class="w-full border-slate-200 rounded-xl font-semibold">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-wider mb-2">Heure de visite</label>
                        <input type="time" name="heure_visite" required value="{{ old('heure_visite', now()->format('H:i')) }}" class="w-full border-slate-200 rounded-xl font-semibold">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-wider mb-2">Nombre de clés remises</label>
                        <input type="number" name="nombre_cles" min="0" value="{{ old('nombre_cles', 2) }}" class="w-full border-slate-200 rounded-xl font-semibold">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-wider mb-2">Télécommandes / Badges</label>
                        <input type="number" name="nombre_telecommandes" min="0" value="{{ old('nombre_telecommandes', 0) }}" class="w-full border-slate-200 rounded-xl font-semibold">
                    </div>
                </div>
            </div>

            <!-- Template pour chaque section -->
            @php
            $sections = [
                [
                    'id' => 'entree',
                    'numero' => 2,
                    'titre' => 'Entrée Principale',
                    'icon' => 'door-open',
                    'elements' => [
                        ['code' => 'porte_entree', 'label' => 'Porte d\'entrée'],
                        ['code' => 'serrure', 'label' => 'Serrure'],
                        ['code' => 'poignee', 'label' => 'Poignée'],
                        ['code' => 'judas', 'label' => 'Judas / Œilleton'],
                        ['code' => 'sonnette', 'label' => 'Sonnette / Interphone'],
                        ['code' => 'boite_lettres', 'label' => 'Boîte aux lettres'],
                    ]
                ],
                [
                    'id' => 'salon',
                    'numero' => 3,
                    'titre' => 'Salon / Séjour',
                    'icon' => 'couch',
                    'elements' => [
                        ['code' => 'murs', 'label' => 'Murs'],
                        ['code' => 'sol', 'label' => 'Sol'],
                        ['code' => 'plafond', 'label' => 'Plafond'],
                        ['code' => 'fenetres', 'label' => 'Fenêtres'],
                        ['code' => 'rideaux', 'label' => 'Rideaux / Volets'],
                        ['code' => 'portes', 'label' => 'Portes intérieures'],
                        ['code' => 'eclairage', 'label' => 'Éclairage'],
                        ['code' => 'prises', 'label' => 'Prises électriques'],
                        ['code' => 'climatisation', 'label' => 'Climatisation'],
                    ]
                ],
                [
                    'id' => 'cuisine',
                    'numero' => 4,
                    'titre' => 'Cuisine',
                    'icon' => 'utensils',
                    'elements' => [
                        ['code' => 'murs', 'label' => 'Murs / Carrelage mural'],
                        ['code' => 'sol', 'label' => 'Sol'],
                        ['code' => 'plafond', 'label' => 'Plafond'],
                        ['code' => 'evier', 'label' => 'Évier'],
                        ['code' => 'robinet', 'label' => 'Robinetterie'],
                        ['code' => 'placards', 'label' => 'Placards / Tiroirs'],
                        ['code' => 'plan_travail', 'label' => 'Plan de travail'],
                        ['code' => 'hotte', 'label' => 'Hotte'],
                        ['code' => 'four', 'label' => 'Four'],
                        ['code' => 'plaques', 'label' => 'Plaques de cuisson'],
                        ['code' => 'refrigerateur', 'label' => 'Réfrigérateur'],
                        ['code' => 'prises', 'label' => 'Prises électriques'],
                    ]
                ],
                [
                    'id' => 'chambres',
                    'numero' => 5,
                    'titre' => 'Chambres',
                    'icon' => 'bed',
                    'elements' => [
                        ['code' => 'murs', 'label' => 'Murs'],
                        ['code' => 'sol', 'label' => 'Sol'],
                        ['code' => 'plafond', 'label' => 'Plafond'],
                        ['code' => 'fenetres', 'label' => 'Fenêtres'],
                        ['code' => 'portes', 'label' => 'Portes'],
                        ['code' => 'placards', 'label' => 'Placards'],
                        ['code' => 'eclairage', 'label' => 'Éclairage'],
                        ['code' => 'prises', 'label' => 'Prises électriques'],
                        ['code' => 'climatisation', 'label' => 'Climatisation'],
                    ]
                ],
                [
                    'id' => 'salle_bain',
                    'numero' => 6,
                    'titre' => 'Salle de Bain',
                    'icon' => 'bath',
                    'elements' => [
                        ['code' => 'murs', 'label' => 'Murs / Carrelage'],
                        ['code' => 'sol', 'label' => 'Sol'],
                        ['code' => 'plafond', 'label' => 'Plafond'],
                        ['code' => 'lavabo', 'label' => 'Lavabo'],
                        ['code' => 'robinet_lavabo', 'label' => 'Robinet lavabo'],
                        ['code' => 'douche', 'label' => 'Douche / Cabine'],
                        ['code' => 'baignoire', 'label' => 'Baignoire'],
                        ['code' => 'robinet_douche', 'label' => 'Robinetterie douche'],
                        ['code' => 'miroir', 'label' => 'Miroir'],
                        ['code' => 'ventilation', 'label' => 'Ventilation / VMC'],
                    ]
                ],
                [
                    'id' => 'toilettes',
                    'numero' => 7,
                    'titre' => 'Toilettes / WC',
                    'icon' => 'toilet',
                    'elements' => [
                        ['code' => 'wc', 'label' => 'Cuvette WC'],
                        ['code' => 'abattant', 'label' => 'Abattant'],
                        ['code' => 'chasse_eau', 'label' => 'Chasse d\'eau'],
                        ['code' => 'murs', 'label' => 'Murs / Carrelage'],
                        ['code' => 'sol', 'label' => 'Sol'],
                        ['code' => 'ventilation', 'label' => 'Ventilation'],
                    ]
                ],
                [
                    'id' => 'electricite',
                    'numero' => 8,
                    'titre' => 'Électricité & Plomberie',
                    'icon' => 'bolt',
                    'elements' => [
                        ['code' => 'tableau_electrique', 'label' => 'Tableau électrique'],
                        ['code' => 'disjoncteurs', 'label' => 'Disjoncteurs'],
                        ['code' => 'compteur_eau', 'label' => 'Compteur d\'eau'],
                        ['code' => 'compteur_electrique', 'label' => 'Compteur électrique'],
                        ['code' => 'chauffe_eau', 'label' => 'Chauffe-eau'],
                        ['code' => 'tuyauterie', 'label' => 'Tuyauterie visible'],
                    ]
                ],
                [
                    'id' => 'exterieur',
                    'numero' => 9,
                    'titre' => 'Extérieur (si applicable)',
                    'icon' => 'tree',
                    'elements' => [
                        ['code' => 'balcon', 'label' => 'Balcon / Terrasse'],
                        ['code' => 'garde_corps', 'label' => 'Garde-corps'],
                        ['code' => 'jardin', 'label' => 'Jardin / Cour'],
                        ['code' => 'cloture', 'label' => 'Clôture'],
                        ['code' => 'portail', 'label' => 'Portail'],
                        ['code' => 'garage', 'label' => 'Garage / Parking'],
                        ['code' => 'eclairage_ext', 'label' => 'Éclairage extérieur'],
                    ]
                ],
            ];
            @endphp

            @foreach($sections as $section)
            <div class="premium-card p-8 bg-white section-card" data-section="{{ $section['id'] }}">
                <div class="flex items-center justify-between mb-6 border-b border-slate-100 pb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-blue-600 text-white flex items-center justify-center font-black">
                            {{ $section['numero'] }}
                        </div>
                        <div class="flex items-center gap-3">
                            <i class="fas fa-{{ $section['icon'] }} text-blue-600 text-xl"></i>
                            <h3 class="text-lg font-black text-slate-900 uppercase tracking-tight">{{ $section['titre'] }}</h3>
                        </div>
                    </div>
                    
                    <!-- Toggle pour ignorer toute la section -->
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="hidden" name="sections[{{ $section['id'] }}][ignored]" value="0">
                        <input type="checkbox" 
                               name="sections[{{ $section['id'] }}][ignored]" 
                               value="1" 
                               class="w-5 h-5 text-emerald-600 rounded border-slate-300 focus:ring-emerald-500 section-toggle"
                               onchange="toggleSection(this, '{{ $section['id'] }}')">
                        <span class="text-sm font-bold text-slate-600 toggle-label">
                            <i class="fas fa-times-circle text-slate-400"></i> Ignorer cette section
                        </span>
                    </label>
                </div>

                <div class="section-content space-y-4">
                    @foreach($section['elements'] as $element)
                    <div class="p-4 bg-slate-50 rounded-xl border border-slate-200 hover:border-blue-300 transition-colors">
                        <div class="flex items-center justify-between flex-wrap gap-4">
                            <div class="flex items-center gap-3 flex-1 min-w-[200px]">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" 
                                           name="sections[{{ $section['id'] }}][{{ $element['code'] }}][present]" 
                                           value="1" 
                                           checked
                                           class="w-5 h-5 text-blue-600 rounded border-slate-300 focus:ring-blue-500"
                                           onchange="toggleElement(this)">
                                    <span class="font-bold text-sm text-slate-700">{{ $element['label'] }}</span>
                                </label>
                            </div>

                            <div class="flex items-center gap-3 element-controls">
                                <select name="sections[{{ $section['id'] }}][{{ $element['code'] }}][etat]" 
                                        class="border-slate-300 rounded-lg text-sm font-semibold py-2 px-3 etat-select">
                                    <option value="excellent" class="text-emerald-600">✓ Excellent état</option>
                                    <option value="bon" class="text-blue-600">Bon état</option>
                                    <option value="correct" class="text-amber-600">État correct</option>
                                    <option value="mauvais" class="text-red-600">Mauvais état</option>
                                </select>

                                <button type="button" 
                                        class="px-3 py-2 bg-slate-200 text-slate-700 rounded-lg text-xs font-bold hover:bg-slate-300 transition-colors"
                                        onclick="toggleObservations(this)">
                                    <i class="fas fa-comment mr-1"></i> Note
                                </button>
                            </div>
                        </div>

                        <div class="observations-box hidden mt-3">
                            <textarea name="sections[{{ $section['id'] }}][{{ $element['code'] }}][observations]" 
                                      rows="2" 
                                      placeholder="Observations détaillées (fissures, rayures, défauts...)"
                                      class="w-full border-slate-300 rounded-lg text-sm font-medium placeholder:text-slate-400"></textarea>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach

            <!-- Relevés compteurs -->
            <div class="premium-card p-8 bg-white">
                <div class="flex items-center gap-3 mb-6 border-b border-slate-100 pb-4">
                    <div class="w-10 h-10 rounded-lg bg-emerald-600 text-white flex items-center justify-center font-black">
                        10
                    </div>
                    <div class="flex items-center gap-3">
                        <i class="fas fa-tachometer-alt text-emerald-600 text-xl"></i>
                        <h3 class="text-lg font-black text-slate-900 uppercase tracking-tight">Relevés des Compteurs</h3>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-wider mb-2">Compteur d'eau (index)</label>
                        <input type="text" name="compteur_eau" placeholder="Ex: 001234" class="w-full border-slate-200 rounded-xl font-semibold">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-wider mb-2">Compteur électricité (index)</label>
                        <input type="text" name="compteur_electricite" placeholder="Ex: 005678" class="w-full border-slate-200 rounded-xl font-semibold">
                    </div>
                </div>
            </div>

            <!-- Observations générales -->
            <div class="premium-card p-8 bg-white">
                <div class="flex items-center gap-3 mb-6">
                    <i class="fas fa-clipboard-list text-blue-600 text-2xl"></i>
                    <h3 class="text-lg font-black text-slate-900 uppercase tracking-tight">Observations Générales</h3>
                </div>

                <textarea name="observations_generales" 
                          rows="6" 
                          placeholder="Toute observation générale sur l'état du logement, ambiance, particularités..."
                          class="w-full border-slate-300 rounded-xl font-medium text-slate-700 placeholder:text-slate-400"></textarea>
            </div>

            <!-- Boutons d'action -->
            <div class="premium-card p-8 bg-gradient-to-r from-slate-900 to-slate-800">
                <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                    <div class="text-white">
                        <h4 class="font-black text-lg uppercase tracking-tight">État des lieux complété ?</h4>
                        <p class="text-sm text-slate-300 mt-1">Le rapport sera généré et pourra être imprimé pour signature</p>
                    </div>
                    <div class="flex gap-3">
                        <a href="{{ route('contrats.show', $contrat) }}" class="px-8 py-4 bg-slate-700 text-white rounded-xl font-black text-sm uppercase tracking-wider hover:bg-slate-600 transition-colors">
                            <i class="fas fa-times mr-2"></i> Annuler
                        </a>
                        <button type="submit" class="px-8 py-4 bg-emerald-500 text-white rounded-xl font-black text-sm uppercase tracking-wider hover:bg-emerald-600 transition-colors shadow-lg shadow-emerald-200">
                            <i class="fas fa-check mr-2"></i> Enregistrer l'état des lieux
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        function toggleSection(checkbox, sectionId) {
            const sectionCard = document.querySelector(`.section-card[data-section="${sectionId}"]`);
            const content = sectionCard.querySelector('.section-content');
            const header = sectionCard.querySelector('h3');
            const label = checkbox.closest('label').querySelector('.toggle-label');
            
            if (checkbox.checked) {
                // Section ignorée
                content.classList.add('opacity-30', 'pointer-events-none');
                sectionCard.classList.remove('bg-white');
                sectionCard.classList.add('bg-slate-100');
                header.classList.add('line-through', 'text-slate-400');
                label.innerHTML = '<i class="fas fa-check-circle text-emerald-500"></i> Section ignorée';
            } else {
                // Section active
                content.classList.remove('opacity-30', 'pointer-events-none');
                sectionCard.classList.remove('bg-slate-100');
                sectionCard.classList.add('bg-white');
                header.classList.remove('line-through', 'text-slate-400');
                label.innerHTML = '<i class="fas fa-times-circle text-slate-400"></i> Ignorer cette section';
            }
        }

        function toggleElement(checkbox) {
            const controls = checkbox.closest('.p-4').querySelector('.element-controls');
            const observations = checkbox.closest('.p-4').querySelector('.observations-box');
            
            if (checkbox.checked) {
                controls.classList.remove('opacity-50', 'pointer-events-none');
            } else {
                controls.classList.add('opacity-50', 'pointer-events-none');
                observations.classList.add('hidden');
            }
        }

        function toggleObservations(button) {
            const observationsBox = button.closest('.p-4').querySelector('.observations-box');
            observationsBox.classList.toggle('hidden');
            
            if (observationsBox.classList.contains('hidden')) {
                button.innerHTML = '<i class="fas fa-comment mr-1"></i> Note';
            } else {
                button.innerHTML = '<i class="fas fa-comment-slash mr-1"></i> Masquer';
                observationsBox.querySelector('textarea').focus();
            }
        }

        // Highlight des états selon la sélection
        document.querySelectorAll('.etat-select').forEach(select => {
            select.addEventListener('change', function() {
                const parent = this.closest('.p-4');
                parent.classList.remove('border-emerald-300', 'border-amber-300', 'border-red-300', 'bg-emerald-50', 'bg-amber-50', 'bg-red-50');
                
                switch(this.value) {
                    case 'excellent':
                        parent.classList.add('border-emerald-300', 'bg-emerald-50');
                        break;
                    case 'correct':
                    case 'mauvais':
                        parent.classList.add('border-amber-300', 'bg-amber-50');
                        break;
                }
            });
        });
    </script>
</x-app-layout>
