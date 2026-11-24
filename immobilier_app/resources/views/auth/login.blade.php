<x-guest-layout :fullWidth="true">
    <div class="min-h-screen bg-white flex">
        <!-- Left side: Form -->
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
                    <h2 class="mt-8 text-4xl font-black text-slate-900 uppercase tracking-tight leading-none">
                        Connexion
                    </h2>
                    <p class="mt-4 text-lg text-slate-500 font-medium">
                        Heureux de vous revoir ! Accédez à votre espace de gestion immobilière.
                    </p>
                </div>

                <div class="mt-12">
                    <div class="mt-6">
                        <form method="POST" action="{{ route('login') }}" class="space-y-8">
                            @csrf

                            <div>
                                <label for="email" class="block text-xs font-black text-gray-400 uppercase tracking-[0.2em] mb-3">
                                    Adresse Email Professionnelle
                                </label>
                                <div class="mt-1">
                                    <input id="email" name="email" type="email" autocomplete="email" required class="appearance-none block w-full px-5 py-4 border border-gray-100 bg-gray-50 rounded-2xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm font-bold transition-all" value="{{ old('email') }}" placeholder="nom@agence.com">
                                </div>
                                <x-input-error :messages="$errors->get('email')" class="mt-2" />
                            </div>

                            <div class="space-y-1">
                                <div class="flex items-center justify-between mb-3">
                                    <label for="password" class="block text-xs font-black text-gray-400 uppercase tracking-[0.2em]">
                                        Mot de passe
                                    </label>
                                    @if (Route::has('password.request'))
                                    <a href="{{ route('password.request') }}" class="text-xs font-black text-blue-600 hover:text-blue-500 uppercase tracking-widest">
                                        Oublié ?
                                    </a>
                                    @endif
                                </div>
                                <div class="mt-1">
                                    <input id="password" name="password" type="password" autocomplete="current-password" required class="appearance-none block w-full px-5 py-4 border border-gray-100 bg-gray-50 rounded-2xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm font-bold transition-all" placeholder="••••••••">
                                </div>
                                <x-input-error :messages="$errors->get('password')" class="mt-2" />
                            </div>

                            <div class="flex items-center">
                                <input id="remember_me" name="remember" type="checkbox" class="h-5 w-5 text-blue-600 focus:ring-blue-500 border-gray-200 rounded-lg transition-all cursor-pointer">
                                <label for="remember_me" class="ml-3 block text-sm font-bold text-gray-600 cursor-pointer">
                                    Rester connecté pour 30 jours
                                </label>
                            </div>

                            <div>
                                <button type="submit" class="w-full flex justify-center py-5 px-4 border border-transparent rounded-2xl shadow-xl shadow-blue-100 text-sm font-black text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 uppercase tracking-widest transition transform hover:-translate-y-1 active:scale-95">
                                    Se Connecter à l'Espace
                                </button>
                            </div>
                        </form>
                    </div>

                    <div class="mt-12 pt-8 border-t border-gray-50">
                        <p class="text-sm text-gray-500 font-bold text-center">
                            Nouvelle agence ?
                            <a href="{{ route('register.agency') }}" class="text-blue-600 font-black hover:text-blue-700 ml-1 underline underline-offset-4 decoration-2">
                                Inscrire mon agence gratuitement
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right side: Image/Branding -->
        <div class="hidden lg:block relative w-0 flex-1">
            <img class="absolute inset-0 h-full w-full object-cover" src="https://images.unsplash.com/photo-1560518883-ce09059eeffa?ixlib=rb-4.0.3&ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&auto=format&fit=crop&w=1073&q=80" alt="Immobilier Moderne">
            <div class="absolute inset-0 bg-blue-600 mix-blend-multiply opacity-30"></div>
            <div class="absolute inset-0 flex items-center justify-center p-20 text-white">
                <div class="max-w-md">
                    <h2 class="text-5xl font-black uppercase tracking-tighter leading-none mb-6">La gestion immobilière, réinventée.</h2>
                    <p class="text-xl font-medium text-blue-50 leading-relaxed">Automatisez vos processus, sécurisez vos revenus et offrez une expérience premium à vos locataires.</p>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>