<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Mon Espace Propriétaire') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-indigo-500">
                    <div class="text-gray-500 text-sm uppercase">Total de mes Biens</div>
                    <div class="text-3xl font-bold">{{ $stats['total_biens'] }}</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-green-500">
                    <div class="text-gray-500 text-sm uppercase">Loyers perçus (Total)</div>
                    <div class="text-3xl font-bold text-green-600">{{ number_format($stats['total_loyers_percus'], 0, ',', ' ') }} {{ Auth::user()->agence->devise }}</div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Suivi de vos actifs</h3>
                <p class="text-gray-600">Consultez l'état de vos biens et l'historique des paiements de vos locataires.</p>
            </div>
        </div>
    </div>
</x-app-layout>
