<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 w-full">
            <div>
                <h2 class="font-black text-2xl md:text-2xl text-slate-900 uppercase tracking-tight">
                    {{ __('Tickets d\'Intervention') }}
                </h2>
                <p class="text-[11px] text-slate-500 font-bold uppercase tracking-widest mt-1">Suivi des demandes de maintenance et interventions</p>
            </div>
            @if(Auth::user()->hasRole('locataire'))
            <div>
                <a href="{{ route('tickets.create') }}" class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-[10px] font-black uppercase tracking-widest rounded-xl transition-all shadow-lg shadow-indigo-200 inline-flex items-center gap-2">
                    <i class="fas fa-plus"></i> Nouvelle demande
                </a>
            </div>
            @endif
        </div>
    </x-slot>

    <div class="space-y-5">
        <!-- Filtres de recherche -->
        <div class="bg-white p-4 md:p-6 rounded-xl shadow-sm border border-slate-100">
            <form action="{{ route('tickets.index') }}" method="GET">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                    <div>
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Objet / Sujet</label>
                        <input type="text" name="objet" value="{{ request('objet') }}" placeholder="Sujet de l'intervention" 
                            class="w-full px-3 py-2 border border-slate-200 rounded-lg bg-slate-50 placeholder-slate-400 focus:outline-none focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm transition-colors">
                    </div>
                    @if(!Auth::user()->hasRole('locataire'))
                    <div>
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Locataire</label>
                        <input type="text" name="locataire" value="{{ request('locataire') }}" placeholder="Nom du locataire" 
                            class="w-full px-3 py-2 border border-slate-200 rounded-lg bg-slate-50 placeholder-slate-400 focus:outline-none focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm transition-colors">
                    </div>
                    @else
                    <div>
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Bien</label>
                        <input type="text" name="bien" value="{{ request('bien') }}" placeholder="Nom du bien" 
                            class="w-full px-3 py-2 border border-slate-200 rounded-lg bg-slate-50 placeholder-slate-400 focus:outline-none focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm transition-colors">
                    </div>
                    @endif
                    <div>
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Priorité</label>
                        <select name="priorite" class="w-full px-3 py-2 border border-slate-200 rounded-lg bg-slate-50 focus:outline-none focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm transition-colors">
                            <option value="">Toutes</option>
                            <option value="Faible" {{ request('priorite') == 'Faible' ? 'selected' : '' }}>Faible</option>
                            <option value="Moyenne" {{ request('priorite') == 'Moyenne' ? 'selected' : '' }}>Moyenne</option>
                            <option value="Haute" {{ request('priorite') == 'Haute' ? 'selected' : '' }}>Haute</option>
                            <option value="Urgente" {{ request('priorite') == 'Urgente' ? 'selected' : '' }}>Urgente</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Statut</label>
                        <select name="statut" class="w-full px-3 py-2 border border-slate-200 rounded-lg bg-slate-50 focus:outline-none focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm transition-colors">
                            <option value="">Tous</option>
                            <option value="Ouvert" {{ request('statut') == 'Ouvert' ? 'selected' : '' }}>Ouvert</option>
                            <option value="En cours" {{ request('statut') == 'En cours' ? 'selected' : '' }}>En cours</option>
                            <option value="Résolu" {{ request('statut') == 'Résolu' ? 'selected' : '' }}>Résolu</option>
                            <option value="Fermé" {{ request('statut') == 'Fermé' ? 'selected' : '' }}>Fermé</option>
                        </select>
                    </div>
                </div>
                <div class="flex items-center gap-3 mt-4">
                    <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-black uppercase tracking-widest rounded-lg transition-colors shadow-sm inline-flex items-center gap-2">
                        <i class="fas fa-search"></i> Rechercher
                    </button>
                    @if(request()->hasAny(['objet', 'locataire', 'bien', 'priorite', 'statut']))
                    <a href="{{ route('tickets.index') }}" class="px-5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-black uppercase tracking-widest rounded-lg transition-colors inline-flex items-center gap-2">
                        <i class="fas fa-times"></i> Effacer
                    </a>
                    @endif
                    <a href="{{ route('tickets.print', request()->query()) }}" target="_blank" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-black uppercase tracking-widest rounded-lg transition-colors inline-flex items-center gap-2">
                        <i class="fas fa-print"></i> Imprimer
                    </a>
                </div>
            </form>
        </div>

        @if (session('success'))
        <div class="premium-card bg-emerald-50 border-l-4 border-emerald-600 p-4 flex items-center gap-3">
            <i class="fas fa-check-circle text-emerald-600 text-lg shrink-0"></i>
            <p class="text-sm font-black text-emerald-800">{{ session('success') }}</p>
        </div>
        @endif

        {{-- Stats Cards (admin) --}}
        @if(Auth::user()->hasRole('admin'))
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 md:gap-5">
            <div class="premium-card p-4 md:p-6 bg-white border-l-4 border-l-indigo-600">
                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Nouveaux</p>
                <p class="text-2xl font-black text-slate-900">{{ $tickets->where('statut', 'Nouveau')->count() }}</p>
            </div>
            <div class="premium-card p-4 md:p-6 bg-white border-l-4 border-l-amber-600">
                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">En cours</p>
                <p class="text-2xl font-black text-slate-900">{{ $tickets->where('statut', 'En cours')->count() }}</p>
            </div>
            <div class="premium-card p-4 md:p-6 bg-white border-l-4 border-l-red-600">
                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Critiques</p>
                <p class="text-2xl font-black text-slate-900">{{ $tickets->where('priorite', 'Critique')->where('statut', '!=', 'Résolu')->count() }}</p>
            </div>
            <div class="premium-card p-4 md:p-6 bg-white border-l-4 border-l-emerald-600">
                <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-1">Résolus</p>
                <p class="text-2xl font-black text-slate-900">{{ $tickets->where('statut', 'Résolu')->count() }}</p>
            </div>
        </div>
        @endif

        {{-- Liste des Tickets --}}
        <div class="premium-card bg-white overflow-hidden">
            <div class="p-4 md:p-6 border-b border-slate-100">
                <h3 class="text-sm font-black text-slate-900 uppercase tracking-tight">Toutes les demandes</h3>
            </div>

            @if($tickets->isEmpty())
            <div class="text-center py-12 text-slate-400">
                <i class="fas fa-inbox text-4xl mb-3"></i>
                <p class="text-sm font-medium">Aucune demande d'intervention</p>
            </div>
            @else

            {{-- VUE MOBILE : cards --}}
            <div class="md:hidden divide-y divide-slate-200">
                @foreach($tickets as $ticket)
                <div class="p-4 space-y-3">
                    <div class="flex items-start gap-3 mb-2">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-bold text-slate-900 truncate">#{{ $ticket->id }} — {{ $ticket->objet }}</p>
                            @if($ticket->locataire)
                            <p class="text-xs text-slate-500 mt-0.5 truncate">{{ $ticket->locataire->user->name }}</p>
                            @endif
                            @if($ticket->bien)
                            <p class="text-[10px] text-slate-400 truncate"><i class="fas fa-building mr-1"></i>{{ $ticket->bien->nom_bien }}</p>
                            @endif
                        </div>
                    </div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="px-2 py-0.5 text-[10px] font-black uppercase rounded-full whitespace-nowrap {{ $ticket->statut == 'Résolu' ? 'bg-emerald-100 text-emerald-700' : ($ticket->statut == 'En cours' ? 'bg-amber-100 text-amber-700' : 'bg-slate-100 text-slate-700') }}">
                            {{ $ticket->statut }}
                        </span>
                        <span class="px-2 py-0.5 text-[10px] font-black uppercase rounded-full whitespace-nowrap {{ $ticket->priorite == 'Critique' ? 'bg-red-100 text-red-700' : ($ticket->priorite == 'Urgent' ? 'bg-orange-100 text-orange-700' : 'bg-blue-100 text-blue-700') }}">
                            {{ $ticket->priorite }}
                        </span>
                        @if($ticket->type_intervention)
                        <span class="px-2 py-0.5 text-[9px] font-bold uppercase rounded-md bg-indigo-50 text-indigo-600 whitespace-nowrap">
                            <i class="fas fa-tools mr-1"></i>{{ $ticket->type_intervention }}
                        </span>
                        @endif
                    </div>
                    </div>
                    <div class="flex items-center justify-between">
                        <p class="text-[10px] text-slate-400">{{ $ticket->created_at->translatedFormat('d/m/Y') }}</p>
                        <a href="{{ route('tickets.show', $ticket) }}" class="px-3 py-1.5 text-[10px] font-black text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg uppercase transition-all">
                            <i class="fas fa-eye mr-1"></i> Voir
                        </a>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- VUE DESKTOP : tableau --}}
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-50 whitespace-nowrap px-6 py-5">
                        <tr class="text-left text-[9px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-100 ">
                            <th class="whitespace-nowrap px-6 py-5">Ticket</th>
                            <th class="whitespace-nowrap px-6 py-5">Locataire / Bien</th>
                            <th class="whitespace-nowrap px-6 py-5">Type</th>
                            <th class="whitespace-nowrap px-6 py-5">Priorité</th>
                            <th class="whitespace-nowrap px-6 py-5">Statut</th>
                            <th class="whitespace-nowrap px-6 py-5">Créé le</th>
                            <th class="text-right whitespace-nowrap px-6 py-5">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @foreach($tickets as $ticket)
                        <tr class="group hover:bg-slate-50 transition-colors ">
                            <td class="whitespace-nowrap px-6 py-5">
                                <p class="text-sm font-semibold text-slate-900">#{{ $ticket->id }} - {{ $ticket->objet }}</p>
                                <p class="text-[10px] text-slate-500 mt-1">{{ Str::limit($ticket->description_probleme, 50) }}</p>
                            </td>
                            <td class="whitespace-nowrap px-6 py-5">
                                @if($ticket->locataire)
                                <p class="text-sm font-semibold text-slate-900">{{ $ticket->locataire->user->name }}</p>
                                @endif
                                @if($ticket->bien)
                                <p class="text-[10px] text-slate-500"><i class="fas fa-building mr-1"></i>{{ $ticket->bien->nom_bien }}</p>
                                @endif
                            </td>
                            <td class="whitespace-nowrap px-6 py-5">
                                <p class="text-sm font-semibold text-slate-700">{{ $ticket->type_intervention ?? '-' }}</p>
                            </td>
                            <td class="whitespace-nowrap px-6 py-5">
                                <span class="px-3 py-1 text-[10px] font-black uppercase rounded-full {{ $ticket->priorite == 'Critique' ? 'bg-red-100 text-red-700' : ($ticket->priorite == 'Urgent' ? 'bg-orange-100 text-orange-700' : 'bg-blue-100 text-blue-700') }}">
                                    {{ $ticket->priorite }}
                                </span>
                            </td>
                            <td class="whitespace-nowrap px-6 py-5">
                                <span class="px-3 py-1 text-[10px] font-black uppercase rounded-full {{ $ticket->statut == 'Résolu' ? 'bg-emerald-100 text-emerald-700' : ($ticket->statut == 'En cours' ? 'bg-amber-100 text-amber-700' : 'bg-slate-100 text-slate-700') }}">
                                    {{ $ticket->statut }}
                                </span>
                            </td>
                            <td class="whitespace-nowrap px-6 py-5">
                                <p class="text-sm text-slate-600">{{ $ticket->created_at->translatedFormat('d/m/Y à H:i') }}</p>
                            </td>
                            <td class="text-right whitespace-nowrap px-6 py-5">
                                <a href="{{ route('tickets.show', $ticket) }}" class="inline-flex items-center gap-1 px-4 py-2 text-[10px] font-black text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg uppercase transition-all">
                                    <i class="fas fa-eye mr-1"></i> Voir
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>

        {{-- Pagination --}}
        <div class="mt-6">
            {{ $tickets->links() }}
        </div>
    </div>
</x-app-layout>