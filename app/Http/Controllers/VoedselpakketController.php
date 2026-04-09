<?php

namespace App\Http\Controllers;

use App\Models\Voedselpakket;
use Illuminate\Http\Request;

class VoedselpakketController extends Controller
{
    public function index()
    {
        // Haal alle pakketten op, inclusief de gegevens van het gezin (klant), 
        // gesorteerd op de nieuwste datum eerst.
        $pakketten = Voedselpakket::with('klant')
                        ->orderBy('datum_samenstelling', 'desc')
                        ->get();

        return view('voedselpakketten.index', compact('pakketten'));
    }

// --- CREATE: Toon het formulier om een nieuw pakket aan te maken ---
    public function create()
    {
        // Haal alle klanten op zodat we ze in een dropdown (select) kunnen tonen
        $klanten = \App\Models\Klant::orderBy('gezinsnaam')->get();
        return view('voedselpakketten.create', compact('klanten'));
    }

    // --- STORE: Sla het nieuwe pakket op in de database ---
    public function store(Request $request)
    {
        // 1. Validatie (Beveiliging)
        $request->validate([
            'klant_id' => 'required|exists:klanten,id',
            'opmerking' => 'nullable|string|max:250',
        ]);

        // 2. Opslaan
        $pakket = new Voedselpakket();
        $pakket->klant_id = $request->klant_id;
        $pakket->datum_samenstelling = now(); // Vandaag klaargemaakt
        $pakket->is_actief = 1;
        $pakket->opmerking = $request->opmerking;
        $pakket->save();

        // 3. Doorsturen: Na het aanmaken wil je waarschijnlijk meteen producten toevoegen
        return redirect()->route('voedselpakketten.samenstellen', $pakket->id)
                         ->with('success', 'Nieuw pakket geregistreerd. Je kunt nu producten toevoegen.');
    }

    // --- EDIT: Toon het formulier om een bestaand pakket te wijzigen ---
    public function edit($id)
    {
        $pakket = Voedselpakket::findOrFail($id);
        $klanten = \App\Models\Klant::orderBy('gezinsnaam')->get();
        
        return view('voedselpakketten.edit', compact('pakket', 'klanten'));
    }

    // --- UPDATE: Sla de wijzigingen op in de database ---
    public function update(Request $request, $id)
    {
        $pakket = Voedselpakket::findOrFail($id);

        $request->validate([
            'klant_id' => 'required|exists:klanten,id',
            'opmerking' => 'nullable|string|max:250',
            // Als het vinkje "Opgehaald" is aangevinkt, sturen we een datum mee
            'opgehaald' => 'nullable|boolean' 
        ]);

        $pakket->klant_id = $request->klant_id;
        $pakket->opmerking = $request->opmerking;

        // Logica voor Peter Abraham: Is het pakket zojuist opgehaald?
        if ($request->has('opgehaald') && is_null($pakket->datum_uitgifte)) {
            $pakket->datum_uitgifte = now();
        } elseif (!$request->has('opgehaald')) {
            $pakket->datum_uitgifte = null; // Zet terug als ze een foutje maakten
        }

        $pakket->save();

        return redirect()->route('voedselpakketten.index')
                         ->with('success', 'Pakket succesvol gewijzigd!');
    }

    // --- DELETE: Verwijder het pakket uit de database ---
    public function destroy($id)
    {
        $pakket = Voedselpakket::findOrFail($id);

        // Good practice: Verwijder eerst de koppelingen in de tussentabel 
        // Dit voorkomt "wees-data" in je database.
        $pakket->producten()->detach(); 

        // Verwijder daarna pas het pakket zelf
        $pakket->delete();

        return redirect()->route('voedselpakketten.index')
                         ->with('success', 'Het voedselpakket is verwijderd.');
    }

    public function mijnPakket()
    {
        // We zoeken het pakket waar de klant_id overeenkomt met de ID van de ingelogde gebruiker
        $pakket = \App\Models\Voedselpakket::where('klant_id', auth()->user()->id)
                    ->orderBy('datum_samenstelling', 'desc')
                    ->first();

        return view('voedselpakketten.mijn-pakket', compact('pakket'));
    }
}