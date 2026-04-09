DROP DATABASE voedselbank_maaskantje;
CREATE DATABASE voedselbank_maaskantje CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
use voedselbank_maaskantje;
-- =========================
-- KLANTEN
-- =========================
-- =========================
-- KLANTEN
-- =========================
CREATE TABLE klanten (
    id INT AUTO_INCREMENT PRIMARY KEY,
    naam VARCHAR(100) NOT NULL,
    email VARCHAR(100) NULL,
    telefoon VARCHAR(20) NULL,
    adres VARCHAR(150) NULL,
    datum_aangemaakt DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    datum_gewijzigd DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =========================
-- CONTACT
-- =========================
CREATE TABLE contact (
    id INT AUTO_INCREMENT PRIMARY KEY,
    voornaam VARCHAR(50) NOT NULL,
    achternaam VARCHAR(50) NOT NULL,
    telefoon VARCHAR(20) NULL,
    email VARCHAR(100) NULL,
    functie VARCHAR(50) NULL,
    datum_aangemaakt DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    datum_gewijzigd DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =========================
-- LEVERANCIERS
-- =========================
CREATE TABLE leveranciers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    naam VARCHAR(50) NOT NULL,
    adres VARCHAR(100) NULL,
    telefoon VARCHAR(20) NULL,
    email VARCHAR(100) NULL,
    is_actief TINYINT(1) NOT NULL DEFAULT 1,
    datum_aangemaakt DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    datum_gewijzigd DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =========================
-- CATEGORIES
-- =========================
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    naam VARCHAR(100) NOT NULL,
    beschrijving TEXT NULL,
    is_actief TINYINT(1) NOT NULL DEFAULT 1,
    datum_aangemaakt DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    datum_gewijzigd DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =========================
-- PRODUCTS
-- =========================
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    categorie_id INT NOT NULL,
    naam VARCHAR(150) NOT NULL,
    beschrijving TEXT NULL,
    prijs DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    is_actief TINYINT(1) NOT NULL DEFAULT 1,
    datum_aangemaakt DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    datum_gewijzigd DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_products_category FOREIGN KEY (categorie_id) REFERENCES categories(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =========================
-- VOORRAAD
-- =========================
CREATE TABLE voorraad (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    hoeveelheid INT NOT NULL DEFAULT 0,
    minimum_voorraad INT NULL DEFAULT 0,
    locatie VARCHAR(100) NULL,
    datum_aangemaakt DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    datum_gewijzigd DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_voorraad_product FOREIGN KEY (product_id) REFERENCES products(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =========================
-- WENSEN / ALLERGIEËN
-- =========================
CREATE TABLE wensen (
    id INT AUTO_INCREMENT PRIMARY KEY,
    naam VARCHAR(100) NOT NULL,
    type VARCHAR(50) NULL,
    datum_aangemaakt DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    datum_gewijzigd DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =========================
-- KLANT_WENS
-- =========================
CREATE TABLE klant_wens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    klant_id INT NOT NULL,
    wens_id INT NOT NULL,
    CONSTRAINT fk_klant_wens_klant FOREIGN KEY (klant_id) REFERENCES klanten(id),
    CONSTRAINT fk_klant_wens_wens FOREIGN KEY (wens_id) REFERENCES wensen(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =========================
-- VOEDSELPAKKET
-- =========================
CREATE TABLE voedselpakket (
    id INT AUTO_INCREMENT PRIMARY KEY,
    klant_id INT NOT NULL,
    datum_samenstelling DATE NOT NULL,
    datum_uitgifte DATE NULL,
    is_actief TINYINT(1) NOT NULL DEFAULT 1,
    opmerking VARCHAR(250) NULL,
    aangemaakt_datum DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    gewijzigd_datum DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_voedselpakket_klant FOREIGN KEY (klant_id) REFERENCES klanten(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =========================
-- VOEDSELPAKKET_PRODUCT
-- =========================
CREATE TABLE voedselpakket_product (
    id INT AUTO_INCREMENT PRIMARY KEY,
    voedselpakket_id INT NOT NULL,
    product_id INT NOT NULL,
    aantal INT NOT NULL DEFAULT 1,
    datum_aangemaakt DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    datum_gewijzigd DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_voedselpakket_product_pakket FOREIGN KEY (voedselpakket_id) REFERENCES voedselpakket(id),
    CONSTRAINT fk_voedselpakket_product_product FOREIGN KEY (product_id) REFERENCES products(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
