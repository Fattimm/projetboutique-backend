<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    public function handle($request, Closure $next)
    {
        $user = Auth::user();

        if ($user && ($user->hasRole('ADMIN') || $user->hasRole('BOUTIQUIER'))) {
            return $next($request);
        }

        return response()->json(['message' => 'Accès interdit'], 403);
    }

}
