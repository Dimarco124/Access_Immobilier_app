<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Profil Propriétaire : {{ $proprietaire->user->name }}
            </h2>
            <a href="{{ route('proprietaires.index') }}" class="text-gray-600 hover:text-gray-900">← Retour</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Infos Propriétaire -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <span class="text-gray-500 text-sm">Nom</span>
                        <div class="font-bold text-lg">{{ $proprietaire->user->name }}</div>
                    </div>
                    <div>
                        <span class="text-gray-500 text-sm">Email</span>
                        <div class="font-bold">{{ $proprietaire->user->email }}</div>
                    </div>
                    <div>
                        <span class="text-gray-500 text-sm">Téléphone</span>
                        <div class="font-bold">{{ $proprietaire->user->telephone ?? '-' }}</div>
                    </div>
                    <div>
                        <span class="text-gray-500 text-sm">Civilité</span>
                        <div class="font-bold">{{ $proprietaire->civilite ?? '-' }}</div>
                    </div>
                    <div>
                        <span class="text-gray-500 text-sm">Adresse</span>
                        <div class="font-bold">{{ $proprietaire->adresse_residence ?? '-' }}</div>
                    </div>
                    <div>
                        <span class="text-gray-500 text-sm">Pièce d'Identité</span>
                        <div class="font-bold">{{ $proprietaire->numero_piece_identite ?? '-' }}</div>
                    </div>
                    <div>
                        <span class="text-gray-500 text-sm">Pays</span>
                        <div class="font-bold">{{ $proprietaire->pays ?? '-' }}</div>
                    </div>
                    <div>
                        <span class="text-gray-500 text-sm">Ville</span>
                        <div class="font-bold">{{ $proprietaire->ville ?? '-' }}</div>
                    </div>
                </div>
            </div>

            <!-- Biens -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Biens ({{ $proprietaire->biens->count() }})</h3>
                
                @if($proprietaire->biens->count() > 0)
                <div class="space-y-4">
                    @foreach($proprietaire->biens as $bien)
                    <div class="border border-gray-200 rounded p-4">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <div class="font-bold text-lg">{{ $bien->nom_bien }}</div>
                                <div class="text-sm text-gray-600">{{ $bien->localisation }}</div>
                            </div>
                            <div class="text-right">
                                <div class="text-sm text-gray-500">Loyer Mensuel</div>
                                <div class="font-bold text-green-600">
                                    {{ number_format($bien->loyer_mensuel ?? 0, 0, ',', ' ') }} {{ Auth::user()->agence->devise }}
                                </div>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-3 mt-3 text-sm">
                            <div>
                                <span class="text-gray-500">Type</span>
                                <div class="font-semibold">{{ $bien->type_bien }}</div>
                            </div>
                            <div>
                                <span class="text-gray-500">Commission</span>
                                <div class="font-semibold">{{ $bien->taux_commission_agence }}%</div>
                            </div>
                            <div>
                                <span class="text-gray-500">Statut</span>
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    @if($bien->statut === 'Disponible') bg-green-100 text-green-800
                                    @elseif($bien->statut === 'Loué') bg-blue-100 text-blue-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    {{ $bien->statut }}
                                </span>
                            </div>
                        </div>

                        @if($bien->children->count() > 0)
                        <div class="mt-3 pl-4 border-l-2 border-gray-300">
                            <div class="text-sm font-semibold text-gray-700 mb-2">Sous-biens ({{ $bien->children->count() }})</div>
                            <div class="space-y-2">
                                @foreach($bien->children as $subbien)
                                <div class="text-sm bg-gray-50 p-2 rounded">
                                    <div class="font-semibold">{{ $subbien->nom_bien }}</div>
                                    <div class="text-gray-600">
                                        Loyer: {{ number_format($subbien->loyer_mensuel ?? 0, 0, ',', ' ') }} {{ Auth::user()->agence->devise }}
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-gray-500 italic">Aucun bien associé.</p>
                @endif
            </div>

            <!-- Action Buttons -->
            <div class="flex gap-3 justify-end">
                <a href="{{ route('proprietaires.edit', $proprietaire) }}" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                    Modifier
                </a>
                <form method="POST" action="{{ route('proprietaires.destroy', $proprietaire) }}" class="inline" onsubmit="return confirm('Êtes-vous sûr?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700">
                        Supprimer
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
