<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-3">
                    <h2 class="font-black text-2xl text-slate-900 uppercase tracking-tight">Gestion de l'Équipe</h2>
                    @if($config->isSoloMode())
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-50 text-amber-700 border border-amber-200 text-xs font-black uppercase">
                            <i class="fas fa-user text-amber-500"></i> Mode Solo
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 text-xs font-black uppercase">
                            <i class="fas fa-users text-emerald-500"></i> Mode Équipe
                        </span>
                    @endif
                </div>
                <p class="text-xs text-slate-500 font-bold uppercase tracking-widest mt-1">Gérez les accès, rôles et permissions de votre agence</p>
            </div>
            
            <div class="flex items-center flex-wrap gap-2.5">
                @if($config->isSoloMode())
                    <form method="POST" action="{{ route('team.switch-to-team-mode') }}" class="inline">
                        @csrf
                        <button type="submit" class="px-4 py-2.5 bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 text-white font-black text-xs uppercase tracking-wider rounded-xl shadow-md hover:shadow-lg transition-all flex items-center gap-2">
                            <i class="fas fa-users-cog"></i>
                            <span>Activer le mode équipe</span>
                        </button>
                    </form>
                @else
                    <form method="POST" action="{{ route('team.switch-to-solo-mode') }}" class="inline">
                        @csrf
                        <button type="submit" onclick="return confirm('Êtes-vous sûr de vouloir repasser en mode Solo ?')" class="px-3.5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl border border-slate-200 transition-colors flex items-center gap-2" title="Repasser en gestion solo">
                            <i class="fas fa-user"></i>
                            <span>Passer en mode solo</span>
                        </button>
                    </form>
                @endif

                <a href="{{ route('team.create') }}" class="px-4 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-black text-xs uppercase tracking-wider rounded-xl shadow-md hover:shadow-lg transition-all flex items-center gap-2">
                    <i class="fas fa-user-plus"></i>
                    <span>Ajouter un membre</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-6">
        @if(session('success'))
            <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-bold flex items-center gap-3">
                <i class="fas fa-check-circle text-emerald-600 text-lg"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if(session('info'))
            <div class="p-4 rounded-xl bg-blue-50 border border-blue-200 text-blue-800 text-sm font-bold flex items-center gap-3">
                <i class="fas fa-info-circle text-blue-600 text-lg"></i>
                <span>{{ session('info') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="p-4 rounded-xl bg-red-50 border border-red-200 text-red-800 text-sm font-bold flex items-center gap-3">
                <i class="fas fa-exclamation-circle text-red-600 text-lg"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        @if($config->isSoloMode())
            <!-- Carte explicative Mode Solo -->
            <div class="premium-card p-6 bg-gradient-to-r from-blue-50/70 via-indigo-50/50 to-white border-2 border-blue-200 rounded-2xl">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 rounded-xl bg-blue-600 text-white flex items-center justify-center shrink-0 shadow-md shadow-blue-200">
                            <i class="fas fa-user-shield text-xl"></i>
                        </div>
                        <div>
                            <h3 class="font-black text-base text-slate-900 uppercase tracking-tight">Mode Solo Actif</h3>
                            <p class="text-sm text-slate-600 mt-1 max-w-2xl">
                                Vous gérez actuellement votre agence de manière autonome avec tous les privilèges. Vous pouvez activer le <strong>Mode Équipe</strong> à tout moment pour intégrer des collaborateurs et leur déléguer des modules spécifiques (Gestion locative, Finances, Visites, Contrats...).
                            </p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 self-start md:self-center shrink-0">
                        <form method="POST" action="{{ route('team.switch-to-team-mode') }}">
                            @csrf
                            <button type="submit" class="px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-black text-xs uppercase tracking-wider rounded-xl shadow-md transition-all">
                                <i class="fas fa-users mr-1.5"></i>
                                Activer le Mode Équipe
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @else
            <!-- Carte récapitulative Mode Équipe -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="premium-card p-5 bg-white border border-slate-200 rounded-2xl flex items-center gap-4">
                    <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 border border-emerald-100 flex items-center justify-center text-lg">
                        <i class="fas fa-user-check"></i>
                    </div>
                    <div>
                        <p class="text-[11px] font-black uppercase tracking-wider text-slate-400">Membres Actifs</p>
                        <p class="text-2xl font-black text-slate-900">{{ $teamMembers->where('status', 'active')->count() }}</p>
                    </div>
                </div>

                <div class="premium-card p-5 bg-white border border-slate-200 rounded-2xl flex items-center gap-4">
                    <div class="w-11 h-11 rounded-xl bg-amber-50 text-amber-600 border border-amber-100 flex items-center justify-center text-lg">
                        <i class="fas fa-user-clock"></i>
                    </div>
                    <div>
                        <p class="text-[11px] font-black uppercase tracking-wider text-slate-400">Membres Suspendus</p>
                        <p class="text-2xl font-black text-slate-900">{{ $teamMembers->where('status', 'suspended')->count() }}</p>
                    </div>
                </div>

                <div class="premium-card p-5 bg-white border border-slate-200 rounded-2xl flex items-center gap-4">
                    <div class="w-11 h-11 rounded-xl bg-blue-50 text-blue-600 border border-blue-100 flex items-center justify-center text-lg">
                        <i class="fas fa-cubes"></i>
                    </div>
                    <div>
                        <p class="text-[11px] font-black uppercase tracking-wider text-slate-400">Modules Configurables</p>
                        <p class="text-2xl font-black text-slate-900">{{ $modules->count() }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if($teamMembers->count() > 0)
            <!-- Matrice des responsabilités -->
            <div class="premium-card p-6 bg-white border border-slate-200 rounded-2xl shadow-sm">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                    <div>
                        <h3 class="font-black text-lg text-slate-900 uppercase tracking-tight">Matrice des Responsabilités</h3>
                        <p class="text-xs text-slate-500 font-bold uppercase tracking-wider mt-0.5">Vue globale des affectations et des niveaux d'accès par module</p>
                    </div>
                    <a href="{{ route('team.create') }}" class="inline-flex items-center gap-2 px-3.5 py-2 bg-blue-50 hover:bg-blue-100 text-blue-700 font-bold text-xs rounded-xl border border-blue-200 transition-colors self-start sm:self-auto">
                        <i class="fas fa-plus-circle"></i>
                        <span>Nouveau membre</span>
                    </a>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b-2 border-slate-100 bg-slate-50/70">
                                <th class="text-left text-xs font-black text-slate-600 uppercase px-4 py-3 rounded-l-lg">Collaborateur</th>
                                @foreach($modules as $module)
                                    <th class="text-center text-xs font-black text-slate-600 uppercase px-3 py-3">
                                        <div class="flex flex-col items-center gap-1">
                                            <i class="{{ $module->icon }} text-base text-slate-700"></i>
                                            <span class="text-[10px] whitespace-nowrap">{{ $module->nom }}</span>
                                        </div>
                                    </th>
                                @endforeach
                                <th class="text-center text-xs font-black text-slate-600 uppercase px-4 py-3 rounded-r-lg">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($teamMembers as $member)
                                <tr class="hover:bg-slate-50/80 transition-colors">
                                    <td class="px-4 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 text-white flex items-center justify-center font-black text-sm shadow-sm shrink-0">
                                                {{ strtoupper(substr($member->nom_complet, 0, 1)) }}
                                            </div>
                                            <div class="min-w-0">
                                                <p class="font-bold text-slate-900 truncate">{{ $member->nom_complet }}</p>
                                                <p class="text-xs text-slate-500 truncate">{{ $member->email }}</p>
                                                @if($member->status === 'suspended')
                                                    <span class="inline-block px-2 py-0.5 bg-red-100 text-red-700 text-[9px] font-black rounded-full mt-0.5">Suspendu</span>
                                                @else
                                                    <span class="inline-block px-2 py-0.5 bg-emerald-100 text-emerald-700 text-[9px] font-black rounded-full mt-0.5">Actif</span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    @foreach($modules as $module)
                                        <td class="px-3 py-4 text-center">
                                            @php
                                                $assignment = $matrix[$member->id][$module->id] ?? null;
                                            @endphp
                                            @if($assignment)
                                                <div class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold {{ 
                                                    $assignment->isManager() ? 'bg-emerald-100 text-emerald-800' : 
                                                    ($assignment->isCollaborator() ? 'bg-blue-100 text-blue-800' : 'bg-slate-100 text-slate-700')
                                                }}" title="{{ $assignment->isManager() ? 'Gestionnaire' : ($assignment->isCollaborator() ? 'Collaborateur' : 'Consultant') }}">
                                                    @if($assignment->isManager())
                                                        <i class="fas fa-star text-[10px] text-amber-500"></i><i class="fas fa-star text-[10px] text-amber-500"></i><i class="fas fa-star text-[10px] text-amber-500"></i>
                                                    @elseif($assignment->isCollaborator())
                                                        <i class="fas fa-star text-[10px] text-blue-500"></i><i class="fas fa-star text-[10px] text-blue-500"></i>
                                                    @else
                                                        <i class="fas fa-eye text-[10px] text-slate-500"></i>
                                                    @endif
                                                </div>
                                            @else
                                                <span class="text-slate-300 font-bold">-</span>
                                            @endif
                                        </td>
                                    @endforeach
                                    <td class="px-4 py-4">
                                        <div class="flex items-center justify-center gap-1.5">
                                            <a href="{{ route('team.edit', $member) }}" class="p-2 text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded-lg transition-colors" title="Modifier les accès">
                                                <i class="fas fa-edit"></i>
                                            </a>

                                            <a href="{{ route('team.reset-password', $member) }}" class="p-2 text-indigo-600 hover:text-indigo-800 hover:bg-indigo-50 rounded-lg transition-colors" title="Réinitialiser le mot de passe">
                                                <i class="fas fa-key"></i>
                                            </a>

                                            @if($member->status === 'active')
                                                <form method="POST" action="{{ route('team.suspend', $member) }}" class="inline">
                                                    @csrf
                                                    <button type="submit" class="p-2 text-amber-600 hover:text-amber-800 hover:bg-amber-50 rounded-lg transition-colors" title="Suspendre le compte" onclick="return confirm('Suspendre ce membre ?')">
                                                        <i class="fas fa-pause-circle"></i>
                                                    </button>
                                                </form>
                                            @elseif($member->status === 'suspended')
                                                <form method="POST" action="{{ route('team.reactivate', $member) }}" class="inline">
                                                    @csrf
                                                    <button type="submit" class="p-2 text-emerald-600 hover:text-emerald-800 hover:bg-emerald-50 rounded-lg transition-colors" title="Réactiver le compte">
                                                        <i class="fas fa-play-circle"></i>
                                                    </button>
                                                </form>
                                            @endif

                                            <a href="{{ route('team.history', $member) }}" class="p-2 text-slate-600 hover:text-slate-800 hover:bg-slate-100 rounded-lg transition-colors" title="Historique des accès">
                                                <i class="fas fa-history"></i>
                                            </a>

                                            <form method="POST" action="{{ route('team.destroy', $member) }}" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-2 text-red-600 hover:text-red-800 hover:bg-red-50 rounded-lg transition-colors" title="Supprimer définitivement" onclick="return confirm('Supprimer définitivement ce membre ? Cette action est irréversible.')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-6 p-4 bg-slate-50/80 rounded-xl border border-slate-200">
                    <p class="text-xs font-black text-slate-600 uppercase mb-2">Légende des niveaux d'accès :</p>
                    <div class="flex flex-wrap gap-6">
                        <div class="flex items-center gap-2">
                            <div class="flex items-center gap-0.5 text-amber-500">
                                <i class="fas fa-star text-xs"></i>
                                <i class="fas fa-star text-xs"></i>
                                <i class="fas fa-star text-xs"></i>
                            </div>
                            <span class="text-xs text-slate-700 font-bold">Gestionnaire (CRUD complet)</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="flex items-center gap-0.5 text-blue-500">
                                <i class="fas fa-star text-xs"></i>
                                <i class="fas fa-star text-xs"></i>
                            </div>
                            <span class="text-xs text-slate-700 font-bold">Collaborateur (Créer / Modifier)</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i class="fas fa-eye text-slate-500 text-xs"></i>
                            <span class="text-xs text-slate-700 font-bold">Consultant (Lecture seule)</span>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <!-- Aucun collaborateur -->
            <div class="premium-card p-12 text-center bg-white border border-slate-200 rounded-2xl shadow-sm">
                <div class="w-20 h-20 mx-auto rounded-2xl bg-blue-50 border border-blue-100 flex items-center justify-center mb-4">
                    <i class="fas fa-users text-blue-600 text-3xl"></i>
                </div>
                <h3 class="font-black text-xl text-slate-900 mb-2 uppercase tracking-tight">Aucun collaborateur enregistré</h3>
                <p class="text-slate-600 max-w-md mx-auto mb-6 text-sm">
                    Invitez des membres de votre équipe pour leur déléguer des modules et gérer votre agence en toute sérénité.
                </p>
                <div class="flex items-center justify-center gap-3">
                    <a href="{{ route('team.create') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-black text-xs uppercase tracking-wider rounded-xl shadow-lg hover:shadow-xl hover:scale-105 transition-all">
                        <i class="fas fa-user-plus"></i>
                        Ajouter un collaborateur
                    </a>
                </div>
            </div>
        @endif
    </div>
</x-app-layout>
