<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Habitat Immobilier - L'Excellence en Gestion Immobilière</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        .glass {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(12px);
        }
    </style>
</head>

<body class="antialiased bg-[#FAFBFF] text-gray-900">
    <!-- Navbar -->
    <nav class="fixed w-full z-50 transition-all duration-300 glass border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20 items-center">
                <a href="{{ url('/') }}" class="flex items-center gap-3 group">
                    <img src="{{ asset('images/logo.svg') }}" alt="Habitat Immobilier" class="h-11 w-auto transition-transform group-hover:scale-105">
                </a>
                <div class="hidden md:flex items-center gap-10">
                    <a href="#features" class="text-sm font-bold text-gray-500 hover:text-blue-600 transition uppercase tracking-widest">Solutions</a>
                    <a href="#about" class="text-sm font-bold text-gray-500 hover:text-blue-600 transition uppercase tracking-widest">À Propos</a>
                    @auth
                    <a href="{{ url('/dashboard') }}" class="bg-blue-600 text-white px-6 py-3 rounded-xl font-black text-xs uppercase tracking-widest hover:bg-blue-700 transition">Mon Dashboard</a>
                    @else
                    <a href="{{ route('login') }}" class="text-sm font-bold text-gray-900 hover:text-blue-600 transition uppercase tracking-widest">Connexion</a>
                    <a href="{{ route('register.agency') }}" class="bg-blue-600 text-white px-6 py-3 rounded-xl font-black text-xs uppercase tracking-widest hover:bg-blue-700 transition shadow-xl shadow-blue-100">Démarrer</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <header class="relative pt-32 pb-20 lg:pt-48 lg:pb-32 overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center max-w-4xl mx-auto">
                <span class="inline-block px-4 py-2 rounded-full bg-blue-50 text-blue-600 text-[10px] font-black uppercase tracking-[0.3em] mb-6">Plateforme Immobilière N°1</span>
                <h1 class="text-5xl lg:text-7xl font-black text-gray-900 leading-[1.1] mb-8 tracking-tight">
                    Propulsez votre agence dans <span class="text-blue-600">l'ère digitale.</span>
                </h1>
                <p class="text-xl text-gray-500 mb-12 leading-relaxed font-medium">
                    La solution de gestion immobilière la plus intuitive et complète pour les professionnels exigeants. Automatisation, sécurité et transparence réunies.
                </p>
                <div class="flex flex-col sm:flex-row items-center justify-center gap-6">
                    <a href="{{ route('register.agency') }}" class="w-full sm:w-auto bg-blue-600 text-white px-10 py-5 rounded-2xl font-black text-sm uppercase tracking-widest hover:bg-blue-700 transition shadow-2xl shadow-blue-200 transform hover:-translate-y-1">
                        Inscrire mon agence
                    </a>
                    <a href="#features" class="w-full sm:w-auto bg-white text-gray-900 px-10 py-5 rounded-2xl font-black text-sm uppercase tracking-widest border border-gray-100 hover:border-blue-200 transition shadow-sm transform hover:-translate-y-1">
                        Voir les fonctions
                    </a>
                </div>
            </div>
        </div>
        <!-- Background Elements -->
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-full h-full -z-0 opacity-10">
            <div class="absolute top-20 left-10 w-64 h-64 bg-blue-400 rounded-full blur-3xl"></div>
            <div class="absolute bottom-10 right-10 w-96 h-96 bg-indigo-400 rounded-full blur-3xl"></div>
        </div>
    </header>

    <!-- Visual Preview Section -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-10 mb-32 relative z-20">
        <div class="relative bg-gray-900 rounded-[2.5rem] p-4 shadow-2xl overflow-hidden group">
            <img src="https://images.unsplash.com/photo-1460925895917-afdab827c52f?ixlib=rb-4.0.3&auto=format&fit=crop&w=2426&q=80" alt="Dashboard Preview" class="rounded-[2rem] w-full object-cover opacity-80 group-hover:scale-105 transition duration-700">
            <div class="absolute inset-0 flex items-center justify-center">
                <div class="w-20 h-20 bg-white/20 backdrop-blur-md rounded-full flex items-center justify-center text-white text-2xl border border-white/30">
                    <i class="fas fa-play"></i>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="py-20 bg-white border-y border-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-12 text-center">
                <div>
                    <p class="text-4xl font-black text-gray-900 mb-2">+1200</p>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Biens Gérés</p>
                </div>
                <div>
                    <p class="text-4xl font-black text-blue-600 mb-2">50+</p>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Agences Actives</p>
                </div>
                <div>
                    <p class="text-4xl font-black text-gray-900 mb-2">99.9%</p>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Disponibilité</p>
                </div>
                <div>
                    <p class="text-4xl font-black text-blue-600 mb-2">24/7</p>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Support Expert</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Grid -->
    <section id="features" class="py-32">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-20">
                <h2 class="text-4xl font-black text-gray-900 uppercase tracking-tight mb-4">Puissance & Simplicité</h2>
                <p class="text-gray-500 font-medium max-w-2xl mx-auto italic">Chaque fonctionnalité a été pensée pour répondre aux défis réels du marché immobilier africain.</p>
            </div>
            <div class="grid md:grid-cols-3 gap-10">
                <!-- Feature 1 -->
                <div class="bg-white p-10 rounded-[2rem] border border-gray-100 hover:shadow-2xl transition duration-500 transform hover:-translate-y-2 group">
                    <div class="w-16 h-16 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center mb-8 group-hover:bg-blue-600 group-hover:text-white transition duration-500">
                        <i class="fas fa-file-invoice-dollar text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-black text-gray-900 mb-4 uppercase">Facturation Intelligente</h3>
                    <p class="text-gray-500 leading-relaxed text-sm font-medium">Génération automatique des loyers, calcul des frais de retard et quittances professionnelles en un clic.</p>
                </div>
                <!-- Feature 2 -->
                <div class="bg-white p-10 rounded-[2rem] border border-gray-100 hover:shadow-2xl transition duration-500 transform hover:-translate-y-2 group">
                    <div class="w-16 h-16 bg-green-50 text-green-600 rounded-2xl flex items-center justify-center mb-8 group-hover:bg-green-600 group-hover:text-white transition duration-500">
                        <i class="fas fa-user-shield text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-black text-gray-900 mb-4 uppercase">Audit Locataire</h3>
                    <p class="text-gray-500 leading-relaxed text-sm font-medium">Sécurisez vos revenus avec un système d'audit rigoureux avant toute signature de contrat.</p>
                </div>
                <!-- Feature 3 -->
                <div class="bg-white p-10 rounded-[2rem] border border-gray-100 hover:shadow-2xl transition duration-500 transform hover:-translate-y-2 group">
                    <div class="w-16 h-16 bg-purple-50 text-purple-600 rounded-2xl flex items-center justify-center mb-8 group-hover:bg-purple-600 group-hover:text-white transition duration-500">
                        <i class="fas fa-chart-pie text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-black text-gray-900 mb-4 uppercase">Rapports Financiers</h3>
                    <p class="text-gray-500 leading-relaxed text-sm font-medium">Vue à 360° pour les agences, les propriétaires et les locataires sur leurs situations comptables.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-blue-600 rounded-[3rem] p-12 lg:p-20 text-center relative overflow-hidden shadow-2xl shadow-blue-200">
                <div class="relative z-10">
                    <h2 class="text-4xl lg:text-5xl font-black text-white mb-8 tracking-tight">Prêt à transformer votre gestion ?</h2>
                    <p class="text-slate-400 mb-12 text-white max-w-2xl mx-auto font-medium italic">Rejoignez la communauté E-Immo et offrez le meilleur à vos clients dès aujourd'hui.</p>
                    <a href="{{ route('register.agency') }}" class="inline-block bg-white text-blue-600 px-12 py-5 rounded-2xl font-black text-sm uppercase tracking-widest hover:bg-blue-50 transition shadow-xl transform hover:-translate-y-1">
                        Démarrer l'essai gratuit
                    </a>
                </div>
                <!-- Background Pattern -->
                <div class="absolute inset-0 opacity-10">
                    <div class="absolute -top-10 -right-10 w-64 h-64 border-[40px] border-white rounded-full"></div>
                    <div class="absolute -bottom-10 -left-10 w-96 h-96 border-[60px] border-white rounded-full opacity-20"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-white pt-20 pb-10 border-t border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-4 gap-12 mb-16">
                <div class="col-span-2">
                    <div class="flex items-center gap-2 mb-6">
                        <img src="{{ asset('images/logo.svg') }}" alt="Habitat Immobilier" class="h-10 w-auto">
                    </div>
                    <p class="text-slate-400 text-sm max-w-sm leading-relaxed font-medium">
                        La plateforme de référence pour la gestion immobilière moderne. Conçue pour l'excellence opérationnelle et la satisfaction client.
                    </p>
                </div>
                <div>
                    <h4 class="text-xs font-black text-gray-900 uppercase tracking-widest mb-6">Liens Rapides</h4>
                    <ul class="space-y-4 text-sm font-bold text-gray-400">
                        <li><a href="#" class="hover:text-blue-600 transition">Accueil</a></li>
                        <li><a href="#features" class="hover:text-blue-600 transition">Fonctionnalités</a></li>
                        <li><a href="{{ route('register.agency') }}" class="hover:text-blue-600 transition">Inscrire mon Agence</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="text-xs font-black text-gray-900 uppercase tracking-widest mb-6">Légal</h4>
                    <ul class="space-y-4 text-sm font-bold text-gray-400">
                        <li><a href="#" class="hover:text-blue-600 transition">Confidentialité</a></li>
                        <li><a href="#" class="hover:text-blue-600 transition">CGU / CGV</a></li>
                        <li><a href="#" class="hover:text-blue-600 transition">Contact</a></li>
                    </ul>
                </div>
            </div>
            <div class="pt-8 border-t border-gray-50 flex flex-col md:row justify-between items-center gap-4">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">&copy; {{ date('Y') }} Habitat Immobilier. Tous droits réservés.</p>
                <div class="flex gap-6 text-gray-400">
                    <a href="#"><i class="fab fa-facebook-f hover:text-blue-600 transition"></i></a>
                    <a href="#"><i class="fab fa-linkedin-in hover:text-blue-600 transition"></i></a>
                    <a href="#"><i class="fab fa-twitter hover:text-blue-600 transition"></i></a>
                </div>
            </div>
        </div>
    </footer>
</body>

</html>