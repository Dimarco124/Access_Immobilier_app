<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Ajouter un Bien Immobilier') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('biens.store') }}" class="space-y-6">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="proprietaire_id" :value="__('Propriétaire *')" />
                            <select id="proprietaire_id" name="proprietaire_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="">Sélectionnez le propriétaire</option>
                                @foreach($proprietaires as $proprio)
                                <option value="{{ $proprio->id }}" @selected(old('proprietaire_id') == $proprio->id)>
                                    {{ $proprio->user->name }}
                                </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('proprietaire_id')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="type_bien" :value="__('Type de bien *')" />
                            <select id="type_bien" name="type_bien" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="">Sélectionnez le type</option>
                                <option value="Immeuble" @selected(old('type_bien') == 'Immeuble')>Immeuble</option>
                                <option value="Villa" @selected(old('type_bien') == 'Villa')>Villa</option>
                                <option value="Appartement Seul" @selected(old('type_bien') == 'Appartement Seul')>Appartement Seul</option>
                                <option value="Magasin/Entrepôt" @selected(old('type_bien') == 'Magasin/Entrepôt')>Magasin/Entrepôt</option>
                            </select>
                            <x-input-error :messages="$errors->get('type_bien')" class="mt-2" />
                        </div>

                        <div class="md:col-span-2">
                            <x-input-label for="nom_bien" :value="__('Nom du bien (ex: Immeuble Horizon) *')" />
                            <x-text-input id="nom_bien" name="nom_bien" type="text" class="mt-1 block w-full" value="{{ old('nom_bien') }}" required />
                            <x-input-error :messages="$errors->get('nom_bien')" class="mt-2" />
                        </div>

                        <div class="md:col-span-2">
                            <x-input-label for="adresse" :value="__('Adresse précise *')" />
                            <x-text-input id="adresse" name="adresse" type="text" class="mt-1 block w-full" value="{{ old('adresse') }}" required />
                            <x-input-error :messages="$errors->get('adresse')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="taux_commission_agence" :value="__('Commission agence (%) *')" />
                            <x-text-input id="taux_commission_agence" name="taux_commission_agence" type="number" step="0.01" class="mt-1 block w-full" value="{{ old('taux_commission_agence', '10.00') }}" required />
                            <x-input-error :messages="$errors->get('taux_commission_agence')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="prix_visite" :value="__('Prix de la visite ('.Auth::user()->agence->devise.')')" />
                            <x-text-input id="prix_visite" name="prix_visite" type="number" step="0.01" class="mt-1 block w-full" value="{{ old('prix_visite', '0.00') }}" />
                            <x-input-error :messages="$errors->get('prix_visite')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="loyer_mensuel" :value="__('Loyer mensuel (Si louable directement)')" />
                            <x-text-input id="loyer_mensuel" name="loyer_mensuel" type="number" step="0.01" class="mt-1 block w-full" value="{{ old('loyer_mensuel') }}" placeholder="Laisser vide si contient des sous-biens" />
                            <p class="text-xs text-gray-500 mt-1">Laisser vide si vous allez ajouter des appartements/studios</p>
                            <x-input-error :messages="$errors->get('loyer_mensuel')" class="mt-2" />
                        </div>

                        <div class="md:col-span-2">
                            <x-input-label for="description" :value="__('Description (Optionnel)')" />
                            <textarea id="description" name="description" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" rows="3">{{ old('description') }}</textarea>
                            <x-input-error :messages="$errors->get('description')" class="mt-2" />
                        </div>
                    </div>

                    <div class="flex items-center justify-between">
                        <a href="{{ route('biens.index') }}" class="text-gray-600 hover:text-gray-900">
                            ← Retour
                        </a>
                        <x-primary-button>
                            {{ __('Enregistrer le Bien') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>