<?php

namespace App\Http\Controllers;

use App\Models\VoorraadModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Exception;

class VoorraadController extends Controller
{
    private $voorraadModel;

    /**
     * In Laravel gebruiken we Dependency Injection of Facades
     * in plaats van handmatige DB connecties in de constructor.
     */
    public function __construct()
    {
        // We halen de PDO connectie uit de Laravel DB facade
        $this->voorraadModel = new VoorraadModel(DB::connection()->getPdo());
    }

    /**
     * Rendert de voorraad pagina
     */
    public function index()
    {
        try {
            $voorraad = $this->voorraadModel->getVoorraadLijst();
            $melding = "";

            if ($voorraad === false) {
                $melding = "Er is een fout opgetreden bij het laden van de voorraad.";
                $voorraad = [];
            } elseif (count($voorraad) === 0) {
                $melding = "Er is momenteel geen voorraad beschikbaar.";
            }

            // In Laravel gebruiken we view() helper, verwijder de 'require_once'
            return view('voorraad.index', compact('voorraad', 'melding'));

        } catch (Exception $e) {
            // Technische log via Laravel Log
            logger()->error("Fout in VoorraadController: " . $e->getMessage());
            return back()->with('error', 'Er is iets misgegaan.');
        }
    }
}
