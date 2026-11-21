<x-app-layout>
    <div class="py-8 sm:py-12 bg-slate-100/80 min-h-screen text-slate-800 antialiased font-sans">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

            <!-- BARRE D'ACTIONS SUPÉRIEURE (ÉCRAN UNIQUEMENT) -->
            <div class="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white p-4 sm:p-5 rounded-2xl shadow-sm border border-slate-200/80 no-print">
                <div class="flex items-center gap-3">
                    @if(Auth::user()->hasRole('locataire'))
                    <a href="{{ route('factures.mes-factures') }}" class="w-10 h-10 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 flex items-center justify-center transition-all" title="Retour à mes factures">
                        <i class="fas fa-arrow-left text-sm"></i>
                    </a>
                    @else
                    <a href="{{ route('factures.index') }}" class="w-10 h-10 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 flex items-center justify-center transition-all" title="Retour aux factures">
                        <i class="fas fa-arrow-left text-sm"></i>
                    </a>
                    @endif

                    <div>
                        <div class="flex items-center gap-2">
                            <h2 class="text-sm sm:text-base font-black text-slate-900 uppercase tracking-tight">Quittance Officielle & Reçu d'Encaissement</h2>
                            <span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase tracking-wider bg-emerald-100 text-emerald-800 border border-emerald-200">
                                Encaissé
                            </span>
                        </div>
                        <p class="text-[11px] text-slate-500 font-medium">Réf. Reçu : <strong class="text-slate-800">REC-{{ $facture->numero_facture }}-{{ $dernierPaiement->id }}</strong></p>
                    </div>
                </div>

                <div class="flex items-center gap-2.5 w-full sm:w-auto justify-end">
                    <button type="button" onclick="window.print()" class="w-full sm:w-auto px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-black uppercase tracking-wider shadow-md shadow-blue-500/20 transition-all flex items-center justify-center gap-2 cursor-pointer">
                        <i class="fas fa-print text-sm"></i>
                        <span>Imprimer / Exporter PDF</span>
                    </button>
                </div>
            </div>

            @php
                $devise = $facture->agence->devise ?? 'XOF';
                $montantVerse = (float)($dernierPaiement->montant_verse ?? 0);
                $resteAPayer = max(0, (float)($facture->montant_total_du ?? 0) - (float)($facture->montant_deja_paye ?? 0));
                $securityCode = 'SEC-REC-' . strtoupper(substr(md5($facture->id . $dernierPaiement->id . $facture->created_at), 0, 8));

                // Conversion en toutes lettres
                $montantEnLettres = '';
                if (class_exists('\NumberFormatter')) {
                    try {
                        $formatter = new \NumberFormatter('fr', \NumberFormatter::SPELLOUT);
                        $montantEnLettres = ucfirst($formatter->format($montantVerse));
                    } catch (\Exception $e) {
                        $montantEnLettres = '';
                    }
                }
            @endphp

            <!-- FEUILLE DE QUITTANCE NOTARIÉE CORPORATE -->
            <div class="bg-white shadow-2xl rounded-2xl p-6 sm:p-12 relative border border-slate-200/90 overflow-hidden" id="printable-receipt">

                <!-- FILIGRANE DE SÉCURITÉ INSTITUTIONNEL (ARRIÈRE-PLAN) -->
                <div class="absolute inset-0 flex items-center justify-center pointer-events-none select-none opacity-[0.02] no-print">
                    <div class="text-center">
                        <i class="fas fa-certificate text-[28rem] text-slate-900"></i>
                    </div>
                </div>

                <div class="relative z-10 space-y-8">

                    <!-- 1. EN-TÊTE CORPORATE & MENTIONS LÉGALES -->
                    <div class="border-b-2 border-slate-900/90 pb-6">
                        <div class="flex flex-col sm:flex-row justify-between items-start gap-6">
                            
                            <!-- Logo & Informations Cabinet / Agence -->
                            <div class="flex items-start gap-4 max-w-md">
                                @if($facture->agence->logo)
                                    <img src="{{ asset('storage/' . $facture->agence->logo) }}" alt="Logo {{ $facture->agence->nom }}" class="h-16 sm:h-20 w-auto object-contain shrink-0">
                                @else
                                    <div class="w-16 h-16 sm:w-20 sm:h-20 bg-gradient-to-br from-slate-900 to-blue-900 rounded-xl flex items-center justify-center text-white font-black text-2xl shrink-0 shadow-md">
                                        {{ strtoupper(substr($facture->agence->nom, 0, 2)) }}
                                    </div>
                                @endif

                                <div class="space-y-1">
                                    <h1 class="text-base sm:text-lg font-black text-slate-900 uppercase tracking-tight leading-tight">
                                        {{ $facture->agence->nom }}
                                    </h1>
                                    <p class="text-[10px] font-bold uppercase tracking-widest text-blue-700">Administration de Biens & Gestion Immobilière</p>
                                    <div class="text-[11px] text-slate-600 space-y-0.5 leading-snug">
                                        <p><i class="fas fa-map-marker-alt text-slate-400 w-3 text-center mr-1"></i> {{ $facture->agence->adresse }}</p>
                                        <p><i class="fas fa-phone-alt text-slate-400 w-3 text-center mr-1"></i> {{ $facture->agence->telephone }} 
                                            @if($facture->agence->email) <span class="mx-1 text-slate-300">•</span> <i class="fas fa-envelope text-slate-400 w-3 text-center mr-1"></i> {{ $facture->agence->email }} @endif
                                        </p>
                                        @if($facture->agence->numero_agrement || $facture->agence->compte_contribuable)
                                        <p class="text-[10px] text-slate-500 pt-0.5">
                                            @if($facture->agence->numero_agrement) <strong>Agrément :</strong> {{ $facture->agence->numero_agrement }} @endif
                                            @if($facture->agence->compte_contribuable) <span class="mx-1 text-slate-300">•</span> <strong>C.C :</strong> {{ $facture->agence->compte_contribuable }} @endif
                                        </p>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Cartouche d'Identification Officielle du Reçu -->
                            <div class="sm:text-right shrink-0 w-full sm:w-auto bg-slate-50/80 sm:bg-transparent p-4 sm:p-0 rounded-xl border sm:border-0 border-slate-200">
                                <div class="inline-block px-3 py-1 bg-emerald-800 text-white rounded-md text-[10px] font-black uppercase tracking-widest mb-2 shadow-sm">
                                    Quittance Libératoire
                                </div>
                                <div class="space-y-1">
                                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Reçu N°</p>
                                    <p class="text-xl sm:text-2xl font-black text-blue-900 tracking-tight font-mono">REC-{{ $facture->numero_facture }}-{{ $dernierPaiement->id }}</p>
                                    <p class="text-[11px] text-slate-500 font-medium">Date d'encaissement : <strong class="text-slate-800">{{ \Carbon\Carbon::parse($dernierPaiement->date_paiement)->translatedFormat('d F Y') }}</strong></p>
                                    <p class="text-[9px] font-mono font-bold text-slate-400 tracking-wider">CODE : {{ $securityCode }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Grand Titre Solennel -->
                        <div class="text-center mt-6 pt-4 border-t border-slate-200/80">
                            <h2 class="text-lg sm:text-xl font-black text-slate-900 uppercase tracking-[0.25em]">
                                Quittance Officielle de Loyer
                            </h2>
                            <p class="text-[11px] text-slate-500 font-bold uppercase tracking-widest mt-0.5">
                                Reçu d'Encaissement au titre du bail n° {{ $facture->contrat->numero_contrat ?? 'CB-'.str_pad($facture->contrat->id, 6, '0', STR_PAD_LEFT) }}
                            </p>
                        </div>
                    </div>

                    <!-- 2. PARTIES & INFORMATIONS DÉTAILLÉES -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        
                        <!-- Client / Locataire Débiteur -->
                        <div class="p-4 sm:p-5 rounded-xl border border-slate-200 bg-slate-50/60 flex flex-col justify-between">
                            <div>
                                <div class="flex items-center justify-between pb-2 mb-2 border-b border-slate-200">
                                    <span class="text-[10px] font-black text-blue-800 uppercase tracking-widest flex items-center gap-1.5">
                                        <i class="fas fa-user-check"></i> Reçu de M. / Mme
                                    </span>
                                    <span class="text-[9px] font-bold text-slate-400 uppercase">Preneur (Locataire)</span>
                                </div>
                                <h4 class="text-sm font-black text-slate-900 uppercase tracking-tight">
                                    {{ $facture->contrat->locataire->civilite ?? '' }} {{ $facture->contrat->locataire->user->name }}
                                </h4>
                                <div class="mt-2 space-y-1 text-xs text-slate-600">
                                    <p><span class="text-slate-400 font-medium">Email :</span> {{ $facture->contrat->locataire->user->email }}</p>
                                    @if($facture->contrat->locataire->user->telephone)
                                    <p><span class="text-slate-400 font-medium">Téléphone :</span> {{ $facture->contrat->locataire->user->telephone }}</p>
                                    @endif
                                    @if($facture->contrat->locataire->numero_piece_identite)
                                    <p><span class="text-slate-400 font-medium">Pièce d'identité :</span> N° {{ $facture->contrat->locataire->numero_piece_identite }}</p>
                                    @endif
                                    @if($facture->contrat->locataire->profession)
                                    <p><span class="text-slate-400 font-medium">Profession :</span> {{ $facture->contrat->locataire->profession }}</p>
                                    @endif
                                </div>
                            </div>
                            <div class="mt-4 pt-3 border-t border-dashed border-slate-200 text-xs">
                                <span class="text-slate-400 font-medium text-[10px] uppercase block">Désignation du Bien Loué</span>
                                <strong class="text-slate-900 font-black">{{ $facture->contrat->bien->nom_bien }}</strong> 
                                <span class="text-slate-500 font-medium">({{ $facture->contrat->bien->type_bien }} - {{ $facture->contrat->bien->adresse }})</span>
                            </div>
                        </div>

                        <!-- Modalités de Paiement & Transaction -->
                        <div class="p-4 sm:p-5 rounded-xl border border-blue-200 bg-blue-50/30 flex flex-col justify-between">
                            <div>
                                <div class="flex items-center justify-between pb-2 mb-2 border-b border-blue-200">
                                    <span class="text-[10px] font-black text-blue-800 uppercase tracking-widest flex items-center gap-1.5">
                                        <i class="fas fa-credit-card"></i> Détails du Règlement
                                    </span>
                                    <span class="text-[9px] font-bold text-emerald-700 uppercase bg-emerald-100 px-2 py-0.5 rounded font-black">
                                        Règlement Confirmé
                                    </span>
                                </div>
                                <dl class="space-y-2 text-xs">
                                    <div class="flex justify-between items-center border-b border-blue-100/60 pb-1.5">
                                        <dt class="text-slate-400 font-medium">Mode de règlement :</dt>
                                        <dd class="font-bold text-slate-900 capitalize">{{ $dernierPaiement->mode_paiement }}</dd>
                                    </div>
                                    @if($dernierPaiement->reference_transaction)
                                    <div class="flex justify-between items-center border-b border-blue-100/60 pb-1.5">
                                        <dt class="text-slate-400 font-medium">Réf. transaction :</dt>
                                        <dd class="font-bold font-mono text-blue-900">{{ $dernierPaiement->reference_transaction }}</dd>
                                    </div>
                                    @endif
                                    <div class="flex justify-between items-center border-b border-blue-100/60 pb-1.5">
                                        <dt class="text-slate-400 font-medium">Période concernée :</dt>
                                        <dd class="font-bold text-slate-900">{{ $facture->mois_facture }}</dd>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <dt class="text-slate-400 font-medium">Nature de l'échéance :</dt>
                                        <dd class="font-bold text-slate-800">{{ $facture->type_facture }}</dd>
                                    </div>
                                </dl>
                            </div>
                            <div class="mt-4 pt-3 border-t border-dashed border-blue-200 text-[10px] text-blue-800 font-semibold flex items-center justify-between">
                                <span>Facture d'origine : <strong>#{{ $facture->numero_facture }}</strong></span>
                                <span class="text-emerald-700 font-black"><i class="fas fa-check-circle mr-1"></i> Encaissé avec succès</span>
                            </div>
                        </div>
                    </div>

                    <!-- 3. TABLEAU FINANCIER D'ENCAISSEMENT -->
                    <div class="rounded-xl border-2 border-slate-900/80 overflow-hidden bg-white">
                        <div class="bg-slate-900 text-white px-5 py-3 flex items-center justify-between flex-wrap gap-2">
                            <h3 class="text-xs font-black uppercase tracking-widest flex items-center gap-2">
                                <i class="fas fa-list-alt text-blue-400"></i>
                                <span>Ventilation Financière de la Quittance</span>
                            </h3>
                            <span class="text-[10px] font-black uppercase tracking-widest text-slate-300">
                                Monnaie de compte : {{ $devise }}
                            </span>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-xs">
                                <thead>
                                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-500 font-black uppercase text-[10px] tracking-wider">
                                        <th class="text-left px-5 py-3">Description de la Prestation</th>
                                        <th class="text-center px-4 py-3">Période</th>
                                        <th class="text-right px-4 py-3">Total Facturé</th>
                                        <th class="text-right px-5 py-3">Montant Encaissé</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="hover:bg-slate-50/50">
                                        <td class="px-5 py-4">
                                            <p class="font-black text-slate-900 text-sm">{{ $facture->type_facture }}</p>
                                            <p class="text-[10px] text-slate-500">Paiement du loyer et charges d'occupation des lieux loués</p>
                                        </td>
                                        <td class="px-4 py-4 text-center font-bold text-slate-700">{{ $facture->mois_facture }}</td>
                                        <td class="px-4 py-4 text-right font-bold text-slate-800">
                                            {{ number_format($facture->montant_total_du, 0, ',', ' ') }} {{ $devise }}
                                        </td>
                                        <td class="px-5 py-4 text-right font-black text-emerald-700 text-base">
                                            {{ number_format($montantVerse, 0, ',', ' ') }} {{ $devise }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- TOTAL REÇU & DÉTAILS DE QUITTANCE -->
                        <div class="bg-gradient-to-r from-slate-900 via-blue-950 to-slate-900 text-white p-5 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                            <div>
                                <p class="text-[10px] font-black uppercase tracking-[0.2em] text-emerald-400">Montant Total Encaissé sur ce Reçu</p>
                                <p class="text-2xl sm:text-3xl font-black text-white tracking-tight mt-0.5">
                                    {{ number_format($montantVerse, 0, ',', ' ') }} <span class="text-sm font-bold text-emerald-400">{{ $devise }}</span>
                                </p>
                                @if(!empty($montantEnLettres))
                                <p class="text-[11px] text-slate-300 font-medium italic mt-1">
                                    Soit : <strong>{{ $montantEnLettres }} {{ $devise === 'XOF' ? 'Francs CFA' : $devise }}</strong>
                                </p>
                                @endif
                            </div>

                            <div class="sm:text-right bg-white/10 backdrop-blur-sm px-4 py-2.5 rounded-xl border border-white/10">
                                <p class="text-[10px] font-black uppercase tracking-wider text-slate-300">Situation du Compte</p>
                                <div class="mt-1 flex items-center sm:justify-end gap-3 text-xs">
                                    <div>
                                        <span class="text-slate-400 text-[10px] block">Déjà réglé :</span>
                                        <strong class="text-white">{{ number_format($facture->montant_deja_paye, 0, ',', ' ') }} {{ $devise }}</strong>
                                    </div>
                                    <div class="border-l border-white/20 pl-3">
                                        <span class="text-slate-400 text-[10px] block">Solde restant :</span>
                                        @if($resteAPayer <= 0)
                                            <strong class="text-emerald-400 font-black">0 {{ $devise }} (Soldé)</strong>
                                        @else
                                            <strong class="text-amber-400 font-black">{{ number_format($resteAPayer, 0, ',', ' ') }} {{ $devise }}</strong>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 4. SIGNATURE, CACHET ET NOTE LÉGALE DE QUITTANCE -->
                    <div class="pt-6 border-t-2 border-slate-900 space-y-6 page-break-inside-avoid">
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-end gap-6">
                            
                            <!-- Mentions et quittance expresse -->
                            <div class="space-y-2 max-w-md">
                                <p class="text-xs font-bold text-slate-800">
                                    Fait à <strong>{{ $facture->agence->adresse ? explode(',', $facture->agence->adresse)[0] : 'Abidjan' }}</strong>, 
                                    le <strong>{{ \Carbon\Carbon::parse($dernierPaiement->date_paiement)->translatedFormat('d F Y') }}</strong>.
                                </p>
                                <p class="text-[11px] text-slate-600 italic leading-relaxed">
                                    « Le paiement de la présente quittance n'emporte pas présomption de paiement des termes antérieurs si le Preneur ne peut en justifier par quittance régulière. Cette quittance annule tout reçu provisoire antérieur. »
                                </p>
                                <p class="text-[10px] font-bold text-emerald-800 uppercase tracking-wider">
                                    <i class="fas fa-check-double mr-1"></i> Quittance délivrée sous réserve de parfait encaissement
                                </p>
                            </div>

                            <!-- Cadre Cachet & Signature de l'Agence -->
                            <div class="border-2 border-slate-300 rounded-2xl p-5 bg-slate-50/50 flex flex-col justify-between h-44 w-full sm:w-64 relative text-center shrink-0">
                                <div>
                                    <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Pour le Bailleur & Agence</p>
                                    <p class="text-xs font-black text-slate-900 uppercase mt-0.5">{{ $facture->agence->nom }}</p>
                                </div>

                                <div class="my-auto pointer-events-none opacity-40">
                                    <div class="inline-block border border-dashed border-blue-500 rounded-xl px-4 py-2">
                                        <span class="text-[10px] font-bold text-blue-800 uppercase tracking-widest block">Cachet & Signature</span>
                                        <span class="text-[9px] text-slate-500">Service Comptabilité</span>
                                    </div>
                                </div>

                                <div class="pt-2 border-t border-slate-200">
                                    <p class="text-[10px] font-bold text-slate-600">Signature autorisée</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 5. PIED DE PAGE SÉCURISÉ & MENTIONS LÉGALES -->
                    <div class="pt-4 border-t border-slate-200 text-center text-[10px] text-slate-500 space-y-1">
                        <p class="font-bold text-slate-600">
                            {{ $facture->agence->nom }} • Siège Social : {{ $facture->agence->adresse }} 
                            @if($facture->agence->telephone) • Tél : {{ $facture->agence->telephone }} @endif
                            @if($facture->agence->numero_agrement) • Agrément N° {{ $facture->agence->numero_agrement }} @endif
                        </p>
                        <p class="text-[9px] text-slate-400 italic">
                            Document officiel infalsifiable généré électroniquement par ONE.Immo • {{ $securityCode }} • Tout faux est passible de poursuites judiciaires.
                        </p>
                    </div>

                </div>
            </div>

            <!-- BOUTON D'ACTION INFÉRIEUR (ÉCRAN UNIQUEMENT) -->
            <div class="mt-8 flex justify-between items-center no-print">
                <span class="text-xs text-slate-400 font-medium">
                    <i class="fas fa-shield-alt text-emerald-600 mr-1"></i> Document certifié conforme aux normes d'administration de biens
                </span>

                <button type="button" onclick="window.print()" class="px-8 py-3.5 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-black uppercase tracking-widest shadow-xl shadow-slate-900/20 transition-all flex items-center gap-2.5 cursor-pointer">
                    <i class="fas fa-print text-sm text-blue-400"></i>
                    <span>Imprimer la Quittance Officielle</span>
                </button>
            </div>

        </div>
    </div>

    <!-- STYLES CSS D'IMPRESSION A4 HAUTE FIDÉLITÉ -->
    <style>
        @media print {
            @page {
                size: A4 portrait;
                margin: 8mm 10mm;
            }

            body {
                background: white !important;
                color: #0f172a !important;
                font-size: 11pt !important;
                line-height: 1.3 !important;
            }

            header,
            nav,
            aside,
            footer,
            .no-print,
            [class*="sidebar"],
            [class*="navigation"] {
                display: none !important;
                visibility: hidden !important;
            }

            html,
            body,
            .min-h-screen,
            .py-12,
            .py-8 {
                background: white !important;
                padding: 0 !important;
                margin: 0 !important;
                height: auto !important;
                min-height: auto !important;
                overflow: visible !important;
            }

            .max-w-4xl {
                max-width: 100% !important;
                width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
            }

            #printable-receipt {
                box-shadow: none !important;
                border: none !important;
                padding: 0 !important;
                margin: 0 !important;
                border-radius: 0 !important;
                background: white !important;
            }

            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            .page-break-inside-avoid {
                page-break-inside: avoid !important;
                break-inside: avoid !important;
            }
        }
    </style>
</x-app-layout>