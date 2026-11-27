<x-onboarding-layout>
    <x-slot name="header">
        {{-- Barre de progression --}}
        <div class="flex items-center gap-2 mb-5">
            <div class="step-dot done"><i class="fas fa-check text-[10px]"></i></div>
            <div class="step-line done"></div>
            <div class="step-dot done"><i class="fas fa-check text-[10px]"></i></div>
            <div class="step-line done"></div>
            <div class="step-dot done"><i class="fas fa-check text-[10px]"></i></div>
            <div class="step-line done"></div>
            <div class="step-dot pending">4</div>
        </div>
        <div>
            <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-50 text-blue-600 text-[10px] font-black uppercase tracking-widest mb-2">
                <span class="w-1.5 h-1.5 rounded-full bg-blue-600"></span>
                Étape 3 sur 4
            </span>
            <h2 class="text-2xl font-black text-slate-900 tracking-tight">Attribuez les responsabilités</h2>
            <p class="text-sm text-slate-500 font-medium mt-1">Complétez les informations de chaque responsable pour les modules sélectionnés.</p>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto">
        <form method="POST" action="{{ route('team-setup.review') }}" class="space-y-6">
            @csrf

            {{-- Règle importante --}}
            <div class="bg-amber-50/70 border border-amber-100 rounded-2xl p-5 flex items-start gap-4">
                <div class="w-9 h-9 rounded-xl bg-amber-100 flex items-center justify-center text-amber-600 shrink-0">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div>
                    <p class="text-sm font-black text-slate-800 mb-1">Règle importante</p>
                    <p class="text-xs text-slate-600">Une personne peut gérer plusieurs modules. Si vous saisissez le même email plusieurs fois, les modules seront automatiquement regroupés pour cette personne.</p>
                </div>
            </div>

            {{-- Cartes par module --}}
            <div class="space-y-5">
                @foreach($selectedModules as $module)
                    <div class="bg-white border-2 border-slate-200 rounded-2xl overflow-hidden hover:border-slate-300 hover:shadow-md transition-all">
                        {{-- En-tête du module --}}
                        <div class="flex items-center justify-between gap-4 px-6 py-4 bg-gradient-to-r from-slate-50 to-blue-50/30 border-b border-slate-100">
                            <div class="flex items-center gap-4">
                                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white shadow-sm">
                                    <i class="{{ $module->icon }} text-base"></i>
                                </div>
                                <div>
                                    <h3 class="text-sm font-black text-slate-900 uppercase tracking-tight">{{ $module->nom }}</h3>
                                    <p class="text-[10px] text-slate-500 font-medium">{{ $module->description }}</p>
                                </div>
                            </div>
                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-blue-50 text-blue-600 text-[9px] font-black uppercase tracking-widest shrink-0">
                                Module
                            </span>
                        </div>

                        {{-- Formulaire membre --}}
                        <div class="p-6">
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                                <div>
                                    <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1.5">Nom complet <span class="text-red-500">*</span></label>
                                    <input type="text" name="modules[{{ $module->id }}][nom_complet]" required
                                        placeholder="Jean Dupont"
                                        class="w-full" />
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1.5">Email <span class="text-red-500">*</span></label>
                                    <input type="email" name="modules[{{ $module->id }}][email]" required
                                        placeholder="jean@agence.com"
                                        class="w-full" />
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1.5">Téléphone</label>
                                    <input type="tel" name="modules[{{ $module->id }}][telephone]"
                                        placeholder="+225 07 ..."
                                        class="w-full" />
                                </div>
                                <div>
                                    <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1.5">Mot de passe <span class="text-red-500">*</span></label>
                                    <input type="password" name="modules[{{ $module->id }}][password]" required minlength="8"
                                        placeholder="Min. 8 caractères"
                                        class="w-full" />
                                </div>
                            </div>
                            <div>
                                <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1.5">Niveau d'accès <span class="text-red-500">*</span></label>
                                <select name="modules[{{ $module->id }}][access_level]" required class="w-full">
                                    <option value="manager">🔑 Gestionnaire — Accès complet (Créer, Modifier, Supprimer)</option>
                                    <option value="collaborator">✏️ Collaborateur — Créer et Modifier uniquement</option>
                                    <option value="viewer">👁 Consultant — Lecture seule</option>
                                </select>
                                <input type="hidden" name="modules[{{ $module->id }}][module_id]" value="{{ $module->id }}">
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            @if($errors->any())
                <div class="p-4 rounded-2xl bg-red-50 border border-red-200 flex items-start gap-3">
                    <i class="fas fa-exclamation-circle text-red-500 mt-0.5"></i>
                    <div>
                        <p class="text-sm font-black text-red-700 mb-1">Erreurs dans le formulaire</p>
                        <ul class="text-xs text-red-600 space-y-0.5">
                            @foreach($errors->all() as $error)
                                <li>• {{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            {{-- Navigation --}}
            <div class="flex items-center justify-between pt-2">
                <a href="{{ route('team-setup.select-modules') }}"
                    class="inline-flex items-center gap-2 px-6 py-3 rounded-2xl border-2 border-slate-200 bg-white text-sm font-black text-slate-600 uppercase tracking-widest hover:bg-slate-50 hover:border-slate-300 transition-all">
                    <i class="fas fa-arrow-left"></i> Retour
                </a>
                <button type="submit"
                    class="inline-flex items-center gap-3 px-8 py-3.5 rounded-2xl bg-gradient-to-r from-blue-600 to-indigo-600 text-white text-sm font-black uppercase tracking-widest shadow-lg shadow-blue-200 hover:from-blue-700 hover:to-indigo-700 hover:shadow-xl transition-all duration-200 hover:-translate-y-0.5">
                    Voir le récapitulatif <i class="fas fa-arrow-right"></i>
                </button>
            </div>
        </form>
    </div>
</x-onboarding-layout>
