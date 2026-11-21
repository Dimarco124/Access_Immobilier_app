<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class BienRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::user()->hasRole('admin');
    }

    public function rules(): array
    {
        return [
            'nom_bien' => 'required|string|max:255',
            'type_bien' => 'required|in:Appartement,Villa,Studio,Bureau,Magasin,Terrain,Autre',
            'adresse' => 'required|string|max:500',
            'ville' => 'required|string|max:100',
            'quartier' => 'nullable|string|max:100',
            'superficie' => 'nullable|numeric|min:1|max:999999',
            'nombre_pieces' => 'nullable|integer|min:1|max:100',
            'nombre_chambres' => 'nullable|integer|min:0|max:50',
            'nombre_salles_bain' => 'nullable|integer|min:0|max:20',
            'montant_loyer' => 'required|numeric|min:0|max:999999999',
            'montant_charges' => 'nullable|numeric|min:0|max:999999999',
            'montant_caution' => 'nullable|numeric|min:0|max:999999999',
            'taux_commission_agence' => 'required|numeric|min:0|max:100',
            'proprietaire_id' => 'required|exists:proprietaires,id',
            'famille_de_bien_id' => 'required|exists:familles_de_biens,id',
            'description' => 'nullable|string|max:2000',
            'equipements' => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'nom_bien.required' => 'Le nom du bien est obligatoire.',
            'type_bien.required' => 'Le type de bien est obligatoire.',
            'adresse.required' => 'L\'adresse est obligatoire.',
            'montant_loyer.required' => 'Le montant du loyer est obligatoire.',
            'taux_commission_agence.required' => 'Le taux de commission est obligatoire.',
            'proprietaire_id.required' => 'Le propriétaire est obligatoire.',
            'proprietaire_id.exists' => 'Le propriétaire sélectionné n\'existe pas.',
            'famille_de_bien_id.required' => 'La famille de bien est obligatoire.',
            'famille_de_bien_id.exists' => 'La famille de bien sélectionnée n\'existe pas.',
        ];
    }
}
