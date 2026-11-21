<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'ONE.Immo') }}</title>

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

        @media (min-width: 768px) {

            input:not([type="checkbox"]):not([type="radio"]),
            select {
                height: 2.25rem !important;
            }
        }

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

        .iti {
            width: 100% !important;
        }

        .iti__country-list {
            z-index: 100 !important;
        }

        .iti input.phone-input {
            padding-left: 96px !important;
        }

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
    </style>
</head>

<body class="font-sans antialiased">
    <div class="min-h-screen flex flex-col bg-[#f8fafc]">
        <nav
            class="bg-white border-b border-slate-100 shrink-0 h-16 flex items-center justify-between px-4 sm:px-6 lg:px-8">
            <a href="{{ route('dashboard') }}"
                class="text-slate-900 font-black uppercase tracking-widest text-sm">ONE.Immo</a>
            <div class="hidden sm:flex items-center gap-3 px-3 py-1.5 bg-slate-50 rounded-lg border border-slate-100">
                <div class="w-1.5 h-1.5 rounded-full bg-emerald-500"></div>
                <span
                    class="text-[9px] font-black text-slate-500 uppercase tracking-widest">{{ Auth::user()->roles->first()->name ?? 'Utilisateur' }}</span>
            </div>
        </nav>

        <main class="flex-1 overflow-auto">
            @if(isset($header))
                <div class="border-b border-slate-100 px-4 sm:px-6 lg:px-8 py-4 bg-[#f8fafc]">
                    {{ $header }}
                </div>
            @endif

            <div class="px-4 sm:px-6 lg:px-8 py-8">
                {{ $slot }}
            </div>
        </main>
    </div>
</body>

</html>