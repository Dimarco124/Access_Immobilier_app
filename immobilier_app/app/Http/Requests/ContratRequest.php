<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ContratRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::user()->hasRole('admin');
    }

    public function rules(): array
    {
        return [
            'locataire_id' => 'required|exists:locataires,id',
            'bien_id' => 'required|exists:biens,id',
            'date_debut' => 'required|date',
            'duree_bail_mois' => 'required|integer|min:1|max:600',
            'montant_loyer_initial' => 'required|numeric|min:0|max:999999999',
            'montant_charges' => 'nullable|numeric|min:0|max:999999999',
            'montant_caution' => 'required|numeric|min:0|max:999999999',
            'periodicite_paiement' => 'required|in:Mensuel,Trimestriel,Semestriel,Annuel',
            'date_signature' => 'nullable|date|before_or_equal:today',
            'conditions_particulieres' => 'nullable|string|max:2000',
            'clause_resiliation' => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'locataire_id.required' => 'Le locataire est obligatoire.',
            'locataire_id.exists' => 'Le locataire sélectionné n\'existe pas.',
            'bien_id.required' => 'Le bien est obligatoire.',
            'bien_id.exists' => 'Le bien sélectionné n\'existe pas.',
            'date_debut.required' => 'La date de début est obligatoire.',
            'duree_bail_mois.required' => 'La durée du bail est obligatoire.',
            'duree_bail_mois.min' => 'La durée du bail doit être d\'au moins 1 mois.',
            'montant_loyer_initial.required' => 'Le montant du loyer est obligatoire.',
            'montant_caution.required' => 'Le montant de la caution est obligatoire.',
            'periodicite_paiement.required' => 'La périodicité de paiement est obligatoire.',
        ];
    }
}
