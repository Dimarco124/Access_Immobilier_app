<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 w-full">
            <div>
                <h2 class="font-black text-2xl text-slate-900 uppercase tracking-tight">Notifications</h2>
                <p class="text-[11px] text-slate-500 font-bold uppercase tracking-widest mt-1">Toutes vos notifications</p>
            </div>
            
        
            <div class="mt-1">
                <a href="{{ route('dashboard') }}" class="text-xs font-black text-blue-600 hover:text-blue-800 uppercase tracking-widest transition-colors flex  items-start  gap-2 shrink-0 self-start inline-flex">
                <i class="fas fa-arrow-left self-start inline-flex"></i> Retour
            </a>
            </div>
        </div></x-slot>

    <div class="space-y-3">
        @if($notifications->count() > 0)
            @foreach($notifications as $notification)
                <div class="premium-card bg-white border-l-4 @if(!$notification->read) border-l-blue-600 bg-blue-50/30 @else border-l-slate-100 @endif">
                    <div class="p-4 md:p-6 flex items-start gap-3 md:gap-4">
                        <div class="w-9 h-9 md:w-10 md:h-10 rounded-full flex items-center justify-center flex-shrink-0
                            @if($notification->type === 'success') bg-emerald-100 text-emerald-600
                            @elseif($notification->type === 'warning') bg-amber-100 text-amber-600
                            @elseif($notification->type === 'danger') bg-red-100 text-red-600
                            @else bg-blue-100 text-blue-600 @endif">
                            <i class="fas fa-{{
                                $notification->type === 'success' ? 'check-circle' :
                                ($notification->type === 'warning' ? 'exclamation-triangle' :
                                ($notification->type === 'danger' ? 'times-circle' : 'info-circle'))
                            }}"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-2">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 mb-1 flex-wrap">
                                        <h3 class="text-sm font-bold text-slate-900">{{ $notification->title }}</h3>
                                        @if(!$notification->read)
                                            <span class="w-2 h-2 bg-blue-600 rounded-full animate-pulse shrink-0"></span>
                                        @endif
                                    </div>
                                    <p class="text-xs text-slate-600">{{ $notification->message }}</p>
                                    <p class="text-[10px] text-slate-400 mt-2">{{ $notification->created_at->translatedFormat('d/m/Y à H:i') }}</p>
                                </div>
                                @if($notification->action_url)
                                    <a href="{{ $notification->action_url }}" class="shrink-0 self-start px-4 py-2 bg-blue-600 text-white text-[10px] font-black uppercase tracking-widest rounded-lg hover:bg-blue-700 transition-colors flex items-center gap-2">
                                        {{ $notification->action_text ?? 'Voir' }}
                                        <i class="fas fa-arrow-right"></i>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach

            <div class="mt-6">
                {{ $notifications->links() }}
            </div>
        @else
            <div class="premium-card bg-white p-10 md:p-12 text-center">
                <i class="fas fa-inbox text-4xl md:text-5xl text-slate-300 mb-4"></i>
                <h3 class="text-lg md:text-xl font-black text-slate-900 mb-2">Aucune notification</h3>
                <p class="text-slate-500 text-sm">Vous n'avez pas encore de notifications.</p>
            </div>
        @endif
    </div>
</x-app-layout>