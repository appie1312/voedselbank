<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_klanten_overzicht');

        DB::unprepared(<<<'SQL'
CREATE PROCEDURE sp_klanten_overzicht (
    IN p_zoekterm VARCHAR(150),
    IN p_max_rijen INT
)
BEGIN
    DECLARE v_max_rijen INT DEFAULT 5;

    SET v_max_rijen = IFNULL(NULLIF(p_max_rijen, 0), 5);
    SET v_max_rijen = LEAST(GREATEST(v_max_rijen, 1), 25);

    SELECT
        k.id,
        k.gezinsnaam,
        k.adres,
        k.telefoonnummer,
        k.emailadres,
        k.aanwezigheidsstatus,
        k.aantal_volwassenen,
        k.aantal_kinderen,
        k.aantal_babys,
        COUNT(DISTINCT vp.id) AS totaal_voedselpakketten,
        GROUP_CONCAT(DISTINCT wa.beschrijving ORDER BY wa.beschrijving SEPARATOR ', ') AS wensen_allergieen
    FROM klanten AS k
    LEFT JOIN voedselpakketten AS vp ON vp.klant_id = k.id
    LEFT JOIN klant_wens AS kw ON kw.klant_id = k.id
    LEFT JOIN wens_allergies AS wa ON wa.id = kw.wens_id
    WHERE (
        p_zoekterm IS NULL
        OR p_zoekterm = ''
        OR k.gezinsnaam LIKE CONCAT('%', p_zoekterm, '%')
        OR k.adres LIKE CONCAT('%', p_zoekterm, '%')
        OR k.emailadres LIKE CONCAT('%', p_zoekterm, '%')
        OR k.telefoonnummer LIKE CONCAT('%', p_zoekterm, '%')
    )
    GROUP BY
        k.id,
        k.gezinsnaam,
        k.adres,
        k.telefoonnummer,
        k.emailadres,
        k.aanwezigheidsstatus,
        k.aantal_volwassenen,
        k.aantal_kinderen,
        k.aantal_babys
    ORDER BY k.gezinsnaam ASC
    LIMIT v_max_rijen;
END
SQL);
    }

    public function down(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_klanten_overzicht');
    }
};
