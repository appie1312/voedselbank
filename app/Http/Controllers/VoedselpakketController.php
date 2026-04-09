<?php

namespace App\Http\Controllers;

use App\Models\Klant;
use App\Models\Voedselpakket;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Throwable;

class VoedselpakketController extends Controller
{
    // Overzicht inclusief inhouds-samenvatting per pakket.
    public function index(Request $request): View
    {
        $zoekterm = (string) $request->input('zoekterm', '');
        $aantalRijen = (int) $request->input('aantal_rijen', 5);

        try {
            $pakketten = Voedselpakket::haalOverzichtViaStoredProcedure($zoekterm, $aantalRijen);
            $pakketten = $this->vulInhoudAan($pakketten);
        } catch (Throwable $exception) {
            Log::error('Technische log: voedselpakkettenoverzicht laden mislukt.', [
                'user_id' => $request->user()?->id,
                'error_class' => $exception::class,
                'error_message' => $exception->getMessage(),
            ]);

            return view('voedselpakketten.index', [
                'pakketten' => collect(),
                'status_error' => 'Het overzicht kon niet worden geladen. Vernieuw de pagina of probeer het later opnieuw.',
            ]);
        }

        return view('voedselpakketten.index', ['pakketten' => $pakketten]);
    }

    public function create(): View|RedirectResponse
    {
        try {
            $klanten = Klant::orderBy('gezinsnaam')->get();

            if ($klanten->isEmpty()) {
                return redirect()->route($this->roleRoute('index'))
                    ->with('status_error', 'Je kunt nog geen pakket registreren: er zijn nog geen gezinnen beschikbaar.');
            }
        } catch (Throwable $exception) {
            Log::error('Technische log: voedselpakket create-form laden mislukt.', [
                'user_id' => auth()->id(),
                'error_class' => $exception::class,
                'error_message' => $exception->getMessage(),
            ]);

            return redirect()->route($this->roleRoute('index'))
                ->with('status_error', 'Er is een storing. Het formulier kon niet worden geladen.');
        }

        return view('voedselpakketten.create', compact('klanten'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'klant_id' => 'required|exists:klanten,id',
        ]);

        try {
            // Businessregel: 1 open pakket per gezin tegelijk.
            $heeftOpenPakket = DB::table('voedselpakketten')
                ->where('klant_id', (int) $request->klant_id)
                ->whereNull('datum_uitgifte')
                ->exists();

            if ($heeftOpenPakket) {
                return back()
                    ->withInput()
                    ->with('status_error', 'Dit gezin heeft al een open pakket. Rond dat pakket eerst af.');
            }

            $pakketId = Voedselpakket::voegToeViaQuery((int) $request->klant_id);
        } catch (Throwable $exception) {
            Log::error('Technische log: voedselpakket toevoegen mislukt.', [
                'user_id' => $request->user()?->id,
                'error_class' => $exception::class,
                'error_message' => $exception->getMessage(),
            ]);

            return back()
                ->withInput()
                ->with('status_error', 'Het voedselpakket kon niet worden opgeslagen.');
        }

        return redirect()->route($this->roleRoute('samenstellen'), ['id' => $pakketId])
            ->with('status_success', 'Voedselpakket geregistreerd. Voeg nu de inhoud toe.');
    }

    public function edit(int $id): View|RedirectResponse
    {
        try {
            $pakket = Voedselpakket::haalVoorWijzigenViaJoin($id);

            if (! $pakket) {
                return redirect()->route($this->roleRoute('index'))
                    ->with('status_error', 'Pakket #' . $id . ' bestaat niet (meer).');
            }

            $klanten = Klant::orderBy('gezinsnaam')->get();
        } catch (Throwable $exception) {
            Log::error('Technische log: voedselpakket wijzigen-form laden mislukt.', [
                'user_id' => auth()->id(),
                'pakket_id' => $id,
                'error_class' => $exception::class,
                'error_message' => $exception->getMessage(),
            ]);

            return redirect()->route($this->roleRoute('index'))
                ->with('status_error', 'Er is een storing. Het wijzigformulier kon niet worden geladen.');
        }

        return view('voedselpakketten.edit', compact('pakket', 'klanten'));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'klant_id' => 'required|exists:klanten,id',
            'opgehaald' => 'nullable|boolean',
        ]);

        try {
            $pakket = DB::table('voedselpakketten')
                ->where('id', $id)
                ->select(['id', 'datum_uitgifte'])
                ->first();

            if (! $pakket) {
                return redirect()->route($this->roleRoute('index'))
                    ->with('status_error', 'Pakket #' . $id . ' bestaat niet (meer).');
            }

            if (! is_null($pakket->datum_uitgifte) && ! $request->boolean('opgehaald')) {
                // Een afgehandeld pakket mag niet terug naar "niet opgehaald".
                return back()
                    ->withInput()
                    ->with('status_error', 'Een opgehaald pakket kan niet teruggezet worden naar niet-opgehaald.');
            }

            $gewijzigd = Voedselpakket::wijzigViaQuery($id, [
                'klant_id' => (int) $request->klant_id,
                'opgehaald' => $request->boolean('opgehaald'),
            ]);

            if (! $gewijzigd) {
                return redirect()->route($this->roleRoute('index'))
                    ->with('status_error', 'Pakket #' . $id . ' kon niet worden bijgewerkt.');
            }
        } catch (Throwable $exception) {
            Log::error('Technische log: voedselpakket wijzigen mislukt.', [
                'user_id' => $request->user()?->id,
                'pakket_id' => $id,
                'error_class' => $exception::class,
                'error_message' => $exception->getMessage(),
            ]);

            return back()
                ->withInput()
                ->with('status_error', 'Het voedselpakket kon niet worden gewijzigd.');
        }

        return redirect()->route($this->roleRoute('index'))
            ->with('status_success', 'Het voedselpakket is bijgewerkt.');
    }

    public function destroy(Request $request, int $id): RedirectResponse
    {
        try {
            $pakket = DB::table('voedselpakketten')
                ->where('id', $id)
                ->select(['id', 'datum_uitgifte'])
                ->first();

            if (! $pakket) {
                return redirect()->route($this->roleRoute('index'))
                    ->with('status_error', 'Pakket #' . $id . ' bestaat niet (meer).');
            }

            if (is_null($pakket->datum_uitgifte)) {
                // Verwijderen mag pas na uitgifte.
                return redirect()->route($this->roleRoute('index'))
                    ->with('status_error', 'Pakket #' . $id . ' kan nog niet verwijderd worden: het is nog niet opgehaald.');
            }

            $verwijderd = Voedselpakket::verwijderViaQuery($id);

            if (! $verwijderd) {
                return redirect()->route($this->roleRoute('index'))
                    ->with('status_error', 'Pakket #' . $id . ' kon niet worden verwijderd.');
            }
        } catch (Throwable $exception) {
            Log::error('Technische log: voedselpakket verwijderen mislukt.', [
                'user_id' => $request->user()?->id,
                'pakket_id' => $id,
                'error_class' => $exception::class,
                'error_message' => $exception->getMessage(),
            ]);

            return redirect()->route($this->roleRoute('index'))
                ->with('status_error', 'Het voedselpakket kon niet worden verwijderd.');
        }

        return redirect()->route($this->roleRoute('index'))
            ->with('status_success', 'Het voedselpakket is verwijderd.');
    }

    public function mijnPakket()
    {
        // We zoeken het pakket waar de klant_id overeenkomt met de ID van de ingelogde gebruiker
        $pakket = \App\Models\Voedselpakket::where('klant_id', auth()->user()->id)
                    ->orderBy('datum_samenstelling', 'desc')
                    ->first();

        return view('voedselpakketten.mijn-pakket', compact('pakket'));
    }

    public function samenstellen(int $id): View|RedirectResponse
    {
        try {
            $pakket = Voedselpakket::haalVoorWijzigenViaJoin($id);

            if (! $pakket) {
                return redirect()->route($this->roleRoute('index'))
                    ->with('status_error', 'Het voedselpakket kon niet worden gevonden.');
            }

            $voorraadProducten = DB::table('voorraad as v')
                ->join('products as p', 'p.id', '=', 'v.product_id')
                ->join('categories as c', 'c.id', '=', 'p.categorie_id')
                ->select([
                    'p.id',
                    'p.productnaam',
                    'p.ean_nummer',
                    'v.hoeveelheid',
                    'c.naam as categorie_naam',
                ])
                ->orderBy('c.naam')
                ->orderBy('p.productnaam')
                ->get();

            // Vooraf invullen van bestaande pakketinhoud.
            $huidigeSamenstelling = DB::table('pakket_product')
                ->where('pakket_id', $id)
                ->pluck('aantal', 'product_id');
        } catch (Throwable $exception) {
            Log::error('Technische log: voedselpakket samenstellen-form laden mislukt.', [
                'user_id' => auth()->id(),
                'pakket_id' => $id,
                'error_class' => $exception::class,
                'error_message' => $exception->getMessage(),
            ]);

            return redirect()->route($this->roleRoute('index'))
                ->with('status_error', 'Er is een storing. Het samenstellen-scherm kon niet worden geladen.');
        }

        return view('voedselpakketten.samenstellen', [
            'pakket' => $pakket,
            'voorraadProducten' => $voorraadProducten,
            'huidigeSamenstelling' => $huidigeSamenstelling,
        ]);
    }

    public function opslaanSamenstelling(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'aantallen' => 'nullable|array',
            'aantallen.*' => 'nullable|integer|min:0',
        ]);

        try {
            $pakketBestaat = DB::table('voedselpakketten')->where('id', $id)->exists();
            if (! $pakketBestaat) {
                return redirect()->route($this->roleRoute('index'))
                    ->with('status_error', 'Pakket #' . $id . ' bestaat niet (meer).');
            }

            $aangevraagd = collect((array) $request->input('aantallen', []))
                ->mapWithKeys(fn ($value, $productId) => [(int) $productId => max(0, (int) $value)]);

            if ($aangevraagd->filter(fn ($aantal) => $aantal > 0)->isEmpty()) {
                // Minimaal 1 product is verplicht voor opslaan.
                return back()
                    ->withInput()
                    ->with('status_error', 'Kies minimaal een product met aantal groter dan 0.');
            }

            $voorraad = DB::table('voorraad')
                ->pluck('hoeveelheid', 'product_id')
                ->mapWithKeys(fn ($qty, $productId) => [(int) $productId => (int) $qty]);

            $productNamen = DB::table('products')
                ->pluck('productnaam', 'id')
                ->mapWithKeys(fn ($naam, $id) => [(int) $id => (string) $naam]);

            $bestaand = DB::table('pakket_product')
                ->where('pakket_id', $id)
                ->pluck('aantal', 'product_id')
                ->mapWithKeys(fn ($qty, $productId) => [(int) $productId => (int) $qty]);

            foreach ($aangevraagd as $productId => $nieuwAantal) {
                if (! $voorraad->has($productId)) {
                    $naam = $productNamen->get($productId, 'Onbekend product');
                    return back()
                        ->withInput()
                        ->with('status_error', $naam . ' staat niet in voorraad en kan niet toegevoegd worden.');
                }

                $oudAantal = $bestaand->get($productId, 0);
                $extraNodig = $nieuwAantal - $oudAantal;
                $beschikbaar = $voorraad->get($productId, 0);

                if ($extraNodig > $beschikbaar) {
                    // Server-side stock check met concrete foutmelding.
                    $naam = $productNamen->get($productId, 'Onbekend product');
                    return back()
                        ->withInput()
                        ->with('status_error', 'Onvoldoende voorraad voor ' . $naam . '. Beschikbaar: ' . $beschikbaar . ', extra nodig: ' . $extraNodig . '.');
                }
            }

            DB::transaction(function () use ($id, $aangevraagd, $bestaand): void {
                // Inhoud + voorraad moeten als één geheel kloppen.
                $syncData = [];

                foreach ($aangevraagd as $productId => $nieuwAantal) {
                    $oudAantal = $bestaand->get($productId, 0);
                    $delta = $nieuwAantal - $oudAantal;

                    if ($delta !== 0) {
                        DB::table('voorraad')
                            ->where('product_id', $productId)
                            ->update([
                                'hoeveelheid' => DB::raw('hoeveelheid - (' . $delta . ')'),
                                'updated_at' => now(),
                            ]);
                    }

                    if ($nieuwAantal > 0) {
                        $syncData[$productId] = ['aantal' => $nieuwAantal];
                    }
                }

                DB::table('pakket_product')->where('pakket_id', $id)->delete();

                foreach ($syncData as $productId => $pivotData) {
                    DB::table('pakket_product')->insert([
                        'pakket_id' => $id,
                        'product_id' => $productId,
                        'aantal' => $pivotData['aantal'],
                    ]);
                }
            });
        } catch (Throwable $exception) {
            Log::error('Technische log: voedselpakket samenstellen opslaan mislukt.', [
                'user_id' => $request->user()?->id,
                'pakket_id' => $id,
                'error_class' => $exception::class,
                'error_message' => $exception->getMessage(),
            ]);

            return back()
                ->withInput()
                ->with('status_error', 'Het voedselpakket kon niet worden opgeslagen.');
        }

        return redirect()->route($this->roleRoute('index'))
            ->with('status_success', 'Het voedselpakket is opgeslagen.');
    }

    private function roleRoute(string $action): string
    {
        return auth()->user()->role . '.voedselpakketten.' . $action;
    }

    private function vulInhoudAan(Collection $pakketten): Collection
    {
        if ($pakketten->isEmpty()) {
            return $pakketten;
        }

        $pakketIds = $pakketten->pluck('id')->map(fn ($id) => (int) $id)->all();

        // Bouw een leesbare samenvatting voor de Info-modal in het overzicht.
        $inhoud = DB::table('pakket_product as pp')
            ->join('products as p', 'p.id', '=', 'pp.product_id')
            ->whereIn('pp.pakket_id', $pakketIds)
            ->orderBy('p.productnaam')
            ->select([
                'pp.pakket_id',
                'p.productnaam',
                'pp.aantal',
            ])
            ->get()
            ->groupBy('pakket_id')
            ->map(function ($regels): string {
                return $regels
                    ->map(fn ($regel) => $regel->productnaam . ' (' . (int) $regel->aantal . ')')
                    ->implode(', ');
            });

        return $pakketten->map(function ($pakket) use ($inhoud) {
            $pakket->inhoud_tekst = $inhoud->get($pakket->id, '-');
            return $pakket;
        });
    }
}

