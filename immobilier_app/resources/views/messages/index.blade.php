<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 w-full">
            <div>
                <h2 class="font-black text-2xl md:text-4xl text-slate-900 tracking-tight uppercase">Messagerie</h2>
                <p class="text-slate-500 font-medium mt-1 text-sm">Gérez vos échanges avec les locataires, propriétaires et l'agence</p>
            </div>
            <div>
                <a href="{{ route('messages.create') }}" class="btn-premium bg-blue-600 text-white px-5 py-3 md:px-8 md:py-4 inline-flex items-center gap-2 hover:bg-blue-700 shadow-lg shadow-blue-100 transition transform hover:-translate-y-1">
                    <i class="fas fa-plus"></i>
                    <span class="uppercase tracking-widest text-xs font-black">Nouveau Message</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="space-y-4 md:space-y-8">
        @if(session('success'))
        <div class="premium-card bg-green-50 border-green-100 p-4 flex items-center gap-3">
            <div class="w-8 h-8 bg-green-500 rounded-xl flex items-center justify-center text-white shrink-0">
                <i class="fas fa-check text-sm"></i>
            </div>
            <p class="text-green-700 font-bold text-sm">{{ session('success') }}</p>
        </div>
        @endif

        <div class="premium-card bg-white overflow-hidden">
            <div class="p-4 md:p-8 border-b border-slate-50 flex items-center gap-3">
                <div class="w-9 h-9 md:w-10 md:h-10 bg-blue-50 rounded-xl flex items-center justify-center text-blue-600">
                    <i class="fas fa-comments"></i>
                </div>
                <h3 class="text-base font-black text-slate-900 uppercase tracking-tight">Mes Conversations</h3>
            </div>

            @if($conversations->count() > 0)
            <div class="divide-y divide-slate-200">
                @foreach($conversations as $conversation)
                @php
                    $autreUtilisateur = $conversation->expediteur_id === Auth::id()
                        ? $conversation->destinataire
                        : $conversation->expediteur;
                    $estNonLu = !$conversation->lu && $conversation->destinataire_id === Auth::id();
                    $roleColor = 'bg-slate-200 text-slate-600';
                    if($autreUtilisateur->hasRole('admin')) $roleColor = 'bg-blue-100 text-blue-600';
                    elseif($autreUtilisateur->hasRole('proprietaire')) $roleColor = 'bg-emerald-100 text-emerald-600';
                    elseif($autreUtilisateur->hasRole('locataire')) $roleColor = 'bg-amber-100 text-amber-600';
                @endphp
                <a href="{{ route('messages.show', $autreUtilisateur->id) }}"
                   class="block p-4 md:p-6 {{ $estNonLu ? 'bg-blue-50/40' : '' }} hover:bg-slate-50 transition-all group">
                    <div class="flex items-start gap-3 md:gap-4">
                        <div class="w-11 h-11 md:w-14 md:h-14 bg-white rounded-xl md:rounded-2xl flex items-center justify-center text-blue-600 font-black text-lg md:text-xl shadow-sm border border-slate-100 group-hover:scale-105 transition-transform shrink-0">
                            {{ substr($autreUtilisateur->name, 0, 1) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex flex-col gap-1 mb-0.5">
                                <h4 class="font-black text-slate-900 uppercase tracking-tight text-sm {{ $estNonLu ? 'text-blue-700' : '' }} truncate">
                                    {{ $autreUtilisateur->name }}
                                </h4>
                                <div class="flex items-center gap-2 flex-wrap">
                                    @if($estNonLu)
                                    <span class="px-2 py-0.5 bg-red-500 text-white text-[8px] font-black uppercase tracking-widest rounded-full whitespace-nowrap">Nouveau</span>
                                    @endif
                                    <span class="text-[9px] font-bold uppercase tracking-widest px-2 py-0.5 rounded-md {{ $roleColor }} whitespace-nowrap">
                                        @if($autreUtilisateur->hasRole('admin')) Admin
                                        @elseif($autreUtilisateur->hasRole('proprietaire')) Propriétaire
                                        @elseif($autreUtilisateur->hasRole('locataire')) Locataire
                                        @endif
                                    </span>
                                </div>
                            </div>
                            <p class="text-xs font-black text-slate-700 {{ $estNonLu ? 'text-blue-900' : '' }} truncate">
                                {{ $conversation->objet }}
                            </p>
                            <p class="text-xs text-slate-500 mt-0.5 truncate hidden sm:block">
                                {{ Str::limit($conversation->contenu, 80) }}
                            </p>
                        </div>
                        <div class="flex flex-col items-end gap-2 shrink-0">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest whitespace-nowrap">
                                {{ $conversation->created_at->diffForHumans() }}
                            </p>
                            <div class="w-8 h-8 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-center text-slate-300 group-hover:text-blue-600 group-hover:border-blue-100 transition-all">
                                <i class="fas fa-chevron-right text-xs"></i>
                            </div>
                        </div>
                    </div>
                </a>
                @endforeach
            </div>
            @else
            <div class="py-12 md:py-20 text-center px-6">
                <div class="w-16 h-16 md:w-24 md:h-24 bg-slate-50 rounded-[2rem] flex items-center justify-center text-slate-200 mx-auto mb-5">
                    <i class="fas fa-envelope-open text-3xl md:text-4xl"></i>
                </div>
                <h3 class="text-lg md:text-xl font-black text-slate-900 uppercase tracking-tight">Aucune conversation</h3>
                <p class="text-slate-500 font-medium mt-2 mb-6 md:mb-8 text-sm">Vous n'avez pas encore de messages dans votre boîte de réception</p>
                <a href="{{ route('messages.create') }}" class="btn-premium bg-blue-600 text-white px-8 py-4 uppercase tracking-widest text-xs font-black hover:bg-blue-700 shadow-xl shadow-blue-100 transition-all inline-flex items-center gap-3">
                    <i class="fas fa-plus"></i>
                    Démarrer une discussion
                </a>
            </div>
            @endif
        </div>
    </div>
</x-app-layout>
