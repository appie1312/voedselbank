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
    // Model voor database interactie
    private VoorraadModel $voorraadModel;

    // Vaste lijst met toegestane locaties
    private const VOORRAAD_LOCATIES = [
        'Magazijn A',
        'Magazijn B',
        'Koeling',
        'Vriezer',
        'Schap 1',
        'Schap 2',
    ];

    // Constructor: maakt verbinding met database en initialiseert model
    public function __construct()
    {
        $this->voorraadModel = new VoorraadModel(DB::connection()->getPdo());
    }

    // Toon overzicht van alle voorraad
    public function index()
    {
        try {
            // Haal voorraad op
            $voorraad = $this->voorraadModel->getVoorraadLijst();
            $melding = '';

            // Als ophalen mislukt → lege array
            if ($voorraad === false) {
                $voorraad = [];
            }
            // Als er geen voorraad is → melding tonen
            elseif (count($voorraad) === 0) {
                $melding = 'Er is momenteel geen voorraad beschikbaar.';
            }

            // Geef data door aan view
            return view('voorraad.index', compact('voorraad', 'melding'));
        } catch (Exception $e) {
            // Log fout
            logger()->error('Fout in VoorraadController: ' . $e->getMessage());

            // Fallback view
            return view('voorraad.index', [
                'voorraad' => [],
                'melding' => '',
            ]);
        }
    }

    // Formulier tonen om nieuw product toe te voegen
    public function create()
    {
        // Haal producten op die nog niet in voorraad zitten
        $productenNietInVoorraad = $this->voorraadModel->getProductenNietInVoorraad();

        // Locaties ophalen
        $locaties = self::VOORRAAD_LOCATIES;

        return view('voorraad.create', compact('productenNietInVoorraad', 'locaties'));
    }

    // Opslaan van nieuw voorraad item
    public function store(Request $request): RedirectResponse
    {
        // Validatie van input
        $data = $request->validate([
            'product_naam' => ['required', 'string', 'max:150'],
            'hoeveelheid' => ['required', 'integer', 'min:0'],
            'minimum_voorraad' => ['required', 'integer', 'min:0'],
            'locatie' => ['nullable', 'string', 'max:100', Rule::in(self::VOORRAAD_LOCATIES)],
        ]);

        // Spaties verwijderen uit naam
        $productNaam = trim($data['product_naam']);

        // Check of product al bestaat
        $product = $this->voorraadModel->findProductByNaam($productNaam);

        // Zo niet → nieuw product aanmaken
        if (! $product) {
            $product = $this->voorraadModel->maakProductAan($productNaam);
        }

        // Als aanmaken mislukt → foutmelding
        if (! $product) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors([
                    'product_naam' => 'Product kon niet worden aangemaakt.',
                ]);
        }

        // Check of product al in voorraad zit
        if ($this->voorraadModel->staatProductAlInVoorraad((int) $product->id)) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors([
                    'product_naam' => 'Dit product staat al in de voorraad.',
                ]);
        }

        // Product toevoegen aan voorraad
        $toegevoegd = $this->voorraadModel->addProductAanVoorraad(
            (int) $product->id,
            (int) $data['hoeveelheid'],
            (int) $data['minimum_voorraad'],
            $data['locatie'] ?? null
        );

        // Als toevoegen mislukt
        if (! $toegevoegd) {
            return redirect()
                ->route('voorraad')
                ->with('error', 'Product kon niet worden toegevoegd aan de voorraad.');
        }

        // Succesmelding
        return redirect()
            ->route('voorraad')
            ->with('success', 'Product is toegevoegd aan de voorraad.');
    }

    // Bewerken van voorraad item
    public function edit(int $productId)
    {
        // Haal specifieke voorraadregel op
        $voorraadItem = $this->voorraadModel->getVoorraadRegelByProductId($productId);

        $locaties = self::VOORRAAD_LOCATIES;

        // Als niet gevonden → foutmelding
        if (! $voorraadItem) {
            return redirect()
                ->route('voorraad')
                ->with('error', 'Voorraadregel is niet gevonden.');
        }

        return view('voorraad.edit', compact('voorraadItem', 'locaties'));
    }

    // Updaten van voorraad item
    public function update(Request $request, int $productId): RedirectResponse
    {
        // Validatie
        $data = $request->validate([
            'hoeveelheid' => ['required', 'integer', 'min:0'],
            'minimum_voorraad' => ['required', 'integer', 'min:0'],
            'locatie' => ['nullable', 'string', 'max:100', Rule::in(self::VOORRAAD_LOCATIES)],
        ]);

        // Haal huidige data op
        $voorraadItem = $this->voorraadModel->getVoorraadRegelByProductId($productId);

        if (! $voorraadItem) {
            return redirect()
                ->route('voorraad')
                ->with('error', 'Voorraadregel is niet gevonden.');
        }

        // Oude en nieuwe locatie vergelijken
        $nieuweLocatie = $data['locatie'] ?? null;
        $huidigeLocatie = $voorraadItem->locatie ?? null;

        // Check of er iets is gewijzigd
        if (
            (int) $voorraadItem->hoeveelheid === (int) $data['hoeveelheid']
            && (int) $voorraadItem->minimum_voorraad === (int) $data['minimum_voorraad']
            && $huidigeLocatie === $nieuweLocatie
        ) {
            return redirect()
                ->route('voorraad')
                ->with('error', 'Je hebt ' . $voorraadItem->product_naam . ' niet gewijzigd.');
        }

        // Update uitvoeren
        $gewijzigd = $this->voorraadModel->updateVoorraadRegel(
            $productId,
            (int) $data['hoeveelheid'],
            (int) $data['minimum_voorraad'],
            $data['locatie'] ?? null
        );

        // Als update mislukt
        if (! $gewijzigd) {
            return redirect()
                ->route('voorraad')
                ->with('error', 'Voorraadregel kon niet worden gewijzigd.');
        }

        // Succesmelding
        return redirect()
            ->route('voorraad')
            ->with('success', 'Voorraadregel is gewijzigd.');
    }

    // Verwijderen van voorraad item
    public function destroy(int $productId): RedirectResponse
    {
        // Verwijder voorraadregel
        $verwijderd = $this->voorraadModel->deleteVoorraadRegel($productId);

        // Als verwijderen mislukt
        if (! $verwijderd) {
            return redirect()
                ->route('voorraad')
                ->with('error', 'Voorraadregel kon niet worden verwijderd.');
        }

        // Succesmelding
        return redirect()
            ->route('voorraad')
            ->with('success', 'Voorraadregel is verwijderd.');
    }
}
