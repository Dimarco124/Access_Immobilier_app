<?php

namespace App\Http\Requests\Auth;

use App\Models\PlatformActivityLog;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        if (! Auth::attempt($this->only('email', 'password'), $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey());

            PlatformActivityLog::record(
                'login_failed',
                'Tentative de connexion échouée pour ' . $this->string('email'),
                null,
                ['email' => (string) $this->string('email')]
            );

            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        $user = Auth::user();
        if ($user && !$user->hasRole('super_admin')) {
            if ($user->is_active === false) {
                Auth::logout();
                PlatformActivityLog::record('login_blocked', 'Connexion refusée (compte suspendu) : ' . $user->email, $user->agence_id, [], $user->id);
                throw ValidationException::withMessages([
                    'email' => 'Votre compte a été suspendu par l\'administrateur de la plateforme.',
                ]);
            }

            if ($user->agence && $user->agence->statut === 'suspendu') {
                Auth::logout();
                PlatformActivityLog::record('login_blocked_agency', 'Connexion refusée (agence suspendue) : ' . $user->email, $user->agence_id, [], $user->id);
                throw ValidationException::withMessages([
                    'email' => 'L\'accès de votre agence a été temporairement suspendu. Veuillez contacter le support.',
                ]);
            }
        }

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('email')).'|'.$this->ip());
    }
}
