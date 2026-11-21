<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 w-full">
            <div>
                <h2 class="font-black text-2xl md:text-4xl text-slate-900 tracking-tight uppercase">{{ __('Gestion des Propriétaires') }}</h2>
                <p class="text-slate-500 font-medium mt-1 text-sm">Gérez vos relations bailleurs et le suivi de leurs patrimoines</p>
            </div>
            @if(Auth::user()->hasModuleAccess('proprietaires', 'collaborator'))
            <div>
                <a href="{{ route('proprietaires.create') }}" class="btn-premium bg-blue-600 text-white px-5 py-3 md:px-8 md:py-4 inline-flex items-center gap-2 hover:bg-blue-700 shadow-lg shadow-blue-100 transition transform hover:-translate-y-1">
                    <i class="fas fa-plus-circle"></i>
                    <span class="uppercase tracking-widest text-xs font-black">Nouveau Propriétaire</span>
                </a>
            </div>
            @endif
        </div>
    </x-slot>

    <div class="space-y-6">
        <!-- Filtres de recherche -->
        <div class="bg-white p-4 md:p-6 rounded-xl shadow-sm border border-slate-100">
            <form action="{{ route('proprietaires.index') }}" method="GET">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                    <div>
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Nom</label>
                        <input type="text" name="nom" value="{{ request('nom') }}" placeholder="Nom du propriétaire" 
                            class="w-full px-3 py-2 border border-slate-200 rounded-lg bg-slate-50 placeholder-slate-400 focus:outline-none focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm transition-colors">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Email / Téléphone</label>
                        <input type="text" name="contact" value="{{ request('contact') }}" placeholder="Email ou téléphone" 
                            class="w-full px-3 py-2 border border-slate-200 rounded-lg bg-slate-50 placeholder-slate-400 focus:outline-none focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm transition-colors">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Ville</label>
                        <input type="text" name="ville" value="{{ request('ville') }}" placeholder="Ville" 
                            class="w-full px-3 py-2 border border-slate-200 rounded-lg bg-slate-50 placeholder-slate-400 focus:outline-none focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm transition-colors">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Civilité</label>
                        <select name="civilite" class="w-full px-3 py-2 border border-slate-200 rounded-lg bg-slate-50 focus:outline-none focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm transition-colors">
                            <option value="">Toutes</option>
                            <option value="M." {{ request('civilite') == 'M.' ? 'selected' : '' }}>M.</option>
                            <option value="Mme" {{ request('civilite') == 'Mme' ? 'selected' : '' }}>Mme</option>
                            <option value="Mlle" {{ request('civilite') == 'Mlle' ? 'selected' : '' }}>Mlle</option>
                        </select>
                    </div>
                </div>
                <div class="flex items-center gap-3 mt-4">
                    <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-black uppercase tracking-widest rounded-lg transition-colors shadow-sm inline-flex items-center gap-2">
                        <i class="fas fa-search"></i> Rechercher
                    </button>
                    @if(request()->hasAny(['nom', 'contact', 'ville', 'civilite']))
                    <a href="{{ route('proprietaires.index') }}" class="px-5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-black uppercase tracking-widest rounded-lg transition-colors inline-flex items-center gap-2">
                        <i class="fas fa-times"></i> Effacer
                    </a>
                    @endif
                    <a href="{{ route('proprietaires.print', request()->query()) }}" target="_blank" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-black uppercase tracking-widest rounded-lg transition-colors inline-flex items-center gap-2">
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
                @foreach($proprietaires as $proprietaire)
                <div class="p-4">
                    <div class="flex items-start gap-3 mb-3">
                        <div class="w-11 h-11 rounded-xl bg-indigo-100 flex items-center justify-center text-indigo-600 font-black text-lg shrink-0">
                            {{ substr($proprietaire->user->name, 0, 1) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-black text-slate-900 truncate">{{ $proprietaire->user->name }}</p>
                            <p class="text-xs text-slate-400 truncate mt-0.5">{{ $proprietaire->user->email }}</p>
                            <div class="flex flex-wrap items-center gap-1.5 mt-1">
                                <span class="inline-flex items-center gap-1 text-[10px] font-black text-blue-700 bg-blue-50 px-2 py-0.5 rounded-md whitespace-nowrap">
                                    <i class="fas fa-city text-[8px]"></i>
                                    {{ $proprietaire->proprietes_count ?? $proprietaire->famillesDeBiens()->count() }} Propriété(s)
                                </span>
                                <span class="inline-flex items-center gap-1 text-[10px] font-bold text-slate-500 bg-slate-50 px-2 py-0.5 rounded-md whitespace-nowrap">
                                    <i class="fas fa-door-open text-[8px]"></i>
                                    {{ $proprietaire->unites_count ?? $proprietaire->biens_count ?? 0 }} Unité(s)
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 justify-end">
                        <a href="{{ route('proprietaires.show', $proprietaire) }}" class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center hover:bg-blue-600 hover:text-white transition-all">
                            <i class="fas fa-eye text-[10px]"></i>
                        </a>
                        @if(Auth::user()->hasModuleAccess('proprietaires', 'collaborator'))
                        <a href="{{ route('proprietaires.edit', $proprietaire) }}" class="w-8 h-8 rounded-lg bg-slate-50 text-slate-600 flex items-center justify-center hover:bg-slate-900 hover:text-white transition-all">
                            <i class="fas fa-edit text-[10px]"></i>
                        </a>
                        @endif
                        @if(Auth::user()->hasModuleAccess('proprietaires', 'manager'))
                        <form method="POST" action="{{ route('proprietaires.destroy', $proprietaire) }}" onsubmit="return confirm('Supprimer ce propriétaire ?')" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-8 h-8 rounded-lg bg-red-50 text-red-500 flex items-center justify-center hover:bg-red-500 hover:text-white transition-all">
                                <i class="fas fa-trash text-[10px]"></i>
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
                    <thead class="bg-slate-50 whitespace-nowrap px-6 py-5">
                        <tr class="">
                            <th class="text-left text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] whitespace-nowrap px-6 py-5">Identité & Contact</th>
                            <th class="text-left text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] whitespace-nowrap px-6 py-5">Pièce d'identité</th>
                            <th class="text-left text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] whitespace-nowrap px-6 py-5">Statut Patrimoine</th>
                            <th class="text-right text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] whitespace-nowrap px-6 py-5">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-slate-200">
                        @foreach($proprietaires as $proprietaire)
                        <tr class="hover:bg-slate-50 transition-colors group ">
                            <td class="whitespace-nowrap px-6 py-5">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 rounded-2xl bg-slate-100 flex items-center justify-center text-slate-400 group-hover:bg-blue-100 group-hover:text-blue-600 transition-colors font-black text-lg">
                                        {{ substr($proprietaire->user->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-black text-slate-900">{{ $proprietaire->user->name }}</p>
                                        <p class="text-xs text-slate-400 font-medium">{{ $proprietaire->user->email }} • {{ $proprietaire->user->telephone }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="whitespace-nowrap px-6 py-5">
                                <div class="flex flex-col">
                                    <span class="text-xs font-black text-slate-700 uppercase tracking-tighter">{{ $proprietaire->numero_piece_identite }}</span>
                                    <span class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-1">N° Document</span>
                                </div>
                            </td>
                            <td class="whitespace-nowrap px-6 py-5">
                                <div class="flex flex-col gap-1">
                                    <span class="px-3 py-1 inline-flex items-center gap-1.5 text-[10px] leading-4 font-black rounded-lg uppercase tracking-wider bg-blue-50 text-blue-700 w-fit">
                                        <i class="fas fa-city text-[9px]"></i>
                                        {{ $proprietaire->proprietes_count ?? $proprietaire->famillesDeBiens()->count() }} Propriété(s)
                                    </span>
                                    <span class="text-[10px] font-bold text-slate-500 pl-1 inline-flex items-center gap-1">
                                        <i class="fas fa-door-open text-[8px] text-slate-400"></i>
                                        {{ $proprietaire->unites_count ?? $proprietaire->biens_count ?? 0 }} Unité(s) locative(s)
                                    </span>
                                </div>
                            </td>
                            <td class="whitespace-nowrap text-right px-4 py-4">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('proprietaires.show', $proprietaire) }}" class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center hover:bg-blue-600 hover:text-white transition-all shadow-sm">
                                        <i class="fas fa-eye text-xs"></i>
                                    </a>
                                    @if(Auth::user()->hasModuleAccess('proprietaires', 'collaborator'))
                                    <a href="{{ route('proprietaires.edit', $proprietaire) }}" class="w-8 h-8 rounded-lg bg-slate-50 text-slate-600 flex items-center justify-center hover:bg-slate-900 hover:text-white transition-all shadow-sm">
                                        <i class="fas fa-edit text-xs"></i>
                                    </a>
                                    @endif
                                    @if(Auth::user()->hasModuleAccess('proprietaires', 'manager'))
                                    <form method="POST" action="{{ route('proprietaires.destroy', $proprietaire) }}" onsubmit="return confirm('Supprimer ce propriétaire ?')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-8 h-8 rounded-lg bg-red-50 text-red-500 flex items-center justify-center hover:bg-red-500 hover:text-white transition-all shadow-sm">
                                            <i class="fas fa-trash text-xs"></i>
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

        {{-- Pagination --}}
        <div class="mt-6">
            {{ $proprietaires->links() }}
        </div>
    </div>
</x-app-layout>