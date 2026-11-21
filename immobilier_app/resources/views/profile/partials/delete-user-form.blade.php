<section class="space-y-6">
    <header>
        <h2 class="text-lg font-black text-slate-900 uppercase tracking-tight">
            {{ __('Suppression du Compte') }}
        </h2>

        <p class="mt-1 text-[11px] font-bold text-slate-500 uppercase tracking-widest leading-relaxed">
            {{ __('Une fois votre compte supprimé, toutes ses ressources et données seront définitivement effacées. Avant de supprimer votre compte, veuillez télécharger toutes les données ou informations que vous souhaitez conserver.') }}
        </p>
    </header>

    <x-danger-button
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
        class="bg-red-600 hover:bg-red-700 text-[10px] font-black uppercase tracking-widest rounded-lg px-8 py-3"
    >{{ __('Supprimer le compte') }}</x-danger-button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-8">
            @csrf
            @method('delete')

            <h2 class="text-lg font-black text-slate-900 uppercase tracking-tight">
                {{ __('Êtes-vous sûr de vouloir supprimer votre compte ?') }}
            </h2>

            <p class="mt-3 text-[11px] font-bold text-slate-500 uppercase tracking-widest leading-relaxed">
                {{ __('Une fois votre compte supprimé, toutes ses ressources et données seront définitivement effacées. Veuillez saisir votre mot de passe pour confirmer que vous souhaitez supprimer définitivement votre compte.') }}
            </p>

            <div class="mt-6">
                <x-input-label for="password" value="{{ __('Mot de passe') }}" class="sr-only" />

                <x-text-input
                    id="password"
                    name="password"
                    type="password"
                    class="block w-full"
                    placeholder="{{ __('Saisissez votre mot de passe') }}"
                />

                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            <div class="mt-8 flex justify-end gap-3">
                <x-secondary-button x-on:click="$dispatch('close')" class="bg-slate-100 text-slate-600 text-[10px] font-black uppercase tracking-widest border-none hover:bg-slate-200">
                    {{ __('Annuler') }}
                </x-secondary-button>

                <x-danger-button class="bg-red-600 hover:bg-red-700 text-[10px] font-black uppercase tracking-widest rounded-lg">
                    {{ __('Supprimer définitivement') }}
                </x-danger-button>
            </div>
        </form>
    </x-modal>
</section>
