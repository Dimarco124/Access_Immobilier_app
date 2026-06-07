<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Créer un Nouveau Contrat de Location') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('contrats.store') }}" class="space-y-6">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-input-label for="locataire_id" :value="__('Locataire')" />
                            <select id="locataire_id" name="locataire_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="">Sélectionnez un locataire</option>
                                @foreach($locataires as $locataire)
                                    <option value="{{ $locataire->id }}">{{ $locataire->user->name }}</option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('locataire_id')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="bien_id" :value="__('Bien à louer (Uniquement disponibles sans sous-biens)')" />
                            <select id="bien_id" name="bien_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required onchange="updateLoyer(this)">
                                <option value="">Sélectionnez un bien</option>
                                @foreach($biens as $bien)
                                    <option value="{{ $bien->id }}" data-loyer="{{ $bien->loyer_mensuel }}">
                                        {{ $bien->nom_bien }} ({{ number_format($bien->loyer_mensuel, 0, ',', ' ') }} XOF)
                                    </option>
                                @endforeach
                            </select>
                            <x-input-error :messages="$errors->get('bien_id')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="date_debut" :value="__('Date de début')" />
                            <x-text-input id="date_debut" name="date_debut" type="date" class="mt-1 block w-full" required />
                            <x-input-error :messages="$errors->get('date_debut')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="date_fin" :value="__('Date de fin (Optionnel)')" />
                            <x-text-input id="date_fin" name="date_fin" type="date" class="mt-1 block w-full" />
                            <x-input-error :messages="$errors->get('date_fin')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="montant_loyer_actuel" :value="__('Montant du Loyer')" />
                            <x-text-input id="montant_loyer_actuel" name="montant_loyer_actuel" type="number" class="mt-1 block w-full" required />
                            <x-input-error :messages="$errors->get('montant_loyer_actuel')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="montant_caution" :value="__('Montant de la Caution')" />
                            <x-text-input id="montant_caution" name="montant_caution" type="number" class="mt-1 block w-full" required />
                            <x-input-error :messages="$errors->get('montant_caution')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="nombre_mois_avance" :value="__('Nombre de mois d\'avance')" />
                            <x-text-input id="nombre_mois_avance" name="nombre_mois_avance" type="number" class="mt-1 block w-full" value="0" required />
                            <x-input-error :messages="$errors->get('nombre_mois_avance')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="frais_dossier" :value="__('Frais de dossier')" />
                            <x-text-input id="frais_dossier" name="frais_dossier" type="number" class="mt-1 block w-full" value="0" />
                            <x-input-error :messages="$errors->get('frais_dossier')" class="mt-2" />
                        </div>
                    </div>

                    <div class="flex items-center justify-end mt-4">
                        <x-primary-button class="ml-4">
                            {{ __('Créer le Contrat') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function updateLoyer(select) {
            const option = select.options[select.selectedIndex];
            const loyer = option.getAttribute('data-loyer');
            if (loyer) {
                document.getElementById('montant_loyer_actuel').value = loyer;
                document.getElementById('montant_caution').value = loyer * 2; // Par défaut 2 mois
            }
        }
    </script>
</x-app-layout>
