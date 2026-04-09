<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Throwable;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        $profiel = null;

        try {
            $profiel = Account::profielVanGebruiker($request->user()->id);

            Log::info('Technische log: profielpagina geladen.', [
                'user_id' => $request->user()?->id,
            ]);
        } catch (Throwable $exception) {
            Log::error('Technische log: profielpagina laden mislukt.', [
                'user_id' => $request->user()?->id,
                'error_class' => $exception::class,
                'error_message' => $exception->getMessage(),
            ]);
        }

        return view('profile.edit', [
            'profiel' => $profiel,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        $attributes = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'telefoon' => ['nullable', 'string', 'max:30'],
            'adres' => ['nullable', 'string', 'max:255'],
            'afdeling' => ['nullable', 'string', 'max:120'],
            'beschikbaarheid' => ['nullable', 'string', 'max:120'],
            'verantwoordelijkheden' => ['nullable', 'string', 'max:120'],
            'bio' => ['nullable', 'string', 'max:1500'],
        ]);

        try {
            DB::transaction(function () use ($user, $attributes): void {
                $user->update([
                    'name' => $attributes['name'],
                    'email' => $attributes['email'],
                ]);

                $user->profile()->updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'telefoon' => $attributes['telefoon'] ?? null,
                        'adres' => $attributes['adres'] ?? null,
                        'afdeling' => $attributes['afdeling'] ?? null,
                        'beschikbaarheid' => $attributes['beschikbaarheid'] ?? null,
                        'verantwoordelijkheden' => $attributes['verantwoordelijkheden'] ?? null,
                        'bio' => $attributes['bio'] ?? null,
                    ]
                );
            });

            Log::info('Technische log: profiel bijgewerkt.', [
                'user_id' => $user->id,
                'email_nieuw' => $attributes['email'],
            ]);
        } catch (Throwable $exception) {
            Log::error('Technische log: profiel bijwerken mislukt.', [
                'user_id' => $user->id,
                'error_class' => $exception::class,
                'error_message' => $exception->getMessage(),
            ]);

            return back()
                ->withInput()
                ->with('status_error', 'Profiel opslaan is mislukt. Controleer de technische log.');
        }

        return redirect()
            ->route('profile.edit')
            ->with('status_success', 'Profiel succesvol bijgewerkt.');
    }
}

