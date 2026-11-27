<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        if ($request->user()->hasRole('super_admin')) {
            return view('profile.super-admin-edit', [
                'user' => $request->user(),
            ]);
        }

        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();

        // Bloquer la modification de l'email pour les membres d'équipe
        if ($user->hasRole('team_member') && $request->email !== $user->email) {
            return Redirect::route('profile.edit')->withErrors(['email' => 'Votre adresse email est gérée par votre agence et ne peut être modifiée.']);
        }

        $user->fill($request->validated());

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        // Le Super Admin ne peut pas supprimer son compte via cette page
        if ($request->user()->hasRole('super_admin')) {
            return Redirect::route('profile.edit')->withErrors(['account' => 'Le compte Super Admin ne peut pas être supprimé via cette interface.']);
        }

        // Les membres d'équipe ne peuvent pas supprimer leur propre compte
        if ($request->user()->hasRole('team_member')) {
            return Redirect::route('profile.edit')->withErrors(['account' => 'Votre compte est géré par votre agence. Veuillez contacter un administrateur.']);
        }

        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
