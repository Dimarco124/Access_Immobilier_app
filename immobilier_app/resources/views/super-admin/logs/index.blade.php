@extends('layouts.super-admin')

@section('title', 'Journal d\'Activité & Audit')
@section('subtitle', 'Traçabilité des actions critiques et de sécurité sur la plateforme')

@section('content')
    <div class="sa-card p-5">
        <form method="GET" action="{{ route('super-admin.logs.index') }}" class="flex flex-wrap items-center gap-3">
            <input type="text" name="action" value="{{ request('action') }}" placeholder="Filtrer par action (ex: login_failed, agency_)..."
                class="w-full sm:w-72 rounded-xl border border-slate-200 px-3.5 py-2 text-xs">
            <select name="agence_id" class="rounded-xl border border-slate-200 px-3.5 py-2 text-xs font-bold">
                <option value="">Toutes les agences</option>
                @foreach($agences as $ag)
                    <option value="{{ $ag->id }}" {{ (string) request('agence_id') === (string) $ag->id ? 'selected' : '' }}>{{ $ag->nom }}</option>
                @endforeach
            </select>
            <button type="submit" class="px-4 py-2 rounded-xl bg-slate-900 text-white text-xs font-black uppercase">Filtrer</button>
        </form>
    </div>

    <div class="sa-card overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-xs">
                <thead>
                    <tr class="bg-slate-50 text-left text-[10px] font-black uppercase text-slate-500 border-b border-slate-200">
                        <th class="px-5 py-3">Date</th>
                        <th class="px-5 py-3">Action</th>
                        <th class="px-5 py-3">Description</th>
                        <th class="px-5 py-3">Agence / Utilisateur</th>
                        <th class="px-5 py-3">IP</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($logs as $log)
                        <tr class="hover:bg-slate-50">
                            <td class="px-5 py-3 whitespace-nowrap text-slate-500">{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
                            <td class="px-5 py-3 whitespace-nowrap">
                                <span class="px-2 py-0.5 rounded-full bg-indigo-50 text-indigo-700 text-[10px] font-black uppercase">{{ $log->action }}</span>
                            </td>
                            <td class="px-5 py-3 text-slate-800 font-medium">{{ $log->description }}</td>
                            <td class="px-5 py-3 text-slate-600">
                                {{ $log->agence->nom ?? 'Plateforme' }}
                                @if($log->user) <span class="text-slate-400">• {{ $log->user->email }}</span> @endif
                            </td>
                            <td class="px-5 py-3 text-slate-400">{{ $log->ip_address ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-5 py-10 text-center text-slate-400">Aucun événement enregistré.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($logs->hasPages())
            <div class="px-5 py-4 border-t border-slate-100">{{ $logs->links() }}</div>
        @endif
    </div>
@endsection
