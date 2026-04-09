<?php

namespace App\Http\Controllers;

use App\Models\VoorraadModel;
use Illuminate\Support\Facades\DB;
use Exception;

class VoorraadController extends Controller
{
    private VoorraadModel $voorraadModel;

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
                $melding = 'Er is een fout opgetreden bij het laden van de voorraad.';
                $voorraad = [];
            } elseif (count($voorraad) === 0) {
                $melding = 'Er is momenteel geen voorraad beschikbaar.';
            }

            return view('voorraad.index', compact('voorraad', 'melding'));
        } catch (Exception $e) {
            logger()->error('Fout in VoorraadController: ' . $e->getMessage());

            return view('voorraad.index', [
                'voorraad' => [],
                'melding' => 'Er is een fout opgetreden bij het laden van de voorraad.',
            ]);
        }
    }
}
