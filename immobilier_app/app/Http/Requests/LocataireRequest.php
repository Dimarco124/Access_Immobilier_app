<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class LocataireRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::user()->hasRole('admin');
    }

    public function rules(): array
    {
        $locataireId = $this->route('locataire') ? $this->route('locataire')->id : null;
        
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . ($locataireId ? $this->route('locataire')->user_id : 'NULL'),
            'telephone' => 'required|string|max:20',
            'password' => $locataireId ? 'nullable|min:8' : 'required|min:8',
            'civilite' => 'required|in:M.,Mme,Mlle',
            'numero_piece_identite' => 'required|string|max:100',
            'type_piece_identite' => 'required|in:CNI,Passeport,Permis de conduire,Autre',
            'date_naissance' => 'nullable|date|before:today',
            'lieu_naissance' => 'nullable|string|max:100',
            'profession' => 'nullable|string|max:100',
            'employeur' => 'nullable|string|max:200',
            'revenu_mensuel' => 'nullable|numeric|min:0|max:999999999',
            'adresse_complete' => 'nullable|string|max:500',
            'ville' => 'nullable|string|max:100',
            'pays' => 'nullable|string|max:100',
            'situation_matrimoniale' => 'nullable|in:Célibataire,Marié(e),Divorcé(e),Veuf(ve)',
            'nombre_personnes_charge' => 'nullable|integer|min:0|max:50',
            'contact_urgence_nom' => 'nullable|string|max:255',
            'contact_urgence_telephone' => 'nullable|string|max:20',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Le nom complet est obligatoire.',
            'email.required' => 'L\'email est obligatoire.',
            'email.unique' => 'Cet email est déjà utilisé.',
            'telephone.required' => 'Le téléphone est obligatoire.',
            'password.required' => 'Le mot de passe est obligatoire.',
            'password.min' => 'Le mot de passe doit avoir au moins 8 caractères.',
            'civilite.required' => 'La civilité est obligatoire.',
            'numero_piece_identite.required' => 'Le numéro de pièce d\'identité est obligatoire.',
        ];
    }
}
