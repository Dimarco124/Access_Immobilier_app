<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class MessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Tous les utilisateurs authentifiés peuvent envoyer des messages
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'destinataire_id' => 'required|exists:users,id',
            'objet' => 'required|string|max:255',
            'contenu' => 'required|string|max:5000',
        ];
    }

    public function messages(): array
    {
        return [
            'destinataire_id.required' => 'Le destinataire est obligatoire.',
            'destinataire_id.exists' => 'Le destinataire sélectionné n\'existe pas.',
            'objet.required' => 'L\'objet du message est obligatoire.',
            'objet.max' => 'L\'objet ne peut pas dépasser 255 caractères.',
            'contenu.required' => 'Le contenu du message est obligatoire.',
            'contenu.max' => 'Le message ne peut pas dépasser 5000 caractères.',
        ];
    }
}
