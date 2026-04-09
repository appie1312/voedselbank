-- Stored Procedure: klant verwijderen
-- Uitvoeren in MySQL Workbench op database `voedselbank_maaskantje`

USE voedselbank_maaskantje;

DROP PROCEDURE IF EXISTS sp_klant_verwijderen;

DELIMITER $$

CREATE PROCEDURE sp_klant_verwijderen (
    IN p_klant_id BIGINT UNSIGNED
)
BEGIN
    DECLARE v_klant_bestaat INT DEFAULT 0;
    DECLARE v_aanwezigheidsstatus VARCHAR(30) DEFAULT NULL;

    SELECT COUNT(*), MAX(aanwezigheidsstatus)
    INTO v_klant_bestaat, v_aanwezigheidsstatus
    FROM klanten
    WHERE id = p_klant_id;

    IF v_klant_bestaat = 0 THEN
        SELECT 0 AS verwijderd, 0 AS aanwezig, 0 AS klant_bestaat;
    ELSEIF v_aanwezigheidsstatus = 'binnen_land' THEN
        SELECT 0 AS verwijderd, 1 AS aanwezig, 1 AS klant_bestaat;
    ELSE
        START TRANSACTION;

        DELETE pp
        FROM pakket_product AS pp
        INNER JOIN voedselpakketten AS vp ON vp.id = pp.pakket_id
        WHERE vp.klant_id = p_klant_id;

        DELETE vp
        FROM voedselpakketten AS vp
        INNER JOIN klanten AS k ON k.id = vp.klant_id
        WHERE k.id = p_klant_id;

        DELETE kw
        FROM klant_wens AS kw
        INNER JOIN klanten AS k ON k.id = kw.klant_id
        WHERE k.id = p_klant_id;

        DELETE FROM klanten WHERE id = p_klant_id;

        COMMIT;

        SELECT 1 AS verwijderd, 0 AS aanwezig, 1 AS klant_bestaat;
    END IF;
END $$

DELIMITER ;

-- Test:
-- CALL sp_klant_verwijderen(1);
