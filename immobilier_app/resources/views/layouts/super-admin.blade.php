<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Super Admin — {{ config('app.name', 'Habitat Immobilier') }}</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        html, body { font-size: 14px; background-color: #F4F6FA; color: #1e293b; }
        .sa-card {
            background: #ffffff;
            border-radius: 1rem;
            border: 1px solid #e2e8f0;
            box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04);
        }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="font-sans antialiased bg-[#F4F6FA] min-h-screen" x-data="{ mobileMenu: false }">
    <div class="min-h-screen flex">
        {{-- Sidebar Super Admin --}}
        <aside class="hidden lg:flex lg:w-64 lg:flex-col lg:fixed lg:inset-y-0 bg-slate-900 text-slate-200 border-r border-slate-800 z-30">
            <div class="h-16 flex items-center justify-between px-5 border-b border-slate-800">
                <a href="{{ route('super-admin.dashboard') }}" class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white shadow-lg shadow-blue-900/40">
                        <i class="fas fa-shield-alt text-sm"></i>
                    </div>
                    <div>
                        <p class="text-xs font-black uppercase tracking-widest text-white leading-none">Super Admin</p>
                        <p class="text-[10px] text-slate-400 font-semibold mt-0.5">Habitat SaaS</p>
                    </div>
                </a>
            </div>

            @php
                $openTicketsCount = \App\Models\SupportTicket::whereIn('status', ['open', 'in_progress'])->count();
                $navLinks = [
                    ['route' => 'super-admin.dashboard', 'pattern' => 'super-admin.dashboard', 'icon' => 'fas fa-chart-line', 'label' => 'Vue d\'ensemble'],
                    ['route' => 'super-admin.agencies.index', 'pattern' => 'super-admin.agencies.*', 'icon' => 'fas fa-building', 'label' => 'Agences'],
                    ['route' => 'super-admin.users.index', 'pattern' => 'super-admin.users.*', 'icon' => 'fas fa-users-cog', 'label' => 'Utilisateurs & Support'],
                    ['route' => 'super-admin.announcements.index', 'pattern' => 'super-admin.announcements.*', 'icon' => 'fas fa-bullhorn', 'label' => 'Annonces globales'],
                    ['route' => 'super-admin.tickets.index', 'pattern' => 'super-admin.tickets.*', 'icon' => 'fas fa-life-ring', 'label' => 'Tickets Support', 'badge' => $openTicketsCount],
                    ['route' => 'super-admin.logs.index', 'pattern' => 'super-admin.logs.*', 'icon' => 'fas fa-history', 'label' => 'Journal d\'activité'],
                    ['route' => 'super-admin.settings.index', 'pattern' => 'super-admin.settings.*', 'icon' => 'fas fa-tools', 'label' => 'Maintenance'],
                ];
            @endphp

            <nav class="flex-1 px-3 py-5 space-y-1 overflow-y-auto">
                @foreach($navLinks as $item)
                    @php $active = request()->routeIs($item['pattern']); @endphp
                    <a href="{{ route($item['route']) }}"
                       class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all {{ $active ? 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white shadow-md shadow-blue-950/50' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        <span class="flex items-center gap-3">
                            <i class="{{ $item['icon'] }} w-4 text-center {{ $active ? 'text-white' : 'text-slate-400' }}"></i>
                            <span>{{ $item['label'] }}</span>
                        </span>
                        @if(!empty($item['badge']) && $item['badge'] > 0)
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-rose-500 text-white">{{ $item['badge'] }}</span>
                        @endif
                    </a>
                @endforeach
            </nav>

            <div class="p-4 border-t border-slate-800 space-y-2">
                <a href="{{ route('profile.edit') }}" class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold text-slate-300 hover:bg-slate-800 hover:text-white transition">
                    <i class="fas fa-user-circle text-slate-400"></i>
                    <span>Mon Profil</span>
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold text-rose-400 hover:bg-rose-500/10 transition">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Déconnexion</span>
                    </button>
                </form>
            </div>
        </aside>

        {{-- Contenu principal --}}
        <div class="flex-1 lg:pl-64 flex flex-col min-w-0">
            {{-- Topbar --}}
            <header class="h-16 bg-white border-b border-slate-200 px-4 sm:px-8 flex items-center justify-between sticky top-0 z-20">
                <div class="flex items-center gap-3">
                    <button @click="mobileMenu = !mobileMenu" class="lg:hidden w-9 h-9 rounded-xl bg-slate-100 flex items-center justify-center text-slate-700">
                        <i class="fas fa-bars"></i>
                    </button>
                    <div>
                        <h1 class="text-sm sm:text-base font-black text-slate-900 uppercase tracking-tight">
                            @yield('title', 'Panneau Super Admin')
                        </h1>
                        @hasSection('subtitle')
                            <p class="text-[11px] text-slate-500 font-medium">@yield('subtitle')</p>
                        @endif
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    @if(\App\Models\PlatformSetting::getValue('maintenance_mode') === '1')
                        <a href="{{ route('super-admin.settings.index') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-amber-100 text-amber-800 text-[10px] font-black uppercase tracking-widest">
                            <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                            Maintenance Active
                        </a>
                    @endif
                    <div class="hidden sm:flex items-center gap-2 px-3 py-1.5 rounded-full bg-slate-100 text-[11px] font-bold text-slate-700">
                        <i class="fas fa-shield-alt text-indigo-600"></i>
                        <span>{{ Auth::user()->name }}</span>
                    </div>
                </div>
            </header>

            {{-- Menu mobile --}}
            <div x-show="mobileMenu" x-cloak class="lg:hidden bg-slate-900 text-slate-200 px-4 py-3 space-y-1 border-b border-slate-800">
                @foreach($navLinks as $item)
                    <a href="{{ route($item['route']) }}" class="flex items-center justify-between px-3 py-2 rounded-lg text-xs font-bold {{ request()->routeIs($item['pattern']) ? 'bg-blue-600 text-white' : 'text-slate-300' }}">
                        <span><i class="{{ $item['icon'] }} mr-2"></i>{{ $item['label'] }}</span>
                        @if(!empty($item['badge']) && $item['badge'] > 0)
                            <span class="px-2 py-0.5 rounded-full text-[10px] bg-rose-500 text-white">{{ $item['badge'] }}</span>
                        @endif
                    </a>
                @endforeach
                <form method="POST" action="{{ route('logout') }}" class="pt-2 border-t border-slate-800">
                    @csrf
                    <button type="submit" class="w-full text-left px-3 py-2 text-xs font-bold text-rose-400">
                        <i class="fas fa-sign-out-alt mr-2"></i>Déconnexion
                    </button>
                </form>
            </div>

            <main class="flex-1 p-4 sm:p-8 space-y-6">
                @if(session('success'))
                    <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold flex items-center gap-3">
                        <i class="fas fa-check-circle text-emerald-600 text-base"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                @if(session('error'))
                    <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-bold flex items-center gap-3">
                        <i class="fas fa-exclamation-circle text-rose-600 text-base"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                @endif

                @if($errors->any())
                    <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs">
                        <p class="font-black mb-1">Veuillez corriger les erreurs suivantes :</p>
                        <ul class="list-disc pl-5 space-y-0.5">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')
            </main>

            <footer class="px-8 py-4 text-center text-[11px] text-slate-400 font-semibold border-t border-slate-200/60 bg-white">
                Super Admin • Développé par <span class="text-slate-600 font-black">Valdes Di Marco</span> • +2250554356019
            </footer>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
