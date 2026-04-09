<?php

namespace App\Controllers;

use App\Models\VoorraadModel;

class VoorraadController
{
    private $voorraadModel;

    public function __construct($db)
    {
        $this->voorraadModel = new VoorraadModel($db);
    }

    /**
     * Rendert de voorraad pagina
     */
    public function index()
    {
        $voorraad = $this->voorraadModel->getVoorraadLijst();
        $melding = "";

        // Validatie van data-ontvangst en bepalen van feedback meldingen
        if ($voorraad === false) {
            $melding = "Er is een technische fout opgetreden bij het laden van de voorraad.";
            $voorraad = [];
        } elseif (empty($voorraad)) {
            $melding = "Er is momenteel geen voorraad beschikbaar.";
        }

        // View laden (data wordt meegegeven)
        require_once '../app/views/voorraad/index.php';
    }
}
