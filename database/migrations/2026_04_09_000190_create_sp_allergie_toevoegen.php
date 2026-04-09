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
    IN p_beschrijving VARCHAR(100)
)
BEGIN
    DECLARE v_allergie_id BIGINT UNSIGNED DEFAULT NULL;
    DECLARE v_is_gekoppeld INT DEFAULT 0;

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

        SELECT
            1 AS toegevoegd,
            0 AS bestaat_gekoppeld,
            0 AS bestaat_al,
            LAST_INSERT_ID() AS allergie_id;
    ELSE
        SELECT COUNT(*)
        INTO v_is_gekoppeld
        FROM klant_wens
        WHERE wens_id = v_allergie_id;

        IF v_is_gekoppeld > 0 THEN
            SELECT
                0 AS toegevoegd,
                1 AS bestaat_gekoppeld,
                1 AS bestaat_al,
                v_allergie_id AS allergie_id;
        ELSE
            SELECT
                0 AS toegevoegd,
                0 AS bestaat_gekoppeld,
                1 AS bestaat_al,
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
