<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 w-full">
            <div class="flex  flex-col items-start gap-4 w-full">
                <div>
                    <h2 class="font-black text-2xl text-slate-900 uppercase tracking-tight">
                        Ticket #{{ $ticket->id }} : {{ $ticket->objet }}
                    </h2>
                    <p class="text-[11px] text-slate-500 font-bold uppercase tracking-widest mt-1">Suivi de votre demande d'intervention</p>
                </div>
                <div class="flex  items-start  gap-3">
                    
                    @if($ticket->statut != 'Résolu' && Auth::user()->hasRole('admin'))
                        <form method="POST" action="{{ route('tickets.close', $ticket) }}" class="inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-[10px] font-black uppercase tracking-widest rounded-lg transition-all shadow-lg shadow-emerald-200">
                                <i class="fas fa-check mr-1"></i> Marquer comme résolu
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        
            <div class="mt-1">
                <a href="{{ route('tickets.index') }}" class="text-xs font-black text-slate-500 hover:text-slate-700 uppercase tracking-widest flex  items-start  gap-2 self-start inline-flex">
                        <i class="fas fa-arrow-left self-start inline-flex"></i> Retour
                    </a>
            </div>
        </div></x-slot>

    <div class="space-y-6">
        @if (session('success'))
            <div class="premium-card bg-emerald-50 border-l-4 border-emerald-600 p-4 flex items-center gap-3">
                <i class="fas fa-check-circle text-emerald-600 text-xl"></i>
                <div>
                    <p class="text-sm font-black text-emerald-800">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Colonne 1: Détails Ticket -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Détails -->
                <div class="premium-card bg-white overflow-hidden">
                    <div class="p-6 border-b border-slate-100 bg-slate-50/30">
                        <h3 class="text-sm font-black text-slate-900 uppercase tracking-tight">Détails</h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 bg-indigo-100 rounded-lg flex items-center justify-center text-indigo-600 mt-0.5">
                                <i class="fas fa-clipboard-list text-sm"></i>
                            </div>
                            <div>
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Statut</p>
                                <span class="inline-block px-3 py-1 text-[10px] font-black uppercase rounded-full {{ $ticket->statut == 'Résolu' ? 'bg-emerald-100 text-emerald-700' : ($ticket->statut == 'En cours' ? 'bg-amber-100 text-amber-700' : 'bg-slate-100 text-slate-700') }}">
                                    {{ $ticket->statut }}
                                </span>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center text-orange-600 mt-0.5">
                                <i class="fas fa-exclamation-triangle text-sm"></i>
                            </div>
                            <div>
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Priorité</p>
                                <span class="inline-block px-3 py-1 text-[10px] font-black uppercase rounded-full {{ $ticket->priorite == 'Critique' ? 'bg-red-100 text-red-700' : ($ticket->priorite == 'Urgent' ? 'bg-orange-100 text-orange-700' : 'bg-blue-100 text-blue-700') }}">
                                    {{ $ticket->priorite }}
                                </span>
                            </div>
                        </div>

                        @if($ticket->type_intervention)
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 bg-emerald-100 rounded-lg flex items-center justify-center text-emerald-600 mt-0.5">
                                    <i class="fas fa-wrench text-sm"></i>
                                </div>
                                <div>
                                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Type d'intervention</p>
                                    <p class="text-sm font-semibold text-slate-900">{{ $ticket->type_intervention }}</p>
                                </div>
                            </div>
                        @endif

                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center text-purple-600 mt-0.5">
                                <i class="fas fa-calendar text-sm"></i>
                            </div>
                            <div>
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Créé le</p>
                                <p class="text-sm font-semibold text-slate-900">{{ $ticket->created_at->translatedFormat('d/m/Y à H:i') }}</p>
                            </div>
                        </div>

                        @if($ticket->bien)
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 bg-slate-100 rounded-lg flex items-center justify-center text-slate-600 mt-0.5">
                                    <i class="fas fa-building text-sm"></i>
                                </div>
                                <div>
                                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Bien concerné</p>
                                    <p class="text-sm font-semibold text-slate-900">{{ $ticket->bien->nom_bien }}</p>
                                    <p class="text-[10px] text-slate-500">{{ $ticket->bien->adresse }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Colonne 2 & 3: Description et Discussion -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Description -->
                <div class="premium-card bg-white overflow-hidden">
                    <div class="p-6 border-b border-slate-100 bg-slate-50/30">
                        <h3 class="text-sm font-black text-slate-900 uppercase tracking-tight">Description du problème</h3>
                    </div>
                    <div class="p-6">
                        <p class="text-sm text-slate-700 leading-relaxed">{{ $ticket->description_probleme }}</p>
                    </div>
                </div>

                <!-- Discussion -->
                <div class="premium-card bg-white overflow-hidden">
                    <div class="p-6 border-b border-slate-100 bg-slate-50/30">
                        <h3 class="text-sm font-black text-slate-900 uppercase tracking-tight">Discussion & Suivi</h3>
                    </div>
                    <div class="p-6 space-y-4">
                        @forelse($ticket->messages as $message)
                            <div class="flex {{ $message->user_id == Auth::id() ? 'justify-end' : 'justify-start' }}">
                                <div class="max-w-md {{ $message->user_id == Auth::id() ? 'bg-indigo-50 border border-indigo-100' : 'bg-slate-50 border border-slate-100' }} p-4 rounded-xl shadow-sm">
                                    <div class="flex items-center justify-between gap-3 mb-2">
                                        <p class="text-xs font-bold text-slate-700">{{ $message->user->name }}</p>
                                        <p class="text-[9px] text-slate-400 italic">{{ $message->created_at->diffForHumans() }}</p>
                                    </div>
                                    <p class="text-sm text-slate-800">{{ $message->contenu_message }}</p>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8 text-slate-400">
                                <i class="fas fa-comments text-3xl mb-2"></i>
                                <p class="text-sm font-medium">Aucun message pour le moment.</p>
                            </div>
                        @endforelse
                    </div>

                    @if($ticket->statut != 'Résolu')
                        <div class="p-6 border-t border-slate-100">
                            <form method="POST" action="{{ route('tickets.message', $ticket) }}">
                                @csrf
                                <div>
                                    <label for="contenu_message" class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2">Votre message</label>
                                    <textarea id="contenu_message" name="contenu_message" class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" rows="3" required placeholder="Tapez votre message ici..."></textarea>
                                </div>
                                <div class="flex justify-end mt-4">
                                    <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-[10px] font-black uppercase tracking-widest rounded-lg shadow-lg shadow-indigo-200 transition-all">
                                        <i class="fas fa-paper-plane mr-1"></i> Envoyer le message
                                    </button>
                                </div>
                            </form>
                        </div>
                    @else
                        <div class="p-6 border-t border-slate-100 bg-emerald-50 text-center">
                            <i class="fas fa-check-circle text-emerald-600 text-2xl mb-2"></i>
                            <p class="text-sm text-emerald-700 font-semibold italic">Ce ticket est fermé. Aucune nouvelle réponse n'est possible.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
