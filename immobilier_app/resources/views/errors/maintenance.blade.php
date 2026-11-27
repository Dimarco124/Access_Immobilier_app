<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Maintenance en cours — {{ config('app.name', 'Habitat Immobilier') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#F4F6FA] min-h-screen flex items-center justify-center p-6 font-sans antialiased">
    <div class="max-w-lg w-full bg-white rounded-3xl border border-slate-200 shadow-xl p-8 sm:p-10 text-center space-y-5">
        <div class="w-16 h-16 mx-auto rounded-2xl bg-gradient-to-br from-blue-600 to-indigo-600 text-white flex items-center justify-center shadow-lg shadow-blue-200">
            <i class="fas fa-tools text-2xl"></i>
        </div>
        <span class="inline-block px-3 py-1 rounded-full bg-amber-50 text-amber-700 text-[10px] font-black uppercase tracking-widest">
            Maintenance Plateforme
        </span>
        <h1 class="text-2xl font-black text-slate-900">Nous revenons très vite !</h1>
        <p class="text-sm text-slate-600 leading-relaxed">
            {{ $message ?? 'La plateforme est actuellement en maintenance pour amélioration. Merci de votre patience.' }}
        </p>
        <div class="pt-4 border-t border-slate-100 text-[11px] text-slate-400 font-semibold">
            Habitat Immobilier • Support : +2250554356019
        </div>
    </div>
</body>
</html>
