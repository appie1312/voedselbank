<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_allergieen_overzicht');

        DB::unprepared(<<<'SQL'
CREATE PROCEDURE sp_allergieen_overzicht (
    IN p_klant_id BIGINT UNSIGNED,
    IN p_zoekterm VARCHAR(100),
    IN p_max_rijen INT
)
BEGIN
    DECLARE v_max_rijen INT DEFAULT 10;

    SET v_max_rijen = IFNULL(NULLIF(p_max_rijen, 0), 10);
    SET v_max_rijen = LEAST(GREATEST(v_max_rijen, 1), 50);

    IF p_klant_id IS NULL THEN
        SELECT
            wa.id AS allergie_id,
            wa.beschrijving AS allergie_beschrijving
        FROM wens_allergies AS wa
        WHERE (
            p_zoekterm IS NULL
            OR p_zoekterm = ''
            OR wa.beschrijving LIKE CONCAT('%', p_zoekterm, '%')
        )
        ORDER BY wa.beschrijving ASC
        LIMIT v_max_rijen;
    ELSE
        SELECT
            wa.id AS allergie_id,
            wa.beschrijving AS allergie_beschrijving
        FROM klant_wens AS kw
        INNER JOIN wens_allergies AS wa ON wa.id = kw.wens_id
        WHERE kw.klant_id = p_klant_id
        AND (
            p_zoekterm IS NULL
            OR p_zoekterm = ''
            OR wa.beschrijving LIKE CONCAT('%', p_zoekterm, '%')
        )
        ORDER BY wa.beschrijving ASC
        LIMIT v_max_rijen;
    END IF;
END
SQL);
    }

    public function down(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_allergieen_overzicht');
    }
};
