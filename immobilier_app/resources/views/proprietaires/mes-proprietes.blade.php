<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 w-full">
            <div>
                <h2 class="font-black text-2xl text-slate-900 tracking-tight uppercase">Mes propriétés</h2>
                <p class="text-[11px] text-slate-500 font-bold uppercase tracking-widest mt-1">Suivi de tous vos biens immobiliers</p>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        <!-- Cards de résumé -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="premium-card p-6 bg-white border-l-4 border-l-indigo-600">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-slate-400 text-[9px] uppercase font-black tracking-widest mb-1">Total Unités</p>
                        <p class="text-3xl font-black text-slate-900">{{ $total_biens ?? 0 }}</p>
                    </div>
                    <div class="w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center text-indigo-600">
                        <i class="fas fa-building text-xl"></i>
                    </div>
                </div>
            </div>
            <div class="premium-card p-6 bg-white border-l-4 border-l-emerald-600">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-slate-400 text-[9px] uppercase font-black tracking-widest mb-1">Unités Louées</p>
                        <p class="text-3xl font-black text-emerald-600">{{ $biens_loues ?? 0 }}</p>
                    </div>
                    <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center text-emerald-600">
                        <i class="fas fa-check text-xl"></i>
                    </div>
                </div>
            </div>
            <div class="premium-card p-6 bg-white border-l-4 border-l-emerald-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-slate-400 text-[9px] uppercase font-black tracking-widest mb-1">Unités Disponibles</p>
                        <p class="text-3xl font-black text-emerald-600">{{ $biens_disponibles ?? 0 }}</p>
                    </div>
                    <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center text-emerald-600">
                        <i class="fas fa-home text-xl"></i>
                    </div>
                </div>
            </div>
            <div class="premium-card p-6 bg-white border-l-4 border-l-amber-600">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-slate-400 text-[9px] uppercase font-black tracking-widest mb-1">Taux d'occupation</p>
                        <p class="text-3xl font-black text-amber-600">{{ $taux_occupation ?? 0 }}%</p>
                    </div>
                    <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center text-amber-600">
                        <i class="fas fa-chart-pie text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Liste des familles de biens -->
        @if($familles && $familles->count() > 0)
        <div class="space-y-6">
            @foreach($familles as $famille)
            <div class="premium-card bg-white overflow-hidden">
                <div class="p-6 border-b border-slate-100 flex items-center justify-between bg-slate-50/30">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-indigo-600 rounded-xl flex items-center justify-center text-white">
                            <i class="fas fa-building"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-black text-slate-900">{{ $famille->nom_famille }}</h3>
                            <p class="text-xs text-slate-500"><i class="fas fa-map-marker-alt mr-1"></i> {{ $famille->adresse }}</p>
                        </div>
                    </div>
                    <span class="px-4 py-1.5 text-[10px] font-black rounded-full uppercase bg-indigo-100 text-indigo-700">
                        {{ $famille->type_famille }}
                    </span>
                </div>

                <div class="p-6">
                    @if($famille->biens && $famille->biens->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($famille->biens as $bien)
                        <div class="border border-slate-100 rounded-xl p-5 bg-slate-50/50">
                            <div class="flex items-start justify-between mb-4">
                                <h4 class="font-black text-slate-900">{{ $bien->nom_bien }}</h4>
                                <span class="px-2 py-0.5 text-[8px] font-black rounded-full uppercase {{ $bien->statut == 'Loué' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                                    {{ $bien->statut }}
                                </span>
                            </div>
                            <p class="text-xs text-slate-500 mb-4"><i class="fas fa-map-marker-alt mr-1"></i> {{ $bien->adresse }}</p>
                            <p class="text-xs text-slate-500 mb-1"><i class="fas fa-home mr-1"></i> {{ $bien->type_bien }}</p>
                            
                            <!-- Contrat actif si existe -->
                            @if($bien->contrats && $bien->contrats->where('statut', 'Actif')->count() > 0)
                            @php
                            $contratActif = $bien->contrats->where('statut', 'Actif')->first();
                            @endphp
                            @if($contratActif)
                            <div class="mt-4 pt-4 border-t border-slate-100">
                                <p class="text-[9px] font-black uppercase text-slate-400 mb-1">Loyer mensuel</p>
                                <p class="text-lg font-bold text-emerald-600">{{ number_format($contratActif->montant_loyer_actuel, 0, ',', ' ') }} {{ Auth::user()->agence->devise ?? 'XOF' }}</p>
                                @if($contratActif->locataire && $contratActif->locataire->user)
                                <p class="text-xs text-slate-500 mt-1"><i class="fas fa-user mr-1"></i> {{ $contratActif->locataire->user->name }}</p>
                                @endif
                            </div>
                            @endif
                            @endif
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center py-8">
                        <i class="fas fa-building text-4xl text-slate-200 mb-3"></i>
                        <p class="text-sm text-slate-400 font-bold">Aucune unité locative dans cette propriété</p>
                    </div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="premium-card bg-white p-12 text-center">
            <i class="fas fa-building text-5xl text-slate-200 mb-4"></i>
            <h3 class="text-xl font-black text-slate-900 mb-2">Aucune propriété enregistrée</h3>
            <p class="text-slate-500 mb-6">Vos biens immobiliers apparaîtront ici une fois ajoutés par l'agence</p>
            <a href="{{ route('messages.index') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-indigo-600 text-white text-[10px] font-black uppercase tracking-widest rounded-lg hover:bg-indigo-700 transition-all">
                <i class="fas fa-envelope"></i>
                Contacter l'agence
            </a>
        </div>
        @endif
    </div>
</x-app-layout>