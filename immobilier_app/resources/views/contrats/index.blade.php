<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 w-full">
            <div>
                <h2 class="font-black text-2xl md:text-4xl text-slate-900 tracking-tight uppercase">{{ __('Baux & Engagements') }}</h2>
                <p class="text-slate-500 font-medium mt-1 text-sm">Gérez les contrats de location et le suivi des échéances</p>
            </div>
            <div>
                <a href="{{ route('contrats.create') }}" class="btn-premium bg-blue-600 text-white px-5 py-3 md:px-8 md:py-4 inline-flex items-center gap-2 hover:bg-blue-700 shadow-lg shadow-blue-100 transition transform hover:-translate-y-1">
                    <i class="fas fa-file-signature"></i>
                    <span class="uppercase tracking-widest text-xs font-black">Nouveau Contrat</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        <!-- Filtres de recherche -->
        <div class="bg-white p-4 md:p-6 rounded-xl shadow-sm border border-slate-100">
            <form action="{{ route('contrats.index') }}" method="GET">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                    <div>
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">N° Contrat / Référence</label>
                        <input type="text" name="numero" value="{{ request('numero') }}" placeholder="Ex: CON-0001" 
                            class="w-full px-3 py-2 border border-slate-200 rounded-lg bg-slate-50 placeholder-slate-400 focus:outline-none focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm transition-colors">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Locataire</label>
                        <input type="text" name="locataire" value="{{ request('locataire') }}" placeholder="Nom du locataire" 
                            class="w-full px-3 py-2 border border-slate-200 rounded-lg bg-slate-50 placeholder-slate-400 focus:outline-none focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm transition-colors">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Bien</label>
                        <input type="text" name="bien" value="{{ request('bien') }}" placeholder="Nom du bien" 
                            class="w-full px-3 py-2 border border-slate-200 rounded-lg bg-slate-50 placeholder-slate-400 focus:outline-none focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm transition-colors">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Statut</label>
                        <select name="statut" class="w-full px-3 py-2 border border-slate-200 rounded-lg bg-slate-50 focus:outline-none focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm transition-colors">
                            <option value="">Tous</option>
                            <option value="Actif" {{ request('statut') == 'Actif' ? 'selected' : '' }}>Actif</option>
                            <option value="Clôturé" {{ request('statut') == 'Clôturé' ? 'selected' : '' }}>Clôturé</option>
                            <option value="Brouillon" {{ request('statut') == 'Brouillon' ? 'selected' : '' }}>Brouillon</option>
                            <option value="Résilié" {{ request('statut') == 'Résilié' ? 'selected' : '' }}>Résilié</option>
                        </select>
                    </div>
                </div>
                <div class="flex items-center gap-3 mt-4">
                    <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-black uppercase tracking-widest rounded-lg transition-colors shadow-sm inline-flex items-center gap-2">
                        <i class="fas fa-search"></i> Rechercher
                    </button>
                    @if(request()->hasAny(['numero', 'locataire', 'bien', 'statut']))
                    <a href="{{ route('contrats.index') }}" class="px-5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-black uppercase tracking-widest rounded-lg transition-colors inline-flex items-center gap-2">
                        <i class="fas fa-times"></i> Effacer
                    </a>
                    @endif
                    <a href="{{ route('contrats.print', request()->query()) }}" target="_blank" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-black uppercase tracking-widest rounded-lg transition-colors inline-flex items-center gap-2">
                        <i class="fas fa-print"></i> Imprimer
                    </a>
                </div>
            </form>
        </div>

        @if (session('success'))
        <div class="premium-card bg-green-50 border-green-100 p-4 flex items-center gap-3">
            <div class="w-8 h-8 bg-green-500 rounded-xl flex items-center justify-center text-white shrink-0">
                <i class="fas fa-check text-sm"></i>
            </div>
            <p class="text-green-700 font-bold text-sm">{{ session('success') }}</p>
        </div>
        @endif

        <div class="premium-card overflow-hidden">

            {{-- VUE MOBILE : cards --}}
            <div class="md:hidden divide-y divide-slate-200">
                @forelse($contrats as $contrat)
                <div class="p-4 space-y-3">
                    <div class="flex items-start gap-3 mb-2">
                        <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center font-black text-slate-600 shrink-0 text-sm">
                            {{ substr($contrat->locataire->user->name, 0, 1) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-black text-slate-900 truncate">{{ $contrat->locataire->user->name }}</p>
                            <p class="text-xs text-blue-600 font-bold truncate">{{ $contrat->bien->nom_bien }}</p>
                            <p class="text-[10px] text-slate-400 uppercase tracking-wider">{{ $contrat->bien->type_bien }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="px-2 py-1 text-[10px] font-black rounded-lg uppercase inline-flex items-center gap-1 whitespace-nowrap {{ $contrat->statut == 'Actif' ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600' }}">
                            <i class="fas fa-circle text-[6px]"></i>{{ $contrat->statut }}
                        </span>
                        <div class="text-xs">
                            <span class="font-black text-slate-900 whitespace-nowrap">{{ number_format($contrat->montant_loyer_actuel, 0, ',', ' ') }} {{ Auth::user()->agence->devise }}/mois</span>
                        </div>
                        <div class="flex items-center gap-1 text-slate-400 text-xs whitespace-nowrap">
                            <i class="far fa-calendar-alt text-xs"></i>
                            <span class="font-bold text-slate-600">{{ $contrat->date_debut->format('d/m/Y') }}</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 pt-2 border-t border-slate-50">
                        <a href="{{ route('contrats.show', $contrat) }}" class="flex-1 h-9 rounded-lg bg-slate-100 text-slate-600 flex items-center justify-center text-xs font-bold hover:bg-slate-900 hover:text-white transition-all gap-1">
                            <i class="fas fa-eye text-xs"></i> Voir
                        </a>
                        @if($contrat->statut === 'Actif')
                        <a href="{{ route('contrats.edit', $contrat) }}" class="flex-1 h-9 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center text-xs font-bold hover:bg-amber-600 hover:text-white transition-all gap-1">
                            <i class="fas fa-edit text-xs"></i> Modifier
                        </a>
                        @endif
                        <a href="{{ route('contrats.bail', $contrat) }}" class="w-9 h-9 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center hover:bg-indigo-600 hover:text-white transition-all">
                            <i class="fas fa-file-contract text-xs"></i>
                        </a>
                        @if($contrat->statut === 'Actif')
                        <form method="POST" action="{{ route('contrats.destroy', $contrat) }}" onsubmit="return confirm('Résilier ce contrat ?')" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-9 h-9 rounded-lg bg-red-50 text-red-500 flex items-center justify-center hover:bg-red-500 hover:text-white transition-all" title="Résilier">
                                <i class="fas fa-times-circle text-xs"></i>
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
                @empty
                <div class="p-12 text-center text-slate-400">
                    <i class="fas fa-file-contract text-3xl mb-3"></i>
                    <p class="text-sm font-medium">Aucun contrat enregistré</p>
                </div>
                @endforelse
            </div>

            {{-- VUE DESKTOP : tableau --}}
            <div class="hidden md:block overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50 whitespace-nowrap px-6 py-5">
                        <tr class="">
                            <th class="text-left text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] whitespace-nowrap px-6 py-5">Locataire</th>
                            <th class="text-left text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] whitespace-nowrap px-6 py-5">Propriété & Unité Locative</th>
                            <th class="text-left text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] whitespace-nowrap px-6 py-5">Loyer Mensuel</th>
                            <th class="text-left text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] whitespace-nowrap px-6 py-5">Période</th>
                            <th class="text-left text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] whitespace-nowrap px-6 py-5">Statut</th>
                            <th class="text-right text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] whitespace-nowrap px-6 py-5">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-slate-200">
                        @forelse($contrats as $contrat)
                        <tr class="hover:bg-slate-50 transition-colors group ">
                            <td class="whitespace-nowrap px-6 py-5">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center text-slate-400 group-hover:bg-blue-100 group-hover:text-blue-600 transition-colors font-black text-sm">
                                        {{ substr($contrat->locataire->user->name, 0, 1) }}
                                    </div>
                                    <p class="text-sm font-black text-slate-900">{{ $contrat->locataire->user->name }}</p>
                                </div>
                            </td>
                            <td class="whitespace-nowrap px-6 py-5">
                                <div class="flex flex-col">
                                    <span class="text-sm font-black text-slate-700 uppercase tracking-tighter">{{ $contrat->bien->nom_bien }}</span>
                                    @if($contrat->bien->familleDeBien)
                                    <span class="text-[10px] text-blue-600 font-black uppercase mt-1">{{ $contrat->bien->familleDeBien->nom_famille }}</span>
                                    @endif
                                    <span class="text-[10px] text-slate-400 font-medium uppercase tracking-widest">{{ $contrat->bien->type_bien }}</span>
                                </div>
                            </td>
                            <td class="whitespace-nowrap px-6 py-5">
                                <span class="text-sm font-black text-slate-900">{{ number_format($contrat->montant_loyer_actuel, 0, ',', ' ') }}</span>
                                <span class="text-[10px] text-slate-400 font-bold ml-1">{{ Auth::user()->agence->devise }}</span>
                            </td>
                            <td class="whitespace-nowrap px-6 py-5">
                                <div class="flex items-center gap-2">
                                    <i class="far fa-calendar-alt text-slate-300 text-xs"></i>
                                    <span class="text-xs font-bold text-slate-600">{{ $contrat->date_debut->format('d/m/Y') }}</span>
                                </div>
                            </td>
                            <td class="whitespace-nowrap px-6 py-5">
                                <span class="px-4 py-1.5 inline-flex text-[10px] font-black rounded-xl uppercase tracking-widest {{ $contrat->statut == 'Actif' ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600' }}">
                                    <i class="fas fa-circle text-[6px] mr-2 mt-1"></i>
                                    {{ $contrat->statut }}
                                </span>
                            </td>
                            <td class="whitespace-nowrap text-right whitespace-nowrap px-6 py-5">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('contrats.show', $contrat) }}" class="w-10 h-10 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center hover:bg-slate-200 hover:text-slate-800 transition-all shadow-sm" title="Voir les détails">
                                        <i class="fas fa-eye text-sm"></i>
                                    </a>
                                    @if($contrat->statut === 'Actif')
                                    <a href="{{ route('contrats.edit', $contrat) }}" class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center hover:bg-amber-600 hover:text-white transition-all shadow-sm" title="Modifier le contrat">
                                        <i class="fas fa-edit text-sm"></i>
                                    </a>
                                    @else
                                    <span class="w-10 h-10 rounded-xl bg-slate-100/60 text-slate-400 flex items-center justify-center cursor-not-allowed" title="Contrat clôturé (non modifiable)">
                                        <i class="fas fa-lock text-xs"></i>
                                    </span>
                                    @endif
                                    <a href="{{ route('contrats.bail', $contrat) }}" class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center hover:bg-indigo-600 hover:text-white transition-all shadow-sm" title="Voir le bail">
                                        <i class="fas fa-file-contract text-sm"></i>
                                    </a>
                                    @if($contrat->statut === 'Actif')
                                    <form method="POST" action="{{ route('contrats.destroy', $contrat) }}" onsubmit="return confirm('Résilier ce contrat ?')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-10 h-10 rounded-xl bg-red-50 text-red-500 flex items-center justify-center hover:bg-red-500 hover:text-white transition-all shadow-sm" title="Résilier">
                                            <i class="fas fa-times-circle text-sm"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr class="">
                            <td colspan="6" class="text-center text-slate-400 whitespace-nowrap px-6 py-5">
                                <i class="fas fa-file-contract text-3xl mb-3"></i>
                                <p class="text-sm font-medium mt-2">Aucun contrat enregistré</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pagination --}}
        <div class="mt-6">
            {{ $contrats->links() }}
        </div>
    </div>
</x-app-layout>