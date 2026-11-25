<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Impression') - {{ Auth::user()->agence->nom_agence }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 min-h-screen">
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-2xl sm:rounded-2xl p-12 relative" id="printable-list">
                <!-- Watermark -->
                <div class="absolute inset-0 flex items-center justify-center opacity-[0.02] pointer-events-none no-print">
                    <i class="fas fa-print text-[25rem]"></i>
                </div>

                <div class="relative z-10">
                    <!-- En-tête -->
                    <div class="flex justify-between items-start border-b-2 border-slate-100 pb-10 mb-10">
                        <div class="flex gap-6 items-center">
                            <div class="flex flex-col items-center gap-1.5">
                                @if(Auth::user()->agence && Auth::user()->agence->logo_url)
                                    <img src="{{ Auth::user()->agence->logo_url }}" alt="Logo {{ Auth::user()->agence->nom }}" class="h-20 w-auto object-contain">
                                @else
                                    <div class="w-20 h-20 bg-gradient-to-br from-blue-600 to-blue-800 rounded-2xl flex items-center justify-center text-white font-black text-3xl shadow-lg shadow-blue-200">
                                        {{ strtoupper(substr(Auth::user()->agence->nom ?? 'AG', 0, 2)) }}
                                    </div>
                                @endif
                                <span class="text-sm font-black text-slate-900 mt-2 text-center w-full block">{{ Auth::user()->agence->nom_agence }}</span>
                            </div>
                            <div>
                                <div class="text-sm text-slate-500 mt-1 space-y-0.5">
                                    @if(Auth::user()->agence->adresse)
                                    <p class="flex items-center gap-2"><i class="fas fa-map-marker-alt w-4 text-center"></i> {{ Auth::user()->agence->adresse }}</p>
                                    @endif
                                    @if(Auth::user()->agence->telephone)
                                    <p class="flex items-center gap-2"><i class="fas fa-phone w-4 text-center"></i> {{ Auth::user()->agence->telephone }}</p>
                                    @endif
                                    @if(Auth::user()->agence->email)
                                    <p class="flex items-center gap-2"><i class="fas fa-envelope w-4 text-center"></i> {{ Auth::user()->agence->email }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="bg-blue-50 text-blue-700 px-4 py-2 rounded-lg inline-block mb-3 border border-blue-100">
                                <h1 class="text-xl font-black uppercase tracking-widest">@yield('title', 'Liste')</h1>
                            </div>
                            <p class="text-slate-500 text-sm font-medium">Date d'impression</p>
                            <p class="text-slate-900 font-bold">{{ now()->format('d/m/Y à H:i') }}</p>
                            <p class="text-slate-500 text-sm font-medium mt-2">Imprimé par</p>
                            <p class="text-slate-900 font-bold">{{ Auth::user()->name }}</p>
                        </div>
                    </div>

                    <!-- Filtres appliqués -->
                    @hasSection('filters')
                    <div class="bg-yellow-50 p-6 rounded-xl border border-yellow-100 mb-10">
                        <h3 class="text-xs font-black text-yellow-600 uppercase mb-4 tracking-widest flex items-center gap-2">
                            <i class="fas fa-filter text-yellow-500"></i> Filtres appliqués
                        </h3>
                        <div class="grid grid-cols-2 gap-x-6 gap-y-2 text-sm">
                            @yield('filters')
                        </div>
                    </div>
                    @endif

                    <!-- Statistiques -->
                    @hasSection('summary')
                    <div class="mb-10">
                        @yield('summary')
                    </div>
                    @endif

                    <!-- Tableau de données -->
                    <div class="mb-12 overflow-hidden rounded-xl border border-slate-200">
                        @yield('table')
                    </div>

                    <!-- Signature & Note -->
                    <div class="flex justify-between items-end pt-10 border-t border-slate-100">
                        <div>
                            <p class="text-slate-400 text-sm italic mb-1">Document généré électroniquement par ONE.Immo.</p>
                            <p class="text-slate-500 font-medium">{{ Auth::user()->agence->nom_agence }}</p>
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
            <div class="mt-8 flex justify-center no-print">
                <button id="printBtn" type="button" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3.5 rounded-xl font-black shadow-lg shadow-blue-200 transition-all hover:-translate-y-1 flex items-center gap-3 uppercase text-sm tracking-widest">
                    <i class="fas fa-print text-lg"></i> Imprimer
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
            
            html, body, .min-h-screen, .h-screen, .overflow-hidden, .overflow-y-auto {
                height: auto !important;
                min-height: auto !important;
                overflow: visible !important;
                background-color: white !important;
            }

            #printable-list, #printable-list * {
                visibility: visible;
            }
            
            #printable-list {
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
            
            #printable-list * {
                color-adjust: exact !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            
            /* Compress spacing for print */
            #printable-list .mb-10 { margin-bottom: 1rem !important; }
            #printable-list .pb-10 { padding-bottom: 1rem !important; }
            #printable-list .mb-12 { margin-bottom: 1.5rem !important; }
            #printable-list .pt-10 { padding-top: 1rem !important; }
            #printable-list .gap-6 { gap: 1rem !important; }
            #printable-list .p-12 { padding: 1rem !important; }
            #printable-list .py-6 { padding-top: 0.75rem !important; padding-bottom: 0.75rem !important; }
            #printable-list .h-32 { height: 6rem !important; }
        }
    </style>

    <script>
        document.getElementById('printBtn').addEventListener('click', function() {
            window.print();
        });
    </script>
</body>
</html>
