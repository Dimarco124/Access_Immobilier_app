<x-guest-layout>
    <div class="py-6" x-data="{ step: @if($errors->any()) 3 @else 1 @endif }">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-8">

                <!-- Header -->
                <div class="mb-8 text-center">
                    <h2 class="text-2xl font-bold text-gray-900">Inscription de votre Agence</h2>
                    <p class="text-gray-600 mt-2">Configurez votre espace de gestion immobilière en quelques étapes.</p>
                </div>

                <!-- Progress Bar -->
                <div class="mb-8">
                    <div class="flex items-center justify-between relative">
                        <div class="absolute left-0 top-1/2 w-full h-0.5 bg-gray-200 -z-0"></div>
                        <div class="flex flex-col items-center relative z-10">
                            <div :class="step >= 1 ? 'bg-blue-600 text-white' : 'bg-gray-200'" class="w-8 h-8 rounded-full flex items-center justify-center font-bold transition-colors duration-300">1</div>
                            <span class="text-xs mt-1 font-medium">Agence</span>
                        </div>
                        <div class="flex flex-col items-center relative z-10">
                            <div :class="step >= 2 ? 'bg-blue-600 text-white' : 'bg-gray-200'" class="w-8 h-8 rounded-full flex items-center justify-center font-bold transition-colors duration-300">2</div>
                            <span class="text-xs mt-1 font-medium">Logo & Détails</span>
                        </div>
                        <div class="flex flex-col items-center relative z-10">
                            <div :class="step >= 3 ? 'bg-blue-600 text-white' : 'bg-gray-200'" class="w-8 h-8 rounded-full flex items-center justify-center font-bold transition-colors duration-300">3</div>
                            <span class="text-xs mt-1 font-medium">Administrateur</span>
                        </div>
                    </div>
                </div>

                <form method="POST" action="{{ route('register.agency.post') }}" enctype="multipart/form-data">
                    @csrf

                    <!-- Etape 1 : Infos Agence -->
                    <div x-show="step === 1" x-transition>
                        <div class="space-y-4">
                            <div>
                                <x-input-label for="nom_agence" :value="__('Nom de l\'agence')" />
                                <x-text-input id="nom_agence" name="nom_agence" type="text" class="mt-1 block w-full" :value="old('nom_agence')" required />
                                <x-input-error :messages="$errors->get('nom_agence')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="ville" :value="__('Ville')" />
                                <x-text-input id="ville" name="ville" type="text" class="mt-1 block w-full" :value="old('ville')" required />
                                <x-input-error :messages="$errors->get('ville')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="telephone_agence" :value="__('Téléphone Agence')" />
                                <x-text-input id="telephone_agence" name="telephone_agence" type="tel" class="mt-1 block w-full phone-input" :value="old('telephone_agence')" required />
                                <x-input-error :messages="$errors->get('telephone_agence')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="email_agence" :value="__('Email de l\'agence (Professionnel)')" />
                                <x-text-input id="email_agence" name="email_agence" type="email" class="mt-1 block w-full" :value="old('email_agence')" required />
                                <x-input-error :messages="$errors->get('email_agence')" class="mt-2" />
                            </div>
                        </div>
                        <div class="mt-8 flex justify-end">
                            <button type="button" @click="step = 2" class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 transition font-bold">Suivant</button>
                        </div>
                    </div>

                    <!-- Etape 2 : Logo & Description -->
                    <div x-show="step === 2" x-transition>
                        <div class="space-y-4">
                            <div>
                                <x-input-label for="logo" :value="__('Logo de l\'agence')" />
                                <input type="file" name="logo" id="logo" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <x-input-error :messages="$errors->get('logo')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="description" :value="__('Description de l\'agence')" />
                                <textarea id="description" name="description" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" rows="4">{{ old('description') }}</textarea>
                                <x-input-error :messages="$errors->get('description')" class="mt-2" />
                            </div>
                        </div>
                        <div class="mt-8 flex justify-between">
                            <button type="button" @click="step = 1" class="bg-gray-200 text-gray-800 px-6 py-2 rounded-md hover:bg-gray-300 transition font-bold">Précédent</button>
                            <button type="button" @click="step = 3" class="bg-blue-600 text-white px-6 py-2 rounded-md hover:bg-blue-700 transition font-bold">Suivant</button>
                        </div>
                    </div>

                    <!-- Etape 3 : Compte Admin -->
                    <div x-show="step === 3" x-transition>
                        <div class="space-y-4">
                            <div>
                                <x-input-label for="name" :value="__('Votre Nom (Administrateur)')" />
                                <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name')" required />
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="email" :value="__('Votre Email Personnel')" />
                                <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email')" required />
                                <x-input-error :messages="$errors->get('email')" class="mt-2" />
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <x-input-label for="password" :value="__('Mot de passe')" />
                                    <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" required autocomplete="new-password" />
                                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="password_confirmation" :value="__('Confirmer')" />
                                    <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" required />
                                </div>
                            </div>
                        </div>
                        <div class="mt-8 flex justify-between">
                            <button type="button" @click="step = 2" class="bg-gray-200 text-gray-800 px-6 py-2 rounded-md hover:bg-gray-300 transition font-bold">Précédent</button>
                            <button type="submit" class="bg-green-600 text-white px-8 py-2 rounded-md hover:bg-green-700 transition font-bold shadow-lg">Finaliser l'inscription</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.phone-input').forEach(input => {
                window.intlTelInput(input, {
                    initialCountry: "ci",
                    preferredCountries: ["ci", "fr", "sn", "bf"],
                    utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@23.0.10/build/js/utils.js",
                });
            });
        });
    </script>
</x-guest-layout>