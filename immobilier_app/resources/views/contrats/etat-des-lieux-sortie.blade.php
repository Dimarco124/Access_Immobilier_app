<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 w-full">
            <div>
                <h2 class="font-black text-4xl text-red-900 tracking-tight uppercase">
                    Mettre Fin au Contrat
                </h2>
                <p class="text-slate-500 font-medium mt-1">État des lieux de sortie - Contrat {{ $contrat->numero_contrat }} - {{ $contrat->bien->nom_bien }}</p>
                <p class="text-xs text-slate-400 font-bold mt-2">
                    Locataire: {{ $contrat->locataire->user->name }} | 
                    Propriétaire: {{ $contrat->bien->proprietaire->user->name }}
                </p>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        <!-- Alerte si pas d'état des lieux d'entrée -->
        @if(empty($contrat->etat_des_lieux_entree))
        <div class="premium-card p-6 bg-red-50 border border-red-200">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-xl bg-red-600 flex items-center justify-center shrink-0">
                    <i class="fas fa-exclamation-triangle text-white text-xl"></i>
                </div>
                <div>
                    <h3 class="font-black text-lg text-red-900 uppercase tracking-tight">Attention</h3>
                    <p class="text-sm text-red-700 mt-2 leading-relaxed">
                        <strong>Aucun état des lieux d'entrée trouvé.</strong> 
                        La comparaison automatique ne sera pas possible. Vous devrez évaluer manuellement les dégradations.
                    </p>
                </div>
            </div>
        </div>
        @else
        <!-- Instructions avec comparaison -->
        <div class="premium-card p-6 bg-gradient-to-r from-red-50 to-orange-50 border border-red-200">
            <div class="flex items-start gap-4">
                <div class="w-12 h-12 rounded-xl bg-red-600 flex items-center justify-center shrink-0">
                    <i class="fas fa-balance-scale text-white text-xl"></i>
                </div>
                <div>
                    <h3 class="font-black text-lg text-red-900 uppercase tracking-tight">Clôture Automatique du Contrat</h3>
                    <p class="text-sm text-slate-700 mt-2 leading-relaxed">
                        L'état actuel sera automatiquement comparé à l'état d'entrée. 
                        <strong class="text-red-700">Le contrat sera marqué comme TERMINÉ</strong> et le bien redeviendra disponible.
                    </p>
                </div>
            </div>
        </div>
        @endif

        <form method="POST" action="{{ route('contrats.etat-des-lieux-sortie.store', $contrat) }}" id="etatDesLieuxSortieForm" class="space-y-6">
            @csrf

            <!-- 1. INFORMATIONS GÉNÉRALES -->
            <div class="premium-card p-8 bg-white">
                <div class="flex items-center gap-3 mb-6 border-b border-slate-100 pb-4">
                    <div class="w-10 h-10 rounded-lg bg-slate-900 text-white flex items-center justify-center font-black">
                        1
                    </div>
                    <h3 class="text-lg font-black text-slate-900 uppercase tracking-tight">Informations Sortie</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-wider mb-2">Date de visite de sortie</label>
                        <input type="date" name="date_visite" required value="{{ old('date_visite', now()->format('Y-m-d')) }}" class="w-full border-slate-200 rounded-xl font-semibold">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-wider mb-2">Heure de visite</label>
                        <input type="time" name="heure_visite" required value="{{ old('heure_visite', now()->format('H:i')) }}" class="w-full border-slate-200 rounded-xl font-semibold">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-wider mb-2">Clés restituées</label>
                        <input type="number" name="nombre_cles_restituees" min="0" value="{{ old('nombre_cles_restituees', $etatEntree['nombre_cles'] ?? 0) }}" class="w-full border-slate-200 rounded-xl font-semibold">
                        @if(isset($etatEntree['nombre_cles']))
                        <p class="text-xs text-slate-500 mt-1">Remises à l'entrée: {{ $etatEntree['nombre_cles'] }}</p>
                        @endif
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-wider mb-2">Télécommandes / Badges restitués</label>
                        <input type="number" name="nombre_telecommandes_restituees" min="0" value="{{ old('nombre_telecommandes_restituees', $etatEntree['nombre_telecommandes'] ?? 0) }}" class="w-full border-slate-200 rounded-xl font-semibold">
                        @if(isset($etatEntree['nombre_telecommandes']))
                        <p class="text-xs text-slate-500 mt-1">Remises à l'entrée: {{ $etatEntree['nombre_telecommandes'] }}</p>
                        @endif
                    </div>
                </div>
            </div>

            @php
            $sections = [
                ['id' => 'entree', 'numero' => 2, 'titre' => 'Entrée Principale', 'icon' => 'door-open'],
                ['id' => 'salon', 'numero' => 3, 'titre' => 'Salon / Séjour', 'icon' => 'couch'],
                ['id' => 'cuisine', 'numero' => 4, 'titre' => 'Cuisine', 'icon' => 'utensils'],
                ['id' => 'chambres', 'numero' => 5, 'titre' => 'Chambres', 'icon' => 'bed'],
                ['id' => 'salle_bain', 'numero' => 6, 'titre' => 'Salle de Bain', 'icon' => 'bath'],
                ['id' => 'toilettes', 'numero' => 7, 'titre' => 'Toilettes / WC', 'icon' => 'toilet'],
                ['id' => 'electricite', 'numero' => 8, 'titre' => 'Électricité & Plomberie', 'icon' => 'bolt'],
                ['id' => 'exterieur', 'numero' => 9, 'titre' => 'Extérieur', 'icon' => 'tree'],
            ];
            @endphp

            @foreach($sections as $section)
            @php
            $sectionData = $etatEntree['sections'][$section['id']] ?? null;
            $sectionIgnored = isset($sectionData['ignored']) && $sectionData['ignored'] == '1';
            @endphp

            @if(!$sectionIgnored && $sectionData)
            <div class="premium-card p-8 bg-white section-card" data-section="{{ $section['id'] }}">
                <div class="flex items-center justify-between mb-6 border-b border-slate-100 pb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-purple-600 text-white flex items-center justify-center font-black">
                            {{ $section['numero'] }}
                        </div>
                        <div class="flex items-center gap-3">
                            <i class="fas fa-{{ $section['icon'] }} text-purple-600 text-xl"></i>
                            <h3 class="text-lg font-black text-slate-900 uppercase tracking-tight">{{ $section['titre'] }}</h3>
                        </div>
                    </div>
                </div>

                <div class="section-content space-y-4">
                    @foreach($sectionData as $elementCode => $elementData)
                        @if($elementCode !== 'ignored' && is_array($elementData) && isset($elementData['present']) && $elementData['present'])
                        @php
                        $etatEntreeElement = $elementData['etat'] ?? 'bon';
                        $labelElement = ucfirst(str_replace('_', ' ', $elementCode));
                        @endphp
                        <div class="p-4 bg-slate-50 rounded-xl border border-slate-200 hover:border-purple-300 transition-colors element-compare" 
                             data-etat-entree="{{ $etatEntreeElement }}">
                            <div class="flex items-center justify-between flex-wrap gap-4">
                                <div class="flex-1 min-w-[200px]">
                                    <div class="flex items-center gap-2 mb-2">
                                        <span class="font-bold text-sm text-slate-700">{{ $labelElement }}</span>
                                        <span class="text-xs text-slate-400 px-2 py-0.5 bg-slate-200 rounded">
                                            Entrée: 
                                            @if($etatEntreeElement === 'excellent')
                                                <span class="text-emerald-600">✓ Excellent</span>
                                            @elseif($etatEntreeElement === 'bon')
                                                <span class="text-blue-600">Bon</span>
                                            @elseif($etatEntreeElement === 'correct')
                                                <span class="text-amber-600">Correct</span>
                                            @else
                                                <span class="text-red-600">Mauvais</span>
                                            @endif
                                        </span>
                                    </div>
                                    @if(isset($elementData['observations']) && $elementData['observations'])
                                    <p class="text-xs text-slate-500 italic">Note d'entrée: {{ $elementData['observations'] }}</p>
                                    @endif
                                </div>

                                <div class="flex items-center gap-3">
                                    <select name="sections[{{ $section['id'] }}][{{ $elementCode }}][etat]" 
                                            class="border-slate-300 rounded-lg text-sm font-semibold py-2 px-3 etat-select-sortie"
                                            onchange="detecterDegradation(this)">
                                        <option value="excellent" class="text-emerald-600">✓ Excellent état</option>
                                        <option value="bon" class="text-blue-600" selected>Bon état</option>
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

                            <!-- Alerte de dégradation (cachée par défaut) -->
                            <div class="degradation-alert hidden mt-3 p-3 bg-red-50 border border-red-200 rounded-lg">
                                <p class="text-xs font-bold text-red-700">
                                    <i class="fas fa-exclamation-triangle mr-1"></i> 
                                    <span class="degradation-message"></span>
                                </p>
                            </div>

                            <div class="observations-box hidden mt-3">
                                <textarea name="sections[{{ $section['id'] }}][{{ $elementCode }}][observations]" 
                                          rows="2" 
                                          placeholder="Observations de sortie (nouveaux dommages, réparations nécessaires...)"
                                          class="w-full border-slate-300 rounded-lg text-sm font-medium placeholder:text-slate-400"></textarea>
                            </div>
                        </div>
                        @endif
                    @endforeach
                </div>
            </div>
            @endif
            @endforeach

            <!-- Relevés compteurs de sortie -->
            <div class="premium-card p-8 bg-white">
                <div class="flex items-center gap-3 mb-6 border-b border-slate-100 pb-4">
                    <div class="w-10 h-10 rounded-lg bg-emerald-600 text-white flex items-center justify-center font-black">
                        10
                    </div>
                    <div class="flex items-center gap-3">
                        <i class="fas fa-tachometer-alt text-emerald-600 text-xl"></i>
                        <h3 class="text-lg font-black text-slate-900 uppercase tracking-tight">Relevés des Compteurs (Sortie)</h3>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-wider mb-2">Compteur d'eau (index final)</label>
                        <input type="text" name="compteur_eau" placeholder="Ex: 001500" class="w-full border-slate-200 rounded-xl font-semibold">
                        @if(isset($etatEntree['compteurs']['eau']) && $etatEntree['compteurs']['eau'])
                        <p class="text-xs text-slate-500 mt-1">Index d'entrée: {{ $etatEntree['compteurs']['eau'] }}</p>
                        @endif
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-wider mb-2">Compteur électricité (index final)</label>
                        <input type="text" name="compteur_electricite" placeholder="Ex: 006000" class="w-full border-slate-200 rounded-xl font-semibold">
                        @if(isset($etatEntree['compteurs']['electricite']) && $etatEntree['compteurs']['electricite'])
                        <p class="text-xs text-slate-500 mt-1">Index d'entrée: {{ $etatEntree['compteurs']['electricite'] }}</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Observations générales -->
            <div class="premium-card p-8 bg-white">
                <div class="flex items-center gap-3 mb-6">
                    <i class="fas fa-clipboard-list text-purple-600 text-2xl"></i>
                    <h3 class="text-lg font-black text-slate-900 uppercase tracking-tight">Observations Générales de Sortie</h3>
                </div>

                <textarea name="observations_generales" 
                          rows="6" 
                          placeholder="Observations générales sur l'état du logement à la sortie, travaux à prévoir..."
                          class="w-full border-slate-300 rounded-xl font-medium text-slate-700 placeholder:text-slate-400"></textarea>
            </div>

            <!-- Résumé des dégradations et montant suggéré -->
            <div id="resume-degradations" class="premium-card p-8 bg-gradient-to-r from-amber-50 to-orange-50 border-2 border-amber-300 hidden">
                <div class="flex items-start gap-4 mb-6">
                    <div class="w-12 h-12 rounded-xl bg-amber-600 flex items-center justify-center shrink-0">
                        <i class="fas fa-exclamation-circle text-white text-xl"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="font-black text-lg text-amber-900 uppercase tracking-tight">Dégradations Détectées</h3>
                        <p class="text-sm text-amber-700 mt-1">Les éléments suivants ont subi une dégradation par rapport à l'état d'entrée:</p>
                    </div>
                </div>

                <div id="liste-degradations" class="space-y-2 mb-6"></div>

                <div class="p-4 bg-white rounded-xl border border-amber-300">
                    <label class="block text-[10px] font-black text-amber-700 uppercase tracking-wider mb-2">
                        Montant suggéré de retenue sur caution ({{ Auth::user()->agence->devise }})
                    </label>
                    <input type="number" 
                           name="montant_retenue_suggere" 
                           id="montant_retenue_suggere"
                           min="0" 
                           max="{{ $contrat->montant_caution }}"
                           step="0.01"
                           placeholder="0"
                           class="w-full border-amber-300 rounded-xl font-black text-2xl text-amber-900">
                    <p class="text-xs text-amber-600 mt-2">
                        <i class="fas fa-info-circle mr-1"></i> 
                        Caution déposée: {{ number_format($contrat->montant_caution, 0, ',', ' ') }} {{ Auth::user()->agence->devise }}
                    </p>
                </div>
            </div>

            <!-- Boutons d'action -->
            <div class="premium-card p-8 bg-gradient-to-r from-red-900 to-red-800">
                <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                    <div class="text-white">
                        <h4 class="font-black text-lg uppercase tracking-tight">Confirmer la Clôture du Contrat</h4>
                        <p class="text-sm text-red-200 mt-1">Le contrat sera automatiquement terminé et le bien libéré</p>
                    </div>
                    <div class="flex gap-3">
                        <a href="{{ route('contrats.show', $contrat) }}" class="px-8 py-4 bg-slate-700 text-white rounded-xl font-black text-sm uppercase tracking-wider hover:bg-slate-600 transition-colors">
                            <i class="fas fa-times mr-2"></i> Annuler
                        </a>
                        <button type="submit" class="px-8 py-4 bg-red-500 text-white rounded-xl font-black text-sm uppercase tracking-wider hover:bg-red-600 transition-colors shadow-lg shadow-red-200">
                            <i class="fas fa-check-circle mr-2"></i> Clôturer le Contrat
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        const etatsValeur = {
            'excellent': 4,
            'bon': 3,
            'correct': 2,
            'mauvais': 1
        };

        let degradationsDetectees = [];

        function detecterDegradation(selectElement) {
            const container = selectElement.closest('.element-compare');
            const etatEntree = container.dataset.etatEntree;
            const etatSortie = selectElement.value;
            const alert = container.querySelector('.degradation-alert');
            const message = alert.querySelector('.degradation-message');
            const elementLabel = container.querySelector('.font-bold').textContent;

            const valeurEntree = etatsValeur[etatEntree];
            const valeurSortie = etatsValeur[etatSortie];

            // Si l'état de sortie est PIRE que l'état d'entrée
            if (valeurSortie < valeurEntree) {
                alert.classList.remove('hidden');
                message.textContent = `Dégradation: de "${etatEntree}" à "${etatSortie}"`;
                container.classList.add('border-red-400', 'bg-red-50');
                
                // Ajouter à la liste des dégradations
                const index = degradationsDetectees.findIndex(d => d.label === elementLabel);
                if (index >= 0) {
                    degradationsDetectees[index] = {label: elementLabel, entree: etatEntree, sortie: etatSortie};
                } else {
                    degradationsDetectees.push({label: elementLabel, entree: etatEntree, sortie: etatSortie});
                }
            } else {
                alert.classList.add('hidden');
                container.classList.remove('border-red-400', 'bg-red-50');
                
                // Retirer de la liste
                degradationsDetectees = degradationsDetectees.filter(d => d.label !== elementLabel);
            }

            mettreAJourResume();
        }

        function mettreAJourResume() {
            const resume = document.getElementById('resume-degradations');
            const liste = document.getElementById('liste-degradations');

            if (degradationsDetectees.length > 0) {
                resume.classList.remove('hidden');
                liste.innerHTML = '';
                
                degradationsDetectees.forEach(deg => {
                    const item = document.createElement('div');
                    item.className = 'flex items-center justify-between p-3 bg-white rounded-lg border border-amber-200';
                    item.innerHTML = `
                        <span class="font-bold text-sm text-slate-700">${deg.label}</span>
                        <span class="text-xs text-amber-700 font-medium">
                            ${deg.entree} → ${deg.sortie}
                        </span>
                    `;
                    liste.appendChild(item);
                });
            } else {
                resume.classList.add('hidden');
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

        // Initialiser au chargement
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.etat-select-sortie').forEach(select => {
                detecterDegradation(select);
            });
        });
    </script>
</x-app-layout>
