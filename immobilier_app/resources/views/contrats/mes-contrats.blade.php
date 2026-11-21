<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-black text-2xl md:text-3xl text-slate-900 tracking-tight uppercase">{{ __('Mes Contrats') }}</h2>
            <p class="text-slate-500 text-sm mt-1">Suivi de vos contrats de location actifs</p>
        </div>
    </x-slot>

    <div class="space-y-4 md:space-y-6">
        @if (session('success'))
        <div class="premium-card bg-green-50 border-l-4 border-green-500 p-4 flex items-center gap-3">
            <i class="fas fa-check-circle text-green-600 text-lg shrink-0"></i>
            <span class="text-green-700 font-bold text-sm">{{ session('success') }}</span>
        </div>
        @endif

        @if($contrats->count() > 0)
        <div class="grid gap-4 md:gap-6">
            @foreach($contrats as $contrat)
            <div class="premium-card overflow-hidden">
                {{-- Header card --}}
                <div class="p-4 md:p-6 bg-slate-50 border-b border-slate-100">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 md:w-12 md:h-12 rounded-xl bg-blue-600 flex items-center justify-center text-white shrink-0">
                                <i class="fas fa-file-contract"></i>
                            </div>
                            <div>
                                <h3 class="font-black text-slate-900 text-base md:text-lg uppercase tracking-tight">{{ $contrat->bien->nom_bien }}</h3>
                                <p class="text-xs text-slate-500 font-medium mt-0.5">{{ $contrat->bien->type_bien }}</p>
                            </div>
                        </div>
                        <span class="px-3 py-1 shrink-0 inline-flex items-center rounded-full text-xs font-black
                            @if($contrat->statut === 'Actif') bg-green-100 text-green-700
                            @elseif($contrat->statut === 'Terminé') bg-slate-100 text-slate-600
                            @else bg-red-100 text-red-700 @endif">
                            {{ $contrat->statut }}
                        </span>
                    </div>
                </div>

                {{-- Body --}}
                <div class="p-4 md:p-6 grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Propriétaire</p>
                        <p class="font-bold text-slate-800 mt-1 text-sm">{{ $contrat->bien->proprietaire->user->name }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Loyer mensuel</p>
                        <p class="font-black text-green-600 mt-1 text-lg">{{ number_format($contrat->montant_loyer_actuel, 0, ',', ' ') }}</p>
                        <p class="text-xs text-slate-400">{{ Auth::user()->agence->devise }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Caution</p>
                        <p class="font-semibold text-slate-800 mt-1 text-sm">{{ number_format($contrat->montant_caution, 0, ',', ' ') }} {{ Auth::user()->agence->devise }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Début</p>
                        <p class="font-bold text-slate-800 mt-1 text-sm">{{ \Carbon\Carbon::parse($contrat->date_debut)->format('d/m/Y') }}</p>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="px-4 py-3 md:px-6 bg-slate-50 border-t border-slate-100">
                    <a href="{{ route('contrats.show', $contrat) }}" class="w-full md:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-bold text-xs transition-all">
                        Voir les détails
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
            @endforeach
        </div>

        <div class="mt-4">
            {{ $contrats->links() }}
        </div>
        @else
        <div class="premium-card p-12 flex flex-col items-center justify-center text-center">
            <div class="w-16 h-16 rounded-2xl bg-blue-50 flex items-center justify-center text-blue-200 mb-4">
                <i class="fas fa-file-contract text-3xl"></i>
            </div>
            <p class="text-lg font-black text-slate-600 uppercase tracking-tight">Aucun contrat enregistré</p>
            <p class="text-slate-400 text-sm mt-2">Vos contrats apparaîtront ici une fois créés</p>
        </div>
        @endif
    </div>
</x-app-layout>
