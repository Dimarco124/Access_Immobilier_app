<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 w-full">
            <h2 class="font-bold text-3xl text-white">
                <i class="fas fa-paper-plane mr-2"></i>
                Nouveau Message
            </h2>
            
        
            <div class="mt-1">
                <a href="{{ route('messages.index') }}" class="btn-secondary self-start inline-flex">
                <i class="fas fa-arrow-left self-start inline-flex"></i>
                Retour
            </a>
            </div>
        </div></x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            
            @if(!Auth::user()->hasRole('admin'))
            <div class="alert-info">
                <i class="fas fa-info-circle text-xl"></i>
                <div>
                    <p class="font-semibold">Information</p>
                    <p class="text-sm">Vous pouvez envoyer des messages uniquement à l'agence. Pour toute question ou demande, contactez l'agence via cette messagerie.</p>
                </div>
            </div>
            @endif

            <div class="card">
                <div class="card-body">
                    <form action="{{ route('messages.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <!-- Destinataire -->
                        <div class="form-group">
                            <label for="destinataire_id" class="form-label">
                                <i class="fas fa-user text-blue-600 mr-2"></i>
                                Destinataire *
                            </label>
                            <select name="destinataire_id" id="destinataire_id" 
                                    class="form-select @error('destinataire_id') border-red-500 @enderror" required>
                                <option value="">-- Sélectionner un destinataire --</option>
                                @foreach($utilisateurs as $utilisateur)
                                <option value="{{ $utilisateur->id }}" {{ old('destinataire_id') == $utilisateur->id ? 'selected' : '' }}>
                                    {{ $utilisateur->name }} ({{ $utilisateur->email }})
                                    @if($utilisateur->hasRole('admin'))
                                    - 🏢 Agence
                                    @elseif($utilisateur->hasRole('proprietaire'))
                                    - 🏠 Propriétaire
                                    @elseif($utilisateur->hasRole('locataire'))
                                    - 👤 Locataire
                                    @endif
                                </option>
                                @endforeach
                            </select>
                            @if(!Auth::user()->hasRole('admin'))
                            <p class="mt-2 text-xs text-blue-600">
                                <i class="fas fa-lock mr-1"></i>
                                Vous ne pouvez communiquer qu'avec l'agence
                            </p>
                            @endif
                            @error('destinataire_id')
                            <p class="form-error">
                                <i class="fas fa-exclamation-circle"></i>
                                {{ $message }}
                            </p>
                            @enderror
                        </div>

                        <!-- Objet -->
                        <div class="form-group">
                            <label for="objet" class="form-label">
                                <i class="fas fa-tag text-blue-600 mr-2"></i>
                                Objet *
                            </label>
                            <input type="text" 
                                   name="objet" 
                                   id="objet" 
                                   value="{{ old('objet') }}"
                                   class="form-input @error('objet') border-red-500 @enderror" 
                                   placeholder="Exemple: Demande de renseignements"
                                   required>
                            @error('objet')
                            <p class="form-error">
                                <i class="fas fa-exclamation-circle"></i>
                                {{ $message }}
                            </p>
                            @enderror
                        </div>

                        <!-- Contenu -->
                        <div class="form-group">
                            <label for="contenu" class="form-label">
                                <i class="fas fa-comment-dots text-blue-600 mr-2"></i>
                                Message *
                            </label>
                            <textarea name="contenu" 
                                      id="contenu" 
                                      rows="8"
                                      class="form-textarea @error('contenu') border-red-500 @enderror" 
                                      placeholder="Écrivez votre message ici..."
                                      required>{{ old('contenu') }}</textarea>
                            @error('contenu')
                            <p class="form-error">
                                <i class="fas fa-exclamation-circle"></i>
                                {{ $message }}
                            </p>
                            @enderror
                        </div>

                        <!-- Pièce jointe -->
                        <div class="form-group">
                            <label for="piece_jointe" class="form-label">
                                <i class="fas fa-paperclip text-blue-600 mr-2"></i>
                                Pièce jointe (optionnel)
                            </label>
                            <input type="file" 
                                   name="piece_jointe" 
                                   id="piece_jointe"
                                   class="form-input @error('piece_jointe') border-red-500 @enderror"
                                   accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                            <p class="mt-2 text-xs text-slate-500">
                                <i class="fas fa-info-circle mr-1"></i>
                                Formats acceptés: PDF, DOC, DOCX, JPG, PNG (Max: 10 MB)
                            </p>
                            @error('piece_jointe')
                            <p class="form-error">
                                <i class="fas fa-exclamation-circle"></i>
                                {{ $message }}
                            </p>
                            @enderror
                        </div>

                        <!-- Actions -->
                        <div class="flex gap-4">
                            <button type="submit" class="btn-primary flex-1">
                                <i class="fas fa-paper-plane"></i>
                                Envoyer le message
                            </button>
                            <a href="{{ route('messages.index') }}" class="btn-secondary">
                                <i class="fas fa-times"></i>
                                Annuler
                            </a>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
