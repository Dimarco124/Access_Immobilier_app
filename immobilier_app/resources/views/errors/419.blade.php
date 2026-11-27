<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Session expirée - {{ config('app.name', 'Habitat Immobilier') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 font-sans antialiased min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full bg-white rounded-3xl shadow-xl shadow-slate-200/60 border border-slate-100 p-8 text-center">
        <div class="w-16 h-16 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center mx-auto mb-6">
            <i class="fas fa-clock text-2xl"></i>
        </div>
        <h1 class="text-2xl font-black text-slate-900 uppercase tracking-tight mb-3">
            Session expirée
        </h1>
        <p class="text-sm text-slate-500 font-medium leading-relaxed mb-8">
            Votre session a expiré suite à une période d'inactivité. Par mesure de sécurité, veuillez vous reconnecter pour continuer.
        </p>
        <a href="{{ route('login') }}"
           class="w-full inline-flex items-center justify-center gap-2 py-4 px-6 rounded-2xl bg-blue-600 hover:bg-blue-700 text-white font-black text-xs uppercase tracking-widest shadow-lg shadow-blue-200 transition-all">
            <i class="fas fa-sign-in-alt"></i>
            <span>Se reconnecter</span>
        </a>
    </div>
</body>
</html>

