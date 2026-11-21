<x-onboarding-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 w-full">
            <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                <div>
                    <h2 class="font-black text-xl md:text-2xl text-slate-900 tracking-tight uppercase">Attribuez les
                        responsabilités</h2>
                    <p class="text-[10px] text-slate-500 font-bold uppercase tracking-widest mt-1">Complétez les
                        informations de chaque responsable pour les modules sélectionnés.</p>
                </div>
                <div
                    class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-slate-100 text-slate-600 text-[10px] font-black uppercase tracking-widest">
                    Étape 3/4</div>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto space-y-12">
            <form method="POST" action="{{ route('team-setup.review') }}">
                @csrf

                <div class="premium-card p-6 bg-slate-50 border border-slate-200 mt-6">
                    <div class="flex items-start gap-4">
                        <i class="fas fa-info-circle text-slate-500 text-xl mt-1"></i>
                        <div>
                            <p class="text-sm text-slate-900 font-bold mb-1">Règle importante</p>
                            <p class="text-xs text-slate-600">Une personne peut gérer plusieurs modules. Si vous
                                saisissez le même email plusieurs fois, les modules seront automatiquement regroupés
                                pour cette personne.</p>
                        </div>
                    </div>
                </div>

                <div class="space-y-8">
                    @foreach($selectedModules as $module)
                        <div class="premium-card border border-slate-200 p-6 mb-6">
                            <div class="flex flex-col md:flex-row gap-6 md:items-center md:justify-between mb-4">
                                <div class="flex items-center gap-4">
                                    <div
                                        class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-100 to-indigo-100 flex items-center justify-center text-blue-600">
                                        <i class="{{ $module->icon }} text-xl"></i>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-black text-slate-900 uppercase tracking-tight mb-1">
                                            {{ $module->nom }}
                                        </h3>
                                        <p class="text-sm text-slate-600">{{ $module->description }}</p>
                                    </div>
                                </div>
                                <span
                                    class="inline-flex items-center gap-2 rounded-full bg-slate-100 px-3 py-1 text-[10px] font-black uppercase tracking-widest text-slate-500">Module</span>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                <div>
                                    <label class="block text-xs font-black text-slate-700 uppercase mb-2">Nom complet <span
                                            class="text-red-500">*</span></label>
                                    <input type="text" name="modules[{{ $module->id }}][nom_complet]" required
                                        class="w-full rounded-lg border border-slate-200 px-4 py-2 text-sm text-slate-700 focus:border-blue-500 focus:ring-2 focus:ring-blue-100" />
                                </div>
                                <div>
                                    <label class="block text-xs font-black text-slate-700 uppercase mb-2">Email <span
                                            class="text-red-500">*</span></label>
                                    <input type="email" name="modules[{{ $module->id }}][email]" required
                                        class="w-full rounded-lg border border-slate-200 px-4 py-2 text-sm text-slate-700 focus:border-blue-500 focus:ring-2 focus:ring-blue-100" />
                                </div>
                                <div>
                                    <label class="block text-xs font-black text-slate-700 uppercase mb-2">Téléphone</label>
                                    <input type="tel" name="modules[{{ $module->id }}][telephone]"
                                        class="w-full rounded-lg border border-slate-200 px-4 py-2 text-sm text-slate-700 focus:border-blue-500 focus:ring-2 focus:ring-blue-100" />
                                </div>
                                <div>
                                    <label class="block text-xs font-black text-slate-700 uppercase mb-2">Mot de passe <span
                                            class="text-red-500">*</span></label>
                                    <input type="password" name="modules[{{ $module->id }}][password]" required
                                        minlength="8"
                                        class="w-full rounded-lg border border-slate-200 px-4 py-2 text-sm text-slate-700 focus:border-blue-500 focus:ring-2 focus:ring-blue-100" />
                                    <p class="text-xs text-slate-500 mt-1">Minimum 8 caractères.</p>
                                </div>
                            </div>

                            <div class="mt-4">
                                <label class="block text-xs font-black text-slate-700 uppercase mb-2">Niveau d'accès <span
                                        class="text-red-500">*</span></label>
                                <select name="modules[{{ $module->id }}][access_level]" required
                                    class="w-full rounded-lg border border-slate-200 px-4 py-2 text-sm text-slate-700 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 font-bold">
                                    <option value="manager">Gestionnaire - Accès complet (Créer, Modifier, Supprimer)
                                    </option>
                                    <option value="collaborator">Collaborateur - Créer et Modifier uniquement</option>
                                    <option value="viewer">Consultant - Lecture seule</option>
                                </select>
                                <input type="hidden" name="modules[{{ $module->id }}][module_id]" value="{{ $module->id }}">
                            </div>
                        </div>
                    @endforeach
                </div>

                @if($errors->any())
                    <div class="premium-card p-4 bg-red-50 border-2 border-red-200">
                        <div class="flex items-start gap-3">
                            <i class="fas fa-exclamation-circle text-red-600"></i>
                            <div>
                                <p class="text-sm text-red-700 font-bold mb-1">Erreurs dans le formulaire</p>
                                <ul class="text-xs text-red-600 space-y-1">
                                    @foreach ($errors->all() as $error)
                                        <li>• {{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="flex flex-col gap-4 sm:flex-row sm:justify-between sm:items-center mt-8">
                    <a href="{{ route('team-setup.select-modules') }}"
                        class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-6 py-3 text-sm font-black text-slate-700 uppercase tracking-widest hover:bg-slate-50 transition">
                        <i class="fas fa-arrow-left"></i>Retour
                    </a>
                    <button type="submit"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-6 py-3 text-sm font-black text-white uppercase tracking-widest shadow-lg shadow-blue-200 hover:bg-blue-700 transition">
                        Voir le récapitulatif
                        <i class="fas fa-arrow-right"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-onboarding-layout>