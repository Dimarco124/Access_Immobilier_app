<x-guest-layout :fullWidth="true">
    <div class="min-h-screen bg-white flex">
        <!-- Left side: Image/Branding -->
        <div class="hidden lg:block relative w-0 flex-1">
            <img class="absolute inset-0 h-full w-full object-cover" src="https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=1170&q=80" alt="Immobilier Bureau">
            <div class="absolute inset-0 bg-blue-600 mix-blend-multiply opacity-20"></div>
            <div class="absolute inset-0 flex items-center justify-center p-20 text-white">
                <div class="max-w-md text-right">
                    <h2 class="text-5xl font-black uppercase tracking-tighter leading-none mb-6 text-white">L'excellence au service de vos biens.</h2>
                    <p class="text-xl font-medium text-blue-50 leading-relaxed">Une plateforme robuste conçue pour les professionnels exigeants et leurs clients.</p>
                </div>
            </div>
        </div>

        <!-- Right side: Form -->
        <div class="flex-1 flex flex-col justify-center py-12 px-4 sm:px-6 lg:flex-none lg:px-20 xl:px-24 bg-white z-10">
            <div class="mx-auto w-full max-w-md lg:w-[480px]">
                <!-- Bouton Retour à l'accueil -->
                <div class="mb-8">
                    <a href="{{ url('/') }}" class="inline-flex items-center gap-2.5 px-3.5 py-2 rounded-xl bg-gray-50 border border-gray-200 text-xs font-bold text-gray-700 hover:text-blue-600 hover:border-blue-200 hover:bg-blue-50/50 shadow-sm transition-all hover:-translate-x-0.5 group">
                        <i class="fas fa-arrow-left text-[11px] text-gray-400 group-hover:text-blue-600 transition-transform group-hover:-translate-x-1"></i>
                        <span>Retour à l'accueil</span>
                    </a>
                </div>

                <div>
                    <a href="{{ url('/') }}" class="inline-block transition-transform hover:scale-105">
                        <img src="{{ asset('images/logo.svg') }}" alt="Habitat Immobilier" class="h-12 w-auto">
                    </a>
                    <h2 class="mt-8 text-4xl font-black text-slate-900 uppercase tracking-tight leading-none">Inscription</h2>
                    <p class="mt-4 text-lg text-slate-500 font-medium">
                        Créez votre compte utilisateur pour accéder aux services immobiliers premium.
                    </p>
                </div>

                <div class="mt-12">
                    <form method="POST" action="{{ route('register') }}" class="space-y-8">
                        @csrf

                        <!-- Name -->
                        <div>
                            <label for="name" class="block text-xs font-black text-gray-400 uppercase tracking-[0.2em] mb-3">
                                Nom Complet
                            </label>
                            <div class="mt-1">
                                <input id="name" name="name" type="text" :value="old('name')" required autofocus autocomplete="name" class="appearance-none block w-full px-5 py-4 border border-gray-100 bg-gray-50 rounded-2xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm font-bold transition-all" placeholder="John Doe">
                            </div>
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <!-- Email Address -->
                        <div>
                            <label for="email" class="block text-xs font-black text-gray-400 uppercase tracking-[0.2em] mb-3">
                                Adresse Email
                            </label>
                            <div class="mt-1">
                                <input id="email" name="email" type="email" :value="old('email')" required autocomplete="username" class="appearance-none block w-full px-5 py-4 border border-gray-100 bg-gray-50 rounded-2xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm font-bold transition-all" placeholder="john@example.com">
                            </div>
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Password -->
                            <div>
                                <label for="password" class="block text-xs font-black text-gray-400 uppercase tracking-[0.2em] mb-3">
                                    Mot de passe
                                </label>
                                <div class="mt-1">
                                    <input id="password" name="password" type="password" required autocomplete="new-password" class="appearance-none block w-full px-5 py-4 border border-gray-100 bg-gray-50 rounded-2xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm font-bold transition-all" placeholder="••••••••">
                                </div>
                                <x-input-error :messages="$errors->get('password')" class="mt-2" />
                            </div>

                            <!-- Confirm Password -->
                            <div>
                                <label for="password_confirmation" class="block text-xs font-black text-gray-400 uppercase tracking-[0.2em] mb-3">
                                    Confirmer
                                </label>
                                <div class="mt-1">
                                    <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password" class="appearance-none block w-full px-5 py-4 border border-gray-100 bg-gray-50 rounded-2xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm font-bold transition-all" placeholder="••••••••">
                                </div>
                                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex items-center justify-between pt-4">
                            <a class="text-sm font-bold text-blue-600 hover:text-blue-700 underline underline-offset-4 decoration-2" href="{{ route('login') }}">
                                Déjà un compte ?
                            </a>

                            <button type="submit" class="inline-flex justify-center py-5 px-10 border border-transparent rounded-2xl shadow-xl shadow-blue-100 text-sm font-black text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 uppercase tracking-widest transition transform hover:-translate-y-1 active:scale-95">
                                S'Inscrire
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
