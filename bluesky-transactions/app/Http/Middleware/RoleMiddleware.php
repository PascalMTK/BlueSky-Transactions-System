<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        if (!in_array($user->role, $roles)) {
            abort(403, 'Accès non autorisé.');
        }

        if ($user->status !== 'active' && $user->role !== 'admin') {
            auth()->logout();
            return redirect()->route('login')->with('error', 'Votre compte est en attente d\'activation ou a été désactivé.');
        }

        return $next($request);
    }
}
