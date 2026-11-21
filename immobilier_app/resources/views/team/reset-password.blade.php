<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-black text-2xl text-slate-900 tracking-tight uppercase">Réinitialiser le mot de passe</h2>
                <p class="text-[11px] text-slate-500 font-bold uppercase tracking-widest mt-1">Définir un nouveau mot de passe pour ce membre</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('team.edit', $teamMember) }}" class="px-4 py-2 bg-blue-50 border border-blue-200 text-blue-700 font-bold text-xs rounded-xl hover:bg-blue-100 transition-colors flex items-center gap-1.5">
                    <i class="fas fa-edit"></i>
                    Modifier
                </a>
                <a href="{{ route('team.index') }}" class="px-4 py-2 bg-white border border-slate-200 text-slate-700 font-bold text-xs rounded-xl hover:bg-slate-50 transition-colors flex items-center gap-1.5">
                    <i class="fas fa-arrow-left"></i>
                    Retour
                </a>
            </div>
        </div>
    </x-slot>

    <div class="max-w-xl mx-auto">
        <div class="premium-card p-8 bg-white border border-slate-200 rounded-2xl shadow-sm">
            <form method="POST" action="{{ route('team.reset-password.update', $teamMember) }}">
                @csrf

                <div class="mb-6 p-4 rounded-xl bg-slate-50 border border-slate-200 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 text-white flex items-center justify-center font-black text-sm">
                        {{ strtoupper(substr($teamMember->nom_complet, 0, 1)) }}
                    </div>
                    <div>
                        <p class="text-xs font-black uppercase text-slate-400 tracking-wider">Collaborateur</p>
                        <p class="text-sm font-black text-slate-900">{{ $teamMember->nom_complet }} <span class="text-xs text-slate-500 font-normal">({{ $teamMember->email }})</span></p>
                    </div>
                </div>

                <div class="mb-5">
                    <label for="password" class="block text-xs font-black uppercase text-slate-700 mb-2">Nouveau mot de passe *</label>
                    <input id="password" name="password" type="password" required minlength="8"
                        class="block w-full px-4 py-3 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('password') border-red-500 @enderror"
                        placeholder="Au moins 8 caractères" />
                    @error('password')
                        <p class="text-xs text-red-600 font-bold mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label for="password_confirmation" class="block text-xs font-black uppercase text-slate-700 mb-2">Confirmer le nouveau mot de passe *</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" required minlength="8"
                        class="block w-full px-4 py-3 rounded-xl border border-slate-200 text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Répétez le mot de passe" />
                </div>

                <div class="flex justify-between items-center pt-2">
                    <a href="{{ route('team.index') }}"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl border border-slate-200 bg-white text-xs font-bold text-slate-700 hover:bg-slate-50 transition-colors">
                        <i class="fas fa-times"></i>
                        Annuler
                    </a>
                    <button type="submit"
                        class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 text-white text-xs font-black uppercase tracking-wider shadow-md hover:shadow-lg transition-all">
                        <i class="fas fa-check-circle"></i>
                        Réinitialiser
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>