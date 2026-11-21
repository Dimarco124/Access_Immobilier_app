<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-black text-2xl text-slate-900 uppercase tracking-tight">Ajouter un Membre</h2>
                <p class="text-xs text-slate-500 font-bold uppercase tracking-widest mt-1">Inviter un nouveau
                    collaborateur</p>
            </div>
            <a href="{{ route('team.index') }}"
                class="px-4 py-2 bg-white border-2 border-slate-200 text-slate-700 font-bold rounded-lg hover:bg-slate-50 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>
                Retour
            </a>
        </div>
    </x-slot>

    <div class="max-w-3xl mx-auto">
        <form method="POST" action="{{ route('team.store') }}">
            @csrf

            <!-- Informations du membre -->
            <div class="premium-card p-6 mb-6">
                <h3 class="font-black text-lg text-slate-900 mb-6 uppercase">Informations du Membre</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label class="block text-xs font-black text-slate-700 uppercase mb-2">Nom complet *</label>
                        <input type="text" name="nom_complet" value="{{ old('nom_complet') }}" required
                            class="w-full px-4 py-3 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('nom_complet') border-red-500 @enderror">
                        @error('nom_complet')
                            <p class="text-xs text-red-600 font-bold mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-black text-slate-700 uppercase mb-2">Email *</label>
                        <input type="email" name="email" value="{{ old('email') }}" required
                            class="w-full px-4 py-3 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('email') border-red-500 @enderror">
                        @error('email')
                            <p class="text-xs text-red-600 font-bold mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-black text-slate-700 uppercase mb-2">Téléphone</label>
                        <input type="tel" name="telephone" value="{{ old('telephone') }}"
                            class="w-full px-4 py-3 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('telephone') border-red-500 @enderror">
                        @error('telephone')
                            <p class="text-xs text-red-600 font-bold mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-black text-slate-700 uppercase mb-2">Mot de passe *</label>
                        <input type="password" name="password" required
                            class="w-full px-4 py-3 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('password') border-red-500 @enderror">
                        @error('password')
                            <p class="text-xs text-red-600 font-bold mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-black text-slate-700 uppercase mb-2">Confirmer le mot de passe *</label>
                        <input type="password" name="password_confirmation" required
                            class="w-full px-4 py-3 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>
            </div>

            <!-- Modules et accès -->
            <div class="premium-card p-6 mb-6">
                <h3 class="font-black text-lg text-slate-900 mb-6 uppercase">Modules Assignés</h3>

                <div class="space-y-4">
                    @foreach($modules as $module)
                        <div
                            class="flex items-start gap-4 p-4 bg-slate-50 rounded-lg border-2 border-slate-200 hover:border-blue-300 transition-colors">
                            <input type="checkbox" name="modules[]" value="{{ $module->id }}" id="module-{{ $module->id }}"
                                class="w-5 h-5 text-blue-600 rounded focus:ring-2 focus:ring-blue-500 mt-1" {{ old('modules') && in_array($module->id, old('modules')) ? 'checked' : '' }}>

                            <div class="flex-1">
                                <label for="module-{{ $module->id }}" class="cursor-pointer">
                                    <div class="flex items-center gap-3 mb-2">
                                        <div
                                            class="w-10 h-10 rounded-lg bg-gradient-to-br from-blue-100 to-indigo-100 flex items-center justify-center">
                                            <i class="{{ $module->icon }} text-blue-600"></i>
                                        </div>
                                        <div>
                                            <p class="font-black text-slate-900">{{ $module->nom }}</p>
                                            @if($module->is_required)
                                                <span
                                                    class="inline-block px-2 py-0.5 bg-red-100 text-red-600 text-[9px] font-black rounded-full uppercase">Obligatoire</span>
                                            @endif
                                        </div>
                                    </div>
                                    <p class="text-xs text-slate-600 mb-3">{{ $module->description }}</p>
                                </label>

                                <select name="access_levels[{{ $module->id }}]"
                                    class="px-3 py-2 border border-slate-200 rounded-lg text-sm font-bold focus:ring-2 focus:ring-blue-500">
                                    <option value="manager" {{ old('access_levels.' . $module->id, 'viewer') === 'manager' ? 'selected' : '' }}>★★★ Gestionnaire (CRUD complet)</option>
                                    <option value="collaborator" {{ old('access_levels.' . $module->id, 'viewer') === 'collaborator' ? 'selected' : '' }}>★★ Collaborateur (Créer/Modifier)
                                    </option>
                                    <option value="viewer" {{ old('access_levels.' . $module->id, 'viewer') === 'viewer' ? 'selected' : '' }}>★ Consultant (Lecture seule)</option>
                                </select>
                            </div>
                        </div>
                    @endforeach
                </div>

                @error('modules')
                    <p class="text-sm text-red-600 font-bold mt-4">{{ $message }}</p>
                @enderror
            </div>



            <!-- Actions -->
            <div class="flex items-center justify-between">
                <a href="{{ route('team.index') }}"
                    class="px-6 py-3 bg-white border-2 border-slate-200 text-slate-700 font-bold rounded-lg hover:bg-slate-50 transition-colors">
                    Annuler
                </a>
                <button type="submit"
                    class="px-8 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-black rounded-lg shadow-lg hover:shadow-xl hover:scale-105 transition-all duration-200">
                    <i class="fas fa-check-circle mr-2"></i>
                    Créer le compte
                </button>
            </div>
        </form>
    </div>
</x-app-layout>