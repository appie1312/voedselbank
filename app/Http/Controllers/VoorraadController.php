<?php

namespace App\Http\Controllers;

use App\Models\VoorraadModel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Exception;

class VoorraadController extends Controller
{
    private VoorraadModel $voorraadModel;
    private const VOORRAAD_LOCATIES = [
        'Magazijn A',
        'Magazijn B',
        'Koeling',
        'Vriezer',
        'Schap 1',
        'Schap 2',
    ];

    public function __construct()
    {
        // Geef de PDO connectie door aan het model
        $this->voorraadModel = new VoorraadModel(DB::connection()->getPdo());
    }

    public function index()
    {
        try {
            $voorraad = $this->voorraadModel->getVoorraadLijst();
            $melding = '';

            if ($voorraad === false) {
                $voorraad = [];
            } elseif (count($voorraad) === 0) {
                $melding = 'Er is momenteel geen voorraad beschikbaar.';
            }

            return view('voorraad.index', compact('voorraad', 'melding'));
        } catch (Exception $e) {
            logger()->error('Fout in VoorraadController: ' . $e->getMessage());

            return view('voorraad.index', [
                'voorraad' => [],
                'melding' => '',
            ]);
        }
    }

    public function create()
    {
        $productenNietInVoorraad = $this->voorraadModel->getProductenNietInVoorraad();
        $locaties = self::VOORRAAD_LOCATIES;

        return view('voorraad.create', compact('productenNietInVoorraad', 'locaties'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'product_naam' => ['required', 'string', 'max:150'],
            'hoeveelheid' => ['required', 'integer', 'min:0'],
            'minimum_voorraad' => ['required', 'integer', 'min:0'],
            'locatie' => ['nullable', 'string', 'max:100', Rule::in(self::VOORRAAD_LOCATIES)],
        ]);

        $productNaam = trim($data['product_naam']);
        $product = $this->voorraadModel->findProductByNaam($productNaam);

        if (! $product) {
            $product = $this->voorraadModel->maakProductAan($productNaam);
        }

        if (! $product) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors([
                    'product_naam' => 'Product kon niet worden aangemaakt.',
                ]);
        }

        if ($this->voorraadModel->staatProductAlInVoorraad((int) $product->id)) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors([
                    'product_naam' => 'Dit product staat al in de voorraad.',
                ]);
        }

        $toegevoegd = $this->voorraadModel->addProductAanVoorraad(
            (int) $product->id,
            (int) $data['hoeveelheid'],
            (int) $data['minimum_voorraad'],
            $data['locatie'] ?? null
        );

        if (! $toegevoegd) {
            return redirect()
                ->route('voorraad')
                ->with('error', 'Product kon niet worden toegevoegd aan de voorraad.');
        }

        return redirect()
            ->route('voorraad')
            ->with('success', 'Product is toegevoegd aan de voorraad.');
    }

    public function edit(int $productId)
    {
        $voorraadItem = $this->voorraadModel->getVoorraadRegelByProductId($productId);
        $locaties = self::VOORRAAD_LOCATIES;

        if (! $voorraadItem) {
            return redirect()
                ->route('voorraad')
                ->with('error', 'Voorraadregel is niet gevonden.');
        }

        return view('voorraad.edit', compact('voorraadItem', 'locaties'));
    }

    public function update(Request $request, int $productId): RedirectResponse
    {
        $data = $request->validate([
            'hoeveelheid' => ['required', 'integer', 'min:0'],
            'minimum_voorraad' => ['required', 'integer', 'min:0'],
            'locatie' => ['nullable', 'string', 'max:100', Rule::in(self::VOORRAAD_LOCATIES)],
        ]);

        $gewijzigd = $this->voorraadModel->updateVoorraadRegel(
            $productId,
            (int) $data['hoeveelheid'],
            (int) $data['minimum_voorraad'],
            $data['locatie'] ?? null
        );

        if (! $gewijzigd) {
            return redirect()
                ->route('voorraad')
                ->with('error', 'Voorraadregel kon niet worden gewijzigd.');
        }

        return redirect()
            ->route('voorraad')
            ->with('success', 'Voorraadregel is gewijzigd.');
    }

    public function destroy(int $productId): RedirectResponse
    {
        $verwijderd = $this->voorraadModel->deleteVoorraadRegel($productId);

        if (! $verwijderd) {
            return redirect()
                ->route('voorraad')
                ->with('error', 'Voorraadregel kon niet worden verwijderd.');
        }

        return redirect()
            ->route('voorraad')
            ->with('success', 'Voorraadregel is verwijderd.');
    }
}
