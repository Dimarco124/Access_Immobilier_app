<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Nouvelle Demande d\'Intervention') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <form method="POST" action="{{ route('tickets.store') }}" class="space-y-6">
                    @csrf

                    <div>
                        <x-input-label for="objet" :value="__('Objet de la demande (ex: Ampoule gâtée, Fuite d\'eau)')" />
                        <x-text-input id="objet" name="objet" type="text" class="mt-1 block w-full" required />
                        <x-input-error :messages="$errors->get('objet')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="priorite" :value="__('Priorité')" />
                        <select id="priorite" name="priorite" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                            <option value="Faible">Faible (Entretien normal)</option>
                            <option value="Urgent">Urgent (Impacte le quotidien)</option>
                            <option value="Critique">Critique (Urgence absolue)</option>
                        </select>
                    </div>

                    <div>
                        <x-input-label for="description_probleme" :value="__('Description détaillée')" />
                        <textarea id="description_probleme" name="description_probleme" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" rows="5" required placeholder="Expliquez ici votre problème en détail..."></textarea>
                        <x-input-error :messages="$errors->get('description_probleme')" class="mt-2" />
                    </div>

                    <div class="flex items-center justify-end mt-4">
                        <x-primary-button class="ml-4">
                            {{ __('Envoyer la demande') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
