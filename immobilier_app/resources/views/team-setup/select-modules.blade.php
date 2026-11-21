<x-onboarding-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 w-full">
            <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                <div>
                    <h2 class="font-black text-xl md:text-2xl text-slate-900 tracking-tight uppercase">Sélection des
                        modules</h2>
                    <p class="text-[10px] text-slate-500 font-bold uppercase tracking-widest mt-1">Cliquez sur les
                        cartes pour sélectionner les modules que vous souhaitez déléguer.</p>
                </div>
                <div
                    class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-slate-100 text-slate-600 text-[10px] font-black uppercase tracking-widest">
                    Étape 2/4</div>
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-6xl mx-auto space-y-10">
            <form method="POST" action="{{ route('team-setup.assign-modules') }}" class="space-y-8">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        @foreach($modules as $module)
                            <label class="cursor-pointer">
                                <input type="checkbox" name="selected_modules[]" value="{{ $module->id }}"
                                    class="sr-only peer" {{ $module->is_required ? 'checked onclick="return false;"' : '' }}>
                                <div
                                    class="premium-card h-full border border-slate-200 p-6 transition-all duration-300 hover:border-blue-400 peer-checked:border-blue-500 peer-checked:bg-blue-50 peer-checked:ring-1 peer-checked:ring-blue-500">
                                    <div class="flex items-start gap-4">
                                        <div
                                            class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-100 to-indigo-100 flex items-center justify-center text-blue-600">
                                            <i class="{{ $module->icon }} text-xl"></i>
                                        </div>
                                        <div class="flex-1 space-y-2">
                                            <div class="flex items-center justify-between gap-3">
                                                <h3 class="text-lg font-black text-slate-900 uppercase tracking-tight">
                                                    {{ $module->nom }}
                                                </h3>
                                                <div
                                                    class="w-7 h-7 rounded-full border border-slate-300 flex items-center justify-center peer-checked:border-blue-600 peer-checked:bg-blue-600 transition">
                                                    <i
                                                        class="fas fa-check text-white text-xs opacity-0 peer-checked:opacity-100"></i>
                                                </div>
                                            </div>
                                            <p class="text-sm text-slate-600">{{ $module->description }}</p>
                                            @if($module->is_required)
                                                <span
                                                    class="inline-flex items-center gap-2 px-2 py-1 rounded-full bg-red-100 text-red-700 text-[10px] font-black uppercase tracking-widest">
                                                    <i class="fas fa-star text-xs"></i>Obligatoire
                                                </span>
                                            @else
                                                <span
                                                    class="inline-flex items-center gap-2 px-2 py-1 rounded-full bg-slate-100 text-slate-600 text-[10px] font-black uppercase tracking-widest">Optionnel</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </label>
                        @endforeach
                    </div>

                    @error('selected_modules')
                        <div class="mb-6 premium-card p-4 bg-red-50 border-2 border-red-200">
                            <div class="flex items-start gap-3">
                                <i class="fas fa-exclamation-circle text-red-600"></i>
                                <p class="text-sm text-red-700 font-bold">{{ $message }}</p>
                            </div>
                        </div>
                    @enderror

                    <div class="premium-card p-6 bg-slate-50 border border-slate-200 mt-8">
                        <div class="flex items-start gap-4">
                            <i class="fas fa-info-circle text-slate-500 text-xl mt-1"></i>
                            <div>
                                <p class="text-sm text-slate-900 font-bold mb-1">Astuce</p>
                                <p class="text-xs text-slate-600">Vous pouvez sélectionner plusieurs modules, puis
                                    cliquer
                                    sur Suivant pour définir les responsables.</p>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-between items-center gap-4 mt-8">
                        <a href="{{ route('team-setup.choose-mode') }}"
                            class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-6 py-3 text-sm font-black text-slate-700 uppercase tracking-widest hover:bg-slate-50 transition">
                            <i class="fas fa-arrow-left"></i>Retour
                        </a>
                        <button type="submit"
                            class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-6 py-3 text-sm font-black text-white uppercase tracking-widest shadow-lg shadow-blue-200 hover:bg-blue-700 transition">
                            Suivant
                            <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>
            </form>
        </div>
    </div>
</x-onboarding-layout>