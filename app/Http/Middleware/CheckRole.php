<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  array  ...$roles
     * @return mixed
     */
   
     public function handle($request, Closure $next)
     {
         $user = Auth::user();
 
         if ($user && $user->role === 'ADMIN') {
             return $next($request);
         }

         if ($user && $user->role === 'BOUTIQUIER') {
            dd($user);
             return $next($request);
         }
 
         return response()->json(['message' => 'Accès interdit'], 403);
     }
    
}
