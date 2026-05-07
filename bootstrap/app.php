<?php

use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Log;
use App\Services\AutoFriendlyMessageService;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Middleware
        $middleware->append([
            \App\Http\Middleware\TrustProxies::class,
            \App\Http\Middleware\PreventRequestsDuringMaintenance::class,
            \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
            \App\Http\Middleware\TrimStrings::class,
            \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
        ]);

        // RouteMiddleware / Alias
        $middleware->alias([
            'auth' => \App\Http\Middleware\Authenticate::class,
            'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
            'auth.session' => \Illuminate\Session\Middleware\AuthenticateSession::class,
            'cache.headers' => \Illuminate\Http\Middleware\SetCacheHeaders::class,
            'can' => \Illuminate\Auth\Middleware\Authorize::class,
            'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
            'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
            'signed' => \Illuminate\Routing\Middleware\ValidateSignature::class,
            'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
            'verified' => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,
            'XSS' => \App\Http\Middleware\XSS::class,
            'Pusher' => \App\Http\Middleware\getPusherSettings::class,
            '2fa' => \App\Http\Middleware\Google2fa::class,
        ]);

        // middlewareGroups / Group Middleware
        // Append middleware to the 'web' group
        $middleware->appendToGroup('web', [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\Session\Middleware\AuthenticateSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
            \App\Http\Middleware\FilterRequest::class,
            \App\Http\Middleware\DetectUserLanguage::class,
        ]);

        // Append middleware to the 'api' group
        $middleware->appendToGroup('api', [
            'throttle:api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ]);

        // Exclude specific routes from CSRF protection
        $middleware->validateCsrfTokens(
            except: [
                'plan/paytm/*',
                'invoice/paytm/*',
                'plan-pay-with-paymentwall/*',
                'iyzipay/callback/*',
                'paytab-success/*',
                '/aamarpay*',
            ]
        );
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->renderable(function (AuthenticationException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Authentication failed. Please login again.',
                ], 401);
            }
        });

        // Handle API Validation Exception
        $exceptions->renderable(function (ValidationException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation error.',
                    'errors' => $e->errors(),
                ], 422);
            }
        });

        // Handle API 404 Exception
        $exceptions->renderable(function (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'API endpoint not found.',
                ], 404);
            }
        });

        $exceptions->renderable(function (\Illuminate\Http\Exceptions\ThrottleRequestsException $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Too many requests. Please slow down.',
                ], 429);
            }

            return redirect()->back()->with([
                'error' => 'You are sending too many requests. Please try again later.'
            ]);
        });

        // Fallback for API Errors
        $exceptions->renderable(function (Throwable $e, $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Something went wrong. Please try again later.',
                ], 500);
            }
        });

        $exceptions->renderable(function (Throwable $e, $request) {
            if (
                !$request->expectsJson() &&
                app()->environment('production') &&
                !($e instanceof ValidationException) &&
                !($e instanceof AuthenticationException)
            ) {
                $code = uniqid('ERR-');

                Log::error("Unhandled Exception [{$code}]", [
                    'code' => $code,
                    'message' => $e->getMessage(),
                    'url' => $request->fullUrl(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ]);

                $status = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : 500;

                return response()->view('errors.custom', [
                    'message' => AutoFriendlyMessageService::generate($e),
                    'code' => $code,
                    'status' => $status,
                ], $status);
            }
        });
    })->create();
