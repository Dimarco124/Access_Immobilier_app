<x-onboarding-layout>
    <x-slot name="header">
        {{-- Barre de progression --}}
        <div class="flex items-center gap-2 mb-5">
            <div class="step-dot done"><i class="fas fa-check text-[10px]"></i></div>
            <div class="step-line done"></div>
            <div class="step-dot done"><i class="fas fa-check text-[10px]"></i></div>
            <div class="step-line done"></div>
            <div class="step-dot pending">3</div>
            <div class="step-line pending"></div>
            <div class="step-dot pending">4</div>
        </div>
        <div>
            <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-50 text-blue-600 text-[10px] font-black uppercase tracking-widest mb-2">
                <span class="w-1.5 h-1.5 rounded-full bg-blue-600"></span>
                Étape 2 sur 4
            </span>
            <h2 class="text-2xl font-black text-slate-900 tracking-tight">Sélection des modules</h2>
            <p class="text-sm text-slate-500 font-medium mt-1">Cochez les modules que vous souhaitez déléguer à votre équipe.</p>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto">
        <form method="POST" action="{{ route('team-setup.assign-modules') }}" class="space-y-6">
            @csrf

            {{-- Grille des modules --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @foreach($modules as $module)
                    <label class="cursor-pointer group">
                        <input type="checkbox" name="selected_modules[]" value="{{ $module->id }}"
                            class="sr-only peer" {{ $module->is_required ? 'checked onclick="return false;"' : '' }}>
                        <div class="relative bg-white border-2 border-slate-200 rounded-2xl p-5 transition-all duration-200
                            peer-checked:border-blue-500 peer-checked:ring-4 peer-checked:ring-blue-500/10 peer-checked:bg-blue-50/50
                            hover:border-slate-300 hover:shadow-md hover:shadow-slate-100">
                            {{-- Checkbox visuel --}}
                            <div class="absolute top-4 right-4 w-6 h-6 rounded-lg border-2 border-slate-300 flex items-center justify-center transition-all
                                peer-checked:border-blue-600 peer-checked:bg-blue-600 group-[peer:checked]:bg-blue-600">
                                <i class="fas fa-check text-white text-[9px] opacity-0 peer-checked:opacity-100"></i>
                            </div>
                            <div class="flex items-start gap-4 pr-6">
                                <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-blue-50 to-indigo-100 flex items-center justify-center text-blue-600 shrink-0">
                                    <i class="{{ $module->icon }} text-lg"></i>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 flex-wrap mb-1">
                                        <h3 class="text-sm font-black text-slate-900 uppercase tracking-tight">{{ $module->nom }}</h3>
                                        @if($module->is_required)
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-amber-50 text-amber-600 text-[9px] font-black uppercase tracking-widest">
                                                <i class="fas fa-star text-[8px]"></i>Obligatoire
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-slate-100 text-slate-500 text-[9px] font-black uppercase tracking-widest">
                                                Optionnel
                                            </span>
                                        @endif
                                    </div>
                                    <p class="text-xs text-slate-500 leading-relaxed">{{ $module->description }}</p>
                                </div>
                            </div>
                        </div>
                    </label>
                @endforeach
            </div>

            @error('selected_modules')
                <div class="p-4 rounded-2xl bg-red-50 border border-red-200 flex items-start gap-3">
                    <i class="fas fa-exclamation-circle text-red-500 mt-0.5"></i>
                    <p class="text-sm text-red-700 font-semibold">{{ $message }}</p>
                </div>
            @enderror

            {{-- Astuce --}}
            <div class="bg-blue-50/60 border border-blue-100 rounded-2xl p-5 flex items-start gap-4">
                <div class="w-9 h-9 rounded-xl bg-blue-100 flex items-center justify-center text-blue-600 shrink-0">
                    <i class="fas fa-lightbulb"></i>
                </div>
                <div>
                    <p class="text-sm font-black text-slate-800 mb-1">Astuce</p>
                    <p class="text-xs text-slate-500">Vous pouvez sélectionner plusieurs modules, puis cliquer sur Suivant pour définir les responsables.</p>
                </div>
            </div>

            {{-- Navigation --}}
            <div class="flex items-center justify-between pt-2">
                <a href="{{ route('team-setup.choose-mode') }}"
                    class="inline-flex items-center gap-2 px-6 py-3 rounded-2xl border-2 border-slate-200 bg-white text-sm font-black text-slate-600 uppercase tracking-widest hover:bg-slate-50 hover:border-slate-300 transition-all">
                    <i class="fas fa-arrow-left"></i> Retour
                </a>
                <button type="submit"
                    class="inline-flex items-center gap-3 px-8 py-3.5 rounded-2xl bg-gradient-to-r from-blue-600 to-indigo-600 text-white text-sm font-black uppercase tracking-widest shadow-lg shadow-blue-200 hover:from-blue-700 hover:to-indigo-700 hover:shadow-xl transition-all duration-200 hover:-translate-y-0.5">
                    Suivant <i class="fas fa-arrow-right"></i>
                </button>
            </div>
        </form>
    </div>
</x-onboarding-layout>
