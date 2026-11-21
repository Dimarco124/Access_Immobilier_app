<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 w-full">
            <div>
                <h2 class="font-black text-2xl md:text-4xl text-slate-900 tracking-tight uppercase">{{ __('Parc Immobilier') }}</h2>
                <p class="text-slate-500 font-medium mt-1 text-sm">Gérez vos biens, immeubles et locaux commerciaux</p>
            </div>
            <div>
                <a href="{{ route('biens.create') }}" class="btn-premium bg-blue-600 text-white px-5 py-3 md:px-8 md:py-4 inline-flex items-center gap-2 hover:bg-blue-700 shadow-lg shadow-blue-100 transition transform hover:-translate-y-1">
                    <i class="fas fa-plus-circle"></i>
                    <span class="uppercase tracking-widest text-xs font-black">Nouveau Bien</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-8 md:space-y-12">
        @if (session('success'))
        <div class="premium-card bg-green-50 border-green-100 p-4 flex items-center gap-3">
            <div class="w-8 h-8 bg-green-500 rounded-xl flex items-center justify-center text-white shrink-0">
                <i class="fas fa-check text-sm"></i>
            </div>
            <p class="text-green-700 font-bold text-sm">{{ session('success') }}</p>
        </div>
        @endif

        @if($proprietaires->count() > 0)
        @foreach($proprietaires as $proprietaire)
        <div class="space-y-4">
            <!-- En-tête Propriétaire -->
            <div class="flex items-center gap-3 px-1">
                <div class="w-8 h-8 rounded-lg bg-indigo-600 flex items-center justify-center text-white shrink-0">
                    <i class="fas fa-user-tie text-xs"></i>
                </div>
                <h3 class="font-black text-base md:text-lg text-slate-900 uppercase tracking-tight">
                    {{ $proprietaire->user->name }}
                </h3>
                <span class="px-2 py-1 bg-slate-100 text-slate-500 text-[10px] font-black rounded-full uppercase">
                    {{ $proprietaire->biens->count() }} Bien(s)
                </span>
            </div>

            @if($proprietaire->biens->count() > 0)
            <div class="premium-card overflow-hidden">

                {{-- VUE MOBILE : cards --}}
                <div class="md:hidden divide-y divide-slate-200">
                    @foreach($proprietaire->biens as $bien)
                    <div class="p-4 space-y-3">
                        <div class="flex items-start gap-3 mb-2">
                            <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600 shrink-0">
                                <i class="fas fa-building"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <a href="{{ route('biens.show', $bien) }}" class="text-sm font-black text-slate-900 uppercase tracking-tight truncate block">{{ $bien->nom_bien }}</a>
                                <p class="text-xs text-slate-400 mt-0.5 truncate"><i class="fas fa-map-marker-alt mr-1 opacity-50"></i>{{ $bien->adresse }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="px-2 py-1 inline-flex text-[10px] font-black rounded-lg uppercase whitespace-nowrap
                                @if($bien->statut === 'Disponible') bg-green-100 text-green-600
                                @elseif($bien->statut === 'Loué') bg-red-100 text-red-600
                                @else bg-amber-100 text-amber-600 @endif">
                                {{ $bien->statut }}
                            </span>
                            <span class="text-xs text-slate-500 font-bold whitespace-nowrap">{{ $bien->type_bien }}</span>
                            <span class="text-xs text-slate-400 whitespace-nowrap"><span class="font-black text-slate-700">{{ $bien->children->count() }}</span> sous-bien(s)</span>
                        </div>
                        <div class="flex items-center gap-2 justify-end pt-2 border-t border-slate-100">
                            <a href="{{ route('biens.show', $bien) }}" class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center hover:bg-blue-600 hover:text-white transition-all">
                                <i class="fas fa-cog text-sm"></i>
                            </a>
                            <a href="{{ route('biens.edit', $bien) }}" class="w-9 h-9 rounded-xl bg-slate-50 text-slate-600 flex items-center justify-center hover:bg-slate-900 hover:text-white transition-all">
                                <i class="fas fa-edit text-sm"></i>
                            </a>
                            <form method="POST" action="{{ route('biens.destroy', $bien) }}" onsubmit="return confirm('Supprimer ce bien et tous ses sous-biens ?')" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-9 h-9 rounded-xl bg-red-50 text-red-500 flex items-center justify-center hover:bg-red-500 hover:text-white transition-all">
                                    <i class="fas fa-trash text-sm"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                    @endforeach
                </div>

                {{-- VUE DESKTOP : tableau --}}
                <div class="hidden md:block overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50 whitespace-nowrap px-6 py-5">
                            <tr class="">
                                <th class="text-left text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] whitespace-nowrap px-6 py-5">Bien & Localisation</th>
                                <th class="text-left text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] whitespace-nowrap px-6 py-5">Type</th>
                                <th class="text-left text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] whitespace-nowrap px-6 py-5">Unités</th>
                                <th class="text-left text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] whitespace-nowrap px-6 py-5">Statut</th>
                                <th class="text-right text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] whitespace-nowrap px-6 py-5">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-slate-200">
                            @foreach($proprietaire->biens as $bien)
                            <tr class="hover:bg-slate-50 transition-colors group ">
                                <td class="whitespace-nowrap px-6 py-5">
                                    <div class="flex items-center gap-4">
                                        <div class="w-12 h-12 rounded-2xl bg-blue-50 flex items-center justify-center text-blue-600 group-hover:bg-blue-600 group-hover:text-white transition-all">
                                            <i class="fas fa-building text-lg"></i>
                                        </div>
                                        <div>
                                            <a href="{{ route('biens.show', $bien) }}" class="text-sm font-black text-slate-900 hover:text-blue-600 transition-colors uppercase tracking-tight">
                                                {{ $bien->nom_bien }}
                                            </a>
                                            <p class="text-xs text-slate-400 font-medium mt-1">
                                                <i class="fas fa-map-marker-alt mr-1 opacity-50"></i> {{ $bien->adresse }}
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td class="whitespace-nowrap px-6 py-5">
                                    <span class="text-xs font-bold text-slate-600 uppercase tracking-tighter">{{ $bien->type_bien }}</span>
                                </td>
                                <td class="whitespace-nowrap px-6 py-5">
                                    <div class="flex items-center gap-2">
                                        <span class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center text-xs font-black text-slate-600">
                                            {{ $bien->children->count() }}
                                        </span>
                                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Sous-biens</span>
                                    </div>
                                </td>
                                <td class="whitespace-nowrap px-6 py-5">
                                    <span class="px-4 py-1.5 inline-flex text-[10px] font-black rounded-xl uppercase tracking-widest
                                                @if($bien->statut === 'Disponible') bg-green-100 text-green-600
                                                @elseif($bien->statut === 'Loué') bg-red-100 text-red-600
                                                @else bg-amber-100 text-amber-600 @endif">
                                        {{ $bien->statut }}
                                    </span>
                                </td>
                                <td class="whitespace-nowrap text-right whitespace-nowrap px-6 py-5">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('biens.show', $bien) }}" class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center hover:bg-blue-600 hover:text-white transition-all shadow-sm" title="Gérer">
                                            <i class="fas fa-cog text-sm"></i>
                                        </a>
                                        <a href="{{ route('biens.edit', $bien) }}" class="w-10 h-10 rounded-xl bg-slate-50 text-slate-600 flex items-center justify-center hover:bg-slate-900 hover:text-white transition-all shadow-sm" title="Éditer">
                                            <i class="fas fa-edit text-sm"></i>
                                        </a>
                                        <form method="POST" action="{{ route('biens.destroy', $bien) }}" onsubmit="return confirm('Supprimer ce bien et tous ses sous-biens ?')" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="w-10 h-10 rounded-xl bg-red-50 text-red-500 flex items-center justify-center hover:bg-red-500 hover:text-white transition-all shadow-sm" title="Supprimer">
                                                <i class="fas fa-trash text-sm"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @else
            <div class="premium-card p-8 md:p-12 bg-slate-50 border-dashed border-2 border-slate-200 flex flex-col items-center justify-center text-center">
                <div class="w-12 h-12 rounded-full bg-slate-100 flex items-center justify-center text-slate-300 mb-3">
                    <i class="fas fa-city text-xl"></i>
                </div>
                <p class="text-slate-400 font-bold uppercase tracking-widest text-xs">Aucun bien enregistré pour ce bailleur</p>
            </div>
            @endif
        </div>
        @endforeach
        @else
        <div class="premium-card p-12 md:p-20 flex flex-col items-center justify-center text-center">
            <div class="w-16 h-16 md:w-24 md:h-24 rounded-3xl bg-blue-50 flex items-center justify-center text-blue-200 mb-6 md:mb-8">
                <i class="fas fa-building text-3xl md:text-4xl"></i>
            </div>
            <h3 class="text-xl md:text-2xl font-black text-slate-900 uppercase tracking-tight mb-2">Votre parc est vide</h3>
            <p class="text-slate-500 font-medium mb-8 md:mb-10 max-w-sm mx-auto text-sm">Commencez par créer un propriétaire pour pouvoir enregistrer vos premiers biens immobiliers.</p>
            <a href="{{ route('proprietaires.create') }}" class="btn-premium bg-blue-600 text-white px-8 py-4 uppercase tracking-widest text-sm hover:bg-blue-700 shadow-xl shadow-blue-200 transition transform hover:-translate-y-1">
                Créer un propriétaire
            </a>
        </div>
        @endif
    </div>
</x-app-layout>