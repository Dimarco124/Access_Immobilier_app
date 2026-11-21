<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class TicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Tous les utilisateurs authentifiés peuvent créer des tickets
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'objet' => 'required|string|max:255',
            'description_probleme' => 'required|string|max:2000',
            'bien_id' => 'required|exists:biens,id',
            'priorite' => 'required|in:Faible,Moyenne,Haute,Urgente',
            'type_intervention' => 'nullable|in:Plomberie,Électricité,Menuiserie,Peinture,Climatisation,Autre',
        ];
    }

    public function messages(): array
    {
        return [
            'objet.required' => 'L\'objet du ticket est obligatoire.',
            'description_probleme.required' => 'La description du problème est obligatoire.',
            'description_probleme.max' => 'La description ne peut pas dépasser 2000 caractères.',
            'bien_id.required' => 'Le bien concerné est obligatoire.',
            'bien_id.exists' => 'Le bien sélectionné n\'existe pas.',
            'priorite.required' => 'La priorité est obligatoire.',
        ];
    }
}
