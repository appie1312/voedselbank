<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        DB::unprepared('DROP PROCEDURE IF EXISTS sp_voedselpakketten_overzicht');

        DB::unprepared(<<<'SQL'
CREATE PROCEDURE sp_voedselpakketten_overzicht (
    IN p_zoekterm VARCHAR(100),
    IN p_aantal_rijen INT,
    IN p_eetwens VARCHAR(100)
)
BEGIN
    DECLARE v_limit INT DEFAULT 5;

    SET v_limit = IFNULL(p_aantal_rijen, 5);

    IF v_limit < 1 THEN
        SET v_limit = 1;
    END IF;

    IF v_limit > 25 THEN
        SET v_limit = 25;
    END IF;

    SELECT
        vp.id,
        vp.klant_id,
        k.gezinsnaam,
        vp.datum_samenstelling,
        vp.datum_uitgifte
    FROM voedselpakketten vp
    INNER JOIN klanten k ON k.id = vp.klant_id
    LEFT JOIN klant_wens kw ON kw.klant_id = k.id
    LEFT JOIN wens_allergies wa ON wa.id = kw.wens_id
    WHERE
        (
            p_zoekterm IS NULL
            OR p_zoekterm = ''
            OR k.gezinsnaam LIKE CONCAT('%', p_zoekterm, '%')
            OR CAST(vp.id AS CHAR) LIKE CONCAT('%', p_zoekterm, '%')
        )
        AND (
            p_eetwens IS NULL
            OR p_eetwens = ''
            OR wa.beschrijving = p_eetwens
        )
    GROUP BY
        vp.id,
        vp.klant_id,
        k.gezinsnaam,
        vp.datum_samenstelling,
        vp.datum_uitgifte
    ORDER BY vp.datum_samenstelling DESC, vp.id DESC
    LIMIT v_limit;
END
SQL);
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        DB::unprepared('DROP PROCEDURE IF EXISTS sp_voedselpakketten_overzicht');
    }
};
