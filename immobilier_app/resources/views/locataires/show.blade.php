<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Profil Locataire : {{ $locataire->user->name }}
            </h2>
            <a href="{{ route('locataires.index') }}" class="text-gray-600 hover:text-gray-900">← Retour</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <!-- Infos Locataire -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <span class="text-gray-500 text-sm">Nom</span>
                        <div class="font-bold text-lg">{{ $locataire->user->name }}</div>
                    </div>
                    <div>
                        <span class="text-gray-500 text-sm">Email</span>
                        <div class="font-bold">{{ $locataire->user->email }}</div>
                    </div>
                    <div>
                        <span class="text-gray-500 text-sm">Téléphone</span>
                        <div class="font-bold">{{ $locataire->user->telephone ?? '-' }}</div>
                    </div>
                    <div>
                        <span class="text-gray-500 text-sm">Profession</span>
                        <div class="font-bold">{{ $locataire->profession ?? '-' }}</div>
                    </div>
                    <div>
                        <span class="text-gray-500 text-sm">Contact Urgence</span>
                        <div class="font-bold">{{ $locataire->contact_urgence ?? '-' }}</div>
                    </div>
                </div>
            </div>

            <!-- Contrats Actifs -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Contrats Actifs ({{ $locataire->contrats->count() }})</h3>
                
                @if($locataire->contrats->count() > 0)
                <div class="space-y-4">
                    @foreach($locataire->contrats as $contrat)
                    <div class="border border-gray-200 rounded p-4">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <span class="text-gray-500 text-sm">Bien</span>
                                <div class="font-bold">{{ $contrat->bien->nom_bien ?? '-' }}</div>
                            </div>
                            <div>
                                <span class="text-gray-500 text-sm">Loyer Mensuel</span>
                                <div class="font-bold text-green-600">{{ number_format($contrat->montant_loyer_actuel, 0, ',', ' ') }} {{ Auth::user()->agence->devise }}</div>
                            </div>
                            <div>
                                <span class="text-gray-500 text-sm">Statut</span>
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    {{ $contrat->statut }}
                                </span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-gray-500 italic">Aucun contrat actif.</p>
                @endif
            </div>

            <!-- Tickets -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Tickets ({{ $locataire->tickets->count() }})</h3>
                
                @if($locataire->tickets->count() > 0)
                <div class="space-y-3">
                    @foreach($locataire->tickets as $ticket)
                    <div class="border border-gray-200 rounded p-3 flex justify-between items-center">
                        <div>
                            <span class="font-semibold">{{ $ticket->objet }}</span>
                            <span class="px-2 ml-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                @if($ticket->statut === 'Nouveau') bg-blue-100 text-blue-800
                                @elseif($ticket->statut === 'En cours') bg-yellow-100 text-yellow-800
                                @else bg-green-100 text-green-800 @endif">
                                {{ $ticket->statut }}
                            </span>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-gray-500 italic">Aucun ticket.</p>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
