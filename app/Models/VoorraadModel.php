<?php

namespace App\Models;

use PDO;
use Exception;

class VoorraadModel
{
    private $db;

    public function __construct($dbConnection)
    {
        $this->db = $dbConnection;
    }

    /**
     * Haalt het volledige voorraadoverzicht op zonder stored procedure
     */
    public function getVoorraadLijst()
    {
        try {
            $query = "
                SELECT
                    p.naam AS product_naam,
                    c.naam AS categorie_naam,
                    v.hoeveelheid,
                    v.locatie,
                    CASE
                        WHEN v.hoeveelheid <= 0 THEN 'Leeg'
                        WHEN v.minimum_voorraad IS NOT NULL AND v.hoeveelheid <= v.minimum_voorraad THEN 'Aanvullen'
                        ELSE 'Voldoende'
                    END AS status
                FROM voorraden v
                JOIN products p ON v.product_id = p.id
                JOIN categories c ON p.categorie_id = c.id
            ";

            $stmt = $this->db->prepare($query);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_OBJ);
        } catch (Exception $e) {
            error_log("Fout in VoorraadModel: " . $e->getMessage());
            return false;
        }
    }
}
