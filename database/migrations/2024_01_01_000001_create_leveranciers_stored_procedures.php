<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Maakt stored procedures aan voor leverancier CRUD-operaties.
     */
    public function up(): void
    {
        // -------------------------------------------------------
        // SP: Alle actieve leveranciers ophalen met hun producten
        // -------------------------------------------------------
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_get_all_leveranciers');
        DB::unprepared('
            CREATE PROCEDURE sp_get_all_leveranciers()
            BEGIN
                SELECT
                    l.id,
                    l.naam,
                    l.adres,
                    l.telefoon,
                    l.email,
                    l.is_actief,
                    l.datum_aangemaakt,
                    l.datum_gewijzigd,
                    GROUP_CONCAT(
                        DISTINCT p.naam
                        ORDER BY p.naam ASC
                        SEPARATOR \', \'
                    ) AS producten,
                    COUNT(DISTINCT lp.product_id) AS aantal_producten
                FROM leveranciers l
                LEFT JOIN leverancier_products lp
                    ON lp.leverancier_id = l.id
                LEFT JOIN products p
                    ON p.id = lp.product_id
                    AND p.is_actief = 1
                GROUP BY
                    l.id,
                    l.naam,
                    l.adres,
                    l.telefoon,
                    l.email,
                    l.is_actief,
                    l.datum_aangemaakt,
                    l.datum_gewijzigd
                ORDER BY l.naam ASC;
            END
        ');

        // -------------------------------------------------------
        // SP: Controleer of leverancier naam al bestaat
        // -------------------------------------------------------
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_check_leverancier_naam');
        DB::unprepared('
            CREATE PROCEDURE sp_check_leverancier_naam(
                IN p_naam VARCHAR(50),
                OUT p_bestaat TINYINT(1)
            )
            BEGIN
                SET p_bestaat = 0;

                SELECT COUNT(*) INTO p_bestaat
                FROM leveranciers
                WHERE LOWER(TRIM(naam)) = LOWER(TRIM(p_naam))
                LIMIT 1;
            END
        ');

        // -------------------------------------------------------
        // SP: Nieuwe leverancier toevoegen
        // -------------------------------------------------------
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_create_leverancier');
        DB::unprepared('
            CREATE PROCEDURE sp_create_leverancier(
                IN  p_naam      VARCHAR(50),
                IN  p_adres     VARCHAR(100),
                IN  p_telefoon  VARCHAR(20),
                IN  p_email     VARCHAR(100),
                OUT p_new_id    INT,
                OUT p_fout      VARCHAR(255)
            )
            BEGIN
                DECLARE v_bestaat INT DEFAULT 0;

                SET p_new_id = NULL;
                SET p_fout   = NULL;

                -- Dubbelcheck: bestaat naam al?
                SELECT COUNT(*) INTO v_bestaat
                FROM leveranciers
                WHERE LOWER(TRIM(naam)) = LOWER(TRIM(p_naam))
                LIMIT 1;

                IF v_bestaat > 0 THEN
                    SET p_fout = \'deze bedrijfsnaam bestaat al\';
                ELSE
                    INSERT INTO leveranciers (naam, adres, telefoon, email, is_actief)
                    VALUES (TRIM(p_naam), TRIM(p_adres), TRIM(p_telefoon), TRIM(p_email), 1);

                    SET p_new_id = LAST_INSERT_ID();
                END IF;
            END
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_get_all_leveranciers');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_check_leverancier_naam');
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_create_leverancier');
    }
};
