<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreLeverancierRequest;
use App\Models\Leverancier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

/**
 * Controller voor het beheer van leveranciers.
 *
 * Rollen: directie, magazijnmedewerker, vrijwilliger.
 * Aanmaken is alleen voorbehouden aan de directie-rol.
 */
class LeverancierController extends Controller
{
    // ----------------------------------------------------------------
    // Publieke acties
    // ----------------------------------------------------------------

    /**
     * Toont het overzicht van alle leveranciers met hun producten.
     *
     * GET /leveranciers
     */
    public function index(): View
    {
        try {
            $leveranciers = Leverancier::getAllMetProducten();

            Log::info('[LeverancierController] Overzicht geladen.', [
                'gebruiker' => auth()->id(),
                'aantal'    => $leveranciers->count(),
            ]);

            return view('leveranciers.index', compact('leveranciers'));
        } catch (\Throwable $e) {
            Log::error('[LeverancierController] Fout bij laden overzicht.', [
                'gebruiker' => auth()->id(),
                'fout'      => $e->getMessage(),
                'trace'     => $e->getTraceAsString(),
            ]);

            return view('leveranciers.index', ['leveranciers' => collect()])
                ->with('error', 'Er is een technische fout opgetreden. Probeer het later opnieuw.');
        }
    }

    /**
     * Verwerkt het formulier voor het aanmaken van een nieuwe leverancier.
     * Alleen directie heeft toegang (middleware in routes).
     *
     * POST /leveranciers
     */
    public function store(StoreLeverancierRequest $request): RedirectResponse
    {
        // Gevalideerde data — server-side validatie zit in StoreLeverancierRequest
        $gevalideerd = $request->validated();

        try {
            $uitvoer = Leverancier::aanmakenViaSP(
                naam:     $gevalideerd['naam'],
                adres:    $gevalideerd['adres']     ?? null,
                telefoon: $gevalideerd['telefoon']  ?? null,
                email:    $gevalideerd['email']     ?? null,
            );

            // De SP zet zelf een foutmelding als naam al bestaat
            if ($uitvoer['fout'] !== null) {
                Log::warning('[LeverancierController] Leverancier aanmaken geweigerd door SP.', [
                    'naam'      => $gevalideerd['naam'],
                    'fout'      => $uitvoer['fout'],
                    'gebruiker' => auth()->id(),
                ]);

                return redirect()
                    ->route('leveranciers.index')
                    ->with('error', $uitvoer['fout']);
            }

            Log::info('[LeverancierController] Leverancier succesvol aangemaakt.', [
                'nieuw_id'  => $uitvoer['nieuw_id'],
                'naam'      => $gevalideerd['naam'],
                'gebruiker' => auth()->id(),
            ]);

            return redirect()
                ->route('leveranciers.index')
                ->with('success', 'leverancier is succesvol toegevoegd');
        } catch (\Throwable $e) {
            Log::error('[LeverancierController] Technische fout bij aanmaken leverancier.', [
                'naam'      => $gevalideerd['naam'],
                'gebruiker' => auth()->id(),
                'fout'      => $e->getMessage(),
                'trace'     => $e->getTraceAsString(),
            ]);

            return redirect()
                ->route('leveranciers.index')
                ->with('error', 'Er is een technische fout opgetreden. Probeer het later opnieuw.');
        }
    }
}
