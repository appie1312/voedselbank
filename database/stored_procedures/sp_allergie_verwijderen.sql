-- Stored Procedure: allergie verwijderen
-- Uitvoeren in MySQL Workbench op database `voedselbank_maaskantje`

USE voedselbank_maaskantje;

DROP PROCEDURE IF EXISTS sp_allergie_verwijderen;

DELIMITER $$

CREATE PROCEDURE sp_allergie_verwijderen (
    IN p_allergie_id BIGINT UNSIGNED
)
BEGIN
    DECLARE v_allergie_bestaat INT DEFAULT 0;
    DECLARE v_in_gebruik INT DEFAULT 0;

    SELECT COUNT(*)
    INTO v_allergie_bestaat
    FROM wens_allergies
    WHERE id = p_allergie_id;

    IF v_allergie_bestaat = 0 THEN
        SELECT
            0 AS verwijderd,
            0 AS in_gebruik,
            0 AS allergie_bestaat;
    ELSE
        SELECT COUNT(*)
        INTO v_in_gebruik
        FROM klant_wens
        WHERE wens_id = p_allergie_id;

        IF v_in_gebruik > 0 THEN
            SELECT
                0 AS verwijderd,
                1 AS in_gebruik,
                1 AS allergie_bestaat;
        ELSE
            DELETE FROM wens_allergies
            WHERE id = p_allergie_id;

            SELECT
                1 AS verwijderd,
                0 AS in_gebruik,
                1 AS allergie_bestaat;
        END IF;
    END IF;
END $$

DELIMITER ;

-- Test:
-- CALL sp_allergie_verwijderen(10);
