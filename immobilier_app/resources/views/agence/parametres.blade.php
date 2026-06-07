<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Configuration de l\'Agence') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    {{ session('success') }}
                </div>
            @endif

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <header>
                        <h2 class="text-lg font-medium text-gray-900">Informations Générales</h2>
                        <p class="mt-1 text-sm text-gray-600">Mettez à jour les informations de base de votre agence.</p>
                    </header>

                    <form method="post" action="{{ route('agence.parametres.update') }}" enctype="multipart/form-data" class="mt-6 space-y-6">
                        @csrf
                        @method('patch')

                        <div>
                            <x-input-label for="nom" :value="__('Nom de l\'agence')" />
                            <x-text-input id="nom" name="nom" type="text" class="mt-1 block w-full" :value="old('nom', $agence->nom)" required />
                            <x-input-error class="mt-2" :messages="$errors->get('nom')" />
                        </div>

                        <div>
                            <x-input-label for="email" :value="__('Email de contact')" />
                            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $agence->email)" required />
                            <x-input-error class="mt-2" :messages="$errors->get('email')" />
                        </div>

                        <div>
                            <x-input-label for="telephone" :value="__('Téléphone')" />
                            <x-text-input id="telephone" name="telephone" type="text" class="mt-1 block w-full" :value="old('telephone', $agence->telephone)" required />
                            <x-input-error class="mt-2" :messages="$errors->get('telephone')" />
                        </div>

                        <div>
                            <x-input-label for="adresse" :value="__('Adresse physique')" />
                            <x-text-input id="adresse" name="adresse" type="text" class="mt-1 block w-full" :value="old('adresse', $agence->adresse)" required />
                            <x-input-error class="mt-2" :messages="$errors->get('adresse')" />
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="taux_tva" :value="__('Taux TVA (%)')" />
                                <x-text-input id="taux_tva" name="taux_tva" type="number" step="0.01" class="mt-1 block w-full" :value="old('taux_tva', $agence->taux_tva)" required />
                                <x-input-error class="mt-2" :messages="$errors->get('taux_tva')" />
                            </div>
                            <div>
                                <x-input-label for="devise" :value="__('Devise')" />
                                <x-text-input id="devise" name="devise" type="text" class="mt-1 block w-full" :value="old('devise', $agence->devise)" required />
                                <x-input-error class="mt-2" :messages="$errors->get('devise')" />
                            </div>
                        </div>

                        <div>
                            <x-input-label for="logo" :value="__('Logo de l\'agence')" />
                            @if($agence->logo)
                                <img src="{{ Storage::url($agence->logo) }}" alt="Logo" class="h-20 mb-2">
                            @endif
                            <input type="file" name="logo" id="logo" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <x-input-error class="mt-2" :messages="$errors->get('logo')" />
                        </div>

                        <div class="flex items-center gap-4">
                            <x-primary-button>{{ __('Enregistrer') }}</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <header>
                        <h2 class="text-lg font-medium text-gray-900">Comptes Bancaires</h2>
                        <p class="mt-1 text-sm text-gray-600">Gérez les comptes bancaires de l'agence pour les paiements.</p>
                    </header>

                    <div class="mt-6 space-y-4">
                        @foreach($agence->comptesBancaires as $compte)
                            <div class="flex justify-between items-center p-3 border rounded">
                                <div>
                                    <div class="font-bold">{{ $compte->nom_banque }}</div>
                                    <div class="text-sm text-gray-600">{{ $compte->numero_compte }}</div>
                                </div>
                                <form method="POST" action="{{ route('agence.comptes.delete', $compte) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900">Supprimer</button>
                                </form>
                            </div>
                        @endforeach
                    </div>

                    <form method="post" action="{{ route('agence.comptes.add') }}" class="mt-6 space-y-6">
                        @csrf
                        <div class="grid grid-cols-1 gap-4">
                            <div>
                                <x-input-label for="nom_banque" :value="__('Nom de la banque')" />
                                <x-text-input id="nom_banque" name="nom_banque" type="text" class="mt-1 block w-full" required />
                            </div>
                            <div>
                                <x-input-label for="numero_compte" :value="__('Numéro de compte')" />
                                <x-text-input id="numero_compte" name="numero_compte" type="text" class="mt-1 block w-full" required />
                            </div>
                            <div>
                                <x-input-label for="rib" :value="__('RIB (Optionnel)')" />
                                <x-text-input id="rib" name="rib" type="text" class="mt-1 block w-full" />
                            </div>
                        </div>
                        <div class="flex items-center gap-4">
                            <x-primary-button>{{ __('Ajouter le compte') }}</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
