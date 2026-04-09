<?php

namespace App\Http\Controllers;

use App\Models\Voorraad;

/**
 * Controller voor het tonen van het voorraadoverzicht.
 */
class VoorraadController extends Controller
{
    /**
     * Toon een overzicht van alle voorraadregels.
     *
     * Acceptatiecriteria:
     * - Als er voorraad is: toon lijst met producten en hun voorraadstatus
     * - Als er geen voorraad is: toon melding
     */
    public function index()
    {
        // Haal voorraad op inclusief product en categorie
        $voorraadItems = Voorraad::with(['product.categorie'])
            ->orderBy('id', 'asc')
            ->get();

        // Geef de data door aan de view
        return view('voorraad.index', compact('voorraadItems'));
    }
}
