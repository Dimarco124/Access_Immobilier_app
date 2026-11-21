<?php

namespace App\Http\Controllers;

use App\Models\Locataire;
use App\Models\Proprietaire;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    /**
     * Servir une pièce d'identité d'un locataire de manière sécurisée
     */
    public function locatairePiece(Request $request, Locataire $locataire, string $type)
    {
        $allowedTypes = ['cni_recto', 'cni_verso', 'copie_piece_identite'];
        if (!in_array($type, $allowedTypes)) {
            abort(404, 'Type de document invalide');
        }

        $user = Auth::user();

        // 1. Même agence impérativement
        if ($locataire->agence_id !== $user->agence_id) {
            abort(403, 'Accès non autorisé');
        }

        // 2. Contrôle des droits
        if ($user->hasRole('admin')) {
            // Admin a un accès complet
        } elseif ($user->hasRole('team_member')) {
            if (!$user->hasModuleAccess('commercial', 'viewer')) {
                abort(403, 'Accès non autorisé à ce module');
            }
        } elseif ($user->hasRole('locataire')) {
            if ($locataire->user_id !== $user->id) {
                abort(403, 'Vous ne pouvez consulter que vos propres documents');
            }
        } else {
            abort(403, 'Accès non autorisé');
        }

        $path = $locataire->{$type};
        if (!$path) {
            abort(404, 'Document non renseigné');
        }

        return $this->serveFile($path, $request->boolean('download'));
    }

    /**
     * Servir une pièce d'identité d'un propriétaire de manière sécurisée
     */
    public function proprietairePiece(Request $request, Proprietaire $proprietaire, string $type)
    {
        $allowedTypes = ['cni_recto', 'cni_verso'];
        if (!in_array($type, $allowedTypes)) {
            abort(404, 'Type de document invalide');
        }

        $user = Auth::user();

        // 1. Même agence impérativement
        if ($proprietaire->agence_id !== $user->agence_id) {
            abort(403, 'Accès non autorisé');
        }

        // 2. Contrôle des droits
        if ($user->hasRole('admin')) {
            // Admin a un accès complet
        } elseif ($user->hasRole('team_member')) {
            if (!$user->hasModuleAccess('proprietaires', 'viewer')) {
                abort(403, 'Accès non autorisé à ce module');
            }
        } elseif ($user->hasRole('proprietaire')) {
            if ($proprietaire->user_id !== $user->id) {
                abort(403, 'Vous ne pouvez consulter que vos propres documents');
            }
        } else {
            abort(403, 'Accès non autorisé');
        }

        $path = $proprietaire->{$type};
        if (!$path) {
            abort(404, 'Document non renseigné');
        }

        return $this->serveFile($path, $request->boolean('download'));
    }

    /**
     * Stream le fichier depuis le stockage privé (ou public en fallback)
     */
    protected function serveFile(string $path, bool $download = false)
    {
        // 1. Chercher d'abord dans le stockage privé 'local' (storage/app/private)
        if (Storage::disk('local')->exists($path)) {
            return $download 
                ? Storage::disk('local')->download($path)
                : Storage::disk('local')->response($path);
        }

        // 2. Fallback dans le stockage 'public' si pas encore déplacé
        if (Storage::disk('public')->exists($path)) {
            return $download 
                ? Storage::disk('public')->download($path)
                : Storage::disk('public')->response($path);
        }

        abort(404, 'Fichier introuvable sur le serveur');
    }
}

