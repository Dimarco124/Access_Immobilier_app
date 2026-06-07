<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Modifier le Locataire : ') }} {{ $locataire->user->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('locataires.update', $locataire) }}" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    @method('PATCH')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="name" :value="__('Nom complet')" />
                            <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $locataire->user->name)" required />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="email" :value="__('Email')" />
                            <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $locataire->user->email)" required />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="telephone" :value="__('Téléphone')" />
                            <x-text-input id="telephone" name="telephone" type="tel" class="mt-1 block w-full phone-input" :value="old('telephone', $locataire->user->telephone)" />
                            <x-input-error :messages="$errors->get('telephone')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="contact_urgence" :value="__('Contact d\'urgence')" />
                            <x-text-input id="contact_urgence" name="contact_urgence" type="tel" class="mt-1 block w-full phone-input" :value="old('contact_urgence', $locataire->contact_urgence)" />
                            <x-input-error :messages="$errors->get('contact_urgence')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="profession" :value="__('Profession')" />
                            <x-text-input id="profession" name="profession" type="text" class="mt-1 block w-full" :value="old('profession', $locataire->profession)" />
                            <x-input-error :messages="$errors->get('profession')" class="mt-2" />
                        </div>

                        <div class="md:col-span-2">
                            <x-input-label for="copie_piece_identite" :value="__('Copie pièce d\'identité (PDF, JPG, PNG)')" />
                            @if($locataire->copie_piece_identite)
                            <div class="mb-2">
                                <a href="{{ Storage::url($locataire->copie_piece_identite) }}" target="_blank" class="text-blue-600 underline text-sm">Voir la pièce actuelle</a>
                            </div>
                            @endif
                            <input type="file" name="copie_piece_identite" id="copie_piece_identite" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                            <x-input-error :messages="$errors->get('copie_piece_identite')" class="mt-2" />
                        </div>
                    </div>

                    <div class="flex items-center justify-end mt-4">
                        <x-primary-button class="ml-4">
                            {{ __('Mettre à jour') }}
                        </x-primary-button>
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
</x-app-layout>