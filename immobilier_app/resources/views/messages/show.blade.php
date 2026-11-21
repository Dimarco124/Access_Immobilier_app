<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 w-full">
            
            <div class="min-w-0">
                <h2 class="font-black text-lg md:text-2xl text-slate-900 tracking-tight uppercase truncate">
                    {{ $destinataire->name }}
                </h2>
                <p class="text-[10px] text-slate-500 font-bold uppercase tracking-widest">
                    {{ $destinataire->email }}
                    <span class="mx-1 text-slate-300">•</span>
                    @if($destinataire->hasRole('admin')) Agence
                    @elseif($destinataire->hasRole('proprietaire')) Propriétaire
                    @elseif($destinataire->hasRole('locataire')) Locataire
                    @endif
                </p>
            </div>
        
            <div class="mt-1">
                <a href="{{ route('messages.index') }}" class="w-9 h-9 rounded-xl bg-white border border-slate-200 flex  items-start  justify-center text-slate-400 hover:text-slate-900 hover:border-slate-300 transition-all shrink-0 self-start inline-flex">
                <i class="fas fa-arrow-left text-xs self-start inline-flex"></i>
            </a>
            </div>
        </div></x-slot>

    <div class="space-y-4">
        @if(session('success'))
        <div class="premium-card bg-emerald-50 border-emerald-100 p-4 flex items-center gap-3">
            <div class="w-8 h-8 bg-emerald-500 rounded-lg flex items-center justify-center text-white text-xs shrink-0">
                <i class="fas fa-check"></i>
            </div>
            <p class="text-emerald-700 text-xs font-bold">{{ session('success') }}</p>
        </div>
        @endif

        <!-- Messages Area -->
        <div class="premium-card bg-white overflow-hidden flex flex-col" style="height: calc(100vh - 220px); min-height: 400px;">
            <div class="flex-1 overflow-y-auto p-4 md:p-6 space-y-4 md:space-y-6" id="messages-container">
                @foreach($messages as $message)
                @php $estMonMessage = $message->expediteur_id === Auth::id(); @endphp
                <div class="flex {{ $estMonMessage ? 'justify-end' : 'justify-start' }}">
                    <div class="max-w-[85%] md:max-w-[75%] flex items-end gap-2 md:gap-3 {{ $estMonMessage ? 'flex-row-reverse' : 'flex-row' }}">
                        <!-- Avatar -->
                        <div class="shrink-0 w-7 h-7 md:w-8 md:h-8 rounded-lg flex items-center justify-center font-black text-[10px] uppercase {{ $estMonMessage ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-600' }}">
                            {{ substr($message->expediteur->name, 0, 1) }}
                        </div>
                        <!-- Bubble -->
                        <div class="space-y-1">
                            <div class="px-3 py-2.5 md:px-4 md:py-3 rounded-2xl shadow-sm border {{ $estMonMessage ? 'bg-slate-900 border-slate-800 text-white' : 'bg-slate-50 border-slate-100 text-slate-800' }}">
                                @if($message->objet && $loop->first || ($loop->index > 0 && $messages[$loop->index - 1]->objet !== $message->objet))
                                <div class="text-[9px] font-black uppercase tracking-widest mb-2 opacity-50">
                                    OBJET : {{ $message->objet }}
                                </div>
                                @endif
                                <p class="text-sm font-medium leading-relaxed">{{ $message->contenu }}</p>
                                @if($message->piece_jointe_url)
                                <div class="mt-2 pt-2 border-t {{ $estMonMessage ? 'border-white/10' : 'border-slate-200' }}">
                                    <a href="{{ Storage::url($message->piece_jointe_url) }}" target="_blank" class="flex items-center gap-2 text-[10px] font-black uppercase tracking-widest hover:opacity-70 transition-opacity">
                                        <i class="fas fa-paperclip"></i> Piece Jointe
                                    </a>
                                </div>
                                @endif
                            </div>
                            <div class="text-[8px] font-black uppercase tracking-widest text-slate-400 {{ $estMonMessage ? 'text-right' : 'text-left' }}">
                                {{ $message->created_at->diffForHumans() }}
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Response Form -->
            <div class="p-3 md:p-6 bg-slate-50 border-t border-slate-100 shrink-0">
                <form action="{{ route('messages.reply', $destinataire->id) }}" method="POST" enctype="multipart/form-data" class="space-y-3">
                    @csrf
                    <textarea name="contenu" rows="2"
                        class="w-full text-xs font-bold border-slate-200 rounded-xl focus:ring-slate-900 focus:border-slate-900 bg-white resize-none"
                        placeholder="Votre réponse ici..."
                        required>{{ old('contenu') }}</textarea>
                    <div class="flex items-center justify-between gap-3">
                        <label for="piece_jointe" class="inline-flex items-center gap-2 px-3 py-2 bg-white border border-slate-200 rounded-lg cursor-pointer hover:bg-slate-50 transition-all">
                            <i class="fas fa-paperclip text-slate-400 text-xs"></i>
                            <span class="text-[9px] font-black text-slate-500 uppercase tracking-widest hidden sm:inline">Ajouter un document</span>
                            <input type="file" name="piece_jointe" id="piece_jointe" class="hidden">
                        </label>
                        <button type="submit" class="px-5 py-2.5 bg-slate-900 text-white text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-black shadow-lg transition-all flex items-center gap-2">
                            <i class="fas fa-paper-plane"></i> <span class="hidden sm:inline">Envoyer</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const container = document.getElementById('messages-container');
            if (container) container.scrollTop = container.scrollHeight;
        });
    </script>
</x-app-layout>