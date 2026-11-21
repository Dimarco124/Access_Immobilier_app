<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Éditer le Sous-bien : ' . $bien->nom_bien) }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('biens.subbiens.update', $bien) }}" class="space-y-6">
                    @csrf
                    @method('PATCH')

                    <div class="bg-blue-50 border border-blue-200 rounded p-4 mb-4">
                        <p class="text-sm text-blue-800">
                            <span class="font-semibold">Bien Parent :</span> 
                            <a href="{{ route('biens.show', $parent) }}" class="text-blue-600 hover:underline font-bold">
                                {{ $parent->nom_bien }}
                            </a>
                        </p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <x-input-label for="nom_bien" :value="__('Nom du sous-bien (ex: Apt A1, Studio B2) *')" />
                            <x-text-input id="nom_bien" name="nom_bien" type="text" class="mt-1 block w-full" value="{{ old('nom_bien', $bien->nom_bien) }}" required />
                            <x-input-error :messages="$errors->get('nom_bien')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="type_bien" :value="__('Type de bien *')" />
                            <select id="type_bien" name="type_bien" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="Studio" @selected(old('type_bien', $bien->type_bien) == 'Studio')>Studio</option>
                                <option value="F2" @selected(old('type_bien', $bien->type_bien) == 'F2')>F2 (2 pièces)</option>
                                <option value="F3" @selected(old('type_bien', $bien->type_bien) == 'F3')>F3 (3 pièces)</option>
                                <option value="F4" @selected(old('type_bien', $bien->type_bien) == 'F4')>F4 (4 pièces)</option>
                                <option value="F5" @selected(old('type_bien', $bien->type_bien) == 'F5')>F5 (5 pièces)</option>
                                <option value="Magasin" @selected(old('type_bien', $bien->type_bien) == 'Magasin')>Magasin</option>
                                <option value="Bureau" @selected(old('type_bien', $bien->type_bien) == 'Bureau')>Bureau</option>
                                <option value="Entrepôt" @selected(old('type_bien', $bien->type_bien) == 'Entrepôt')>Entrepôt</option>
                            </select>
                            <x-input-error :messages="$errors->get('type_bien')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="adresse" :value="__('Adresse (Optionnel)')" />
                            <x-text-input id="adresse" name="adresse" type="text" class="mt-1 block w-full" value="{{ old('adresse', $bien->adresse) }}" placeholder="Hérite du parent si vide" />
                            <x-input-error :messages="$errors->get('adresse')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="loyer_mensuel" :value="__('Loyer mensuel ('.Auth::user()->agence->devise.') *')" />
                            <x-text-input id="loyer_mensuel" name="loyer_mensuel" type="number" step="0.01" class="mt-1 block w-full" value="{{ old('loyer_mensuel', $bien->loyer_mensuel) }}" required />
                            <x-input-error :messages="$errors->get('loyer_mensuel')" class="mt-2" />
                        </div>



                        <div>
                            <x-input-label for="statut" :value="__('Statut *')" />
                            <select id="statut" name="statut" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="Disponible" @selected(old('statut', $bien->statut) == 'Disponible')>Disponible</option>
                                <option value="Loué" @selected(old('statut', $bien->statut) == 'Loué')>Loué</option>
                                <option value="En maintenance" @selected(old('statut', $bien->statut) == 'En maintenance')>En maintenance</option>
                            </select>
                            <x-input-error :messages="$errors->get('statut')" class="mt-2" />
                        </div>

                        <div class="md:col-span-2">
                            <x-input-label for="description" :value="__('Description (Optionnel)')" />
                            <textarea id="description" name="description" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" rows="3">{{ old('description', $bien->description) }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>
                    </div>

                    <div class="flex items-center justify-between">
                        <a href="{{ route('biens.show', $parent) }}" class="text-gray-600 hover:text-gray-900">
                            ← Retour au bien
                        </a>
                        <x-primary-button>
                            {{ __('Mettre à jour') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
