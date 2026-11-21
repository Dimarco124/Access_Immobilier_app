<x-guest-layout :fullWidth="true">
    <div class="min-h-screen bg-gray-50 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <div class="flex justify-center">
                <span class="text-5xl font-black text-slate-900 tracking-tighter uppercase">ONE.Immo</span>
            </div>
            <h2 class="mt-6 text-center text-4xl font-black text-gray-900 uppercase tracking-tight">
                Bienvenue !
            </h2>
            <p class="mt-2 text-center text-lg text-gray-600 font-medium">
                Activez votre compte pour rejoindre l'équipe
            </p>
        </div>

        <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-md">
            <!-- Info Box -->
            <div class="bg-white py-8 px-8 shadow-2xl shadow-blue-100 sm:rounded-3xl border border-gray-100 mb-6">
                <div class="text-center mb-6">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-2xl shadow-xl shadow-emerald-200 mb-4">
                        <i class="fas fa-check-circle text-white text-3xl"></i>
                    </div>
                    <p class="text-sm text-gray-600 mb-2 font-medium">Vous avez été invité(e) à rejoindre :</p>
                    <h3 class="text-2xl font-black text-gray-900 uppercase tracking-tight">{{ $teamMember->agence->nom }}</h3>
                </div>
                
                <div class="border-t-2 border-gray-100 pt-6 mb-6">
                    <p class="font-black text-xs uppercase text-gray-500 mb-3 tracking-wider">
                        <i class="fas fa-user mr-1"></i> Vos informations
                    </p>
                    <div class="space-y-3">
                        <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl">
                            <i class="fas fa-user-circle text-blue-600 text-xl"></i>
                            <span class="text-sm font-bold text-gray-700">{{ $teamMember->nom_complet }}</span>
                        </div>
                        <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl">
                            <i class="fas fa-envelope text-blue-600 text-xl"></i>
                            <span class="text-sm text-gray-600 font-medium">{{ $teamMember->email }}</span>
                        </div>
                    </div>
                </div>

                <div class="border-t-2 border-gray-100 pt-6">
                    <p class="font-black text-xs uppercase text-gray-500 mb-3 tracking-wider">
                        <i class="fas fa-tasks mr-1"></i> Modules assignés
                    </p>
                    <div class="flex flex-wrap gap-2">
                        @foreach($teamMember->modules as $module)
                            <span class="px-3 py-2 bg-blue-50 text-blue-700 text-xs font-bold rounded-lg border border-blue-100">
                                <i class="{{ $module->icon }} mr-1"></i>{{ $module->nom }}
                            </span>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Form -->
            <div class="bg-white py-8 px-8 shadow-2xl shadow-blue-100 sm:rounded-3xl border border-gray-100">
                <p class="text-sm font-bold text-gray-700 mb-6 uppercase tracking-tight">
                    Créez votre mot de passe pour activer votre compte :
                </p>

                <form method="POST" action="{{ route('team-setup.activate-account', $token) }}" class="space-y-6">
                    @csrf

                    <div>
                        <label class="font-black text-xs uppercase text-gray-500 mb-2 block">
                            Mot de passe <span class="text-red-500">*</span>
                        </label>
                        <input type="password" name="password" required class="block w-full border-gray-200 focus:ring-blue-500 focus:border-blue-500 rounded-xl @error('password') border-red-500 @enderror">
                        @error('password')
                            <p class="text-xs text-red-600 font-bold mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="font-black text-xs uppercase text-gray-500 mb-2 block">
                            Confirmer le mot de passe <span class="text-red-500">*</span>
                        </label>
                        <input type="password" name="password_confirmation" required class="block w-full border-gray-200 focus:ring-blue-500 focus:border-blue-500 rounded-xl">
                    </div>

                    <button type="submit" class="w-full bg-green-600 text-white px-10 py-4 rounded-xl font-black uppercase tracking-widest text-sm hover:bg-green-700 shadow-xl shadow-green-200 transition transform hover:-translate-y-1">
                        <i class="fas fa-check-circle mr-2"></i>Activer mon compte
                    </button>
                </form>
            </div>

            <!-- Security Note -->
            <div class="mt-6 p-4 bg-gray-100 rounded-2xl border border-gray-200">
                <div class="flex items-start gap-3">
                    <i class="fas fa-shield-alt text-gray-500 text-lg"></i>
                    <p class="text-xs text-gray-600 font-medium">
                        Votre mot de passe doit contenir au moins 8 caractères pour garantir la sécurité de votre compte.
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
