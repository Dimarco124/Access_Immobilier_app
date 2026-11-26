<x-guest-layout :fullWidth="true">
    <div class="min-h-screen bg-[#F4F6FA] flex flex-col justify-center py-8 px-4 sm:px-6 lg:px-8 relative overflow-hidden">
        <div class="pointer-events-none fixed inset-x-0 top-24 -z-10 flex justify-between opacity-25">
            <div class="w-40 h-80 bg-gradient-to-r from-blue-400/40 to-transparent rounded-r-full blur-2xl"></div>
            <div class="w-40 h-80 bg-gradient-to-l from-indigo-400/40 to-transparent rounded-l-full blur-2xl"></div>
        </div>

        <div class="max-w-6xl w-full mx-auto">
            <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-xl shadow-slate-200/50 overflow-hidden grid lg:grid-cols-12">

                <!-- Colonne gauche : Panneau bleu/indigo -->
                <div class="hidden lg:block lg:col-span-5 p-4">
                    <div class="relative h-full min-h-[540px] rounded-[2rem] bg-gradient-to-br from-blue-600 via-blue-500 to-indigo-600 p-10 text-white overflow-hidden flex flex-col justify-between">
                        <div class="pointer-events-none absolute -top-14 -right-14 w-56 h-56 rounded-full bg-white/10"></div>
                        <div class="pointer-events-none absolute bottom-16 -left-16 w-48 h-48 rounded-full bg-white/10"></div>

                        <div class="relative z-10">
                            <span class="inline-block px-3 py-1 rounded-full bg-white/15 text-[10px] font-black uppercase tracking-widest mb-4">
                                Nouveau Compte
                            </span>
                            <h2 class="text-3xl font-black leading-tight tracking-tight mb-3">
                                L'excellence au service de vos biens.
                            </h2>
                            <p class="text-sm text-blue-50/90 leading-relaxed font-medium">
                                Une plateforme robuste conçue pour les professionnels exigeants et leurs clients.
                            </p>
                        </div>

                        <div class="relative z-10 my-6">
                            <div class="rounded-2xl overflow-hidden border-2 border-white/25 shadow-2xl">
                                <img src="https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?ixlib=rb-4.0.3&auto=format&fit=crop&w=1170&q=80"
                                     alt="Immobilier Bureau"
                                     class="w-full h-56 object-cover">
                            </div>
                        </div>

                        <div class="relative z-10 bg-white/15 backdrop-blur-md border border-white/20 rounded-2xl p-4">
                            <p class="text-xs font-bold text-blue-50 leading-relaxed">
                                <i class="fas fa-building mr-2"></i> Vous dirigez une agence immobilière ?
                                <a href="{{ route('register.agency') }}" class="underline font-black text-white ml-1">Inscrire mon agence →</a>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Colonne droite : Formulaire -->
                <div class="lg:col-span-7 p-7 sm:p-10 lg:p-12 flex flex-col justify-center">
                    <div class="flex items-center justify-between gap-4 mb-8">
                        <a href="{{ url('/') }}" class="inline-block transition-transform hover:scale-105">
                            <img src="{{ asset('images/logo.svg') }}" alt="Habitat Immobilier" class="h-10 w-auto">
                        </a>
                        <a href="{{ url('/') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-[#F4F6FA] border border-slate-200/80 text-xs font-bold text-slate-600 hover:text-blue-600 hover:border-blue-200 transition group">
                            <i class="fas fa-arrow-left text-[10px] text-slate-400 group-hover:text-blue-600 transition-transform group-hover:-translate-x-0.5"></i>
                            <span>Accueil</span>
                        </a>
                    </div>

                    <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-50 text-blue-600 text-[10px] font-black uppercase tracking-widest mb-3 w-fit">
                        <span class="w-1.5 h-1.5 rounded-full bg-blue-600"></span>
                        Création de compte
                    </span>
                    <h1 class="text-3xl sm:text-4xl font-black text-slate-900 tracking-tight leading-tight">
                        Inscription
                    </h1>
                    <p class="mt-2 text-sm text-slate-500 font-medium">
                        Créez votre compte utilisateur pour accéder aux services immobiliers.
                    </p>

                    <form method="POST" action="{{ route('register') }}" class="mt-8 space-y-5">
                        @csrf

                        <div>
                            <label for="name" class="block text-[11px] font-black text-slate-500 uppercase tracking-widest mb-2">
                                Nom Complet
                            </label>
                            <input id="name" name="name" type="text" value="{{ old('name') }}" required autofocus autocomplete="name"
                                class="block w-full px-4 py-3.5 bg-[#F4F6FA] border border-slate-200/80 rounded-2xl text-sm font-bold text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all"
                                placeholder="Kouassi Valdes">
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <div>
                            <label for="email" class="block text-[11px] font-black text-slate-500 uppercase tracking-widest mb-2">
                                Adresse Email
                            </label>
                            <input id="email" name="email" type="email" value="{{ old('email') }}" required autocomplete="username"
                                class="block w-full px-4 py-3.5 bg-[#F4F6FA] border border-slate-200/80 rounded-2xl text-sm font-bold text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all"
                                placeholder="nom@exemple.com">
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="password" class="block text-[11px] font-black text-slate-500 uppercase tracking-widest mb-2">
                                    Mot de passe
                                </label>
                                <input id="password" name="password" type="password" required autocomplete="new-password"
                                    class="block w-full px-4 py-3.5 bg-[#F4F6FA] border border-slate-200/80 rounded-2xl text-sm font-bold text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all"
                                    placeholder="••••••••">
                                <x-input-error :messages="$errors->get('password')" class="mt-2" />
                            </div>

                            <div>
                                <label for="password_confirmation" class="block text-[11px] font-black text-slate-500 uppercase tracking-widest mb-2">
                                    Confirmer
                                </label>
                                <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password"
                                    class="block w-full px-4 py-3.5 bg-[#F4F6FA] border border-slate-200/80 rounded-2xl text-sm font-bold text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all"
                                    placeholder="••••••••">
                                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                            </div>
                        </div>

                        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-3">
                            <a class="text-xs font-extrabold text-blue-600 hover:text-blue-700 underline underline-offset-4 decoration-2" href="{{ route('login') }}">
                                Déjà un compte ? Se connecter
                            </a>

                            <button type="submit"
                                class="w-full sm:w-auto inline-flex items-center justify-center gap-2.5 py-4 px-8 rounded-full bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-black text-xs uppercase tracking-widest shadow-xl shadow-blue-200/70 transition transform hover:-translate-y-0.5 active:scale-95 cursor-pointer">
                                <span>S'Inscrire</span>
                                <i class="fas fa-arrow-right text-[11px]"></i>
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
</x-guest-layout>
