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

        .glass-nav {
            background: rgba(244, 246, 250, 0.88);
            backdrop-filter: blur(14px);
        }

        .hero-bubble {
            background: radial-gradient(circle at 30% 30%, rgba(255, 255, 255, 0.24), rgba(255, 255, 255, 0.04));
        }
    </style>
</head>

<body class="antialiased bg-[#F4F6FA] text-slate-900 overflow-x-hidden">
    <!-- Décor latéral discret rappelant la silhouette organique du design -->
    <div class="pointer-events-none fixed inset-x-0 top-52 -z-10 flex justify-between opacity-25">
        <div class="w-40 h-96 bg-gradient-to-r from-blue-400/40 to-transparent rounded-r-full blur-2xl"></div>
        <div class="w-40 h-96 bg-gradient-to-l from-indigo-400/40 to-transparent rounded-l-full blur-2xl"></div>
    </div>

    <!-- Navbar -->
    <nav class="fixed w-full z-50 transition-all duration-300 glass-nav border-b border-slate-100/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20 items-center">
                <!-- Logo -->
                <a href="{{ url('/') }}" class="flex items-center gap-3 group shrink-0">
                    <img src="{{ asset('images/logo.svg') }}" alt="Habitat Immobilier" class="h-10 sm:h-11 w-auto transition-transform group-hover:scale-105">
                </a>

                <!-- Liens centrés -->
                <div class="hidden lg:flex items-center gap-8">
                    <a href="#about" class="text-xs font-bold text-slate-500 hover:text-blue-600 transition uppercase tracking-widest">À Propos</a>
                    <a href="#features" class="text-xs font-bold text-slate-500 hover:text-blue-600 transition uppercase tracking-widest">Solutions</a>
                    <a href="#process" class="text-xs font-bold text-slate-500 hover:text-blue-600 transition uppercase tracking-widest">Fonctionnement</a>
                    <a href="#contact" class="text-xs font-bold text-slate-500 hover:text-blue-600 transition uppercase tracking-widest">Contact</a>
                </div>

                <!-- Actions droite -->
                <div class="flex items-center gap-2.5 sm:gap-3">
                    <a href="https://wa.me/2250554356019" target="_blank" rel="noopener"
                       class="hidden sm:inline-flex w-9 h-9 rounded-full bg-white border border-slate-200 text-slate-600 hover:text-blue-600 hover:border-blue-200 items-center justify-center shadow-xs transition"
                       title="Contacter sur WhatsApp">
                        <i class="fab fa-whatsapp text-sm"></i>
                    </a>
                    @auth
                        <a href="{{ url('/dashboard') }}" class="inline-flex items-center gap-2 bg-blue-600 text-white px-5 py-2.5 rounded-full font-extrabold text-xs uppercase tracking-wider hover:bg-blue-700 transition shadow-lg shadow-blue-200">
                            <span>Mon Dashboard</span>
                            <i class="fas fa-arrow-right text-[10px]"></i>
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="px-4 py-2.5 rounded-full text-xs font-extrabold text-slate-700 hover:text-blue-600 hover:bg-blue-50 transition uppercase tracking-wider">
                            Connexion
                        </a>
                        <a href="{{ route('register.agency') }}" class="inline-flex items-center gap-2 bg-blue-600 text-white px-5 py-2.5 rounded-full font-extrabold text-xs uppercase tracking-wider hover:bg-blue-700 transition shadow-lg shadow-blue-200">
                            <span>Démarrer</span>
                            <i class="fas fa-arrow-right text-[10px]"></i>
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- HERO SECTION : Carte arrondie avec découpe architecturale en bas à gauche -->
    <header class="pt-28 pb-14 lg:pt-32 lg:pb-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="relative rounded-[2.25rem] sm:rounded-[2.75rem] bg-gradient-to-br from-blue-600 via-blue-500 to-indigo-600 overflow-hidden">
                <!-- Bulles décoratives translucides -->
                <div class="pointer-events-none absolute -top-16 left-1/3 w-72 h-72 rounded-full hero-bubble"></div>
                <div class="pointer-events-none absolute bottom-12 right-12 w-56 h-56 rounded-full hero-bubble"></div>
                <div class="pointer-events-none absolute top-1/2 left-1/4 w-24 h-24 rounded-full hero-bubble"></div>

                <div class="relative z-10 grid lg:grid-cols-12 gap-8 items-end px-6 pt-8 pb-6 sm:px-10 sm:pt-10 lg:px-12 lg:pt-12 lg:pb-0">
                    <!-- Colonne gauche : Carte formulaire frosted + texte -->
                    <div class="lg:col-span-5 lg:pb-12 space-y-6">
                        <!-- Carte frosted glass -->
                        <div class="bg-white/15 backdrop-blur-md border border-white/25 rounded-3xl p-6 shadow-xl">
                            <div class="flex items-center justify-between mb-2">
                                <h2 class="text-base sm:text-lg font-extrabold text-white tracking-tight">Demander une démo</h2>
                                <span class="px-2.5 py-0.5 rounded-full bg-white/20 text-white text-[10px] font-black uppercase tracking-wider">Rapide</span>
                            </div>
                            <p class="text-xs text-blue-50/90 leading-relaxed mb-4 font-medium">
                                Laissez vos coordonnées et notre équipe vous accompagne pour configurer votre agence en 5 minutes.
                            </p>
                            <form id="demoCallbackForm" class="space-y-3">
                                <input type="text" id="demoName" required placeholder="Votre nom ou agence"
                                       class="w-full px-4 py-2.5 rounded-xl bg-white text-slate-800 placeholder-slate-400 text-xs font-semibold border-0 focus:ring-2 focus:ring-blue-200 outline-none">
                                <input type="tel" id="demoPhone" required placeholder="Téléphone (ex: +225 05 54 35 60 19)"
                                       class="w-full px-4 py-2.5 rounded-xl bg-white text-slate-800 placeholder-slate-400 text-xs font-semibold border-0 focus:ring-2 focus:ring-blue-200 outline-none">
                                <button type="submit"
                                        class="w-full py-2.5 px-4 rounded-xl bg-white text-blue-600 hover:bg-blue-50 font-black text-xs uppercase tracking-wider shadow-sm transition flex items-center justify-center gap-2 cursor-pointer">
                                    <span>Être rappelé</span>
                                    <i class="fab fa-whatsapp text-sm"></i>
                                </button>
                            </form>
                        </div>

                        <p class="text-xs sm:text-sm text-blue-50/95 leading-relaxed font-medium max-w-md">
                            Centralisez vos biens, automatisez vos loyers, sécurisez vos cautions et offrez un portail dédié à vos propriétaires et locataires.
                        </p>
                    </div>

                    <!-- Colonne droite : Visuel immobilier moderne -->
                    <div class="lg:col-span-7 flex flex-col justify-end items-center lg:items-end relative">
                        <div class="relative w-full max-w-xl lg:mr-4">
                            <div class="rounded-t-[2rem] overflow-hidden shadow-2xl border-4 border-white/25 bg-white/10">
                                <img src="https://images.unsplash.com/photo-1560518883-ce09059eeffa?ixlib=rb-4.0.3&auto=format&fit=crop&w=1200&q=80"
                                     alt="Gestion Immobilière Moderne"
                                     class="w-full h-64 sm:h-80 lg:h-[360px] object-cover">
                            </div>
                            <!-- Badge flottant sur l'image -->
                            <div class="hidden sm:flex items-center gap-3 absolute bottom-6 -left-6 bg-white/95 backdrop-blur-md px-4 py-3 rounded-2xl shadow-xl border border-blue-50">
                                <div class="w-10 h-10 rounded-xl bg-blue-600 text-white flex items-center justify-center">
                                    <i class="fas fa-shield-alt"></i>
                                </div>
                                <div>
                                    <p class="text-xs font-black text-slate-900">100% Sécurisé</p>
                                    <p class="text-[10px] font-bold text-slate-400">Multi-agences & Cloud</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Barre inférieure avec découpe gris clair identique au fond du site -->
                <div class="relative z-20 flex flex-col md:flex-row items-stretch md:items-end justify-between">
                    <!-- Découpe gauche contenant le gros titre H1 (même couleur #F4F6FA que le body) -->
                    <div class="bg-[#F4F6FA] pt-6 pb-4 pr-8 pl-6 sm:pl-10 lg:pl-12 md:rounded-tr-[2.5rem] max-w-2xl">
                        <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-50 text-blue-600 text-[10px] font-black uppercase tracking-widest mb-3">
                            <span class="w-1.5 h-1.5 rounded-full bg-blue-600"></span>
                            Plateforme Immobilière N°1
                        </span>
                        <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black text-slate-900 leading-[1.1] tracking-tight">
                            Propulsez votre agence <span class="text-blue-600">dans l'ère digitale</span>
                        </h1>
                    </div>

                    <!-- Bouton pillule bas droite -->
                    <div class="px-6 pb-6 sm:px-10 lg:pr-10 lg:pb-8 flex items-center justify-start md:justify-end">
                        @auth
                            <a href="{{ url('/dashboard') }}"
                               class="inline-flex items-center gap-3 bg-white text-blue-600 hover:bg-blue-50 px-6 py-3.5 rounded-full font-black text-xs uppercase tracking-wider shadow-xl transition transform hover:-translate-y-0.5">
                                <span>Accéder au Dashboard</span>
                                <i class="fas fa-arrow-right"></i>
                            </a>
                        @else
                            <a href="{{ route('register.agency') }}"
                               class="inline-flex items-center gap-3 bg-white text-blue-600 hover:bg-blue-50 px-6 py-3.5 rounded-full font-black text-xs uppercase tracking-wider shadow-xl transition transform hover:-translate-y-0.5">
                                <span>Inscrire mon agence</span>
                                <i class="fas fa-arrow-right"></i>
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- SECTION 2 : À PROPOS + 3 CARTES STATISTIQUES (Style exact du modèle) -->
    <section id="about" class="py-16 lg:py-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-12 gap-10 lg:gap-12 items-center">
                <!-- Photo à gauche -->
                <div class="lg:col-span-5">
                    <div class="relative rounded-[2rem] overflow-hidden shadow-xl border border-slate-100 bg-white p-2">
                        <img src="https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80"
                             alt="Architecture et Immobilier"
                             class="w-full h-72 sm:h-96 object-cover rounded-[1.5rem]">
                    </div>
                </div>

                <!-- Texte + 3 cartes stats à droite -->
                <div class="lg:col-span-7 space-y-6">
                    <div>
                        <h2 class="text-3xl sm:text-4xl font-black text-slate-900 tracking-tight">
                            À Propos d'<span class="text-blue-600">Habitat Immobilier</span>
                        </h2>
                        <p class="mt-4 text-sm sm:text-base text-slate-500 leading-relaxed font-medium">
                            Conçue pour les agences immobilières exigeantes, notre plateforme simplifie chaque étape de la gestion locative : suivi des baux, quittances automatiques, recouvrement et reversements aux propriétaires.
                        </p>
                        <p class="mt-3 text-sm sm:text-base text-slate-500 leading-relaxed font-medium">
                            Grâce à une approche claire et structurée, vous gardez un contrôle total sur votre trésorerie, vos cautions détenues et la performance de votre équipe.
                        </p>
                    </div>

                    <!-- 3 cartes statistiques (1ère en bleu plein, 2 autres blanches) -->
                    <div class="grid sm:grid-cols-3 gap-4 pt-2">
                        <div class="rounded-2xl bg-gradient-to-br from-blue-600 to-indigo-600 text-white p-6 shadow-xl shadow-blue-200/60">
                            <p class="text-3xl sm:text-4xl font-black tracking-tight mb-2">+1200</p>
                            <p class="text-xs text-blue-50/90 font-medium leading-relaxed">
                                Biens et unités locatives gérés avec suivi en temps réel.
                            </p>
                        </div>
                        <div class="rounded-2xl bg-white border border-slate-100 p-6 shadow-sm hover:shadow-md transition">
                            <p class="text-3xl sm:text-4xl font-black text-slate-900 tracking-tight mb-2">50+</p>
                            <p class="text-xs text-slate-500 font-medium leading-relaxed">
                                Agences actives qui automatisent leurs loyers chaque mois.
                            </p>
                        </div>
                        <div class="rounded-2xl bg-white border border-slate-100 p-6 shadow-sm hover:shadow-md transition">
                            <p class="text-3xl sm:text-4xl font-black text-slate-900 tracking-tight mb-2">99.9%</p>
                            <p class="text-xs text-slate-500 font-medium leading-relaxed">
                                Disponibilité et sécurité cloud pour vos données comptables.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- SECTION 3 : SOLUTIONS / MODULES CLÉS (4 cartes avec médaillon circulaire) -->
    <section id="features" class="py-16 lg:py-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-14">
                <p class="text-xs font-black uppercase tracking-[0.25em] text-blue-600 mb-2">Nos Solutions</p>
                <h2 class="text-3xl sm:text-4xl font-black text-slate-900 tracking-tight">
                    Des Outils Pensés pour Chaque Pôle
                </h2>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6 items-start">
                <!-- Carte principale mise en avant -->
                <div class="flex flex-col items-center">
                    <div class="w-full bg-white rounded-3xl border border-slate-100 p-7 text-center shadow-lg shadow-slate-200/50 hover:-translate-y-1 transition duration-300">
                        <div class="w-20 h-20 mx-auto mb-5 rounded-full bg-gradient-to-br from-blue-600 to-indigo-600 text-white flex items-center justify-center shadow-lg shadow-blue-200">
                            <i class="fas fa-file-invoice-dollar text-2xl"></i>
                        </div>
                        <h3 class="text-base font-black text-slate-900">Facturation & Loyers</h3>
                        <p class="text-[11px] font-extrabold text-blue-600 uppercase tracking-wider mt-1 mb-3">Automatisation</p>
                        <p class="text-xs text-slate-500 leading-relaxed font-medium">
                            Génération automatique des loyers, calcul des retards et reçus PDF en un clic.
                        </p>
                    </div>
                    <a href="{{ route('register.agency') }}"
                       class="mt-5 inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-full text-xs font-black uppercase tracking-wider shadow-lg shadow-blue-200 transition">
                        <span>Essayer maintenant</span>
                        <i class="fas fa-plus text-[10px]"></i>
                    </a>
                </div>

                <!-- Carte 2 -->
                <div class="bg-white rounded-3xl border border-slate-100 p-7 text-center shadow-sm hover:shadow-xl hover:-translate-y-1 transition duration-300">
                    <div class="w-20 h-20 mx-auto mb-5 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center border border-indigo-100">
                        <i class="fas fa-wallet text-2xl"></i>
                    </div>
                    <h3 class="text-base font-black text-slate-900">Finance & Cautions</h3>
                    <p class="text-[11px] font-extrabold text-blue-600 uppercase tracking-wider mt-1 mb-3">Trésorerie</p>
                    <p class="text-xs text-slate-500 leading-relaxed font-medium">
                        Suivi fiducaire des cautions, calcul des commissions et reversements propriétaires.
                    </p>
                </div>

                <!-- Carte 3 -->
                <div class="bg-white rounded-3xl border border-slate-100 p-7 text-center shadow-sm hover:shadow-xl hover:-translate-y-1 transition duration-300">
                    <div class="w-20 h-20 mx-auto mb-5 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center border border-blue-100">
                        <i class="fas fa-user-shield text-2xl"></i>
                    </div>
                    <h3 class="text-base font-black text-slate-900">Audit & Contrats</h3>
                    <p class="text-[11px] font-extrabold text-blue-600 uppercase tracking-wider mt-1 mb-3">Sécurité</p>
                    <p class="text-xs text-slate-500 leading-relaxed font-medium">
                        Audit locataire rigoureux, contrats digitalisés et suivi des états des lieux.
                    </p>
                </div>

                <!-- Carte 4 -->
                <div class="bg-white rounded-3xl border border-slate-100 p-7 text-center shadow-sm hover:shadow-xl hover:-translate-y-1 transition duration-300">
                    <div class="w-20 h-20 mx-auto mb-5 rounded-full bg-sky-50 text-sky-600 flex items-center justify-center border border-sky-100">
                        <i class="fas fa-users-cog text-2xl"></i>
                    </div>
                    <h3 class="text-base font-black text-slate-900">Portails Multi-Rôles</h3>
                    <p class="text-[11px] font-extrabold text-blue-600 uppercase tracking-wider mt-1 mb-3">Collaboration</p>
                    <p class="text-xs text-slate-500 leading-relaxed font-medium">
                        Espaces dédiés pour votre équipe, vos propriétaires et vos locataires 24/7.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- SECTION 4 : COMMENT ÇA MARCHE (Visuel bleu gauche + étapes numérotées droite) -->
    <section id="process" class="py-16 lg:py-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-12 gap-10 lg:gap-12 items-center">
                <!-- Panneau gauche bleu arrondi -->
                <div class="lg:col-span-5">
                    <div class="relative rounded-[2.25rem] bg-gradient-to-br from-blue-600 via-blue-500 to-indigo-600 p-8 sm:p-10 text-white overflow-hidden shadow-2xl shadow-blue-200/60">
                        <div class="pointer-events-none absolute -top-10 -right-10 w-44 h-44 rounded-full hero-bubble"></div>
                        <div class="pointer-events-none absolute -bottom-12 -left-10 w-52 h-52 rounded-full hero-bubble"></div>

                        <div class="relative z-10 space-y-6">
                            <span class="inline-block px-3 py-1 rounded-full bg-white/15 text-[10px] font-black uppercase tracking-widest">
                                Prise en main immédiate
                            </span>
                            <h3 class="text-2xl sm:text-3xl font-black leading-tight">
                                Démarrez votre gestion digitale en 4 étapes simples
                            </h3>
                            <div class="rounded-2xl overflow-hidden border border-white/20 shadow-lg">
                                <img src="https://images.unsplash.com/photo-1460925895917-afdab827c52f?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80"
                                     alt="Aperçu Tableau de Bord"
                                     class="w-full h-48 object-cover">
                            </div>
                            <a href="{{ route('register.agency') }}"
                               class="inline-flex items-center gap-2 bg-white text-blue-600 hover:bg-blue-50 px-6 py-3 rounded-full font-black text-xs uppercase tracking-wider shadow-md transition">
                                <span>Créer mon compte agence</span>
                                <i class="fas fa-arrow-right text-[10px]"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Liste numérotée à droite -->
                <div class="lg:col-span-7 space-y-6">
                    <div class="flex items-start gap-5 p-5 rounded-2xl bg-white border border-slate-100 shadow-xs hover:shadow-md transition">
                        <div class="w-10 h-10 rounded-full bg-blue-50 text-blue-600 font-black text-sm flex items-center justify-center shrink-0 border border-blue-100 shadow-xs">
                            1
                        </div>
                        <div>
                            <h4 class="text-base font-black text-blue-600 mb-1">Inscription & Identité d'Agence</h4>
                            <p class="text-xs sm:text-sm text-slate-500 leading-relaxed font-medium">
                                Créez votre espace professionnel, importez votre logo, définissez votre devise et invitez vos collaborateurs avec leurs permissions.
                            </p>
                        </div>
                    </div>

                    <div class="flex items-start gap-5 p-5 rounded-2xl bg-white border border-slate-100 shadow-xs hover:shadow-md transition">
                        <div class="w-10 h-10 rounded-full bg-blue-50 text-blue-600 font-black text-sm flex items-center justify-center shrink-0 border border-blue-100 shadow-xs">
                            2
                        </div>
                        <div>
                            <h4 class="text-base font-black text-blue-600 mb-1">Enregistrement des Biens & Contrats</h4>
                            <p class="text-xs sm:text-sm text-slate-500 leading-relaxed font-medium">
                                Ajoutez vos immeubles, propriétaires et locataires. Générez la facture initiale (caution, avance, frais de dossier) automatiquement.
                            </p>
                        </div>
                    </div>

                    <div class="flex items-start gap-5 p-5 rounded-2xl bg-white border border-slate-100 shadow-xs hover:shadow-md transition">
                        <div class="w-10 h-10 rounded-full bg-blue-50 text-blue-600 font-black text-sm flex items-center justify-center shrink-0 border border-blue-100 shadow-xs">
                            3
                        </div>
                        <div>
                            <h4 class="text-base font-black text-blue-600 mb-1">Encaissements & Quittances Instantanées</h4>
                            <p class="text-xs sm:text-sm text-slate-500 leading-relaxed font-medium">
                                Enregistrez les paiements partiels ou complets, imprimez les reçus professionnels et suivez le taux de recouvrement en temps réel.
                            </p>
                        </div>
                    </div>

                    <div class="flex items-start gap-5 p-5 rounded-2xl bg-white border border-slate-100 shadow-xs hover:shadow-md transition">
                        <div class="w-10 h-10 rounded-full bg-blue-50 text-blue-600 font-black text-sm flex items-center justify-center shrink-0 border border-blue-100 shadow-xs">
                            4
                        </div>
                        <div>
                            <h4 class="text-base font-black text-blue-600 mb-1">Reversements & Pilotage Financier</h4>
                            <p class="text-xs sm:text-sm text-slate-500 leading-relaxed font-medium">
                                Visualisez vos commissions nettes, gérez les restitutions de cautions et effectuez les reversements propriétaires sans erreur de calcul.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer (Conservé avec crédit développeur) -->
    <footer id="contact" class="bg-white pt-20 pb-10 border-t border-gray-100">
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
                        <li><a href="tel:+2250554356019" class="hover:text-blue-600 transition">Contact</a></li>
                    </ul>
                </div>
            </div>
            <div class="pt-8 border-t border-gray-50 flex flex-col md:flex-row justify-between items-center gap-4">
                <div class="text-center md:text-left space-y-1">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">&copy; {{ date('Y') }} Habitat Immobilier. Tous droits réservés.</p>
                    <p class="text-[11px] font-bold text-slate-500">
                        Développé par <span class="text-blue-600 font-black">Valdes Di Marco</span>
                        <span class="mx-1.5 text-slate-300">•</span>
                        <a href="tel:+2250554356019" class="hover:text-blue-600 transition">+2250554356019</a>
                    </p>
                </div>
                <div class="flex gap-6 text-gray-400">
                    <a href="#"><i class="fab fa-facebook-f hover:text-blue-600 transition"></i></a>
                    <a href="#"><i class="fab fa-linkedin-in hover:text-blue-600 transition"></i></a>
                    <a href="#"><i class="fab fa-twitter hover:text-blue-600 transition"></i></a>
                </div>
            </div>
        </div>
    </footer>

    <script>
        document.getElementById('demoCallbackForm')?.addEventListener('submit', function (e) {
            e.preventDefault();
            const name = document.getElementById('demoName').value.trim();
            const phone = document.getElementById('demoPhone').value.trim();
            const text = encodeURIComponent(`Bonjour, je souhaite une démo d'Habitat Immobilier.\nNom / Agence : ${name}\nTéléphone : ${phone}`);
            window.open(`https://wa.me/2250554356019?text=${text}`, '_blank', 'noopener');
        });
    </script>
</body>

</html>
