<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 w-full">
            <h2 class="font-black text-2xl text-slate-900 tracking-tight uppercase">
                {{ __('Mon Profil') }}
            </h2>
            <p class="text-[11px] text-slate-500 font-bold uppercase tracking-widest mt-1">Gérez vos informations
                personnelles et la sécurité de votre compte</p>
        </div>
    </x-slot>

    <div class="space-y-6">
        <div class="premium-card p-8 bg-white">
            <div class="max-w-xl">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>

        @if(!Auth::user()->hasRole('team_member') && !Auth::user()->hasRole('locataire') && !Auth::user()->hasRole('proprietaire'))
            <div class="premium-card p-8 bg-white">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>
        @else
            <div class="premium-card p-8 bg-white">
                <div class="max-w-xl">
                    <section>
                        <header class="mb-4">
                            <h2 class="text-lg font-black text-slate-900 uppercase tracking-tight">Sécurité du Compte</h2>
                        </header>
                        <p class="text-sm text-slate-600">
                            @if(Auth::user()->hasRole('team_member'))
                                Les mots de passe sont gérés par votre agence. Pour modifier votre mot de passe, contactez un administrateur de l'agence.
                            @else
                                Les mots de passe sont gérés par l'agence. Pour modifier votre mot de passe, contactez l'agence.
                            @endif
                        </p>
                    </section>
                </div>
            </div>
        @endif

        @if(Auth::user()->hasRole('admin'))
            <div class="premium-card p-8 bg-white border-l-4 border-l-red-500">
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        @endif
    </div>
</x-app-layout>