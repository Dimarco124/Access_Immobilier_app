<x-app-layout>
    <div class="py-6 sm:py-10 bg-slate-100 min-h-screen text-slate-800 antialiased font-sans">
        <div class="max-w-4xl mx-auto px-3 sm:px-6 lg:px-8">

            <!-- BARRE D'ACTIONS SUPÉRIEURE (ÉCRAN UNIQUEMENT) -->
            <div class="mb-5 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 bg-white p-4 rounded-xl shadow-sm border border-slate-200 no-print">
                <div class="flex items-center gap-3">
                    @if(Auth::user()->hasRole('locataire'))
                    <a href="{{ route('contrats.mes-contrats') }}" class="w-9 h-9 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 flex items-center justify-center transition-all" title="Retour aux contrats">
                        <i class="fas fa-arrow-left text-xs"></i>
                    </a>
                    @elseif(Auth::user()->hasRole('proprietaire'))
                    <a href="{{ route('dashboard') }}" class="w-9 h-9 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 flex items-center justify-center transition-all" title="Retour au tableau de bord">
                        <i class="fas fa-arrow-left text-xs"></i>
                    </a>
                    @else
                    <a href="{{ route('contrats.show', $contrat) }}" class="w-9 h-9 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 flex items-center justify-center transition-all" title="Retour au dossier contrat">
                        <i class="fas fa-arrow-left text-xs"></i>
                    </a>
                    @endif

                    <div>
                        <div class="flex items-center gap-2">
                            <h2 class="text-sm sm:text-base font-black text-slate-900 uppercase tracking-tight">Contrat de Bail Officiel</h2>
                            <span class="px-2 py-0.5 rounded text-[9px] font-black uppercase tracking-wider {{ $contrat->statut === 'Actif' ? 'bg-slate-900 text-white' : 'bg-slate-200 text-slate-700' }}">
                                {{ $contrat->statut }}
                            </span>
                        </div>
                        <p class="text-[11px] text-slate-500 font-medium">Réf. officielle : <strong class="text-slate-900 font-mono">{{ $contrat->numero_contrat ?? 'CB-'.str_pad($contrat->id, 6, '0', STR_PAD_LEFT) }}</strong></p>
                    </div>
                </div>

                <div class="flex items-center gap-2.5 w-full sm:w-auto justify-end">
                    <button type="button" onclick="window.print()" class="w-full sm:w-auto px-5 py-2.5 bg-slate-900 hover:bg-black text-white rounded-lg text-xs font-black uppercase tracking-wider shadow-sm transition-all flex items-center justify-center gap-2 cursor-pointer">
                        <i class="fas fa-print text-xs"></i>
                        <span>Imprimer / Exporter PDF</span>
                    </button>
                </div>
            </div>

            @php
                $loyer = (float)($contrat->montant_loyer_actuel ?? 0);
                $caution = (float)($contrat->montant_caution ?? 0);
                $avanceMois = (int)($contrat->nombre_mois_avance ?? 0);
                $loyerAvanceTotal = $loyer * $avanceMois;
                $fraisDossier = (float)($contrat->frais_dossier ?? 0);
                $factureInit = $contrat->factures->first(fn($f) => $f->type_facture === 'Initial');
                $tauxTVA = (float)($contrat->agence->taux_tva ?? 0);
                $montantTVA = $factureInit ? (float)$factureInit->montant_tva : ((($loyerAvanceTotal + $fraisDossier) * $tauxTVA) / 100);
                $totalSign = $factureInit ? (float)$factureInit->montant_total_du : ($caution + $loyerAvanceTotal + $fraisDossier + $montantTVA);
                $devise = $contrat->agence->devise ?? 'XOF';
                $numContrat = $contrat->numero_contrat ?? ('CB-' . str_pad($contrat->id, 6, '0', STR_PAD_LEFT));
                $securityCode = 'SEC-' . strtoupper(substr(md5($contrat->id . $numContrat . $contrat->created_at), 0, 8));

                // Conversion en toutes lettres
                $montantEnLettres = '';
                if (class_exists('\NumberFormatter')) {
                    try {
                        $formatter = new \NumberFormatter('fr', \NumberFormatter::SPELLOUT);
                        $montantEnLettres = ucfirst($formatter->format($totalSign));
                    } catch (\Exception $e) {
                        $montantEnLettres = '';
                    }
                }
            @endphp

            <!-- FEUILLE DE CONTRAT NOTARIÉ CORPORATE -->
            <div class="bg-white shadow-xl rounded-xl p-6 sm:p-10 relative border border-slate-300 text-slate-900" id="printable-contract">

                <!-- ========================================== -->
                <!-- PAGE 1 : IDENTIFICATION, LIEUX & FINANCES -->
                <!-- ========================================== -->
                <div class="contract-page contract-page-1">

                    <!-- 1. EN-TÊTE CORPORATE & MENTIONS LÉGALES -->
                    <div class="border-b-2 border-slate-900 pb-4">
                        <div class="flex flex-row justify-between items-start gap-4">
                            
                            <!-- Logo & Informations Cabinet / Agence -->
                            <div class="flex items-start gap-3.5 max-w-md">
                                @if($contrat->agence && $contrat->agence->logo_url)
                                    <img src="{{ $contrat->agence->logo_url }}" alt="Logo {{ $contrat->agence->nom }}" class="h-14 sm:h-16 max-w-[130px] w-auto object-contain shrink-0">
                                @else
                                    <div class="w-14 h-14 bg-slate-900 rounded-lg flex items-center justify-center text-white font-black text-xl shrink-0">
                                        {{ strtoupper(substr($contrat->agence->nom, 0, 2)) }}
                                    </div>
                                @endif

                                <div class="space-y-0.5">
                                    <h1 class="text-sm sm:text-base font-black text-slate-900 uppercase tracking-tight leading-tight">
                                        {{ $contrat->agence->nom }}
                                    </h1>
                                    <p class="text-[9px] sm:text-[10px] font-bold uppercase tracking-wider text-slate-600">Cabinet de Gestion Immobilière & Administration de Biens</p>
                                    <div class="text-[10px] text-slate-600 space-y-0.5 leading-tight pt-0.5">
                                        <p><i class="fas fa-map-marker-alt text-slate-400 w-3 text-center mr-0.5"></i> {{ $contrat->agence->adresse }}</p>
                                        <p><i class="fas fa-phone-alt text-slate-400 w-3 text-center mr-0.5"></i> {{ $contrat->agence->telephone }} 
                                            @if($contrat->agence->email) <span class="mx-1 text-slate-300">•</span> <i class="fas fa-envelope text-slate-400 w-3 text-center mr-0.5"></i> {{ $contrat->agence->email }} @endif
                                        </p>
                                        @if($contrat->agence->numero_agrement || $contrat->agence->compte_contribuable)
                                        <p class="text-[9px] text-slate-500">
                                            @if($contrat->agence->numero_agrement) <strong>Agrément :</strong> {{ $contrat->agence->numero_agrement }} @endif
                                            @if($contrat->agence->compte_contribuable) <span class="mx-1 text-slate-300">•</span> <strong>C.C :</strong> {{ $contrat->agence->compte_contribuable }} @endif
                                        </p>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Cartouche d'Identification Officielle du Contrat -->
                            <div class="text-right shrink-0">
                                <div class="inline-block px-2.5 py-0.5 bg-slate-900 text-white rounded text-[9px] font-black uppercase tracking-widest mb-1.5">
                                    Acte Sous Seing Privé
                                </div>
                                <div class="space-y-0.5">
                                    <p class="text-[9px] font-black text-slate-500 uppercase tracking-wider">N° de Contrat de Bail</p>
                                    <p class="text-lg sm:text-xl font-black text-slate-900 tracking-tight font-mono">{{ $numContrat }}</p>
                                    <p class="text-[10px] text-slate-600 font-medium">Date d'effet : <strong class="text-slate-900">{{ \Carbon\Carbon::parse($contrat->date_debut)->translatedFormat('d F Y') }}</strong></p>
                                    <p class="text-[8px] font-mono font-bold text-slate-400 tracking-wider">CERTIF : {{ $securityCode }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Grand Titre Solennel -->
                        <div class="text-center mt-4 pt-3 border-t border-slate-200">
                            <h2 class="text-base sm:text-lg font-black text-slate-900 uppercase tracking-[0.2em]">
                                Contrat de Bail à Usage d'Habitation
                            </h2>
                            <p class="text-[9px] sm:text-[10px] text-slate-500 font-bold uppercase tracking-wider mt-0.5">
                                Régie par les lois et règlements en vigueur • Valant Quittance de Règlement Initial
                            </p>
                        </div>
                    </div>

                    <!-- 2. PRÉAMBULE & DÉSIGNATION DES PARTIES -->
                    <div class="mt-4">
                        <div class="flex items-center justify-between pb-1 mb-2 border-b border-slate-300">
                            <h3 class="text-[11px] font-black text-slate-900 uppercase tracking-widest">Entre les Soussignés</h3>
                            <span class="text-[9px] text-slate-400 uppercase font-semibold">Désignation contractuelle des parties</span>
                        </div>

                        <div class="contract-grid-2 grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                            <!-- Bloc Bailleur / Propriétaire & Mandataire -->
                            <div class="p-3.5 rounded-lg border border-slate-400 bg-slate-50/30 flex flex-col justify-between">
                                <div>
                                    <div class="flex items-center justify-between pb-1.5 mb-1.5 border-b border-slate-200">
                                        <span class="text-[10px] font-black text-slate-900 uppercase tracking-wider flex items-center gap-1.5">
                                            <i class="fas fa-user-tie text-slate-600"></i> 1. Le Bailleur
                                        </span>
                                        <span class="text-[9px] font-bold text-slate-500 uppercase">Propriétaire</span>
                                    </div>
                                    <h4 class="text-xs font-black text-slate-900 uppercase tracking-tight">
                                        {{ $contrat->bien->proprietaire->civilite ?? '' }} {{ $contrat->bien->proprietaire->user->name ?? 'Propriétaire Non Renseigné' }}
                                    </h4>
                                    <div class="mt-1.5 space-y-0.5 text-[11px] text-slate-700">
                                        <p><span class="text-slate-500 font-medium">Adresse :</span> {{ $contrat->bien->proprietaire->adresse_residence ?? $contrat->bien->proprietaire->ville ?? $contrat->agence->adresse }}</p>
                                        @if($contrat->bien->proprietaire->numero_piece_identite)
                                        <p><span class="text-slate-500 font-medium">Pièce d'identité :</span> N° {{ $contrat->bien->proprietaire->numero_piece_identite }}</p>
                                        @endif
                                        @if($contrat->bien->proprietaire->user->telephone)
                                        <p><span class="text-slate-500 font-medium">Contact :</span> {{ $contrat->bien->proprietaire->user->telephone }}</p>
                                        @endif
                                    </div>
                                </div>
                                <div class="mt-2.5 pt-2 border-t border-slate-200 text-[10px] text-slate-600 italic">
                                    Représenté légalement par son Mandataire exclusif de gestion : <strong>{{ $contrat->agence->nom }}</strong>.
                                </div>
                            </div>

                            <!-- Bloc Preneur / Locataire -->
                            <div class="p-3.5 rounded-lg border border-slate-400 bg-slate-50/30 flex flex-col justify-between">
                                <div>
                                    <div class="flex items-center justify-between pb-1.5 mb-1.5 border-b border-slate-200">
                                        <span class="text-[10px] font-black text-slate-900 uppercase tracking-wider flex items-center gap-1.5">
                                            <i class="fas fa-user text-slate-600"></i> 2. Le Preneur
                                        </span>
                                        <span class="text-[9px] font-bold text-slate-500 uppercase">Locataire Bénéficiaire</span>
                                    </div>
                                    <h4 class="text-xs font-black text-slate-900 uppercase tracking-tight">
                                        {{ $contrat->locataire->civilite ?? '' }} {{ $contrat->locataire->user->name }}
                                    </h4>
                                    <div class="mt-1.5 space-y-0.5 text-[11px] text-slate-700">
                                        <p><span class="text-slate-500 font-medium">Profession :</span> {{ $contrat->locataire->profession ?? 'Non spécifiée' }}</p>
                                        <p><span class="text-slate-500 font-medium">Pièce d'identité :</span> {{ $contrat->locataire->numero_piece_identite ? 'N° ' . $contrat->locataire->numero_piece_identite : 'Dossier validé' }}</p>
                                        <p><span class="text-slate-500 font-medium">Téléphone :</span> {{ $contrat->locataire->user->telephone ?? 'Non renseigné' }}</p>
                                        <p><span class="text-slate-500 font-medium">Email :</span> {{ $contrat->locataire->user->email }}</p>
                                        @if($contrat->locataire->contact_urgence)
                                        <p><span class="text-slate-500 font-medium">Contact d'urgence :</span> {{ $contrat->locataire->contact_urgence }}</p>
                                        @endif
                                    </div>
                                </div>
                                <div class="mt-2.5 pt-2 border-t border-slate-200 text-[10px] text-slate-600 italic">
                                    Preneur principal engagé solidairement aux clauses du présent bail.
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 3. ARTICLE I & II : DÉSIGNATION DU BIEN & DURÉE -->
                    <div class="contract-grid-2 grid grid-cols-1 sm:grid-cols-2 gap-3.5 mt-3.5">
                        
                        <!-- Article I : Désignation des Lieux -->
                        <div class="p-3.5 rounded-lg border border-slate-300 bg-white">
                            <h3 class="text-[11px] font-black text-slate-900 uppercase tracking-wider mb-2 pb-1 border-b border-slate-100 flex items-center justify-between">
                                <span>ART. 1 — Désignation des Lieux</span>
                                <span class="text-[9px] text-slate-400 font-semibold uppercase">Description</span>
                            </h3>
                            <dl class="space-y-1.5 text-[11px]">
                                <div class="flex justify-between items-start border-b border-slate-100 pb-1">
                                    <dt class="text-slate-500 font-medium">Propriété :</dt>
                                    <dd class="font-bold text-slate-900 text-right">
                                        {{ $contrat->bien->familleDeBien?->nom_famille ?? $contrat->bien->nom_bien }}
                                        @if($contrat->bien->familleDeBien)
                                        <span class="block text-[8px] font-semibold text-slate-400">
                                            ({{ $contrat->bien->familleDeBien->nature === 'individuelle' ? 'Bien individuel' : 'Immeuble / Collectif' }})
                                        </span>
                                        @endif
                                    </dd>
                                </div>
                                <div class="flex justify-between items-center border-b border-slate-100 pb-1">
                                    <dt class="text-slate-500 font-medium">Unité / Lot :</dt>
                                    <dd class="font-black text-slate-900 font-mono">{{ $contrat->bien->nom_bien }}</dd>
                                </div>
                                <div class="flex justify-between items-center border-b border-slate-100 pb-1">
                                    <dt class="text-slate-500 font-medium">Typologie :</dt>
                                    <dd class="font-bold text-slate-800 uppercase">{{ $contrat->bien->type_bien }}</dd>
                                </div>
                                <div class="flex justify-between items-start border-b border-slate-100 pb-1">
                                    <dt class="text-slate-500 font-medium">Adresse exacte :</dt>
                                    <dd class="font-semibold text-slate-700 text-right max-w-[65%]">{{ $contrat->bien->adresse }}</dd>
                                </div>
                                <div class="flex justify-between items-center pt-0.5">
                                    <dt class="text-slate-500 font-medium">Destination :</dt>
                                    <dd class="font-bold text-slate-900 uppercase text-[10px]">Habitation bourgeoise exclusive</dd>
                                </div>
                            </dl>
                        </div>

                        <!-- Article II : Durée & Calendrier -->
                        <div class="p-3.5 rounded-lg border border-slate-300 bg-white">
                            <h3 class="text-[11px] font-black text-slate-900 uppercase tracking-wider mb-2 pb-1 border-b border-slate-100 flex items-center justify-between">
                                <span>ART. 2 — Durée & Calendrier</span>
                                <span class="text-[9px] text-slate-400 font-semibold uppercase">Période</span>
                            </h3>
                            <dl class="space-y-1.5 text-[11px]">
                                <div class="flex justify-between items-center border-b border-slate-100 pb-1">
                                    <dt class="text-slate-500 font-medium">Prise d'effet :</dt>
                                    <dd class="font-bold text-slate-900">{{ \Carbon\Carbon::parse($contrat->date_debut)->translatedFormat('d F Y') }}</dd>
                                </div>
                                <div class="flex justify-between items-center border-b border-slate-100 pb-1">
                                    <dt class="text-slate-500 font-medium">Échéance du bail :</dt>
                                    <dd class="font-bold text-slate-900">
                                        @if($contrat->date_fin)
                                            {{ \Carbon\Carbon::parse($contrat->date_fin)->translatedFormat('d F Y') }}
                                        @else
                                            <span class="text-slate-800 font-bold">Durée indéterminée</span>
                                        @endif
                                    </dd>
                                </div>
                                <div class="flex justify-between items-center border-b border-slate-100 pb-1">
                                    <dt class="text-slate-500 font-medium">Durée contractuelle :</dt>
                                    <dd class="font-bold text-slate-900">
                                        @if($contrat->date_fin)
                                            {{ \Carbon\Carbon::parse($contrat->date_debut)->diffInMonths($contrat->date_fin) }} mois fermes
                                        @else
                                            Tacite reconduction mensuelle
                                        @endif
                                    </dd>
                                </div>
                                <div class="flex justify-between items-center border-b border-slate-100 pb-1">
                                    <dt class="text-slate-500 font-medium">Préavis de congé :</dt>
                                    <dd class="font-semibold text-slate-700">Trois (3) mois par lettre notifiée</dd>
                                </div>
                                <div class="flex justify-between items-center pt-0.5">
                                    <dt class="text-slate-500 font-medium">Reconduction :</dt>
                                    <dd class="font-semibold text-slate-700 text-[10px]">Tacite reconduction de plein droit</dd>
                                </div>
                            </dl>
                        </div>
                    </div>

                    <!-- 4. ARTICLE III : CONDITIONS FINANCIÈRES & QUITTANCE / REÇU DES RÈGLEMENTS INITIAUX -->
                    <div class="rounded-lg border-2 border-slate-900 overflow-hidden bg-white mt-3.5 page-break-inside-avoid">
                        <div class="bg-slate-900 text-white px-4 py-2 flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <h3 class="text-xs font-black uppercase tracking-wider">ART. 3 — Conditions Financières & Quittance Initiale</h3>
                            </div>
                            <span class="text-[9px] font-bold uppercase tracking-wider text-slate-200">
                                Quittance Libératoire d'Entrée
                            </span>
                        </div>

                        <!-- Tableau Financier Notarié -->
                        <div class="overflow-x-auto">
                            <table class="w-full text-xs">
                                <thead>
                                    <tr class="bg-slate-100 border-b border-slate-300 text-slate-700 font-black uppercase text-[9px] tracking-wider">
                                        <th class="text-left px-4 py-2">Désignation de la Rubrique</th>
                                        <th class="text-center px-3 py-2">Modalité / Base</th>
                                        <th class="text-right px-3 py-2">Montant Unitaire</th>
                                        <th class="text-right px-4 py-2">Règlement à la Signature</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-200">
                                    <!-- Loyer Mensuel -->
                                    <tr>
                                        <td class="px-4 py-2.5">
                                            <p class="font-bold text-slate-900">Loyer Mensuel Principal</p>
                                            <p class="text-[9px] text-slate-500">Payable d'avance au plus tard le 5 de chaque mois</p>
                                        </td>
                                        <td class="px-3 py-2.5 text-center text-slate-600 font-medium">1 mois</td>
                                        <td class="px-3 py-2.5 text-right font-bold text-slate-900 font-mono">{{ number_format($loyer, 0, ',', ' ') }} {{ $devise }}</td>
                                        <td class="px-4 py-2.5 text-right">
                                            @if($avanceMois > 0)
                                                <span class="text-[9px] font-bold text-slate-700 uppercase bg-slate-100 border border-slate-200 px-2 py-0.5 rounded">Inclus dans avance</span>
                                            @else
                                                <span class="text-[9px] font-semibold text-slate-400">À terme échu</span>
                                            @endif
                                        </td>
                                    </tr>

                                    <!-- Caution / Dépôt de garantie -->
                                    <tr>
                                        <td class="px-4 py-2.5">
                                            <p class="font-bold text-slate-900">Dépôt de Garantie (Caution)</p>
                                            <p class="text-[9px] text-slate-500">Garantie d'exécution des obligations, non productif d'intérêts</p>
                                        </td>
                                        <td class="px-3 py-2.5 text-center text-slate-600 font-medium">
                                            {{ $loyer > 0 ? round($caution / $loyer) : 0 }} mois
                                        </td>
                                        <td class="px-3 py-2.5 text-right text-slate-400">—</td>
                                        <td class="px-4 py-2.5 text-right font-black text-slate-900 font-mono">
                                            {{ number_format($caution, 0, ',', ' ') }} {{ $devise }}
                                        </td>
                                    </tr>

                                    <!-- Avance de loyer -->
                                    @if($avanceMois > 0)
                                    <tr>
                                        <td class="px-4 py-2.5">
                                            <p class="font-bold text-slate-900">Avance de Loyer Initiale</p>
                                            <p class="text-[9px] text-slate-500">Couvre les {{ $avanceMois }} premier(s) mois d'occupation</p>
                                        </td>
                                        <td class="px-3 py-2.5 text-center text-slate-600 font-medium">
                                            {{ $avanceMois }} mois
                                        </td>
                                        <td class="px-3 py-2.5 text-right text-slate-700 font-mono">{{ number_format($loyer, 0, ',', ' ') }} {{ $devise }}</td>
                                        <td class="px-4 py-2.5 text-right font-black text-slate-900 font-mono">
                                            {{ number_format($loyerAvanceTotal, 0, ',', ' ') }} {{ $devise }}
                                        </td>
                                    </tr>
                                    @endif

                                    <!-- Frais de dossier -->
                                    @if($fraisDossier > 0)
                                    <tr>
                                        <td class="px-4 py-2.5">
                                            <p class="font-bold text-slate-900">Honoraires & Frais de Rédaction</p>
                                            <p class="text-[9px] text-slate-500">Constitution du dossier et formalisation du bail</p>
                                        </td>
                                        <td class="px-3 py-2.5 text-center text-slate-600 font-medium">Forfait</td>
                                        <td class="px-3 py-2.5 text-right text-slate-400">—</td>
                                        <td class="px-4 py-2.5 text-right font-black text-slate-900 font-mono">
                                            {{ number_format($fraisDossier, 0, ',', ' ') }} {{ $devise }}
                                        </td>
                                    </tr>
                                    @endif

                                    <!-- T.V.A. (si applicable) -->
                                    @if($montantTVA > 0)
                                    <tr>
                                        <td class="px-4 py-2.5">
                                            <p class="font-bold text-slate-900">T.V.A. ({{ number_format($tauxTVA, 0) }}%)</p>
                                            <p class="text-[9px] text-slate-500">Taxe sur la valeur ajoutée sur loyer et honoraires</p>
                                        </td>
                                        <td class="px-3 py-2.5 text-center text-slate-600 font-medium">{{ number_format($tauxTVA, 0) }}%</td>
                                        <td class="px-3 py-2.5 text-right text-slate-400">—</td>
                                        <td class="px-4 py-2.5 text-right font-black text-slate-900 font-mono">
                                            {{ number_format($montantTVA, 0, ',', ' ') }} {{ $devise }}
                                        </td>
                                    </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>

                        <!-- TOTAL REÇU & CLAUSE DE QUITTANCE FORMELLE -->
                        <div class="bg-slate-900 text-white p-3.5 sm:p-4 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                            <div>
                                <p class="text-[9px] font-black uppercase tracking-[0.15em] text-slate-300">Total Encaissé à la Signature (Valant Quittance)</p>
                                <p class="text-xl sm:text-2xl font-black text-white tracking-tight mt-0.5 font-mono">
                                    {{ number_format($totalSign, 0, ',', ' ') }} <span class="text-xs font-bold text-slate-300">{{ $devise }}</span>
                                </p>
                                @if(!empty($montantEnLettres))
                                <p class="text-[10px] text-slate-300 font-medium italic mt-0.5">
                                    Soit : <strong>{{ $montantEnLettres }} {{ $devise === 'XOF' ? 'Francs CFA' : $devise }}</strong>
                                </p>
                                @endif
                            </div>

                            <div class="sm:text-right bg-slate-800 p-2.5 rounded border border-slate-700 max-w-sm">
                                <p class="text-[9px] font-black uppercase tracking-wider text-slate-200 mb-0.5">
                                    Quittance Libératoire Initiale
                                </p>
                                <p class="text-[9px] text-slate-300 leading-tight italic">
                                    Le Bailleur reconnaît avoir reçu du Preneur ladite somme, dont quittance sans réserve sous réserve d'encaissement définitif.
                                </p>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- SÉPARATEUR DE PAGE D'IMPRESSION PROPRE -->
                <div class="print-page-break my-6 pt-2 border-t border-dashed border-slate-300 text-center relative no-print-border">
                    <span class="bg-white px-3 text-[9px] font-bold uppercase tracking-widest text-slate-400 relative -top-2.5">
                        Clauses Contractuelles & Signatures
                    </span>
                </div>

                <!-- ========================================== -->
                <!-- PAGE 2 : CLAUSES JURIDIQUES & SIGNATURES  -->
                <!-- ========================================== -->
                <div class="contract-page contract-page-2">

                    <!-- 5. ARTICLES IV À VIII : CLAUSES JURIDIQUES GÉNÉRALES -->
                    <div>
                        <div class="flex items-center justify-between pb-1 mb-2.5 border-b border-slate-300">
                            <h3 class="text-[11px] font-black text-slate-900 uppercase tracking-widest">Clauses Juridiques & Obligations Contractuelles</h3>
                            <span class="text-[9px] text-slate-400 uppercase font-semibold">Articles 4 à 8</span>
                        </div>

                        <div class="contract-grid-2 grid grid-cols-1 sm:grid-cols-2 gap-3 text-justify leading-relaxed">
                            
                            <!-- Article 4 -->
                            <div class="p-3 rounded-lg border border-slate-300 bg-slate-50/20 space-y-1">
                                <h4 class="text-[10px] font-black text-slate-900 uppercase tracking-wider">
                                    Art. 4 — Modalités de Paiement & Retards
                                </h4>
                                <p class="text-[10px] text-slate-700">
                                    Le loyer est payable par anticipation au plus tard le 5 de chaque mois. Tout paiement donne lieu à une quittance de loyer délivrée au Preneur. Tout retard supérieur à 10 jours ouvrés donne lieu de plein droit à l'application de pénalités de retard conformément aux barèmes légaux et contractuels de l'agence.
                                </p>
                            </div>

                            <!-- Article 5 -->
                            <div class="p-3 rounded-lg border border-slate-300 bg-slate-50/20 space-y-1">
                                <h4 class="text-[10px] font-black text-slate-900 uppercase tracking-wider">
                                    Art. 5 — Jouissance des Lieux & Entretien
                                </h4>
                                <p class="text-[10px] text-slate-700">
                                    Le Preneur s'oblige à jouir des lieux paisiblement en bon père de famille, selon leur destination bourgeoise exclusive. Il prendra à sa charge exclusive toutes les réparations locatives et l'entretien courant des installations. Les grosses réparations (structure, toiture, vétusté majeure) incombent au Bailleur.
                                </p>
                            </div>

                            <!-- Article 6 -->
                            <div class="p-3 rounded-lg border border-slate-300 bg-slate-50/20 space-y-1">
                                <h4 class="text-[10px] font-black text-slate-900 uppercase tracking-wider">
                                    Art. 6 — Restitution du Dépôt de Garantie
                                </h4>
                                <p class="text-[10px] text-slate-700">
                                    Le dépôt de garantie ne peut en aucun cas s'imputer sur les derniers mois de loyer dus par le Preneur. Il sera intégralement restitué dans un délai légal maximum de trente (30) jours suivant la remise des clés et déduction justifiée des réparations locatives constatées lors de l'état des lieux contradictoire de sortie.
                                </p>
                            </div>

                            <!-- Article 7 -->
                            <div class="p-3 rounded-lg border border-slate-300 bg-slate-50/20 space-y-1">
                                <h4 class="text-[10px] font-black text-slate-900 uppercase tracking-wider">
                                    Art. 7 — Clause Résolutoire de Plein Droit
                                </h4>
                                <p class="text-[10px] text-slate-700">
                                    À défaut de paiement d'un seul terme de loyer à son échéance, ou en cas d'inexécution d'une seule des clauses des présentes, le présent bail sera résilié immédiatement de plein droit huit (8) jours après sommation de payer ou mise en demeure restée infructueuse, sans préjudice de tous dommages et intérêts.
                                </p>
                            </div>

                            <!-- Article 8 -->
                            <div class="p-3 rounded-lg border border-slate-300 bg-slate-50/20 space-y-1 sm:col-span-2">
                                <h4 class="text-[10px] font-black text-slate-900 uppercase tracking-wider">
                                    Art. 8 — État des Lieux Contradictoire & Assurances
                                </h4>
                                <p class="text-[10px] text-slate-700">
                                    Un état des lieux contradictoire d'entrée a été dressé lors de la remise des clés en présence des deux parties ou de leurs représentants, et demeure obligatoirement annexé aux présentes. Le Preneur s'engage formellement à souscrire et maintenir en état de validité une police d'assurance couvrant les risques locatifs, incendie et dégâts des eaux pour toute la durée du bail.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- 6. SIGNATURES, CACHET OFFICIEL & PARAPHES -->
                    <div class="mt-4 pt-3 border-t-2 border-slate-900 space-y-3.5 page-break-inside-avoid">
                        <div class="text-center">
                            <p class="text-[11px] font-semibold text-slate-800">
                                Fait à <strong>{{ $contrat->agence->adresse ? explode(',', $contrat->agence->adresse)[0] : 'Yamoussoukro' }}</strong>, 
                                le <strong>{{ \Carbon\Carbon::parse($contrat->date_debut)->translatedFormat('d F Y') }}</strong>, 
                                en deux (2) exemplaires originaux de bonne foi revêtus de la signature des parties.
                            </p>
                        </div>

                        <div class="contract-grid-2 grid grid-cols-2 gap-5">
                            
                            <!-- Signature Bailleur & Agence -->
                            <div class="border-2 border-slate-400 rounded-lg p-3.5 bg-slate-50/30 flex flex-col justify-between h-44 sm:h-48 relative">
                                <div>
                                    <p class="text-[9px] font-black uppercase tracking-wider text-slate-500">Pour le Bailleur & Mandataire</p>
                                    <p class="text-xs font-black text-slate-900 uppercase mt-0.5">{{ $contrat->agence->nom }}</p>
                                    <p class="text-[9px] text-slate-600 font-medium italic mt-0.5">« Bon pour mandat, bail et quittance »</p>
                                </div>

                                <!-- Emplacement Sceau / Tampon -->
                                <div class="my-auto text-center pointer-events-none opacity-40">
                                    <div class="inline-block border border-dashed border-slate-500 rounded px-3 py-1.5">
                                        <span class="text-[9px] font-bold text-slate-700 uppercase tracking-widest block">Cachet & Signature</span>
                                        <span class="text-[8px] text-slate-500">Direction de l'Agence</span>
                                    </div>
                                </div>

                                <div class="pt-1.5 border-t border-slate-200 text-center">
                                    <p class="text-[9px] font-bold text-slate-600">Signature autorisée de l'Agence</p>
                                </div>
                            </div>

                            <!-- Signature Preneur / Locataire -->
                            <div class="border-2 border-slate-400 rounded-lg p-3.5 bg-slate-50/30 flex flex-col justify-between h-44 sm:h-48 relative">
                                <div>
                                    <p class="text-[9px] font-black uppercase tracking-wider text-slate-500">Pour le Preneur (Le Locataire)</p>
                                    <p class="text-xs font-black text-slate-900 uppercase mt-0.5">{{ $contrat->locataire->civilite ?? '' }} {{ $contrat->locataire->user->name }}</p>
                                    <p class="text-[9px] text-slate-600 font-medium italic mt-0.5">« Lu et approuvé, bon pour accord de bail »</p>
                                </div>

                                <!-- Emplacement Signature manuscrite -->
                                <div class="my-auto text-center pointer-events-none opacity-40">
                                    <div class="inline-block border border-dashed border-slate-500 rounded px-3 py-1.5">
                                        <span class="text-[9px] font-bold text-slate-700 uppercase tracking-widest block">Signature Manuscrite</span>
                                        <span class="text-[8px] text-slate-500">Précédée de la mention manuscrite</span>
                                    </div>
                                </div>

                                <div class="pt-1.5 border-t border-slate-200 text-center">
                                    <p class="text-[9px] font-bold text-slate-600">Signature du Locataire</p>
                                </div>
                            </div>
                        </div>

                        <!-- Zone de Paraphes en bas de page -->
                        <div class="flex justify-between items-center text-[10px] text-slate-500 pt-1.5 border-t border-dashed border-slate-300">
                            <span>Paraphe Bailleur / Agence : ___________________________</span>
                            <span>Paraphe Locataire : ___________________________</span>
                        </div>
                    </div>

                    <!-- 7. PIED DE PAGE SÉCURISÉ & MENTIONS LÉGALES -->
                    <div class="mt-3 pt-2 border-t border-slate-200 text-center text-[9px] text-slate-500 space-y-0.5">
                        <p class="font-bold text-slate-700">
                            {{ $contrat->agence->nom }} • Siège Social : {{ $contrat->agence->adresse }} 
                            @if($contrat->agence->telephone) • Tél : {{ $contrat->agence->telephone }} @endif
                            @if($contrat->agence->numero_agrement) • Agrément N° {{ $contrat->agence->numero_agrement }} @endif
                        </p>
                        <p class="text-[8px] text-slate-400 italic">
                            Document contractuel infalsifiable généré électroniquement par le système de gestion ONE.Immo • Réf : {{ $numContrat }} • {{ $securityCode }} • Tout faux ou altération est passible de poursuites judiciaires.
                        </p>
                    </div>

                </div>

            </div>

            <!-- BOUTON D'ACTION INFÉRIEUR (ÉCRAN UNIQUEMENT) -->
            <div class="mt-6 flex justify-between items-center no-print">
                <span class="text-xs text-slate-500 font-medium">
                    <i class="fas fa-shield-alt text-slate-700 mr-1"></i> Document certifié conforme aux normes juridiques de location
                </span>

                <button type="button" onclick="window.print()" class="px-6 py-2.5 bg-slate-900 hover:bg-black text-white rounded-lg text-xs font-black uppercase tracking-wider shadow-sm transition-all flex items-center gap-2 cursor-pointer">
                    <i class="fas fa-print text-xs"></i>
                    <span>Imprimer le Contrat Officiel</span>
                </button>
            </div>

        </div>
    </div>

    <!-- STYLES CSS D'IMPRESSION A4 HAUTE FIDÉLITÉ (FIDÉLITÉ 1:1 ÉCRAN / PAPIER) -->
    <style>
        @media print {
            @page {
                size: A4 portrait;
                margin: 10mm 12mm 10mm 12mm;
            }

            /* Réinitialisation complète de tous les conteneurs d'application */
            html,
            body,
            body > div,
            #app-surface,
            #page-content,
            main,
            main > div,
            .min-h-screen,
            .py-6,
            .py-10 {
                position: static !important;
                overflow: visible !important;
                height: auto !important;
                min-height: 0 !important;
                max-height: none !important;
                width: 100% !important;
                max-width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
                transform: none !important;
                box-shadow: none !important;
                border: none !important;
                background: #ffffff !important;
                font-size: 10.5pt !important;
                color: #0f172a !important;
                line-height: 1.25 !important;
            }

            /* Masquage de l'interface applicative et des boutons */
            header,
            nav,
            aside,
            footer,
            #page-loader,
            .no-print,
            [class*="drawer"],
            [class*="navigation"] {
                display: none !important;
                visibility: hidden !important;
            }

            .max-w-4xl {
                max-width: 100% !important;
                width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
            }

            /* Feuille de contrat : zéro bordure externe, zéro ombre en impression */
            #printable-contract {
                position: static !important;
                box-shadow: none !important;
                border: none !important;
                padding: 0 !important;
                margin: 0 !important;
                border-radius: 0 !important;
                background: transparent !important;
                width: 100% !important;
                max-width: 100% !important;
            }

            /* Forcer le maintien absolu des colonnes en impression (WYSIWYG) */
            .contract-grid-2 {
                display: grid !important;
                grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
                gap: 10px !important;
            }

            /* Saut de page automatique entre Page 1 et Page 2 */
            .print-page-break {
                display: block !important;
                break-before: page !important;
                page-break-before: always !important;
                height: 0 !important;
                margin: 0 !important;
                padding: 0 !important;
                border: none !important;
            }

            .no-print-border {
                border: none !important;
            }

            .page-break-inside-avoid {
                break-inside: avoid !important;
                page-break-inside: avoid !important;
            }

            /* Respect strict des bordures et fonds noirs/gris à l'impression */
            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
        }
    </style>
</x-app-layout>