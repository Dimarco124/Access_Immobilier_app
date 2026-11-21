<?php

namespace App\Http\Requests\Finance;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class RecordPaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::user()->hasRole('admin') 
            && Auth::user()->agence_id === $this->route('facture')->agence_id;
    }

    public function rules(): array
    {
        return [
            'montant_verse' => ['required', 'numeric', 'min:1', 'max:999999999', 'regex:/^\d+(\.\d{1,2})?$/'],
            'mode_paiement' => 'required|in:Espèces,Chèque,Virement,Mobile Money',
            'date_paiement' => 'required|date|before_or_equal:today',
            'reference_transaction' => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'montant_verse.required' => 'Le montant est obligatoire.',
            'montant_verse.min' => 'Le montant doit être supérieur à 0.',
            'montant_verse.regex' => 'Format invalide (max 2 décimales).',
            'mode_paiement.required' => 'Le mode de paiement est obligatoire.',
            'date_paiement.before_or_equal' => 'La date ne peut pas être dans le futur.',
        ];
    }
}
