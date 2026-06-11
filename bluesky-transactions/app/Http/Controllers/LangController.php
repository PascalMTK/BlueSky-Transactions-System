<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class LangController extends Controller
{
    public function switch(string $locale)
    {
        if (in_array($locale, ['fr', 'en'])) {
            Session::put('locale', $locale);
        }
        return back();
    }
}
