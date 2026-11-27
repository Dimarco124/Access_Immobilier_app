@php
    $navItems = [];
    if (Auth::user()->hasRole('admin')) {
        $navItems = [
            ['route' => 'dashboard', 'pattern' => 'dashboard', 'icon' => 'fas fa-chart-pie', 'label' => 'Tableau de bord'],
            ['route' => 'proprietaires.index', 'pattern' => 'proprietaires.*', 'icon' => 'fas fa-user-tie', 'label' => 'Propriétaires'],
            ['route' => 'locataires.index', 'pattern' => 'locataires.*', 'icon' => 'fas fa-users', 'label' => 'Locataires'],
            ['route' => 'proprietes.index', 'pattern' => ['proprietes.*', 'familles-de-biens.*', 'biens.*'], 'icon' => 'fas fa-city', 'label' => 'Propriétés'],
            ['route' => 'contrats.index', 'pattern' => 'contrats.*', 'icon' => 'fas fa-file-contract', 'label' => 'Contrats'],
            ['route' => 'factures.index', 'pattern' => 'factures.*', 'icon' => 'fas fa-receipt', 'label' => 'Paiements'],
            ['route' => 'finance.index', 'pattern' => 'finance.*', 'icon' => 'fas fa-wallet', 'label' => 'Finance'],
            ['route' => 'visites.index', 'pattern' => 'visites.*', 'icon' => 'fas fa-calendar-alt', 'label' => 'Visites'],
            ['route' => 'messages.index', 'pattern' => 'messages.*', 'icon' => 'fas fa-envelope', 'label' => 'Messages'],
            ['route' => 'team.index', 'pattern' => 'team.*', 'icon' => 'fas fa-users-cog', 'label' => 'Gestion d\'Équipe'],
            ['route' => 'agence.parametres', 'pattern' => 'agence.parametres', 'icon' => 'fas fa-cog', 'label' => 'Réglages'],
            ['route' => 'support.index', 'pattern' => 'support.*', 'icon' => 'fas fa-life-ring', 'label' => 'Support Plateforme'],
        ];
    } elseif (Auth::user()->hasRole('team_member')) {
        $userModules = getUserModules();
        $navItems = [
            ['route' => 'dashboard', 'pattern' => 'dashboard', 'icon' => 'fas fa-chart-pie', 'label' => 'Tableau de bord'],
        ];
        if (in_array('proprietaires', $userModules, true)) {
            $navItems[] = ['route' => 'proprietaires.index', 'pattern' => 'proprietaires.*', 'icon' => 'fas fa-user-tie', 'label' => 'Propriétaires'];
        }
        if (in_array('commercial', $userModules, true)) {
            $navItems[] = ['route' => 'locataires.index', 'pattern' => 'locataires.*', 'icon' => 'fas fa-users', 'label' => 'Locataires'];
        }
        if (in_array('immobilier', $userModules, true)) {
            $navItems[] = ['route' => 'proprietes.index', 'pattern' => ['proprietes.*', 'familles-de-biens.*', 'biens.*'], 'icon' => 'fas fa-city', 'label' => 'Propriétés'];
        }
        if (in_array('commercial', $userModules, true)) {
            $navItems[] = ['route' => 'contrats.index', 'pattern' => 'contrats.*', 'icon' => 'fas fa-file-contract', 'label' => 'Contrats'];
        }
        if (in_array('finance', $userModules, true)) {
            $navItems[] = ['route' => 'factures.index', 'pattern' => 'factures.*', 'icon' => 'fas fa-receipt', 'label' => 'Paiements'];
            $navItems[] = ['route' => 'finance.index', 'pattern' => ['finance.*', 'agency.finance-report'], 'icon' => 'fas fa-wallet', 'label' => 'Finance'];
        }
        if (in_array('commercial', $userModules, true)) {
            $navItems[] = ['route' => 'visites.index', 'pattern' => 'visites.*', 'icon' => 'fas fa-calendar-alt', 'label' => 'Visites'];
        }
        if (in_array('relation_client', $userModules, true)) {
            $navItems[] = ['route' => 'messages.index', 'pattern' => 'messages.*', 'icon' => 'fas fa-envelope', 'label' => 'Messages'];
        }
    } elseif (Auth::user()->hasRole('proprietaire')) {
        $navItems = [
            ['route' => 'dashboard', 'pattern' => 'dashboard', 'icon' => 'fas fa-chart-pie', 'label' => 'Tableau de bord'],
            ['route' => 'proprietaires.mes-proprietes', 'pattern' => 'proprietaires.mes-proprietes', 'icon' => 'fas fa-building', 'label' => 'Mes Propriétés'],
            ['route' => 'owner.finance-report', 'pattern' => 'owner.finance-report', 'icon' => 'fas fa-chart-line', 'label' => 'Rapport Financier'],
            ['route' => 'messages.index', 'pattern' => 'messages.*', 'icon' => 'fas fa-envelope', 'label' => 'Messages'],
        ];
    } elseif (Auth::user()->hasRole('locataire')) {
        $navItems = [
            ['route' => 'dashboard', 'pattern' => 'dashboard', 'icon' => 'fas fa-chart-pie', 'label' => 'Tableau de bord'],
            ['route' => 'contrats.mes-contrats', 'pattern' => 'contrats.mes-contrats', 'icon' => 'fas fa-file-signature', 'label' => 'Mes Contrats'],
            ['route' => 'factures.mes-factures', 'pattern' => 'factures.mes-factures', 'icon' => 'fas fa-file-invoice-dollar', 'label' => 'Mes Factures'],
            ['route' => 'tenant.finance-report', 'pattern' => 'tenant.finance-report', 'icon' => 'fas fa-history', 'label' => 'Historique'],
            ['route' => 'messages.index', 'pattern' => 'messages.*', 'icon' => 'fas fa-envelope', 'label' => 'Messages'],
        ];
    }
@endphp

{{-- Sidebar fixe avec couleur bleu ciel doux (carte 500 000 / indigo-50 #EEF2FF) --}}
<aside
    id="main-sidebar"
    class="sidebar-container h-full z-30 shrink-0 overflow-hidden bg-[#eef2ff] border-r border-indigo-100 shadow-xs"
>
    {{-- Conteneur interne à largeur fixe (260px) pour éviter tout chevauchement de texte pendant la transition --}}
    <div class="w-[260px] h-full flex flex-col justify-between py-4 px-3 overflow-hidden">
        {{-- Brand Header --}}
        <div class="shrink-0 pb-3 mb-1 border-b border-indigo-200/60">
            <div class="flex items-center justify-between">
                <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5 group min-w-0">
                    <div class="w-9 h-9 rounded-xl bg-white text-indigo-600 flex items-center justify-center font-black text-base shadow-sm border border-indigo-100 shrink-0 group-hover:scale-105 transition-transform overflow-hidden p-1">
                        @if(Auth::user()->agence && Auth::user()->agence->logo_url)
                            <img src="{{ Auth::user()->agence->logo_url }}" alt="Logo" class="h-full w-full object-contain">
                        @else
                            <img src="{{ asset('images/logo-icon.svg') }}" alt="Habitat" class="h-full w-full object-contain">
                        @endif
                    </div>
                    <div class="min-w-0">
                        <span class="block font-black text-sm uppercase tracking-tight text-slate-900 truncate">{{ Auth::user()->agence->nom ?? 'Habitat Immo' }}</span>
                        <span class="inline-flex items-center gap-1.5 text-[9px] font-black uppercase tracking-widest text-indigo-600 mt-0.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-indigo-500"></span>
                            {{ Auth::user()->roles->first()->name ?? 'Utilisateur' }}
                        </span>
                    </div>
                </a>
                {{-- Bouton fermer la sidebar --}}
                <button @click="toggleSidebar()"
                    class="w-7 h-7 rounded-lg bg-white/70 hover:bg-white text-slate-500 hover:text-slate-800 flex items-center justify-center transition-colors shrink-0 shadow-xs"
                    title="Fermer le menu">
                    <i class="fas fa-times text-xs"></i>
                </button>
            </div>
        </div>

        {{-- Navigation Links --}}
        <div class="flex-1 overflow-y-auto scrollbar-none py-1 space-y-0.5 my-1">
            @foreach($navItems as $item)
                @php
                    if (is_array($item['pattern'])) {
                        $isActive = request()->routeIs(...$item['pattern']);
                    } else {
                        $isActive = request()->routeIs($item['pattern']);
                    }
                @endphp
                <a href="{{ route($item['route']) }}"
                    class="flex items-center gap-2.5 px-3 py-2 rounded-xl group transition-all duration-150 {{ $isActive ? 'bg-white text-indigo-600 font-extrabold shadow-sm border border-indigo-100' : 'text-slate-600 hover:text-indigo-600 hover:bg-white/80 font-bold' }}">
                    <div class="shrink-0 w-6 h-6 rounded-md flex items-center justify-center {{ $isActive ? 'text-indigo-600' : 'text-slate-400 group-hover:text-indigo-600' }}">
                        <i class="{{ $item['icon'] }} text-xs"></i>
                    </div>
                    <span class="text-xs uppercase tracking-wider truncate">{{ $item['label'] }}</span>
                    @if($isActive)
                        <i class="fas fa-chevron-right text-[9px] text-indigo-400 ml-auto"></i>
                    @endif
                </a>
            @endforeach
        </div>

        {{-- Footer --}}
        <div class="pt-2.5 mt-1 border-t border-indigo-200/60 shrink-0 space-y-2">
            <div class="flex items-center gap-2">
                <a href="{{ route('profile.edit') }}" class="flex-1 flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-bold text-slate-600 hover:text-indigo-600 hover:bg-white/80 transition-colors">
                    <i class="fas fa-user-circle text-slate-400 text-xs"></i>
                    <span>Mon Profil</span>
                </a>
                @if(Auth::user()->hasRole('admin'))
                    <a href="{{ route('agence.parametres') }}" class="flex items-center justify-center w-7 h-7 rounded-lg text-slate-600 hover:text-indigo-600 hover:bg-white/80 transition-colors" title="Paramètres de l'agence">
                        <i class="fas fa-cog text-slate-400 text-xs"></i>
                    </a>
                @endif
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center justify-center gap-2 py-2 px-4 rounded-xl bg-white text-indigo-600 hover:bg-red-50 hover:text-red-600 border border-indigo-100 font-black text-xs uppercase tracking-wider shadow-sm transition-colors">
                    <i class="fas fa-sign-out-alt text-xs"></i>
                    <span>Déconnexion</span>
                </button>
            </form>
        </div>
    </div>
</aside>
