<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Gestion des Contrats de Location') }}
            </h2>
            <a href="{{ route('contrats.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Nouveau Contrat
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Locataire</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Bien / Unité</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Loyer</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Début</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($contrats as $contrat)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $contrat->locataire->user->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-bold">{{ $contrat->bien->nom_bien }}</div>
                                <div class="text-xs text-gray-500">{{ $contrat->bien->type_bien }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ number_format($contrat->montant_loyer_actuel, 0, ',', ' ') }} {{ Auth::user()->agence->devise }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $contrat->date_debut }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $contrat->statut == 'Actif' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $contrat->statut }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap flex space-x-2">
                                <a href="{{ route('contrats.show', $contrat) }}" class="text-indigo-600 hover:text-indigo-900">Détails</a>
                                <form method="POST" action="{{ route('contrats.destroy', $contrat) }}" onsubmit="return confirm('Résilier ce contrat ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">Résilier</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
