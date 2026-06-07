<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-3xl text-gray-900">Tableau de Bord</h2>
                <p class="text-gray-600 mt-1">Bienvenue, {{ Auth::user()->name }} • {{ Auth::user()->agence->nom }}</p>
            </div>
            <div class="flex items-center gap-3">
                <div class="text-right">
                    <p class="text-sm text-gray-500">Aujourd'hui</p>
                    <p class="font-semibold text-gray-900">{{ \Carbon\Carbon::now()->isoFormat('D MMMM YYYY') }}</p>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-8 bg-gradient-to-br from-blue-50 via-white to-purple-50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            <!-- Stats Principales avec Icônes -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- CA Mensuel -->
                <div class="relative bg-gradient-to-br from-green-500 to-green-600 rounded-2xl shadow-xl p-8 text-white overflow-hidden">
                    <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-white opacity-10 rounded-full"></div>
                    <div class="absolute bottom-0 left-0 -mb-8 -ml-8 w-32 h-32 bg-white opacity-10 rounded-full"></div>
                    <div class="relative z-10">
                        <div class="flex items-center justify-between mb-4">
                            <div class="p-3 bg-white bg-opacity-20 rounded-xl">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <span class="text-green-100 text-sm font-semibold">Ce mois</span>
                        </div>
                        <p class="text-white text-opacity-90 text-sm font-medium mb-2">Chiffre d'Affaires</p>
                        <p class="text-4xl font-bold mb-1">{{ number_format($stats['ca_mensuel'], 0, ',', ' ') }}</p>
                        <p class="text-white text-opacity-75 text-sm">{{ Auth::user()->agence->devise }}</p>
                    </div>
                </div>

                <!-- Factures Impayées -->
                <div class="relative bg-gradient-to-br from-red-500 to-red-600 rounded-2xl shadow-xl p-8 text-white overflow-hidden">
                    <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-white opacity-10 rounded-full"></div>
                    <div class="absolute bottom-0 left-0 -mb-8 -ml-8 w-32 h-32 bg-white opacity-10 rounded-full"></div>
                    <div class="relative z-10">
                        <div class="flex items-center justify-between mb-4">
                            <div class="p-3 bg-white bg-opacity-20 rounded-xl">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                </svg>
                            </div>
                            <span class="text-red-100 text-sm font-semibold">À recouvrer</span>
                        </div>
                        <p class="text-white text-opacity-90 text-sm font-medium mb-2">Factures Impayées</p>
                        <p class="text-4xl font-bold mb-1">{{ $stats['factures_impayees'] }}</p>
                        <p class="text-white text-opacity-75 text-sm">Factures en attente</p>
                    </div>
                </div>

                <!-- Tickets Ouverts -->
                <div class="relative bg-gradient-to-br from-orange-500 to-orange-600 rounded-2xl shadow-xl p-8 text-white overflow-hidden">
                    <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-white opacity-10 rounded-full"></div>
                    <div class="absolute bottom-0 left-0 -mb-8 -ml-8 w-32 h-32 bg-white opacity-10 rounded-full"></div>
                    <div class="relative z-10">
                        <div class="flex items-center justify-between mb-4">
                            <div class="p-3 bg-white bg-opacity-20 rounded-xl">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                </svg>
                            </div>
                            <span class="text-orange-100 text-sm font-semibold">Support</span>
                        </div>
                        <p class="text-white text-opacity-90 text-sm font-medium mb-2">Tickets Ouverts</p>
                        <p class="text-4xl font-bold mb-1">{{ $stats['tickets_ouverts'] }}</p>
                        <p class="text-white text-opacity-75 text-sm">Demandes en cours</p>
                    </div>
                </div>
            </div>

            <!-- Stats Secondaires avec Icônes -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <!-- Biens -->
                <div class="bg-white rounded-xl shadow-md hover:shadow-xl transition duration-300 p-6">
                    <div class="flex items-center gap-4">
                        <div class="p-3 bg-blue-100 rounded-lg">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-gray-500 text-xs font-medium">Total Biens</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $stats['total_biens'] }}</p>
                        </div>
                    </div>
                </div>

                <!-- Unités -->
                <div class="bg-white rounded-xl shadow-md hover:shadow-xl transition duration-300 p-6">
                    <div class="flex items-center gap-4">
                        <div class="p-3 bg-purple-100 rounded-lg">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-gray-500 text-xs font-medium">Total Sous-Biens</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $stats['total_sous_biens'] }}</p>
                        </div>
                    </div>
                </div>

                <!-- Contrats -->
                <div class="bg-white rounded-xl shadow-md hover:shadow-xl transition duration-300 p-6">
                    <div class="flex items-center gap-4">
                        <div class="p-3 bg-green-100 rounded-lg">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-gray-500 text-xs font-medium">Contrats Actifs</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $stats['total_contrats'] }}</p>
                        </div>
                    </div>
                </div>

                <!-- Agence -->
                <div class="bg-gradient-to-br from-blue-600 to-blue-700 rounded-xl shadow-md hover:shadow-xl transition duration-300 p-6">
                    <div class="flex items-center gap-4">
                        <div class="p-3 bg-white bg-opacity-20 rounded-lg">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-white text-opacity-75 text-xs font-medium">Agence</p>
                            <p class="text-xl font-bold text-white">{{ Str::limit(Auth::user()->agence->nom, 15) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Derniers Paiements -->
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                <div class="bg-gradient-to-r from-blue-600 to-purple-600 px-8 py-6">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-white bg-opacity-20 rounded-lg">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-2xl font-bold text-white">Derniers Paiements</h3>
                            <p class="text-blue-100 text-sm">Transactions récentes encaissées</p>
                        </div>
                    </div>
                </div>

                <div class="p-8">
                    @if(count($derniers_paiements) > 0)
                    <div class="space-y-4">
                        @foreach($derniers_paiements as $paiement)
                        <div class="flex items-center justify-between p-4 bg-gradient-to-r from-gray-50 to-white rounded-xl hover:shadow-md transition duration-200 border border-gray-100">
                            <div class="flex items-center gap-4">
                                <div class="p-3 bg-green-100 rounded-lg">
                                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-bold text-gray-900">{{ $paiement->facture->contrat->locataire->user->name }}</p>
                                    <p class="text-sm text-gray-500">{{ \Carbon\Carbon::parse($paiement->date_paiement)->isoFormat('D MMM YYYY') }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-2xl font-bold text-green-600">{{ number_format($paiement->montant_verse, 0, ',', ' ') }} <span class="text-sm">{{ Auth::user()->agence->devise }}</span></p>
                                <span class="inline-block mt-1 px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-semibold">{{ $paiement->mode_paiement }}</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center py-12">
                        <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                        </svg>
                        <p class="text-gray-500 font-semibold">Aucun paiement récent</p>
                    </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
