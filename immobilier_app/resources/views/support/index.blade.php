<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-black text-xl md:text-2xl text-slate-900 tracking-tight uppercase">Support Plateforme</h2>
            <p class="text-[11px] text-slate-500 font-bold uppercase tracking-widest mt-1">Contacter l'équipe technique Habitat Immobilier</p>
        </div>
    </x-slot>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="premium-card p-6 bg-white">
            <h3 class="text-sm font-black text-slate-900 uppercase mb-4">Nouvelle demande</h3>
            <form method="POST" action="{{ route('support.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-[10px] font-black uppercase text-slate-500 mb-1">Sujet *</label>
                    <input type="text" name="subject" required placeholder="Ex: Question sur la facturation..." class="w-full">
                </div>
                <div>
                    <label class="block text-[10px] font-black uppercase text-slate-500 mb-1">Priorité *</label>
                    <select name="priority" class="w-full font-bold">
                        <option value="normal">Normale</option>
                        <option value="high">Haute</option>
                        <option value="urgent">Urgente</option>
                    </select>
                </div>
                <div>
                    <label class="block text-[10px] font-black uppercase text-slate-500 mb-1">Message *</label>
                    <textarea name="message" rows="5" required placeholder="Décrivez votre besoin ou problème..."></textarea>
                </div>
                <button type="submit" class="w-full py-2.5 rounded-xl bg-blue-600 text-white text-xs font-black uppercase tracking-wider hover:bg-blue-700 transition">
                    Envoyer au support
                </button>
            </form>
        </div>

        <div class="lg:col-span-2 premium-card p-6 bg-white space-y-4">
            <h3 class="text-sm font-black text-slate-900 uppercase">Vos demandes récentes</h3>
            @forelse($tickets as $ticket)
                <div class="p-4 rounded-xl border border-slate-200 space-y-2">
                    <div class="flex items-center justify-between gap-2">
                        <h4 class="font-black text-slate-900 text-sm">#{{ $ticket->id }} — {{ $ticket->subject }}</h4>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase bg-slate-100 text-slate-700">{{ $ticket->status }}</span>
                    </div>
                    <p class="text-xs text-slate-600">{{ $ticket->message }}</p>
                    @if($ticket->admin_reply)
                        <div class="p-3 rounded-xl bg-blue-50 border border-blue-100 text-xs text-blue-900">
                            <p class="font-black text-[10px] uppercase text-blue-600 mb-1">Réponse du support</p>
                            {{ $ticket->admin_reply }}
                        </div>
                    @endif
                    <p class="text-[10px] text-slate-400">Envoyé le {{ $ticket->created_at->format('d/m/Y H:i') }}</p>
                </div>
            @empty
                <p class="text-xs text-slate-400 text-center py-8">Aucune demande envoyée pour le moment.</p>
            @endforelse

            @if($tickets->hasPages())
                <div>{{ $tickets->links() }}</div>
            @endif
        </div>
    </div>
</x-app-layout>
