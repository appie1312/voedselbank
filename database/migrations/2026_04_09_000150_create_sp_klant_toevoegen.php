<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_klant_toevoegen');

        DB::unprepared(<<<'SQL'
CREATE PROCEDURE sp_klant_toevoegen (
    IN p_gezinsnaam VARCHAR(100),
    IN p_adres VARCHAR(255),
    IN p_telefoonnummer VARCHAR(20),
    IN p_emailadres VARCHAR(150),
    IN p_aantal_volwassenen INT,
    IN p_aantal_kinderen INT,
    IN p_aantal_babys INT
)
BEGIN
    DECLARE v_bestaat INT DEFAULT 0;
    DECLARE v_klant_id BIGINT UNSIGNED DEFAULT NULL;

    SELECT COUNT(*)
    INTO v_bestaat
    FROM klanten
    WHERE (
        (p_emailadres IS NOT NULL AND p_emailadres <> '' AND emailadres = p_emailadres)
        OR (
            gezinsnaam = p_gezinsnaam
            AND adres = p_adres
            AND telefoonnummer = p_telefoonnummer
        )
    );

    IF v_bestaat > 0 THEN
        SELECT 0 AS toegevoegd, 1 AS bestaat_al, NULL AS klant_id;
    ELSE
        INSERT INTO klanten (
            gezinsnaam,
            adres,
            telefoonnummer,
            emailadres,
            aantal_volwassenen,
            aantal_kinderen,
            aantal_babys,
            created_at,
            updated_at
        ) VALUES (
            p_gezinsnaam,
            p_adres,
            p_telefoonnummer,
            NULLIF(p_emailadres, ''),
            p_aantal_volwassenen,
            p_aantal_kinderen,
            p_aantal_babys,
            NOW(),
            NOW()
        );

        SET v_klant_id = LAST_INSERT_ID();
        SELECT 1 AS toegevoegd, 0 AS bestaat_al, v_klant_id AS klant_id;
    END IF;
END
SQL);
    }

    public function down(): void
    {
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_klant_toevoegen');
    }
};
