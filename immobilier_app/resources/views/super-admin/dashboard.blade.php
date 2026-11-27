@extends('layouts.super-admin')

@section('title', 'Vue d\'ensemble SaaS')
@section('subtitle', 'Pilotage global de la plateforme Habitat Immobilier')

@section('content')
    {{-- KPIs Principaux --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="sa-card p-5 border-l-4 border-l-blue-600">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Agences inscrites</p>
                    <p class="text-3xl font-black text-slate-900 mt-1">{{ $stats['total_agences'] }}</p>
                    <p class="text-[11px] font-bold text-emerald-600 mt-1">
                        {{ $stats['agences_actives'] }} actives • {{ $stats['agences_suspendues'] }} suspendues
                    </p>
                </div>
                <div class="w-11 h-11 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                    <i class="fas fa-building text-lg"></i>
                </div>
            </div>
        </div>

        <div class="sa-card p-5 border-l-4 border-l-indigo-600">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Utilisateurs</p>
                    <p class="text-3xl font-black text-slate-900 mt-1">{{ $stats['total_users'] }}</p>
                    <p class="text-[11px] font-bold text-slate-500 mt-1">
                        +{{ $stats['nouvelles_agences_mois'] }} agence(s) ce mois
                    </p>
                </div>
                <div class="w-11 h-11 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                    <i class="fas fa-users text-lg"></i>
                </div>
            </div>
        </div>

        <div class="sa-card p-5 border-l-4 border-l-emerald-600">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Parc & Contrats</p>
                    <p class="text-3xl font-black text-slate-900 mt-1">{{ $stats['total_biens'] }}</p>
                    <p class="text-[11px] font-bold text-emerald-600 mt-1">
                        {{ $stats['total_contrats_actifs'] }} contrat(s) actif(s)
                    </p>
                </div>
                <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <i class="fas fa-file-contract text-lg"></i>
                </div>
            </div>
        </div>

        <div class="sa-card p-5 border-l-4 border-l-amber-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Volume Loyers (Mois)</p>
                    <p class="text-2xl font-black text-slate-900 mt-1">
                        {{ number_format($stats['volume_mois'], 0, ',', ' ') }}
                        <span class="text-xs font-bold text-slate-400">XOF</span>
                    </p>
                    <p class="text-[11px] font-bold text-slate-500 mt-1">
                        Cumul : {{ number_format($stats['volume_total'], 0, ',', ' ') }} XOF
                    </p>
                </div>
                <div class="w-11 h-11 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                    <i class="fas fa-coins text-lg"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Bandeau raccourcis / état plateforme --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
        <div class="sa-card p-4 flex items-center gap-3">
            <span class="w-9 h-9 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center"><i class="fas fa-user-tie"></i></span>
            <div>
                <p class="text-[10px] font-black uppercase text-slate-400">Propriétaires</p>
                <p class="text-base font-black text-slate-900">{{ $stats['total_proprietaires'] }}</p>
            </div>
        </div>
        <div class="sa-card p-4 flex items-center gap-3">
            <span class="w-9 h-9 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center"><i class="fas fa-user-check"></i></span>
            <div>
                <p class="text-[10px] font-black uppercase text-slate-400">Locataires</p>
                <p class="text-base font-black text-slate-900">{{ $stats['total_locataires'] }}</p>
            </div>
        </div>
        <a href="{{ route('super-admin.tickets.index') }}" class="sa-card p-4 flex items-center gap-3 hover:border-rose-200 transition">
            <span class="w-9 h-9 rounded-xl {{ $stats['tickets_ouverts'] > 0 ? 'bg-rose-50 text-rose-600' : 'bg-emerald-50 text-emerald-600' }} flex items-center justify-center"><i class="fas fa-life-ring"></i></span>
            <div>
                <p class="text-[10px] font-black uppercase text-slate-400">Tickets ouverts</p>
                <p class="text-base font-black text-slate-900">{{ $stats['tickets_ouverts'] }} @if($stats['tickets_urgents'] > 0)<span class="text-xs text-rose-600">({{ $stats['tickets_urgents'] }} urgent)</span>@endif</p>
            </div>
        </a>
        <a href="{{ route('super-admin.announcements.index') }}" class="sa-card p-4 flex items-center gap-3 hover:border-blue-200 transition">
            <span class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center"><i class="fas fa-bullhorn"></i></span>
            <div>
                <p class="text-[10px] font-black uppercase text-slate-400">Annonces actives</p>
                <p class="text-base font-black text-slate-900">{{ $stats['annonces_actives'] }}</p>
            </div>
        </a>
    </div>

    {{-- Graphique Croissance & Volume + Alertes --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 sa-card p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-sm font-black uppercase tracking-tight text-slate-900">Croissance & Volume (12 mois)</h2>
                    <p class="text-xs text-slate-500">Nouvelles agences inscrites et volume financier traité</p>
                </div>
            </div>
            <div id="saasGrowthChart" style="min-height: 290px;"></div>
        </div>

        <div class="sa-card p-6 space-y-5">
            <div class="flex items-center justify-between">
                <h2 class="text-sm font-black uppercase tracking-tight text-slate-900">Alertes & Support</h2>
                <a href="{{ route('super-admin.tickets.index') }}" class="text-xs font-bold text-blue-600 hover:underline">Voir tout</a>
            </div>

            @if($ticketsRecents->isNotEmpty())
                <div class="space-y-2.5">
                    @foreach($ticketsRecents as $ticket)
                        <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                            <div class="flex items-center justify-between gap-2">
                                <span class="text-xs font-black text-slate-800 truncate">{{ $ticket->subject }}</span>
                                <span class="px-2 py-0.5 rounded-full text-[9px] font-black uppercase {{ $ticket->priority === 'urgent' ? 'bg-rose-100 text-rose-700' : ($ticket->priority === 'high' ? 'bg-amber-100 text-amber-700' : 'bg-blue-100 text-blue-700') }}">
                                    {{ $ticket->priority }}
                                </span>
                            </div>
                            <p class="text-[11px] text-slate-500 mt-1">{{ $ticket->agence->nom ?? 'Plateforme' }} • {{ $ticket->created_at->diffForHumans() }}</p>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-xs text-slate-400 py-4 text-center">Aucun ticket en attente.</p>
            @endif

            @if($agencesSuspendues->isNotEmpty())
                <div class="pt-3 border-t border-slate-100">
                    <p class="text-[10px] font-black uppercase tracking-widest text-rose-600 mb-2">Agences suspendues</p>
                    <div class="space-y-2">
                        @foreach($agencesSuspendues as $ag)
                            <div class="flex items-center justify-between text-xs">
                                <a href="{{ route('super-admin.agencies.show', $ag) }}" class="font-bold text-slate-700 hover:text-blue-600">{{ $ag->nom }}</a>
                                <span class="text-[10px] text-slate-400">{{ $ag->updated_at->diffForHumans() }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Top Agences & Journal récent --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="sa-card p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-sm font-black uppercase tracking-tight text-slate-900">Top Agences</h2>
                <a href="{{ route('super-admin.agencies.index') }}" class="text-xs font-bold text-blue-600 hover:underline">Toutes les agences</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-xs">
                    <thead>
                        <tr class="text-left text-[10px] font-black uppercase text-slate-400 border-b border-slate-100">
                            <th class="pb-2">Agence</th>
                            <th class="pb-2 text-center">Biens</th>
                            <th class="pb-2 text-center">Contrats</th>
                            <th class="pb-2 text-right">Statut</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($topAgences as $ag)
                            <tr>
                                <td class="py-3">
                                    <a href="{{ route('super-admin.agencies.show', $ag) }}" class="font-black text-slate-800 hover:text-blue-600">{{ $ag->nom }}</a>
                                    <p class="text-[10px] text-slate-400">{{ $ag->email }}</p>
                                </td>
                                <td class="py-3 text-center font-bold text-slate-700">{{ $ag->biens_count }}</td>
                                <td class="py-3 text-center font-bold text-slate-700">{{ $ag->contrats_count }}</td>
                                <td class="py-3 text-right">
                                    <span class="px-2.5 py-1 rounded-full text-[9px] font-black uppercase {{ $ag->statut === 'actif' ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }}">
                                        {{ $ag->statut }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="py-6 text-center text-slate-400">Aucune agence enregistrée.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="sa-card p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-sm font-black uppercase tracking-tight text-slate-900">Activité récente</h2>
                <a href="{{ route('super-admin.logs.index') }}" class="text-xs font-bold text-blue-600 hover:underline">Journal complet</a>
            </div>
            <div class="space-y-3">
                @forelse($recentLogs as $log)
                    <div class="flex items-start justify-between gap-3 text-xs border-b border-slate-100 pb-2.5 last:border-0">
                        <div>
                            <p class="font-bold text-slate-800">{{ $log->description }}</p>
                            <p class="text-[10px] text-slate-400 mt-0.5">
                                <span class="font-bold uppercase text-indigo-600">{{ $log->action }}</span>
                                @if($log->ip_address) • {{ $log->ip_address }} @endif
                            </p>
                        </div>
                        <span class="text-[10px] text-slate-400 whitespace-nowrap">{{ $log->created_at->diffForHumans() }}</span>
                    </div>
                @empty
                    <p class="text-xs text-slate-400 text-center py-6">Aucune activité enregistrée pour le moment.</p>
                @endforelse
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const el = document.querySelector('#saasGrowthChart');
    if (!el || typeof ApexCharts === 'undefined') return;

    const options = {
        chart: { type: 'line', height: 290, toolbar: { show: false }, fontFamily: 'inherit' },
        series: [
            { name: 'Volume Loyers (XOF)', type: 'column', data: @json($volumeParMois) },
            { name: 'Nouvelles Agences', type: 'line', data: @json($agencesParMois) }
        ],
        stroke: { width: [0, 3], curve: 'smooth' },
        colors: ['#3b82f6', '#10b981'],
        labels: @json($moisLabels),
        yaxis: [
            { title: { text: 'Volume (XOF)' }, labels: { formatter: v => Math.round(v).toLocaleString('fr-FR') } },
            { opposite: true, title: { text: 'Agences' }, labels: { formatter: v => Math.round(v) } }
        ],
        dataLabels: { enabled: false },
        legend: { position: 'top' }
    };
    new ApexCharts(el, options).render();
});
</script>
@endpush
