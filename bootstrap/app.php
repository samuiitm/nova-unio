<?php

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use Illuminate\Validation\ValidationException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'panel.access' => \App\Http\Middleware\EnsurePanelAccess::class,
            'panel.manage' => \App\Http\Middleware\EnsurePanelManagement::class,
            'panel.admin'  => \App\Http\Middleware\EnsureAdmin::class,
        ]);

        $middleware->redirectGuestsTo(fn () => route('login'));
        $middleware->redirectUsersTo(fn () => route('panel.home'));
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $renderPanelError = function (Request $request, int $status, array $data = []) {
            if ($request->expectsJson()) {
                return null;
            }

            if (! $request->is('panel') && ! $request->is('panel/*')) {
                return null;
            }

            $view = view()->exists("errors.panel.$status")
                ? "errors.panel.$status"
                : 'errors.panel.generic';

            return response()->view($view, array_merge([
                'status' => $status,
            ], $data), $status);
        };

        // IMPORTANTE: si no está autenticado, redirigir a login, no pintar 500
        $exceptions->render(function (AuthenticationException $e, Request $request) {
            if ($request->expectsJson()) {
                return null;
            }

            if ($request->is('panel') || $request->is('panel/*')) {
                return redirect()->guest(route('login'));
            }

            return null;
        });

        $exceptions->render(function (NotFoundHttpException $e, Request $request) use ($renderPanelError) {
            return $renderPanelError($request, 404);
        });

        $exceptions->render(function (AuthorizationException|AccessDeniedHttpException $e, Request $request) use ($renderPanelError) {
            return $renderPanelError($request, 403);
        });

        $exceptions->render(function (TokenMismatchException $e, Request $request) use ($renderPanelError) {
            return $renderPanelError($request, 419);
        });

        $exceptions->render(function (TooManyRequestsHttpException $e, Request $request) use ($renderPanelError) {
            return $renderPanelError($request, 429);
        });

        $exceptions->render(function (HttpException $e, Request $request) use ($renderPanelError) {
            $status = $e->getStatusCode();

            if (in_array($status, [403, 404, 419, 429], true)) {
                return null;
            }

            return $renderPanelError($request, $status);
        });

        $exceptions->render(function (ValidationException $e, Request $request) {
            if ($request->expectsJson()) {
                return null;
            }

            if ($request->is('panel') || $request->is('panel/*')) {
                return redirect()
                    ->back()
                    ->withInput($request->except(['password', 'password_confirmation']))
                    ->withErrors($e->errors());
            }

            return null;
        });

        $exceptions->render(function (Throwable $e, Request $request) use ($renderPanelError) {
            if (app()->environment('local')) {
                return null;
            }

            return $renderPanelError($request, 500);
        });
    })->create();