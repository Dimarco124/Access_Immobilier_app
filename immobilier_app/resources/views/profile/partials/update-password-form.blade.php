<section>
    <header class="mb-8">
        <h2 class="text-lg font-black text-slate-900 uppercase tracking-tight">
            {{ __('Sécurité du Compte') }}
        </h2>

        <p class="mt-1 text-[11px] font-bold text-slate-500 uppercase tracking-widest">
            {{ __('Assurez-vous que votre compte utilise un mot de passe long et complexe.') }}
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="space-y-6">
        @csrf
        @method('put')

        <div>
            <x-input-label for="update_password_current_password" :value="__('Mot de passe actuel')" class="text-[9px] font-black uppercase text-slate-400 tracking-widest mb-2" />
            <x-text-input id="update_password_current_password" name="current_password" type="password" class="block w-full" autocomplete="current-password" />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="update_password_password" :value="__('Nouveau mot de passe')" class="text-[9px] font-black uppercase text-slate-400 tracking-widest mb-2" />
            <x-text-input id="update_password_password" name="password" type="password" class="block w-full" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="update_password_password_confirmation" :value="__('Confirmer le mot de passe')" class="text-[9px] font-black uppercase text-slate-400 tracking-widest mb-2" />
            <x-text-input id="update_password_password_confirmation" name="password_confirmation" type="password" class="block w-full" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center gap-4 pt-4">
            <x-primary-button class="bg-slate-900 hover:bg-black text-[10px] font-black uppercase tracking-widest rounded-lg px-8 py-3">{{ __('Mettre à jour') }}</x-primary-button>

            @if (session('status') === 'password-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-[10px] font-black text-emerald-600 uppercase tracking-widest"
                >{{ __('Modifié avec succès.') }}</p>
            @endif
        </div>
    </form>
</section>
