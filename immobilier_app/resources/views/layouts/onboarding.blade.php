<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Configuration — {{ config('app.name', 'Habitat Immobilier') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        html, body { font-size: 14px; background-color: #F4F6FA; color: #1e293b; }

        .premium-card {
            background: white;
            border-radius: 0.875rem;
            border: 1px solid #f1f5f9;
            box-shadow: 0 1px 3px 0 rgba(0,0,0,0.05), 0 1px 2px -1px rgba(0,0,0,0.05);
            transition: all 0.2s ease-in-out;
        }
        .premium-card:hover { border-color: #e2e8f0; box-shadow: 0 4px 12px -2px rgba(0,0,0,0.08); }

        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #f1f5f9; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }

        input:not([type="checkbox"]):not([type="radio"]), select, textarea {
            padding: 0.5rem 0.875rem !important;
            font-size: 0.8125rem !important;
            border-radius: 0.625rem !important;
            border: 1.5px solid #e2e8f0 !important;
            background-color: #F4F6FA !important;
            transition: border-color .2s, box-shadow .2s;
        }
        input:not([type="checkbox"]):not([type="radio"]):focus, select:focus, textarea:focus {
            border-color: #3b82f6 !important;
            box-shadow: 0 0 0 3px rgba(59,130,246,.12) !important;
            background-color: white !important;
            outline: none !important;
        }
        @media (min-width:768px){ input:not([type="checkbox"]):not([type="radio"]), select { height:2.5rem !important; } }
        @media (max-width:767px){ input:not([type="checkbox"]):not([type="radio"]), select { height:2.75rem !important; font-size:1rem !important; } }
        textarea { height:auto !important; min-height:4rem !important; }

        /* Step indicator */
        .step-dot { width:2rem; height:2rem; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:.7rem; font-weight:900; transition:all .3s; }
        .step-dot.done { background: linear-gradient(135deg,#3b82f6,#6366f1); color:white; box-shadow:0 4px 12px rgba(99,102,241,.35); }
        .step-dot.active { background: white; border:2px solid #3b82f6; color:#3b82f6; box-shadow:0 0 0 4px rgba(59,130,246,.15); }
        .step-dot.pending { background:#f1f5f9; border:2px solid #e2e8f0; color:#94a3b8; }
        .step-line { flex:1; height:2px; background:#e2e8f0; border-radius:2px; }
        .step-line.done { background:linear-gradient(90deg,#3b82f6,#6366f1); }

        [x-cloak] { display:none !important; }
    </style>
</head>

<body class="font-sans antialiased bg-[#F4F6FA]">

    {{-- ===== NAVBAR ===== --}}
    <nav class="fixed top-0 inset-x-0 z-50 h-16 bg-white/90 backdrop-blur-md border-b border-slate-100 flex items-center justify-between px-4 sm:px-8">
        <a href="{{ url('/') }}" class="flex items-center gap-2.5 transition-transform hover:scale-105">
            <img src="{{ asset('images/logo.svg') }}" alt="Habitat Immobilier" class="h-9 w-auto">
        </a>
        <div class="flex items-center gap-3">
            <div class="hidden sm:flex items-center gap-2 px-3 py-1.5 rounded-full bg-[#F4F6FA] border border-slate-200 text-[10px] font-black text-slate-500 uppercase tracking-widest">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                {{ Auth::user()->roles->first()->name ?? 'Admin' }}
            </div>
            <span class="hidden sm:block text-[11px] font-semibold text-slate-500">{{ Auth::user()->name }}</span>
        </div>
    </nav>

    {{-- ===== MAIN ===== --}}
    <div class="min-h-screen pt-16 pb-12 flex flex-col">
        {{-- Header zone (step bar + title) --}}
        @if(isset($header))
        <div class="bg-white border-b border-slate-100 shadow-sm">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 py-5">
                {{ $header }}
            </div>
        </div>
        @endif

        {{-- Content --}}
        <div class="flex-1 py-8 px-4 sm:px-6">
            {{ $slot }}
        </div>

        {{-- Footer --}}
        <footer class="text-center py-4">
            <p class="text-[10px] font-semibold text-slate-400">
                Développé par <span class="font-black text-slate-500">Valdes Di Marco</span>
                <span class="mx-1">•</span> +2250554356019
            </p>
        </footer>
    </div>

</body>
</html>