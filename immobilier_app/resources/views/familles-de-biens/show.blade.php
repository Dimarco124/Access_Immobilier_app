<x-app-layout>
    @php
        $isIndiv = $familleDeBien->isIndividuelle();
        $uniteIndiv = $isIndiv ? $familleDeBien->biens()->with(['contrats.locataire.user'])->first() : null;
        $contratActif = $uniteIndiv ? $uniteIndiv->contrats->where('statut', 'Actif')->first() : null;
    @endphp

    <x-slot name="header">
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 w-full">
            <div>
                <div class="flex items-center gap-2">
                    <span class="px-2.5 py-0.5 text-[10px] font-black rounded-full uppercase tracking-wider {{ $isIndiv ? 'bg-blue-100 text-blue-700' : 'bg-indigo-100 text-indigo-700' }}">
                        {{ $isIndiv ? 'Bien Individuel' : 'Propriété Collective' }}
                    </span>
                    <span class="text-xs text-slate-400 font-bold uppercase">{{ $familleDeBien->type_famille }}</span>
                </div>
                <h2 class="font-black text-2xl md:text-3xl text-slate-900 tracking-tight uppercase mt-1">{{ $familleDeBien->nom_famille }}</h2>
                <p class="text-slate-500 font-medium text-xs mt-0.5"><i class="fas fa-map-marker-alt text-slate-400 mr-1"></i> {{ $familleDeBien->adresse }}</p>
            </div>
            <div class="flex flex-wrap items-center gap-2.5">
                <a href="{{ route('proprietes.edit', $familleDeBien) }}" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-black uppercase tracking-wider transition-colors inline-flex items-center gap-2">
                    <i class="fas fa-edit"></i> Modifier
                </a>
                @if(!$isIndiv)
                <a href="{{ route('biens.create') }}?famille={{ $familleDeBien->id }}" class="btn-premium bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 inline-flex items-center gap-2 rounded-xl text-xs font-black uppercase tracking-wider shadow-lg shadow-blue-500/20 transition-all">
                    <i class="fas fa-plus"></i>
                    <span>Ajouter une Unité Locative</span>
                </a>
                @endif
                <a href="{{ route('proprietes.index') }}" class="px-4 py-2.5 bg-white border border-slate-200 text-slate-600 hover:text-slate-900 rounded-xl text-xs font-black uppercase tracking-wider transition-colors inline-flex items-center gap-1.5">
                    <i class="fas fa-arrow-left"></i> Retour
                </a>
            </div>
        </div>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Colonne gauche : Infos Propriété -->
        <div class="lg:col-span-1 space-y-6">
            <div class="premium-card p-6 bg-white rounded-2xl border border-slate-100 shadow-xs">
                <h3 class="font-black text-xs text-slate-400 uppercase tracking-widest mb-4">Fiche Propriété</h3>
                <div class="space-y-4 text-sm">
                    <div class="flex flex-col">
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-wider">Propriétaire Bailleur</span>
                        <span class="font-bold text-slate-800 mt-0.5">{{ $familleDeBien->proprietaire->user->name ?? '-' }}</span>
                        <span class="text-[11px] text-slate-400">{{ $familleDeBien->proprietaire->user->email ?? '' }}</span>
                    </div>
                    <div class="flex flex-col pt-3 border-t border-slate-50">
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-wider">Adresse</span>
                        <span class="font-bold text-slate-800 mt-0.5">{{ $familleDeBien->adresse }}</span>
                    </div>
                    <div class="flex flex-col pt-3 border-t border-slate-50">
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-wider">Catégorie</span>
                        <span class="font-bold text-slate-800 mt-0.5">{{ $familleDeBien->type_famille }}</span>
                    </div>
                    @if($familleDeBien->description)
                    <div class="flex flex-col pt-3 border-t border-slate-50">
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-wider">Description</span>
                        <p class="text-xs text-slate-600 mt-1 leading-relaxed">{{ $familleDeBien->description }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Carte Statut / Résumé -->
            <div class="premium-card p-6 bg-white rounded-2xl border border-slate-100 shadow-xs">
                @if($isIndiv && $uniteIndiv)
                    <h3 class="font-black text-xs text-slate-400 uppercase tracking-widest mb-3">Statut Locatif</h3>
                    <div class="p-4 rounded-xl {{ $uniteIndiv->statut === 'Loué' ? 'bg-blue-50 border border-blue-100' : 'bg-emerald-50 border border-emerald-100' }}">
                        <p class="text-[10px] font-black uppercase tracking-wider {{ $uniteIndiv->statut === 'Loué' ? 'text-blue-700' : 'text-emerald-700' }}">
                            {{ $uniteIndiv->statut }}
                        </p>
                        <p class="text-xl font-black text-slate-900 mt-1">
                            {{ number_format($uniteIndiv->loyer_mensuel, 0, ',', ' ') }} {{ Auth::user()->agence->devise }}
                            <span class="text-xs font-medium text-slate-400">/ mois</span>
                        </p>
                    </div>
                    @if($contratActif)
                        <div class="mt-4 pt-4 border-t border-slate-100 text-xs">
                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-wider">Locataire actuel</span>
                            <p class="font-black text-slate-800 mt-1">{{ $contratActif->locataire->user->name ?? 'N/A' }}</p>
                            <a href="{{ route('contrats.show', $contratActif) }}" class="text-blue-600 hover:underline font-bold text-[11px] mt-1 inline-block">
                                Voir le contrat de bail &rarr;
                            </a>
                        </div>
                    @endif
                @else
                    <h3 class="font-black text-xs text-slate-400 uppercase tracking-widest mb-3">Unités Locatives</h3>
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-3xl font-black text-slate-900">{{ $familleDeBien->biens->count() }}</p>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mt-0.5">Portes / Lots au total</p>
                        </div>
                        <div class="w-12 h-12 bg-indigo-50 rounded-2xl flex items-center justify-center text-indigo-600">
                            <i class="fas fa-door-open text-xl"></i>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Colonne droite -->
        <div class="lg:col-span-2">
            @if($isIndiv && $uniteIndiv)
                <!-- Vue détaillée du bien individuel -->
                <div class="premium-card bg-white rounded-2xl border border-slate-100 shadow-xs p-6 space-y-6">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                        <div>
                            <h3 class="font-black text-base text-slate-900 uppercase tracking-tight">Paramètres du Bien Individuel</h3>
                            <p class="text-xs text-slate-400 font-medium">Ce bien est directement prêt pour les contrats et les visites</p>
                        </div>
                        <a href="{{ route('biens.show', $uniteIndiv) }}" class="px-4 py-2 bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white rounded-xl text-xs font-black uppercase tracking-wider transition-all inline-flex items-center gap-1.5">
                            <i class="fas fa-cog"></i> Gérer l'unité
                        </a>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="p-4 bg-slate-50 rounded-xl border border-slate-100">
                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-wider">Loyer Mensuel</span>
                            <p class="text-lg font-black text-slate-900 mt-1">{{ number_format($uniteIndiv->loyer_mensuel, 0, ',', ' ') }} {{ Auth::user()->agence->devise }}</p>
                        </div>
                        <div class="p-4 bg-slate-50 rounded-xl border border-slate-100">
                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-wider">Commission Agence</span>
                            <p class="text-lg font-black text-indigo-600 mt-1">{{ $uniteIndiv->taux_commission_agence ?? 10 }}%</p>
                        </div>
                        <div class="p-4 bg-slate-50 rounded-xl border border-slate-100">
                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-wider">Frais de Visite</span>
                            <p class="text-lg font-black text-slate-900 mt-1">{{ number_format($uniteIndiv->prix_visite ?? 0, 0, ',', ' ') }} {{ Auth::user()->agence->devise }}</p>
                        </div>
                    </div>

                    @if($uniteIndiv->contrats->count() > 0)
                        <div>
                            <h4 class="text-xs font-black text-slate-400 uppercase tracking-widest mb-3">Historique des Contrats</h4>
                            <div class="divide-y divide-slate-100 border border-slate-100 rounded-xl overflow-hidden">
                                @foreach($uniteIndiv->contrats as $c)
                                <div class="p-3.5 flex items-center justify-between text-xs hover:bg-slate-50 transition-colors">
                                    <div>
                                        <p class="font-black text-slate-800">{{ $c->locataire->user->name ?? 'Locataire' }}</p>
                                        <p class="text-[10px] text-slate-400 mt-0.5">Du {{ $c->date_debut ? $c->date_debut->format('d/m/Y') : '' }} au {{ $c->date_fin ? $c->date_fin->format('d/m/Y') : 'Indéterminée' }}</p>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <span class="px-2 py-0.5 text-[9px] font-black rounded uppercase {{ $c->statut === 'Actif' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' }}">
                                            {{ $c->statut }}
                                        </span>
                                        <a href="{{ route('contrats.show', $c) }}" class="text-blue-600 hover:text-blue-800 font-bold">Voir</a>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            @else
                <!-- Vue collective : tableau des unités locatives -->
                <div class="premium-card bg-white rounded-2xl border border-slate-100 shadow-xs overflow-hidden">
                    <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                        <div>
                            <h3 class="font-black text-sm text-slate-900 uppercase tracking-tight">Unités Locatives (Lots)</h3>
                            <p class="text-xs text-slate-400 font-medium">Logements, appartements et commerces de cette propriété</p>
                        </div>
                        <a href="{{ route('biens.create') }}?famille={{ $familleDeBien->id }}" class="btn-premium bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl text-xs font-black uppercase tracking-wider inline-flex items-center gap-1.5 shadow-sm">
                            <i class="fas fa-plus"></i>
                            <span>Nouvelle Unité</span>
                        </a>
                    </div>
                    
                    @if($familleDeBien->biens->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-100 text-sm">
                            <thead class="bg-slate-50/80">
                                <tr>
                                    <th class="text-left text-[10px] font-black text-slate-400 uppercase tracking-widest px-6 py-4">Nom de l'Unité</th>
                                    <th class="text-left text-[10px] font-black text-slate-400 uppercase tracking-widest px-6 py-4">Type</th>
                                    <th class="text-left text-[10px] font-black text-slate-400 uppercase tracking-widest px-6 py-4">Loyer Mensuel</th>
                                    <th class="text-left text-[10px] font-black text-slate-400 uppercase tracking-widest px-6 py-4">Statut</th>
                                    <th class="text-right text-[10px] font-black text-slate-400 uppercase tracking-widest px-6 py-4">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 bg-white">
                                @foreach($familleDeBien->biens as $bien)
                                <tr class="hover:bg-slate-50/70 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-xs font-black">
                                                <i class="fas fa-door-closed"></i>
                                            </div>
                                            <div>
                                                <a href="{{ route('biens.show', $bien) }}" class="font-black text-slate-900 hover:text-blue-600 transition-colors text-sm">
                                                    {{ $bien->nom_bien }}
                                                </a>
                                                <span class="block text-[10px] text-slate-400">{{ $bien->adresse }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-xs font-bold text-slate-600">
                                        {{ $bien->type_bien }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-xs font-black text-slate-900">
                                        {{ number_format($bien->loyer_mensuel, 0, ',', ' ') }} {{ Auth::user()->agence->devise }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2.5 py-1 inline-flex text-[9px] font-black rounded-lg uppercase tracking-wider
                                            @if($bien->statut === 'Disponible') bg-emerald-50 text-emerald-700
                                            @elseif($bien->statut === 'Loué') bg-blue-50 text-blue-700
                                            @else bg-amber-50 text-amber-700 @endif">
                                            {{ $bien->statut }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <div class="flex items-center justify-end gap-1.5">
                                            <a href="{{ route('biens.show', $bien) }}" class="w-8 h-8 rounded-lg bg-slate-50 hover:bg-blue-600 text-slate-600 hover:text-white flex items-center justify-center transition-all" title="Gérer">
                                                <i class="fas fa-cog text-xs"></i>
                                            </a>
                                            <a href="{{ route('biens.edit', $bien) }}" class="w-8 h-8 rounded-lg bg-slate-50 hover:bg-slate-900 text-slate-600 hover:text-white flex items-center justify-center transition-all" title="Modifier">
                                                <i class="fas fa-edit text-xs"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="p-12 text-center bg-slate-50/30">
                        <div class="w-16 h-16 rounded-2xl bg-indigo-50 flex items-center justify-center text-indigo-400 mx-auto mb-4">
                            <i class="fas fa-door-open text-2xl"></i>
                        </div>
                        <p class="text-slate-400 font-bold uppercase tracking-wider text-xs mb-4">Aucune unité locative dans cette propriété</p>
                        <a href="{{ route('biens.create') }}?famille={{ $familleDeBien->id }}" class="btn-premium bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 uppercase tracking-wider text-xs font-black rounded-xl shadow-lg shadow-blue-500/20 transition-all inline-flex items-center gap-2">
                            <i class="fas fa-plus"></i>
                            <span>Ajouter une Unité Locative</span>
                        </a>
                    </div>
                    @endif
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
