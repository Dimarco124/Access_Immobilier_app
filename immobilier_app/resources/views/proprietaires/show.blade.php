<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 w-full">
            <div>
                <h2 class="font-black text-4xl text-slate-900 tracking-tight uppercase">
                    Profil Propriétaire
                </h2>
                <p class="text-slate-500 font-medium mt-1">Dossier de {{ $proprietaire->user->name }} - Suivi administratif et patrimoine</p>
            </div>
            
        
            <div class="mt-1">
                <a href="{{ route('proprietaires.index') }}" class="text-xs font-black text-slate-400 uppercase tracking-widest hover:text-slate-600 transition-colors self-start inline-flex">
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
            <!-- Colonne Gauche : Infos & Patrimoine -->
            <div class="lg:col-span-2 space-y-8">
                <!-- Infos Propriétaire -->
                <div class="premium-card p-8 bg-white">
                    <div class="flex items-center gap-3 mb-8">
                        <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center text-blue-600">
                            <i class="fas fa-user-tie"></i>
                        </div>
                        <h3 class="text-lg font-black text-slate-900 uppercase tracking-tight">Informations Personnelles</h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100">
                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1">Civilité & Nom</span>
                            <div class="font-black text-slate-900 uppercase text-sm">{{ $proprietaire->civilite }} {{ $proprietaire->user->name }}</div>
                        </div>
                        <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100">
                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1">Email</span>
                            <div class="font-bold text-slate-700 text-sm">{{ $proprietaire->user->email }}</div>
                        </div>
                        <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100">
                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1">Téléphone</span>
                            <div class="font-black text-slate-900 text-sm">{{ $proprietaire->user->telephone ?? '-' }}</div>
                        </div>
                        <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100">
                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1">N° Pièce d'identité</span>
                            <div class="font-black text-slate-900 text-sm">{{ $proprietaire->numero_piece_identite ?? '-' }}</div>
                        </div>
                        <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100">
                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1">Localisation</span>
                            <div class="font-black text-slate-900 text-sm uppercase">{{ $proprietaire->ville ?? '-' }}, {{ $proprietaire->pays ?? '-' }}</div>
                        </div>
                        <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100">
                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1">RIB Bancaire</span>
                            <div class="font-black text-slate-900 text-sm">{{ $proprietaire->rib_bancaire ?? '-' }}</div>
                        </div>
                        <div class="p-4 bg-slate-50 rounded-2xl border border-slate-100 md:col-span-2">
                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1">Adresse Résidence</span>
                            <div class="font-bold text-slate-700 text-sm uppercase">{{ $proprietaire->adresse_residence ?? '-' }}</div>
                        </div>
                    </div>
                </div>

                <!-- Documents & Pièces Justificatives (Visualisation Directe dans l'App) -->
                <div class="premium-card p-8 bg-white">
                    <div class="flex items-center justify-between mb-8">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center text-blue-600">
                                <i class="fas fa-id-card"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-black text-slate-900 uppercase tracking-tight">Pièces d'Identité & Justificatifs</h3>
                                <p class="text-xs text-slate-400 font-bold">Visualisation intégrée des documents d'identification</p>
                            </div>
                        </div>
                        @if(Auth::user()->hasModuleAccess('proprietaires', 'collaborator'))
                        <a href="{{ route('proprietaires.edit', $proprietaire) }}" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-[10px] font-black uppercase tracking-wider transition inline-flex items-center gap-1.5">
                            <i class="fas fa-camera text-xs"></i> Actualiser les photos
                        </a>
                        @endif
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Recto de la pièce -->
                        <div class="p-5 border border-slate-100 rounded-3xl bg-slate-50/50 flex flex-col justify-between">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center gap-2.5">
                                    <span class="w-2.5 h-2.5 rounded-full {{ $proprietaire->cni_recto ? 'bg-emerald-500' : 'bg-slate-300' }}"></span>
                                    <h4 class="text-xs font-black text-slate-900 uppercase tracking-wider">CNI / Passeport (Recto)</h4>
                                </div>
                                <span class="text-[9px] font-black uppercase px-2 py-0.5 rounded-md {{ $proprietaire->cni_recto ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-200 text-slate-500' }}">
                                    {{ $proprietaire->cni_recto ? 'Disponible' : 'Manquant' }}
                                </span>
                            </div>

                            @if($proprietaire->cni_recto)
                                @php
                                    $isPdfRecto = str_ends_with(strtolower($proprietaire->cni_recto), '.pdf');
                                @endphp

                                @if(!$isPdfRecto)
                                <div class="relative group overflow-hidden rounded-2xl bg-slate-900 aspect-video flex items-center justify-center cursor-pointer shadow-sm my-2" onclick="openShowModal('{{ $proprietaire->getDocumentUrl('cni_recto') }}', 'Carte d\'identité - Recto ({{ $proprietaire->user->name }})')">
                                    <img src="{{ $proprietaire->getDocumentUrl('cni_recto') }}" alt="CNI Recto" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                    <div class="absolute inset-0 bg-slate-950/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-3">
                                        <span class="px-3 py-1.5 bg-white text-slate-900 text-xs font-black uppercase rounded-xl shadow-lg flex items-center gap-1.5 transform translate-y-2 group-hover:translate-y-0 transition">
                                            <i class="fas fa-search-plus"></i> Voir en grand
                                        </span>
                                    </div>
                                </div>
                                @else
                                <div class="my-2 p-6 bg-white border border-slate-200 rounded-2xl flex flex-col items-center justify-center text-center">
                                    <i class="fas fa-file-pdf text-4xl text-rose-500 mb-2"></i>
                                    <span class="text-xs font-bold text-slate-700 mb-2 truncate max-w-full">{{ basename($proprietaire->cni_recto) }}</span>
                                    <span class="text-[10px] text-slate-400 uppercase font-black">Document PDF</span>
                                </div>
                                @endif

                                <div class="flex items-center justify-between pt-2 border-t border-slate-100 mt-2">
                                    @if(!$isPdfRecto)
                                    <button type="button" onclick="openShowModal('{{ $proprietaire->getDocumentUrl('cni_recto') }}', 'Carte d\'identité - Recto ({{ $proprietaire->user->name }})')" class="text-xs font-black text-blue-600 hover:text-blue-700 uppercase tracking-wider flex items-center gap-1.5">
                                        <i class="fas fa-expand"></i> Inspecter dans l'app
                                    </button>
                                    @else
                                    <a href="{{ $proprietaire->getDocumentUrl('cni_recto') }}" target="_blank" class="text-xs font-black text-blue-600 hover:text-blue-700 uppercase tracking-wider flex items-center gap-1.5">
                                        <i class="fas fa-external-link-alt"></i> Ouvrir le document
                                    </a>
                                    @endif
                                    <a href="{{ $proprietaire->getDocumentUrl('cni_recto', true) }}" download class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center transition" title="Télécharger">
                                        <i class="fas fa-download text-xs"></i>
                                    </a>
                                </div>
                            @else
                                <div class="my-2 py-8 border-2 border-dashed border-slate-200 rounded-2xl flex flex-col items-center justify-center text-center bg-white">
                                    <div class="w-10 h-10 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mb-2">
                                        <i class="fas fa-id-card text-lg"></i>
                                    </div>
                                    <p class="text-xs font-bold text-slate-400">Aucune photo du recto</p>
                                </div>
                                @if(Auth::user()->hasModuleAccess('proprietaires', 'collaborator'))
                                <a href="{{ route('proprietaires.edit', $proprietaire) }}" class="mt-2 text-center py-2 bg-blue-50 hover:bg-blue-100 text-blue-600 text-xs font-black uppercase rounded-xl transition">
                                    + Ajouter le recto
                                </a>
                                @endif
                            @endif
                        </div>

                        <!-- Verso de la pièce -->
                        <div class="p-5 border border-slate-100 rounded-3xl bg-slate-50/50 flex flex-col justify-between">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center gap-2.5">
                                    <span class="w-2.5 h-2.5 rounded-full {{ $proprietaire->cni_verso ? 'bg-emerald-500' : 'bg-slate-300' }}"></span>
                                    <h4 class="text-xs font-black text-slate-900 uppercase tracking-wider">CNI / Passeport (Verso)</h4>
                                </div>
                                <span class="text-[9px] font-black uppercase px-2 py-0.5 rounded-md {{ $proprietaire->cni_verso ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-200 text-slate-500' }}">
                                    {{ $proprietaire->cni_verso ? 'Disponible' : 'Manquant' }}
                                </span>
                            </div>

                            @if($proprietaire->cni_verso)
                                @php
                                    $isPdfVerso = str_ends_with(strtolower($proprietaire->cni_verso), '.pdf');
                                @endphp

                                @if(!$isPdfVerso)
                                <div class="relative group overflow-hidden rounded-2xl bg-slate-900 aspect-video flex items-center justify-center cursor-pointer shadow-sm my-2" onclick="openShowModal('{{ $proprietaire->getDocumentUrl('cni_verso') }}', 'Carte d\'identité - Verso ({{ $proprietaire->user->name }})')">
                                    <img src="{{ $proprietaire->getDocumentUrl('cni_verso') }}" alt="CNI Verso" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                    <div class="absolute inset-0 bg-slate-950/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-3">
                                        <span class="px-3 py-1.5 bg-white text-slate-900 text-xs font-black uppercase rounded-xl shadow-lg flex items-center gap-1.5 transform translate-y-2 group-hover:translate-y-0 transition">
                                            <i class="fas fa-search-plus"></i> Voir en grand
                                        </span>
                                    </div>
                                </div>
                                @else
                                <div class="my-2 p-6 bg-white border border-slate-200 rounded-2xl flex flex-col items-center justify-center text-center">
                                    <i class="fas fa-file-pdf text-4xl text-rose-500 mb-2"></i>
                                    <span class="text-xs font-bold text-slate-700 mb-2 truncate max-w-full">{{ basename($proprietaire->cni_verso) }}</span>
                                    <span class="text-[10px] text-slate-400 uppercase font-black">Document PDF</span>
                                </div>
                                @endif

                                <div class="flex items-center justify-between pt-2 border-t border-slate-100 mt-2">
                                    @if(!$isPdfVerso)
                                    <button type="button" onclick="openShowModal('{{ $proprietaire->getDocumentUrl('cni_verso') }}', 'Carte d\'identité - Verso ({{ $proprietaire->user->name }})')" class="text-xs font-black text-blue-600 hover:text-blue-700 uppercase tracking-wider flex items-center gap-1.5">
                                        <i class="fas fa-expand"></i> Inspecter dans l'app
                                    </button>
                                    @else
                                    <a href="{{ $proprietaire->getDocumentUrl('cni_verso') }}" target="_blank" class="text-xs font-black text-blue-600 hover:text-blue-700 uppercase tracking-wider flex items-center gap-1.5">
                                        <i class="fas fa-external-link-alt"></i> Ouvrir le document
                                    </a>
                                    @endif
                                    <a href="{{ $proprietaire->getDocumentUrl('cni_verso', true) }}" download class="w-8 h-8 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center transition" title="Télécharger">
                                        <i class="fas fa-download text-xs"></i>
                                    </a>
                                </div>
                            @else
                                <div class="my-2 py-8 border-2 border-dashed border-slate-200 rounded-2xl flex flex-col items-center justify-center text-center bg-white">
                                    <div class="w-10 h-10 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mb-2">
                                        <i class="fas fa-id-card text-lg"></i>
                                    </div>
                                    <p class="text-xs font-bold text-slate-400">Aucune photo du verso</p>
                                </div>
                                @if(Auth::user()->hasModuleAccess('proprietaires', 'collaborator'))
                                <a href="{{ route('proprietaires.edit', $proprietaire) }}" class="mt-2 text-center py-2 bg-blue-50 hover:bg-blue-100 text-blue-600 text-xs font-black uppercase rounded-xl transition">
                                    + Ajouter le verso
                                </a>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Patrimoine Immobilier -->
                <div class="premium-card p-8 bg-white">
                    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-8">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center text-blue-600">
                                <i class="fas fa-city"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-black text-slate-900 uppercase tracking-tight">Patrimoine Immobilier</h3>
                                <p class="text-xs font-bold text-slate-400">
                                    {{ $proprietaire->famillesDeBiens->count() }} Propriété(s) • {{ $proprietaire->biens->count() }} Unité(s) locative(s)
                                </p>
                            </div>
                        </div>
                        @if(Auth::user()->hasModuleAccess('immobilier', 'collaborator'))
                        <div class="flex items-center gap-2">
                            <a href="{{ route('proprietes.create') }}?proprietaire_id={{ $proprietaire->id }}" class="px-3 py-1.5 bg-blue-50 text-blue-600 rounded-lg text-[10px] font-black uppercase tracking-wider hover:bg-blue-600 hover:text-white transition-all inline-flex items-center gap-1">
                                <i class="fas fa-plus"></i> Nouvelle Propriété
                            </a>
                            <a href="{{ route('biens.create') }}?proprietaire_id={{ $proprietaire->id }}" class="px-3 py-1.5 bg-slate-900 text-white rounded-lg text-[10px] font-black uppercase tracking-wider hover:bg-black transition-all inline-flex items-center gap-1">
                                <i class="fas fa-door-open"></i> Nouvelle Unité
                            </a>
                        </div>
                        @endif
                    </div>

                    <!-- Liste des Propriétés du bailleur -->
                    @if($proprietaire->famillesDeBiens->count() > 0)
                    <div class="space-y-4">
                        @foreach($proprietaire->famillesDeBiens as $propriete)
                        <div class="p-5 bg-slate-50 rounded-2xl border border-slate-100 hover:bg-white hover:shadow-md transition-all">
                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-3 border-b border-slate-200/60 mb-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-blue-600 text-white flex items-center justify-center text-sm shadow-sm">
                                        <i class="fas {{ $propriete->isIndividuelle() ? 'fa-home' : 'fa-building' }}"></i>
                                    </div>
                                    <div>
                                        <a href="{{ route('proprietes.show', $propriete) }}" class="font-black text-slate-900 text-sm hover:text-blue-600 transition-colors uppercase">
                                            {{ $propriete->nom_famille }}
                                        </a>
                                        <p class="text-[10px] text-slate-400 font-medium">
                                            {{ $propriete->type_famille }} • {{ $propriete->adresse }}
                                        </p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="px-2.5 py-0.5 rounded-full text-[9px] font-black uppercase tracking-wider {{ $propriete->isIndividuelle() ? 'bg-blue-100 text-blue-700' : 'bg-indigo-100 text-indigo-700' }}">
                                        {{ $propriete->isIndividuelle() ? 'Bien Individuel' : 'Immeuble Collectif' }}
                                    </span>
                                    <span class="text-xs font-black text-slate-700">
                                        {{ $propriete->biens->count() }} unité(s)
                                    </span>
                                </div>
                            </div>

                            @if($propriete->biens->count() > 0)
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5 pt-1">
                                @foreach($propriete->biens as $unite)
                                <div class="p-3 bg-white rounded-xl border border-slate-100 flex items-center justify-between">
                                    <div class="flex items-center gap-2.5 min-w-0">
                                        <i class="fas fa-door-closed text-slate-400 text-xs"></i>
                                        <div class="truncate">
                                            <p class="text-xs font-black text-slate-800 truncate">{{ $unite->nom_bien }}</p>
                                            <p class="text-[9px] text-slate-400 font-bold uppercase">{{ $unite->type_bien }}</p>
                                        </div>
                                    </div>
                                    <div class="text-right shrink-0">
                                        <p class="text-xs font-black text-blue-600">{{ number_format($unite->loyer_mensuel ?? 0, 0, ',', ' ') }} {{ Auth::user()->agence->devise }}</p>
                                        <span class="text-[8px] font-black uppercase px-1.5 py-0.5 rounded {{ $unite->statut == 'Loué' ? 'bg-emerald-50 text-emerald-600' : 'bg-slate-100 text-slate-600' }}">
                                            {{ $unite->statut }}
                                        </span>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            @else
                            <p class="text-[11px] text-slate-400 italic">Aucune unité locative ajoutée dans cette propriété.</p>
                            @endif
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center py-10 bg-slate-50 rounded-[2rem] border-2 border-dashed border-slate-200">
                        <i class="fas fa-city text-3xl text-slate-300 mb-2"></i>
                        <p class="text-xs font-black text-slate-500 uppercase tracking-wider">Aucune propriété enregistrée pour ce bailleur</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Colonne Droite : Actions & Stats -->
            <div class="space-y-8">
                <div class="premium-card p-8 bg-white">
                    <h3 class="text-xs font-black text-slate-900 uppercase tracking-widest mb-6">Actions Administratives</h3>
                    <div class="space-y-4">
                        @if(Auth::user()->hasModuleAccess('proprietaires', 'collaborator'))
                        <a href="{{ route('proprietaires.edit', $proprietaire) }}" class="w-full flex items-center justify-center gap-2.5 px-6 py-3.5 bg-blue-600 hover:bg-blue-700 text-white rounded-2xl text-[11px] font-black uppercase tracking-wider shadow-md shadow-blue-100 transition-all hover:scale-[1.01] active:scale-95">
                            <i class="fas fa-edit text-xs"></i> Modifier le profil
                        </a>
                        @endif
                        @if(Auth::user()->hasModuleAccess('proprietaires', 'manager'))
                        <form action="{{ route('proprietaires.destroy', $proprietaire) }}" method="POST" class="w-full" onsubmit="return confirm('Attention : Cette action est irréversible. Confirmer la suppression ?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full flex items-center justify-center gap-2.5 px-6 py-3.5 bg-red-50 hover:bg-red-600 text-red-600 hover:text-white rounded-2xl text-[11px] font-black uppercase tracking-wider transition-all hover:scale-[1.01] active:scale-95">
                                <i class="fas fa-trash text-xs"></i> Supprimer le compte
                            </button>
                        </form>
                        @endif
                    </div>
                </div>

                <div class="premium-card p-8 bg-white">
                    <h3 class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-6">Résumé Financier</h3>
                    <div class="space-y-6">
                        <div class="flex items-center justify-between p-4 bg-slate-50 rounded-2xl border border-slate-100">
                            <div>
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Revenu Brut Total</p>
                                <p class="text-lg font-black text-slate-900">
                                    {{ number_format($proprietaire->biens->sum('loyer_mensuel'), 0, ',', ' ') }}
                                </p>
                            </div>
                            <div class="w-10 h-10 bg-blue-100 text-blue-600 rounded-xl flex items-center justify-center">
                                <i class="fas fa-wallet"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal d'inspection grand format dans l'application -->
    <div id="showPhotoModal" class="fixed inset-0 z-50 bg-slate-950/90 backdrop-blur-md hidden items-center justify-center p-4">
        <div class="relative max-w-4xl w-full bg-white rounded-3xl overflow-hidden shadow-2xl flex flex-col max-h-[92vh]">
            <div class="p-5 border-b border-slate-100 flex items-center justify-between bg-slate-50">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-blue-100 text-blue-600 flex items-center justify-center">
                        <i class="fas fa-id-card text-lg"></i>
                    </div>
                    <div>
                        <h4 id="showPhotoModalTitle" class="text-sm font-black text-slate-900 uppercase tracking-tight">Pièce d'identité</h4>
                        <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Inspection haute définition dans l'application</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <a id="showPhotoModalDownload" href="" download class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-black uppercase tracking-wider flex items-center gap-1.5 transition">
                        <i class="fas fa-download"></i> Télécharger
                    </a>
                    <button type="button" onclick="closeShowModal()" class="w-10 h-10 rounded-xl bg-slate-200/60 hover:bg-slate-200 text-slate-700 flex items-center justify-center transition">
                        <i class="fas fa-times text-base"></i>
                    </button>
                </div>
            </div>
            
            <div class="p-6 flex-1 overflow-auto flex items-center justify-center bg-slate-950 min-h-[400px]">
                <img id="showPhotoModalImg" src="" alt="Aperçu document" class="max-w-full max-h-[72vh] object-contain rounded-xl shadow-2xl transition-transform duration-200">
            </div>

            <div class="p-4 border-t border-slate-100 bg-white flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <button type="button" onclick="rotateShowPhoto()" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-black uppercase flex items-center gap-1.5 transition">
                        <i class="fas fa-redo"></i> Pivoter
                    </button>
                    <button type="button" onclick="zoomShowPhoto(0.2)" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-black uppercase flex items-center gap-1.5 transition">
                        <i class="fas fa-search-plus"></i> Zoom +
                    </button>
                    <button type="button" onclick="zoomShowPhoto(-0.2)" class="px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-black uppercase flex items-center gap-1.5 transition">
                        <i class="fas fa-search-minus"></i> Zoom -
                    </button>
                </div>
                <button type="button" onclick="closeShowModal()" class="px-6 py-2.5 bg-slate-900 text-white text-xs font-black uppercase rounded-xl hover:bg-black transition">
                    Fermer
                </button>
            </div>
        </div>
    </div>

    <script>
        let currentRotation = 0;
        let currentScale = 1;

        function openShowModal(src, title) {
            if (!src) return;
            currentRotation = 0;
            currentScale = 1;
            const img = document.getElementById('showPhotoModalImg');
            img.src = src;
            img.style.transform = `scale(1) rotate(0deg)`;
            document.getElementById('showPhotoModalTitle').textContent = title;
            document.getElementById('showPhotoModalDownload').href = src;
            
            const modal = document.getElementById('showPhotoModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.style.overflow = 'hidden';
        }

        function closeShowModal() {
            const modal = document.getElementById('showPhotoModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.getElementById('showPhotoModalImg').src = '';
            document.body.style.overflow = '';
        }

        function rotateShowPhoto() {
            currentRotation = (currentRotation + 90) % 360;
            updateTransform();
        }

        function zoomShowPhoto(delta) {
            currentScale = Math.max(0.5, Math.min(3, currentScale + delta));
            updateTransform();
        }

        function updateTransform() {
            const img = document.getElementById('showPhotoModalImg');
            img.style.transform = `scale(${currentScale}) rotate(${currentRotation}deg)`;
        }

        window.addEventListener('click', function(e) {
            const modal = document.getElementById('showPhotoModal');
            if (e.target === modal) {
                closeShowModal();
            }
        });
        window.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeShowModal();
            }
        });
    </script>
</x-app-layout>
