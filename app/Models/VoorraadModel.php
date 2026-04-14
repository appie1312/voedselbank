<?php

namespace App\Models;

use PDO;
use Exception;

class VoorraadModel
{
    // Database connectie opslaan in een private property
    private PDO $db;

    // Constructor: krijgt PDO connectie mee vanuit controller
    public function __construct(PDO $dbConnection)
    {
        $this->db = $dbConnection;
    }

    /**
     * Haalt het volledige voorraadoverzicht op.
     *
     * @return array|false
     */
    public function getVoorraadLijst()
    {
        try {
            // Query om alle voorraadregels op te halen
            // inclusief productnaam, categorie, hoeveelheid, minimum voorraad, locatie en status
            $query = "
                SELECT
                    p.id AS product_id,
                    p.productnaam AS product_naam,
                    c.naam AS categorie_naam,
                    v.hoeveelheid,
                    v.minimum_voorraad,
                    v.locatie,
                    CASE
                        WHEN v.hoeveelheid <= 0 THEN 'Leeg'
                        WHEN v.minimum_voorraad IS NOT NULL AND v.hoeveelheid <= v.minimum_voorraad THEN 'Aanvullen'
                        ELSE 'Voldoende'
                    END AS status
                FROM voorraad v
                INNER JOIN products p ON v.product_id = p.id
                INNER JOIN categories c ON p.categorie_id = c.id
                ORDER BY p.productnaam ASC
            ";

            // Query voorbereiden en uitvoeren
            $stmt = $this->db->prepare($query);
            $stmt->execute();

            // Alle resultaten ophalen als objecten
            return $stmt->fetchAll(PDO::FETCH_OBJ);
        } catch (Exception $e) {
            // Fout loggen en false teruggeven
            error_log("Fout in VoorraadModel::getVoorraadLijst: " . $e->getMessage());
            return false;
        }
    }

    // Haalt alle producten op die nog niet in de voorraad staan
    public function getProductenNietInVoorraad(): array
    {
        try {
            $query = "
                SELECT p.id, p.productnaam
                FROM products p
                LEFT JOIN voorraad v ON v.product_id = p.id
                WHERE v.product_id IS NULL
                ORDER BY p.productnaam ASC
            ";

            $stmt = $this->db->prepare($query);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_OBJ);
        } catch (Exception $e) {
            // Bij fout lege array teruggeven
            error_log("Fout in VoorraadModel::getProductenNietInVoorraad: " . $e->getMessage());
            return [];
        }
    }

    // Voegt een product toe aan de voorraad
    public function addProductAanVoorraad(int $productId, int $hoeveelheid, int $minimumVoorraad, ?string $locatie): bool
    {
        try {
            $query = "
                INSERT INTO voorraad (product_id, hoeveelheid, minimum_voorraad, locatie, created_at, updated_at)
                VALUES (:product_id, :hoeveelheid, :minimum_voorraad, :locatie, NOW(), NOW())
            ";

            $stmt = $this->db->prepare($query);

            // Execute geeft true of false terug
            return $stmt->execute([
                ':product_id' => $productId,
                ':hoeveelheid' => $hoeveelheid,
                ':minimum_voorraad' => $minimumVoorraad,
                ':locatie' => $locatie,
            ]);
        } catch (Exception $e) {
            error_log("Fout in VoorraadModel::addProductAanVoorraad: " . $e->getMessage());
            return false;
        }
    }

    // Zoekt een product op naam dat nog niet in voorraad zit
    public function findBeschikbaarProductByNaam(string $productNaam): object|null
    {
        try {
            $query = "
                SELECT p.id, p.productnaam
                FROM products p
                LEFT JOIN voorraad v ON v.product_id = p.id
                WHERE v.product_id IS NULL
                  AND LOWER(p.productnaam) = LOWER(:productnaam)
                LIMIT 1
            ";

            $stmt = $this->db->prepare($query);
            $stmt->execute([
                ':productnaam' => trim($productNaam),
            ]);

            $result = $stmt->fetch(PDO::FETCH_OBJ);

            // Als geen resultaat, dan null teruggeven
            return $result ?: null;
        } catch (Exception $e) {
            error_log("Fout in VoorraadModel::findBeschikbaarProductByNaam: " . $e->getMessage());
            return null;
        }
    }

    // Zoekt een product op naam, ongeacht of het al in voorraad zit
    public function findProductByNaam(string $productNaam): object|null
    {
        try {
            $query = "
                SELECT id, productnaam
                FROM products
                WHERE LOWER(productnaam) = LOWER(:productnaam)
                LIMIT 1
            ";

            $stmt = $this->db->prepare($query);
            $stmt->execute([
                ':productnaam' => trim($productNaam),
            ]);

            $result = $stmt->fetch(PDO::FETCH_OBJ);

            return $result ?: null;
        } catch (Exception $e) {
            error_log("Fout in VoorraadModel::findProductByNaam: " . $e->getMessage());
            return null;
        }
    }

    // Controleert of een product al in de voorraad staat
    public function staatProductAlInVoorraad(int $productId): bool
    {
        try {
            $query = "SELECT COUNT(*) FROM voorraad WHERE product_id = :product_id";
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                ':product_id' => $productId,
            ]);

            // Als count groter is dan 0, bestaat het product al in voorraad
            return (int) $stmt->fetchColumn() > 0;
        } catch (Exception $e) {
            error_log("Fout in VoorraadModel::staatProductAlInVoorraad: " . $e->getMessage());
            return false;
        }
    }

    // Maakt een nieuw product aan in de products tabel
    public function maakProductAan(string $productNaam): object|null
    {
        try {
            // Haal standaard categorie op
            $categorieId = $this->getStandaardCategorieId();

            if ($categorieId === null) {
                return null;
            }

            $query = "
                INSERT INTO products (productnaam, ean_nummer, aantal_in_voorraad, categorie_id, created_at, updated_at)
                VALUES (:productnaam, :ean_nummer, 0, :categorie_id, NOW(), NOW())
            ";

            $stmt = $this->db->prepare($query);
            $stmt->execute([
                ':productnaam' => trim($productNaam),
                ':ean_nummer' => $this->genereerUniekEanNummer(),
                ':categorie_id' => $categorieId,
            ]);

            // ID van nieuw product ophalen
            $productId = (int) $this->db->lastInsertId();

            // Zelf een object teruggeven
            return (object) [
                'id' => $productId,
                'productnaam' => trim($productNaam),
            ];
        } catch (Exception $e) {
            error_log("Fout in VoorraadModel::maakProductAan: " . $e->getMessage());
            return null;
        }
    }

    // Werkt een bestaande voorraadregel bij
    public function updateVoorraadRegel(int $productId, int $hoeveelheid, int $minimumVoorraad, ?string $locatie): bool
    {
        try {
            $query = "
                UPDATE voorraad
                SET hoeveelheid = :hoeveelheid,
                    minimum_voorraad = :minimum_voorraad,
                    locatie = :locatie,
                    updated_at = NOW()
                WHERE product_id = :product_id
            ";

            $stmt = $this->db->prepare($query);

            return $stmt->execute([
                ':product_id' => $productId,
                ':hoeveelheid' => $hoeveelheid,
                ':minimum_voorraad' => $minimumVoorraad,
                ':locatie' => $locatie,
            ]);
        } catch (Exception $e) {
            error_log("Fout in VoorraadModel::updateVoorraadRegel: " . $e->getMessage());
            return false;
        }
    }

    // Haalt één voorraadregel op via product ID
    public function getVoorraadRegelByProductId(int $productId): object|null
    {
        try {
            $query = "
                SELECT
                    p.id AS product_id,
                    p.productnaam AS product_naam,
                    c.naam AS categorie_naam,
                    v.hoeveelheid,
                    v.minimum_voorraad,
                    v.locatie
                FROM voorraad v
                INNER JOIN products p ON v.product_id = p.id
                INNER JOIN categories c ON p.categorie_id = c.id
                WHERE p.id = :product_id
                LIMIT 1
            ";

            $stmt = $this->db->prepare($query);
            $stmt->execute([
                ':product_id' => $productId,
            ]);

            $result = $stmt->fetch(PDO::FETCH_OBJ);

            return $result ?: null;
        } catch (Exception $e) {
            error_log("Fout in VoorraadModel::getVoorraadRegelByProductId: " . $e->getMessage());
            return null;
        }
    }

    // Verwijdert een voorraadregel op basis van product ID
    public function deleteVoorraadRegel(int $productId): bool
    {
        try {
            $query = "DELETE FROM voorraad WHERE product_id = :product_id";
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                ':product_id' => $productId,
            ]);

            // rowCount controleert of er echt iets verwijderd is
            return $stmt->rowCount() > 0;
        } catch (Exception $e) {
            error_log("Fout in VoorraadModel::deleteVoorraadRegel: " . $e->getMessage());
            return false;
        }
    }

    // Haalt standaard categorie ID op, of maakt categorie "Overig" aan als die niet bestaat
    private function getStandaardCategorieId(): int|null
    {
        try {
            $query = "SELECT id FROM categories ORDER BY id ASC LIMIT 1";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $categoryId = $stmt->fetchColumn();

            // Als er al een categorie bestaat, gebruik die
            if ($categoryId !== false) {
                return (int) $categoryId;
            }

            // Anders maak categorie "Overig" aan
            $insert = "INSERT INTO categories (naam, created_at, updated_at) VALUES ('Overig', NOW(), NOW())";
            $this->db->exec($insert);

            return (int) $this->db->lastInsertId();
        } catch (Exception $e) {
            error_log("Fout in VoorraadModel::getStandaardCategorieId: " . $e->getMessage());
            return null;
        }
    }

    // Genereert een uniek 13-cijferig EAN nummer
    private function genereerUniekEanNummer(): string
    {
        // Maximaal 25 pogingen om een uniek nummer te maken
        for ($i = 0; $i < 25; $i++) {
            $ean = str_pad((string) random_int(0, 9999999999999), 13, '0', STR_PAD_LEFT);

            $query = "SELECT COUNT(*) FROM products WHERE ean_nummer = :ean_nummer";
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                ':ean_nummer' => $ean,
            ]);

            // Als het nummer nog niet bestaat, gebruik deze
            if ((int) $stmt->fetchColumn() === 0) {
                return $ean;
            }
        }

        // Fallback als het na 25 keer niet lukt
        return str_pad((string) time(), 13, '0', STR_PAD_LEFT);
    }
}
