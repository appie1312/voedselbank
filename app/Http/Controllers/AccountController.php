<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;

class AccountController extends Controller
{
    public function index(): RedirectResponse
    {
        return redirect()->route('klanten.index');
    }
}
