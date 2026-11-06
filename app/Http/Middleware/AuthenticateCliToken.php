<?php

namespace App\Http\Middleware;

use App\Models\CliToken;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateCliToken
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $header = $request->header('Authorization');

        if ($header && str_starts_with($header, 'Bearer arena_live_')) {
            $token = substr($header, 7);

            $cliToken = CliToken::query()
                ->with('user')
                ->where('token', $token)
                ->first();

            if ($cliToken && $cliToken->user) {
                $cliToken->forceFill([
                    'last_used_at' => now(),
                    'ip_address' => $request->ip(),
                ])->save();

                Auth::setUser($cliToken->user);
                $request->setUserResolver(fn () => $cliToken->user);
            }
        }

        return $next($request);
    }
}
