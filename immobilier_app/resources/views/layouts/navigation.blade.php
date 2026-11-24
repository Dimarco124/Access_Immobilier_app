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
        ];
    } elseif (Auth::user()->hasRole('team_member')) {
        $userModules = getUserModules();
        $navItems = [
            ['route' => 'dashboard', 'pattern' => 'dashboard', 'icon' => 'fas fa-chart-pie', 'label' => 'Tableau de bord'],
        ];
        
        // MODULE PROPRIÉTAIRES
        if (in_array('proprietaires', $userModules, true)) {
            $navItems[] = ['route' => 'proprietaires.index', 'pattern' => 'proprietaires.*', 'icon' => 'fas fa-user-tie', 'label' => 'Propriétaires'];
        }
        
        // MODULE COMMERCIAL
        if (in_array('commercial', $userModules, true)) {
            $navItems[] = ['route' => 'locataires.index', 'pattern' => 'locataires.*', 'icon' => 'fas fa-users', 'label' => 'Locataires'];
        }
        
        // MODULE IMMOBILIER
        if (in_array('immobilier', $userModules, true)) {
            $navItems[] = ['route' => 'proprietes.index', 'pattern' => ['proprietes.*', 'familles-de-biens.*', 'biens.*'], 'icon' => 'fas fa-city', 'label' => 'Propriétés'];
        }
        
        // MODULE COMMERCIAL - Contrats
        if (in_array('commercial', $userModules, true)) {
            $navItems[] = ['route' => 'contrats.index', 'pattern' => 'contrats.*', 'icon' => 'fas fa-file-contract', 'label' => 'Contrats'];
        }
        
        // MODULE FINANCE
        if (in_array('finance', $userModules, true)) {
            $navItems[] = ['route' => 'factures.index', 'pattern' => 'factures.*', 'icon' => 'fas fa-receipt', 'label' => 'Paiements'];
            $navItems[] = ['route' => 'finance.index', 'pattern' => ['finance.*', 'agency.finance-report'], 'icon' => 'fas fa-wallet', 'label' => 'Finance'];
        }
        
        // MODULE COMMERCIAL - Visites
        if (in_array('commercial', $userModules, true)) {
            $navItems[] = ['route' => 'visites.index', 'pattern' => 'visites.*', 'icon' => 'fas fa-calendar-alt', 'label' => 'Visites'];
        }
        
        // MODULE RELATION CLIENT
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

<!-- Drawer Menu (Revealed behind shifted app on #3B5BDB) -->
<aside 
    x-cloak
    class="w-[260px] sm:w-[280px] md:w-[300px] h-full z-10 flex flex-col justify-between py-3.5 sm:py-4 px-3 sm:px-4 shrink-0 overflow-hidden text-white bg-[#3B5BDB]"
>
    <!-- Brand Header -->
    <div class="shrink-0 pb-3 mb-1 border-b border-white/20">
        <div class="flex items-center justify-between">
            <a href="{{ route('dashboard') }}" @click="sessionStorage.setItem('menuOpenAfterNav', 'true')" class="flex items-center gap-2.5 group min-w-0">
                <div class="w-9 h-9 rounded-xl bg-white text-[#3B5BDB] flex items-center justify-center font-black text-base shadow-sm shrink-0 group-hover:scale-105 transition-transform overflow-hidden p-1">
                    @if(Auth::user()->agence && Auth::user()->agence->logo)
                        <img src="{{ Storage::url(Auth::user()->agence->logo) }}" alt="Logo" class="h-full w-full object-contain">
                    @else
                        <img src="{{ asset('images/logo-icon.svg') }}" alt="Habitat" class="h-full w-full object-contain">
                    @endif
                </div>
                <div class="min-w-0">
                    <span class="block font-black text-xs sm:text-sm uppercase tracking-tight text-white truncate">{{ Auth::user()->agence->nom ?? 'Habitat Immo' }}</span>
                    <span class="inline-flex items-center gap-1.5 text-[9px] font-black uppercase tracking-widest text-white/75 mt-0.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-300"></span>
                        {{ Auth::user()->roles->first()->name ?? 'Utilisateur' }}
                    </span>
                </div>
            </a>
            <button @click="menuOpen = false; sessionStorage.removeItem('menuOpenAfterNav')" class="w-7 h-7 rounded-lg bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition-all shrink-0" title="Fermer le menu">
                <i class="fas fa-times text-xs"></i>
            </button>
        </div>
    </div>

    <!-- Navigation Links List (Fits cleanly without scrollbar) -->
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
                @click="sessionStorage.setItem('menuOpenAfterNav', 'true')"
                class="flex items-center gap-2.5 px-3 py-1.5 sm:py-2 rounded-xl transition-all duration-150 group {{ $isActive ? 'bg-white text-[#3B5BDB] font-extrabold shadow-sm' : 'text-white/85 hover:text-white hover:bg-white/10 font-bold' }}">
                <div class="shrink-0 w-6 h-6 rounded-md flex items-center justify-center {{ $isActive ? 'text-[#3B5BDB]' : 'text-white/80 group-hover:text-white' }} transition-colors">
                    <i class="{{ $item['icon'] }} text-xs"></i>
                </div>
                <span class="text-[11px] sm:text-xs uppercase tracking-wider truncate">{{ $item['label'] }}</span>
                @if($isActive)
                    <i class="fas fa-chevron-right text-[9px] text-[#3B5BDB]/70 ml-auto"></i>
                @endif
            </a>
        @endforeach
    </div>

    <!-- Drawer Footer Actions -->
    <div class="pt-2.5 mt-1 border-t border-white/20 shrink-0 space-y-2">
        <div class="flex items-center gap-2">
            <a href="{{ route('profile.edit') }}" @click="sessionStorage.setItem('menuOpenAfterNav', 'true')" class="flex-1 flex items-center gap-2 px-3 py-1.5 rounded-lg text-xs font-bold text-white/90 hover:text-white hover:bg-white/10 transition-colors">
                <i class="fas fa-user-circle text-white/80 text-xs"></i>
                <span>Mon Profil</span>
            </a>
            @if(Auth::user()->hasRole('admin'))
                <a href="{{ route('agence.parametres') }}" @click="sessionStorage.setItem('menuOpenAfterNav', 'true')" class="flex items-center justify-center w-7 h-7 rounded-lg text-white/90 hover:text-white hover:bg-white/10 transition-colors" title="Paramètres de l'agence">
                    <i class="fas fa-cog text-white/80 text-xs"></i>
                </a>
            @endif
        </div>

        <!-- Logout Action Button -->
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="w-full flex items-center justify-center gap-2 py-2 px-4 rounded-xl bg-white text-[#3B5BDB] hover:bg-white/95 font-black text-xs uppercase tracking-wider shadow-sm transition-all hover:scale-[1.01] active:scale-95">
                <i class="fas fa-sign-out-alt text-xs"></i>
                <span>Déconnexion</span>
            </button>
        </form>
    </div>
</aside>