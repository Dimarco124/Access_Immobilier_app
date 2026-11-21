<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;

trait PrintableList
{
    /**
     * Prépare les données pour l'impression
     */
    protected function preparePrintData($query, $title, $columns = [])
    {
        $items = $query->get(); // Récupérer tous les items pour l'impression
        $agence = Auth::user()->agence;
        $filters = request()->except(['_token', 'page']);
        
        return [
            'items' => $items,
            'agence' => $agence,
            'title' => $title,
            'columns' => $columns,
            'filters' => $filters,
            'printDate' => now()->format('d/m/Y H:i'),
            'printedBy' => Auth::user()->name,
        ];
    }
}
