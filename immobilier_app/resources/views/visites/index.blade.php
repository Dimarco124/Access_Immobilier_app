<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-bold text-3xl text-gray-900">
                    @if(Auth::user()->hasRole('locataire'))
                        {{ __('Mes Visites') }}
                    @else
                        {{ __('Gestion des Visites') }}
                    @endif
                </h2>
                <p class="text-gray-600 mt-1">
                    @if(Auth::user()->hasRole('locataire'))
                        Consultez l'historique de vos visites
                    @else
                        Suivi de toutes les visites immobilières
                    @endif
                </p>
            </div>
            @if(Auth::user()->hasRole('admin'))
            <a href="{{ route('visites.create') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow-lg transition duration-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Nouvelle Visite
            </a>
            @endif
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

            @if($visites->count() > 0)
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-gradient-to-r from-blue-600 to-blue-700 text-white">
                                <th class="px-6 py-4 text-left text-sm font-semibold">Date & Heure</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold">Locataire</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold">Bien / Sous-bien</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold">Montant Payé</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold">Statut</th>
                                <th class="px-6 py-4 text-left text-sm font-semibold">Notes</th>
                                <th class="px-6 py-4 text-right text-sm font-semibold">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($visites as $visite)
                            <tr class="hover:bg-blue-50 transition duration-150">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="font-semibold text-gray-900">{{ $visite->date_heure_visite->format('d/m/Y') }}</div>
                                    <div class="text-sm text-gray-500">{{ $visite->date_heure_visite->format('H:i') }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($visite->locataire)
                                    <a href="{{ route('locataires.show', $visite->locataire) }}" class="text-blue-600 hover:text-blue-800 font-semibold">
                                        {{ $visite->locataire->user->name }}
                                    </a>
                                    @else
                                    <span class="text-gray-400 italic">Supprimé</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if($visite->bien)
                                    <a href="{{ route('biens.show', $visite->bien) }}" class="text-blue-600 hover:text-blue-800 font-semibold">
                                        {{ $visite->bien->nom_bien }}
                                    </a>
                                    @else
                                    <span class="text-gray-400 italic">Supprimé</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="font-bold text-green-600">{{ number_format($visite->montant_paye, 0, ',', ' ') }} {{ Auth::user()->agence->devise }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold
                                        @if($visite->statut === 'Programmé') bg-blue-100 text-blue-800
                                        @elseif($visite->statut === 'Effectué') bg-green-100 text-green-800
                                        @else bg-red-100 text-red-800 @endif">
                                        {{ $visite->statut }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600 max-w-xs truncate" title="{{ $visite->notes ?? '-' }}">
                                    {{ $visite->notes ? substr($visite->notes, 0, 30) . '...' : '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <div class="flex justify-end gap-2">
                                        @if($visite->statut === 'Programmé' && Auth::user()->hasRole('admin'))
                                        <form method="POST" action="{{ route('visites.completed', $visite) }}" style="display:inline;">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="px-3 py-2 bg-green-100 text-green-700 hover:bg-green-200 rounded-lg text-xs font-semibold transition duration-200" title="Marquer comme effectuée">
                                                ✓ Effectuée
                                            </button>
                                        </form>
                                        @endif
                                        @if($visite->statut !== 'Annulé' && Auth::user()->hasRole('admin'))
                                        <form method="POST" action="{{ route('visites.cancel', $visite) }}" style="display:inline;">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="px-3 py-2 bg-yellow-100 text-yellow-700 hover:bg-yellow-200 rounded-lg text-xs font-semibold transition duration-200" title="Annuler">
                                                ✕ Annuler
                                            </button>
                                        </form>
                                        @endif
                                        @if(Auth::user()->hasRole('admin'))
                                        <form method="POST" action="{{ route('visites.destroy', $visite) }}" onsubmit="return confirm('Supprimer cette visite ?')" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="px-3 py-2 bg-red-100 text-red-700 hover:bg-red-200 rounded-lg text-xs font-semibold transition duration-200" title="Supprimer">
                                                🗑 Supprimer
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                    {{ $visites->links() }}
                </div>
            </div>
            @else
            <div class="bg-white rounded-2xl shadow-lg p-12 text-center">
                <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                </svg>
                <p class="text-xl text-gray-600 font-semibold">Aucune visite enregistrée</p>
                <p class="text-gray-500 mt-2">Les visites apparaîtront ici une fois créées</p>
            </div>
            @endif
        </div>
    </div>
</x-app-layout>
