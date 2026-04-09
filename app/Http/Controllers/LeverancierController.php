<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Leverancier;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Carbon\Carbon;

class LeverancierController extends Controller
{
    public function index(): View
    {
        $leveranciers = Leverancier::getAllMetProducten();

        return view('leveranciers.index', compact('leveranciers'));
    }

    public function create(): View
    {
        $leverancier = null;
        $producten = DB::table('products')
            ->select('id', 'productnaam')
            ->orderBy('productnaam')
            ->get();

        $geselecteerdeProductIds = [];

        return view('leveranciers.create', compact('producten', 'leverancier', 'geselecteerdeProductIds'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'bedrijfsnaam' => ['required', 'string', 'max:150', Rule::unique('leveranciers', 'bedrijfsnaam')],
            'adres' => ['required', 'string', 'max:255'],
            'contactpersoon_naam' => ['required', 'string', 'max:100'],
            'contactpersoon_email' => ['nullable', 'email', 'max:150'],
            'telefoonnummer' => ['required', 'string', 'max:20'],
            'volgende_levering' => ['nullable', 'date'],
            'product_ids' => ['nullable', 'array'],
            'product_ids.*' => ['integer', 'exists:products,id'],
        ], [
            'bedrijfsnaam.unique' => 'deze bedrijfsnaam bestaat al',
        ]);

        $leverancier = Leverancier::create([
            'bedrijfsnaam' => $data['bedrijfsnaam'],
            'adres' => $data['adres'],
            'contactpersoon_naam' => $data['contactpersoon_naam'],
            'contactpersoon_email' => $data['contactpersoon_email'] ?? null,
            'telefoonnummer' => $data['telefoonnummer'],
            'volgende_levering' => $data['volgende_levering'] ?? null,
        ]);

        $productIds = $data['product_ids'] ?? [];

        if (Schema::hasTable('leverancier_products') && ! empty($productIds)) {
            $now = now();

            foreach ($productIds as $productId) {
                DB::table('leverancier_products')->updateOrInsert(
                    [
                        'leverancier_id' => $leverancier->id,
                        'product_id' => $productId,
                    ],
                    [
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]
                );
            }
        }

        return redirect()->route('leveranciers.index')->with('success', 'Leverancier toegevoegd!');
    }

    public function edit(int $leverancierId): View
    {
        $leverancier = Leverancier::findOrFail($leverancierId);

        $producten = DB::table('products')
            ->select('id', 'productnaam')
            ->orderBy('productnaam')
            ->get();

        $geselecteerdeProductIds = [];

        if (Schema::hasTable('leverancier_products')) {
            $geselecteerdeProductIds = DB::table('leverancier_products')
                ->where('leverancier_id', $leverancier->id)
                ->pluck('product_id')
                ->map(static fn ($id): int => (int) $id)
                ->all();
        }

        return view('leveranciers.create', compact('leverancier', 'producten', 'geselecteerdeProductIds'));
    }

    public function update(Request $request, int $leverancierId): RedirectResponse
    {
        $leverancier = Leverancier::find($leverancierId);

        if (! $leverancier) {
            return redirect()
                ->route('leveranciers.index')
                ->with('error', 'leverancier is al verwijder en kan daarom niet gewijzigd worden');
        }

        $data = $request->validate([
            'bedrijfsnaam' => ['required', 'string', 'max:150', Rule::unique('leveranciers', 'bedrijfsnaam')->ignore($leverancier->id)],
            'adres' => ['required', 'string', 'max:255'],
            'contactpersoon_naam' => ['required', 'string', 'max:100'],
            'contactpersoon_email' => ['nullable', 'email', 'max:150'],
            'telefoonnummer' => ['required', 'string', 'max:20'],
            'volgende_levering' => ['nullable', 'date'],
            'product_ids' => ['nullable', 'array'],
            'product_ids.*' => ['integer', 'exists:products,id'],
        ]);

        $veldWijzigingen = [
            'bedrijfsnaam' => $data['bedrijfsnaam'],
            'adres' => $data['adres'],
            'contactpersoon_naam' => $data['contactpersoon_naam'],
            'contactpersoon_email' => $data['contactpersoon_email'] ?? null,
            'telefoonnummer' => $data['telefoonnummer'],
            'volgende_levering' => $data['volgende_levering'] ?? null,
        ];

        $isGewijzigd = false;

        foreach ($veldWijzigingen as $veld => $waarde) {
            $huidigeWaarde = $leverancier->{$veld};

            if ($veld === 'volgende_levering') {
                $huidigeWaarde = $huidigeWaarde ? Carbon::parse((string) $huidigeWaarde)->format('Y-m-d') : null;
                $waarde = $waarde ? Carbon::parse((string) $waarde)->format('Y-m-d') : null;
            }

            if ((string) ($huidigeWaarde ?? '') !== (string) ($waarde ?? '')) {
                $isGewijzigd = true;
                break;
            }
        }

        $nieuweProductIds = collect($data['product_ids'] ?? [])
            ->map(static fn ($id): int => (int) $id)
            ->unique()
            ->sort()
            ->values()
            ->all();

        $huidigeProductIds = [];
        if (Schema::hasTable('leverancier_products')) {
            $huidigeProductIds = DB::table('leverancier_products')
                ->where('leverancier_id', $leverancier->id)
                ->pluck('product_id')
                ->map(static fn ($id): int => (int) $id)
                ->sort()
                ->values()
                ->all();
        }

        if ($huidigeProductIds !== $nieuweProductIds) {
            $isGewijzigd = true;
        }

        if (! $isGewijzigd) {
            return redirect()
                ->route('leveranciers.index')
                ->with('error', 'leverancier is niet gewijzigd');
        }

        $leverancier->update($veldWijzigingen);

        if (Schema::hasTable('leverancier_products')) {
            DB::table('leverancier_products')->where('leverancier_id', $leverancier->id)->delete();

            $now = now();
            foreach ($nieuweProductIds as $productId) {
                DB::table('leverancier_products')->insert([
                    'leverancier_id' => $leverancier->id,
                    'product_id' => $productId,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }

        return redirect()
            ->route('leveranciers.index')
            ->with('success', 'leverancier is gewijzigd');
    }

    public function destroy(int $leverancierId): RedirectResponse
    {
        $leverancier = Leverancier::find($leverancierId);

        if (! $leverancier) {
            return redirect()
                ->route('leveranciers.index')
                ->with('error', 'leverancier is al verwijder en kan daarom niet verwijderd worden');
        }

        $leverancier->delete();

        return redirect()
            ->route('leveranciers.index')
            ->with('success', 'leverancier is verwijderd');
    }
}
