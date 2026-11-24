<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Habitat Immobilier') }}</title>

    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

    <style>
        .iti {
            width: 100%;
        }

        .iti input.phone-input {
            padding-left: 96px !important;
        }
    </style>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans text-gray-900 antialiased">
    @if(isset($fullWidth) && $fullWidth)
    {{ $slot }}
    @else
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-50 relative px-4">
        <!-- Bouton Retour à l'accueil -->
        <div class="absolute top-6 left-6">
            <a href="{{ url('/') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white border border-gray-200 text-xs font-bold text-gray-700 hover:text-blue-600 hover:border-blue-200 shadow-sm transition-all hover:-translate-x-0.5">
                <i class="fas fa-arrow-left text-[11px]"></i>
                <span>Retour à l'accueil</span>
            </a>
        </div>

        <a href="/" class="flex items-center gap-3 group mb-2 mt-8 sm:mt-0">
            <img src="{{ asset('images/logo.svg') }}" alt="Habitat Immobilier" class="h-12 w-auto transition-transform group-hover:scale-105">
        </a>

        <div class="w-full sm:max-w-md mt-4 px-8 py-8 bg-white shadow-xl shadow-blue-100/50 overflow-hidden sm:rounded-2xl border border-gray-100">
            {{ $slot }}
        </div>
    </div>
    @endif
</body>

</html>