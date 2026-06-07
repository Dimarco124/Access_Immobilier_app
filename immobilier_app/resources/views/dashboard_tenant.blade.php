<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Mon Espace Locataire') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-blue-500">
                    <div class="text-gray-500 text-sm uppercase">Contrats Actifs</div>
                    <div class="text-3xl font-bold">{{ $stats['contrats_actifs'] }}</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-red-500">
                    <div class="text-gray-500 text-sm uppercase">Factures à régler</div>
                    <div class="text-3xl font-bold text-red-600">{{ $stats['factures_a_payer'] }}</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-yellow-500">
                    <div class="text-gray-500 text-sm uppercase">Tickets en cours</div>
                    <div class="text-3xl font-bold text-yellow-600">{{ $stats['tickets_en_cours'] }}</div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Bienvenue, {{ Auth::user()->name }} !</h3>
                <p class="text-gray-600">Utilisez le menu pour suivre vos paiements ou envoyer une demande d'intervention.</p>
            </div>
        </div>
    </div>
</x-app-layout>
