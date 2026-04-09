DROP DATABASE IF EXISTS voedselbank_maaskantje;
CREATE DATABASE voedselbank_maaskantje CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE voedselbank_maaskantje;

-- =========================
-- KLANTEN
-- =========================
CREATE TABLE klanten (
    id INT AUTO_INCREMENT PRIMARY KEY,
    naam VARCHAR(100) NOT NULL,
    email VARCHAR(100) NULL,
    telefoon VARCHAR(20) NULL,
    adres VARCHAR(150) NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =========================
-- CONTACTEN
-- =========================
CREATE TABLE contacten (
    id INT AUTO_INCREMENT PRIMARY KEY,
    voornaam VARCHAR(50) NOT NULL,
    achternaam VARCHAR(50) NOT NULL,
    telefoon VARCHAR(20) NULL,
    email VARCHAR(100) NULL,
    functie VARCHAR(50) NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =========================
-- KLANT_CONTACTEN
-- =========================
CREATE TABLE klant_contacten (
    id INT AUTO_INCREMENT PRIMARY KEY,
    klant_id INT NOT NULL,
    contact_id INT NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_klant_contacten_klant
        FOREIGN KEY (klant_id) REFERENCES klanten(id)
        ON DELETE CASCADE,
    CONSTRAINT fk_klant_contacten_contact
        FOREIGN KEY (contact_id) REFERENCES contacten(id)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =========================
-- LEVERANCIER_CONTACTEN
-- =========================
CREATE TABLE leverancier_contacten (
    id INT AUTO_INCREMENT PRIMARY KEY,
    leverancier_id INT NOT NULL,
    contact_id INT NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_leverancier_contacten_leverancier
        FOREIGN KEY (leverancier_id) REFERENCES leveranciers(id)
        ON DELETE CASCADE,
    CONSTRAINT fk_leverancier_contacten_contact
        FOREIGN KEY (contact_id) REFERENCES contacten(id)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =========================
-- CATEGORIEEN
-- =========================
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    naam VARCHAR(100) NOT NULL,
    beschrijving TEXT NULL,
    is_actief TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =========================
-- PRODUCTEN
-- =========================
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    categorie_id INT NOT NULL,
    naam VARCHAR(150) NOT NULL,
    beschrijving TEXT NULL,
    prijs DECIMAL(10,2) NOT NULL,
    is_actief TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_products_category
        FOREIGN KEY (categorie_id) REFERENCES categories(id)
        ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =========================
-- LEVERANCIER_PRODUCTEN
-- =========================
CREATE TABLE leverancier_products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    leverancier_id INT NOT NULL,
    product_id INT NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_leverancier_products_leverancier
        FOREIGN KEY (leverancier_id) REFERENCES leveranciers(id)
        ON DELETE CASCADE,
    CONSTRAINT fk_leverancier_products_product
        FOREIGN KEY (product_id) REFERENCES products(id)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =========================
-- VOORRADEN
-- =========================
CREATE TABLE voorraden (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    hoeveelheid INT NOT NULL DEFAULT 0,
    minimum_voorraad INT NULL DEFAULT 0,
    locatie VARCHAR(100) NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_voorraden_product
        FOREIGN KEY (product_id) REFERENCES products(id)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =========================
-- WENSEN
-- =========================
CREATE TABLE wensen (
    id INT AUTO_INCREMENT PRIMARY KEY,
    naam VARCHAR(100) NOT NULL,
    type VARCHAR(50) NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =========================
-- KLANT_WENSEN
-- =========================
CREATE TABLE klant_wensen (
    id INT AUTO_INCREMENT PRIMARY KEY,
    klant_id INT NOT NULL,
    wens_id INT NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_klant_wensen_klant
        FOREIGN KEY (klant_id) REFERENCES klanten(id)
        ON DELETE CASCADE,
    CONSTRAINT fk_klant_wensen_wens
        FOREIGN KEY (wens_id) REFERENCES wensen(id)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =========================
-- VOEDSELPAKKETTEN
-- =========================
CREATE TABLE voedselpakketten (
    id INT AUTO_INCREMENT PRIMARY KEY,
    klant_id INT NOT NULL,
    datum_samenstelling DATE NOT NULL,
    datum_uitgifte DATE NULL,
    is_actief TINYINT(1) NOT NULL DEFAULT 1,
    opmerking VARCHAR(250) NULL,
    created_at DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
    updated_at DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),

    CONSTRAINT fk_voedselpakketten_klant
        FOREIGN KEY (klant_id) REFERENCES klanten(id)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =========================
-- VOEDSELPAKKET_PRODUCTEN
-- =========================
CREATE TABLE voedselpakket_producten (
    id INT AUTO_INCREMENT PRIMARY KEY,
    voedselpakket_id INT NOT NULL,
    product_id INT NOT NULL,
    aantal INT NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_voedselpakket_producten_pakket
        FOREIGN KEY (voedselpakket_id) REFERENCES voedselpakketten(id)
        ON DELETE CASCADE,
    CONSTRAINT fk_voedselpakket_producten_product
        FOREIGN KEY (product_id) REFERENCES products(id)
        ON DELETE CASCADE
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
    last_acptivity INT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE INDEX sessions_user_id_index ON sessions (user_id);
CREATE INDEX sessions_last_activity_index ON sessions (last_activity);
