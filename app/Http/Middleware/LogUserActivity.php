<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\UserActivity;

class LogUserActivity
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if (Auth::check()) {
            UserActivity::create([
                'user_id' => Auth::id(),
                'action' => $request->method() . ' ' . $request->path(),
                'source' => $request->is('api/*') ? 'api' : 'web',
                'ip' => $request->ip(),
                'location' => geoip($request->ip())['city'] . ', ' . geoip($request->ip())['country'],
            ]);
        }

        return $response;
    }
}
