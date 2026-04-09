DELIMITER $$

DROP PROCEDURE IF EXISTS sp_get_all_leveranciers $$
CREATE PROCEDURE sp_get_all_leveranciers()
BEGIN
    SELECT 
        l.id,
        l.naam,
        l.adres,
        l.telefoon,
        l.email,
        l.is_actief
    FROM leveranciers l
    WHERE l.is_actief = 1;
END $$

DELIMITER ;