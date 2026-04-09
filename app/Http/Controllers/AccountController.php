<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Throwable;

class AccountController extends Controller
{
    public function index(): View
    {
        $accounts = collect();

        try {
            $accounts = Account::overzichtLijst();

            Log::info('Technische log: directie accountoverzicht geladen.', [
                'aantal_accounts' => $accounts->count(),
            ]);
        } catch (Throwable $exception) {
            Log::error('Technische log: accountoverzicht laden mislukt.', [
                'error_class' => $exception::class,
                'error_message' => $exception->getMessage(),
            ]);

            return view('accounts.index', [
                'accounts' => $accounts,
                'status_error' => 'Accountoverzicht kon niet geladen worden.',
            ]);
        }

        return view('accounts.index', [
            'accounts' => $accounts,
        ]);
    }
}
