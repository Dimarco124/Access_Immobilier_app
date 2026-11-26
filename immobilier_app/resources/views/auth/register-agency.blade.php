<x-guest-layout :fullWidth="true">
    <div class="min-h-screen bg-[#F4F6FA] py-8 px-4 sm:px-6 lg:px-8 relative overflow-hidden" x-data="registrationForm()">
        <div class="pointer-events-none fixed inset-x-0 top-24 -z-10 flex justify-between opacity-25">
            <div class="w-40 h-80 bg-gradient-to-r from-blue-400/40 to-transparent rounded-r-full blur-2xl"></div>
            <div class="w-40 h-80 bg-gradient-to-l from-indigo-400/40 to-transparent rounded-l-full blur-2xl"></div>
        </div>

        <!-- Top Bar -->
        <div class="max-w-5xl mx-auto mb-6 flex items-center justify-between gap-4">
            <a href="{{ url('/') }}" class="inline-flex items-center gap-2.5 px-4 py-2 rounded-full bg-white border border-slate-200/80 text-xs font-bold text-slate-600 hover:text-blue-600 hover:border-blue-200 shadow-xs transition group">
                <i class="fas fa-arrow-left text-[10px] text-slate-400 group-hover:text-blue-600 transition-transform group-hover:-translate-x-0.5"></i>
                <span>Retour à l'accueil</span>
            </a>

            <a href="{{ url('/') }}" class="hidden sm:inline-block transition-transform hover:scale-105">
                <img src="{{ asset('images/logo.svg') }}" alt="Habitat Immobilier" class="h-10 w-auto">
            </a>

            <div class="flex items-center gap-2">
                <span class="text-xs font-medium text-slate-500 hidden md:inline">Déjà inscrit ?</span>
                <a href="{{ route('login') }}" class="inline-flex items-center gap-1.5 px-5 py-2 rounded-full bg-blue-600 text-white hover:bg-blue-700 text-xs font-extrabold uppercase tracking-wider shadow-md shadow-blue-200/70 transition">
                    <i class="fas fa-sign-in-alt text-[10px]"></i>
                    <span>Connexion</span>
                </a>
            </div>
        </div>

        <!-- Header Hero Banner (Assorti au design d'accueil) -->
        <div class="max-w-5xl mx-auto mb-8">
            <div class="relative rounded-[2rem] bg-gradient-to-br from-blue-600 via-blue-500 to-indigo-600 p-7 sm:p-10 text-white overflow-hidden">
                <div class="pointer-events-none absolute -top-12 -right-12 w-48 h-48 rounded-full bg-white/10"></div>
                <div class="pointer-events-none absolute -bottom-14 left-1/3 w-36 h-36 rounded-full bg-white/10"></div>

                <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <div>
                        <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/15 text-[10px] font-black uppercase tracking-widest mb-3">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-300"></span>
                            Configuration Agence • 3 Étapes
                        </span>
                        <h1 class="text-2xl sm:text-4xl font-black tracking-tight">
                            Inscrire mon Agence
                        </h1>
                        <p class="mt-2 text-sm text-blue-50/90 font-medium max-w-xl">
                            Rejoignez les agences modernes et automatisez vos loyers, cautions, quittances et reversements dès aujourd'hui.
                        </p>
                    </div>
                    <div class="hidden md:flex items-center gap-3 bg-white/15 backdrop-blur-md border border-white/25 rounded-2xl px-5 py-3.5 shrink-0">
                        <div class="w-10 h-10 rounded-xl bg-white text-blue-600 flex items-center justify-center font-black">
                            <i class="fas fa-rocket"></i>
                        </div>
                        <div>
                            <p class="text-xs font-black">Prêt en 2 min</p>
                            <p class="text-[10px] text-blue-100 font-bold">Essai immédiat</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Carte Formulaire 3 étapes -->
        <div class="max-w-5xl mx-auto">
            <div class="bg-white py-8 px-6 sm:py-12 sm:px-14 rounded-[2.5rem] border border-slate-100 shadow-xl shadow-slate-200/50">

                <!-- Stepper -->
                <nav aria-label="Progress" class="mb-10">
                    <ol role="list" class="flex items-center justify-between w-full">
                        <template x-for="(label, index) in ['Agence', 'Légal & Logo', 'Administrateur']" :key="index">
                            <li class="relative flex flex-col items-center flex-1">
                                <div class="flex items-center justify-center">
                                    <div :class="step > index + 1 ? 'bg-emerald-500 shadow-emerald-200' : (step === index + 1 ? 'bg-gradient-to-br from-blue-600 to-indigo-600 scale-110 shadow-lg shadow-blue-200' : 'bg-slate-200 text-slate-500')"
                                        class="w-10 h-10 rounded-full flex items-center justify-center text-white font-black transition-all duration-300 z-10">
                                        <template x-if="step > index + 1">
                                            <i class="fas fa-check text-xs"></i>
                                        </template>
                                        <template x-if="step <= index + 1">
                                            <span x-text="index + 1" class="text-sm"></span>
                                        </template>
                                    </div>
                                    <template x-if="index < 2">
                                        <div :class="step > index + 1 ? 'bg-emerald-500' : 'bg-slate-200'"
                                            class="absolute left-1/2 w-full h-1 -translate-y-1/2 top-5 transition-all duration-300"></div>
                                    </template>
                                </div>
                                <span :class="step === index + 1 ? 'text-blue-600 font-black' : (step > index + 1 ? 'text-emerald-600 font-extrabold' : 'text-slate-400 font-bold')"
                                    class="mt-3 text-[10px] uppercase tracking-widest" x-text="label"></span>
                            </li>
                        </template>
                    </ol>
                </nav>

                <form method="POST" action="{{ route('register.agency.post') }}" enctype="multipart/form-data" class="space-y-8">
                    @csrf

                    @if ($errors->any())
                    <div class="bg-red-50 border border-red-200 rounded-2xl p-4 mb-6">
                        <div class="flex items-start gap-3">
                            <i class="fas fa-exclamation-circle text-red-500 mt-0.5"></i>
                            <div>
                                <p class="text-xs text-red-800 font-black uppercase tracking-wider">
                                    {{ __('Oups ! Il y a des erreurs dans le formulaire :') }}
                                </p>
                                <ul class="mt-1.5 list-disc list-inside text-xs text-red-600 font-medium space-y-0.5">
                                    @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Étape 1 : Identité Agence -->
                    <div x-show="step === 1" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div class="md:col-span-2">
                                <label for="nom_agence" class="block font-black text-[11px] uppercase tracking-widest text-slate-500 mb-2">
                                    {{ __('Nom Commercial de l\'agence') }} <span class="text-red-500">*</span>
                                </label>
                                <input id="nom_agence" name="nom_agence" type="text" value="{{ old('nom_agence') }}" required
                                    class="block w-full px-4 py-3.5 bg-[#F4F6FA] border border-slate-200/80 rounded-2xl text-sm font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all"
                                    placeholder="Ex: Habitat Immobilier Côte d'Ivoire">
                                <x-input-error :messages="$errors->get('nom_agence')" class="mt-2" />
                            </div>
                            <div>
                                <label for="email_agence" class="block font-black text-[11px] uppercase tracking-widest text-slate-500 mb-2">
                                    {{ __('Email Professionnel') }} <span class="text-red-500">*</span>
                                </label>
                                <input id="email_agence" name="email_agence" type="email" value="{{ old('email_agence') }}" required
                                    class="block w-full px-4 py-3.5 bg-[#F4F6FA] border border-slate-200/80 rounded-2xl text-sm font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all"
                                    placeholder="contact@agence.com">
                            </div>
                            <div>
                                <label for="telephone_agence" class="block font-black text-[11px] uppercase tracking-widest text-slate-500 mb-2">
                                    {{ __('Téléphone') }} <span class="text-red-500">*</span>
                                </label>
                                <input id="telephone_agence" name="telephone_agence" type="tel" value="{{ old('telephone_agence') }}" required
                                    class="block w-full px-4 py-3.5 bg-[#F4F6FA] border border-slate-200/80 rounded-2xl text-sm font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all phone-input">
                            </div>
                            <div class="md:col-span-2">
                                <label for="adresse" class="block font-black text-[11px] uppercase tracking-widest text-slate-500 mb-2">
                                    {{ __('Adresse du Siège') }} <span class="text-red-500">*</span>
                                </label>
                                <input id="adresse" name="adresse" type="text" value="{{ old('adresse') }}" required
                                    class="block w-full px-4 py-3.5 bg-[#F4F6FA] border border-slate-200/80 rounded-2xl text-sm font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all"
                                    placeholder="Cocody Angré, Rue des Jardins">
                            </div>
                            <div>
                                <label for="ville" class="block font-black text-[11px] uppercase tracking-widest text-slate-500 mb-2">
                                    {{ __('Ville') }} <span class="text-red-500">*</span>
                                </label>
                                <input id="ville" name="ville" type="text" value="{{ old('ville') }}" required
                                    class="block w-full px-4 py-3.5 bg-[#F4F6FA] border border-slate-200/80 rounded-2xl text-sm font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all"
                                    placeholder="Abidjan">
                            </div>
                            <div>
                                <label for="site_web" class="block font-black text-[11px] uppercase tracking-widest text-slate-500 mb-2">
                                    {{ __('Site Web (Optionnel)') }}
                                </label>
                                <input id="site_web" name="site_web" type="url" value="{{ old('site_web') }}" placeholder="https://"
                                    class="block w-full px-4 py-3.5 bg-[#F4F6FA] border border-slate-200/80 rounded-2xl text-sm font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all">
                            </div>
                        </div>
                        <div class="mt-8 flex justify-end">
                            <button type="button" @click="nextStep()"
                                class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white px-8 py-4 rounded-full font-black uppercase tracking-widest text-xs shadow-xl shadow-blue-200/70 transition transform hover:-translate-y-0.5 cursor-pointer">
                                <span>Continuer</span>
                                <i class="fas fa-arrow-right text-[11px]"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Étape 2 : Légal & Logo -->
                    <div x-show="step === 2" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-6">
                        <div class="space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <div>
                                    <label for="numero_agrement" class="block font-black text-[11px] uppercase tracking-widest text-slate-500 mb-2">
                                        {{ __('Numéro d\'agrément') }} <span class="text-blue-500 text-[10px] normal-case">(Optionnel)</span>
                                    </label>
                                    <input id="numero_agrement" name="numero_agrement" type="text" value="{{ old('numero_agrement') }}" placeholder="Ex: AGR-2026-..."
                                        class="block w-full px-4 py-3.5 bg-[#F4F6FA] border border-slate-200/80 rounded-2xl text-sm font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all">
                                </div>
                                <div>
                                    <label for="compte_contribuable" class="block font-black text-[11px] uppercase tracking-widest text-slate-500 mb-2">
                                        {{ __('Compte Contribuable') }} <span class="text-blue-500 text-[10px] normal-case">(Optionnel)</span>
                                    </label>
                                    <input id="compte_contribuable" name="compte_contribuable" type="text" value="{{ old('compte_contribuable') }}"
                                        class="block w-full px-4 py-3.5 bg-[#F4F6FA] border border-slate-200/80 rounded-2xl text-sm font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all">
                                </div>
                            </div>

                            <div class="flex items-center justify-center w-full">
                                <label for="logo" class="flex flex-col items-center justify-center w-full h-44 border-2 border-dashed border-blue-200 rounded-3xl cursor-pointer bg-[#F4F6FA] hover:bg-blue-50/50 transition">
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                        <div class="w-12 h-12 rounded-2xl bg-blue-100 text-blue-600 flex items-center justify-center mb-3">
                                            <i class="fas fa-cloud-upload-alt text-xl"></i>
                                        </div>
                                        <p class="mb-1 text-xs text-slate-700 font-black uppercase tracking-wider">Logo de l'agence <span class="text-blue-500 normal-case font-bold">(Optionnel)</span></p>
                                        <p class="text-[11px] text-slate-400 font-medium">PNG, JPG, GIF, SVG ou WebP (MAX. 5MB)</p>
                                    </div>
                                    <input type="file" name="logo" id="logo" class="hidden" accept="image/jpeg,image/png,image/jpg,image/gif,image/svg+xml,image/webp" @change="previewLogo($event)" />
                                </label>
                            </div>
                            <div x-show="logoPreview" class="mt-4 flex justify-center">
                                <img :src="logoPreview" class="h-20 w-auto rounded-xl shadow-md border border-slate-100 p-1 bg-white">
                            </div>
                            @error('logo')
                                <div class="mt-2 text-xs text-red-600 font-bold flex items-center gap-2">
                                    <i class="fas fa-exclamation-circle"></i>
                                    <span>{{ $message }}</span>
                                </div>
                            @enderror

                            <div>
                                <label for="description" class="block font-black text-[11px] uppercase tracking-widest text-slate-500 mb-2">
                                    {{ __('Présentation de l\'agence') }} <span class="text-red-500">*</span>
                                </label>
                                <textarea id="description" name="description" rows="3" required
                                    class="block w-full px-4 py-3.5 bg-[#F4F6FA] border border-slate-200/80 rounded-2xl text-sm font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all"
                                    placeholder="Décrivez brièvement votre activité et vos zones d'intervention...">{{ old('description') }}</textarea>
                            </div>
                        </div>
                        <div class="mt-8 flex justify-between items-center">
                            <button type="button" @click="step = 1" class="px-5 py-3 rounded-full bg-[#F4F6FA] text-slate-600 font-black uppercase tracking-widest text-xs hover:bg-slate-200/70 transition cursor-pointer">
                                <i class="fas fa-arrow-left mr-2"></i> Retour
                            </button>
                            <button type="button" @click="nextStep()"
                                class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white px-8 py-4 rounded-full font-black uppercase tracking-widest text-xs shadow-xl shadow-blue-200/70 transition transform hover:-translate-y-0.5 cursor-pointer">
                                <span>Continuer</span>
                                <i class="fas fa-arrow-right text-[11px]"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Étape 3 : Compte Admin -->
                    <div x-show="step === 3" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-6">
                        <div class="space-y-5">
                            <div class="bg-blue-50 p-5 rounded-2xl border border-blue-100">
                                <p class="text-blue-800 text-xs sm:text-sm font-bold leading-relaxed flex items-center gap-2.5">
                                    <i class="fas fa-info-circle text-blue-600 text-base shrink-0"></i>
                                    <span>Ces informations serviront à créer votre compte d'administrateur principal pour gérer l'agence.</span>
                                </p>
                            </div>
                            <div>
                                <label for="name" class="block font-black text-[11px] uppercase tracking-widest text-slate-500 mb-2">
                                    {{ __('Nom complet') }} <span class="text-red-500">*</span>
                                </label>
                                <input id="name" name="name" type="text" value="{{ old('name') }}" required
                                    class="block w-full px-4 py-3.5 bg-[#F4F6FA] border border-slate-200/80 rounded-2xl text-sm font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all">
                            </div>
                            <div>
                                <label for="email" class="block font-black text-[11px] uppercase tracking-widest text-slate-500 mb-2">
                                    {{ __('Email de connexion') }} <span class="text-red-500">*</span>
                                </label>
                                <input id="email" name="email" type="email" value="{{ old('email') }}" required
                                    class="block w-full px-4 py-3.5 bg-[#F4F6FA] border border-slate-200/80 rounded-2xl text-sm font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all">
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <div>
                                    <label for="password" class="block font-black text-[11px] uppercase tracking-widest text-slate-500 mb-2">
                                        {{ __('Mot de passe') }} <span class="text-red-500">*</span>
                                    </label>
                                    <input id="password" name="password" type="password" required autocomplete="new-password"
                                        class="block w-full px-4 py-3.5 bg-[#F4F6FA] border border-slate-200/80 rounded-2xl text-sm font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all"
                                        placeholder="••••••••">
                                </div>
                                <div>
                                    <label for="password_confirmation" class="block font-black text-[11px] uppercase tracking-widest text-slate-500 mb-2">
                                        {{ __('Confirmer') }} <span class="text-red-500">*</span>
                                    </label>
                                    <input id="password_confirmation" name="password_confirmation" type="password" required
                                        class="block w-full px-4 py-3.5 bg-[#F4F6FA] border border-slate-200/80 rounded-2xl text-sm font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all"
                                        placeholder="••••••••">
                                </div>
                            </div>
                        </div>
                        <div class="mt-8 flex justify-between items-center">
                            <button type="button" @click="step = 2" class="px-5 py-3 rounded-full bg-[#F4F6FA] text-slate-600 font-black uppercase tracking-widest text-xs hover:bg-slate-200/70 transition cursor-pointer">
                                <i class="fas fa-arrow-left mr-2"></i> Retour
                            </button>
                            <button type="submit"
                                class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white px-8 py-4 rounded-full font-black uppercase tracking-widest text-xs shadow-xl shadow-emerald-200/70 transition transform hover:-translate-y-0.5 cursor-pointer">
                                <span>Finaliser l'inscription</span>
                                <i class="fas fa-rocket text-[11px]"></i>
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
