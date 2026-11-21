<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 w-full">
            <div>
                <h2 class="font-black text-2xl text-slate-900 tracking-tight uppercase">
                    {{ $bien->nom_bien }}
                </h2>
                <p class="text-[11px] text-slate-500 font-bold uppercase tracking-widest">
                    {{ $bien->familleDeBien->nom_famille ?? 'Sans propriété' }} / {{ $bien->type_bien }}
                </p>
            </div>
            
        
            <div class="mt-1">
                <a href="{{ route('biens.edit', $bien) }}" class="px-4 py-2 bg-slate-900 text-white text-[10px] font-black uppercase tracking-widest hover:bg-black transition-all rounded-lg self-start inline-flex">
                    <i class="fas fa-edit mr-1 self-start inline-flex"></i> Modifier
                </a>
                <a href="{{ route('proprietes.show', $bien->famille_de_bien_id) }}" class="px-4 py-2 bg-white border border-slate-200 text-slate-600 text-[10px] font-black uppercase tracking-widest hover:bg-slate-50 transition-all rounded-lg self-start inline-flex">
                    <i class="fas fa-arrow-left mr-1 self-start inline-flex"></i> Retour
                </a>
            </div>
        </div></x-slot>

    <div class="space-y-6">
        @if (session('success'))
        <div class="premium-card bg-emerald-50 border-emerald-100 p-4 flex items-center gap-3">
            <div class="w-8 h-8 bg-emerald-500 rounded-lg flex items-center justify-center text-white text-xs">
                <i class="fas fa-check"></i>
            </div>
            <p class="text-emerald-700 text-xs font-bold">{{ session('success') }}</p>
        </div>
        @endif

        <!-- Infos Unité -->
        <div class="premium-card p-6 bg-white">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="p-4 bg-slate-50 rounded-xl border border-slate-100">
                    <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-1">Propriété</span>
                    <div class="font-bold text-slate-800 text-xs uppercase">{{ $bien->familleDeBien->nom_famille ?? '-' }}</div>
                </div>
                <div class="p-4 bg-slate-50 rounded-xl border border-slate-100">
                    <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-1">Propriétaire</span>
                    <div class="font-bold text-slate-800 text-xs uppercase">{{ $bien->proprietaire->user->name ?? '-' }}</div>
                </div>
                <div class="p-4 bg-slate-50 rounded-xl border border-slate-100">
                    <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-1">Type</span>
                    <div class="font-bold text-slate-800 text-xs uppercase">{{ $bien->type_bien }}</div>
                </div>
                <div class="p-4 bg-slate-50 rounded-xl border border-slate-100">
                    <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest block mb-1">Localisation</span>
                    <div class="font-bold text-slate-800 text-[10px] uppercase truncate">{{ $bien->adresse }}</div>
                </div>
            </div>
        </div>

        <!-- Gestion des Unités -->
        <div class="premium-card overflow-hidden bg-white">
            <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-slate-50/30">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-slate-900 rounded-lg flex items-center justify-center text-white text-xs">
                        <i class="fas fa-layer-group"></i>
                    </div>
                    <h3 class="text-sm font-black text-slate-900 uppercase tracking-tight">Unités Locatives</h3>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead>
                        <tr class="bg-slate-50/50 ">
                            <th class="text-left text-[9px] font-black text-slate-400 uppercase tracking-widest whitespace-nowrap px-6 py-5">Désignation</th>
                            <th class="text-left text-[9px] font-black text-slate-400 uppercase tracking-widest whitespace-nowrap px-6 py-5">Type</th>
                            <th class="text-left text-[9px] font-black text-slate-400 uppercase tracking-widest whitespace-nowrap px-6 py-5">Loyer</th>
                            <th class="text-left text-[9px] font-black text-slate-400 uppercase tracking-widest whitespace-nowrap px-6 py-5">Statut</th>
                            <th class="text-right text-[9px] font-black text-slate-400 uppercase tracking-widest whitespace-nowrap px-6 py-5">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        @forelse($bien->unites as $unite)
                        <tr class="hover:bg-slate-50/50 transition-colors ">
                            <td class="whitespace-nowrap px-6 py-5">
                                <span class="text-xs font-bold text-slate-900 uppercase">{{ $unite->nom_unite }}</span>
                            </td>
                            <td class="whitespace-nowrap px-6 py-5">
                                <span class="text-[10px] font-bold text-slate-500 uppercase">{{ $unite->type_unite }}</span>
                            </td>
                            <td class="whitespace-nowrap px-6 py-5">
                                <span class="text-xs font-black text-slate-900">
                                    {{ number_format($unite->loyer_mensuel ?? 0, 0, ',', ' ') }} <span class="text-[9px] text-slate-400">{{ Auth::user()->agence->devise }}</span>
                                </span>
                            </td>
                            <td class="whitespace-nowrap px-6 py-5">
                                <span class="px-3 py-1 text-[9px] font-black rounded-lg uppercase tracking-widest
                                    @if($unite->statut === 'Disponible') bg-emerald-50 text-emerald-600
                                    @elseif($unite->statut === 'Loué') bg-slate-100 text-slate-600
                                    @else bg-amber-50 text-amber-600 @endif">
                                    {{ $unite->statut }}
                                </span>
                            </td>
                            <td class="whitespace-nowrap text-right whitespace-nowrap px-6 py-5">
                                <div class="flex items-center justify-end gap-2">
                                    <!-- Actions could be added here if needed -->
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr class="">
                            <td colspan="5" class="text-center whitespace-nowrap px-6 py-5">
                                <p class="text-[10px] font-black text-slate-300 uppercase tracking-widest">Aucune unité rattachée</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>