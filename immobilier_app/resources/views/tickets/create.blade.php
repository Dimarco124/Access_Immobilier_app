<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 w-full">
            <div class="flex  flex-col items-start gap-4 w-full">
                <h2 class="font-black text-2xl text-slate-900 uppercase tracking-tight">
                    {{ __('Nouvelle Demande d\'Intervention') }}
                </h2>
                
            </div>
            <p class="text-[11px] text-slate-500 font-bold uppercase tracking-widest">Signalez un problème technique ou une demande de maintenance</p>
        
            <div class="mt-1">
                <a href="{{ route('tickets.index') }}" class="text-xs font-black text-slate-500 hover:text-slate-700 uppercase tracking-widest flex  items-start  gap-2 self-start inline-flex">
                    <i class="fas fa-arrow-left self-start inline-flex"></i> Retour
                </a>
            </div>
        </div></x-slot>

    <div class="space-y-6">
        <div class="premium-card bg-white overflow-hidden">
            <div class="p-6 border-b border-slate-100 bg-slate-50/30">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-indigo-600 rounded-xl flex items-center justify-center text-white">
                        <i class="fas fa-tools"></i>
                    </div>
                    <h3 class="text-sm font-black text-slate-900 uppercase tracking-tight">Détails de la demande</h3>
                </div>
            </div>
            <div class="p-6">
                <form method="POST" action="{{ route('tickets.store') }}" class="space-y-6">
                    @csrf

                    <!-- Type d'intervention -->
                    <div>
                        <label for="type_intervention" class="text-[11px] font-black text-slate-400 uppercase tracking-widest block mb-2">Type d'intervention *</label>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <label class="flex items-center gap-3 p-4 border border-slate-200 rounded-xl cursor-pointer hover:border-indigo-500 transition-all">
                                <input type="radio" name="type_intervention" value="Maintenance" class="w-4 h-4 text-indigo-600" checked>
                                <div>
                                    <p class="text-sm font-semibold text-slate-900">Maintenance</p>
                                    <p class="text-[10px] text-slate-500">Entretien régulier, petit problème</p>
                                </div>
                            </label>
                            <label class="flex items-center gap-3 p-4 border border-slate-200 rounded-xl cursor-pointer hover:border-indigo-500 transition-all">
                                <input type="radio" name="type_intervention" value="Réparation" class="w-4 h-4 text-indigo-600">
                                <div>
                                    <p class="text-sm font-semibold text-slate-900">Réparation</p>
                                    <p class="text-[10px] text-slate-500">Panne, casse, problème technique</p>
                                </div>
                            </label>
                            <label class="flex items-center gap-3 p-4 border border-slate-200 rounded-xl cursor-pointer hover:border-indigo-500 transition-all">
                                <input type="radio" name="type_intervention" value="Plomberie" class="w-4 h-4 text-indigo-600">
                                <div>
                                    <p class="text-sm font-semibold text-slate-900">Plomberie</p>
                                    <p class="text-[10px] text-slate-500">Fuite, problème d'eau</p>
                                </div>
                            </label>
                            <label class="flex items-center gap-3 p-4 border border-slate-200 rounded-xl cursor-pointer hover:border-indigo-500 transition-all">
                                <input type="radio" name="type_intervention" value="Électricité" class="w-4 h-4 text-indigo-600">
                                <div>
                                    <p class="text-sm font-semibold text-slate-900">Électricité</p>
                                    <p class="text-[10px] text-slate-500">Panne électrique, ampoule, prise</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Objet -->
                    <div>
                        <label for="objet" class="text-[11px] font-black text-slate-400 uppercase tracking-widest block mb-2">Objet de la demande *</label>
                        <input id="objet" name="objet" type="text" placeholder="Ex: Ampoule gâtée dans le salon" class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
                        @error('objet')
                            <p class="text-xs text-red-500 mt-1 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Priorité -->
                    <div>
                        <label for="priorite" class="text-[11px] font-black text-slate-400 uppercase tracking-widest block mb-2">Priorité *</label>
                        <select id="priorite" name="priorite" class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" required>
                            <option value="Faible">Faible - Peut attendre (Entretien normal)</option>
                            <option value="Urgent">Urgent - Impacte le quotidien</option>
                            <option value="Critique">Critique - Urgence absolue (inondation, panne de chauffage)</option>
                        </select>
                        @error('priorite')
                            <p class="text-xs text-red-500 mt-1 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="description_probleme" class="text-[11px] font-black text-slate-400 uppercase tracking-widest block mb-2">Description détaillée *</label>
                        <textarea id="description_probleme" name="description_probleme" class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500" rows="5" required placeholder="Expliquez ici votre problème en détail (localisation, ce qui ne fonctionne pas, depuis quand...)"></textarea>
                        @error('description_probleme')
                            <p class="text-xs text-red-500 mt-1 font-semibold">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                        <a href="{{ route('tickets.index') }}" class="px-5 py-2.5 bg-slate-100 text-slate-700 text-[10px] font-black uppercase tracking-widest rounded-lg hover:bg-slate-200 transition-all">
                            Annuler
                        </a>
                        <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-[10px] font-black uppercase tracking-widest rounded-lg shadow-lg shadow-indigo-200 transition-all">
                            <i class="fas fa-paper-plane mr-1"></i> Envoyer la demande
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
