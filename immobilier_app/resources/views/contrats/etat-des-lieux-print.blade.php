<x-app-layout>
    <div class="py-12 bg-slate-50 min-h-screen">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-2xl sm:rounded-2xl p-12 relative" id="printable-receipt">
                <!-- Watermark -->
                <div class="absolute inset-0 flex items-center justify-center opacity-[0.02] pointer-events-none no-print">
                    <i class="fas fa-clipboard-check text-[25rem]"></i>
                </div>

                <div class="relative z-10">
                    <!-- En-tête -->
                    <div class="flex justify-between items-start border-b-4 border-emerald-600 pb-10 mb-10">
                        <div class="flex gap-6 items-center">
                            <div class="flex flex-col items-center gap-1.5">
                                @if($contrat->agence->logo)
                                <img src="{{ asset('storage/' . $contrat->agence->logo) }}" alt="Logo {{ $contrat->agence->nom }}" class="h-24 w-auto object-contain">
                                @else
                                <div class="w-24 h-24 bg-gradient-to-br from-emerald-600 to-emerald-800 rounded-2xl flex items-center justify-center text-white font-black text-4xl shadow-lg shadow-emerald-200">
                                    {{ strtoupper(substr($contrat->agence->nom, 0, 2)) }}
                                </div>
                                @endif
                                <span class="text-lg font-black text-slate-900 mt-2 text-center w-full block">{{ $contrat->agence->nom }}</span>
                            </div>
                            <div>
                                <div class="text-sm text-slate-500 mt-1 space-y-0.5">
                                    <p class="flex items-center gap-2"><i class="fas fa-map-marker-alt w-4 text-center"></i> {{ $contrat->agence->adresse }}</p>
                                    <p class="flex items-center gap-2"><i class="fas fa-phone w-4 text-center"></i> {{ $contrat->agence->telephone }}</p>
                                    <p class="flex items-center gap-2"><i class="fas fa-envelope w-4 text-center"></i> {{ $contrat->agence->email }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="bg-emerald-50 text-emerald-700 px-6 py-3 rounded-xl inline-block mb-3 border-2 border-emerald-100">
                                <h1 class="text-2xl font-black uppercase tracking-widest">ÉTAT DES LIEUX</h1>
                                <p class="text-sm font-bold text-emerald-600 mt-1">D'ENTRÉE</p>
                            </div>
                            <p class="text-slate-500 text-sm font-medium">Contrat N°</p>
                            <p class="text-slate-900 font-black text-lg">{{ $contrat->numero_contrat }}</p>
                            <p class="text-slate-500 text-sm font-medium mt-2">Date de visite</p>
                            <p class="text-slate-900 font-bold">{{ \Carbon\Carbon::parse($etatDesLieux['date_visite'])->format('d/m/Y') }} à {{ $etatDesLieux['heure_visite'] }}</p>
                        </div>
                    </div>

                    <!-- Parties présentes -->
                    <div class="grid grid-cols-2 gap-12 mb-12">
                        <!-- Bailleur (Propriétaire) -->
                        <div class="bg-slate-50 p-8 rounded-xl border-2 border-slate-100">
                            <h3 class="text-xs font-black text-slate-400 uppercase mb-6 tracking-widest flex items-center gap-2">
                                <i class="fas fa-user-tie text-emerald-500 text-lg"></i> PROPRIÉTAIRE
                            </h3>
                            <p class="text-xl font-black text-slate-900 mb-1">{{ $contrat->bien->proprietaire->user->name }}</p>
                            <div class="text-sm text-slate-500 mt-4 space-y-2">
                                <p class="flex items-center gap-2"><i class="fas fa-envelope w-4 text-center text-slate-400"></i> {{ $contrat->bien->proprietaire->user->email }}</p>
                                <p class="flex items-center gap-2"><i class="fas fa-phone w-4 text-center text-slate-400"></i> {{ $contrat->bien->proprietaire->user->telephone }}</p>
                            </div>
                        </div>

                        <!-- Locataire -->
                        <div class="bg-emerald-50/50 p-8 rounded-xl border-2 border-emerald-100">
                            <h3 class="text-xs font-black text-emerald-400 uppercase mb-6 tracking-widest flex items-center gap-2">
                                <i class="fas fa-user text-emerald-500 text-lg"></i> LOCATAIRE
                            </h3>
                            <p class="text-xl font-black text-slate-900 mb-1">{{ $contrat->locataire->user->name }}</p>
                            <div class="text-sm text-slate-500 mt-4 space-y-2">
                                <p class="flex items-center gap-2"><i class="fas fa-envelope w-4 text-center text-slate-400"></i> {{ $contrat->locataire->user->email }}</p>
                                <p class="flex items-center gap-2"><i class="fas fa-phone w-4 text-center text-slate-400"></i> {{ $contrat->locataire->user->telephone }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Détails du bien -->
                    <div class="mb-12 overflow-hidden rounded-2xl border-2 border-emerald-100 bg-gradient-to-br from-emerald-50 to-slate-50 p-8">
                        <h3 class="text-sm font-black text-emerald-600 uppercase mb-6 tracking-[0.2em] flex items-center gap-3">
                            <i class="fas fa-home text-2xl"></i> BIEN CONCERNÉ
                        </h3>
                        <div class="grid grid-cols-2 gap-8">
                            <div>
                                <p class="text-xs font-black text-slate-400 uppercase mb-1">Désignation</p>
                                <p class="text-lg font-bold text-slate-900">{{ $contrat->bien->nom_bien }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-black text-slate-400 uppercase mb-1">Type</p>
                                <p class="text-lg font-bold text-slate-900 uppercase">{{ $contrat->bien->type_bien }}</p>
                            </div>
                            <div class="col-span-2">
                                <p class="text-xs font-black text-slate-400 uppercase mb-1">Adresse</p>
                                <p class="text-lg font-bold text-slate-900">{{ $contrat->bien->adresse }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Informations générales -->
                    <div class="mb-12">
                        <h3 class="text-sm font-black text-emerald-600 uppercase mb-6 tracking-[0.2em] flex items-center gap-3">
                            <i class="fas fa-info-circle text-2xl"></i> INFORMATIONS GÉNÉRALES
                        </h3>
                        <div class="grid grid-cols-4 gap-6">
                            <div class="bg-slate-50 p-6 rounded-xl border-2 border-slate-100 text-center">
                                <p class="text-xs font-black text-slate-400 uppercase mb-2">Clés remises</p>
                                <p class="text-3xl font-black text-emerald-600">{{ $etatDesLieux['nombre_cles'] ?? 0 }}</p>
                            </div>
                            <div class="bg-slate-50 p-6 rounded-xl border-2 border-slate-100 text-center">
                                <p class="text-xs font-black text-slate-400 uppercase mb-2">Télécommandes</p>
                                <p class="text-3xl font-black text-emerald-600">{{ $etatDesLieux['nombre_telecommandes'] ?? 0 }}</p>
                            </div>
                            <div class="bg-emerald-50 p-6 rounded-xl border-2 border-emerald-100 text-center">
                                <p class="text-xs font-black text-emerald-400 uppercase mb-2">Compteur Eau</p>
                                <p class="text-xl font-black text-slate-700">{{ $etatDesLieux['compteurs']['eau'] ?? 'N/A' }}</p>
                            </div>
                            <div class="bg-emerald-50 p-6 rounded-xl border-2 border-emerald-100 text-center">
                                <p class="text-xs font-black text-emerald-400 uppercase mb-2">Compteur Élec.</p>
                                <p class="text-xl font-black text-slate-700">{{ $etatDesLieux['compteurs']['electricite'] ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Détail des sections -->
                    @php
                    $sections = [
                        ['id' => 'entree', 'titre' => 'Entrée Principale', 'icon' => 'door-open'],
                        ['id' => 'salon', 'titre' => 'Salon / Séjour', 'icon' => 'couch'],
                        ['id' => 'cuisine', 'titre' => 'Cuisine', 'icon' => 'utensils'],
                        ['id' => 'chambres', 'titre' => 'Chambres', 'icon' => 'bed'],
                        ['id' => 'salle_bain', 'titre' => 'Salle de Bain', 'icon' => 'bath'],
                        ['id' => 'toilettes', 'titre' => 'Toilettes / WC', 'icon' => 'toilet'],
                        ['id' => 'electricite', 'titre' => 'Électricité & Plomberie', 'icon' => 'bolt'],
                        ['id' => 'exterieur', 'titre' => 'Extérieur', 'icon' => 'tree'],
                    ];
                    
                    $etatsLibelle = [
                        'excellent' => ['label' => 'Excellent', 'color' => 'emerald', 'icon' => 'check-circle'],
                        'bon' => ['label' => 'Bon', 'color' => 'blue', 'icon' => 'check'],
                        'correct' => ['label' => 'Correct', 'color' => 'amber', 'icon' => 'exclamation-circle'],
                        'mauvais' => ['label' => 'Mauvais', 'color' => 'red', 'icon' => 'times-circle'],
                    ];
                    @endphp

                    <div class="mb-12">
                        <h3 class="text-sm font-black text-emerald-600 uppercase mb-6 tracking-[0.2em] flex items-center gap-3">
                            <i class="fas fa-list-check text-2xl"></i> ÉTAT DÉTAILLÉ DES PIÈCES
                        </h3>

                        @foreach($sections as $section)
                            @php
                            $sectionData = $etatDesLieux['sections'][$section['id']] ?? null;
                            $sectionIgnored = isset($sectionData['ignored']) && $sectionData['ignored'] == '1';
                            @endphp

                            @if($sectionData && !$sectionIgnored)
                            <div class="mb-8 page-break-inside-avoid">
                                <!-- Titre de section -->
                                <div class="bg-gradient-to-r from-emerald-600 to-emerald-700 text-white px-6 py-4 rounded-t-xl flex items-center gap-3">
                                    <i class="fas fa-{{ $section['icon'] }} text-2xl"></i>
                                    <h4 class="text-lg font-black uppercase tracking-wider">{{ $section['titre'] }}</h4>
                                </div>

                                <!-- Éléments de la section -->
                                <div class="border-2 border-emerald-100 rounded-b-xl overflow-hidden">
                                    @php
                                    $hasElements = false;
                                    @endphp

                                    @foreach($sectionData as $elementCode => $elementData)
                                        @if($elementCode !== 'ignored' && is_array($elementData) && isset($elementData['present']) && $elementData['present'])
                                            @php
                                            $hasElements = true;
                                            $etat = $elementData['etat'] ?? 'bon';
                                            $etatInfo = $etatsLibelle[$etat];
                                            $labelElement = ucfirst(str_replace('_', ' ', $elementCode));
                                            @endphp

                                            <div class="p-4 border-b border-slate-100 hover:bg-slate-50 transition-colors flex items-center justify-between">
                                                <div class="flex-1">
                                                    <p class="font-bold text-slate-900">{{ $labelElement }}</p>
                                                    @if(isset($elementData['observations']) && $elementData['observations'])
                                                    <p class="text-xs text-slate-500 italic mt-1">
                                                        <i class="fas fa-comment-dots mr-1"></i>
                                                        {{ $elementData['observations'] }}
                                                    </p>
                                                    @endif
                                                </div>
                                                <div class="flex items-center gap-2">
                                                    <span class="px-4 py-2 bg-{{ $etatInfo['color'] }}-100 text-{{ $etatInfo['color'] }}-700 rounded-lg font-bold text-sm flex items-center gap-2">
                                                        <i class="fas fa-{{ $etatInfo['icon'] }}"></i>
                                                        {{ $etatInfo['label'] }}
                                                    </span>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach

                                    @if(!$hasElements)
                                    <div class="p-6 text-center text-slate-400 italic">
                                        <i class="fas fa-info-circle mr-2"></i>
                                        Aucun élément enregistré pour cette section
                                    </div>
                                    @endif
                                </div>
                            </div>
                            @endif
                        @endforeach
                    </div>

                    <!-- Observations générales -->
                    @if(isset($etatDesLieux['observations_generales']) && $etatDesLieux['observations_generales'])
                    <div class="mb-12">
                        <h3 class="text-sm font-black text-emerald-600 uppercase mb-6 tracking-[0.2em] flex items-center gap-3">
                            <i class="fas fa-clipboard-list text-2xl"></i> OBSERVATIONS GÉNÉRALES
                        </h3>
                        <div class="bg-slate-50 p-6 rounded-xl border-2 border-slate-100">
                            <p class="text-slate-700 leading-relaxed whitespace-pre-wrap">{{ $etatDesLieux['observations_generales'] }}</p>
                        </div>
                    </div>
                    @endif

                    <!-- Résumé statistique -->
                    <div class="mb-12 bg-gradient-to-br from-emerald-50 to-blue-50 p-8 rounded-2xl border-2 border-emerald-200">
                        <h3 class="text-sm font-black text-emerald-700 uppercase mb-6 tracking-[0.2em] text-center">
                            <i class="fas fa-chart-pie mr-2"></i> RÉSUMÉ STATISTIQUE
                        </h3>
                        @php
                        $stats = ['excellent' => 0, 'bon' => 0, 'correct' => 0, 'mauvais' => 0];
                        foreach($etatDesLieux['sections'] as $sectionId => $sectionData) {
                            if(!isset($sectionData['ignored']) || $sectionData['ignored'] != '1') {
                                foreach($sectionData as $elementCode => $elementData) {
                                    if($elementCode !== 'ignored' && is_array($elementData) && isset($elementData['present']) && $elementData['present']) {
                                        $etat = $elementData['etat'] ?? 'bon';
                                        $stats[$etat]++;
                                    }
                                }
                            }
                        }
                        $total = array_sum($stats);
                        @endphp

                        <div class="grid grid-cols-4 gap-4">
                            <div class="bg-white p-4 rounded-xl border border-emerald-300 text-center">
                                <p class="text-2xl font-black text-emerald-600">{{ $stats['excellent'] }}</p>
                                <p class="text-xs font-bold text-slate-500 uppercase">Excellent</p>
                            </div>
                            <div class="bg-white p-4 rounded-xl border border-blue-300 text-center">
                                <p class="text-2xl font-black text-blue-600">{{ $stats['bon'] }}</p>
                                <p class="text-xs font-bold text-slate-500 uppercase">Bon</p>
                            </div>
                            <div class="bg-white p-4 rounded-xl border border-amber-300 text-center">
                                <p class="text-2xl font-black text-amber-600">{{ $stats['correct'] }}</p>
                                <p class="text-xs font-bold text-slate-500 uppercase">Correct</p>
                            </div>
                            <div class="bg-white p-4 rounded-xl border border-red-300 text-center">
                                <p class="text-2xl font-black text-red-600">{{ $stats['mauvais'] }}</p>
                                <p class="text-xs font-bold text-slate-500 uppercase">Mauvais</p>
                            </div>
                        </div>
                        <p class="text-center text-slate-600 font-bold mt-4">
                            Total : <span class="text-emerald-700 text-xl">{{ $total }}</span> éléments inspectés
                        </p>
                    </div>

                    <!-- Signatures -->
                    <div class="pt-10 border-t-2 border-slate-100">
                        <h3 class="text-sm font-black text-slate-500 uppercase mb-8 tracking-widest text-center">SIGNATURES DES PARTIES</h3>
                        <p class="text-xs text-center text-slate-500 mb-6 italic">
                            Les parties reconnaissent avoir inspecté ensemble le logement et attestent de l'exactitude du présent état des lieux.
                        </p>

                        <div class="grid grid-cols-3 gap-8 max-w-4xl mx-auto">
                            <!-- Propriétaire -->
                            <div class="text-center">
                                <div class="w-full h-32 border-2 border-dashed border-slate-300 rounded-xl flex items-center justify-center bg-slate-50 mb-3">
                                    <span class="text-slate-400 font-medium text-xs">Signature Propriétaire</span>
                                </div>
                                <p class="font-black text-slate-900">{{ $contrat->bien->proprietaire->user->name }}</p>
                                <p class="text-xs text-slate-500">Le {{ \Carbon\Carbon::parse($etatDesLieux['date_visite'])->format('d/m/Y') }}</p>
                            </div>

                            <!-- Locataire -->
                            <div class="text-center">
                                <div class="w-full h-32 border-2 border-dashed border-emerald-300 rounded-xl flex items-center justify-center bg-emerald-50 mb-3">
                                    <span class="text-emerald-600 font-medium text-xs">Signature Locataire</span>
                                </div>
                                <p class="font-black text-slate-900">{{ $contrat->locataire->user->name }}</p>
                                <p class="text-xs text-slate-500">Le {{ \Carbon\Carbon::parse($etatDesLieux['date_visite'])->format('d/m/Y') }}</p>
                            </div>

                            <!-- Agent / Témoin -->
                            <div class="text-center">
                                <div class="w-full h-32 border-2 border-dashed border-indigo-300 rounded-xl flex items-center justify-center bg-indigo-50 mb-3">
                                    <span class="text-indigo-600 font-medium text-xs">Agent / Témoin</span>
                                </div>
                                <p class="font-black text-slate-900">{{ $etatDesLieux['rempli_par'] ?? 'Agent immobilier' }}</p>
                                <p class="text-xs text-slate-500">{{ $contrat->agence->nom }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Note de bas de page -->
                    <div class="mt-12 pt-8 border-t border-slate-200 text-center">
                        <p class="text-slate-400 text-sm italic mb-1">
                            Document généré électroniquement par {{ $contrat->agence->nom }} le {{ \Carbon\Carbon::parse($etatDesLieux['date_enregistrement'])->format('d/m/Y à H:i') }}.
                        </p>
                        <p class="text-slate-500 font-bold">
                            Cet état des lieux est établi en deux (2) exemplaires originaux, un pour chaque partie.
                        </p>
                        <p class="text-xs text-slate-400 mt-3">
                            Conformément aux dispositions légales, cet état des lieux pourra servir de référence en cas de litige.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="mt-8 flex justify-between items-center no-print">
                <a href="{{ route('contrats.show', $contrat) }}" class="group flex items-center gap-2 text-slate-500 hover:text-slate-900 font-bold transition-colors">
                    <div class="w-10 h-10 rounded-full bg-white border border-slate-200 flex items-center justify-center group-hover:border-slate-400 group-hover:bg-slate-50 transition-all">
                        <i class="fas fa-arrow-left"></i>
                    </div>
                    Retour au contrat
                </a>
                <button id="printBtn" type="button" class="bg-emerald-600 hover:bg-emerald-700 text-white px-8 py-3.5 rounded-xl font-black shadow-lg shadow-emerald-200 transition-all hover:-translate-y-1 flex items-center gap-3 uppercase text-sm tracking-widest">
                    <i class="fas fa-print text-lg"></i> Imprimer l'état des lieux
                </button>
            </div>
        </div>
    </div>

    <style>
        @media print {
            @page {
                margin: 0.5cm;
                size: A4;
            }

            body * {
                visibility: hidden;
            }

            aside,
            nav,
            header,
            footer {
                display: none !important;
            }

            html,
            body,
            .min-h-screen,
            .h-screen,
            .overflow-hidden,
            .overflow-y-auto {
                height: auto !important;
                min-height: auto !important;
                overflow: visible !important;
                background-color: white !important;
            }

            #printable-receipt,
            #printable-receipt * {
                visibility: visible;
            }

            #printable-receipt {
                position: absolute !important;
                left: 0 !important;
                top: 0 !important;
                width: 100% !important;
                margin: 0 !important;
                padding: 10px !important;
                background-color: white !important;
                box-shadow: none !important;
                border: none !important;
            }

            .no-print,
            .no-print * {
                display: none !important;
                visibility: hidden !important;
            }

            #printable-receipt * {
                color-adjust: exact !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            /* Compress spacing for print */
            #printable-receipt .mb-10 {
                margin-bottom: 1rem !important;
            }

            #printable-receipt .pb-10 {
                padding-bottom: 1rem !important;
            }

            #printable-receipt .mb-12 {
                margin-bottom: 1.25rem !important;
            }

            #printable-receipt .pt-10 {
                padding-top: 1rem !important;
            }

            #printable-receipt .gap-12 {
                gap: 1rem !important;
            }

            #printable-receipt .p-12 {
                padding: 1rem !important;
            }

            #printable-receipt .p-8 {
                padding: 0.75rem !important;
            }

            #printable-receipt .p-6 {
                padding: 0.5rem !important;
            }

            #printable-receipt .h-32 {
                height: 5rem !important;
            }

            #printable-receipt .mb-8 {
                margin-bottom: 0.75rem !important;
            }

            /* Éviter les coupures de page */
            .page-break-inside-avoid {
                page-break-inside: avoid;
                break-inside: avoid;
            }

            /* Force page breaks before certain sections if needed */
            .page-break-before {
                page-break-before: always;
                break-before: always;
            }
        }
    </style>

    <script>
        document.getElementById('printBtn').addEventListener('click', function() {
            window.print();
        });
    </script>
</x-app-layout>
