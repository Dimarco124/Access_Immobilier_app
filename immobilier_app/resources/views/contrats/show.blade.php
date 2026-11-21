<x-app-layout>
    <x-slot name="header">
        <div class="flex  flex-col items-start gap-4">
            <div>
                <h2 class="font-black text-2xl text-slate-900 tracking-tight uppercase">Dossier du Contrat</h2>
                <p class="text-[11px] text-slate-500 font-bold uppercase tracking-widest mt-1">
                    {{ $contrat->numero_contrat ?? 'Contrat #' . $contrat->id }} <span class="mx-2 text-slate-300">•</span> {{ $contrat->locataire->user->name }}
                </p>
            </div>
            <div class="flex items-center gap-3">
                @if($contrat->statut === 'Actif' && (Auth::user()->hasRole('admin') || Auth::user()->hasModuleAccess('commercial', 'collaborator')))
                <a href="{{ route('contrats.edit', $contrat) }}" class="px-4 py-2 bg-amber-500 text-white rounded-lg text-xs font-black uppercase hover:bg-amber-600 transition-colors flex items-center gap-2 shadow-sm">
                    <i class="fas fa-edit"></i>
                    Modifier
                </a>
                @endif
                <a href="{{ route('contrats.bail', $contrat) }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-xs font-black uppercase hover:bg-indigo-700 transition-colors flex items-center gap-2">
                    <i class="fas fa-file-contract"></i>
                    Voir le Bail
                </a>
                @if(!empty($contrat->etat_des_lieux_entree))
                <a href="{{ route('contrats.etat-des-lieux.print', $contrat) }}" target="_blank" class="px-4 py-2 bg-emerald-600 text-white rounded-lg text-xs font-black uppercase hover:bg-emerald-700 transition-colors flex items-center gap-2">
                    <i class="fas fa-clipboard-check"></i>
                    Voir État des Lieux Entrée
                </a>
                @endif
                @if($contrat->statut === 'Actif' && Auth::user()->hasRole('admin'))
                <a href="{{ route('contrats.etat-des-lieux-sortie', $contrat) }}" class="px-4 py-2 bg-red-600 text-white rounded-lg text-xs font-black uppercase hover:bg-red-700 transition-colors flex items-center gap-2">
                    <i class="fas fa-door-closed"></i>
                    Mettre fin au contrat
                </a>
                @endif
                @if(!empty($contrat->etat_des_lieux_sortie) && $contrat->statut === 'Terminé')
                <a href="{{ route('contrats.etat-des-lieux-sortie.print', $contrat) }}" target="_blank" class="px-4 py-2 bg-purple-600 text-white rounded-lg text-xs font-black uppercase hover:bg-purple-700 transition-colors flex items-center gap-2">
                    <i class="fas fa-file-pdf"></i>
                    Voir État des Lieux Sortie
                </a>
                @endif
                @if(Auth::user()->hasRole('locataire'))
                <a href="{{ route('contrats.mes-contrats') }}" class="px-4 py-2 bg-slate-100 text-slate-600 rounded-lg text-xs font-black uppercase hover:bg-slate-200 transition-colors">
                    Retour à mes contrats
                </a>
                @elseif(Auth::user()->hasRole('proprietaire'))
                <a href="{{ route('dashboard') }}" class="px-4 py-2 bg-slate-100 text-slate-600 rounded-lg text-xs font-black uppercase hover:bg-slate-200 transition-colors">
                    Retour au dashboard
                </a>
                @else
                <a href="{{ route('contrats.index') }}" class="px-4 py-2 bg-slate-100 text-slate-600 rounded-lg text-xs font-black uppercase hover:bg-slate-200 transition-colors">
                    Retour à la liste
                </a>
                @endif
                @if($contrat->statut === 'Actif')
                <span class="px-3 py-1 bg-emerald-100 text-emerald-700 text-[10px] font-black rounded-full uppercase">Contrat Actif</span>
                @else
                <span class="px-3 py-1 bg-slate-100 text-slate-600 text-[10px] font-black rounded-full uppercase">{{ $contrat->statut }}</span>
                @endif
            </div>
        </div>
    </x-slot>

    {{-- Messages flash --}}
    @if(session('success'))
    <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 rounded-2xl flex items-center gap-3">
        <div class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0">
            <i class="fas fa-check-circle"></i>
        </div>
        <p class="text-xs font-bold text-emerald-800">{{ session('success') }}</p>
    </div>
    @endif

    @if(session('error'))
    <div class="mb-6 p-4 bg-rose-50 border border-rose-200 rounded-2xl flex items-center gap-3">
        <div class="w-8 h-8 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center shrink-0">
            <i class="fas fa-exclamation-triangle"></i>
        </div>
        <p class="text-xs font-bold text-rose-800">{{ session('error') }}</p>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Colonne de gauche : Infos Contrat --}}
        <div class="lg:col-span-1 space-y-6">
            @if($contrat->statut === 'Terminé')
            <div class="p-6 bg-slate-900 text-white rounded-3xl shadow-lg border border-slate-800">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-9 h-9 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center">
                        <i class="fas fa-flag-checkered text-sm"></i>
                    </div>
                    <div>
                        <h4 class="text-xs font-black uppercase tracking-wider text-white">Contrat Clôturé</h4>
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">
                            Le {{ $contrat->date_cloture ? $contrat->date_cloture->format('d/m/Y') : '-' }}
                        </p>
                    </div>
                </div>
                <div class="space-y-2 text-xs border-t border-slate-800 pt-3">
                    <div class="flex justify-between">
                        <span class="text-slate-400 font-bold">Caution initiale :</span>
                        <span class="font-black">{{ number_format($contrat->montant_caution, 0, ',', ' ') }} {{ Auth::user()->agence->devise }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-400 font-bold">Retenue réparations :</span>
                        <span class="font-black text-rose-400">- {{ number_format($contrat->montant_caution_retenu ?? 0, 0, ',', ' ') }} {{ Auth::user()->agence->devise }}</span>
                    </div>
                    <div class="flex justify-between pt-1 border-t border-slate-800">
                        <span class="text-slate-400 font-bold">Caution remboursée :</span>
                        <span class="font-black text-emerald-400">{{ number_format($contrat->montant_caution_rembourse ?? 0, 0, ',', ' ') }} {{ Auth::user()->agence->devise }}</span>
                    </div>
                    @if($contrat->notes_cloture)
                    <div class="pt-2 mt-2 border-t border-slate-800 text-[11px] text-slate-300 italic">
                        "{{ $contrat->notes_cloture }}"
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <div class="premium-card p-6 bg-white">
                <h3 class="font-black text-sm text-slate-900 uppercase tracking-tight mb-4">Informations Générales</h3>

                <div class="space-y-4">
                    <div class="flex flex-col">
                        <span class="text-[10px] font-black text-slate-400 uppercase">Propriété & Unité Locative</span>
                        <span class="text-sm font-bold text-slate-700">{{ $contrat->bien->nom_bien }}</span>
                        @if($contrat->bien->familleDeBien)
                        <span class="text-xs text-slate-500">{{ $contrat->bien->familleDeBien->nom_famille }}</span>
                        @endif
                    </div>

                    <div class="flex flex-col">
                        <span class="text-[10px] font-black text-slate-400 uppercase">Locataire</span>
                        <span class="text-sm font-bold text-slate-700">{{ $contrat->locataire->user->name }}</span>
                        <span class="text-xs text-slate-500">{{ $contrat->locataire->user->telephone }}</span>
                    </div>

                    <div class="grid grid-cols-2 gap-4 pt-4 border-t border-slate-50">
                        <div class="flex flex-col">
                            <span class="text-[10px] font-black text-slate-400 uppercase">Date Début</span>
                            <span class="text-sm font-bold text-slate-700">{{ $contrat->date_debut->format('d/m/Y') }}</span>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-[10px] font-black text-slate-400 uppercase">Date Fin</span>
                            <span class="text-sm font-bold text-slate-700">
                                @if($contrat->date_fin)
                                    {{ $contrat->date_fin->format('d/m/Y') }}
                                @else
                                    <span class="text-slate-400">Non définie</span>
                                @endif
                            </span>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-slate-50">
                        <div class="flex flex-col text-right">
                            <span class="text-[10px] font-black text-slate-400 uppercase">Loyer Mensuel</span>
                            <span class="text-sm font-black text-blue-600">{{ number_format($contrat->montant_loyer_actuel, 0, ',', ' ') }} {{ Auth::user()->agence->devise }}</span>
                        </div>
                    </div>

                    @if($contrat->nombre_mois_avance > 0)
                    <div class="pt-4 border-t border-slate-50">
                        <div class="flex items-center justify-between">
                            <span class="text-[10px] font-black text-slate-400 uppercase">Mois d'avance</span>
                            <span class="text-sm font-black text-purple-600">{{ $contrat->nombre_mois_avance }} mois</span>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <div class="premium-card p-6 bg-white border-l-4 border-indigo-500">
                <h3 class="font-black text-sm text-slate-900 uppercase tracking-tight mb-4">Garantie & Caution</h3>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase">Caution Déposée</p>
                        <p class="text-xl font-black text-slate-900">{{ number_format($contrat->montant_caution, 0, ',', ' ') }} <span class="text-xs">{{ Auth::user()->agence->devise }}</span></p>
                    </div>
                    <div class="w-12 h-12 bg-indigo-50 rounded-xl flex items-center justify-center">
                        <i class="fas fa-shield-alt text-indigo-500 text-xl"></i>
                    </div>
                </div>
            </div>

            <div class="premium-card p-6 bg-white">
                <h3 class="font-black text-sm text-slate-900 uppercase tracking-tight mb-4">Récapitulatif</h3>
                <div class="space-y-2 text-xs">
                    <div class="flex justify-between">
                        <span class="text-slate-500">Mois total couverts</span>
                        <span class="font-bold text-slate-700">
                            @php
                                // Compter uniquement les mois avec facture ou soldés par avance
                                $moisCouverts = collect($historiqueMois)->filter(function($i) {
                                    return $i['facture'] !== null || $i['considerer_comme_solde'];
                                })->count();
                            @endphp
                            {{ $moisCouverts }}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Mois soldés</span>
                        <span class="font-bold text-emerald-600">{{ collect($historiqueMois)->filter(fn($i) => $i['considerer_comme_solde'] || ($i['facture'] && $i['facture']->statut === 'Payée'))->count() }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">Mois en avance</span>
                        <span class="font-bold text-purple-600">{{ $contrat->nombre_mois_avance }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Colonne de droite : Historique des Paiements (Dossier) --}}
        <div class="lg:col-span-2">
            <div class="premium-card bg-white overflow-hidden">
                <div class="p-6 border-b border-slate-50 flex items-center justify-between flex-wrap gap-4">
                    <div>
                        <h3 class="font-black text-sm text-slate-900 uppercase tracking-tight">Historique des Loyers (Le Dossier)</h3>
                        <p class="text-[10px] text-slate-400 font-bold uppercase mt-1">État de paiement mois par mois depuis le début du contrat</p>
                    </div>
                    <div class="flex gap-2 flex-wrap">
                        <div class="flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                            <span class="text-[10px] font-bold text-slate-500 uppercase">Payé</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                            <span class="text-[10px] font-bold text-slate-500 uppercase">Partiel</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-red-500"></span>
                            <span class="text-[10px] font-bold text-slate-500 uppercase">Impayé</span>
                        </div>
                        @if($contrat->nombre_mois_avance > 0)
                        <div class="flex items-center gap-1.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-purple-500"></span>
                            <span class="text-[10px] font-bold text-slate-500 uppercase">Avance</span>
                        </div>
                        @endif
                    </div>
                </div>

                <div class="divide-y divide-slate-200">
                    @foreach($historiqueMois as $item)
                    <div class="p-5 flex items-center justify-between hover:bg-slate-50 transition-colors {{ $item['is_current'] ? 'bg-blue-50/30' : '' }} {{ $item['considerer_comme_solde'] ? 'bg-emerald-50/30' : '' }}">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-xl bg-slate-100 flex flex-col items-center justify-center">
                                <span class="text-[9px] font-black text-slate-400 uppercase">{{ $item['date']->format('M') }}</span>
                                <span class="text-sm font-black text-slate-700 leading-none">{{ $item['date']->format('Y') }}</span>
                            </div>
                            <div>
                                <h4 class="text-sm font-black text-slate-800 uppercase">{{ $item['mois'] }}</h4>
                                <div class="flex gap-2 mt-1 flex-wrap">
                                    @if($item['is_current'])
                                    <span class="px-2 py-0.5 bg-blue-100 text-blue-700 text-[8px] font-black rounded-full uppercase">Mois en cours</span>
                                    @endif
                                    @if($item['is_avance'])
                                    @if($item['avance_validee'])
                                    <span class="px-2 py-0.5 bg-purple-100 text-purple-700 text-[8px] font-black rounded-full uppercase">Soldé par avance</span>
                                    @else
                                    <span class="px-2 py-0.5 bg-purple-100 text-purple-500 text-[8px] font-black rounded-full uppercase">Avance (non payée)</span>
                                    @endif
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-6">
                            @if($item['facture'])
                            <div class="text-right">
                                <p class="text-xs font-black text-slate-700">{{ number_format($item['facture']->montant_total_du, 0, ',', ' ') }} {{ Auth::user()->agence->devise }}</p>
                                @if($item['facture']->montant_deja_paye > 0)
                                <p class="text-[9px] text-emerald-500 font-bold uppercase">Payé : {{ number_format($item['facture']->montant_deja_paye, 0, ',', ' ') }}</p>
                                @endif
                                <p class="text-[9px] text-slate-400 font-bold uppercase">Facture #{{ $item['facture']->numero_facture }}</p>
                            </div>

                            <div class="flex items-center gap-2 min-w-[100px] justify-end">
                                @if($item['considerer_comme_solde'])
                                <span class="px-3 py-1 bg-emerald-100 text-emerald-700 text-[10px] font-black rounded-full uppercase">Soldé</span>
                                @elseif($item['facture']->statut === 'Payée')
                                <span class="px-3 py-1 bg-emerald-100 text-emerald-700 text-[10px] font-black rounded-full uppercase">Soldé</span>
                                @elseif($item['facture']->statut === 'Partielle')
                                <span class="px-3 py-1 bg-amber-100 text-amber-700 text-[10px] font-black rounded-full uppercase">Partiel</span>
                                @else
                                <span class="px-3 py-1 bg-red-100 text-red-700 text-[10px] font-black rounded-full uppercase">Impayé</span>
                                @endif
                            </div>
                            @else
                            <div class="flex items-center gap-2 min-w-[100px] justify-end">
                                @if($item['considerer_comme_solde'])
                                <span class="px-3 py-1 bg-emerald-100 text-emerald-700 text-[10px] font-black rounded-full uppercase">Soldé</span>
                                @else
                                <span class="px-3 py-1 bg-slate-100 text-slate-400 text-[10px] font-black rounded-full uppercase">Non facturé</span>
                                @endif
                            </div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</x-app-layout>