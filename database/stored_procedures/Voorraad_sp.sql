DROP PROCEDURE IF EXISTS spGetVoorraadOverzicht;

DELIMITER $$

CREATE PROCEDURE spGetVoorraadOverzicht()
BEGIN
    SELECT
        p.naam AS product_naam,
        c.naam AS categorie_naam,
        v.hoeveelheid,
        v.locatie,
        CASE
            WHEN v.hoeveelheid <= 0 THEN 'Leeg'
            WHEN v.minimum_voorraad IS NOT NULL AND v.hoeveelheid <= v.minimum_voorraad THEN 'Aanvullen'
            ELSE 'Voldoende'
        END AS status
    FROM voorraad v
    INNER JOIN products p ON v.product_id = p.id
    INNER JOIN categories c ON p.categorie_id = c.id
    ORDER BY p.naam ASC;
END $$

DELIMITER ;
