<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-black text-2xl text-slate-900 uppercase tracking-tight">Modifier un Membre</h2>
                <p class="text-xs text-slate-500 font-bold uppercase tracking-widest mt-1">{{ $teamMember->nom_complet }}</p>
            </div>
            <a href="{{ route('team.index') }}" class="px-4 py-2 bg-white border-2 border-slate-200 text-slate-700 font-bold rounded-lg hover:bg-slate-50 transition-colors">
                <i class="fas fa-arrow-left mr-2"></i>
                Retour
            </a>
        </div>
    </x-slot>

    <div class="max-w-3xl mx-auto">
        <div class="premium-card p-6 mb-6 {{ $teamMember->status === 'suspended' ? 'bg-red-50/70 border-red-200' : 'bg-white' }} rounded-2xl border border-slate-200">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Statut du compte</p>
                    <div class="flex items-center gap-2">
                        @if($teamMember->status === 'active')
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-emerald-100 text-emerald-800 text-xs font-black rounded-full">
                                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                Actif
                            </span>
                        @elseif($teamMember->status === 'suspended')
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-red-100 text-red-800 text-xs font-black rounded-full">
                                <span class="w-2 h-2 rounded-full bg-red-500"></span>
                                Suspendu
                            </span>
                        @endif
                    </div>
                </div>
                <div class="flex items-center flex-wrap gap-2">
                    <a href="{{ route('team.reset-password', $teamMember) }}" class="px-3 py-2 bg-indigo-50 hover:bg-indigo-100 border border-indigo-200 text-indigo-700 font-bold text-xs rounded-xl transition-colors flex items-center gap-1.5">
                        <i class="fas fa-key"></i>
                        <span>Mot de passe</span>
                    </a>

                    <a href="{{ route('team.history', $teamMember) }}" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 border border-slate-200 text-slate-700 font-bold text-xs rounded-xl transition-colors flex items-center gap-1.5">
                        <i class="fas fa-history"></i>
                        <span>Historique</span>
                    </a>

                    @if($teamMember->status === 'active')
                        <form method="POST" action="{{ route('team.suspend', $teamMember) }}" class="inline">
                            @csrf
                            <button type="submit" class="px-3 py-2 bg-amber-500 hover:bg-amber-600 text-white font-bold text-xs rounded-xl transition-colors flex items-center gap-1.5" onclick="return confirm('Suspendre temporairement ce membre ?')">
                                <i class="fas fa-pause-circle"></i>
                                <span>Suspendre</span>
                            </button>
                        </form>
                    @elseif($teamMember->status === 'suspended')
                        <form method="POST" action="{{ route('team.reactivate', $teamMember) }}" class="inline">
                            @csrf
                            <button type="submit" class="px-3 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl transition-colors flex items-center gap-1.5">
                                <i class="fas fa-play-circle"></i>
                                <span>Réactiver</span>
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('team.update', $teamMember) }}">
            @csrf
            @method('PATCH')

            <!-- Informations du membre -->
            <div class="premium-card p-6 mb-6">
                <h3 class="font-black text-lg text-slate-900 mb-6 uppercase">Informations du Membre</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label class="block text-xs font-black text-slate-700 uppercase mb-2">Nom complet *</label>
                        <input type="text" name="nom_complet" value="{{ old('nom_complet', $teamMember->nom_complet) }}" required class="w-full px-4 py-3 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('nom_complet') border-red-500 @enderror">
                        @error('nom_complet')
                            <p class="text-xs text-red-600 font-bold mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-black text-slate-700 uppercase mb-2">Email *</label>
                        <input type="email" name="email" value="{{ old('email', $teamMember->email) }}" required class="w-full px-4 py-3 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('email') border-red-500 @enderror">
                        @error('email')
                            <p class="text-xs text-red-600 font-bold mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-black text-slate-700 uppercase mb-2">Téléphone</label>
                        <input type="tel" name="telephone" value="{{ old('telephone', $teamMember->telephone) }}" class="w-full px-4 py-3 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('telephone') border-red-500 @enderror">
                        @error('telephone')
                            <p class="text-xs text-red-600 font-bold mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-black text-slate-700 uppercase mb-2">Nouveau mot de passe</label>
                        <input type="password" name="password" autocomplete="new-password" class="w-full px-4 py-3 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error('password') border-red-500 @enderror" placeholder="Laissez vide pour conserver l'actuel">
                        @error('password')
                            <p class="text-xs text-red-600 font-bold mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-black text-slate-700 uppercase mb-2">Confirmer le mot de passe</label>
                        <input type="password" name="password_confirmation" autocomplete="new-password" class="w-full px-4 py-3 border border-slate-200 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>
            </div>

            <!-- Modules et accès -->
            <div class="premium-card p-6 mb-6">
                <h3 class="font-black text-lg text-slate-900 mb-6 uppercase">Modules Assignés</h3>
                
                <div class="space-y-4">
                    @foreach($modules as $module)
                        @php
                            $currentAssignment = $currentAssignments->where('module_id', $module->id)->first();
                            $isChecked = $currentAssignment !== null;
                            $currentLevel = $currentAssignment?->access_level ?? 'viewer';
                        @endphp
                        <div class="flex items-start gap-4 p-4 bg-slate-50 rounded-lg border-2 border-slate-200 hover:border-blue-300 transition-colors">
                            <input type="checkbox" name="modules[]" value="{{ $module->id }}" id="module-{{ $module->id }}" class="w-5 h-5 text-blue-600 rounded focus:ring-2 focus:ring-blue-500 mt-1" {{ $isChecked ? 'checked' : '' }}>
                            
                            <div class="flex-1">
                                <label for="module-{{ $module->id }}" class="cursor-pointer">
                                    <div class="flex items-center gap-3 mb-2">
                                        <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-blue-100 to-indigo-100 flex items-center justify-center">
                                            <i class="{{ $module->icon }} text-blue-600"></i>
                                        </div>
                                        <div>
                                            <p class="font-black text-slate-900">{{ $module->nom }}</p>
                                            @if($module->is_required)
                                                <span class="inline-block px-2 py-0.5 bg-red-100 text-red-600 text-[9px] font-black rounded-full uppercase">Obligatoire</span>
                                            @endif
                                        </div>
                                    </div>
                                    <p class="text-xs text-slate-600 mb-3">{{ $module->description }}</p>
                                </label>
                                
                                <select name="access_levels[{{ $module->id }}]" class="px-3 py-2 border border-slate-200 rounded-lg text-sm font-bold focus:ring-2 focus:ring-blue-500">
                                    <option value="manager" {{ old('access_levels.'.$module->id, $currentLevel) === 'manager' ? 'selected' : '' }}>★★★ Gestionnaire (CRUD complet)</option>
                                    <option value="collaborator" {{ old('access_levels.'.$module->id, $currentLevel) === 'collaborator' ? 'selected' : '' }}>★★ Collaborateur (Créer/Modifier)</option>
                                    <option value="viewer" {{ old('access_levels.'.$module->id, $currentLevel) === 'viewer' ? 'selected' : '' }}>★ Consultant (Lecture seule)</option>
                                </select>
                            </div>
                        </div>
                    @endforeach
                </div>

                @error('modules')
                    <p class="text-sm text-red-600 font-bold mt-4">{{ $message }}</p>
                @enderror
            </div>

            <!-- Info Box -->
            <div class="mb-6 p-4 bg-amber-50 border border-amber-200 rounded-lg">
                <div class="flex items-start gap-3">
                    <i class="fas fa-exclamation-triangle text-amber-600 text-lg mt-0.5"></i>
                    <div>
                        <p class="text-sm text-amber-800 font-bold">⚠️ Notification automatique</p>
                        <p class="text-xs text-amber-700 mt-1">Le membre sera notifié par email de toute modification de ses accès.</p>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-between">
                <a href="{{ route('team.index') }}" class="px-6 py-3 bg-white border-2 border-slate-200 text-slate-700 font-bold rounded-lg hover:bg-slate-50 transition-colors">
                    Annuler
                </a>
                <button type="submit" class="px-8 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-black rounded-lg shadow-lg hover:shadow-xl hover:scale-105 transition-all duration-200">
                    <i class="fas fa-save mr-2"></i>
                    Enregistrer les modifications
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
