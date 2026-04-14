<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_allergie_toevoegen');

        DB::unprepared(<<<'SQL'
CREATE PROCEDURE sp_allergie_toevoegen (
    IN p_klant_id BIGINT UNSIGNED,
    IN p_beschrijving VARCHAR(100)
)
BEGIN
    DECLARE v_allergie_id BIGINT UNSIGNED DEFAULT NULL;
    DECLARE v_klant_bestaat INT DEFAULT 0;
    DECLARE v_koppeling_bestaat INT DEFAULT 0;
    DECLARE v_bestaat_al INT DEFAULT 0;

    SELECT COUNT(*)
    INTO v_klant_bestaat
    FROM klanten
    WHERE id = p_klant_id;

    IF v_klant_bestaat = 0 THEN
        SELECT
            0 AS toegevoegd,
            0 AS bestaat_gekoppeld,
            0 AS bestaat_al,
            0 AS klant_bestaat,
            NULL AS allergie_id;
    ELSE
        SELECT id
        INTO v_allergie_id
        FROM wens_allergies
        WHERE LOWER(beschrijving) = LOWER(p_beschrijving)
        LIMIT 1;

        IF v_allergie_id IS NULL THEN
            INSERT INTO wens_allergies (
                beschrijving,
                created_at,
                updated_at
            ) VALUES (
                p_beschrijving,
                NOW(),
                NOW()
            );

            SET v_allergie_id = LAST_INSERT_ID();
            SET v_bestaat_al = 0;
        ELSE
            SET v_bestaat_al = 1;
        END IF;

        SELECT COUNT(*)
        INTO v_koppeling_bestaat
        FROM klant_wens
        WHERE klant_id = p_klant_id
            AND wens_id = v_allergie_id;

        IF v_koppeling_bestaat > 0 THEN
            SELECT
                0 AS toegevoegd,
                1 AS bestaat_gekoppeld,
                v_bestaat_al AS bestaat_al,
                1 AS klant_bestaat,
                v_allergie_id AS allergie_id;
        ELSE
            INSERT INTO klant_wens (
                klant_id,
                wens_id
            ) VALUES (
                p_klant_id,
                v_allergie_id
            );

            SELECT
                1 AS toegevoegd,
                0 AS bestaat_gekoppeld,
                v_bestaat_al AS bestaat_al,
                1 AS klant_bestaat,
                v_allergie_id AS allergie_id;
        END IF;
    END IF;
END
SQL);
    }

    public function down(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_allergie_toevoegen');
    }
};
