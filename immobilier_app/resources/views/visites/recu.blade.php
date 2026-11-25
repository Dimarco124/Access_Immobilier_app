<x-app-layout>
    <div class="py-12 bg-slate-50 min-h-screen">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-2xl sm:rounded-2xl p-12 relative" id="printable-receipt">
                <!-- Watermark -->
                <div class="absolute inset-0 flex items-center justify-center opacity-[0.02] pointer-events-none no-print">
                    <i class="fas fa-check-circle text-[25rem]"></i>
                </div>

                <div class="relative z-10">
                    <!-- En-tête du reçu -->
                    <div class="flex justify-between items-start border-b-2 border-slate-100 pb-10 mb-10">
                        <div class="flex gap-6 items-center">
                            <div class="flex flex-col items-center gap-1.5">
                                @if($visite->agence && $visite->agence->logo_url)
                                    <img src="{{ $visite->agence->logo_url }}" alt="Logo {{ $visite->agence->nom }}" class="h-20 w-auto object-contain">
                                @else
                                    <div class="w-20 h-20 bg-gradient-to-br from-blue-600 to-blue-800 rounded-2xl flex items-center justify-center text-white font-black text-3xl shadow-lg shadow-blue-200">
                                        {{ strtoupper(substr($visite->agence->nom, 0, 2)) }}
                                    </div>
                                @endif
                                <span class="text-sm font-black text-slate-900 mt-2 text-center w-full block">{{ $visite->agence->nom }}</span>
                            </div>
                            <div>
                                <div class="text-sm text-slate-500 mt-1 space-y-0.5">
                                    <p class="flex items-center gap-2"><i class="fas fa-map-marker-alt w-4 text-center"></i> {{ $visite->agence->adresse }}</p>
                                    <p class="flex items-center gap-2"><i class="fas fa-phone w-4 text-center"></i> {{ $visite->agence->telephone }}</p>
                                    <p class="flex items-center gap-2"><i class="fas fa-envelope w-4 text-center"></i> {{ $visite->agence->email }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="bg-emerald-50 text-emerald-700 px-4 py-2 rounded-lg inline-block mb-3 border border-emerald-100">
                                <h1 class="text-xl font-black uppercase tracking-widest">Reçu de Visite</h1>
                            </div>
                            <p class="text-slate-500 text-sm font-medium">Référence</p>
                            <p class="text-slate-900 font-black text-lg">VIS-{{ str_pad($visite->id, 5, '0', STR_PAD_LEFT) }}</p>
                            <p class="text-slate-500 text-sm font-medium mt-2">Date d'émission</p>
                            <p class="text-slate-900 font-bold">{{ $visite->created_at->format('d/m/Y') }}</p>
                        </div>
                    </div>

                    <!-- Informations Client & Détails -->
                    <div class="grid grid-cols-2 gap-12 mb-12">
                        <div class="bg-slate-50 p-6 rounded-xl border border-slate-100">
                            <h3 class="text-xs font-black text-slate-400 uppercase mb-4 tracking-widest flex items-center gap-2">
                                <i class="fas fa-user-tag text-emerald-500"></i> Visiteur (Locataire)
                            </h3>
                            <p class="text-xl font-black text-slate-900 mb-1">{{ $visite->locataire->user->name }}</p>
                            <div class="text-sm text-slate-500 mb-3 mt-2 space-y-1">
                                <p class="flex items-center gap-2"><i class="fas fa-envelope w-4 text-center text-slate-400"></i> {{ $visite->locataire->user->email }}</p>
                                @if($visite->locataire->user->telephone)
                                <p class="flex items-center gap-2"><i class="fas fa-phone w-4 text-center text-slate-400"></i> {{ $visite->locataire->user->telephone }}</p>
                                @endif
                                @if($visite->locataire->numero_piece_identite)
                                <p class="flex items-center gap-2"><i class="fas fa-id-card w-4 text-center text-slate-400"></i> CNI: {{ $visite->locataire->numero_piece_identite }}</p>
                                @endif
                                @if($visite->locataire->profession)
                                <p class="flex items-center gap-2"><i class="fas fa-briefcase w-4 text-center text-slate-400"></i> {{ $visite->locataire->profession }}</p>
                                @endif
                                @if($visite->locataire->pays)
                                <p class="flex items-center gap-2"><i class="fas fa-globe w-4 text-center text-slate-400"></i> {{ $visite->locataire->pays }}</p>
                                @endif
                            </div>
                            <div class="pt-3 border-t border-slate-200 mt-2">
                                <p class="text-slate-600 font-bold uppercase text-xs tracking-wider mb-1">Désignation du Bien à visiter</p>
                                <p class="text-slate-600 font-medium">{{ $visite->bien->nom_bien }}</p>
                            </div>
                            @if($visite->unite)
                            <p class="inline-block mt-2 bg-white px-3 py-1 rounded border border-slate-200 text-slate-700 font-bold uppercase text-xs shadow-sm">
                                <i class="fas fa-door-open mr-1 text-slate-400"></i> {{ $visite->unite->nom_unite }}
                            </p>
                            @endif
                        </div>
                        <div class="bg-emerald-50/50 p-6 rounded-xl border border-emerald-100">
                            <h3 class="text-xs font-black text-emerald-600 uppercase mb-4 tracking-widest flex items-center gap-2">
                                <i class="fas fa-calendar-check text-emerald-500"></i> Détails de la visite
                            </h3>
                            <div class="space-y-3">
                                <div class="flex justify-between items-center border-b border-emerald-100/50 pb-2">
                                    <span class="text-slate-500 text-sm">Date prévue</span>
                                    <span class="text-slate-900 font-bold capitalize">{{ $visite->date_heure_visite->format('d/m/Y à H:i') }}</span>
                                </div>
                                <div class="flex justify-between items-center border-b border-emerald-100/50 pb-2">
                                    <span class="text-slate-500 text-sm">Statut</span>
                                    <span class="bg-emerald-100 text-emerald-700 px-2 py-0.5 rounded text-xs font-black uppercase tracking-wider">
                                        {{ $visite->statut }}
                                    </span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-slate-500 text-sm">Paiement</span>
                                    <span class="bg-emerald-100 text-emerald-700 px-2 py-0.5 rounded text-xs font-black uppercase tracking-wider flex items-center gap-1">
                                        <i class="fas fa-check-circle"></i> Encaissé
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tableau Récapitulatif -->
                    <div class="mb-12 overflow-hidden rounded-xl border border-slate-200">
                        <table class="w-full">
                            <thead>
                                <tr class="bg-slate-50 border-b border-slate-200 ">
                                    <th class="text-left text-xs font-black text-slate-500 uppercase tracking-widest whitespace-nowrap px-6 py-5">Description</th>
                                    <th class="text-right text-xs font-black text-slate-500 uppercase tracking-widest whitespace-nowrap px-6 py-5">Montant</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200">
                                <tr class="bg-white ">
                                    <td class="text-slate-700 whitespace-nowrap px-6 py-5">
                                        <div class="font-bold text-slate-900 text-lg">Frais de visite immobilière</div>
                                        <div class="text-sm text-slate-500 mt-1 flex items-center gap-2">
                                            <i class="fas fa-home"></i> {{ $visite->bien->nom_bien }}
                                        </div>
                                    </td>
                                    <td class="text-right font-black text-slate-900 text-lg whitespace-nowrap px-6 py-5">
                                        {{ number_format($visite->montant_paye, 0, ',', ' ') }} <span class="text-sm text-slate-500">{{ $visite->agence->devise }}</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Totaux -->
                    <div class="flex justify-end mb-16">
                        <div class="w-80 bg-slate-50 p-6 rounded-xl border border-slate-100">
                            <div class="space-y-4">
                                <div class="flex justify-between items-center text-slate-500">
                                    <span class="font-medium">Total Facturé</span>
                                    <span class="font-bold text-slate-700">{{ number_format($visite->montant_paye, 0, ',', ' ') }} {{ $visite->agence->devise }}</span>
                                </div>
                                <div class="flex justify-between items-center bg-white p-3 rounded-lg border border-slate-200 shadow-sm mt-4">
                                    <span class="text-emerald-600 font-black uppercase text-sm tracking-wider">Montant Payé</span>
                                    <span class="text-emerald-600 text-xl font-black">{{ number_format($visite->montant_paye, 0, ',', ' ') }} {{ $visite->agence->devise }}</span>
                                </div>
                                <div class="flex justify-between items-center pt-2 border-t border-slate-200">
                                    <span class="font-medium text-slate-500">Reste à payer</span>
                                    <span class="font-black text-lg text-emerald-500">
                                        0 {{ $visite->agence->devise }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Signature & Note -->
                    <div class="flex justify-between items-end pt-10 border-t border-slate-100">
                        <div>
                            <p class="text-slate-400 text-sm italic mb-1">Document généré électroniquement par ONE.Immo.</p>
                            <p class="text-slate-500 font-medium">Merci pour votre confiance !</p>
                        </div>
                        <div class="text-center">
                            <h4 class="text-xs font-black text-slate-400 uppercase mb-4 tracking-widest">Cachet & Signature</h4>
                            <div class="w-48 h-32 border-2 border-dashed border-slate-200 rounded-xl flex items-center justify-center bg-slate-50/50">
                                <span class="text-slate-300 font-medium text-sm rotate-[-10deg]">Emplacement Cachet</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="mt-8 flex justify-between items-center no-print">
                <a href="{{ route('visites.index') }}" class="group flex items-center gap-2 text-slate-500 hover:text-slate-900 font-bold transition-colors">
                    <div class="w-10 h-10 rounded-full bg-white border border-slate-200 flex items-center justify-center group-hover:border-slate-400 group-hover:bg-slate-50 transition-all">
                        <i class="fas fa-arrow-left"></i>
                    </div>
                    Retour aux visites
                </a>
                <button id="printBtn" type="button" class="bg-emerald-600 hover:bg-emerald-700 text-white px-8 py-3.5 rounded-xl font-black shadow-lg shadow-emerald-200 transition-all hover:-translate-y-1 flex items-center gap-3 uppercase text-sm tracking-widest">
                    <i class="fas fa-print text-lg"></i> Imprimer le reçu
                </button>
            </div>
        </div>
    </div>

    <style>
        @media print {
            @page { margin: 0.5cm; }
            
            body * {
                visibility: hidden;
            }
            
            aside, nav, header, footer {
                display: none !important;
            }
            
            html, body, .min-h-screen, .h-screen, .overflow-hidden, .overflow-y-auto {
                height: auto !important;
                min-height: auto !important;
                overflow: visible !important;
                background-color: white !important;
            }

            #printable-receipt, #printable-receipt * {
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

            .no-print, .no-print * {
                display: none !important;
                visibility: hidden !important;
            }
            
            #printable-receipt * {
                color-adjust: exact !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            
            /* Compress spacing for print to fit on one page */
            #printable-receipt .mb-10 { margin-bottom: 1rem !important; }
            #printable-receipt .pb-10 { padding-bottom: 1rem !important; }
            #printable-receipt .mb-12 { margin-bottom: 1.5rem !important; }
            #printable-receipt .mb-16 { margin-bottom: 1.5rem !important; }
            #printable-receipt .pt-10 { padding-top: 1rem !important; }
            #printable-receipt .gap-12 { gap: 1.5rem !important; }
            #printable-receipt .p-12 { padding: 1rem !important; }
            #printable-receipt .py-6 { padding-top: 0.75rem !important; padding-bottom: 0.75rem !important; }
            #printable-receipt .h-32 { height: 6rem !important; }
        }
    </style>

    <script>
        document.getElementById('printBtn').addEventListener('click', function() {
            window.print();
        });
    </script>
</x-app-layout>
