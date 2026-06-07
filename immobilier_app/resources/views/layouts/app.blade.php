
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'gImmo') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
        
        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <style>
            :root {
                --primary: #3b82f6;
                --primary-dark: #2563eb;
                --primary-light: #93c5fd;
                --secondary: #10b981;
                --secondary-dark: #059669;
                --gray-50: #f9fafb;
                --gray-100: #f3f4f6;
                --gray-200: #e5e7eb;
                --gray-700: #374151;
                --gray-900: #111827;
            }
            
            .glass-effect {
                background: rgba(255, 255, 255, 0.1);
                backdrop-filter: blur(10px);
                -webkit-backdrop-filter: blur(10px);
                border: 1px solid rgba(255, 255, 255, 0.2);
            }
            
            .shadow-soft {
                box-shadow: 0 2px 15px -3px rgba(0, 0, 0, 0.07), 0 10px 20px -2px rgba(0, 0, 0, 0.04);
            }
            
            .shadow-hard {
                box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 20px -2px rgba(0, 0, 0, 0.08);
            }
            
            .gradient-bg {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            }
            
            .hover-lift {
                transition: all 0.3s ease;
            }
            
            .hover-lift:hover {
                transform: translateY(-2px);
                box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            }
            
            .card-hover {
                transition: all 0.3s ease;
                border: 1px solid var(--gray-200);
            }
            
            .card-hover:hover {
                border-color: var(--primary);
                transform: translateY(-2px);
            }
        </style>
    </head>
    <body class="font-sans antialiased bg-gray-50">
        <div class="min-h-screen">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @if (isset($header))
                <header class="bg-gradient-to-r from-blue-600 to-indigo-700 text-white shadow-hard">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <main class="py-8">
                {{ $slot }}
            </main>

            <!-- Footer -->
            <footer class="bg-gray-900 text-white mt-auto">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    <div class="flex flex-col md:flex-row justify-between items-center">
                        <div class="mb-4 md:mb-0">
                            <div class="flex items-center space-x-2">
                                @if(Auth::user()->agence && Auth::user()->agence->logo)
                                <img src="{{ Storage::url(Auth::user()->agence->logo) }}" alt="Logo" class="block h-8 w-auto">
                                @else
                                <x-application-logo class="block h-8 w-auto fill-current text-white" />
                                @endif
                                <span class="font-bold text-xl">{{ Auth::user()->agence->nom ?? 'gImmo' }}</span>
                            </div>
                            <p class="text-gray-400 text-sm mt-2">Solution complète de gestion immobilière</p>
                        </div>
                        <div class="flex space-x-6">
                            <span class="text-gray-400">© {{ date('Y') }} Tous droits réservés</span>
                            <span class="text-gray-400">v1.0.0</span>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
    </body>
</html>
