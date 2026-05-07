<?php

namespace App\Http\Middleware;

use Closure;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PragmaRX\Google2FALaravel\Support\Authenticator;
use Symfony\Component\HttpFoundation\Response;

class Google2fa
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        
        if ($user && $user->google2fa_secret && $user->google2fa_enable == 1) {
            $authenticator = app(Authenticator::class)->boot($request);
            if ($authenticator->isAuthenticated()) {
                return $next($request);
            }

            return $authenticator->makeRequestOneTimePasswordResponse();
        } else {
            return $next($request);
        }
    }
}
