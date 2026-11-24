<x-guest-layout :fullWidth="true">
    <div class="min-h-screen bg-gray-50 flex flex-col justify-center py-8 sm:py-12 sm:px-6 lg:px-8" x-data="registrationForm()">
        <!-- Top Navigation Bar avec bouton Retour -->
        <div class="w-full max-w-5xl mx-auto px-4 sm:px-6 mb-8 flex items-center justify-between">
            <a href="{{ url('/') }}" class="inline-flex items-center gap-2.5 px-4 py-2.5 rounded-xl bg-white border border-gray-200 text-xs font-bold text-gray-700 hover:text-blue-600 hover:border-blue-200 shadow-sm transition-all hover:-translate-x-0.5 group">
                <i class="fas fa-arrow-left text-[11px] text-gray-400 group-hover:text-blue-600 transition-transform group-hover:-translate-x-1"></i>
                <span>Retour à l'accueil</span>
            </a>

            <div class="flex items-center gap-2">
                <span class="text-xs font-medium text-gray-500 hidden sm:inline">Déjà inscrit ?</span>
                <a href="{{ route('login') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-blue-50 text-blue-600 hover:bg-blue-100 text-xs font-extrabold uppercase tracking-wider transition-colors">
                    <i class="fas fa-sign-in-alt text-[11px]"></i>
                    <span>Connexion</span>
                </a>
            </div>
        </div>

        <div class="sm:mx-auto sm:w-full sm:max-w-4xl">
            <div class="flex justify-center">
                <a href="{{ url('/') }}" class="inline-block transition-transform hover:scale-105">
                    <img src="{{ asset('images/logo.svg') }}" alt="Habitat Immobilier" class="h-14 w-auto">
                </a>
            </div>
            <h2 class="mt-6 text-center text-4xl font-black text-gray-900 uppercase tracking-tight">
                Inscrire mon Agence
            </h2>
            <p class="mt-2 text-center text-lg text-gray-600 font-medium">
                Rejoignez les meilleures agences immobilières et passez à la vitesse supérieure.
            </p>
        </div>

        <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-5xl">
            <div class="bg-white py-12 px-8 shadow-2xl shadow-blue-100 sm:rounded-3xl sm:px-16 border border-gray-100">

                <!-- Stepper -->
                <nav aria-label="Progress" class="mb-12">
                    <ol role="list" class="flex items-center justify-between w-full">
                        <template x-for="(label, index) in ['Agence', 'Légal & Logo', 'Administrateur']" :key="index">
                            <li class="relative flex flex-col items-center flex-1">
                                <div class="flex items-center justify-center">
                                    <div :class="step > index + 1 ? 'bg-emerald-500' : (step === index + 1 ? 'bg-slate-900 scale-110 shadow-lg' : 'bg-slate-200')"
                                        class="w-10 h-10 rounded-full flex items-center justify-center text-white font-black transition-all duration-500 z-10">
                                        <template x-if="step > index + 1">
                                            <i class="fas fa-check"></i>
                                        </template>
                                        <template x-if="step <= index + 1">
                                            <span x-text="index + 1"></span>
                                        </template>
                                    </div>
                                    <template x-if="index < 2">
                                        <div :class="step > index + 1 ? 'bg-emerald-500' : 'bg-slate-200'"
                                            class="absolute left-1/2 w-full h-1 -translate-y-1/2 top-5 transition-all duration-500"></div>
                                    </template>
                                </div>
                                <span :class="step === index + 1 ? 'text-slate-900 font-black' : 'text-slate-400 font-bold'"
                                    class="mt-3 text-[10px] uppercase tracking-widest" x-text="label"></span>
                            </li>
                        </template>
                    </ol>
                </nav>

                <form method="POST" action="{{ route('register.agency.post') }}" enctype="multipart/form-data" class="space-y-8">
                    @csrf

                    @if ($errors->any())
                    <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-6">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-exclamation-circle text-red-400"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-red-700 font-bold">
                                    {{ __('Oups ! Il y a des erreurs dans le formulaire :') }}
                                </p>
                                <ul class="mt-1 list-disc list-inside text-xs text-red-600">
                                    @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Etape 1 : Identité Agence -->
                    <div x-show="step === 1" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-8">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="md:col-span-2">
                                <x-input-label for="nom_agence" class="font-black text-xs uppercase text-gray-500 mb-2">
                                    {{ __('Nom Commercial de l\'agence') }} <span class="text-red-500">*</span>
                                </x-input-label>
                                <x-text-input id="nom_agence" name="nom_agence" type="text" class="block w-full border-gray-200 focus:ring-blue-500 focus:border-blue-500 rounded-xl" :value="old('nom_agence')" required />
                                <x-input-error :messages="$errors->get('nom_agence')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="email_agence" class="font-black text-xs uppercase text-gray-500 mb-2">
                                    {{ __('Email Professionnel') }} <span class="text-red-500">*</span>
                                </x-input-label>
                                <x-text-input id="email_agence" name="email_agence" type="email" class="block w-full border-gray-200 focus:ring-blue-500 focus:border-blue-500 rounded-xl" :value="old('email_agence')" required />
                            </div>
                            <div>
                                <x-input-label for="telephone_agence" class="font-black text-xs uppercase text-gray-500 mb-2">
                                    {{ __('Téléphone') }} <span class="text-red-500">*</span>
                                </x-input-label>
                                <x-text-input id="telephone_agence" name="telephone_agence" type="tel" class="block w-full border-gray-200 focus:ring-blue-500 focus:border-blue-500 rounded-xl phone-input" :value="old('telephone_agence')" required />
                            </div>
                            <div class="md:col-span-2">
                                <x-input-label for="adresse" class="font-black text-xs uppercase text-gray-500 mb-2">
                                    {{ __('Adresse du Siège') }} <span class="text-red-500">*</span>
                                </x-input-label>
                                <x-text-input id="adresse" name="adresse" type="text" class="block w-full border-gray-200 focus:ring-blue-500 focus:border-blue-500 rounded-xl" :value="old('adresse')" required />
                            </div>
                            <div>
                                <x-input-label for="ville" class="font-black text-xs uppercase text-gray-500 mb-2">
                                    {{ __('Ville') }} <span class="text-red-500">*</span>
                                </x-input-label>
                                <x-text-input id="ville" name="ville" type="text" class="block w-full border-gray-200 focus:ring-blue-500 focus:border-blue-500 rounded-xl" :value="old('ville')" required />
                            </div>
                            <div>
                                <x-input-label for="site_web" class="font-black text-xs uppercase text-gray-500 mb-2">
                                    {{ __('Site Web (Optionnel)') }}
                                </x-input-label>
                                <x-text-input id="site_web" name="site_web" type="url" class="block w-full border-gray-200 focus:ring-blue-500 focus:border-blue-500 rounded-xl" :value="old('site_web')" placeholder="https://" />
                            </div>
                        </div>
                        <div class="mt-10 flex justify-end">
                            <button type="button" @click="nextStep()" class="bg-blue-600 text-white px-10 py-4 rounded-xl font-black uppercase tracking-widest text-sm hover:bg-blue-700 shadow-xl shadow-blue-200 transition transform hover:-translate-y-1">
                                Continuer <i class="fas fa-arrow-right ml-2"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Etape 2 : Légal & Logo -->
                    <div x-show="step === 2" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-8">
                        <div class="space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <x-input-label for="numero_agrement" class="font-black text-xs uppercase text-gray-500 mb-2">
                                        {{ __('Numéro d\'agrément') }} <span class="text-blue-400 text-[10px] normal-case">(Optionnel)</span>
                                        <span class="ml-1 text-[10px] text-gray-400 normal-case font-medium">(Autorisation d'exercice)</span>
                                    </x-input-label>
                                    <x-text-input id="numero_agrement" name="numero_agrement" type="text" class="block w-full border-gray-200 focus:ring-blue-500 focus:border-blue-500 rounded-xl" :value="old('numero_agrement')" placeholder="Ex: AGR-2024-..." />
                                </div>
                                <div>
                                    <x-input-label for="compte_contribuable" class="font-black text-xs uppercase text-gray-500 mb-2">
                                        {{ __('Compte Contribuable') }} <span class="text-blue-400 text-[10px] normal-case">(Optionnel)</span>
                                        <span class="ml-1 text-[10px] text-gray-400 normal-case font-medium">(Identifiant Fiscal)</span>
                                    </x-input-label>
                                    <x-text-input id="compte_contribuable" name="compte_contribuable" type="text" class="block w-full border-gray-200 focus:ring-blue-500 focus:border-blue-500 rounded-xl" :value="old('compte_contribuable')" />
                                </div>
                            </div>

                            <div class="flex items-center justify-center w-full">
                                <label for="logo" class="flex flex-col items-center justify-center w-full h-48 border-2 border-dashed border-gray-300 rounded-3xl cursor-pointer bg-gray-50 hover:bg-gray-100 transition">
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                        <i class="fas fa-cloud-upload-alt text-4xl text-blue-500 mb-3"></i>
                                        <p class="mb-2 text-sm text-gray-500 font-bold uppercase tracking-tighter">Logo de l'agence <span class="text-blue-400 text-[10px] normal-case">(Optionnel)</span></p>
                                        <p class="text-xs text-gray-400 font-medium">PNG, JPG, GIF, SVG ou WebP (MAX. 5MB)</p>
                                    </div>
                                    <input type="file" name="logo" id="logo" class="hidden" accept="image/jpeg,image/png,image/jpg,image/gif,image/svg+xml,image/webp" @change="previewLogo($event)" />
                                </label>
                            </div>
                            <div x-show="logoPreview" class="mt-4 flex justify-center">
                                <img :src="logoPreview" class="h-20 w-auto rounded-lg shadow-md border border-gray-100">
                            </div>
                            @error('logo')
                                <div class="mt-2 text-sm text-red-600 font-bold flex items-center gap-2">
                                    <i class="fas fa-exclamation-circle"></i>
                                    <span>{{ $message }}</span>
                                </div>
                            @enderror

                            <div>
                                <x-input-label for="description" class="font-black text-xs uppercase text-gray-500 mb-2">
                                    {{ __('Présentation de l\'agence') }} <span class="text-red-500">*</span>
                                </x-input-label>
                                <textarea id="description" name="description" class="block w-full border-gray-200 focus:ring-blue-500 focus:border-blue-500 rounded-xl" rows="3" required>{{ old('description') }}</textarea>
                            </div>
                        </div>
                        <div class="mt-10 flex justify-between">
                            <button type="button" @click="step = 1" class="text-gray-400 font-black uppercase tracking-widest text-xs hover:text-gray-600 transition">
                                <i class="fas fa-arrow-left mr-2"></i> Retour
                            </button>
                            <button type="button" @click="nextStep()" class="bg-blue-600 text-white px-10 py-4 rounded-xl font-black uppercase tracking-widest text-sm hover:bg-blue-700 shadow-xl shadow-blue-200 transition transform hover:-translate-y-1">
                                Continuer <i class="fas fa-arrow-right ml-2"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Etape 3 : Compte Admin -->
                    <div x-show="step === 3" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-8">
                        <div class="space-y-6">
                            <div class="bg-blue-50 p-6 rounded-2xl border border-blue-100 mb-6">
                                <p class="text-blue-800 text-sm font-bold leading-relaxed">
                                    <i class="fas fa-info-circle mr-2"></i> Ces informations serviront à créer votre compte d'administrateur principal pour gérer l'agence.
                                </p>
                            </div>
                            <div>
                                <x-input-label for="name" class="font-black text-xs uppercase text-gray-500 mb-2">
                                    {{ __('Nom complet') }} <span class="text-red-500">*</span>
                                </x-input-label>
                                <x-text-input id="name" name="name" type="text" class="block w-full border-gray-200 focus:ring-blue-500 focus:border-blue-500 rounded-xl" :value="old('name')" required />
                            </div>
                            <div>
                                <x-input-label for="email" class="font-black text-xs uppercase text-gray-500 mb-2">
                                    {{ __('Email de connexion') }} <span class="text-red-500">*</span>
                                </x-input-label>
                                <x-text-input id="email" name="email" type="email" class="block w-full border-gray-200 focus:ring-blue-500 focus:border-blue-500 rounded-xl" :value="old('email')" required />
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <x-input-label for="password" class="font-black text-xs uppercase text-gray-500 mb-2">
                                        {{ __('Mot de passe') }} <span class="text-red-500">*</span>
                                    </x-input-label>
                                    <x-text-input id="password" name="password" type="password" class="block w-full border-gray-200 focus:ring-blue-500 focus:border-blue-500 rounded-xl" required autocomplete="new-password" />
                                </div>
                                <div>
                                    <x-input-label for="password_confirmation" class="font-black text-xs uppercase text-gray-500 mb-2">
                                        {{ __('Confirmer') }} <span class="text-red-500">*</span>
                                    </x-input-label>
                                    <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="block w-full border-gray-200 focus:ring-blue-500 focus:border-blue-500 rounded-xl" required />
                                </div>
                            </div>
                        </div>
                        <div class="mt-10 flex justify-between items-center">
                            <button type="button" @click="step = 2" class="text-gray-400 font-black uppercase tracking-widest text-xs hover:text-gray-600 transition">
                                <i class="fas fa-arrow-left mr-2"></i> Retour
                            </button>
                            <button type="submit" class="bg-green-600 text-white px-10 py-4 rounded-xl font-black uppercase tracking-widest text-sm hover:bg-green-700 shadow-xl shadow-green-200 transition transform hover:-translate-y-1">
                                Finaliser <i class="fas fa-rocket ml-2"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function registrationForm() {
            return {
                step: 1,
                logoPreview: null,
                nextStep() {
                    // Validation par étape
                    if (this.step === 1) {
                        const fields = ['nom_agence', 'email_agence', 'telephone_agence', 'adresse', 'ville'];
                        for (const fieldId of fields) {
                            const input = document.getElementById(fieldId);
                            if (!input.value.trim()) {
                                alert('Veuillez remplir tous les champs obligatoires (indiqués par une étoile rouge).');
                                input.focus();
                                return;
                            }
                        }
                    } else if (this.step === 2) {
                        const desc = document.getElementById('description').value.trim();

                        if (!desc) {
                            alert('Veuillez fournir la présentation de l\'agence.');
                            document.getElementById('description').focus();
                            return;
                        }
                    }
                    this.step++;
                },
                previewLogo(event) {
                    const file = event.target.files[0];
                    if (file) {
                        this.logoPreview = URL.createObjectURL(file);
                    }
                }
            }
        }

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