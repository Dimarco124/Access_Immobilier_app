@extends('layouts.super-admin')

@section('title', 'Mon Profil')
@section('subtitle', 'Gérez vos informations personnelles et la sécurité de votre compte')

@section('content')
<div class="max-w-2xl space-y-6">

    {{-- Bouton retour --}}
    <div>
        <a href="{{ route('super-admin.dashboard') }}"
           class="inline-flex items-center gap-2 text-xs font-bold text-slate-500 hover:text-blue-600 transition-colors uppercase tracking-wider">
            <i class="fas fa-arrow-left"></i>
            Retour au tableau de bord
        </a>
    </div>

    {{-- Alertes session --}}
    @if (session('status') === 'profile-updated')
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold flex items-center gap-3">
            <i class="fas fa-check-circle text-emerald-600 text-base"></i>
            <span>Profil mis à jour avec succès.</span>
        </div>
    @endif
    @if (session('status') === 'password-updated')
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold flex items-center gap-3">
            <i class="fas fa-check-circle text-emerald-600 text-base"></i>
            <span>Mot de passe mis à jour avec succès.</span>
        </div>
    @endif

    {{-- ======================== INFORMATIONS PERSONNELLES ======================== --}}
    <div class="sa-card p-6">
        {{-- En-tête section --}}
        <div class="flex items-center gap-3 mb-6 pb-4 border-b border-slate-100">
            <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                <i class="fas fa-user text-sm"></i>
            </div>
            <div>
                <h2 class="text-sm font-black uppercase tracking-tight text-slate-900">Informations Personnelles</h2>
                <p class="text-[11px] text-slate-400 font-medium mt-0.5">Mettez à jour votre nom et votre adresse e-mail.</p>
            </div>
        </div>

        <form method="post" action="{{ route('profile.update') }}" class="space-y-5">
            @csrf
            @method('patch')

            {{-- Nom --}}
            <div>
                <label for="name" class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-1.5">
                    Nom complet
                </label>
                <input
                    id="name"
                    name="name"
                    type="text"
                    value="{{ old('name', $user->name) }}"
                    required autofocus autocomplete="name"
                    class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm text-slate-900 bg-white
                           focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-400 transition-all
                           @error('name') border-rose-400 @enderror"
                />
                @error('name')
                    <p class="mt-1 text-[11px] text-rose-500 font-semibold">{{ $message }}</p>
                @enderror
            </div>

            {{-- Email --}}
            <div>
                <label for="email" class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-1.5">
                    Adresse Email
                </label>
                <input
                    id="email"
                    name="email"
                    type="email"
                    value="{{ old('email', $user->email) }}"
                    required autocomplete="username"
                    class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm text-slate-900 bg-white
                           focus:outline-none focus:ring-2 focus:ring-blue-500/30 focus:border-blue-400 transition-all
                           @error('email') border-rose-400 @enderror"
                />
                @error('email')
                    <p class="mt-1 text-[11px] text-rose-500 font-semibold">{{ $message }}</p>
                @enderror
                @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                    <p class="mt-1.5 text-[11px] text-amber-600 font-semibold flex items-center gap-1">
                        <i class="fas fa-exclamation-triangle"></i>
                        Votre adresse e-mail n'est pas vérifiée.
                    </p>
                @endif
            </div>

            <div class="pt-1">
                <button type="submit"
                        class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white font-black text-xs px-5 py-2.5 rounded-xl transition-colors uppercase tracking-wider">
                    <i class="fas fa-save"></i>
                    Enregistrer
                </button>
            </div>
        </form>
    </div>

    {{-- ======================== MOT DE PASSE ======================== --}}
    <div class="sa-card p-6">
        <div class="flex items-center gap-3 mb-6 pb-4 border-b border-slate-100">
            <div class="w-9 h-9 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                <i class="fas fa-lock text-sm"></i>
            </div>
            <div>
                <h2 class="text-sm font-black uppercase tracking-tight text-slate-900">Sécurité du Compte</h2>
                <p class="text-[11px] text-slate-400 font-medium mt-0.5">Utilisez un mot de passe long et complexe pour sécuriser votre compte.</p>
            </div>
        </div>

        <form method="post" action="{{ route('password.update') }}" class="space-y-5">
            @csrf
            @method('put')

            {{-- Mot de passe actuel --}}
            <div>
                <label for="current_password" class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-1.5">
                    Mot de passe actuel
                </label>
                <input
                    id="current_password"
                    name="current_password"
                    type="password"
                    autocomplete="current-password"
                    class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm text-slate-900 bg-white
                           focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400 transition-all
                           @error('current_password', 'updatePassword') border-rose-400 @enderror"
                />
                @error('current_password', 'updatePassword')
                    <p class="mt-1 text-[11px] text-rose-500 font-semibold">{{ $message }}</p>
                @enderror
            </div>

            {{-- Nouveau mot de passe --}}
            <div>
                <label for="new_password" class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-1.5">
                    Nouveau mot de passe
                </label>
                <input
                    id="new_password"
                    name="password"
                    type="password"
                    autocomplete="new-password"
                    class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm text-slate-900 bg-white
                           focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400 transition-all
                           @error('password', 'updatePassword') border-rose-400 @enderror"
                />
                @error('password', 'updatePassword')
                    <p class="mt-1 text-[11px] text-rose-500 font-semibold">{{ $message }}</p>
                @enderror
            </div>

            {{-- Confirmation --}}
            <div>
                <label for="password_confirmation" class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-1.5">
                    Confirmer le nouveau mot de passe
                </label>
                <input
                    id="password_confirmation"
                    name="password_confirmation"
                    type="password"
                    autocomplete="new-password"
                    class="w-full border border-slate-200 rounded-xl px-4 py-2.5 text-sm text-slate-900 bg-white
                           focus:outline-none focus:ring-2 focus:ring-indigo-500/30 focus:border-indigo-400 transition-all"
                />
            </div>

            <div class="pt-1">
                <button type="submit"
                        class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white font-black text-xs px-5 py-2.5 rounded-xl transition-colors uppercase tracking-wider">
                    <i class="fas fa-key"></i>
                    Mettre à jour le mot de passe
                </button>
            </div>
        </form>
    </div>

    {{-- ======================== BADGE COMPTE SA ======================== --}}
    <div class="sa-card p-5 border-l-4 border-l-blue-600">
        <div class="flex items-start gap-4">
            <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center flex-shrink-0">
                <i class="fas fa-shield-alt text-sm"></i>
            </div>
            <div>
                <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Compte Super Administrateur</p>
                <p class="text-sm font-black text-slate-900 mt-0.5">{{ $user->name }}</p>
                <p class="text-[11px] text-slate-500 mt-1 leading-relaxed">
                    Ce compte dispose des privilèges maximaux sur la plateforme.
                    La suppression du compte Super Admin n'est pas autorisée depuis cette interface pour des raisons de sécurité.
                </p>
            </div>
        </div>
    </div>

</div>
@endsection
