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
        // SP: Alle leveranciers ophalen
        // -------------------------------------------------------
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_get_all_leveranciers');
        DB::unprepared('
            CREATE PROCEDURE sp_get_all_leveranciers()
            BEGIN
                SELECT
                    l.id,
                    l.bedrijfsnaam,
                    l.adres,
                    l.contactpersoon_naam,
                    l.contactpersoon_email,
                    l.telefoonnummer,
                    l.volgende_levering
                FROM leveranciers l
                ORDER BY l.bedrijfsnaam ASC;
            END
        ');

        // -------------------------------------------------------
        // SP: Controleer of leverancier naam al bestaat
        // -------------------------------------------------------
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_check_leverancier_naam');
        DB::unprepared('
            CREATE PROCEDURE sp_check_leverancier_naam(
                IN p_bedrijfsnaam VARCHAR(150),
                OUT p_bestaat TINYINT(1)
            )
            BEGIN
                SET p_bestaat = 0;

                SELECT COUNT(*) INTO p_bestaat
                FROM leveranciers
                WHERE LOWER(TRIM(bedrijfsnaam)) = LOWER(TRIM(p_bedrijfsnaam))
                LIMIT 1;
            END
        ');

        // -------------------------------------------------------
        // SP: Nieuwe leverancier toevoegen
        // -------------------------------------------------------
        DB::unprepared('DROP PROCEDURE IF EXISTS sp_create_leverancier');
        DB::unprepared('
            CREATE PROCEDURE sp_create_leverancier(
                IN  p_bedrijfsnaam        VARCHAR(150),
                IN  p_adres               VARCHAR(255),
                IN  p_contactpersoon_naam VARCHAR(100),
                IN  p_contactpersoon_email VARCHAR(150),
                IN  p_telefoonnummer      VARCHAR(20),
                IN  p_volgende_levering   DATETIME,
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
                WHERE LOWER(TRIM(bedrijfsnaam)) = LOWER(TRIM(p_bedrijfsnaam))
                LIMIT 1;

                IF v_bestaat > 0 THEN
                    SET p_fout = \'deze bedrijfsnaam bestaat al\';
                ELSE
                    INSERT INTO leveranciers (
                        bedrijfsnaam,
                        adres,
                        contactpersoon_naam,
                        contactpersoon_email,
                        telefoonnummer,
                        volgende_levering,
                        created_at,
                        updated_at
                    )
                    VALUES (
                        TRIM(p_bedrijfsnaam),
                        TRIM(p_adres),
                        TRIM(p_contactpersoon_naam),
                        TRIM(p_contactpersoon_email),
                        TRIM(p_telefoonnummer),
                        p_volgende_levering,
                        NOW(),
                        NOW()
                    );

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
