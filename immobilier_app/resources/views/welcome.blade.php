<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>gImmo - Solution de Gestion Immobilière Moderne</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased bg-gray-50">
    <div class="relative min-h-screen">
        <!-- Navigation -->
        <nav class="bg-white shadow-sm py-4">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex justify-between items-center">
                <div class="flex items-center gap-2">
                    <span class="text-2xl font-black text-blue-600">gImmo</span>
                </div>
                <div class="flex gap-4">
                    @auth
                        <a href="{{ url('/dashboard') }}" class="text-gray-700 font-semibold hover:text-blue-600 transition">Tableau de bord</a>
                    @else
                        <a href="{{ route('login') }}" class="text-gray-700 font-semibold hover:text-blue-600 transition pt-2">Connexion</a>
                        <a href="{{ route('register.agency') }}" class="bg-blue-600 text-white px-6 py-2 rounded-full font-bold hover:bg-blue-700 transition shadow-md">Inscrire mon Agence</a>
                    @endauth
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <main>
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 lg:py-32">
                <div class="grid lg:grid-cols-2 gap-12 items-center">
                    <div>
                        <h1 class="text-5xl lg:text-6xl font-black text-gray-900 leading-tight mb-6">
                            Gérez votre parc immobilier <span class="text-blue-600">sans effort.</span>
                        </h1>
                        <p class="text-xl text-gray-600 mb-8 leading-relaxed">
                            Automatisez vos baux, suivez les paiements en temps réel et simplifiez la communication avec vos locataires. La solution tout-en-un pour les agences modernes.
                        </p>
                        <div class="flex flex-col sm:flex-row gap-4">
                            <a href="{{ route('register.agency') }}" class="bg-blue-600 text-white px-8 py-4 rounded-xl font-bold text-lg hover:bg-blue-700 transition text-center shadow-lg">
                                Commencer maintenant
                            </a>
                            <a href="#features" class="bg-white text-gray-800 px-8 py-4 rounded-xl font-bold text-lg border-2 border-gray-100 hover:border-blue-100 transition text-center">
                                Découvrir les fonctions
                            </a>
                        </div>
                        <div class="mt-10 flex items-center gap-4">
                            <div class="flex -space-x-2">
                                <img class="w-10 h-10 rounded-full border-2 border-white" src="https://ui-avatars.com/api/?name=User+1&background=random" alt="">
                                <img class="w-10 h-10 rounded-full border-2 border-white" src="https://ui-avatars.com/api/?name=User+2&background=random" alt="">
                                <img class="w-10 h-10 rounded-full border-2 border-white" src="https://ui-avatars.com/api/?name=User+3&background=random" alt="">
                            </div>
                            <p class="text-sm text-gray-500 font-medium">+50 agences nous font déjà confiance</p>
                        </div>
                    </div>
                    <div class="relative">
                        <div class="bg-blue-600/5 absolute -inset-4 rounded-3xl -rotate-2"></div>
                        <div class="relative bg-white p-4 rounded-2xl shadow-2xl border border-gray-100">
                            <img src="https://laravel.com/img/logomark.min.svg" class="w-full opacity-10 p-20" alt="Dashboard Preview">
                            <div class="absolute inset-0 flex items-center justify-center">
                                <span class="text-3xl font-black text-blue-600 opacity-20">Aperçu Dashboard</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Features -->
            <div id="features" class="bg-white py-24">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center mb-16">
                    <h2 class="text-3xl font-bold text-gray-900 mb-4">Tout ce dont vous avez besoin</h2>
                    <p class="text-gray-600 max-w-2xl mx-auto italic">"gImmo a été conçu pour simplifier le quotidien des gestionnaires immobiliers."</p>
                </div>
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid md:grid-cols-3 gap-8">
                    <div class="p-8 rounded-2xl bg-gray-50 border border-gray-100 hover:shadow-xl transition duration-300">
                        <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-xl flex items-center justify-center mb-6">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H5a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-3">Multi-Agences</h3>
                        <p class="text-gray-600">Inscrivez votre agence et gérez vos propres biens en toute isolation et sécurité.</p>
                    </div>
                    <div class="p-8 rounded-2xl bg-gray-50 border border-gray-100 hover:shadow-xl transition duration-300">
                        <div class="w-12 h-12 bg-green-100 text-green-600 rounded-xl flex items-center justify-center mb-6">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-3">Facturation Auto</h3>
                        <p class="text-gray-600">Générez vos quittances et factures en un clic et suivez les impayés sans stress.</p>
                    </div>
                    <div class="p-8 rounded-2xl bg-gray-50 border border-gray-100 hover:shadow-xl transition duration-300">
                        <div class="w-12 h-12 bg-purple-100 text-purple-600 rounded-xl flex items-center justify-center mb-6">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-3">Support Tickets</h3>
                        <p class="text-gray-600">Communiquez directement avec vos locataires via un système de tickets interactif.</p>
                    </div>
                </div>
            </div>
        </main>

        <!-- Footer -->
        <footer class="bg-gray-900 text-gray-400 py-12">
            <div class="max-w-7xl mx-auto px-4 text-center">
                <p>&copy; 2026 gImmo Software. Tous droits réservés.</p>
            </div>
        </footer>
    </div>
</body>
</html>
