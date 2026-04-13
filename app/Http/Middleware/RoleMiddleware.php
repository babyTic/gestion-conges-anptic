<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * Usage: ->middleware('role:agent,rh')
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login')->with('error', 'Veuillez vous connecter.');
        }

        // Normaliser casse si besoin
        $userRole = strtolower($user->role);
        $allowed = array_map('strtolower', $roles);

        if (!in_array($userRole, $allowed)) {
            // Redirige proprement vers dashboard avec message
            return redirect()->route('dashboard')->with('error', 'Accès non autorisé.');
            // ou : abort(403, 'Accès refusé.');
        }

        return $next($request);
    }
}
