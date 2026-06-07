<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Ajouter un Propriétaire') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('proprietaires.store') }}" class="space-y-6">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="civilite" :value="__('Civilité')" />
                            <select id="civilite" name="civilite" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="M.">Monsieur (M.)</option>
                                <option value="Mme">Madame (Mme)</option>
                                <option value="Mlle">Mademoiselle (Mlle)</option>
                            </select>
                            <x-input-error :messages="$errors->get('civilite')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="name" :value="__('Nom complet')" />
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" required />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="email" :value="__('Email')" />
                            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" required />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="password" :value="__('Mot de passe (pour son accès)')" />
                            <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" required />
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="telephone" :value="__('Téléphone Principal')" />
                            <x-text-input id="telephone" name="telephone" type="tel" class="mt-1 block w-full phone-input" />
                            <x-input-error :messages="$errors->get('telephone')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="contact_secondaire" :value="__('Contact Secondaire')" />
                            <x-text-input id="contact_secondaire" name="contact_secondaire" type="tel" class="mt-1 block w-full phone-input" />
                            <x-input-error :messages="$errors->get('contact_secondaire')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="pays" :value="__('Pays')" />
                            <x-text-input id="pays" name="pays" type="text" class="mt-1 block w-full" placeholder="Ex: Côte d'Ivoire" />
                            <x-input-error :messages="$errors->get('pays')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="ville" :value="__('Ville')" />
                            <x-text-input id="ville" name="ville" type="text" class="mt-1 block w-full" placeholder="Ex: Abidjan" />
                            <x-input-error :messages="$errors->get('ville')" class="mt-2" />
                        </div>

                        <div class="md:col-span-2">
                            <x-input-label for="adresse_residence" :value="__('Adresse de résidence')" />
                            <x-text-input id="adresse_residence" name="adresse_residence" type="text" class="mt-1 block w-full" />
                            <x-input-error :messages="$errors->get('adresse_residence')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="numero_piece_identite" :value="__('Numéro pièce d\'identité')" />
                            <x-text-input id="numero_piece_identite" name="numero_piece_identite" type="text" class="mt-1 block w-full" />
                            <x-input-error :messages="$errors->get('numero_piece_identite')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="rib_bancaire" :value="__('RIB Bancaire')" />
                            <x-text-input id="rib_bancaire" name="rib_bancaire" type="text" class="mt-1 block w-full" />
                            <x-input-error :messages="$errors->get('rib_bancaire')" class="mt-2" />
                        </div>
                    </div>

                    <script>
                        document.querySelectorAll('.phone-input').forEach(input => {
                            window.intlTelInput(input, {
                                initialCountry: "ci",
                                preferredCountries: ["ci", "fr", "sn", "bf"],
                                utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@23.0.10/build/js/utils.js",
                            });
                        });
                    </script>

                    <div class="flex items-center justify-end mt-4">
                        <x-primary-button class="ml-4">
                            {{ __('Enregistrer') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>