<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class VisiteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::user()->hasRole('admin');
    }

    public function rules(): array
    {
        return [
            'bien_id' => 'required|exists:biens,id',
            'locataire_id' => 'nullable|exists:locataires,id',
            'nom_visiteur' => 'required|string|max:255',
            'telephone_visiteur' => 'required|string|max:20',
            'email_visiteur' => 'nullable|email|max:255',
            'date_visite' => 'required|date|after_or_equal:today',
            'heure_visite' => 'required|date_format:H:i',
            'montant_visite' => 'nullable|numeric|min:0|max:999999',
            'notes' => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'bien_id.required' => 'Le bien est obligatoire.',
            'bien_id.exists' => 'Le bien sélectionné n\'existe pas.',
            'nom_visiteur.required' => 'Le nom du visiteur est obligatoire.',
            'telephone_visiteur.required' => 'Le téléphone du visiteur est obligatoire.',
            'date_visite.required' => 'La date de visite est obligatoire.',
            'date_visite.after_or_equal' => 'La date de visite ne peut pas être dans le passé.',
            'heure_visite.required' => 'L\'heure de visite est obligatoire.',
        ];
    }
}
