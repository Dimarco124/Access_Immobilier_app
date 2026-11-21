<x-onboarding-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 w-full">
            <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                <div>
                    <h2 class="font-black text-xl md:text-2xl text-slate-900 tracking-tight uppercase">Configuration de
                        votre agence</h2>
                    <p class="text-[10px] text-slate-500 font-bold uppercase tracking-widest mt-1">
                        Choisissez le mode de gestion de votre agence.
                    </p>
                </div>
                <div
                    class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-slate-100 text-slate-600 text-[10px] font-black uppercase tracking-widest">
                    Étape 1/4
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-5xl mx-auto space-y-12">
            <form action="{{ route('team-setup.store-mode') }}" method="POST">
                @csrf

                <div class="grid md:grid-cols-2 gap-6">
                    <label class="cursor-pointer">
                        <input type="radio" name="mode" value="solo" class="sr-only peer" {{ old('mode', 'solo') === 'solo' ? 'checked' : '' }}>
                        <div
                            class="premium-card h-full flex flex-col justify-between border border-slate-200 p-8 transition-all duration-300 peer-checked:border-blue-500 peer-checked:ring-1 peer-checked:ring-blue-500 peer-checked:bg-blue-50 hover:border-slate-300">
                            <div class="space-y-4">
                                <div
                                    class="w-16 h-16 bg-slate-100 rounded-2xl flex items-center justify-center text-slate-700">
                                    <i class="fas fa-user text-3xl"></i>
                                </div>
                                <div>
                                    <h3 class="text-xl font-black text-slate-900 uppercase tracking-tight">Je gère seul
                                    </h3>
                                    <p class="mt-2 text-sm text-slate-600">Accès complet à tous les modules de l'agence.
                                    </p>
                                </div>
                            </div>
                            <div class="mt-4 text-sm text-slate-500 font-semibold">Parfait si vous souhaitez piloter
                                l'agence seul.</div>
                        </div>
                    </label>

                    <label class="cursor-pointer">
                        <input type="radio" name="mode" value="team" class="sr-only peer" {{ old('mode') === 'team' ? 'checked' : '' }}>
                        <div
                            class="premium-card h-full flex flex-col justify-between border border-slate-200 p-8 transition-all duration-300 peer-checked:border-blue-500 peer-checked:ring-1 peer-checked:ring-blue-500 peer-checked:bg-blue-50 hover:border-slate-300">
                            <div class="space-y-4">
                                <div
                                    class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center text-white shadow-lg">
                                    <i class="fas fa-users text-3xl"></i>
                                </div>
                                <div>
                                    <h3 class="text-xl font-black text-slate-900 uppercase tracking-tight">Nous sommes
                                        une équipe</h3>
                                    <p class="mt-2 text-sm text-slate-600">Définissez les responsabilités de chaque
                                        membre.</p>
                                </div>
                            </div>
                            <div class="mt-4 text-sm text-slate-500 font-semibold">Choisissez cette option pour
                                organiser votre équipe.</div>
                        </div>
                    </label>
                </div>

                @error('mode')
                    <div class="mb-6 premium-card p-4 bg-red-50 border-2 border-red-200 mt-4">
                        <div class="flex items-start gap-3">
                            <i class="fas fa-exclamation-circle text-red-600"></i>
                            <p class="text-sm text-red-700 font-bold">{{ $message }}</p>
                        </div>
                    </div>
                @enderror

                <div class="premium-card p-6 bg-slate-50 border border-slate-200 mt-8">
                    <div class="flex items-start gap-4">
                        <i class="fas fa-info-circle text-slate-500 text-xl mt-1"></i>
                        <div>
                            <p class="text-sm text-slate-900 font-bold mb-1">Vous pourrez changer ce paramètre plus tard
                            </p>
                            <p class="text-xs text-slate-600">L'onglet de gestion d'équipe restera accessible pour faire
                                évoluer votre organisation.</p>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end mt-6">
                    <button type="submit"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 px-6 py-3 text-sm font-black text-white uppercase tracking-widest shadow-lg shadow-blue-200 hover:bg-blue-700 transition">
                        Suivant
                        <i class="fas fa-arrow-right"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-onboarding-layout>