@extends('layouts.super-admin')

@section('title', 'Mon Profil')
@section('subtitle', 'Gérez vos informations personnelles et la sécurité de votre compte')

@section('content')
<div class="max-w-2xl space-y-6">

    {{-- Bouton retour --}}
    <a href="{{ route('super-admin.dashboard') }}"
       class="inline-flex items-center gap-2 text-[10px] font-black uppercase tracking-wider text-slate-500 hover:text-blue-600 transition-colors">
        <i class="fas fa-arrow-left"></i> Retour au tableau de bord
    </a>

    {{-- ===== INFORMATIONS PERSONNELLES ===== --}}
    <div class="sa-card p-6">
        <h2 class="text-sm font-black uppercase tracking-wider text-slate-900 border-b border-slate-100 pb-2 mb-4">
            Informations Personnelles
        </h2>

        @if (session('status') === 'profile-updated')
            <div class="mb-4 p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold flex items-center gap-2">
                <i class="fas fa-check-circle text-emerald-600"></i> Profil mis à jour avec succès.
            </div>
        @endif

        <form method="post" action="{{ route('profile.update') }}" class="space-y-4">
            @csrf
            @method('patch')

            <div>
                <label class="block text-[10px] font-black uppercase text-slate-500 mb-1">Nom complet *</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name"
                       class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm @error('name') border-rose-400 @enderror">
                @error('name')
                    <p class="mt-1 text-[11px] text-rose-500 font-semibold">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-[10px] font-black uppercase text-slate-500 mb-1">Adresse Email *</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" required autocomplete="username"
                       class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm @error('email') border-rose-400 @enderror">
                @error('email')
                    <p class="mt-1 text-[11px] text-rose-500 font-semibold">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-end pt-2 border-t border-slate-100">
                <button type="submit"
                        class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 text-white text-xs font-black uppercase tracking-wider shadow-md hover:from-blue-700 hover:to-indigo-700">
                    <i class="fas fa-save mr-1.5"></i> Enregistrer
                </button>
            </div>
        </form>
    </div>

    {{-- ===== MOT DE PASSE ===== --}}
    <div class="sa-card p-6">
        <h2 class="text-sm font-black uppercase tracking-wider text-slate-900 border-b border-slate-100 pb-2 mb-4">
            Sécurité du Compte
        </h2>

        @if (session('status') === 'password-updated')
            <div class="mb-4 p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold flex items-center gap-2">
                <i class="fas fa-check-circle text-emerald-600"></i> Mot de passe mis à jour avec succès.
            </div>
        @endif

        <form method="post" action="{{ route('password.update') }}" class="space-y-4">
            @csrf
            @method('put')

            <div>
                <label class="block text-[10px] font-black uppercase text-slate-500 mb-1">Mot de passe actuel *</label>
                <input type="password" name="current_password" autocomplete="current-password"
                       class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm @error('current_password', 'updatePassword') border-rose-400 @enderror">
                @error('current_password', 'updatePassword')
                    <p class="mt-1 text-[11px] text-rose-500 font-semibold">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-[10px] font-black uppercase text-slate-500 mb-1">Nouveau mot de passe *</label>
                <input type="password" name="password" autocomplete="new-password"
                       class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm @error('password', 'updatePassword') border-rose-400 @enderror">
                @error('password', 'updatePassword')
                    <p class="mt-1 text-[11px] text-rose-500 font-semibold">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-[10px] font-black uppercase text-slate-500 mb-1">Confirmer le nouveau mot de passe *</label>
                <input type="password" name="password_confirmation" autocomplete="new-password"
                       class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm">
            </div>

            <div class="flex justify-end pt-2 border-t border-slate-100">
                <button type="submit"
                        class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 text-white text-xs font-black uppercase tracking-wider shadow-md hover:from-blue-700 hover:to-indigo-700">
                    <i class="fas fa-key mr-1.5"></i> Mettre à jour le mot de passe
                </button>
            </div>
        </form>
    </div>

    {{-- ===== BADGE SUPER ADMIN ===== --}}
    <div class="sa-card p-5 border-l-4 border-l-blue-600">
        <div class="flex items-start gap-3">
            <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center flex-shrink-0">
                <i class="fas fa-shield-alt text-sm"></i>
            </div>
            <div>
                <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Compte Super Administrateur</p>
                <p class="text-sm font-black text-slate-900 mt-0.5">{{ $user->name }}</p>
                <p class="text-[11px] text-slate-500 mt-1">
                    Ce compte dispose des privilèges maximaux. La suppression n'est pas autorisée depuis cette interface.
                </p>
            </div>
        </div>
    </div>

</div>
@endsection
