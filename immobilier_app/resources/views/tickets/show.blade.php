<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Ticket #{{ $ticket->id }} : {{ $ticket->objet }}
            </h2>
            <div class="space-x-2">
                @if($ticket->statut != 'Résolu' && !Auth::user()->hasRole('locataire'))
                <form method="POST" action="{{ route('tickets.close', $ticket) }}" class="inline">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-1 px-3 rounded text-sm">
                        Marquer comme Résolu
                    </button>
                </form>
                @endif
                <a href="{{ route('tickets.index') }}" class="text-gray-600 hover:text-gray-900">Retour</a>
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

            <!-- Détails Ticket -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <span class="text-gray-500 text-sm">Description du problème :</span>
                        <p class="mt-2 text-gray-800 bg-gray-50 p-4 rounded border italic">
                            "{{ $ticket->description_probleme }}"
                        </p>
                    </div>
                    <div class="text-right">
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                            {{ $ticket->priorite }}
                        </span>
                        <div class="text-xs text-gray-400 mt-2 italic">Ouvert le {{ $ticket->created_at->format('d/m/Y à H:i') }}</div>
                    </div>
                </div>
            </div>

            <!-- Fil de discussion -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-6">Discussion</h3>

                <div class="space-y-4 mb-8">
                    @forelse($ticket->messages as $message)
                        <div class="flex {{ $message->user_id == Auth::id() ? 'justify-end' : 'justify-start' }}">
                            <div class="max-w-md {{ $message->user_id == Auth::id() ? 'bg-blue-100 border-blue-200' : 'bg-gray-100 border-gray-200' }} border p-3 rounded-lg shadow-sm">
                                <div class="text-xs font-bold text-gray-600 mb-1">
                                    {{ $message->user->name }} ({{ $message->created_at->diffForHumans() }})
                                </div>
                                <div class="text-gray-800">
                                    {{ $message->contenu_message }}
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-center text-gray-500 italic">Aucun message pour le moment.</p>
                    @endforelse
                </div>

                @if($ticket->statut != 'Résolu')
                <form method="POST" action="{{ route('tickets.message', $ticket) }}" class="mt-6 border-t pt-6">
                    @csrf
                    <div>
                        <x-input-label for="contenu_message" :value="__('Votre réponse')" />
                        <textarea id="contenu_message" name="contenu_message" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" rows="3" required placeholder="Tapez votre message ici..."></textarea>
                    </div>
                    <div class="flex justify-end mt-4">
                        <x-primary-button>Envoyer le message</x-primary-button>
                    </div>
                </form>
                @else
                <div class="bg-gray-100 p-4 rounded text-center text-gray-600 italic">
                    Ce ticket est fermé. Aucune nouvelle réponse n'est possible.
                </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
