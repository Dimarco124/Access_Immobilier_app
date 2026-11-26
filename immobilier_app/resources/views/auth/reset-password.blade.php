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
                                Protection du Compte
                            </span>
                            <h2 class="text-3xl font-black leading-tight tracking-tight mb-3">
                                Sécurisez votre accès.
                            </h2>
                            <p class="text-sm text-blue-50/90 leading-relaxed font-medium">
                                Choisissez un nouveau mot de passe robuste pour protéger vos données immobilières.
                            </p>
                        </div>

                        <div class="relative z-10 rounded-2xl overflow-hidden border-2 border-white/25 shadow-2xl">
                            <img src="https://images.unsplash.com/photo-1554224155-1696413565d3?ixlib=rb-4.0.3&auto=format&fit=crop&w=1170&q=80"
                                 alt="Sécurité"
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
                        <a href="{{ route('login') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-[#F4F6FA] border border-slate-200/80 text-xs font-bold text-slate-600 hover:text-blue-600 transition">
                            <span>Connexion</span>
                        </a>
                    </div>

                    <h1 class="text-3xl font-black text-slate-900 tracking-tight">
                        Nouveau mot de passe
                    </h1>
                    <p class="mt-2 text-sm text-slate-500 font-medium">
                        Réinitialisez votre accès en quelques secondes.
                    </p>

                    <form method="POST" action="{{ route('password.store') }}" class="mt-8 space-y-5">
                        @csrf
                        <input type="hidden" name="token" value="{{ $request->route('token') }}">

                        <div>
                            <label for="email" class="block text-[11px] font-black text-slate-500 uppercase tracking-widest mb-2">
                                Email
                            </label>
                            <input id="email" name="email" type="email" value="{{ old('email', $request->email) }}" required autofocus autocomplete="username"
                                class="block w-full px-4 py-3.5 bg-[#F4F6FA] border border-slate-200/80 rounded-2xl text-sm font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all">
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="password" class="block text-[11px] font-black text-slate-500 uppercase tracking-widest mb-2">
                                    Mot de passe
                                </label>
                                <input id="password" name="password" type="password" required autocomplete="new-password"
                                    class="block w-full px-4 py-3.5 bg-[#F4F6FA] border border-slate-200/80 rounded-2xl text-sm font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all"
                                    placeholder="••••••••">
                                <x-input-error :messages="$errors->get('password')" class="mt-2" />
                            </div>

                            <div>
                                <label for="password_confirmation" class="block text-[11px] font-black text-slate-500 uppercase tracking-widest mb-2">
                                    Confirmer
                                </label>
                                <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password"
                                    class="block w-full px-4 py-3.5 bg-[#F4F6FA] border border-slate-200/80 rounded-2xl text-sm font-bold text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all"
                                    placeholder="••••••••">
                                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                            </div>
                        </div>

                        <div class="pt-2">
                            <button type="submit"
                                class="w-full inline-flex items-center justify-center gap-2 py-4 px-6 rounded-full bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-black text-xs uppercase tracking-widest shadow-xl shadow-blue-200/70 transition transform hover:-translate-y-0.5 cursor-pointer">
                                <span>Réinitialiser le mot de passe</span>
                                <i class="fas fa-check text-[11px]"></i>
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
</x-guest-layout>
