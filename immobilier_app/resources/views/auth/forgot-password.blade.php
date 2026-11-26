<x-guest-layout :fullWidth="true">
    <div class="min-h-screen bg-[#F4F6FA] flex flex-col justify-center py-8 px-4 sm:px-6 lg:px-8 relative overflow-hidden">
        <div class="max-w-5xl w-full mx-auto">
            <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-xl shadow-slate-200/50 overflow-hidden grid lg:grid-cols-12">

                <!-- Colonne gauche : Panneau bleu/indigo -->
                <div class="hidden lg:block lg:col-span-5 p-4">
                    <div class="relative h-full min-h-[480px] rounded-[2rem] bg-gradient-to-br from-blue-600 via-blue-500 to-indigo-600 p-10 text-white overflow-hidden flex flex-col justify-between">
                        <div class="pointer-events-none absolute -top-14 -right-14 w-56 h-56 rounded-full bg-white/10"></div>
                        <div class="pointer-events-none absolute bottom-12 -left-12 w-44 h-44 rounded-full bg-white/10"></div>

                        <div class="relative z-10">
                            <span class="inline-block px-3 py-1 rounded-full bg-white/15 text-[10px] font-black uppercase tracking-widest mb-4">
                                Récupération Sécurisée
                            </span>
                            <h2 class="text-3xl font-black leading-tight tracking-tight mb-3">
                                Pas de panique.
                            </h2>
                            <p class="text-sm text-blue-50/90 leading-relaxed font-medium">
                                Entrez votre adresse email et nous vous enverrons un lien sécurisé pour réinitialiser votre mot de passe.
                            </p>
                        </div>

                        <div class="relative z-10 rounded-2xl overflow-hidden border-2 border-white/25 shadow-2xl">
                            <img src="https://images.unsplash.com/photo-1554224155-1696413565d3?ixlib=rb-4.0.3&auto=format&fit=crop&w=1170&q=80"
                                 alt="Récupération"
                                 class="w-full h-52 object-cover">
                        </div>
                    </div>
                </div>

                <!-- Colonne droite : Formulaire -->
                <div class="lg:col-span-7 p-7 sm:p-10 lg:p-12 flex flex-col justify-center">
                    <div class="flex items-center justify-between gap-4 mb-8">
                        <a href="{{ url('/') }}" class="inline-block transition-transform hover:scale-105">
                            <img src="{{ asset('images/logo.svg') }}" alt="Habitat Immobilier" class="h-10 w-auto">
                        </a>
                        <a href="{{ url('/') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-[#F4F6FA] border border-slate-200/80 text-xs font-bold text-slate-600 hover:text-blue-600 transition group">
                            <i class="fas fa-arrow-left text-[10px] text-slate-400 group-hover:text-blue-600"></i>
                            <span>Accueil</span>
                        </a>
                    </div>

                    <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-50 text-blue-600 text-[10px] font-black uppercase tracking-widest mb-3 w-fit">
                        Mot de passe oublié
                    </span>
                    <h1 class="text-3xl font-black text-slate-900 tracking-tight">
                        Réinitialisation
                    </h1>
                    <p class="mt-2 text-sm text-slate-500 font-medium">
                        Indiquez votre adresse email pour recevoir le lien de réinitialisation.
                    </p>

                    <x-auth-session-status class="mt-6 mb-4" :status="session('status')" />

                    <form method="POST" action="{{ route('password.email') }}" class="mt-8 space-y-6">
                        @csrf

                        <div>
                            <label for="email" class="block text-[11px] font-black text-slate-500 uppercase tracking-widest mb-2">
                                Votre Adresse Email
                            </label>
                            <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus
                                class="block w-full px-4 py-3.5 bg-[#F4F6FA] border border-slate-200/80 rounded-2xl text-sm font-bold text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all"
                                placeholder="nom@exemple.com">
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <div class="flex flex-col space-y-4">
                            <button type="submit"
                                class="w-full inline-flex items-center justify-center gap-2 py-4 px-6 rounded-full bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-black text-xs uppercase tracking-widest shadow-xl shadow-blue-200/70 transition transform hover:-translate-y-0.5 cursor-pointer">
                                <span>Envoyer le lien de réinitialisation</span>
                                <i class="fas fa-paper-plane text-[11px]"></i>
                            </button>

                            <a href="{{ route('login') }}" class="text-center text-xs font-extrabold text-slate-500 hover:text-blue-600 transition uppercase tracking-wider">
                                <i class="fas fa-arrow-left mr-1.5"></i> Retour à la connexion
                            </a>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
</x-guest-layout>
