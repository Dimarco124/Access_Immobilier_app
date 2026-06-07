<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Enregistrer une Nouvelle Visite') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('visites.store') }}" class="space-y-6">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="locataire_id" :value="__('Locataire *')" />
                            <select id="locataire_id" name="locataire_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="">Sélectionnez un locataire</option>
                                @foreach($locataires as $locataire)
                                <option value="{{ $locataire->id }}" @selected(old('locataire_id') == $locataire->id)>
                                    {{ $locataire->user->name }} ({{ $locataire->user->email }})
                                </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('locataire_id')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="bien_id" :value="__('Bien / Sous-bien *')" />
                            <select id="bien_id" name="bien_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="">Sélectionnez un bien</option>
                                @foreach($proprietaires as $proprietaire)
                                    @foreach($proprietaire->biens as $bien)
                                        @if($bien->children->count() === 0)
                                        <!-- Bien principal sans sous-biens -->
                                        <option value="{{ $bien->id }}" @selected(old('bien_id') == $bien->id)>
                                            {{ $bien->nom_bien }}
                                        </option>
                                        @else
                                        <!-- Bien principal avec sous-biens : afficher optgroup -->
                                        <optgroup label="{{ $bien->nom_bien }}">
                                            @foreach($bien->children as $child)
                                            <option value="{{ $child->id }}" @selected(old('bien_id') == $child->id)>
                                                → {{ $child->nom_bien }}
                                            </option>
                                            @endforeach
                                        </optgroup>
                                        @endif
                                    @endforeach
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('bien_id')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="date_heure_visite" :value="__('Date et Heure *')" />
                            <x-text-input id="date_heure_visite" name="date_heure_visite" type="datetime-local" class="mt-1 block w-full" value="{{ old('date_heure_visite') }}" required />
                            <x-input-error :messages="$errors->get('date_heure_visite')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="montant_paye" :value="__('Montant payé ('.Auth::user()->agence->devise.')')" />
                            <x-text-input id="montant_paye" name="montant_paye" type="number" step="0.01" class="mt-1 block w-full" value="{{ old('montant_paye', '0.00') }}" />
                            <p class="text-xs text-gray-500 mt-1">Laisser à 0 ou le montant de la visite du bien</p>
                            <x-input-error :messages="$errors->get('montant_paye')" class="mt-2" />
                        </div>

                        <div class="md:col-span-2">
                            <x-input-label for="notes" :value="__('Notes (Optionnel)')" />
                            <textarea id="notes" name="notes" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" rows="3">{{ old('notes') }}</textarea>
                            <p class="text-xs text-gray-500 mt-1">Exemple : Client intéressé, à relancer demain</p>
                            <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                        </div>
                    </div>

                    <div class="flex items-center justify-between">
                        <a href="{{ route('visites.index') }}" class="text-gray-600 hover:text-gray-900">
                            ← Retour
                        </a>
                        <x-primary-button>
                            {{ __('Enregistrer la Visite') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
