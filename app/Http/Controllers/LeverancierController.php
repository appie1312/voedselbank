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

class LeverancierController extends Controller
{
    public function index(): View
    {
        $leveranciers = Leverancier::getAllMetProducten();

        return view('leveranciers.index', compact('leveranciers'));
    }

    public function create(): View
    {
        $producten = DB::table('products')
            ->select('id', 'productnaam')
            ->orderBy('productnaam')
            ->get();

        return view('leveranciers.create', compact('producten'));
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
}
