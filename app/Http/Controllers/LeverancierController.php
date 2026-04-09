<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreLeverancierRequest;
use App\Models\Leverancier;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class LeverancierController extends Controller
{
    public function index(): View
    {
        $leveranciers = Leverancier::getAllMetProducten();

        return view('leveranciers.index', compact('leveranciers'));
    }

    public function store(StoreLeverancierRequest $request): RedirectResponse
    {
        $data = $request->validated();

        // Voorbeeld: aanmaken via gewone Eloquent (SP kan ook)
        $leverancier = Leverancier::create([
            'naam'      => $data['naam'],
            'adres'     => $data['adres'] ?? null,
            'telefoon'  => $data['telefoon'] ?? null,
            'email'     => $data['email'] ?? null,
            'is_actief' => true,
        ]);

        return redirect()->route('leveranciers.index')->with('success', 'Leverancier toegevoegd!');
    }
}