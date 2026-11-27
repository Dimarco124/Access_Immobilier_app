@extends('layouts.super-admin')

@section('title', 'Tickets Support Plateforme')
@section('subtitle', 'Demandes d\'assistance envoyées par les administrateurs d\'agences')

@section('content')
    <div class="sa-card p-4 flex flex-wrap gap-2">
        @foreach(['' => 'Tous', 'open' => 'Ouverts', 'in_progress' => 'En cours', 'resolved' => 'Résolus', 'closed' => 'Fermés'] as $key => $label)
            <a href="{{ route('super-admin.tickets.index', $key ? ['status' => $key] : []) }}"
               class="px-3.5 py-1.5 rounded-xl text-xs font-black uppercase {{ request('status', '') === $key ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    <div class="space-y-4">
        @forelse($tickets as $ticket)
            <div class="sa-card p-6 space-y-4">
                <div class="flex flex-wrap items-start justify-between gap-3">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-black text-slate-400">#{{ $ticket->id }}</span>
                            <h3 class="text-base font-black text-slate-900">{{ $ticket->subject }}</h3>
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase {{ $ticket->priority === 'urgent' ? 'bg-rose-100 text-rose-700' : ($ticket->priority === 'high' ? 'bg-amber-100 text-amber-700' : 'bg-blue-100 text-blue-700') }}">
                                {{ $ticket->priority }}
                            </span>
                        </div>
                        <p class="text-xs text-slate-500 mt-1">
                            Agence : <span class="font-bold text-slate-700">{{ $ticket->agence->nom ?? 'N/A' }}</span> •
                            Auteur : {{ $ticket->user->name ?? 'N/A' }} ({{ $ticket->user->email ?? '' }}) •
                            {{ $ticket->created_at->format('d/m/Y H:i') }}
                        </p>
                    </div>
                    <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase bg-slate-100 text-slate-700">{{ $ticket->status }}</span>
                </div>

                <div class="p-4 rounded-xl bg-slate-50 text-xs text-slate-700 whitespace-pre-line">{{ $ticket->message }}</div>

                <form method="POST" action="{{ route('super-admin.tickets.reply', $ticket) }}" class="grid grid-cols-1 md:grid-cols-4 gap-3 items-end pt-2 border-t border-slate-100">
                    @csrf
                    <div class="md:col-span-2">
                        <label class="block text-[10px] font-black uppercase text-slate-500 mb-1">Réponse Super Admin</label>
                        <textarea name="admin_reply" rows="2" placeholder="Votre réponse à l'agence..." class="w-full rounded-xl border border-slate-200 px-3 py-2 text-xs">{{ $ticket->admin_reply }}</textarea>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black uppercase text-slate-500 mb-1">Statut</label>
                        <select name="status" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-xs font-bold">
                            <option value="open" {{ $ticket->status === 'open' ? 'selected' : '' }}>Ouvert</option>
                            <option value="in_progress" {{ $ticket->status === 'in_progress' ? 'selected' : '' }}>En cours</option>
                            <option value="resolved" {{ $ticket->status === 'resolved' ? 'selected' : '' }}>Résolu</option>
                            <option value="closed" {{ $ticket->status === 'closed' ? 'selected' : '' }}>Fermé</option>
                        </select>
                    </div>
                    <button type="submit" class="px-4 py-2.5 rounded-xl bg-slate-900 text-white text-xs font-black uppercase hover:bg-slate-800">
                        Enregistrer
                    </button>
                </form>
            </div>
        @empty
            <div class="sa-card p-10 text-center text-xs text-slate-400">Aucun ticket support trouvé.</div>
        @endforelse

        @if($tickets->hasPages())
            <div>{{ $tickets->links() }}</div>
        @endif
    </div>
@endsection
