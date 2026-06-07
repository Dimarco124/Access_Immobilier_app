<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-bold text-3xl text-gray-900">{{ __('Mes Contrats') }}</h2>
            <p class="text-gray-600 mt-1">Suivi de vos contrats de location actifs</p>
        </div>
    </x-slot>

    <div class="py-12 bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-6 p-4 bg-green-50 border-l-4 border-green-500 text-green-700 rounded-r-lg">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                        <span>{{ session('success') }}</span>
                    </div>
                </div>
            @endif

            @if($contrats->count() > 0)
            <div class="grid gap-6">
                @foreach($contrats as $contrat)
                <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition duration-300 overflow-hidden">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 p-6">
                        <!-- Infos Bien -->
                        <div>
                            <p class="text-gray-500 text-sm font-semibold uppercase">Bien</p>
                            <p class="text-lg font-bold text-gray-900 mt-1">{{ $contrat->bien->nom_bien }}</p>
                            <p class="text-sm text-gray-600 mt-1">{{ $contrat->bien->type_bien }}</p>
                        </div>

                        <!-- Propriétaire -->
                        <div>
                            <p class="text-gray-500 text-sm font-semibold uppercase">Propriétaire</p>
                            <p class="text-lg font-bold text-gray-900 mt-1">{{ $contrat->bien->proprietaire->user->name }}</p>
                        </div>

                        <!-- Loyer -->
                        <div>
                            <p class="text-gray-500 text-sm font-semibold uppercase">Loyer Mensuel</p>
                            <p class="text-2xl font-bold text-green-600 mt-1">{{ number_format($contrat->montant_loyer_actuel, 0, ',', ' ') }}</p>
                            <p class="text-sm text-gray-600">{{ Auth::user()->agence->devise }}</p>
                        </div>

                        <!-- Statut & Dates -->
                        <div>
                            <p class="text-gray-500 text-sm font-semibold uppercase">Contrat</p>
                            <div class="mt-2 space-y-2">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold
                                    @if($contrat->statut === 'Actif') bg-green-100 text-green-800
                                    @elseif($contrat->statut === 'Terminé') bg-gray-100 text-gray-800
                                    @else bg-red-100 text-red-800 @endif">
                                    {{ $contrat->statut }}
                                </span>
                                <p class="text-xs text-gray-600">Début: {{ \Carbon\Carbon::parse($contrat->date_debut)->format('d/m/Y') }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Détails additionnels -->
                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                            <div>
                                <p class="text-gray-600">Caution</p>
                                <p class="font-semibold text-gray-900">{{ number_format($contrat->montant_caution, 0, ',', ' ') }} {{ Auth::user()->agence->devise }}</p>
                            </div>
                            <div>
                                <p class="text-gray-600">Mois d'avance</p>
                                <p class="font-semibold text-gray-900">{{ $contrat->nombre_mois_avance }}</p>
                            </div>
                            <div>
                                <p class="text-gray-600">Frais dossier</p>
                                <p class="font-semibold text-gray-900">{{ number_format($contrat->frais_dossier, 0, ',', ' ') }} {{ Auth::user()->agence->devise }}</p>
                            </div>
                            <div class="text-right">
                                <a href="{{ route('contrats.show', $contrat) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold transition duration-200">
                                    Détails
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-8">
                {{ $contrats->links() }}
            </div>
            @else
            <div class="bg-white rounded-2xl shadow-lg p-12 text-center">
                <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <p class="text-xl text-gray-600 font-semibold">Aucun contrat enregistré</p>
                <p class="text-gray-500 mt-2">Vos contrats apparaîtront ici une fois créés</p>
            </div>
            @endif
        </div>
    </div>
</x-app-layout>
