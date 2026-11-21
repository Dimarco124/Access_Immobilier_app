<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Enregistrer une Nouvelle Visite') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('visites.store') }}" class="space-y-6">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="locataire_id" :value="__('Locataire *')" />
                            <select id="locataire_id" name="locataire_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="">Sélectionnez un locataire</option>
                                @foreach($locataires as $locataire)
                                <option value="{{ $locataire->id }}" @selected(old('locataire_id')==$locataire->id)>
                                    {{ $locataire->user->name }} ({{ $locataire->user->email }})
                                </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('locataire_id')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="selection_unite" :value="__('Bien / Unité *')" />
                            <select id="selection_unite" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required onchange="updateBienAndUnite(this)">
                                <option value="">Sélectionnez un bien ou une unité</option>
                                @foreach($proprietaires as $proprietaire)
                                @foreach($proprietaire->biens as $bien)
                                @if($bien->children->count() === 0)
                                <!-- Bien principal sans sous-biens (chercher son unité correspondante) -->
                                @php $unite = \App\Models\Unite::where('migrated_from_bien_id', $bien->id)->first(); @endphp
                                <option value="{{ $unite->id ?? '' }}" data-bien-id="{{ $bien->id }}" data-prix="{{ $bien->prix_visite }}">
                                    {{ $bien->nom_bien }}
                                </option>
                                @else
                                <!-- Bien principal avec sous-biens -->
                                <optgroup label="{{ $bien->nom_bien }}">
                                    @foreach($bien->children as $child)
                                    @php $uniteChild = \App\Models\Unite::where('migrated_from_bien_id', $child->id)->first(); @endphp
                                    <option value="{{ $uniteChild->id ?? '' }}" data-bien-id="{{ $child->id }}" data-prix="{{ $child->prix_visite }}">
                                        → {{ $child->nom_bien }}
                                    </option>
                                    @endforeach
                                </optgroup>
                                @endif
                                @endforeach
                                @endforeach
                            </select>
                            <input type="hidden" name="bien_id" id="bien_id">
                            <input type="hidden" name="unite_id" id="unite_id">
                            <x-input-error :messages="$errors->get('bien_id')" class="mt-2" />
                            <x-input-error :messages="$errors->get('unite_id')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="date_heure_visite" :value="__('Date et Heure *')" />
                            <x-text-input id="date_heure_visite" name="date_heure_visite" type="datetime-local" class="mt-1 block w-full" value="{{ old('date_heure_visite') }}" required />
                            <x-input-error :messages="$errors->get('date_heure_visite')" class="mt-2" />
                        </div>

                        <div class="md:col-span-2">
                            <x-input-label for="montant_paye" :value="__('Frais de visite')" />
                            <div class="relative">
                                <x-text-input id="montant_paye" name="montant_paye" type="number" step="0.01" class="mt-1 block w-full border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-slate-700 font-bold text-xl py-3 pr-16" required value="{{ old('montant_paye', '0.00') }}" />
                                <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                                    <span class="text-slate-400 font-bold">{{ Auth::user()->agence->devise }}</span>
                                </div>
                            </div>
                            <x-input-error :messages="$errors->get('montant_paye')" class="mt-2" />
                        </div>

                        <div class="md:col-span-2">
                            <x-input-label for="notes" :value="__('Notes (Optionnel)')" />
                            <textarea id="notes" name="notes" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" rows="3">{{ old('notes') }}</textarea>
                            <p class="text-xs text-gray-500 mt-1">Exemple : Client intéressé, à relancer demain</p>
                            <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                        </div>
                    </div>

                    <div class="flex items-center justify-between">
                        <a href="{{ route('visites.index') }}" class="text-gray-600 hover:text-gray-900">
                            ← Retour
                        </a>
                        <x-primary-button>
                            {{ __('Enregistrer la Visite') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        function updateBienAndUnite(select) {
            const option = select.options[select.selectedIndex];
            const bienId = option.getAttribute('data-bien-id');
            const uniteId = option.value;
            const prix = option.getAttribute('data-prix');
            const devise = "{{ Auth::user()->agence->devise }}";

            document.getElementById('bien_id').value = bienId || '';
            document.getElementById('unite_id').value = uniteId || '';

            if (prix) {
                document.getElementById('montant_paye').value = prix;
            } else {
                document.getElementById('montant_paye').value = 0;
            }
        }
    </script>
</x-app-layout>