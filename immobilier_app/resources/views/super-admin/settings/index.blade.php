@extends('layouts.super-admin')

@section('title', 'Mode Maintenance & Paramètres')
@section('subtitle', 'Mettre la plateforme en maintenance tout en conservant l\'accès Super Admin')

@section('content')
    <div class="max-w-2xl mx-auto sa-card p-6 sm:p-8">
        <form method="POST" action="{{ route('super-admin.settings.update') }}" class="space-y-6">
            @csrf

            <div class="p-5 rounded-2xl border {{ $maintenanceMode ? 'bg-amber-50 border-amber-200' : 'bg-slate-50 border-slate-200' }} flex items-center justify-between gap-4">
                <div>
                    <h2 class="text-sm font-black text-slate-900 uppercase">Mode Maintenance Plateforme</h2>
                    <p class="text-xs text-slate-600 mt-1">
                        Lorsqu'il est activé, toutes les agences, propriétaires et locataires voient la page de maintenance. Seul le Super Admin garde l'accès complet.
                    </p>
                </div>
                <label class="inline-flex items-center cursor-pointer shrink-0">
                    <input type="checkbox" name="maintenance_mode" value="1" {{ $maintenanceMode ? 'checked' : '' }} class="w-5 h-5 rounded text-blue-600 border-slate-300">
                </label>
            </div>

            <div>
                <label class="block text-[10px] font-black uppercase text-slate-500 mb-1">Message affiché pendant la maintenance *</label>
                <textarea name="maintenance_message" rows="4" required class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-sm">{{ old('maintenance_message', $maintenanceMessage) }}</textarea>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-slate-900 text-white text-xs font-black uppercase tracking-wider hover:bg-slate-800">
                    Enregistrer les paramètres
                </button>
            </div>
        </form>
    </div>
@endsection
