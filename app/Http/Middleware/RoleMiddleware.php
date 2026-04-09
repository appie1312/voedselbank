<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user || ! in_array($user->role, $roles, true)) {
            Log::warning('Technische log: toegang geweigerd op rol.', [
                'user_id' => $user?->id,
                'role_user' => $user?->role,
                'toegestaan' => $roles,
                'url' => $request->fullUrl(),
            ]);

            abort(403, 'Geen toegang tot deze pagina.');
        }

        return $next($request);
    }
}

