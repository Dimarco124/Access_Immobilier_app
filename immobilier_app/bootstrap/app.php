<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(at: '*');
        $middleware->web(append: [
            \App\Http\Middleware\CheckPlatformAccess::class,
        ]);
        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
            'module' => \App\Http\Middleware\CheckModuleAccess::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->respond(function (\Symfony\Component\HttpFoundation\Response $response, \Throwable $exception, \Illuminate\Http\Request $request) {
            if ($response->getStatusCode() === 419) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => 'Votre session a expiré. Veuillez rafraîchir la page.',
                        'redirect' => route('login'),
                    ], 419);
                }

                // Si l'utilisateur est encore connecté (ex: double onglet), on rafraîchit le formulaire précédent
                if (auth()->check() && !$request->is('login') && !$request->is('logout')) {
                    return back()
                        ->withInput($request->except('password', 'password_confirmation', '_token'))
                        ->with('error', 'La page avait expiré par inactivité. Veuillez valider à nouveau.');
                }

                // Sinon (session expirée ou soumission sur /login après longue inactivité) -> retour propre sur login
                return redirect()->route('login')
                    ->withInput($request->except('password', 'password_confirmation', '_token'))
                    ->with('status', 'Votre session a expiré par inactivité. Vous pouvez vous reconnecter.');
            }

            return $response;
        });
    })->create();
