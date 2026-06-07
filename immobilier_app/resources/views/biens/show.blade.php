<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Détails du Bien : {{ $bien->nom_bien }}
            </h2>
            <div class="space-x-2">
                <a href="{{ route('biens.edit', $bien) }}" class="text-gray-600 hover:text-gray-900">Modifier</a>
                <a href="{{ route('biens.index') }}" class="text-gray-600 hover:text-gray-900">Retour</a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                {{ session('success') }}
            </div>
            @endif

            <!-- Infos Bien -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div>
                        <span class="text-gray-500 text-sm">Propriétaire</span>
                        <div class="font-bold">{{ $bien->proprietaire->user->name }}</div>
                    </div>
                    <div>
                        <span class="text-gray-500 text-sm">Type</span>
                        <div class="font-bold">{{ $bien->type_bien }}</div>
                    </div>
                    <div>
                        <span class="text-gray-500 text-sm">Prix Visite</span>
                        <div class="font-bold text-blue-600">{{ number_format($bien->prix_visite, 0, ',', ' ') }} {{ Auth::user()->agence->devise }}</div>
                    </div>
                    <div>
                        <span class="text-gray-500 text-sm">Adresse</span>
                        <div class="font-bold">{{ $bien->adresse }}</div>
                    </div>
                </div>
            </div>

            <!-- Gestion des Sous-Biens -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-medium text-gray-900">
                        Sous-biens (Appartements, Studios...)
                        <span class="text-sm text-gray-500 font-normal">{{ $bien->children->count() }}</span>
                    </h3>
                    @if($bien->parent_id === null)
                    <button onclick="document.getElementById('modal-subbien').classList.remove('hidden')" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded text-sm">
                        + Ajouter un Sous-bien
                    </button>
                    @endif
                </div>

                @if($bien->children->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nom</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Loyer</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Prix Visite</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($bien->children as $child)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 font-bold text-blue-600">{{ $child->nom_bien }}</td>
                                <td class="px-6 py-4 text-sm">{{ $child->type_bien }}</td>
                                <td class="px-6 py-4 whitespace-nowrap font-bold text-green-600">
                                    {{ number_format($child->loyer_mensuel ?? 0, 0, ',', ' ') }} {{ Auth::user()->agence->devise }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-blue-600">
                                    {{ number_format($child->prix_visite ?? 0, 0, ',', ' ') }} {{ Auth::user()->agence->devise }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        @if($child->statut === 'Disponible') bg-green-100 text-green-800
                                        @elseif($child->statut === 'Loué') bg-red-100 text-red-800
                                        @else bg-yellow-100 text-yellow-800 @endif">
                                        {{ $child->statut }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm space-x-2">
                                    <a href="{{ route('biens.subbiens.edit', $child) }}" class="text-indigo-600 hover:text-indigo-900">Éditer</a>
                                    <form method="POST" action="{{ route('biens.subbiens.destroy', $child) }}" onsubmit="return confirm('Supprimer ce sous-bien ?')" style="display:inline;">
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
                <div class="bg-blue-50 border border-blue-200 rounded p-4 text-center">
                    <p class="text-gray-700">
                        <span class="font-semibold">Aucun sous-bien.</span> 
                        Ce bien peut être loué directement s'il a un loyer défini.
                    </p>
                </div>
                @endif
            </div>

            <!-- Gestion des Visites -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-medium text-gray-900">Historique des Visites</h3>
                    <button onclick="document.getElementById('modal-visite').classList.remove('hidden')" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-3 rounded text-sm">
                        + Nouvelle Visite
                    </button>
                </div>

                @if($bien->visites->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Locataire</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Montant Payé</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Notes</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($bien->visites as $visite)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm">{{ $visite->date_heure_visite->format('d/m/Y H:i') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap font-semibold">
                                    @if($visite->locataire)
                                    <a href="{{ route('locataires.show', $visite->locataire) }}" class="text-blue-600 hover:underline">
                                        {{ $visite->locataire->user->name }}
                                    </a>
                                    @else
                                    <span class="text-gray-400 italic">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap font-bold text-green-600">{{ number_format($visite->montant_paye, 0, ',', ' ') }} {{ Auth::user()->agence->devise }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        @if($visite->statut === 'Programmé') bg-blue-100 text-blue-800
                                        @elseif($visite->statut === 'Effectué') bg-green-100 text-green-800
                                        @else bg-red-100 text-red-800 @endif">
                                        {{ $visite->statut }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $visite->notes ? substr($visite->notes, 0, 30) . '...' : '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm space-x-1">
                                    @if($visite->statut === 'Programmé')
                                    <form method="POST" action="{{ route('visites.completed', $visite) }}" style="display:inline;">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="text-green-600 hover:text-green-900 font-semibold">Effectuée</button>
                                    </form>
                                    @endif
                                    @if($visite->statut !== 'Annulé')
                                    <form method="POST" action="{{ route('visites.cancel', $visite) }}" style="display:inline;">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="text-yellow-600 hover:text-yellow-900 font-semibold">Annuler</button>
                                    </form>
                                    @endif
                                    <form method="POST" action="{{ route('visites.destroy', $visite) }}" onsubmit="return confirm('Supprimer cette visite ?')" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900 font-semibold">Supprimer</button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @else
                <div class="text-center py-6 text-gray-500">
                    <p class="italic">Aucune visite enregistrée.</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Modal Sous-bien -->
    <div id="modal-subbien" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">Ajouter un Sous-bien</h3>
                <button onclick="document.getElementById('modal-subbien').classList.add('hidden')" class="text-gray-400 hover:text-gray-600 text-2xl">&times;</button>
            </div>
            <form action="{{ route('biens.subbiens.store', $bien) }}" method="POST">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nom du sous-bien *</label>
                        <input type="text" name="nom_bien" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Type *</label>
                        <select name="type_bien" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" required>
                            <option value="Studio">Studio</option>
                            <option value="F2">F2 (2 pièces)</option>
                            <option value="F3">F3 (3 pièces)</option>
                            <option value="F4">F4 (4 pièces)</option>
                            <option value="Magasin">Magasin</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Loyer mensuel {{ Auth::user()->agence->devise }} *</label>
                        <input type="number" name="loyer_mensuel" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Prix visite {{ Auth::user()->agence->devise }}</label>
                        <input type="number" name="prix_visite" value="{{ $bien->prix_visite ?? 0 }}" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Adresse (optionnel)</label>
                        <input type="text" name="adresse" placeholder="Hérite du parent si vide" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description (optionnel)</label>
                        <textarea name="description" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" rows="2"></textarea>
                    </div>
                </div>
                <div class="flex justify-end mt-6 space-x-3">
                    <button type="button" onclick="document.getElementById('modal-subbien').classList.add('hidden')" class="bg-gray-200 hover:bg-gray-300 px-4 py-2 rounded text-gray-800 font-medium">Annuler</button>
                    <button type="submit" class="bg-green-500 hover:bg-green-700 px-4 py-2 rounded text-white font-medium">Ajouter</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Visite -->
    <div id="modal-visite" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-medium text-gray-900">Enregistrer une Visite</h3>
                <button onclick="document.getElementById('modal-visite').classList.add('hidden')" class="text-gray-400 hover:text-gray-600 text-2xl">&times;</button>
            </div>
            <form action="{{ route('visites.store') }}" method="POST">
                @csrf
                <input type="hidden" name="bien_id" value="{{ $bien->id }}">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Locataire *</label>
                        <select name="locataire_id" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" required>
                            <option value="">Sélectionnez un locataire</option>
                            @foreach($bien->agence->locataires as $locataire)
                            <option value="{{ $locataire->id }}">{{ $locataire->user->name }} ({{ $locataire->user->email }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date et Heure *</label>
                        <input type="datetime-local" name="date_heure_visite" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Montant payé
                            <span class="text-blue-600 font-semibold">(Prix visite: {{ number_format($bien->prix_visite, 0, ',', ' ') }} {{ Auth::user()->agence->devise }})</span>
                        </label>
                        <input type="number" name="montant_paye" value="{{ $bien->prix_visite }}" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" required>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Notes (Optionnel)</label>
                        <textarea name="notes" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500" rows="2"></textarea>
                    </div>
                </div>
                <div class="flex justify-end mt-6 space-x-3">
                    <button type="button" onclick="document.getElementById('modal-visite').classList.add('hidden')" class="bg-gray-200 hover:bg-gray-300 px-4 py-2 rounded text-gray-800 font-medium">Annuler</button>
                    <button type="submit" class="bg-blue-500 hover:bg-blue-700 px-4 py-2 rounded text-white font-medium">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
