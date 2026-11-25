<x-app-layout>
    <div class="py-12 bg-slate-50 min-h-screen">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-2xl sm:rounded-2xl p-12 relative" id="printable-receipt">
                <!-- Watermark -->
                <div class="absolute inset-0 flex items-center justify-center opacity-[0.02] pointer-events-none no-print">
                    <i class="fas fa-door-closed text-[25rem]"></i>
                </div>

                <div class="relative z-10">
                    <!-- En-tête -->
                    <div class="flex justify-between items-start border-b-4 border-red-600 pb-10 mb-10">
                        <div class="flex gap-6 items-center">
                            <div class="flex flex-col items-center gap-1.5">
                                @if($contrat->agence && $contrat->agence->logo_url)
                                <img src="{{ $contrat->agence->logo_url }}" alt="Logo {{ $contrat->agence->nom }}" class="h-24 w-auto object-contain">
                                @else
                                <div class="w-24 h-24 bg-gradient-to-br from-red-600 to-red-800 rounded-2xl flex items-center justify-center text-white font-black text-4xl shadow-lg shadow-red-200">
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
                            <div class="bg-red-50 text-red-700 px-6 py-3 rounded-xl inline-block mb-3 border-2 border-red-100">
                                <h1 class="text-2xl font-black uppercase tracking-widest">ÉTAT DES LIEUX</h1>
                                <p class="text-sm font-bold text-red-600 mt-1">DE SORTIE</p>
                            </div>
                            <p class="text-slate-500 text-sm font-medium">Contrat N°</p>
                            <p class="text-slate-900 font-black text-lg">{{ $contrat->numero_contrat }}</p>
                            <p class="text-slate-500 text-sm font-medium mt-2">Date de sortie</p>
                            <p class="text-slate-900 font-bold">{{ \Carbon\Carbon::parse($etatDesLieuxSortie['date_visite'])->format('d/m/Y') }} à {{ $etatDesLieuxSortie['heure_visite'] }}</p>
                        </div>
                    </div>

                    <!-- Parties présentes -->
                    <div class="grid grid-cols-2 gap-12 mb-12">
                        <!-- Propriétaire -->
                        <div class="bg-slate-50 p-8 rounded-xl border-2 border-slate-100">
                            <h3 class="text-xs font-black text-slate-400 uppercase mb-6 tracking-widest flex items-center gap-2">
                                <i class="fas fa-user-tie text-red-500 text-lg"></i> PROPRIÉTAIRE
                            </h3>
                            <p class="text-xl font-black text-slate-900 mb-1">{{ $contrat->bien->proprietaire->user->name }}</p>
                            <div class="text-sm text-slate-500 mt-4 space-y-2">
                                <p class="flex items-center gap-2"><i class="fas fa-envelope w-4 text-center text-slate-400"></i> {{ $contrat->bien->proprietaire->user->email }}</p>
                                <p class="flex items-center gap-2"><i class="fas fa-phone w-4 text-center text-slate-400"></i> {{ $contrat->bien->proprietaire->user->telephone }}</p>
                            </div>
                        </div>

                        <!-- Locataire -->
                        <div class="bg-red-50/50 p-8 rounded-xl border-2 border-red-100">
                            <h3 class="text-xs font-black text-red-400 uppercase mb-6 tracking-widest flex items-center gap-2">
                                <i class="fas fa-user text-red-500 text-lg"></i> LOCATAIRE SORTANT
                            </h3>
                            <p class="text-xl font-black text-slate-900 mb-1">{{ $contrat->locataire->user->name }}</p>
                            <div class="text-sm text-slate-500 mt-4 space-y-2">
                                <p class="flex items-center gap-2"><i class="fas fa-envelope w-4 text-center text-slate-400"></i> {{ $contrat->locataire->user->email }}</p>
                                <p class="flex items-center gap-2"><i class="fas fa-phone w-4 text-center text-slate-400"></i> {{ $contrat->locataire->user->telephone }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Détails du bien -->
                    <div class="mb-12 overflow-hidden rounded-2xl border-2 border-red-100 bg-gradient-to-br from-red-50 to-slate-50 p-8">
                        <h3 class="text-sm font-black text-red-600 uppercase mb-6 tracking-[0.2em] flex items-center gap-3">
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

                    <!-- Informations générales de sortie -->
                    <div class="mb-12">
                        <h3 class="text-sm font-black text-red-600 uppercase mb-6 tracking-[0.2em] flex items-center gap-3">
                            <i class="fas fa-info-circle text-2xl"></i> RESTITUTION
                        </h3>
                        <div class="grid grid-cols-4 gap-6">
                            <div class="bg-slate-50 p-6 rounded-xl border-2 border-slate-100 text-center">
                                <p class="text-xs font-black text-slate-400 uppercase mb-2">Clés restituées</p>
                                <p class="text-3xl font-black text-red-600">{{ $etatDesLieuxSortie['nombre_cles_restituees'] ?? 0 }}</p>
                                <p class="text-[9px] text-slate-500 mt-1">Remises: {{ $etatDesLieuxEntree['nombre_cles'] ?? 0 }}</p>
                            </div>
                            <div class="bg-slate-50 p-6 rounded-xl border-2 border-slate-100 text-center">
                                <p class="text-xs font-black text-slate-400 uppercase mb-2">Télécommandes</p>
                                <p class="text-3xl font-black text-red-600">{{ $etatDesLieuxSortie['nombre_telecommandes_restituees'] ?? 0 }}</p>
                                <p class="text-[9px] text-slate-500 mt-1">Remises: {{ $etatDesLieuxEntree['nombre_telecommandes'] ?? 0 }}</p>
                            </div>
                            <div class="bg-red-50 p-6 rounded-xl border-2 border-red-100 text-center">
                                <p class="text-xs font-black text-red-400 uppercase mb-2">Compteur Eau</p>
                                <p class="text-xl font-black text-slate-700">{{ $etatDesLieuxSortie['compteurs']['eau'] ?? 'N/A' }}</p>
                                <p class="text-[9px] text-slate-500 mt-1">Entrée: {{ $etatDesLieuxEntree['compteurs']['eau'] ?? 'N/A' }}</p>
                            </div>
                            <div class="bg-red-50 p-6 rounded-xl border-2 border-red-100 text-center">
                                <p class="text-xs font-black text-red-400 uppercase mb-2">Compteur Élec.</p>
                                <p class="text-xl font-black text-slate-700">{{ $etatDesLieuxSortie['compteurs']['electricite'] ?? 'N/A' }}</p>
                                <p class="text-[9px] text-slate-500 mt-1">Entrée: {{ $etatDesLieuxEntree['compteurs']['electricite'] ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Dégradations détectées (si applicable) -->
                    @if(isset($etatDesLieuxSortie['degradations']) && count($etatDesLieuxSortie['degradations']) > 0)
                    <div class="mb-12 page-break-inside-avoid">
                        <h3 class="text-sm font-black text-red-600 uppercase mb-6 tracking-[0.2em] flex items-center gap-3">
                            <i class="fas fa-exclamation-triangle text-2xl"></i> DÉGRADATIONS CONSTATÉES
                        </h3>
                        <div class="bg-gradient-to-br from-red-50 to-orange-50 border-2 border-red-200 rounded-2xl overflow-hidden">
                            <div class="bg-red-600 text-white px-6 py-4">
                                <p class="font-black text-lg">{{ count($etatDesLieuxSortie['degradations']) }} dégradation(s) détectée(s)</p>
                            </div>
                            <div class="p-6 space-y-3">
                                @foreach($etatDesLieuxSortie['degradations'] as $degradation)
                                <div class="flex items-center justify-between p-4 bg-white rounded-xl border border-red-200">
                                    <div>
                                        <p class="font-bold text-slate-900">{{ $degradation['label'] }}</p>
                                        <p class="text-xs text-slate-500 uppercase">Section: {{ ucfirst(str_replace('_', ' ', $degradation['section'])) }}</p>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-lg text-xs font-bold">{{ ucfirst($degradation['etat_entree']) }}</span>
                                        <i class="fas fa-arrow-right text-slate-400"></i>
                                        <span class="px-3 py-1 bg-red-100 text-red-700 rounded-lg text-xs font-bold">{{ ucfirst($degradation['etat_sortie']) }}</span>
                                        <span class="px-2 py-1 bg-red-600 text-white rounded text-xs font-black">-{{ $degradation['gravite'] }}</span>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @else
                    <div class="mb-12 page-break-inside-avoid">
                        <div class="bg-gradient-to-br from-emerald-50 to-green-50 border-2 border-emerald-200 rounded-2xl p-8 text-center">
                            <i class="fas fa-check-circle text-6xl text-emerald-500 mb-4"></i>
                            <h3 class="text-xl font-black text-emerald-900 uppercase mb-2">Aucune Dégradation Constatée</h3>
                            <p class="text-slate-600">Le logement a été rendu en bon état général.</p>
                        </div>
                    </div>
                    @endif

                    <!-- Bilan financier caution -->
                    <div class="mb-12 page-break-inside-avoid">
                        <h3 class="text-sm font-black text-red-600 uppercase mb-6 tracking-[0.2em] flex items-center gap-3">
                            <i class="fas fa-calculator text-2xl"></i> BILAN FINANCIER - CAUTION
                        </h3>
                        <div class="bg-gradient-to-br from-indigo-50 to-blue-50 border-2 border-indigo-200 rounded-2xl p-8">
                            <div class="grid grid-cols-3 gap-6">
                                <div class="bg-white p-6 rounded-xl border border-indigo-200 text-center">
                                    <p class="text-xs font-black text-slate-500 uppercase mb-2">Caution Déposée</p>
                                    <p class="text-3xl font-black text-indigo-600">{{ number_format($contrat->montant_caution, 0, ',', ' ') }}</p>
                                    <p class="text-xs text-slate-400">{{ $contrat->agence->devise }}</p>
                                </div>
                                <div class="bg-white p-6 rounded-xl border border-red-200 text-center">
                                    <p class="text-xs font-black text-slate-500 uppercase mb-2">Montant Retenu</p>
                                    <p class="text-3xl font-black text-red-600">{{ number_format($contrat->montant_caution_retenu, 0, ',', ' ') }}</p>
                                    <p class="text-xs text-slate-400">{{ $contrat->agence->devise }}</p>
                                </div>
                                <div class="bg-white p-6 rounded-xl border border-emerald-200 text-center">
                                    <p class="text-xs font-black text-slate-500 uppercase mb-2">Montant Remboursé</p>
                                    <p class="text-3xl font-black text-emerald-600">{{ number_format($contrat->montant_caution_rembourse, 0, ',', ' ') }}</p>
                                    <p class="text-xs text-slate-400">{{ $contrat->agence->devise }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Observations générales -->
                    @if(isset($etatDesLieuxSortie['observations_generales']) && $etatDesLieuxSortie['observations_generales'])
                    <div class="mb-12">
                        <h3 class="text-sm font-black text-red-600 uppercase mb-6 tracking-[0.2em] flex items-center gap-3">
                            <i class="fas fa-clipboard-list text-2xl"></i> OBSERVATIONS GÉNÉRALES
                        </h3>
                        <div class="bg-slate-50 p-6 rounded-xl border-2 border-slate-100">
                            <p class="text-slate-700 leading-relaxed whitespace-pre-wrap">{{ $etatDesLieuxSortie['observations_generales'] }}</p>
                        </div>
                    </div>
                    @endif

                    <!-- Notes de clôture -->
                    @if($contrat->notes_cloture)
                    <div class="mb-12">
                        <h3 class="text-sm font-black text-red-600 uppercase mb-6 tracking-[0.2em] flex items-center gap-3">
                            <i class="fas fa-file-alt text-2xl"></i> NOTES DE CLÔTURE
                        </h3>
                        <div class="bg-amber-50 p-6 rounded-xl border-2 border-amber-200">
                            <p class="text-slate-700 leading-relaxed">{{ $contrat->notes_cloture }}</p>
                        </div>
                    </div>
                    @endif

                    <!-- Signatures -->
                    <div class="pt-10 border-t-2 border-slate-100">
                        <h3 class="text-sm font-black text-slate-500 uppercase mb-8 tracking-widest text-center">SIGNATURES DES PARTIES</h3>
                        <p class="text-xs text-center text-slate-500 mb-6 italic">
                            Les parties reconnaissent avoir inspecté ensemble le logement et constatent l'état décrit ci-dessus.
                            Le locataire atteste avoir restitué les clés et quitté définitivement les lieux.
                        </p>

                        <div class="grid grid-cols-3 gap-8 max-w-4xl mx-auto">
                            <!-- Propriétaire -->
                            <div class="text-center">
                                <div class="w-full h-32 border-2 border-dashed border-slate-300 rounded-xl flex items-center justify-center bg-slate-50 mb-3">
                                    <span class="text-slate-400 font-medium text-xs">Signature Propriétaire</span>
                                </div>
                                <p class="font-black text-slate-900">{{ $contrat->bien->proprietaire->user->name }}</p>
                                <p class="text-xs text-slate-500">Le {{ \Carbon\Carbon::parse($etatDesLieuxSortie['date_visite'])->format('d/m/Y') }}</p>
                            </div>

                            <!-- Locataire -->
                            <div class="text-center">
                                <div class="w-full h-32 border-2 border-dashed border-red-300 rounded-xl flex items-center justify-center bg-red-50 mb-3">
                                    <span class="text-red-600 font-medium text-xs">Signature Locataire</span>
                                </div>
                                <p class="font-black text-slate-900">{{ $contrat->locataire->user->name }}</p>
                                <p class="text-xs text-slate-500">Le {{ \Carbon\Carbon::parse($etatDesLieuxSortie['date_visite'])->format('d/m/Y') }}</p>
                            </div>

                            <!-- Agent / Témoin -->
                            <div class="text-center">
                                <div class="w-full h-32 border-2 border-dashed border-indigo-300 rounded-xl flex items-center justify-center bg-indigo-50 mb-3">
                                    <span class="text-indigo-600 font-medium text-xs">Agent / Témoin</span>
                                </div>
                                <p class="font-black text-slate-900">{{ $etatDesLieuxSortie['rempli_par'] ?? 'Agent immobilier' }}</p>
                                <p class="text-xs text-slate-500">{{ $contrat->agence->nom }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Statut final du contrat -->
                    <div class="mt-12 p-6 bg-gradient-to-r from-slate-800 to-slate-900 rounded-2xl text-center">
                        <p class="text-white font-black text-xl uppercase tracking-widest mb-2">
                            <i class="fas fa-check-circle mr-2"></i> CONTRAT CLÔTURÉ
                        </p>
                        <p class="text-slate-300 text-sm">
                            Date de clôture: {{ \Carbon\Carbon::parse($contrat->date_cloture)->format('d/m/Y') }}
                        </p>
                    </div>

                    <!-- Note de bas de page -->
                    <div class="mt-12 pt-8 border-t border-slate-200 text-center">
                        <p class="text-slate-400 text-sm italic mb-1">
                            Document généré électroniquement par {{ $contrat->agence->nom }} le {{ \Carbon\Carbon::parse($etatDesLieuxSortie['date_enregistrement'])->format('d/m/Y à H:i') }}.
                        </p>
                        <p class="text-slate-500 font-bold">
                            Cet état des lieux de sortie est établi en deux (2) exemplaires originaux, un pour chaque partie.
                        </p>
                        <p class="text-xs text-slate-400 mt-3">
                            Le présent document fait foi en cas de litige concernant la restitution de la caution.
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
                <button id="printBtn" type="button" class="bg-red-600 hover:bg-red-700 text-white px-8 py-3.5 rounded-xl font-black shadow-lg shadow-red-200 transition-all hover:-translate-y-1 flex items-center gap-3 uppercase text-sm tracking-widest">
                    <i class="fas fa-print text-lg"></i> Imprimer l'état des lieux de sortie
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
        }
    </style>

    <script>
        document.getElementById('printBtn').addEventListener('click', function() {
            window.print();
        });
    </script>
</x-app-layout>
