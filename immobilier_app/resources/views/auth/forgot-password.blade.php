<x-guest-layout :fullWidth="true">
    <div class="min-h-screen bg-white flex">
        <!-- Left side: Image/Branding -->
        <div class="hidden lg:block relative w-0 flex-1">
            <img class="absolute inset-0 h-full w-full object-cover" src="https://images.unsplash.com/photo-1554224155-1696413565d3?ixlib=rb-4.0.3&auto=format&fit=crop&w=1170&q=80" alt="Récupération">
            <div class="absolute inset-0 bg-blue-600 mix-blend-multiply opacity-20"></div>
            <div class="absolute inset-0 flex items-center justify-center p-20 text-white">
                <div class="max-w-md">
                    <h2 class="text-5xl font-black uppercase tracking-tighter leading-none mb-6 text-white">Pas de panique.</h2>
                    <p class="text-xl font-medium text-blue-50 leading-relaxed">Entrez votre email et nous vous enverrons un lien pour réinitialiser votre accès en toute sécurité.</p>
                </div>
            </div>
        </div>

        <!-- Right side: Form -->
        <div class="flex-1 flex flex-col justify-center py-12 px-4 sm:px-6 lg:flex-none lg:px-20 xl:px-24 bg-white z-10">
            <div class="mx-auto w-full max-w-md lg:w-[480px]">
                <div class="mb-10 text-center">
                    <span class="text-3xl font-black text-slate-900 tracking-tighter uppercase">ONE.Immo</span>
                </div>
                <div>
                    <h2 class="mt-8 text-4xl font-black text-gray-900 uppercase tracking-tight leading-none">Oubli ?</h2>
                    <p class="mt-4 text-lg text-gray-500 font-medium italic">
                        {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link.') }}
                    </p>
                </div>

                <!-- Session Status -->
                <x-auth-session-status class="mt-6 mb-4" :status="session('status')" />

                <div class="mt-12">
                    <form method="POST" action="{{ route('password.email') }}" class="space-y-8">
                        @csrf

                        <!-- Email Address -->
                        <div>
                            <label for="email" class="block text-xs font-black text-gray-400 uppercase tracking-[0.2em] mb-3">
                                Votre Adresse Email
                            </label>
                            <div class="mt-1">
                                <input id="email" name="email" type="email" :value="old('email')" required autofocus class="appearance-none block w-full px-5 py-4 border border-gray-100 bg-gray-50 rounded-2xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm font-bold transition-all" placeholder="nom@exemple.com">
                            </div>
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <div class="flex flex-col space-y-4">
                            <button type="submit" class="w-full flex justify-center py-5 px-4 border border-transparent rounded-2xl shadow-xl shadow-blue-100 text-sm font-black text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 uppercase tracking-widest transition transform hover:-translate-y-1 active:scale-95">
                                {{ __('Email Password Reset Link') }}
                            </button>

                            <a href="{{ route('login') }}" class="text-center text-sm font-bold text-gray-400 hover:text-blue-600 transition uppercase tracking-widest">
                                <i class="fas fa-arrow-left mr-2"></i> Retour à la connexion
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>