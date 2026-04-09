<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Exception;

class VoorraadController extends Controller
{
    public function index()
    {
        try {
            $voorraad = DB::table('voorraden as v')
                ->join('products as p', 'v.product_id', '=', 'p.id')
                ->join('categories as c', 'p.categorie_id', '=', 'c.id')
                ->select(
                    'p.naam as product_naam',
                    'c.naam as categorie_naam',
                    'v.hoeveelheid',
                    'v.locatie',
                    DB::raw("
                        CASE
                            WHEN v.hoeveelheid <= 0 THEN 'Leeg'
                            WHEN v.minimum_voorraad IS NOT NULL AND v.hoeveelheid <= v.minimum_voorraad THEN 'Aanvullen'
                            ELSE 'Voldoende'
                        END as status
                    ")
                )
                ->orderBy('p.naam')
                ->get();

            $melding = '';

            if ($voorraad->isEmpty()) {
                $melding = 'Er is momenteel geen voorraad beschikbaar.';
            }

            return view('voorraad.index', compact('voorraad', 'melding'));
        } catch (Exception $e) {
            logger()->error('Fout in VoorraadController: ' . $e->getMessage());

            return view('voorraad.index', [
                'voorraad' => collect(),
                'melding' => 'Er is een fout opgetreden bij het laden van de voorraad.',
            ]);
        }
    }
}
