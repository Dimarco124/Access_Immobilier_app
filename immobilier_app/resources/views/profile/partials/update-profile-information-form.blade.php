<section>
    <header class="mb-8">
        <h2 class="text-lg font-black text-slate-900 uppercase tracking-tight">
            {{ __('Informations Personnelles') }}
        </h2>

        <p class="mt-1 text-[11px] font-bold text-slate-500 uppercase tracking-widest">
            {{ __("Mettez à jour vos informations de profil et votre adresse e-mail.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="space-y-6">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="name" :value="__('Nom Complet')" class="text-[9px] font-black uppercase text-slate-400 tracking-widest mb-2" />
            <x-text-input id="name" name="name" type="text" class="block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" :value="__('Adresse Email')" class="text-[9px] font-black uppercase text-slate-400 tracking-widest mb-2" />
            <x-text-input id="email" name="email" type="email" class="block w-full {{ $user->hasRole('team_member') ? 'bg-slate-100 text-slate-500 cursor-not-allowed' : '' }}" :value="old('email', $user->email)" required autocomplete="username" {{ $user->hasRole('team_member') ? 'readonly' : '' }} />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="mt-4 p-4 bg-amber-50 border border-amber-100 rounded-xl">
                    <p class="text-[10px] font-black text-amber-700 uppercase tracking-tight">
                        {{ __('Votre adresse email n\'est pas vérifiée.') }}

                        <button form="send-verification" class="underline text-amber-900 hover:text-amber-600 transition-colors">
                            {{ __('Cliquez ici pour renvoyer l\'email de vérification.') }}
                        </button>
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <p class="mt-2 text-[10px] font-black text-emerald-600 uppercase">
                            {{ __('Un nouveau lien de vérification a été envoyé.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4 pt-4">
            <x-primary-button class="bg-slate-900 hover:bg-black text-[10px] font-black uppercase tracking-widest rounded-lg px-8 py-3">{{ __('Enregistrer') }}</x-primary-button>

            @if (session('status') === 'profile-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-[10px] font-black text-emerald-600 uppercase tracking-widest"
                >{{ __('Enregistré.') }}</p>
            @endif
        </div>
    </form>
</section>
