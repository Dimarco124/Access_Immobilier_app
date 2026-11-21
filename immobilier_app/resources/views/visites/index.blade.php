<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 w-full">
            <div>
                <h2 class="font-black text-2xl md:text-2xl text-slate-900 tracking-tight uppercase">
                    @if(Auth::user()->hasRole('locataire'))
                    {{ __('Mes Visites') }}
                    @else
                    {{ __('Gestion des Visites') }}
                    @endif
                </h2>
                <p class="text-[11px] text-slate-500 font-bold uppercase tracking-widest mt-1">
                    @if(Auth::user()->hasRole('locataire'))
                    Consultez l'historique de vos visites immobilières
                    @else
                    Planifiez et suivez les visites des futurs locataires
                    @endif
                </p>
            </div>
            @if(Auth::user()->hasModuleAccess('commercial', 'collaborator'))
            <div class="mt-1">
                <a href="{{ route('visites.create') }}" class="px-5 py-2.5 bg-slate-900 text-white text-[10px] font-black uppercase tracking-widest hover:bg-black transition-all rounded-xl shadow-lg inline-flex items-center gap-2">
                    <i class="fas fa-calendar-plus"></i> Nouvelle Visite
                </a>
            </div>
            @endif
        </div>
    </x-slot>

    <div class="space-y-5">
        <!-- Filtres de recherche -->
        <div class="bg-white p-4 md:p-6 rounded-xl shadow-sm border border-slate-100">
            <form action="{{ route('visites.index') }}" method="GET">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                    <div>
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Bien</label>
                        <input type="text" name="bien" value="{{ request('bien') }}" placeholder="Nom du bien" 
                            class="w-full px-3 py-2 border border-slate-200 rounded-lg bg-slate-50 placeholder-slate-400 focus:outline-none focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm transition-colors">
                    </div>
                    @if(!Auth::user()->hasRole('locataire'))
                    <div>
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Locataire</label>
                        <input type="text" name="locataire" value="{{ request('locataire') }}" placeholder="Nom du locataire" 
                            class="w-full px-3 py-2 border border-slate-200 rounded-lg bg-slate-50 placeholder-slate-400 focus:outline-none focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm transition-colors">
                    </div>
                    @endif
                    <div>
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Statut</label>
                        <select name="statut" class="w-full px-3 py-2 border border-slate-200 rounded-lg bg-slate-50 focus:outline-none focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm transition-colors">
                            <option value="">Tous</option>
                            <option value="Planifiée" {{ request('statut') == 'Planifiée' ? 'selected' : '' }}>Planifiée</option>
                            <option value="Effectuée" {{ request('statut') == 'Effectuée' ? 'selected' : '' }}>Effectuée</option>
                            <option value="Annulée" {{ request('statut') == 'Annulée' ? 'selected' : '' }}>Annulée</option>
                        </select>
                    </div>
                </div>
                <div class="flex items-center gap-3 mt-4">
                    <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-black uppercase tracking-widest rounded-lg transition-colors shadow-sm inline-flex items-center gap-2">
                        <i class="fas fa-search"></i> Rechercher
                    </button>
                    @if(request()->hasAny(['bien', 'locataire', 'statut']))
                    <a href="{{ route('visites.index') }}" class="px-5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-black uppercase tracking-widest rounded-lg transition-colors inline-flex items-center gap-2">
                        <i class="fas fa-times"></i> Effacer
                    </a>
                    @endif
                </div>
            </form>
        </div>

        @if (session('success'))
        <div class="premium-card bg-emerald-50 border-emerald-100 p-4 flex items-center gap-3">
            <div class="w-8 h-8 bg-emerald-500 rounded-lg flex items-center justify-center text-white text-xs shrink-0">
                <i class="fas fa-check"></i>
            </div>
            <p class="text-emerald-700 text-xs font-bold">{{ session('success') }}</p>
        </div>
        @endif

        @if($visites->count() > 0)
        <div class="premium-card overflow-hidden bg-white">

            {{-- VUE MOBILE : cards --}}
            <div class="md:hidden divide-y divide-slate-200">
                @foreach($visites as $visite)
                <div class="p-4 space-y-3">
                    <div class="flex items-start gap-3 mb-2">
                        <div class="flex-1 min-w-0">
                            <div class="font-black text-slate-900 uppercase text-xs">{{ $visite->date_heure_visite->format('d M Y') }}</div>
                            <div class="text-[10px] font-bold text-slate-400">{{ $visite->date_heure_visite->format('H:i') }}</div>
                            @if($visite->bien)
                            <p class="text-xs font-black text-slate-800 uppercase mt-1 truncate">{{ $visite->bien->nom_bien }}</p>
                            @endif
                            @if($visite->locataire)
                            <p class="text-[11px] text-slate-500 truncate">{{ $visite->locataire->user->name }}</p>
                            @endif
                        </div>
                        <div class="text-right">
                            <span class="inline-block px-2 py-0.5 text-[9px] font-black rounded-lg uppercase whitespace-nowrap mb-1
                                @if($visite->statut === 'Programmé') bg-slate-100 text-slate-600
                                @elseif($visite->statut === 'Effectué') bg-emerald-50 text-emerald-600
                                @else bg-red-50 text-red-600 @endif">
                                {{ $visite->statut }}
                            </span>
                            <div class="text-xs font-black text-slate-900 whitespace-nowrap">{{ number_format($visite->montant_paye, 0, ',', ' ') }} <span class="text-[9px] text-slate-400">{{ Auth::user()->agence->devise }}</span></div>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 pt-2 border-t border-slate-100">
                        @if($visite->statut !== 'Annulé')
                        <a href="{{ route('visites.recu', $visite) }}" class="flex-1 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center text-[10px] font-black uppercase hover:bg-blue-600 hover:text-white transition-all gap-1">
                            <i class="fas fa-file-invoice text-[10px]"></i> Reçu
                        </a>
                        @endif
                        @if($visite->statut === 'Programmé' && Auth::user()->hasModuleAccess('commercial', 'collaborator'))
                        <form method="POST" action="{{ route('visites.completed', $visite) }}" class="flex-1 inline">
                            @csrf @method('PATCH')
                            <button type="submit" class="w-full h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center text-[10px] font-black uppercase hover:bg-emerald-600 hover:text-white transition-all gap-1">
                                <i class="fas fa-check text-[10px]"></i> Terminé
                            </button>
                        </form>
                        @endif
                        @if(Auth::user()->hasModuleAccess('commercial', 'manager') && $visite->statut !== 'Annulé')
                        <form method="POST" action="{{ route('visites.cancel', $visite) }}" onsubmit="return confirm('Annuler la visite ?')" class="inline">
                            @csrf @method('PATCH')
                            <button type="submit" class="w-8 h-8 rounded-lg bg-red-50 text-red-500 flex items-center justify-center hover:bg-red-500 hover:text-white transition-all">
                                <i class="fas fa-times text-[10px]"></i>
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>

            {{-- VUE DESKTOP : tableau --}}
            <div class="hidden md:block overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead>
                        <tr class="bg-slate-50/50 ">
                            <th class="text-left text-[9px] font-black text-slate-400 uppercase tracking-widest whitespace-nowrap px-6 py-5">Date & Heure</th>
                            <th class="text-left text-[9px] font-black text-slate-400 uppercase tracking-widest whitespace-nowrap px-6 py-5">Locataire</th>
                            <th class="text-left text-[9px] font-black text-slate-400 uppercase tracking-widest whitespace-nowrap px-6 py-5">Bien Immobilier</th>
                            <th class="text-left text-[9px] font-black text-slate-400 uppercase tracking-widest whitespace-nowrap px-6 py-5">Montant</th>
                            <th class="text-left text-[9px] font-black text-slate-400 uppercase tracking-widest whitespace-nowrap px-6 py-5">Statut</th>
                            <th class="text-right text-[9px] font-black text-slate-400 uppercase tracking-widest whitespace-nowrap px-6 py-5">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @foreach($visites as $visite)
                        <tr class="hover:bg-slate-50/30 transition-colors group ">
                            <td class="whitespace-nowrap px-6 py-5">
                                <div class="font-black text-slate-900 uppercase text-[11px]">{{ $visite->date_heure_visite->format('d M Y') }}</div>
                                <div class="text-[9px] font-bold text-slate-400 mt-0.5">{{ $visite->date_heure_visite->format('H:i') }}</div>
                            </td>
                            <td class="whitespace-nowrap px-6 py-5">
                                @if($visite->locataire)
                                <a href="{{ route('locataires.show', $visite->locataire) }}" class="flex items-center gap-2 group/user">
                                    <div class="w-7 h-7 rounded-md bg-slate-100 flex items-center justify-center text-[9px] font-black text-slate-500 group-hover/user:bg-slate-900 group-hover/user:text-white transition-all uppercase">
                                        {{ substr($visite->locataire->user->name, 0, 1) }}
                                    </div>
                                    <span class="text-[11px] font-bold text-slate-700 group-hover/user:text-slate-900 transition-colors uppercase">{{ $visite->locataire->user->name }}</span>
                                </a>
                                @else
                                <span class="text-[9px] font-black text-slate-300 uppercase italic">Utilisateur supprimé</span>
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-6 py-5">
                                @if($visite->bien)
                                <a href="{{ route('biens.show', $visite->bien) }}" class="text-[11px] font-black text-slate-900 uppercase tracking-tight hover:text-slate-600 transition-colors">
                                    {{ $visite->bien->nom_bien }}
                                </a>
                                @else
                                <span class="text-[9px] font-black text-slate-300 uppercase italic">Bien supprimé</span>
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-6 py-5">
                                <span class="font-black text-slate-900 text-xs">{{ number_format($visite->montant_paye, 0, ',', ' ') }} <span class="text-[9px] text-slate-400">{{ Auth::user()->agence->devise }}</span></span>
                            </td>
                            <td class="whitespace-nowrap px-6 py-5">
                                <span class="px-3 py-1 inline-flex text-[9px] font-black rounded-lg uppercase tracking-widest
                                    @if($visite->statut === 'Programmé') bg-slate-100 text-slate-600
                                    @elseif($visite->statut === 'Effectué') bg-emerald-50 text-emerald-600
                                    @else bg-red-50 text-red-600 @endif">
                                    {{ $visite->statut }}
                                </span>
                            </td>
                            <td class="whitespace-nowrap text-right whitespace-nowrap px-6 py-5">
                                <div class="flex justify-end gap-1.5 items-center">
                                    @if($visite->statut !== 'Annulé')
                                    <a href="{{ route('visites.recu', $visite) }}" class="w-7 h-7 rounded-md bg-blue-50 text-blue-600 flex items-center justify-center hover:bg-blue-600 hover:text-white transition-all">
                                        <i class="fas fa-file-invoice text-[10px]"></i>
                                    </a>
                                    @endif
                                    @if($visite->statut === 'Programmé' && Auth::user()->hasModuleAccess('commercial', 'collaborator'))
                                    <form method="POST" action="{{ route('visites.completed', $visite) }}" class="inline">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="w-7 h-7 rounded-md bg-emerald-50 text-emerald-600 flex items-center justify-center hover:bg-emerald-600 hover:text-white transition-all">
                                            <i class="fas fa-check text-[10px]"></i>
                                        </button>
                                    </form>
                                    @endif
                                    @if(Auth::user()->hasModuleAccess('commercial', 'manager') && $visite->statut !== 'Annulé')
                                    <form method="POST" action="{{ route('visites.cancel', $visite) }}" onsubmit="return confirm('Annuler la visite et soustraire le montant encaissé ?')" class="inline">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="w-7 h-7 rounded-md bg-red-50 text-red-500 flex items-center justify-center hover:bg-red-500 hover:text-white transition-all">
                                            <i class="fas fa-times text-[10px]"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @else
        <div class="premium-card py-12 md:py-16 text-center bg-white">
            <div class="w-14 h-14 bg-slate-50 rounded-2xl flex items-center justify-center text-slate-200 mx-auto mb-4">
                <i class="fas fa-calendar-times text-2xl"></i>
            </div>
            <h3 class="text-sm font-black text-slate-900 uppercase tracking-tight">Aucune visite</h3>
            <p class="text-[11px] text-slate-500 font-bold uppercase tracking-widest mt-1 mb-5">Il n'y a actuellement aucune visite programmée.</p>
            @if(Auth::user()->hasModuleAccess('commercial', 'collaborator'))
            <a href="{{ route('visites.create') }}" class="px-6 py-3 bg-slate-900 text-white text-[10px] font-black uppercase tracking-widest hover:bg-black transition-all rounded-lg shadow-lg inline-flex items-center gap-2">
                <i class="fas fa-plus"></i> Programmer une visite
            </a>
            @endif
        </div>
        @endif
    </div>
</x-app-layout>