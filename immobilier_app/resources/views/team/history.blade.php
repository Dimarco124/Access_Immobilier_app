<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-black text-2xl text-slate-900 uppercase tracking-tight">Historique des Accès</h2>
                <p class="text-xs text-slate-500 font-bold uppercase tracking-widest mt-1">{{ $teamMember->nom_complet }}</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('team.edit', $teamMember) }}" class="px-4 py-2 bg-blue-50 border border-blue-200 text-blue-700 font-bold text-xs rounded-xl hover:bg-blue-100 transition-colors flex items-center gap-1.5">
                    <i class="fas fa-edit"></i>
                    Modifier
                </a>
                <a href="{{ route('team.index') }}" class="px-4 py-2 bg-white border border-slate-200 text-slate-700 font-bold text-xs rounded-xl hover:bg-slate-50 transition-colors flex items-center gap-1.5">
                    <i class="fas fa-arrow-left"></i>
                    Retour
                </a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        <!-- Info du membre -->
        <div class="premium-card p-6 bg-white">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 rounded-full bg-gradient-to-br from-blue-100 to-indigo-100 flex items-center justify-center">
                    <span class="font-black text-blue-600 text-2xl">{{ substr($teamMember->nom_complet, 0, 1) }}</span>
                </div>
                <div class="flex-1">
                    <h3 class="font-black text-xl text-slate-900">{{ $teamMember->nom_complet }}</h3>
                    <p class="text-sm text-slate-600">{{ $teamMember->email }}</p>
                    <div class="flex items-center gap-2 mt-2">
                        @if($teamMember->status === 'active')
                            <span class="px-2 py-1 bg-emerald-100 text-emerald-700 text-xs font-black rounded-full">Actif</span>
                        @elseif($teamMember->status === 'suspended')
                            <span class="px-2 py-1 bg-red-100 text-red-700 text-xs font-black rounded-full">Suspendu</span>
                        @else
                            <span class="px-2 py-1 bg-amber-100 text-amber-700 text-xs font-black rounded-full">En attente</span>
                        @endif
                        <span class="text-xs text-slate-500">{{ $teamMember->modules->count() }} modules assignés</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Timeline des événements -->
        <div class="premium-card p-6 bg-white">
            <h3 class="font-black text-lg text-slate-900 mb-6 uppercase">Journal des Modifications</h3>

            @if($history->count() > 0)
                <div class="space-y-4">
                    @foreach($history as $entry)
                        <div class="flex gap-4 pb-4 border-b border-slate-100 last:border-0">
                            <!-- Icon -->
                            <div class="shrink-0">
                                <div class="w-10 h-10 rounded-full flex items-center justify-center {{ 
                                    $entry->action === 'assigned' ? 'bg-emerald-100' :
                                    ($entry->action === 'revoked' ? 'bg-red-100' :
                                    ($entry->action === 'suspended' ? 'bg-amber-100' :
                                    ($entry->action === 'activated' ? 'bg-blue-100' : 'bg-slate-100')))
                                }}">
                                    @if($entry->action === 'assigned')
                                        <i class="fas fa-plus-circle text-emerald-600"></i>
                                    @elseif($entry->action === 'revoked')
                                        <i class="fas fa-minus-circle text-red-600"></i>
                                    @elseif($entry->action === 'modified')
                                        <i class="fas fa-edit text-blue-600"></i>
                                    @elseif($entry->action === 'suspended')
                                        <i class="fas fa-pause-circle text-amber-600"></i>
                                    @elseif($entry->action === 'activated')
                                        <i class="fas fa-check-circle text-blue-600"></i>
                                    @else
                                        <i class="fas fa-history text-slate-600"></i>
                                    @endif
                                </div>
                            </div>

                            <!-- Content -->
                            <div class="flex-1">
                                <div class="flex items-start justify-between mb-2">
                                    <div>
                                        <p class="font-bold text-slate-900">{{ $entry->action_label }}</p>
                                        @if($entry->module_code)
                                            <p class="text-sm text-slate-600">
                                                Module : <span class="font-bold">{{ $entry->module_code }}</span>
                                            </p>
                                        @endif
                                    </div>
                                    <span class="text-xs text-slate-500 font-medium whitespace-nowrap ml-4">
                                        {{ $entry->created_at->diffForHumans() }}
                                    </span>
                                </div>

                                @if($entry->old_level || $entry->new_level)
                                    <div class="flex items-center gap-2 text-sm mb-2">
                                        @if($entry->old_level)
                                            <span class="px-2 py-0.5 bg-slate-100 text-slate-600 text-xs font-bold rounded">
                                                {{ ucfirst($entry->old_level) }}
                                            </span>
                                        @endif
                                        @if($entry->old_level && $entry->new_level)
                                            <i class="fas fa-arrow-right text-slate-400 text-xs"></i>
                                        @endif
                                        @if($entry->new_level)
                                            <span class="px-2 py-0.5 bg-blue-100 text-blue-700 text-xs font-bold rounded">
                                                {{ ucfirst($entry->new_level) }}
                                            </span>
                                        @endif
                                    </div>
                                @endif

                                @if($entry->notes)
                                    <p class="text-xs text-slate-500 italic">{{ $entry->notes }}</p>
                                @endif

                                <div class="flex items-center gap-2 mt-2">
                                    <i class="fas fa-user text-slate-400 text-xs"></i>
                                    <span class="text-xs text-slate-500">
                                        Par : {{ $entry->changedBy?->name ?? 'Système' }}
                                    </span>
                                    <span class="text-xs text-slate-400">•</span>
                                    <span class="text-xs text-slate-400">
                                        {{ $entry->created_at->format('d/m/Y à H:i') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-6">
                    {{ $history->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <div class="w-16 h-16 mx-auto rounded-full bg-slate-100 flex items-center justify-center mb-4">
                        <i class="fas fa-history text-slate-400 text-2xl"></i>
                    </div>
                    <p class="text-slate-500 font-medium">Aucun historique disponible</p>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
