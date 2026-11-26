<x-guest-layout :fullWidth="true">
    <div class="min-h-screen bg-[#F4F6FA] flex flex-col justify-center py-8 px-4 sm:px-6 lg:px-8 relative overflow-hidden">
        <!-- Décor latéral discret -->
        <div class="pointer-events-none fixed inset-x-0 top-24 -z-10 flex justify-between opacity-25">
            <div class="w-40 h-80 bg-gradient-to-r from-blue-400/40 to-transparent rounded-r-full blur-2xl"></div>
            <div class="w-40 h-80 bg-gradient-to-l from-indigo-400/40 to-transparent rounded-l-full blur-2xl"></div>
        </div>

        <div class="max-w-6xl w-full mx-auto">
            <div class="bg-white rounded-[2.5rem] border border-slate-100 shadow-xl shadow-slate-200/50 overflow-hidden grid lg:grid-cols-12">

                <!-- Colonne gauche : Formulaire -->
                <div class="lg:col-span-6 p-7 sm:p-10 lg:p-12 flex flex-col justify-between">
                    <div>
                        <!-- Top bar -->
                        <div class="flex items-center justify-between gap-4 mb-8">
                            <a href="{{ url('/') }}" class="inline-block transition-transform hover:scale-105">
                                <img src="{{ asset('images/logo.svg') }}" alt="Habitat Immobilier" class="h-10 w-auto">
                            </a>
                            <a href="{{ url('/') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-[#F4F6FA] border border-slate-200/80 text-xs font-bold text-slate-600 hover:text-blue-600 hover:border-blue-200 transition group">
                                <i class="fas fa-arrow-left text-[10px] text-slate-400 group-hover:text-blue-600 transition-transform group-hover:-translate-x-0.5"></i>
                                <span>Accueil</span>
                            </a>
                        </div>

                        <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-50 text-blue-600 text-[10px] font-black uppercase tracking-widest mb-3">
                            <span class="w-1.5 h-1.5 rounded-full bg-blue-600"></span>
                            Espace Professionnel
                        </span>
                        <h1 class="text-3xl sm:text-4xl font-black text-slate-900 tracking-tight leading-tight">
                            Connexion
                        </h1>
                        <p class="mt-2 text-sm text-slate-500 font-medium">
                            Heureux de vous revoir ! Accédez à votre espace de gestion immobilière.
                        </p>
                    </div>

                    <div class="mt-8">
                        @if (session('status'))
                            <div class="mb-5 p-4 rounded-2xl bg-blue-50 border border-blue-100 text-blue-700 text-xs font-bold flex items-center gap-3">
                                <i class="fas fa-info-circle text-blue-500 text-base shrink-0"></i>
                                <span>{{ session('status') }}</span>
                            </div>
                        @endif

                        @if (session('error'))
                            <div class="mb-5 p-4 rounded-2xl bg-red-50 border border-red-100 text-red-700 text-xs font-bold flex items-center gap-3">
                                <i class="fas fa-exclamation-circle text-red-500 text-base shrink-0"></i>
                                <span>{{ session('error') }}</span>
                            </div>
                        @endif

                        <form id="login-form" method="POST" action="{{ route('login') }}" class="space-y-5">
                            @csrf

                            <div>
                                <label for="email" class="block text-[11px] font-black text-slate-500 uppercase tracking-widest mb-2">
                                    Adresse Email Professionnelle
                                </label>
                                <input id="email" name="email" type="email" autocomplete="email" required
                                    class="block w-full px-4 py-3.5 bg-[#F4F6FA] border border-slate-200/80 rounded-2xl text-sm font-bold text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all"
                                    value="{{ old('email') }}" placeholder="nom@agence.com">
                                <x-input-error :messages="$errors->get('email')" class="mt-2" />
                            </div>

                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <label for="password" class="block text-[11px] font-black text-slate-500 uppercase tracking-widest">
                                        Mot de passe
                                    </label>
                                    @if (Route::has('password.request'))
                                        <a href="{{ route('password.request') }}" class="text-[11px] font-black text-blue-600 hover:text-blue-700 uppercase tracking-wider">
                                            Oublié ?
                                        </a>
                                    @endif
                                </div>
                                <input id="password" name="password" type="password" autocomplete="current-password" required
                                    class="block w-full px-4 py-3.5 bg-[#F4F6FA] border border-slate-200/80 rounded-2xl text-sm font-bold text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:bg-white transition-all"
                                    placeholder="••••••••">
                                <x-input-error :messages="$errors->get('password')" class="mt-2" />
                            </div>

                            <div class="flex items-center pt-1">
                                <input id="remember_me" name="remember" type="checkbox"
                                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-slate-300 rounded cursor-pointer">
                                <label for="remember_me" class="ml-2.5 block text-xs font-bold text-slate-600 cursor-pointer">
                                    Rester connecté pour 30 jours
                                </label>
                            </div>

                            <div class="pt-2">
                                <button type="submit"
                                    class="w-full inline-flex items-center justify-center gap-2.5 py-4 px-6 rounded-full bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-black text-xs uppercase tracking-widest shadow-xl shadow-blue-200/70 transition transform hover:-translate-y-0.5 active:scale-95 cursor-pointer">
                                    <span>Se Connecter à l'Espace</span>
                                    <i class="fas fa-arrow-right text-[11px]"></i>
                                </button>
                            </div>
                        </form>
                    </div>

                    <div class="mt-8 pt-6 border-t border-slate-100 text-center">
                        <p class="text-xs text-slate-500 font-bold">
                            Nouvelle agence ?
                            <a href="{{ route('register.agency') }}" class="text-blue-600 font-black hover:text-blue-700 ml-1 underline underline-offset-4 decoration-2">
                                Inscrire mon agence gratuitement
                            </a>
                        </p>
                    </div>
                </div>

                <!-- Colonne droite : Panneau bleu/indigo harmonisé avec la page d'accueil -->
                <div class="hidden lg:block lg:col-span-6 p-4">
                    <div class="relative h-full min-h-[540px] rounded-[2rem] bg-gradient-to-br from-blue-600 via-blue-500 to-indigo-600 p-10 text-white overflow-hidden flex flex-col justify-between">
                        <!-- Bulles décoratives -->
                        <div class="pointer-events-none absolute -top-14 -right-14 w-56 h-56 rounded-full bg-white/10"></div>
                        <div class="pointer-events-none absolute bottom-16 -left-16 w-48 h-48 rounded-full bg-white/10"></div>

                        <div class="relative z-10">
                            <span class="inline-block px-3 py-1 rounded-full bg-white/15 text-[10px] font-black uppercase tracking-widest mb-4">
                                Plateforme Multi-Agences
                            </span>
                            <h2 class="text-3xl xl:text-4xl font-black leading-tight tracking-tight mb-3">
                                La gestion immobilière, réinventée.
                            </h2>
                            <p class="text-sm text-blue-50/90 leading-relaxed font-medium max-w-md">
                                Automatisez vos loyers, sécurisez vos cautions et offrez un suivi transparent à vos propriétaires et locataires.
                            </p>
                        </div>

                        <div class="relative z-10 my-6">
                            <div class="rounded-2xl overflow-hidden border-2 border-white/25 shadow-2xl">
                                <img src="https://images.unsplash.com/photo-1560518883-ce09059eeffa?ixlib=rb-4.0.3&auto=format&fit=crop&w=1073&q=80"
                                     alt="Immobilier Moderne"
                                     class="w-full h-56 object-cover">
                            </div>
                        </div>

                        <div class="relative z-10 grid grid-cols-3 gap-3">
                            <div class="bg-white/15 backdrop-blur-md border border-white/20 rounded-2xl p-3 text-center">
                                <p class="text-lg font-black">100%</p>
                                <p class="text-[10px] text-blue-50 font-bold uppercase tracking-wider">Sécurisé</p>
                            </div>
                            <div class="bg-white/15 backdrop-blur-md border border-white/20 rounded-2xl p-3 text-center">
                                <p class="text-lg font-black">24/7</p>
                                <p class="text-[10px] text-blue-50 font-bold uppercase tracking-wider">Accessible</p>
                            </div>
                            <div class="bg-white/15 backdrop-blur-md border border-white/20 rounded-2xl p-3 text-center">
                                <p class="text-lg font-black">50+</p>
                                <p class="text-[10px] text-blue-50 font-bold uppercase tracking-wider">Agences</p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <script>
        (function () {
            const refreshCsrfToken = () => {
                fetch('{{ route('csrf.refresh') }}', {
                    method: 'GET',
                    credentials: 'same-origin',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(res => res.ok ? res.json() : null)
                .then(data => {
                    if (data && data.token) {
                        const meta = document.querySelector('meta[name="csrf-token"]');
                        if (meta) meta.setAttribute('content', data.token);
                        document.querySelectorAll('input[name="_token"]').forEach(input => {
                            input.value = data.token;
                        });
                    }
                })
                .catch(() => {});
            };

            document.addEventListener('visibilitychange', () => {
                if (!document.hidden) refreshCsrfToken();
            });

            setInterval(refreshCsrfToken, 15 * 60 * 1000);
            document.getElementById('email')?.addEventListener('focus', refreshCsrfToken, { once: false });
        })();
    </script>
</x-guest-layout>
