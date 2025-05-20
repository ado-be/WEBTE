<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LocalizationController extends Controller
{
    public function __invoke($locale)
    {
        // Overenie, či požadovaný jazyk existuje v konfigurácii
        if (! in_array($locale, config('localization.locales'))) {
            abort(400);
        }

        session(['localization' => $locale]);
// Presmerovanie späť na predchádzajúcu stránku, cize nie vzdy na prvu
        return redirect()->back();
    }
}
