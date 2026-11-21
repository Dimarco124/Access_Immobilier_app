<x-guest-layout :fullWidth="true">
    <div class="min-h-screen bg-white flex">
        <!-- Left side: Image/Branding -->
        <div class="hidden lg:block relative w-0 flex-1">
            <img class="absolute inset-0 h-full w-full object-cover" src="https://images.unsplash.com/photo-1554224155-1696413565d3?ixlib=rb-4.0.3&auto=format&fit=crop&w=1170&q=80" alt="Sécurité">
            <div class="absolute inset-0 bg-blue-600 mix-blend-multiply opacity-20"></div>
            <div class="absolute inset-0 flex items-center justify-center p-20 text-white">
                <div class="max-w-md">
                    <h2 class="text-5xl font-black uppercase tracking-tighter leading-none mb-6 text-white">Sécurisez votre accès.</h2>
                    <p class="text-xl font-medium text-blue-50 leading-relaxed">Choisissez un nouveau mot de passe robuste pour protéger vos données immobilières.</p>
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
                    <h2 class="mt-8 text-4xl font-black text-gray-900 uppercase tracking-tight leading-none">Nouveau mot de passe</h2>
                    <p class="mt-4 text-lg text-gray-500 font-medium">
                        Réinitialisez votre accès en quelques secondes.
                    </p>
                </div>

                <div class="mt-12">
                    <form method="POST" action="{{ route('password.store') }}" class="space-y-8">
                        @csrf

                        <!-- Password Reset Token -->
                        <input type="hidden" name="token" value="{{ $request->route('token') }}">

                        <!-- Email Address -->
                        <div>
                            <label for="email" class="block text-xs font-black text-gray-400 uppercase tracking-[0.2em] mb-3">
                                Email
                            </label>
                            <div class="mt-1">
                                <input id="email" name="email" type="email" :value="old('email', $request->email)" required autofocus autocomplete="username" class="appearance-none block w-full px-5 py-4 border border-gray-100 bg-gray-50 rounded-2xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm font-bold transition-all">
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

                        <div>
                            <button type="submit" class="w-full flex justify-center py-5 px-4 border border-transparent rounded-2xl shadow-xl shadow-blue-100 text-sm font-black text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 uppercase tracking-widest transition transform hover:-translate-y-1 active:scale-95">
                                {{ __('Reset Password') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>