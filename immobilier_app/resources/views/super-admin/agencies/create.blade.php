@extends('layouts.super-admin')

@section('title', 'Créer une Agence')
@section('subtitle', 'Création manuelle d\'une agence et de son administrateur')

@section('content')
    <div class="max-w-3xl mx-auto sa-card p-6 sm:p-8">
        <form method="POST" action="{{ route('super-admin.agencies.store') }}" class="space-y-6">
            @csrf

            <div>
                <h2 class="text-sm font-black uppercase tracking-wider text-slate-900 border-b border-slate-100 pb-2 mb-4">Informations de l'agence</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-black uppercase text-slate-500 mb-1">Nom de l'agence *</label>
                        <input type="text" name="nom" value="{{ old('nom') }}" required class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black uppercase text-slate-500 mb-1">Email de l'agence *</label>
                        <input type="email" name="email" value="{{ old('email') }}" required class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black uppercase text-slate-500 mb-1">Téléphone *</label>
                        <input type="text" name="telephone" value="{{ old('telephone') }}" required class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black uppercase text-slate-500 mb-1">Adresse *</label>
                        <input type="text" name="adresse" value="{{ old('adresse') }}" required class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black uppercase text-slate-500 mb-1">Devise *</label>
                        <input type="text" name="devise" value="{{ old('devise', 'XOF') }}" required class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black uppercase text-slate-500 mb-1">Taux TVA (%) *</label>
                        <input type="number" step="0.01" name="taux_tva" value="{{ old('taux_tva', '18') }}" required class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm">
                    </div>
                </div>
            </div>

            <div>
                <h2 class="text-sm font-black uppercase tracking-wider text-slate-900 border-b border-slate-100 pb-2 mb-4">Compte Administrateur principal</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[10px] font-black uppercase text-slate-500 mb-1">Nom complet *</label>
                        <input type="text" name="admin_name" value="{{ old('admin_name') }}" required class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black uppercase text-slate-500 mb-1">Email de connexion *</label>
                        <input type="email" name="admin_email" value="{{ old('admin_email') }}" required class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black uppercase text-slate-500 mb-1">Téléphone admin</label>
                        <input type="text" name="admin_telephone" value="{{ old('admin_telephone') }}" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black uppercase text-slate-500 mb-1">Mot de passe initial *</label>
                        <input type="password" name="admin_password" required minlength="8" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm">
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-between pt-4 border-t border-slate-100">
                <a href="{{ route('super-admin.agencies.index') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50">Annuler</a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 text-white text-xs font-black uppercase tracking-wider shadow-md hover:from-blue-700 hover:to-indigo-700">
                    Créer l'agence
                </button>
            </div>
        </form>
    </div>
@endsection
