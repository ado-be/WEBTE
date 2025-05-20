<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\URL;

class Localization
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        // Nastaví jazyk aplikácie na hodnotu zo session,
        // alebo použije predvolený jazyk z config('app.locale') ak session hodnota neexistuje
        app()->setLocale(session('localization', config('app.locale')));



        return $next($request);
    }
}
