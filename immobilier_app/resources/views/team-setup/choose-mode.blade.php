<x-onboarding-layout>
    <x-slot name="header">
        {{-- Barre de progression stylée --}}
        <div class="flex items-center gap-2 mb-5">
            <div class="step-dot done"><i class="fas fa-check text-[10px]"></i></div>
            <div class="step-line done"></div>
            <div class="step-dot pending">2</div>
            <div class="step-line pending"></div>
            <div class="step-dot pending">3</div>
            <div class="step-line pending"></div>
            <div class="step-dot pending">4</div>
        </div>
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-2">
            <div>
                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-50 text-blue-600 text-[10px] font-black uppercase tracking-widest mb-2">
                    <span class="w-1.5 h-1.5 rounded-full bg-blue-600"></span>
                    Étape 1 sur 4
                </span>
                <h2 class="text-2xl font-black text-slate-900 tracking-tight">Configuration de votre agence</h2>
                <p class="text-sm text-slate-500 font-medium mt-1">Choisissez le mode de gestion qui correspond à votre organisation.</p>
            </div>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto">
        <form action="{{ route('team-setup.store-mode') }}" method="POST" class="space-y-6">
            @csrf

            {{-- Choix du mode --}}
            <div class="grid md:grid-cols-2 gap-5">
                {{-- Solo --}}
                <label class="cursor-pointer group">
                    <input type="radio" name="mode" value="solo" class="sr-only peer" {{ old('mode', 'solo') === 'solo' ? 'checked' : '' }}>
                    <div class="h-full relative bg-white border-2 border-slate-200 rounded-2xl p-7 transition-all duration-300
                        peer-checked:border-blue-500 peer-checked:ring-4 peer-checked:ring-blue-500/10 peer-checked:bg-gradient-to-br peer-checked:from-blue-50 peer-checked:to-indigo-50
                        hover:border-slate-300 hover:shadow-lg hover:shadow-slate-100">
                        {{-- Checkmark --}}
                        <div class="absolute top-4 right-4 w-6 h-6 rounded-full border-2 border-slate-300 flex items-center justify-center transition-all
                            peer-checked:border-blue-600 peer-checked:bg-blue-600 group-[.peer:checked~&]:bg-blue-600">
                        </div>
                        <div class="space-y-5">
                            <div class="w-16 h-16 bg-gradient-to-br from-slate-100 to-slate-200 rounded-2xl flex items-center justify-center text-slate-600 shadow-sm
                                peer-checked:from-blue-100 peer-checked:to-indigo-100 peer-checked:text-blue-700">
                                <i class="fas fa-user-tie text-2xl"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-black text-slate-900 uppercase tracking-tight mb-2">Je gère seul</h3>
                                <p class="text-sm text-slate-600 leading-relaxed">Accès complet à tous les modules de l'agence. Idéal pour un gestionnaire unique.</p>
                            </div>
                            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-slate-100 text-slate-600 text-[10px] font-black uppercase tracking-widest">
                                <i class="fas fa-check-circle text-emerald-500"></i>
                                Accès complet
                            </div>
                        </div>
                    </div>
                </label>

                {{-- Équipe --}}
                <label class="cursor-pointer group">
                    <input type="radio" name="mode" value="team" class="sr-only peer" {{ old('mode') === 'team' ? 'checked' : '' }}>
                    <div class="h-full relative bg-white border-2 border-slate-200 rounded-2xl p-7 transition-all duration-300
                        peer-checked:border-blue-500 peer-checked:ring-4 peer-checked:ring-blue-500/10 peer-checked:bg-gradient-to-br peer-checked:from-blue-50 peer-checked:to-indigo-50
                        hover:border-slate-300 hover:shadow-lg hover:shadow-slate-100">
                        <div class="space-y-5">
                            <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-2xl flex items-center justify-center text-white shadow-lg shadow-blue-200">
                                <i class="fas fa-users text-2xl"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-black text-slate-900 uppercase tracking-tight mb-2">Nous sommes une équipe</h3>
                                <p class="text-sm text-slate-600 leading-relaxed">Définissez les responsabilités de chaque membre et organisez votre équipe par modules.</p>
                            </div>
                            <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-blue-50 text-blue-600 text-[10px] font-black uppercase tracking-widest">
                                <i class="fas fa-users text-blue-500"></i>
                                Gestion d'équipe
                            </div>
                        </div>
                    </div>
                </label>
            </div>

            @error('mode')
                <div class="p-4 rounded-2xl bg-red-50 border border-red-200 flex items-start gap-3">
                    <i class="fas fa-exclamation-circle text-red-500 mt-0.5"></i>
                    <p class="text-sm text-red-700 font-semibold">{{ $message }}</p>
                </div>
            @enderror

            {{-- Info --}}
            <div class="bg-blue-50/60 border border-blue-100 rounded-2xl p-5 flex items-start gap-4">
                <div class="w-9 h-9 rounded-xl bg-blue-100 flex items-center justify-center text-blue-600 shrink-0">
                    <i class="fas fa-info"></i>
                </div>
                <div>
                    <p class="text-sm font-black text-slate-800 mb-1">Vous pourrez changer ce paramètre plus tard</p>
                    <p class="text-xs text-slate-500">L'onglet de gestion d'équipe restera accessible pour faire évoluer votre organisation.</p>
                </div>
            </div>

            {{-- Action --}}
            <div class="flex justify-end pt-2">
                <button type="submit"
                    class="inline-flex items-center gap-3 px-8 py-3.5 rounded-2xl bg-gradient-to-r from-blue-600 to-indigo-600 text-white text-sm font-black uppercase tracking-widest shadow-lg shadow-blue-200 hover:from-blue-700 hover:to-indigo-700 hover:shadow-xl hover:shadow-blue-200 transition-all duration-200 hover:-translate-y-0.5">
                    Suivant
                    <i class="fas fa-arrow-right"></i>
                </button>
            </div>
        </form>
    </div>
</x-onboarding-layout>
