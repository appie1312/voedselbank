use voedselbank_maaskantje;
DELIMITER $$

DROP PROCEDURE IF EXISTS sp_get_all_leveranciers $$
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
END $$

DELIMITER ;
