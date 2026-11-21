<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

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
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
        <a href="/" class="flex items-center gap-3 group">
            <div class="w-12 h-12 bg-blue-600 rounded-2xl flex items-center justify-center text-white shadow-xl transition-transform group-hover:scale-105">
                <i class="fas fa-home text-xl"></i>
            </div>
            <span class="text-3xl font-black text-blue-600 tracking-tighter uppercase">ONE.Immo</span>
        </a>

        <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
            {{ $slot }}
        </div>
    </div>
    @endif
</body>

</html>