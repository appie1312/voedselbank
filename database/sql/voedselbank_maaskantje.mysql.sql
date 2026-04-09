DROP DATABASE IF EXISTS voedselbank_maaskantje;
CREATE DATABASE voedselbank_maaskantje CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE voedselbank_maaskantje;

-- =========================
-- KLANTEN
-- =========================
CREATE TABLE klanten (
    id INT AUTO_INCREMENT PRIMARY KEY,
    naam VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    telefoon VARCHAR(20),
    adres VARCHAR(150),
    datum_aangemaakt DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    datum_gewijzigd DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =========================
-- CONTACT
-- =========================
CREATE TABLE contact (
    id INT AUTO_INCREMENT PRIMARY KEY,
    voornaam VARCHAR(50) NOT NULL,
    achternaam VARCHAR(50) NOT NULL,
    telefoon VARCHAR(20),
    email VARCHAR(100),
    functie VARCHAR(50),
    datum_aangemaakt DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    datum_gewijzigd DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =========================
-- KLANT_CONTACT
-- =========================
CREATE TABLE klant_contact (
    id INT AUTO_INCREMENT PRIMARY KEY,
    klant_id INT NOT NULL,
    contact_id INT NOT NULL,
    datum_aangemaakt DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    datum_gewijzigd DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_klant_contact_klant FOREIGN KEY (klant_id) REFERENCES klanten(id),
    CONSTRAINT fk_klant_contact_contact FOREIGN KEY (contact_id) REFERENCES contact(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =========================
-- LEVERANCIERS
-- =========================
CREATE TABLE leveranciers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    naam VARCHAR(50) NOT NULL,
    adres VARCHAR(100),
    telefoon VARCHAR(20),
    email VARCHAR(100),
    is_actief TINYINT(1) NOT NULL DEFAULT 1,
    datum_aangemaakt DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    datum_gewijzigd DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =========================
-- LEVERANCIER_CONTACT
-- =========================
CREATE TABLE leverancier_contact (
    id INT AUTO_INCREMENT PRIMARY KEY,
    leverancier_id INT NOT NULL,
    contact_id INT NOT NULL,
    datum_aangemaakt DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    datum_gewijzigd DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_leverancier_contact_leverancier FOREIGN KEY (leverancier_id) REFERENCES leveranciers(id),
    CONSTRAINT fk_leverancier_contact_contact FOREIGN KEY (contact_id) REFERENCES contact(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =========================
-- CATEGORIES
-- =========================
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    naam VARCHAR(100) NOT NULL,
    beschrijving TEXT,
    is_actief TINYINT(1) NOT NULL DEFAULT 1,
    datum_aangemaakt DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    datum_gewijzigd DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =========================
-- PRODUCTS
-- =========================
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    categorie_id INT NOT NULL,
    naam VARCHAR(150) NOT NULL,
    beschrijving TEXT,
    prijs DECIMAL(10,2) NOT NULL,
    is_actief TINYINT(1) NOT NULL DEFAULT 1,
    datum_aangemaakt DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    datum_gewijzigd DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_products_category FOREIGN KEY (categorie_id) REFERENCES categories(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =========================
-- LEVERANCIER_PRODUCTS
-- =========================
CREATE TABLE leverancier_products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    leverancier_id INT NOT NULL,
    product_id INT NOT NULL,
    datum_aangemaakt DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    datum_gewijzigd DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_leverancier_products_leverancier FOREIGN KEY (leverancier_id) REFERENCES leveranciers(id),
    CONSTRAINT fk_leverancier_products_product FOREIGN KEY (product_id) REFERENCES products(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =========================
-- VOORRAAD
-- =========================
CREATE TABLE voorraad (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    hoeveelheid INT NOT NULL DEFAULT 0,
    minimum_voorraad INT DEFAULT 0,
    locatie VARCHAR(100),
    datum_aangemaakt DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    datum_gewijzigd DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_voorraad_product FOREIGN KEY (product_id) REFERENCES products(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =========================
-- WENSEN / ALLERGIEËN
-- =========================
CREATE TABLE wensen (
    id INT AUTO_INCREMENT PRIMARY KEY,
    naam VARCHAR(100) NOT NULL,
    type VARCHAR(50),
    datum_aangemaakt DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    datum_gewijzigd DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =========================
-- KLANT_WENS
-- =========================
CREATE TABLE klant_wens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    klant_id INT NOT NULL,
    wens_id INT NOT NULL,
    CONSTRAINT fk_klant_wens_klant FOREIGN KEY (klant_id) REFERENCES klanten(id),
    CONSTRAINT fk_klant_wens_wens FOREIGN KEY (wens_id) REFERENCES wensen(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =========================
-- VOEDSELPAKKET
-- =========================
CREATE TABLE voedselpakket (
    id INT AUTO_INCREMENT PRIMARY KEY,
    klant_id INT NOT NULL,
    datum_samenstelling DATE NOT NULL,
    datum_uitgifte DATE,
    is_actief TINYINT(1) NOT NULL DEFAULT 1,
    opmerking VARCHAR(250),
    aangemaakt_datum DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
    gewijzigd_datum DATETIME(6) NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
    CONSTRAINT fk_voedselpakket_klant FOREIGN KEY (klant_id) REFERENCES klanten(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =========================
-- VOEDSELPAKKET_PRODUCT
-- =========================
CREATE TABLE voedselpakket_product (
    id INT AUTO_INCREMENT PRIMARY KEY,
    voedselpakket_id INT NOT NULL,
    product_id INT NOT NULL,
    aantal INT NOT NULL,
    datum_aangemaakt DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    datum_gewijzigd DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_voedselpakket_product_pakket FOREIGN KEY (voedselpakket_id) REFERENCES voedselpakket(id),
    CONSTRAINT fk_voedselpakket_product_product FOREIGN KEY (product_id) REFERENCES products(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =========================
-- LARAVEL SESSIONS
-- =========================
CREATE TABLE sessions (
    id VARCHAR(255) NOT NULL PRIMARY KEY,
    user_id BIGINT UNSIGNED NULL,
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    payload LONGTEXT NOT NULL,
    last_activity INT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE INDEX sessions_user_id_index ON sessions (user_id);
CREATE INDEX sessions_last_activity_index ON sessions (last_activity);

-- =========================
-- TESTDATA CATEGORIES
-- =========================
INSERT INTO categories (naam, beschrijving, is_actief) VALUES
('Groente', 'Verse groenten', 1),
('Fruit', 'Vers fruit', 1),
('Zuivel', 'Zuivelproducten', 1);

-- =========================
-- TESTDATA PRODUCTS
-- =========================
INSERT INTO products (categorie_id, naam, beschrijving, prijs, is_actief) VALUES
(1, 'Wortels', 'Zak wortels 1kg', 1.99, 1),
(2, 'Appels', 'Rode appels', 2.49, 1),
(3, 'Melk', 'Halfvolle melk 1L', 1.39, 1);

-- =========================
-- TESTDATA VOORRAAD
-- =========================
INSERT INTO voorraad (product_id, hoeveelheid, minimum_voorraad, locatie) VALUES
(1, 25, 10, 'Stelling A'),
(2, 5, 10, 'Stelling B'),
(3, 0, 5, 'Koeling 1');

-- =========================
-- STORED PROCEDURE VOOR OVERZICHT
-- =========================
DROP PROCEDURE IF EXISTS spGetVoorraadOverzicht;

DELIMITER $$

CREATE PROCEDURE spGetVoorraadOverzicht()
BEGIN
    SELECT
        v.id,
        p.naam AS product_naam,
        c.naam AS categorie_naam,
        v.hoeveelheid,
        v.minimum_voorraad,
        v.locatie,
        CASE
            WHEN v.hoeveelheid <= 0 THEN 'Leeg'
            WHEN v.hoeveelheid <= v.minimum_voorraad THEN 'Aanvullen'
            ELSE 'Voldoende'
        END AS status
    FROM voorraad v
    INNER JOIN products p ON v.product_id = p.id
    INNER JOIN categories c ON p.categorie_id = c.id
    ORDER BY p.naam ASC;
END $$

DELIMITER ;
