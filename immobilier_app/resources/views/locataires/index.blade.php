<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Gestion des Locataires') }}
            </h2>
            @if(Auth::user()->hasRole('admin'))
            <a href="{{ route('locataires.create') }}" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Ajouter un Locataire
            </a>
            @endif
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
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nom</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Téléphone</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Profession</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($locataires as $locataire)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $locataire->user->name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $locataire->user->email }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $locataire->user->telephone }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $locataire->profession }}</td>
                            <td class="px-6 py-4 whitespace-nowrap flex space-x-2">
                                <a href="{{ route('locataires.show', $locataire) }}" class="text-blue-600 hover:text-blue-900">Détails</a>
                                @if(Auth::user()->hasRole('admin'))
                                <a href="{{ route('locataires.edit', $locataire) }}" class="text-indigo-600 hover:text-indigo-900">Modifier</a>
                                <form method="POST" action="{{ route('locataires.destroy', $locataire) }}" onsubmit="return confirm('Êtes-vous sûr ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">Supprimer</button>
                                </form>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
