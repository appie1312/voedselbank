DROP DATABASE voedselbank_maaskantje;
CREATE DATABASE voedselbank_maaskantje CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
use voedselbank_maaskantje;
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

    FOREIGN KEY (klant_id) REFERENCES klanten(id),
    FOREIGN KEY (contact_id) REFERENCES contact(id)
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

    FOREIGN KEY (leverancier_id) REFERENCES leveranciers(id),
    FOREIGN KEY (contact_id) REFERENCES contact(id)
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

    FOREIGN KEY (categorie_id) REFERENCES categories(id)
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

    FOREIGN KEY (leverancier_id) REFERENCES leveranciers(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =========================
-- VOORRAAD
-- =========================
CREATE TABLE voorraad (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    hoeveelheid INT NOT NULL DEFAULT 0,
    minimum_voorraad INT,
    locatie VARCHAR(100),
    datum_aangemaakt DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    datum_gewijzigd DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (product_id) REFERENCES products(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =========================
-- WENSEN / ALLERGIEËN
-- =========================
CREATE TABLE wensen (
    id INT AUTO_INCREMENT PRIMARY KEY,
    naam VARCHAR(100) NOT NULL,
    type VARCHAR(50), -- bijv. allergie / voorkeur
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

    FOREIGN KEY (klant_id) REFERENCES klanten(id),
    FOREIGN KEY (wens_id) REFERENCES wensen(id)
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
    gewijzigd_datum DATETIME(6),

    FOREIGN KEY (klant_id) REFERENCES klanten(id)
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

    FOREIGN KEY (voedselpakket_id) REFERENCES voedselpakket(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;