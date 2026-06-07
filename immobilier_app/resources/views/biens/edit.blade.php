<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Éditer le Bien : ' . $bien->nom_bien) }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('biens.update', $bien) }}" class="space-y-6">
                    @csrf
                    @method('PATCH')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="nom_bien" :value="__('Nom du bien *')" />
                            <x-text-input id="nom_bien" name="nom_bien" type="text" class="mt-1 block w-full" value="{{ old('nom_bien', $bien->nom_bien) }}" required />
                            <x-input-error :messages="$errors->get('nom_bien')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="type_bien" :value="__('Type de bien *')" />
                            <select id="type_bien" name="type_bien" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="Immeuble" @selected(old('type_bien', $bien->type_bien) == 'Immeuble')>Immeuble</option>
                                <option value="Villa" @selected(old('type_bien', $bien->type_bien) == 'Villa')>Villa</option>
                                <option value="Appartement Seul" @selected(old('type_bien', $bien->type_bien) == 'Appartement Seul')>Appartement Seul</option>
                                <option value="Magasin/Entrepôt" @selected(old('type_bien', $bien->type_bien) == 'Magasin/Entrepôt')>Magasin/Entrepôt</option>
                            </select>
                            <x-input-error :messages="$errors->get('type_bien')" class="mt-2" />
                        </div>

                        <div class="md:col-span-2">
                            <x-input-label for="adresse" :value="__('Adresse précise *')" />
                            <x-text-input id="adresse" name="adresse" type="text" class="mt-1 block w-full" value="{{ old('adresse', $bien->adresse) }}" required />
                            <x-input-error :messages="$errors->get('adresse')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="taux_commission_agence" :value="__('Commission agence (%) *')" />
                            <x-text-input id="taux_commission_agence" name="taux_commission_agence" type="number" step="0.01" class="mt-1 block w-full" value="{{ old('taux_commission_agence', $bien->taux_commission_agence) }}" required />
                            <x-input-error :messages="$errors->get('taux_commission_agence')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="prix_visite" :value="__('Prix de la visite ('.Auth::user()->agence->devise.')')" />
                            <x-text-input id="prix_visite" name="prix_visite" type="number" step="0.01" class="mt-1 block w-full" value="{{ old('prix_visite', $bien->prix_visite) }}" />
                            <x-input-error :messages="$errors->get('prix_visite')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="loyer_mensuel" :value="__('Loyer mensuel')" />
                            <x-text-input id="loyer_mensuel" name="loyer_mensuel" type="number" step="0.01" class="mt-1 block w-full" value="{{ old('loyer_mensuel', $bien->loyer_mensuel) }}" />
                            <p class="text-xs text-gray-500 mt-1">Remplir seulement si ce bien n'a pas de sous-biens</p>
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
                        <a href="{{ route('biens.index') }}" class="text-gray-600 hover:text-gray-900">
                            ← Retour
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
