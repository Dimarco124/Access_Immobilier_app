<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 w-full">
            <div>
                <h2 class="font-black text-4xl text-slate-900 tracking-tight uppercase">
                    Profil Locataire
                </h2>
                <p class="text-slate-500 font-medium mt-1">Dossier de {{ $locataire->user->name }} - Suivi administratif et contractuel</p>
            </div>
            
        
            <div class="mt-1">
                <a href="{{ route('locataires.index') }}" class="text-xs font-black text-slate-400 uppercase tracking-widest hover:text-slate-600 transition-colors self-start inline-flex">
                    <i class="fas fa-arrow-left mr-2 self-start inline-flex"></i> Retour à la liste
                </a>
            </div>
        </div></x-slot>

    <div class="space-y-8">
        @if (session('success'))
        <div class="premium-card bg-green-50 border-green-100 p-6 flex items-center gap-4 animate-bounce">
            <div class="w-10 h-10 bg-green-500 rounded-xl flex items-center justify-center text-white">
                <i class="fas fa-check"></i>
            </div>
            <p class="text-green-700 font-bold">{{ session('success') }}</p>
        </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Colonne Gauche : Infos & Documents -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Infos Locataire -->
                <div class="premium-card p-8 bg-white">
                    <div class="flex items-center gap-3 mb-8">
                        <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center text-blue-600">
                            <i class="fas fa-user"></i>
                        </div>
                        <h3 class="text-lg font-black text-slate-900 uppercase tracking-tight">Informations Personnelles</h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100">
                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1">Civilité & Nom</span>
                            <div class="font-black text-slate-900 uppercase text-sm">{{ $locataire->civilite }} {{ $locataire->user->name }}</div>
                        </div>
                        <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100">
                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1">Email</span>
                            <div class="font-bold text-slate-700 text-sm">{{ $locataire->user->email }}</div>
                        </div>
                        <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100">
                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1">Téléphone</span>
                            <div class="font-black text-slate-900 text-sm">{{ $locataire->user->telephone ?? '-' }}</div>
                        </div>
                        <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100">
                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1">Profession</span>
                            <div class="font-black text-slate-900 text-sm uppercase">{{ $locataire->profession ?? '-' }}</div>
                        </div>
                        <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100">
                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1">N° Pièce d'identité</span>
                            <div class="font-black text-slate-900 text-sm">{{ $locataire->numero_piece_identite ?? '-' }}</div>
                        </div>
                        <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100">
                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1">Localisation</span>
                            <div class="font-black text-slate-900 text-sm uppercase">{{ $locataire->ville ?? '-' }}, {{ $locataire->pays ?? '-' }}</div>
                        </div>
                        <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 md:col-span-2">
                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1">Adresse Résidence</span>
                            <div class="font-bold text-slate-700 text-sm uppercase">{{ $locataire->adresse_residence ?? '-' }}</div>
                        </div>
                        <div class="p-4 bg-red-50 rounded-2xl border border-red-100">
                            <span class="text-[10px] font-black text-red-400 uppercase tracking-widest block mb-1">Contact Urgence</span>
                            <div class="font-black text-red-600 text-sm">{{ $locataire->contact_urgence ?? '-' }}</div>
                        </div>
                    </div>
                </div>

                <!-- Documents -->
                <div class="premium-card p-8 bg-white">
                    <div class="flex items-center gap-3 mb-8">
                        <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center text-blue-600">
                            <i class="fas fa-file-pdf"></i>
                        </div>
                        <h3 class="text-lg font-black text-slate-900 uppercase tracking-tight">Documents & Pièces</h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        @if($locataire->cni_recto)
                        <div class="p-4 border border-slate-100 rounded-2xl flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-slate-100 rounded-xl flex items-center justify-center text-slate-400">
                                    <i class="fas fa-id-card text-xl"></i>
                                </div>
                                <div>
                                    <p class="text-xs font-black text-slate-900 uppercase tracking-tight">CNI (Recto)</p>
                                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Pièce d'identité</p>
                                </div>
                            </div>
                            <a href="{{ $locataire->getDocumentUrl('cni_recto') }}" target="_blank" class="w-10 h-10 bg-blue-600 text-white rounded-xl flex items-center justify-center hover:bg-blue-700 transition-all shadow-lg shadow-blue-100">
                                <i class="fas fa-eye"></i>
                            </a>
                        </div>
                        @endif

                        @if($locataire->cni_verso)
                        <div class="p-4 border border-slate-100 rounded-2xl flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-slate-100 rounded-xl flex items-center justify-center text-slate-400">
                                    <i class="fas fa-id-card text-xl"></i>
                                </div>
                                <div>
                                    <p class="text-xs font-black text-slate-900 uppercase tracking-tight">CNI (Verso)</p>
                                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Pièce d'identité</p>
                                </div>
                            </div>
                            <a href="{{ $locataire->getDocumentUrl('cni_verso') }}" target="_blank" class="w-10 h-10 bg-blue-600 text-white rounded-xl flex items-center justify-center hover:bg-blue-700 transition-all shadow-lg shadow-blue-100">
                                <i class="fas fa-eye"></i>
                            </a>
                        </div>
                        @endif

                        @if($locataire->copie_piece_identite)
                        <div class="p-4 border border-slate-100 rounded-2xl flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-slate-100 rounded-xl flex items-center justify-center text-slate-400">
                                    <i class="fas fa-file-alt text-xl"></i>
                                </div>
                                <div>
                                    <p class="text-xs font-black text-slate-900 uppercase tracking-tight">Autre Document</p>
                                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Justificatif</p>
                                </div>
                            </div>
                            <a href="{{ $locataire->getDocumentUrl('copie_piece_identite') }}" target="_blank" class="w-10 h-10 bg-blue-600 text-white rounded-xl flex items-center justify-center hover:bg-blue-700 transition-all shadow-lg shadow-blue-100">
                                <i class="fas fa-eye"></i>
                            </a>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Colonne Droite : Contrats -->
            <div class="space-y-8">
                <!-- Contrats Actifs -->
                <div class="premium-card p-8 bg-white">
                    <div class="flex items-center justify-between mb-8">
                        <h3 class="text-sm font-black text-slate-900 uppercase tracking-widest flex items-center gap-2">
                            <i class="fas fa-file-contract text-blue-600"></i> Contrats Actifs
                        </h3>
                        <span class="w-6 h-6 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center text-[10px] font-black">{{ $locataire->contrats->count() }}</span>
                    </div>

                    @if($locataire->contrats->count() > 0)
                    <div class="space-y-4">
                        @foreach($locataire->contrats as $contrat)
                        <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 hover:border-blue-200 transition-all group">
                            <div class="font-black text-slate-900 uppercase text-xs mb-2 group-hover:text-blue-600 transition-colors">{{ $contrat->bien->nom_bien ?? '-' }}</div>
                            <div class="flex justify-between items-center">
                                <span class="text-[10px] font-black text-green-600">{{ number_format($contrat->montant_loyer_actuel, 0, ',', ' ') }} <span class="text-slate-400">{{ Auth::user()->agence->devise }}</span></span>
                                <span class="px-3 py-1 bg-emerald-100 text-emerald-600 text-[8px] font-black uppercase tracking-widest rounded-lg">Actif</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="py-8 text-center bg-slate-50 rounded-2xl border border-dashed border-slate-200">
                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Aucun contrat</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>