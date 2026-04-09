<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Throwable;

class DashboardController extends Controller
{
    public function home(): View
    {
        return view('welcome');
    }

    public function redirectByRole(Request $request): RedirectResponse
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('login');
        }

        // Bewaar flash-berichten (zoals succesvol ingelogd/geregistreerd) over deze extra redirect heen.
        $request->session()->reflash();

        if ($user->isDirectie()) {
            return redirect()->route('directie.dashboard');
        }

        if ($user->isMagazijnMedewerker()) {
            return redirect()->route('magazijn.dashboard');
        }

        if ($user->isVrijwilliger()) {
            return redirect()->route('vrijwilliger.dashboard');
        }

        // Onbekende rollen gaan naar de normale homepagina.
        return redirect()->route('home');
    }

    public function directie(Request $request): View
    {
        $accounts = collect();

        try {
            $accounts = Account::overzichtLijst();

            Log::info('Technische log: directie dashboard geladen.', [
                'user_id' => $request->user()?->id,
                'accounts_totaal' => $accounts->count(),
            ]);
        } catch (Throwable $exception) {
            Log::error('Technische log: directie dashboard fout.', [
                'user_id' => $request->user()?->id,
                'error_class' => $exception::class,
                'error_message' => $exception->getMessage(),
            ]);
        }

        return view('dashboard.directie', [
            'accounts' => $accounts,
        ]);
    }

    public function technischeLogs(Request $request): View
    {
        $entries = collect();
        $statusError = null;
        $logPath = storage_path('logs/laravel.log');

        try {
            if (! is_file($logPath)) {
                $statusError = 'Logbestand niet gevonden.';
            } else {
                $regels = file($logPath, FILE_IGNORE_NEW_LINES);
                $laatsteRegels = array_slice($regels ?: [], -200);

                foreach ($laatsteRegels as $regel) {
                    $regel = trim((string) $regel);

                    if ($regel === '') {
                        continue;
                    }

                    if (preg_match('/^\[(.*?)\]\s+\w+\.(\w+):\s+(.*)$/', $regel, $matches) === 1) {
                        $entries->push([
                            'tijd' => $matches[1],
                            'level' => strtolower($matches[2]),
                            'bericht' => $matches[3],
                        ]);
                        continue;
                    }

                    $entries->push([
                        'tijd' => '-',
                        'level' => 'raw',
                        'bericht' => $regel,
                    ]);
                }
            }

            Log::info('Technische log: directie heeft logpagina geopend.', [
                'user_id' => $request->user()?->id,
                'gevonden_regels' => $entries->count(),
            ]);
        } catch (Throwable $exception) {
            $statusError = 'Technische logs konden niet geladen worden.';

            Log::error('Technische log: laden logpagina mislukt.', [
                'user_id' => $request->user()?->id,
                'error_class' => $exception::class,
                'error_message' => $exception->getMessage(),
            ]);
        }

        return view('dashboard.logs', [
            'entries' => $entries,
            'logPath' => $logPath,
            'status_error' => $statusError,
        ]);
    }

    public function magazijn(Request $request): View
    {
        $profiel = null;

        try {
            $profiel = Account::profielVanGebruiker($request->user()->id);

            Log::info('Technische log: magazijn dashboard geladen.', [
                'user_id' => $request->user()?->id,
            ]);
        } catch (Throwable $exception) {
            Log::error('Technische log: magazijn dashboard fout.', [
                'user_id' => $request->user()?->id,
                'error_class' => $exception::class,
                'error_message' => $exception->getMessage(),
            ]);
        }

        return view('dashboard.magazijn', [
            'profiel' => $profiel,
        ]);
    }

    public function vrijwilliger(Request $request): View
    {
        $profiel = null;

        try {
            $profiel = Account::profielVanGebruiker($request->user()->id);

            Log::info('Technische log: vrijwilliger dashboard geladen.', [
                'user_id' => $request->user()?->id,
            ]);
        } catch (Throwable $exception) {
            Log::error('Technische log: vrijwilliger dashboard fout.', [
                'user_id' => $request->user()?->id,
                'error_class' => $exception::class,
                'error_message' => $exception->getMessage(),
            ]);
        }

        return view('dashboard.vrijwilliger', [
            'profiel' => $profiel,
        ]);
    }
}

