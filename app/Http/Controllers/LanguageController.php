<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class LanguageController extends Controller
{
    public function switchLang($lang)
    {
        if (!in_array($lang, ['sk', 'en'])) {
            $lang = 'sk';
        }

        Session::put('locale', $lang);
        App::setLocale($lang);

        return redirect()->back();
    }
}