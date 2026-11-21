<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ProprietaireRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::user()->hasRole('admin');
    }

    public function rules(): array
    {
        $proprietaireId = $this->route('proprietaire') ? $this->route('proprietaire')->id : null;
        
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . ($proprietaireId ? $this->route('proprietaire')->user_id : 'NULL'),
            'telephone' => 'required|string|max:20',
            'password' => $proprietaireId ? 'nullable|min:8' : 'required|min:8',
            'civilite' => 'required|in:M.,Mme,Mlle',
            'numero_piece_identite' => 'required|string|max:100',
            'type_piece_identite' => 'required|in:CNI,Passeport,Permis de conduire,Autre',
            'date_naissance' => 'nullable|date|before:today',
            'lieu_naissance' => 'nullable|string|max:100',
            'profession' => 'nullable|string|max:100',
            'adresse_complete' => 'nullable|string|max:500',
            'ville' => 'nullable|string|max:100',
            'pays' => 'nullable|string|max:100',
            'situation_matrimoniale' => 'nullable|in:Célibataire,Marié(e),Divorcé(e),Veuf(ve)',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Le nom complet est obligatoire.',
            'email.required' => 'L\'email est obligatoire.',
            'email.email' => 'L\'email doit être valide.',
            'email.unique' => 'Cet email est déjà utilisé.',
            'telephone.required' => 'Le téléphone est obligatoire.',
            'password.required' => 'Le mot de passe est obligatoire.',
            'password.min' => 'Le mot de passe doit avoir au moins 8 caractères.',
            'civilite.required' => 'La civilité est obligatoire.',
            'numero_piece_identite.required' => 'Le numéro de pièce d\'identité est obligatoire.',
            'type_piece_identite.required' => 'Le type de pièce d\'identité est obligatoire.',
        ];
    }
}
