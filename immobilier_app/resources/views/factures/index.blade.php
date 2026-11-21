<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 w-full">
            <div>
                <h2 class="font-black text-2xl md:text-4xl text-slate-900 tracking-tight uppercase">{{ __('Paiements & Recouvrements') }}</h2>
                <p class="text-slate-500 font-medium mt-1 text-sm">Suivez l'état de vos encaissements et gérez les factures</p>
            </div>
            <div class="flex items-center gap-3 flex-wrap">
                @if(Auth::user()->hasModuleAccess('finance', 'manager'))
                <form action="{{ route('factures.force_generation') }}" method="POST">
                    @csrf
                    <button type="submit" onclick="return confirm('Êtes-vous sûr de vouloir forcer la génération de toutes les factures du mois en cours et appliquer les pénalités de retard aux factures impayées ?')" class="bg-slate-900 hover:bg-black text-white px-4 py-2.5 rounded-xl font-black shadow-lg transition transform hover:-translate-y-0.5 flex items-center gap-2 uppercase text-[10px] tracking-widest cursor-pointer">
                        <i class="fas fa-bolt text-yellow-400"></i> Traitement Manuel
                    </button>
                </form>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="space-y-6 md:space-y-12">
        <!-- Filtres de recherche -->
        <div class="bg-white p-4 md:p-6 rounded-xl shadow-sm border border-slate-100">
            <form action="{{ route('factures.index') }}" method="GET">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                    <div>
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">N° Facture</label>
                        <input type="text" name="numero" value="{{ request('numero') }}" placeholder="Ex: FAC-0001" 
                            class="w-full px-3 py-2 border border-slate-200 rounded-lg bg-slate-50 placeholder-slate-400 focus:outline-none focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm transition-colors">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Locataire</label>
                        <input type="text" name="locataire" value="{{ request('locataire') }}" placeholder="Nom du locataire" 
                            class="w-full px-3 py-2 border border-slate-200 rounded-lg bg-slate-50 placeholder-slate-400 focus:outline-none focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm transition-colors">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Bien</label>
                        <input type="text" name="bien" value="{{ request('bien') }}" placeholder="Nom du bien" 
                            class="w-full px-3 py-2 border border-slate-200 rounded-lg bg-slate-50 placeholder-slate-400 focus:outline-none focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm transition-colors">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-widest mb-1">Statut</label>
                        <select name="statut" class="w-full px-3 py-2 border border-slate-200 rounded-lg bg-slate-50 focus:outline-none focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm transition-colors">
                            <option value="">Tous</option>
                            <option value="Payée" {{ request('statut') == 'Payée' ? 'selected' : '' }}>Payée</option>
                            <option value="Impayée" {{ request('statut') == 'Impayée' ? 'selected' : '' }}>Impayée</option>
                            <option value="En retard" {{ request('statut') == 'En retard' ? 'selected' : '' }}>En retard</option>
                        </select>
                    </div>
                </div>
                <div class="flex items-center gap-3 mt-4">
                    <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-black uppercase tracking-widest rounded-lg transition-colors shadow-sm inline-flex items-center gap-2">
                        <i class="fas fa-search"></i> Rechercher
                    </button>
                    @if(request()->hasAny(['numero', 'locataire', 'bien', 'statut']))
                    <a href="{{ route('factures.index') }}" class="px-5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-black uppercase tracking-widest rounded-lg transition-colors inline-flex items-center gap-2">
                        <i class="fas fa-times"></i> Effacer
                    </a>
                    @endif
                    <a href="{{ route('factures.print', request()->query()) }}" target="_blank" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-black uppercase tracking-widest rounded-lg transition-colors inline-flex items-center gap-2">
                        <i class="fas fa-print"></i> Imprimer
                    </a>
                </div>
            </form>
        </div>

        @foreach($factures as $contratId => $facturesGroup)
        @php $contrat = $facturesGroup->first()->contrat; @endphp
        <div class="premium-card overflow-hidden">
            <!-- Header du Contrat -->
            <div class="p-4 md:p-8 bg-slate-50 border-b border-slate-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                <div class="flex items-center gap-3 md:gap-4">
                    <div class="w-10 h-10 md:w-14 md:h-14 rounded-xl md:rounded-2xl bg-white shadow-sm flex items-center justify-center text-blue-600 shrink-0">
                        <i class="fas fa-file-contract text-lg md:text-xl"></i>
                    </div>
                    <div>
                        <h3 class="text-base md:text-xl font-black text-slate-900 uppercase tracking-tight">
                            {{ $contrat->locataire->user->name }}
                        </h3>
                        <p class="text-xs text-slate-400 font-bold uppercase tracking-widest mt-0.5">
                            {{ $contrat->bien->nom_bien }}
                            @if($contrat->unite) <span class="mx-1 opacity-30">|</span> <span class="text-blue-600">{{ $contrat->unite->nom_unite }}</span> @endif
                        </p>
                    </div>
                </div>
                <span class="px-3 py-1.5 bg-blue-100 text-blue-600 rounded-xl text-[10px] font-black uppercase tracking-widest shrink-0">
                    {{ $facturesGroup->count() }} Facture(s)
                </span>
            </div>

            {{-- VUE MOBILE : cards --}}
            <div class="md:hidden divide-y divide-slate-200">
                @foreach($facturesGroup as $facture)
                <div class="p-4 space-y-3">
                    <div class="flex items-start gap-3 mb-2">
                        <div class="flex-1">
                            <span class="text-sm font-black text-slate-900">#{{ $facture->numero_facture }}</span>
                            <p class="text-xs font-bold text-slate-600 uppercase tracking-tight mt-0.5">{{ $facture->type_facture ?? 'Loyer' }}</p>
                            <p class="text-[10px] text-slate-400 uppercase tracking-widest">{{ $facture->mois_facture }}</p>
                        </div>
                        <span class="px-2 py-1 text-[10px] font-black rounded-lg uppercase inline-flex items-center gap-1 whitespace-nowrap
                            {{ $facture->statut == 'Payée' ? 'bg-green-100 text-green-600' : ($facture->statut == 'Partielle' ? 'bg-amber-100 text-amber-600' : 'bg-red-100 text-red-600') }}">
                            <i class="fas fa-circle text-[6px]"></i>{{ $facture->statut }}
                        </span>
                    </div>
                    <div class="grid grid-cols-2 gap-2 text-xs">
                        <div class="bg-slate-50 rounded-lg p-2">
                            <p class="text-[10px] text-slate-400 uppercase font-black">Total dû</p>
                            <p class="font-black text-slate-900 mt-0.5">{{ number_format($facture->montant_total_du, 0, ',', ' ') }} <span class="text-slate-400 text-[10px]">{{ Auth::user()->agence->devise }}</span></p>
                        </div>
                        <div class="bg-slate-50 rounded-lg p-2">
                            <p class="text-[10px] text-slate-400 uppercase font-black">Reste</p>
                            <p class="font-black mt-0.5 {{ ($facture->montant_total_du - $facture->montant_deja_paye) > 0 ? 'text-red-600' : 'text-green-600' }}">
                                {{ number_format($facture->montant_total_du - $facture->montant_deja_paye, 0, ',', ' ') }}
                                <span class="text-slate-400 text-[10px]">{{ Auth::user()->agence->devise }}</span>
                            </p>
                        </div>
                    </div>
                    @if($facture->montant_frais_retard > 0)
                    <p class="text-[10px] text-red-500 font-black uppercase">+ {{ number_format($facture->montant_frais_retard, 0, ',', ' ') }} pénalités</p>
                    @endif
                    <div class="pt-1">
                        @if($facture->statut !== 'Payée')
                        <button onclick="openPaymentModal({{ $facture->id }}, '{{ $facture->numero_facture }}', {{ $facture->montant_total_du }}, {{ $facture->montant_deja_paye }}, '{{ $facture->type_facture }}', '{{ $facture->mois_facture }}')"
                            class="w-full h-10 rounded-xl bg-blue-600 text-white text-xs font-black uppercase tracking-widest hover:bg-blue-700 transition-all">
                            Encaisser
                        </button>
                        @else
                        <a href="{{ route('factures.recu', $facture) }}" class="w-full h-10 rounded-xl bg-green-50 text-green-600 text-xs font-black uppercase tracking-widest hover:bg-green-100 transition-all flex items-center justify-center gap-2">
                            <i class="fas fa-file-invoice-dollar"></i> Voir le Reçu
                        </a>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>

            {{-- VUE DESKTOP : tableau --}}
            <div class="hidden md:block overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-white whitespace-nowrap px-6 py-5">
                        <tr class="">
                            <th class="text-left text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] whitespace-nowrap px-6 py-5">N° Facture</th>
                            <th class="text-left text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] whitespace-nowrap px-6 py-5">Type & Période</th>
                            <th class="text-left text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] whitespace-nowrap px-6 py-5">Situation Financière</th>
                            <th class="text-left text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] whitespace-nowrap px-6 py-5">Statut</th>
                            <th class="text-right text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] whitespace-nowrap px-6 py-5">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-slate-200">
                        @foreach($facturesGroup as $facture)
                        <tr class="hover:bg-slate-50 transition-colors group ">
                            <td class="whitespace-nowrap px-6 py-5">
                                <span class="text-sm font-black text-slate-900">#{{ $facture->numero_facture }}</span>
                            </td>
                            <td class="whitespace-nowrap px-6 py-5">
                                <div class="flex flex-col">
                                    <span class="text-xs font-black text-slate-700 uppercase tracking-tighter">{{ $facture->type_facture ?? 'Loyer' }}</span>
                                    <span class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-1">{{ $facture->mois_facture }}</span>
                                </div>
                            </td>
                            <td class="whitespace-nowrap px-6 py-5">
                                <div class="flex flex-col">
                                    <div class="text-sm font-black text-slate-900">
                                        {{ number_format($facture->montant_total_du, 0, ',', ' ') }} <span class="text-[10px] text-slate-400 uppercase">{{ Auth::user()->agence->devise }}</span>
                                    </div>
                                    @if($facture->montant_deja_paye > 0)
                                    <div class="text-[10px] text-green-500 font-bold uppercase mt-1">
                                        Déjà réglé: {{ number_format($facture->montant_deja_paye, 0, ',', ' ') }}
                                    </div>
                                    @endif
                                    @if($facture->montant_frais_retard > 0)
                                    <div class="text-[10px] text-red-500 font-black uppercase mt-1">
                                        + {{ number_format($facture->montant_frais_retard, 0, ',', ' ') }} pénalités
                                    </div>
                                    @endif
                                </div>
                            </td>
                            <td class="whitespace-nowrap px-6 py-5">
                                <span class="px-4 py-1.5 inline-flex text-[10px] font-black rounded-xl uppercase tracking-widest
                                    {{ $facture->statut == 'Payée' ? 'bg-green-100 text-green-600' : ($facture->statut == 'Partielle' ? 'bg-amber-100 text-amber-600' : 'bg-red-100 text-red-600') }}">
                                    <i class="fas fa-circle text-[6px] mr-2 mt-1"></i>
                                    {{ $facture->statut }}
                                </span>
                            </td>
                            <td class="whitespace-nowrap text-right whitespace-nowrap px-6 py-5">
                                @if($facture->statut !== 'Payée')
                                <button onclick="openPaymentModal({{ $facture->id }}, '{{ $facture->numero_facture }}', {{ $facture->montant_total_du }}, {{ $facture->montant_deja_paye }}, '{{ $facture->type_facture }}', '{{ $facture->mois_facture }}')" class="btn-premium bg-blue-600 text-white px-6 py-3 text-[10px] font-black uppercase tracking-widest hover:bg-blue-700 shadow-lg shadow-blue-100 transition-all">
                                    Encaisser
                                </button>
                                @else
                                <a href="{{ route('factures.recu', $facture) }}" class="inline-flex items-center gap-2 text-[10px] font-black text-green-600 uppercase tracking-widest hover:text-green-700 transition-colors">
                                    <i class="fas fa-file-invoice-dollar text-sm"></i> Voir le Reçu
                                </a>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Modal Paiement Premium -->
    <div id="modal-paiement" class="hidden fixed inset-0 bg-slate-900/60 backdrop-blur-sm overflow-y-auto h-full w-full z-50 flex items-center justify-center p-4">
        <div class="relative mx-auto my-auto p-3 md:p-0 border-0 w-full max-w-lg shadow-2xl rounded-[2rem] bg-white overflow-hidden">
            <div class="bg-slate-900 p-6 md:p-10 text-white relative">
                <div class="absolute top-0 right-0 p-8 opacity-10">
                    <i class="fas fa-wallet text-8xl"></i>
                </div>
                <div class="relative z-10">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-xl md:text-2xl font-black uppercase tracking-tighter" id="modal-title">Encaissement</h3>
                        <button onclick="document.getElementById('modal-paiement').classList.add('hidden')" class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center hover:bg-white/20 transition-colors">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="px-3 py-1 bg-blue-500 rounded-lg text-[10px] font-black uppercase tracking-widest" id="modal-subtitle">FACT #0000</span>
                        <span class="text-slate-400 text-xs font-bold uppercase tracking-widest" id="modal-period">JANVIER 2024</span>
                    </div>
                </div>
            </div>

            <form id="payment-form" method="POST" class="p-6 md:p-10 space-y-6">
                @csrf
                <div class="grid grid-cols-2 gap-4 p-4 bg-slate-50 rounded-2xl border border-slate-100">
                    <div>
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Montant Total</p>
                        <p id="modal-total" class="text-lg font-black text-slate-900">0</p>
                    </div>
                    <div class="text-right">
                        <p class="text-[10px] font-black text-red-400 uppercase tracking-widest mb-1">Reste à payer</p>
                        <p id="modal-reste" class="text-lg font-black text-red-600">0</p>
                    </div>
                </div>

                <div class="space-y-5">
                    <div>
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] mb-2">Montant à encaisser</label>
                        <div class="relative">
                            <input type="number" name="montant_verse" id="montant_verse" class="block w-full pl-4 pr-20 bg-slate-50 border-slate-100 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent font-black text-2xl text-blue-700" required>
                            <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                                <span class="text-slate-400 font-black text-xs uppercase tracking-widest">{{ Auth::user()->agence->devise }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] mb-2">Mode de règlement</label>
                            <select name="mode_paiement" class="block w-full bg-slate-50 border-slate-100 rounded-xl text-sm font-bold text-slate-700" required>
                                <option value="Espèces">Espèces</option>
                                <option value="Chèque">Chèque</option>
                                <option value="Virement">Virement</option>
                                <option value="Mobile Money">Mobile Money</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] mb-2">Date de réception</label>
                            <input type="date" name="date_paiement" value="{{ date('Y-m-d') }}" class="block w-full bg-slate-50 border-slate-100 rounded-xl text-sm font-bold text-slate-700" required>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-black text-slate-500 uppercase tracking-[0.2em] mb-2">Référence / N° de transaction</label>
                        <input type="text" name="reference_transaction" class="block w-full bg-slate-50 border-slate-100 rounded-xl text-sm font-bold text-slate-700" placeholder="Ex: N° Chèque, Réf Orange Money...">
                    </div>
                </div>

                <div class="space-y-3">
                    <button type="submit" class="w-full btn-premium bg-blue-600 text-white py-4 text-sm font-black uppercase tracking-widest hover:bg-blue-700 shadow-xl shadow-blue-200 transition transform hover:-translate-y-0.5">
                        Valider l'Encaissement
                    </button>
                    <button type="button" onclick="document.getElementById('modal-paiement').classList.add('hidden')" class="w-full py-3 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] hover:text-slate-600 transition-colors">
                        Annuler
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openPaymentModal(id, numero, total, paye, type, mois) {
            const reste = total - paye;
            const devise = "{{ Auth::user()->agence->devise }}";
            const montantInput = document.getElementById('montant_verse');

            document.getElementById('modal-paiement').classList.remove('hidden');
            document.getElementById('modal-title').innerText = "Encaissement " + type;
            document.getElementById('modal-subtitle').innerText = "FACT #" + numero;
            document.getElementById('modal-period').innerText = mois.toUpperCase();
            document.getElementById('modal-total').innerText = total.toLocaleString('fr-FR') + " " + devise;
            document.getElementById('modal-reste').innerText = reste.toLocaleString('fr-FR') + " " + devise;

            montantInput.value = reste;

            if (type === 'Initial') {
                montantInput.readOnly = true;
                montantInput.classList.add('bg-slate-100', 'cursor-not-allowed');
            } else {
                montantInput.readOnly = false;
                montantInput.classList.remove('bg-slate-100', 'cursor-not-allowed');
                montantInput.max = reste;
            }

            document.getElementById('payment-form').action = '/factures/' + id + '/payer';
        }
    </script>
</x-app-layout>