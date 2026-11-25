<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Habitat Immobilier') }}</title>

    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

    <!-- Scripts & Styles (100% Offline Bundle) -->
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        :root {
            --primary: #0f172a;
            --primary-dark: #020617;
            --primary-light: #f8fafc;
            --accent: #334155;
            --success: #059669;
            --danger: #dc2626;
            --gray-bg: #f8fafc;
        }

        html,
        body {
            font-size: 14px;
            background-color: var(--gray-bg);
            color: #1e293b;
        }

        .premium-card {
            background: white;
            border-radius: 0.75rem;
            border: 1px solid #f1f5f9;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            transition: all 0.2s ease-in-out;
        }

        .premium-card:hover {
            border-color: #e2e8f0;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }

        .btn-premium {
            transition: all 0.2s ease-in-out;
            border-radius: 0.5rem;
            font-weight: 700;
            padding: 0.5rem 1rem;
            font-size: 0.75rem;
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }

        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px;
        }

        .scrollbar-none::-webkit-scrollbar {
            display: none !important;
            width: 0 !important;
            height: 0 !important;
        }

        .scrollbar-none {
            -ms-overflow-style: none !important;
            scrollbar-width: none !important;
        }

        /* =============================================
           SIDEBAR — transition fluide douce et lente
           ============================================= */
        .sidebar-container {
            width: 260px;
            overflow: hidden;
            flex-shrink: 0;
            transition: width 0.35s cubic-bezier(0.4, 0, 0.2, 1),
                        opacity 0.28s ease;
            will-change: width, opacity;
        }

        /* Quand la sidebar est fermée */
        html.sidebar-collapsed .sidebar-container {
            width: 0 !important;
            opacity: 0 !important;
            pointer-events: none !important;
        }

        /* ANTI-SAUT : aucune transition tant que la page se charge */
        html.no-nav-transition .sidebar-container,
        html.no-nav-transition #app-surface {
            transition: none !important;
        }

        /* =============================================
           LOADER DE NAVIGATION — spinner dans le contenu
           ============================================= */
        #nav-loader {
            position: absolute;
            inset: 0;
            background: rgba(248, 250, 252, 0.82);
            backdrop-filter: blur(1.5px);
            z-index: 999;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.2s ease;
        }
        #nav-loader.visible {
            opacity: 1;
            pointer-events: all;
        }
        .nav-spinner {
            width: 36px;
            height: 36px;
            border: 3.5px solid #e2e8f0;
            border-top-color: #0284c7;
            border-radius: 50%;
            animation: spin 0.75s linear infinite;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Global Input Styling - Compact */
        input:not([type="checkbox"]):not([type="radio"]),
        select,
        textarea {
            padding: 0.375rem 0.75rem !important;
            font-size: 0.8125rem !important;
            border-radius: 0.5rem !important;
            border: 1px solid #e2e8f0 !important;
            background-color: #ffffff !important;
        }

        /* Compact height uniquement sur md+ */
        @media (min-width: 768px) {

            input:not([type="checkbox"]):not([type="radio"]),
            select {
                height: 2.25rem !important;
            }
        }

        /* Sur mobile: hauteur naturelle confortable */
        @media (max-width: 767px) {

            input:not([type="checkbox"]):not([type="radio"]),
            select {
                height: 2.75rem !important;
                font-size: 1rem !important;
            }
        }

        textarea {
            height: auto !important;
            min-height: 4rem !important;
        }

        /* intl-tel-input override */
        .iti {
            width: 100% !important;
            display: block !important;
        }

        .iti__country-list {
            z-index: 100 !important;
        }

        .iti input.phone-input,
        .iti input[type="tel"] {
            padding-left: 96px !important;
        }

        /* Loader */
        #page-loader {
            position: fixed;
            inset: 0;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(2px);
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .loader-spinner {
            width: 32px;
            height: 32px;
            border: 3px solid #f1f5f9;
            border-bottom-color: #0f172a;
            border-radius: 50%;
            animation: rotation 0.8s linear infinite;
        }

        @keyframes rotation {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        /* Supprimer le nav-opening-drawer (ancien système drawer) */
        html.nav-opening-drawer #app-surface { transform: none !important; border-radius: 0 !important; }

        .fade-enter {
            opacity: 0;
            transform: translateY(4px);
        }

        .fade-enter-active {
            opacity: 1;
            transform: translateY(0);
            transition: opacity 0.3s ease-out, transform 0.3s ease-out;
        }

        [x-cloak] {
            display: none !important;
        }

        @media print {
            html, body {
                overflow: visible !important;
                height: auto !important;
                min-height: auto !important;
                background: #ffffff !important;
            }

            .h-screen, .w-screen, #app-surface, #page-content, main, main > div {
                position: static !important;
                overflow: visible !important;
                height: auto !important;
                min-height: 0 !important;
                max-height: none !important;
                transform: none !important;
                padding: 0 !important;
                margin: 0 !important;
                box-shadow: none !important;
                border: none !important;
                border-radius: 0 !important;
                background: transparent !important;
                width: 100% !important;
                max-width: 100% !important;
            }

            nav, header, aside, footer, #page-loader, .no-print, [class*="drawer"], [class*="navigation"] {
                display: none !important;
                visibility: hidden !important;
            }
        }
    </style>

    <script>
        // Anti-saut immédiat : appliquer l'état exact AVANT le premier affichage du body
        (function() {
            var isClosed = localStorage.getItem('sidebarOpen') === 'false';
            if (isClosed) {
                document.documentElement.classList.add('sidebar-collapsed');
            }
            // Bloque toute transition CSS pendant le chargement initial
            document.documentElement.classList.add('no-nav-transition');
        })();
    </script>
</head>

<body class="font-sans antialiased overflow-hidden"
    x-data="{
        sidebarOpen: localStorage.getItem('sidebarOpen') !== 'false',
        toggleSidebar() {
            this.sidebarOpen = !this.sidebarOpen;
            localStorage.setItem('sidebarOpen', this.sidebarOpen);
            if (this.sidebarOpen) {
                document.documentElement.classList.remove('sidebar-collapsed');
            } else {
                document.documentElement.classList.add('sidebar-collapsed');
            }
        }
    }"
>
    {{-- Layout principal : sidebar fixe à gauche + contenu à droite --}}
    <div class="h-screen w-screen flex overflow-hidden bg-[#f8fafc]">

        {{-- Sidebar --}}
        @include('layouts.navigation')

        {{-- Zone principale (prend tout l'espace restant) --}}
        <div id="app-surface" class="flex-1 flex flex-col min-w-0 h-full overflow-hidden bg-[#f8fafc] relative">

            {{-- Spinner de navigation (affiché pendant le chargement d'une page) --}}
            <div id="nav-loader">
                <div class="nav-spinner"></div>
            </div>

            <!-- Top Navigation / Header -->
            <nav
                class="bg-white border-b border-slate-100 shrink-0 h-16 flex items-center justify-between px-4 sm:px-6 lg:px-8 z-40">
                <div class="flex items-center gap-3">
                    <button @click="toggleSidebar()"
                        class="w-10 h-10 rounded-xl flex items-center justify-center text-slate-600 hover:text-[#0284c7] hover:bg-sky-50 transition-colors group"
                        :title="sidebarOpen ? 'Fermer le menu' : 'Ouvrir le menu'">
                        <i class="fas fa-bars-staggered text-lg text-slate-700 group-hover:text-[#0284c7]"></i>
                    </button>

                    <div class="flex items-center gap-2.5">
                        @if(Auth::user()->agence && Auth::user()->agence->logo_url)
                            <img src="{{ Auth::user()->agence->logo_url }}" alt="{{ Auth::user()->agence->nom }}" class="w-8 h-8 rounded-lg object-contain bg-white p-0.5 border border-slate-200 shadow-xs">
                        @else
                            <div class="w-8 h-8 rounded-lg bg-blue-600 text-white flex items-center justify-center font-black text-xs shadow-xs">
                                <i class="fas fa-building text-xs"></i>
                            </div>
                        @endif
                        <span class="font-black text-sm text-slate-900 tracking-tight uppercase">
                            {{ Auth::user()->agence->nom ?? 'Habitat Immobilier' }}
                        </span>
                        @if(isset($header))
                            <div class="hidden md:flex items-center text-sm font-medium text-slate-400">
                                <i class="fas fa-chevron-right mx-2 text-[10px]"></i>
                                <span class="text-slate-500">Espace de travail</span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Right Side Actions -->
                <div class="flex items-center gap-3">
                    <div
                        class="hidden sm:flex items-center gap-3 px-3 py-1.5 bg-slate-50 rounded-lg border border-slate-100">
                        <div class="w-1.5 h-1.5 rounded-full bg-emerald-500"></div>
                        <span
                            class="text-[9px] font-black text-slate-500 uppercase tracking-widest">{{ Auth::user()->roles->first()->name ?? 'Utilisateur' }}</span>
                    </div>

                    <!-- Notifications Dropdown -->
                    <x-dropdown align="right" width="w-[calc(100vw-2rem)] sm:w-[420px]">
                        <x-slot name="trigger">
                            <button
                                class="relative flex items-center justify-center w-9 h-9 rounded-xl hover:bg-slate-50 transition-all group">
                                <i class="fas fa-bell text-slate-500 group-hover:text-blue-600"></i>
                                @php
                                    $unreadCount = Auth::user()->unreadAppNotifications()->count();
                                @endphp
                                @if($unreadCount > 0)
                                    <span
                                        class="absolute -top-1 -right-1 bg-red-500 text-white text-[9px] font-black w-5 h-5 flex items-center justify-center rounded-full">
                                        {{ $unreadCount }}
                                    </span>
                                @endif
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between">
                                <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Notifications
                                </p>
                                @if($unreadCount > 0)
                                    <button id="mark-all-read"
                                        class="text-[10px] font-black text-blue-600 hover:text-blue-800 uppercase tracking-widest">
                                        Marquer toutes comme lues
                                    </button>
                                @endif
                            </div>
                            <div class="max-h-80 overflow-y-auto">
                                @forelse(Auth::user()->appNotifications()->latest()->take(10)->get() as $notification)
                                                            <a href="{{ $notification->read ? ($notification->action_url ?? '#') : '#' }}"
                                                                onclick="{{ !$notification->read ? "event.preventDefault(); markAsRead(" . $notification->id . ", '" . ($notification->action_url ?? '') . "');" : '' }}"
                                                                class="block px-4 py-3 border-b border-slate-50 hover:bg-slate-50 transition-all relative
                                                                    {{ !$notification->read ? 'bg-blue-50/60 border-l-2 border-l-blue-500' : '' }}">
                                                                <div class="flex items-start gap-3">
                                                                    <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0
                                                                        @if($notification->type === 'success') bg-emerald-100 text-emerald-600
                                                                        @elseif($notification->type === 'warning') bg-amber-100 text-amber-600
                                                                        @elseif($notification->type === 'danger') bg-red-100 text-red-600
                                                                        @else bg-blue-100 text-blue-600 @endif">
                                                                        <i class="fas fa-{{
                                    $notification->type === 'success' ? 'check-circle' :
                                    ($notification->type === 'warning' ? 'exclamation-triangle' :
                                        ($notification->type === 'danger' ? 'times-circle' : 'info-circle'))
                                                                        }} text-xs"></i>
                                                                    </div>
                                                                    <div class="flex-1 min-w-0">
                                                                        <div class="flex items-center gap-2">
                                                                            <p
                                                                                class="text-xs font-bold {{ !$notification->read ? 'text-blue-800' : 'text-slate-800' }}">
                                                                                {{ $notification->title }}</p>
                                                                            @if(!$notification->read)
                                                                                <span class="w-2 h-2 bg-blue-600 rounded-full animate-pulse"></span>
                                                                            @endif
                                                                        </div>
                                                                        <p class="text-xs text-slate-500 mt-1 line-clamp-1">
                                                                            {{ $notification->message }}</p>
                                                                        <p class="text-[10px] text-slate-400 mt-1">
                                                                            {{ $notification->created_at->diffForHumans() }}</p>
                                                                    </div>
                                                                </div>
                                                            </a>
                                @empty
                                    <div class="px-4 py-6 text-center">
                                        <i class="fas fa-inbox text-2xl text-slate-300 mb-2"></i>
                                        <p class="text-xs text-slate-500">Aucune notification</p>
                                    </div>
                                @endforelse
                            </div>
                            <div class="px-4 py-3 border-t border-slate-100 text-center">
                                <a href="{{ route('notifications.index') }}"
                                    class="text-[10px] font-black text-blue-600 hover:text-blue-800 uppercase tracking-widest">
                                    Voir toutes les notifications
                                </a>
                            </div>
                        </x-slot>
                    </x-dropdown>

                    <x-dropdown align="right" width="48">
                        <x-slot name="trigger">
                            <button
                                class="flex items-center gap-3 p-1 rounded-xl hover:bg-slate-50 transition-all group">
                                <div
                                    class="w-9 h-9 rounded-xl bg-blue-600 flex items-center justify-center text-white shadow-lg shadow-blue-200 group-hover:scale-105 transition-transform">
                                    <i class="fas fa-user text-sm"></i>
                                </div>
                                <div class="hidden md:block text-left">
                                    <p class="text-xs font-black text-slate-800 leading-none">{{ Auth::user()->name }}
                                    </p>
                                    <p class="text-[10px] font-bold text-slate-400 mt-1 uppercase tracking-tighter">Mon
                                        Compte</p>
                                </div>
                                <i
                                    class="fas fa-chevron-down text-[10px] text-slate-300 group-hover:text-blue-600 transition-colors ml-1"></i>
                            </button>
                        </x-slot>

                        <x-slot name="content">
                            <div class="px-4 py-3 border-b border-slate-50">
                                <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Connecté en
                                    tant que</p>
                                <p class="text-sm font-bold text-slate-700 truncate mt-1">{{ Auth::user()->email }}</p>
                            </div>
                            <x-dropdown-link :href="route('profile.edit')"
                                class="font-bold text-slate-600 hover:text-blue-600 flex items-center gap-2">
                                <i class="fas fa-user-circle w-4"></i> {{ __('Mon Profil') }}
                            </x-dropdown-link>

                            @if(Auth::user()->hasRole('admin'))
                                <x-dropdown-link :href="route('agence.parametres')"
                                    class="font-bold text-slate-600 hover:text-blue-600 flex items-center gap-2">
                                    <i class="fas fa-cog w-4"></i> {{ __('Configuration') }}
                                </x-dropdown-link>
                            @endif

                            <div class="border-t border-slate-50"></div>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault(); this.closest('form').submit();"
                                    class="font-bold text-red-500 hover:bg-red-50 flex items-center gap-2">
                                    <i class="fas fa-sign-out-alt w-4"></i> {{ __('Déconnexion') }}
                                </x-dropdown-link>
                            </form>
                        </x-slot>
                    </x-dropdown>
                </div>
            </nav>

            <div id="page-content" class="flex-1 overflow-y-auto page-enter-animation">
                <!-- Page Heading -->
                @if (isset($header))
                    <header class="bg-white border-b border-slate-100 py-4 md:py-6">
                        <div class="w-full px-4 sm:px-6 lg:px-8">
                            {{ $header }}
                        </div>
                    </header>
                @endif

                <!-- Page Content -->
                <main class="py-4 md:py-8">
                    <div class="w-full px-3 sm:px-6 lg:px-8">
                        {{ $slot }}
                    </div>
                </main>

                {{-- Footer --}}
            </div>
        </div>
    </div>

    {{-- markAsRead doit être global (avant DOMContentLoaded) pour être accessible dans les onclick inline --}}
    <script>
        window.markAsRead = function (id, actionUrl) {
            fetch("{{ url('notifications') }}/" + id + '/mark-as-read', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({})
            }).then(() => {
                if (actionUrl && actionUrl !== '') {
                    window.location.href = actionUrl;
                } else {
                    window.location.reload();
                }
            });
        };
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Phone input setup with dynamic padding and anti-duplicate guard
            document.querySelectorAll('.phone-input').forEach(input => {
                if (input.dataset.itiInitialized) return;
                input.dataset.itiInitialized = 'true';

                const iti = window.intlTelInput(input, {
                    initialCountry: "ci",
                    preferredCountries: ["ci", "fr", "sn", "bf"],
                    separateDialCode: true,
                    utilsScript: "https://cdn.jsdelivr.net/npm/intl-tel-input@23.0.10/build/js/utils.js",
                });

                const syncPadding = () => {
                    const itiEl = input.closest('.iti');
                    if (itiEl) {
                        const selectedCountry = itiEl.querySelector('.iti__selected-country');
                        if (selectedCountry) {
                            const width = selectedCountry.offsetWidth || 84;
                            input.style.setProperty('padding-left', (width + 12) + 'px', 'important');
                        }
                    }
                };

                syncPadding();
                setTimeout(syncPadding, 60);
                setTimeout(syncPadding, 250);
                input.addEventListener('countrychange', () => {
                    syncPadding();
                    setTimeout(syncPadding, 50);
                });
            });

            // Auto-teleport all modals/popups to document.body so they never get clipped by scrolling or transformed containers
            const teleportAllModals = () => {
                document.querySelectorAll('[id*="modal"], [id*="Modal"]').forEach(modal => {
                    if (modal.closest('aside') || modal.id === 'page-progress-bar' || modal.id === 'app-surface') return;
                    if (modal.classList.contains('fixed') && modal.parentElement !== document.body) {
                        document.body.appendChild(modal);
                    }
                });
            };
            teleportAllModals();
            setTimeout(teleportAllModals, 100);
            setTimeout(teleportAllModals, 400);

            // Prevent double form submission + afficher le spinner
            document.querySelectorAll('form').forEach(form => {
                form.addEventListener('submit', function (e) {
                    if (this.dataset.submitted) {
                        e.preventDefault();
                        return;
                    }
                    this.dataset.submitted = 'true';

                    // Afficher le spinner de navigation
                    showNavLoader();

                    // Disable all buttons in this form
                    const buttons = this.querySelectorAll('button[type="submit"], input[type="submit"]');
                    const originalStates = [];

                    buttons.forEach((btn, index) => {
                        originalStates[index] = {
                            disabled: btn.disabled,
                            innerHTML: btn.innerHTML,
                            classes: btn.className
                        };
                        btn.disabled = true;
                        btn.classList.add('opacity-50', 'cursor-not-allowed');
                        if (btn.dataset.loadingText) {
                            btn.textContent = btn.dataset.loadingText;
                        } else {
                            btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Chargement...';
                        }
                    });

                    // Reset after 5 seconds (fallback)
                    setTimeout(() => {
                        this.dataset.submitted = '';
                        hideNavLoader();
                        buttons.forEach((btn, index) => {
                            if (originalStates[index]) {
                                btn.disabled = originalStates[index].disabled;
                                btn.innerHTML = originalStates[index].innerHTML;
                                btn.className = originalStates[index].classes;
                            }
                        });
                    }, 5000);
                });
            });

            // ============================================================
            // ANTI-SAUT : Réactiver les transitions après le rendu initial
            // ============================================================
            requestAnimationFrame(() => {
                setTimeout(() => {
                    document.documentElement.classList.remove('no-nav-transition');
                }, 60);
            });

            // ============================================================
            // SPINNER DE NAVIGATION — affiché dans #app-surface
            // ============================================================
            const navLoader = document.getElementById('nav-loader');

            function showNavLoader() {
                if (navLoader) navLoader.classList.add('visible');
            }
            function hideNavLoader() {
                if (navLoader) navLoader.classList.remove('visible');
            }

            // Masquer le loader quand la page est prête (bfcache aussi)
            window.addEventListener('pageshow', hideNavLoader);

            // Afficher le loader sur tout clic de lien interne
            document.querySelectorAll('a[href]').forEach(link => {
                link.addEventListener('click', function (e) {
                    const href = this.getAttribute('href');
                    if (!href) return;
                    if (href.startsWith('#') || href.startsWith('javascript:') || href.startsWith('mailto:') || href.startsWith('tel:')) return;
                    if (this.target === '_blank' || this.hasAttribute('download')) return;
                    if (e.metaKey || e.ctrlKey || e.shiftKey || e.altKey || e.button !== 0) return;
                    try {
                        const targetUrl = new URL(this.href, window.location.href);
                        if (targetUrl.origin !== window.location.origin) return;
                        if (targetUrl.href === window.location.href) return;
                    } catch (err) { return; }

                    showNavLoader();
                });
            });

            // Notifications Mark as Read
            const markAllReadBtn = document.getElementById('mark-all-read');
            if (markAllReadBtn) {
                markAllReadBtn.addEventListener('click', function (e) {
                    e.preventDefault();
                    fetch('{{ route('notifications.mark-all-as-read') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({})
                    }).then(() => {
                        window.location.reload();
                    });
                });
            }
        });
    </script>
    @stack('scripts')
</body>

</html>