<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Gestion des Biens Immobilier') }}
            </h2>
            <a href="{{ route('biens.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                + Ajouter un Bien
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

            @if($proprietaires->count() > 0)
                @foreach($proprietaires as $proprietaire)
                    @if($proprietaire->biens->count() > 0 || true)
                    <div class="mb-8">
                        <!-- En-tête Propriétaire -->
                        <div class="bg-indigo-50 border-l-4 border-indigo-500 p-4 mb-4 rounded">
                            <h3 class="text-lg font-bold text-indigo-900">
                                {{ $proprietaire->user->name }}
                            </h3>
                            <p class="text-sm text-indigo-700">
                                {{ $proprietaire->biens->count() }} bien(s)
                            </p>
                        </div>

                        @if($proprietaire->biens->count() > 0)
                        <!-- Tableau des biens du propriétaire -->
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Bien</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sous-biens</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Visites</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($proprietaire->biens as $bien)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4">
                                            <div>
                                                <a href="{{ route('biens.show', $bien) }}" class="font-bold text-blue-600 hover:underline">
                                                    {{ $bien->nom_bien }}
                                                </a>
                                                <p class="text-xs text-gray-500">{{ $bien->adresse }}</p>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $bien->type_bien }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-3 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                {{ $bien->children->count() }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-3 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                                {{ $bien->visites->count() }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                                @if($bien->statut === 'Disponible') bg-green-100 text-green-800
                                                @elseif($bien->statut === 'Loué') bg-red-100 text-red-800
                                                @else bg-yellow-100 text-yellow-800 @endif">
                                                {{ $bien->statut }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm space-x-2">
                                            <a href="{{ route('biens.show', $bien) }}" class="text-blue-600 hover:text-blue-900">Gérer</a>
                                            <a href="{{ route('biens.edit', $bien) }}" class="text-indigo-600 hover:text-indigo-900">Éditer</a>
                                            <form method="POST" action="{{ route('biens.destroy', $bien) }}" onsubmit="return confirm('Supprimer ce bien et tous ses sous-biens ?')" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900">Supprimer</button>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                        <div class="bg-gray-50 border border-gray-200 rounded p-4 text-center text-gray-600">
                            Aucun bien pour ce propriétaire
                        </div>
                        @endif
                    </div>
                    @endif
                @endforeach
            @else
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-12 text-center">
                <p class="text-gray-600 text-lg mb-4">Aucun propriétaire créé.</p>
                <a href="{{ route('proprietaires.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded">
                    Créer un propriétaire
                </a>
            </div>
            @endif
        </div>
    </div>
</x-app-layout>