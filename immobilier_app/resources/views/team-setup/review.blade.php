<x-onboarding-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 w-full">
            <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                <div>
                    <h2 class="font-black text-xl md:text-2xl text-slate-900 tracking-tight uppercase">Récapitulatif de
                        l’équipe</h2>
                    <p class="text-[10px] text-slate-500 font-bold uppercase tracking-widest mt-1">Vérifiez votre
                        configuration avant de finaliser — les comptes seront créés et activés par l'administrateur.</p>
                </div>
                <div
                    class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-slate-100 text-slate-600 text-[10px] font-black uppercase tracking-widest">
                    Étape 4/4</div>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-6xl mx-auto space-y-12">
            <form method="POST" action="{{ route('team-setup.finalize') }}">
                @csrf

                @foreach($membersData as $index => $member)
                    <input type="hidden" name="members[{{ $index }}][nom_complet]" value="{{ $member['nom_complet'] }}">
                    <input type="hidden" name="members[{{ $index }}][email]" value="{{ $member['email'] }}">
                    <input type="hidden" name="members[{{ $index }}][telephone]" value="{{ $member['telephone'] ?? '' }}">
                    @foreach($member['modules'] as $moduleIndex => $moduleId)
                        <input type="hidden" name="members[{{ $index }}][modules][]" value="{{ $moduleId }}">
                        <input type="hidden" name="members[{{ $index }}][access_levels][]"
                            value="{{ $member['access_levels'][$moduleIndex] }}">
                    @endforeach
                @endforeach

                <div class="premium-card border border-slate-200 bg-slate-50 p-6 mb-6">
                    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                        <div class="flex items-center gap-4">
                            <div class="w-16 h-16 rounded-2xl bg-white shadow flex items-center justify-center">
                                <i class="fas fa-users text-blue-600 text-2xl"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-black text-slate-900 uppercase tracking-tight mb-1">
                                    Configuration de l’équipe</h3>
                                <div class="flex flex-wrap gap-3 text-sm font-bold text-slate-600">
                                    <span><span class="text-blue-600">{{ count($membersData) }}</span> membre(s)</span>
                                    <span>•</span>
                                    <span><span
                                            class="text-blue-600">{{ collect($membersData)->sum(fn($m) => count($m['modules'])) }}</span>
                                        attribution(s)</span>
                                </div>
                            </div>
                        </div>
                        <div
                            class="rounded-full bg-white px-4 py-2 text-xs font-black uppercase tracking-widest text-slate-500 border border-slate-200">
                            Suivi de la configuration</div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mt-8">
                    @foreach($membersData as $member)
                        <div class="premium-card border border-slate-200 p-6 mb-6">
                            <div class="flex items-start gap-4 mb-4">
                                <div
                                    class="w-12 h-12 rounded-full bg-gradient-to-br from-blue-100 to-indigo-100 flex items-center justify-center text-blue-600">
                                    <span class="font-black text-lg">{{ substr($member['nom_complet'], 0, 1) }}</span>
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-lg font-black text-slate-900 uppercase tracking-tight">
                                        {{ $member['nom_complet'] }}
                                    </h3>
                                    <p class="text-sm text-slate-600">{{ $member['email'] }}</p>
                                    @if(!empty($member['telephone']))
                                        <p class="text-xs text-slate-500">{{ $member['telephone'] }}</p>
                                    @endif
                                </div>
                            </div>

                            <div class="rounded-2xl bg-slate-50 border border-slate-200 p-4">
                                <p class="text-xs font-black uppercase tracking-widest text-slate-500 mb-3">Modules assignés
                                </p>
                                <div class="space-y-3">
                                    @foreach($member['modules_details'] as $index => $module)
                                        <div
                                            class="flex items-center justify-between rounded-xl bg-white border border-slate-200 px-4 py-3">
                                            <div class="flex items-center gap-3">
                                                <i class="{{ $module->icon }} text-blue-600"></i>
                                                <span class="text-sm font-bold text-slate-700">{{ $module->nom }}</span>
                                            </div>
                                            <span
                                                class="rounded-full bg-slate-100 px-3 py-1 text-[10px] font-black uppercase tracking-widest text-slate-500">
                                                @php
                                                    $level = $member['access_levels'][$index] ?? 'viewer';
                                                    $labels = ['manager' => 'Gestionnaire', 'collaborator' => 'Collaborateur', 'viewer' => 'Consultant'];
                                                @endphp
                                                {{ $labels[$level] }}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="premium-card bg-emerald-50 border border-emerald-200 p-6 mt-8 mb-6">
                    <div class="flex items-start gap-4">
                        <i class="fas fa-check-circle text-emerald-600 text-xl mt-1"></i>
                        <div>
                            <p class="text-sm text-emerald-900 font-bold mb-1">Prêt à finaliser la configuration</p>
                            <p class="text-xs text-emerald-700">Les comptes seront créés et activés immédiatement avec
                                le mot de passe fourni par l'administrateur.</p>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col gap-4 sm:flex-row sm:justify-between sm:items-center">
                    <a href="{{ route('team-setup.select-modules') }}"
                        class="inline-flex items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-6 py-3 text-sm font-black text-slate-700 uppercase tracking-widest hover:bg-slate-50 transition">
                        <i class="fas fa-arrow-left"></i>Modifier
                    </a>
                    <button type="submit"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-600 px-6 py-3 text-sm font-black text-white uppercase tracking-widest shadow-lg shadow-emerald-200 hover:bg-emerald-700 transition">
                        <i class="fas fa-check"></i>Finaliser la configuration
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-onboarding-layout>