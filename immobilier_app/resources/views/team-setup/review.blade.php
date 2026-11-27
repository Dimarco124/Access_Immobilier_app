<x-onboarding-layout>
    <x-slot name="header">
        {{-- Barre de progression complète --}}
        <div class="flex items-center gap-2 mb-5">
            <div class="step-dot done"><i class="fas fa-check text-[10px]"></i></div>
            <div class="step-line done"></div>
            <div class="step-dot done"><i class="fas fa-check text-[10px]"></i></div>
            <div class="step-line done"></div>
            <div class="step-dot done"><i class="fas fa-check text-[10px]"></i></div>
            <div class="step-line done"></div>
            <div class="step-dot done"><i class="fas fa-check text-[10px]"></i></div>
        </div>
        <div>
            <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-50 text-emerald-600 text-[10px] font-black uppercase tracking-widest mb-2">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                Étape 4 sur 4 — Récapitulatif
            </span>
            <h2 class="text-2xl font-black text-slate-900 tracking-tight">Récapitulatif de l'équipe</h2>
            <p class="text-sm text-slate-500 font-medium mt-1">Vérifiez votre configuration avant de finaliser — les comptes seront créés et activés immédiatement.</p>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto">
        <form method="POST" action="{{ route('team-setup.finalize') }}" class="space-y-6">
            @csrf

            {{-- Hidden fields --}}
            @foreach($membersData as $index => $member)
                <input type="hidden" name="members[{{ $index }}][nom_complet]" value="{{ $member['nom_complet'] }}">
                <input type="hidden" name="members[{{ $index }}][email]" value="{{ $member['email'] }}">
                <input type="hidden" name="members[{{ $index }}][telephone]" value="{{ $member['telephone'] ?? '' }}">
                @foreach($member['modules'] as $moduleIndex => $moduleId)
                    <input type="hidden" name="members[{{ $index }}][modules][]" value="{{ $moduleId }}">
                    <input type="hidden" name="members[{{ $index }}][access_levels][]" value="{{ $member['access_levels'][$moduleIndex] }}">
                @endforeach
            @endforeach

            {{-- Résumé global --}}
            <div class="bg-white border-2 border-slate-200 rounded-2xl p-6 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                <div class="flex items-center gap-5">
                    <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white shadow-lg shadow-blue-200">
                        <i class="fas fa-users text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-black text-slate-900 uppercase tracking-tight mb-1">Configuration de l'équipe</h3>
                        <div class="flex flex-wrap gap-4 text-sm font-bold text-slate-600">
                            <span><span class="text-blue-600 text-base font-black">{{ count($membersData) }}</span> membre(s)</span>
                            <span class="text-slate-300">•</span>
                            <span><span class="text-blue-600 text-base font-black">{{ collect($membersData)->sum(fn($m) => count($m['modules'])) }}</span> attribution(s)</span>
                        </div>
                    </div>
                </div>
                <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-blue-50 text-blue-600 text-[10px] font-black uppercase tracking-widest border border-blue-100">
                    <i class="fas fa-cog"></i> Suivi de la configuration
                </span>
            </div>

            {{-- Cartes membres --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                @foreach($membersData as $member)
                    <div class="bg-white border-2 border-slate-200 rounded-2xl overflow-hidden hover:border-slate-300 hover:shadow-md transition-all">
                        {{-- Identité --}}
                        <div class="flex items-center gap-4 p-5 border-b border-slate-100 bg-gradient-to-r from-slate-50 to-blue-50/30">
                            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white font-black text-lg shadow-sm">
                                {{ strtoupper(substr($member['nom_complet'], 0, 1)) }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3 class="text-sm font-black text-slate-900 uppercase tracking-tight truncate">{{ $member['nom_complet'] }}</h3>
                                <p class="text-xs text-slate-500 font-medium truncate">{{ $member['email'] }}</p>
                                @if(!empty($member['telephone']))
                                    <p class="text-[10px] text-slate-400">{{ $member['telephone'] }}</p>
                                @endif
                            </div>
                        </div>

                        {{-- Modules assignés --}}
                        <div class="p-5">
                            <p class="text-[9px] font-black uppercase tracking-widest text-slate-400 mb-3">Modules assignés</p>
                            <div class="space-y-2">
                                @foreach($member['modules_details'] as $index => $module)
                                    <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 border border-slate-100 hover:bg-blue-50/40 hover:border-blue-100 transition-all">
                                        <div class="flex items-center gap-3">
                                            <div class="w-7 h-7 rounded-lg bg-gradient-to-br from-blue-50 to-indigo-100 flex items-center justify-center text-blue-600">
                                                <i class="{{ $module->icon }} text-xs"></i>
                                            </div>
                                            <span class="text-xs font-black text-slate-700">{{ $module->nom }}</span>
                                        </div>
                                        @php
                                            $level = $member['access_levels'][$index] ?? 'viewer';
                                            $labels = ['manager' => 'Gestionnaire', 'collaborator' => 'Collaborateur', 'viewer' => 'Consultant'];
                                            $colors = ['manager' => 'bg-blue-50 text-blue-600 border-blue-100', 'collaborator' => 'bg-indigo-50 text-indigo-600 border-indigo-100', 'viewer' => 'bg-slate-100 text-slate-500 border-slate-200'];
                                        @endphp
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[9px] font-black uppercase tracking-widest border {{ $colors[$level] ?? 'bg-slate-100 text-slate-500' }}">
                                            {{ $labels[$level] ?? $level }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Prêt à finaliser --}}
            <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-5 flex items-start gap-4">
                <div class="w-9 h-9 rounded-xl bg-emerald-100 flex items-center justify-center text-emerald-600 shrink-0">
                    <i class="fas fa-check-circle text-lg"></i>
                </div>
                <div>
                    <p class="text-sm font-black text-emerald-900 mb-1">Prêt à finaliser la configuration</p>
                    <p class="text-xs text-emerald-700">Les comptes seront créés et activés immédiatement avec le mot de passe fourni par l'administrateur.</p>
                </div>
            </div>

            {{-- Navigation --}}
            <div class="flex items-center justify-between pt-2">
                <a href="{{ route('team-setup.select-modules') }}"
                    class="inline-flex items-center gap-2 px-6 py-3 rounded-2xl border-2 border-slate-200 bg-white text-sm font-black text-slate-600 uppercase tracking-widest hover:bg-slate-50 hover:border-slate-300 transition-all">
                    <i class="fas fa-arrow-left"></i> Modifier
                </a>
                <button type="submit"
                    class="inline-flex items-center gap-3 px-8 py-3.5 rounded-2xl bg-gradient-to-r from-emerald-500 to-teal-600 text-white text-sm font-black uppercase tracking-widest shadow-lg shadow-emerald-200 hover:from-emerald-600 hover:to-teal-700 hover:shadow-xl transition-all duration-200 hover:-translate-y-0.5">
                    <i class="fas fa-check"></i> Finaliser la configuration
                </button>
            </div>
        </form>
    </div>
</x-onboarding-layout>
