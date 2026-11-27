@extends('layouts.super-admin')

@section('title', 'Annonces Globales')
@section('subtitle', 'Diffuser un message sur le tableau de bord de toutes les agences')

@section('content')
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="sa-card p-6">
            <h2 class="text-sm font-black uppercase text-slate-900 mb-4">Nouvelle annonce</h2>
            <form method="POST" action="{{ route('super-admin.announcements.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-[10px] font-black uppercase text-slate-500 mb-1">Titre *</label>
                    <input type="text" name="title" required placeholder="Ex: Nouvelle mise à jour disponible" class="w-full rounded-xl border border-slate-200 px-3.5 py-2 text-xs">
                </div>
                <div>
                    <label class="block text-[10px] font-black uppercase text-slate-500 mb-1">Type *</label>
                    <select name="type" class="w-full rounded-xl border border-slate-200 px-3.5 py-2 text-xs font-bold">
                        <option value="info">Information (Bleu)</option>
                        <option value="success">Nouveauté / Succès (Vert)</option>
                        <option value="warning">Avertissement (Orange)</option>
                        <option value="danger">Alerte Critique (Rouge)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-black uppercase text-slate-500 mb-1">Message *</label>
                    <textarea name="message" rows="4" required placeholder="Contenu visible par toutes les agences..." class="w-full rounded-xl border border-slate-200 px-3.5 py-2 text-xs"></textarea>
                </div>
                <div>
                    <label class="block text-[10px] font-black uppercase text-slate-500 mb-1">Date de fin (optionnel)</label>
                    <input type="datetime-local" name="ends_at" class="w-full rounded-xl border border-slate-200 px-3.5 py-2 text-xs">
                </div>
                <button type="submit" class="w-full py-2.5 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 text-white text-xs font-black uppercase tracking-wider">
                    Publier l'annonce
                </button>
            </form>
        </div>

        <div class="lg:col-span-2 sa-card p-6 space-y-4">
            <h2 class="text-sm font-black uppercase text-slate-900">Historique des annonces</h2>
            @forelse($announcements as $a)
                <div class="p-4 rounded-xl border border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase bg-slate-100 text-slate-700">{{ $a->type }}</span>
                            <h3 class="text-sm font-black text-slate-900">{{ $a->title }}</h3>
                            <span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase {{ $a->is_active ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                                {{ $a->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                        <p class="text-xs text-slate-600 mt-1">{{ $a->message }}</p>
                        <p class="text-[10px] text-slate-400 mt-1">Créée le {{ $a->created_at->format('d/m/Y H:i') }} @if($a->ends_at) • Expire le {{ $a->ends_at->format('d/m/Y H:i') }} @endif</p>
                    </div>
                    <div class="flex items-center gap-2 shrink-0">
                        <form method="POST" action="{{ route('super-admin.announcements.toggle', $a) }}">
                            @csrf
                            <button type="submit" class="px-3 py-1.5 rounded-lg bg-slate-100 text-slate-700 text-xs font-bold hover:bg-slate-200">
                                {{ $a->is_active ? 'Désactiver' : 'Activer' }}
                            </button>
                        </form>
                        <form method="POST" action="{{ route('super-admin.announcements.destroy', $a) }}" onsubmit="return confirm('Supprimer cette annonce ?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-3 py-1.5 rounded-lg bg-rose-50 text-rose-600 text-xs font-bold hover:bg-rose-100">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <p class="text-xs text-slate-400 text-center py-8">Aucune annonce publiée.</p>
            @endforelse
            @if($announcements->hasPages())
                <div class="pt-3">{{ $announcements->links() }}</div>
            @endif
        </div>
    </div>
@endsection
