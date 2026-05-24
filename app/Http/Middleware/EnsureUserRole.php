<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user || ! in_array((string) $user->role, $roles, true)) {
            if ($request->expectsJson()) {
                abort(403, 'Unauthorized role.');
            }

            return redirect('/dashboard');
        }

        return $next($request);
    }
}
