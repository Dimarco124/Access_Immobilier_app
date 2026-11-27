@extends('layouts.super-admin')

@section('title', 'Mon Profil')
@section('subtitle', 'Gérez vos informations personnelles et la sécurité de votre compte')

@section('content')
<div class="space-y-6 max-w-3xl">

    {{-- Bouton retour --}}
    <div>
        <a href="{{ route('super-admin.dashboard') }}"
           class="inline-flex items-center gap-2 text-sm text-slate-400 hover:text-blue-400 transition-colors font-medium">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Retour au tableau de bord
        </a>
    </div>

    {{-- Message succès --}}
    @if (session('status') === 'profile-updated')
        <div class="flex items-center gap-3 bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 rounded-xl px-5 py-3 text-sm font-semibold">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            Profil mis à jour avec succès.
        </div>
    @endif

    @if (session('status') === 'password-updated')
        <div class="flex items-center gap-3 bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 rounded-xl px-5 py-3 text-sm font-semibold">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            Mot de passe mis à jour avec succès.
        </div>
    @endif

    {{-- Informations personnelles --}}
    <div class="sa-card">
        <div class="p-6 border-b border-slate-700/50">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-blue-500/20 flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-bold text-white text-sm uppercase tracking-wider">Informations Personnelles</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Mettez à jour votre nom et votre adresse e-mail.</p>
                </div>
            </div>
        </div>
        <div class="p-6">
            <form method="post" action="{{ route('profile.update') }}" class="space-y-5">
                @csrf
                @method('patch')

                {{-- Nom --}}
                <div>
                    <label for="name" class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">
                        Nom complet
                    </label>
                    <input
                        id="name"
                        name="name"
                        type="text"
                        value="{{ old('name', $user->name) }}"
                        required
                        autofocus
                        autocomplete="name"
                        class="w-full bg-slate-700/50 border border-slate-600/50 rounded-xl px-4 py-3 text-white placeholder-slate-500 text-sm
                               focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500/50 transition-all"
                    />
                    @error('name')
                        <p class="mt-1.5 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Email --}}
                <div>
                    <label for="email" class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">
                        Adresse Email
                    </label>
                    <input
                        id="email"
                        name="email"
                        type="email"
                        value="{{ old('email', $user->email) }}"
                        required
                        autocomplete="username"
                        class="w-full bg-slate-700/50 border border-slate-600/50 rounded-xl px-4 py-3 text-white placeholder-slate-500 text-sm
                               focus:outline-none focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500/50 transition-all"
                    />
                    @error('email')
                        <p class="mt-1.5 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                    @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                        <p class="mt-2 text-xs text-amber-400">
                            Votre adresse e-mail n'est pas vérifiée.
                        </p>
                    @endif
                </div>

                <div class="pt-2">
                    <button type="submit"
                            class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-500 hover:to-indigo-500
                                   text-white font-bold text-sm px-6 py-3 rounded-xl shadow-lg shadow-blue-500/20 transition-all duration-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Enregistrer les modifications
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Sécurité / Mot de passe --}}
    <div class="sa-card">
        <div class="p-6 border-b border-slate-700/50">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-purple-500/20 flex items-center justify-center">
                    <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="font-bold text-white text-sm uppercase tracking-wider">Sécurité du Compte</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Assurez-vous que votre compte utilise un mot de passe long et complexe.</p>
                </div>
            </div>
        </div>
        <div class="p-6">
            <form method="post" action="{{ route('password.update') }}" class="space-y-5">
                @csrf
                @method('put')

                {{-- Mot de passe actuel --}}
                <div>
                    <label for="update_password_current_password"
                           class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">
                        Mot de passe actuel
                    </label>
                    <input
                        id="update_password_current_password"
                        name="current_password"
                        type="password"
                        autocomplete="current-password"
                        class="w-full bg-slate-700/50 border border-slate-600/50 rounded-xl px-4 py-3 text-white placeholder-slate-500 text-sm
                               focus:outline-none focus:ring-2 focus:ring-purple-500/50 focus:border-purple-500/50 transition-all"
                    />
                    @error('current_password', 'updatePassword')
                        <p class="mt-1.5 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Nouveau mot de passe --}}
                <div>
                    <label for="update_password_password"
                           class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">
                        Nouveau mot de passe
                    </label>
                    <input
                        id="update_password_password"
                        name="password"
                        type="password"
                        autocomplete="new-password"
                        class="w-full bg-slate-700/50 border border-slate-600/50 rounded-xl px-4 py-3 text-white placeholder-slate-500 text-sm
                               focus:outline-none focus:ring-2 focus:ring-purple-500/50 focus:border-purple-500/50 transition-all"
                    />
                    @error('password', 'updatePassword')
                        <p class="mt-1.5 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Confirmation --}}
                <div>
                    <label for="update_password_password_confirmation"
                           class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">
                        Confirmer le nouveau mot de passe
                    </label>
                    <input
                        id="update_password_password_confirmation"
                        name="password_confirmation"
                        type="password"
                        autocomplete="new-password"
                        class="w-full bg-slate-700/50 border border-slate-600/50 rounded-xl px-4 py-3 text-white placeholder-slate-500 text-sm
                               focus:outline-none focus:ring-2 focus:ring-purple-500/50 focus:border-purple-500/50 transition-all"
                    />
                    @error('password_confirmation', 'updatePassword')
                        <p class="mt-1.5 text-xs text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="pt-2">
                    <button type="submit"
                            class="inline-flex items-center gap-2 bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-500 hover:to-indigo-500
                                   text-white font-bold text-sm px-6 py-3 rounded-xl shadow-lg shadow-purple-500/20 transition-all duration-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                        Mettre à jour le mot de passe
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Badge info compte Super Admin --}}
    <div class="sa-card border border-blue-500/20 bg-blue-500/5">
        <div class="p-5 flex items-start gap-4">
            <div class="w-9 h-9 rounded-lg bg-blue-500/20 flex items-center justify-center flex-shrink-0 mt-0.5">
                <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
            </div>
            <div>
                <h4 class="text-sm font-bold text-blue-400 uppercase tracking-wider">Compte Super Administrateur</h4>
                <p class="text-xs text-slate-400 mt-1 leading-relaxed">
                    Ce compte dispose des privilèges maximaux sur la plateforme.
                    La suppression du compte Super Admin n'est pas autorisée depuis cette interface pour des raisons de sécurité.
                </p>
            </div>
        </div>
    </div>

</div>
@endsection
