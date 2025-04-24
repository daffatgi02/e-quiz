<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

class LanguageController extends Controller
{
    public function switchLang($lang)
    {
        if (array_key_exists($lang, config('app.languages'))) {
            Session::put('applocale', $lang);
            App::setLocale($lang);

            if (auth()->check()) {
                /** @var \App\Models\User $user */
                $user = auth()->user();
                $user->language = $lang;
                $user->save();
            }
        }
        return redirect()->back();
    }
}
