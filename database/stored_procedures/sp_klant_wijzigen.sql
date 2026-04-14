-- Stored Procedure: klant wijzigen
-- Uitvoeren in MySQL Workbench op database `voedselbank_maaskantje`

USE voedselbank_maaskantje;

DROP PROCEDURE IF EXISTS sp_klant_wijzigen;

DELIMITER $$

CREATE PROCEDURE sp_klant_wijzigen (
    IN p_klant_id BIGINT UNSIGNED,
    IN p_gezinsnaam VARCHAR(100),
    IN p_adres VARCHAR(255),
    IN p_telefoonnummer VARCHAR(20),
    IN p_emailadres VARCHAR(150),
    IN p_aanwezigheidsstatus VARCHAR(30),
    IN p_aantal_volwassenen INT,
    IN p_aantal_kinderen INT,
    IN p_aantal_babys INT
)
BEGIN
    DECLARE v_klant_bestaat INT DEFAULT 0;
    DECLARE v_bestaat_email INT DEFAULT 0;

    SELECT COUNT(*)
    INTO v_klant_bestaat
    FROM klanten
    WHERE id = p_klant_id;

    IF v_klant_bestaat = 0 THEN
        SELECT 0 AS gewijzigd, 0 AS bestaat_email_al, 0 AS klant_bestaat;
    ELSE
        IF p_emailadres IS NOT NULL AND p_emailadres <> '' THEN
            SELECT COUNT(*)
            INTO v_bestaat_email
            FROM klanten
            WHERE id <> p_klant_id
                AND emailadres = p_emailadres;
        END IF;

        IF v_bestaat_email > 0 THEN
            SELECT 0 AS gewijzigd, 1 AS bestaat_email_al, 1 AS klant_bestaat;
        ELSE
            UPDATE klanten
            SET
                gezinsnaam = p_gezinsnaam,
                adres = p_adres,
                telefoonnummer = p_telefoonnummer,
                emailadres = NULLIF(p_emailadres, ''),
                aanwezigheidsstatus = p_aanwezigheidsstatus,
                aantal_volwassenen = p_aantal_volwassenen,
                aantal_kinderen = p_aantal_kinderen,
                aantal_babys = p_aantal_babys,
                updated_at = NOW()
            WHERE id = p_klant_id;

            SELECT 1 AS gewijzigd, 0 AS bestaat_email_al, 1 AS klant_bestaat;
        END IF;
    END IF;
END $$

DELIMITER ;

-- Test:
-- CALL sp_klant_wijzigen(1, 'Familie Jansen', 'Dorpsstraat 1', '0612345678', 'jansen@voorbeeld.nl', 'binnen_land', 2, 1, 0);
