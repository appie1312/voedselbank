<?php

namespace App\Models;

use PDO;
use Exception;

class VoorraadModel
{
    private PDO $db;

    public function __construct(PDO $dbConnection)
    {
        $this->db = $dbConnection;
    }

    /**
     * Haalt het volledige voorraadoverzicht op via een stored procedure.
     *
     * @return array|false
     */
    public function getVoorraadLijst()
    {
        try {
            $query = "CALL spGetVoorraadOverzicht()";
            $stmt = $this->db->prepare($query);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_OBJ);
        } catch (Exception $e) {
            error_log("Fout in VoorraadModel::getVoorraadLijst: " . $e->getMessage());
            return false;
        }
    }
}
