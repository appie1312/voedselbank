-- Stored Procedure: allergieenoverzicht met klantgegevens
-- Uitvoeren in MySQL Workbench op database `voedselbank_maaskantje`

USE voedselbank_maaskantje;

DROP PROCEDURE IF EXISTS sp_allergieen_overzicht;

DELIMITER $$

CREATE PROCEDURE sp_allergieen_overzicht (
    IN p_klant_id BIGINT UNSIGNED,
    IN p_zoekterm VARCHAR(100),
    IN p_max_rijen INT
)
BEGIN
    DECLARE v_max_rijen INT DEFAULT 10;

    SET v_max_rijen = IFNULL(NULLIF(p_max_rijen, 0), 10);
    SET v_max_rijen = LEAST(GREATEST(v_max_rijen, 1), 50);

    SELECT
        kw.klant_id AS klant_id,
        k.gezinsnaam,
        wa.id AS allergie_id,
        wa.beschrijving AS allergie_beschrijving
    FROM klant_wens AS kw
    INNER JOIN wens_allergies AS wa ON wa.id = kw.wens_id
    INNER JOIN klanten AS k ON k.id = kw.klant_id
    WHERE (
        p_klant_id IS NULL
        OR kw.klant_id = p_klant_id
    )
    AND (
        p_zoekterm IS NULL
        OR p_zoekterm = ''
        OR wa.beschrijving LIKE CONCAT('%', p_zoekterm, '%')
        OR k.gezinsnaam LIKE CONCAT('%', p_zoekterm, '%')
    )
    ORDER BY k.gezinsnaam ASC, wa.beschrijving ASC
    LIMIT v_max_rijen;
END $$

DELIMITER ;

-- Test:
-- CALL sp_allergieen_overzicht(NULL, NULL, 10);
