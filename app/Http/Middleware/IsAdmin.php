<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Ak používateľ nie je prihlásený, presmerujte na login
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // Ak používateľ nie je admin, vráťte chybu 403
        if (!Auth::user()->is_admin) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Access denied. Admins only.'], 403);
            }
            return redirect('/')->with('error', 'Nemáte oprávnenie pre prístup do tejto sekcie.');
        }

        return $next($request);
    }
}