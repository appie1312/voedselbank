<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Throwable;

class AuthController extends Controller
{
    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    public function showRegisterForm(): View
    {
        return view('auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $attributes = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        try {
            $attributes['role'] = User::ROLE_VRIJWILLIGER;
            $nieuwAccount = Account::voegToeMetProfiel($attributes);

            Auth::loginUsingId($nieuwAccount->id);
            $request->session()->regenerate();

            Log::info('Technische log: registratie gelukt.', [
                'user_id' => $nieuwAccount->id,
                'email' => $nieuwAccount->email,
                'role' => User::ROLE_VRIJWILLIGER,
            ]);
        } catch (Throwable $exception) {
            Log::error('Technische log: registratie mislukt.', [
                'error_class' => $exception::class,
                'error_message' => $exception->getMessage(),
                'email_payload' => $request->input('email'),
            ]);

            return back()
                ->withInput($request->except(['password', 'password_confirmation']))
                ->with('status_error', 'Registratie is mislukt. Controleer de technische log.');
        }

        return redirect()
            ->route('dashboard.redirect')
            ->with('status_success', 'Jij bent nu een lid bij ons voedselbank.');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'string'],
        ]);

        try {
            if (! Auth::attempt($credentials)) {
                Log::warning('Technische log: inloggen mislukt.', [
                    'email_payload' => $request->input('email'),
                ]);

                return back()
                    ->withInput($request->only('email'))
                    ->with('status_error', 'Voer de juiste inloggegevens in.');
            }

            $request->session()->regenerate();

            Log::info('Technische log: inloggen gelukt.', [
                'user_id' => Auth::id(),
                'email' => $request->input('email'),
            ]);

            return redirect()
                ->route('dashboard.redirect')
                ->with('status_success', 'Succesvol ingelogd.');
        } catch (Throwable $exception) {
            Log::error('Technische log: inloggen crashte.', [
                'error_class' => $exception::class,
                'error_message' => $exception->getMessage(),
                'email_payload' => $request->input('email'),
            ]);

            return back()
                ->withInput($request->only('email'))
                ->with('status_error', 'Inloggen is mislukt door een systeemfout.');
        }
    }

    public function logout(Request $request): RedirectResponse
    {
        Log::info('Technische log: gebruiker logt uit.', [
            'user_id' => $request->user()?->id,
        ]);

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('home')
            ->with('status_success', 'Succesvol uitgelogd.');
    }
}
