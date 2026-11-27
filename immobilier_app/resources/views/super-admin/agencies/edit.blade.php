@extends('layouts.super-admin')

@section('title', 'Modifier ' . $agence->nom)
@section('subtitle', 'Mise à jour des paramètres de l\'agence')

@section('content')
    <div class="max-w-2xl mx-auto sa-card p-6 sm:p-8">
        <form method="POST" action="{{ route('super-admin.agencies.update', $agence) }}" class="space-y-5">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-[10px] font-black uppercase text-slate-500 mb-1">Nom *</label>
                    <input type="text" name="nom" value="{{ old('nom', $agence->nom) }}" required class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm">
                </div>
                <div>
                    <label class="block text-[10px] font-black uppercase text-slate-500 mb-1">Email *</label>
                    <input type="email" name="email" value="{{ old('email', $agence->email) }}" required class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm">
                </div>
                <div>
                    <label class="block text-[10px] font-black uppercase text-slate-500 mb-1">Téléphone *</label>
                    <input type="text" name="telephone" value="{{ old('telephone', $agence->telephone) }}" required class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm">
                </div>
                <div>
                    <label class="block text-[10px] font-black uppercase text-slate-500 mb-1">Adresse *</label>
                    <input type="text" name="adresse" value="{{ old('adresse', $agence->adresse) }}" required class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm">
                </div>
                <div>
                    <label class="block text-[10px] font-black uppercase text-slate-500 mb-1">Devise *</label>
                    <input type="text" name="devise" value="{{ old('devise', $agence->devise) }}" required class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm">
                </div>
                <div>
                    <label class="block text-[10px] font-black uppercase text-slate-500 mb-1">Taux TVA (%) *</label>
                    <input type="number" step="0.01" name="taux_tva" value="{{ old('taux_tva', $agence->taux_tva) }}" required class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-[10px] font-black uppercase text-slate-500 mb-1">Statut *</label>
                    <select name="statut" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm font-bold">
                        <option value="actif" {{ old('statut', $agence->statut) === 'actif' ? 'selected' : '' }}>Actif</option>
                        <option value="suspendu" {{ old('statut', $agence->statut) === 'suspendu' ? 'selected' : '' }}>Suspendu</option>
                    </select>
                </div>
            </div>

            <div class="flex items-center justify-between pt-4 border-t border-slate-100">
                <a href="{{ route('super-admin.agencies.show', $agence) }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600">Annuler</a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-blue-600 text-white text-xs font-black uppercase tracking-wider hover:bg-blue-700">
                    Enregistrer
                </button>
            </div>
        </form>
    </div>
@endsection
