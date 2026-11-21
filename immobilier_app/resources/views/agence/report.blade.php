<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col items-start gap-4 ">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Rapport Financier Agence') }}
            </h2>
            <a href="{{ route('dashboard') }}" class="text-sm font-bold text-gray-500 hover:text-gray-700">
                ← Retour au tableau de bord
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

            <!-- Guide Explicatif Agence -->
            <div class="bg-gradient-to-r from-indigo-600 to-blue-700 rounded-2xl shadow-xl overflow-hidden text-white">
                <div class="p-8 md:flex items-center justify-between">
                    <div class="md:w-2/3">
                        <h3 class="text-2xl font-black mb-4 uppercase tracking-tight">Performance de l'Agence</h3>
                        <p class="text-indigo-100 leading-relaxed mb-6">
                            Ce rapport présente la santé financière de votre agence. Il distingue le flux de trésorerie total (Chiffre d'Affaires)
                            de vos revenus réels basés sur les commissions de gestion.
                        </p>
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div class="bg-white/10 backdrop-blur-md p-4 rounded-xl border border-white/20">
                                <span class="block text-[10px] font-black uppercase text-indigo-200">Volume d'Affaires</span>
                                <span class="text-xs">Total des fonds collectés (Loyers + Visites)</span>
                            </div>
                            <div class="bg-white/10 backdrop-blur-md p-4 rounded-xl border border-white/20">
                                <span class="block text-[10px] font-black uppercase text-indigo-200">Commissions Loyers</span>
                                <span class="text-sm">{{ number_format($totals['commissions_loyers'], 0, ',', ' ') }}</span>
                            </div>
                            <div class="bg-white/10 backdrop-blur-md p-4 rounded-xl border border-white/20">
                                <span class="block text-[10px] font-black uppercase text-indigo-200">Frais de Visites</span>
                                <span class="text-sm">{{ number_format($totals['frais_visites'], 0, ',', ' ') }}</span>
                            </div>
                            <div class="bg-white/10 backdrop-blur-md p-4 rounded-xl border border-white/20">
                                <span class="block text-[10px] font-black uppercase text-indigo-200">Revenu Net Agence</span>
                                <span class="text-sm font-black text-yellow-300">Total de vos commissions</span>
                            </div>
                        </div>
                    </div>
                    <div class="hidden md:block">
                        <i class="fas fa-university text-8xl text-white/20"></i>
                    </div>
                </div>
            </div>

            <!-- Résumé des Totaux -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white p-6 rounded-2xl shadow-sm border-b-4 border-gray-200">
                    <span class="text-xs font-black text-gray-500 uppercase block mb-1">Chiffre d'Affaires Global</span>
                    <span class="text-3xl font-black text-gray-900">{{ number_format($totals['chiffre_affaires'], 0, ',', ' ') }} <span class="text-sm font-normal">{{ Auth::user()->agence->devise }}</span></span>
                </div>
                <div class="bg-white p-6 rounded-2xl shadow-sm border-b-4 border-indigo-500">
                    <span class="text-xs font-black text-indigo-500 uppercase block mb-1">Revenu Net Agence (Commissions + Visites)</span>
                    <span class="text-3xl font-black text-indigo-600">{{ number_format($totals['commissions_total'], 0, ',', ' ') }} <span class="text-sm font-normal">{{ Auth::user()->agence->devise }}</span></span>
                </div>
                <div class="bg-white p-6 rounded-2xl shadow-sm border-b-4 border-green-500">
                    <span class="text-xs font-black text-green-600 uppercase block mb-1">Part Nette Propriétaires</span>
                    <span class="text-3xl font-black text-green-700">{{ number_format($totals['chiffre_affaires'] - $totals['commissions_total'], 0, ',', ' ') }} <span class="text-sm font-normal">{{ Auth::user()->agence->devise }}</span></span>
                </div>
            </div>

            <!-- Détails par Mois -->
            <div class="space-y-6">
                <div class="flex justify-between items-center">
                    <h3 class="font-black text-gray-900 uppercase tracking-wider">Historique Mensuel</h3>
                    <button onclick="window.print()" class="text-sm font-bold text-indigo-600 hover:text-indigo-800 flex items-center gap-2">
                        <i class="fas fa-print"></i> Imprimer le rapport agence
                    </button>
                </div>

                @foreach($paiementsParMois as $mois => $groupe)
                <div class="bg-white shadow-sm sm:rounded-2xl overflow-hidden border border-gray-100">
                    <div class="p-4 bg-gray-50 border-b flex justify-between items-center">
                        <h4 class="font-black text-gray-900 uppercase text-sm">{{ $mois }}</h4>
                        <div class="text-right">
                            <span class="text-[10px] font-black text-indigo-400 uppercase block">Commissions du mois</span>
                            <span class="text-lg font-black text-indigo-600">{{ number_format($groupe->sum('commission_agence'), 0, ',', ' ') }} {{ Auth::user()->agence->devise }}</span>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-white whitespace-nowrap px-6 py-5">
                                <tr class="">
                                    <th class="text-left text-[9px] font-black text-gray-400 uppercase whitespace-nowrap px-6 py-5">Date</th>
                                    <th class="text-left text-[9px] font-black text-gray-400 uppercase whitespace-nowrap px-6 py-5">Bien / Locataire</th>
                                    <th class="text-right text-[9px] font-black text-gray-400 uppercase whitespace-nowrap px-6 py-5">Brut</th>
                                    <th class="text-right text-[9px] font-black text-gray-400 uppercase whitespace-nowrap px-6 py-5">Com. Agence</th>
                                    <th class="text-right text-[9px] font-black text-gray-400 uppercase whitespace-nowrap px-6 py-5">Net Proprio</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach($groupe as $p)
                                <tr class="hover:bg-indigo-50/20 transition ">
                                    <td class="whitespace-nowrap text-xs text-gray-500 whitespace-nowrap px-6 py-5">{{ $p->date_paiement->format('d/m/Y') }}</td>
                                    <td class="whitespace-nowrap px-6 py-5">
                                        <div class="text-xs font-bold text-gray-700 uppercase">{{ $p->facture->contrat->bien->nom_bien }}</div>
                                        <div class="text-[10px] text-gray-400 italic">{{ $p->facture->contrat->locataire->user->name }}</div>
                                    </td>
                                    <td class="text-right text-xs font-medium text-gray-600 whitespace-nowrap px-6 py-5">{{ number_format($p->montant_verse, 0, ',', ' ') }}</td>
                                    <td class="text-right text-xs font-black text-indigo-600 whitespace-nowrap px-6 py-5">{{ number_format($p->commission_agence, 0, ',', ' ') }}</td>
                                    <td class="text-right text-xs text-gray-400 whitespace-nowrap px-6 py-5">{{ number_format($p->montant_net_proprietaire, 0, ',', ' ') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Footer Explicatif -->
            <div class="bg-gray-50 p-6 rounded-2xl border-2 border-dashed border-gray-200 text-center text-gray-500 text-sm italic leading-relaxed">
                Ce rapport est destiné à l'usage interne de la direction de l'agence. Il permet de valider les montants collectés avant reversement aux propriétaires.
            </div>

        </div>
    </div>
</x-app-layout>