-- ==========================================
-- VOEDSELBANK MAASKANTJE - NIEUW MYSQL SCRIPT
-- ==========================================

DROP DATABASE IF EXISTS Voedselbank_Maaskantje_dag_na_dag_2;
CREATE DATABASE Voedselbank_Maaskantje_dag_na_dag_2
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE Voedselbank_Maaskantje_dag_na_dag_2;

-- ------------------------------------------
-- 1) Laravel Core Tables
-- ------------------------------------------

CREATE TABLE users (
    Id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    role VARCHAR(50) NOT NULL DEFAULT 'vrijwilliger',
    email_verified_at DATETIME(6) NULL,
    password VARCHAR(255) NOT NULL,
    remember_token VARCHAR(100) NULL,
    IsActief BIT(1) NOT NULL DEFAULT b'1',
    Opmerking VARCHAR(255) NULL,
    DatumAangemaakt DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
    DatumGewijzigd DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
    UNIQUE KEY uk_users_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE password_reset_tokens (
    email VARCHAR(255) PRIMARY KEY,
    token VARCHAR(255) NOT NULL,
    IsActief BIT(1) NOT NULL DEFAULT b'1',
    Opmerking VARCHAR(255) NULL,
    DatumAangemaakt DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
    DatumGewijzigd DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE sessions (
    id VARCHAR(255) PRIMARY KEY,
    user_id BIGINT UNSIGNED NULL,
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    payload LONGTEXT NOT NULL,
    last_activity INT NOT NULL,
    IsActief BIT(1) NOT NULL DEFAULT b'1',
    Opmerking VARCHAR(255) NULL,
    DatumAangemaakt DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
    DatumGewijzigd DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
    INDEX sessions_user_id_index (user_id),
    INDEX sessions_last_activity_index (last_activity)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE cache (
    `key` VARCHAR(255) PRIMARY KEY,
    `value` MEDIUMTEXT NOT NULL,
    expiration BIGINT NOT NULL,
    IsActief BIT(1) NOT NULL DEFAULT b'1',
    Opmerking VARCHAR(255) NULL,
    DatumAangemaakt DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
    DatumGewijzigd DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
    INDEX cache_expiration_index (expiration)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE cache_locks (
    `key` VARCHAR(255) PRIMARY KEY,
    `owner` VARCHAR(255) NOT NULL,
    expiration BIGINT NOT NULL,
    IsActief BIT(1) NOT NULL DEFAULT b'1',
    Opmerking VARCHAR(255) NULL,
    DatumAangemaakt DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
    DatumGewijzigd DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
    INDEX cache_locks_expiration_index (expiration)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE jobs (
    Id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    queue VARCHAR(255) NOT NULL,
    payload LONGTEXT NOT NULL,
    attempts TINYINT UNSIGNED NOT NULL,
    reserved_at INT UNSIGNED NULL,
    available_at INT UNSIGNED NOT NULL,
    created_at INT UNSIGNED NOT NULL,
    IsActief BIT(1) NOT NULL DEFAULT b'1',
    Opmerking VARCHAR(255) NULL,
    DatumAangemaakt DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
    DatumGewijzigd DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
    INDEX jobs_queue_index (queue)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE job_batches (
    id VARCHAR(255) PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    total_jobs INT NOT NULL,
    pending_jobs INT NOT NULL,
    failed_jobs INT NOT NULL,
    failed_job_ids LONGTEXT NOT NULL,
    options MEDIUMTEXT NULL,
    cancelled_at INT NULL,
    created_at INT NOT NULL,
    finished_at INT NULL,
    IsActief BIT(1) NOT NULL DEFAULT b'1',
    Opmerking VARCHAR(255) NULL,
    DatumAangemaakt DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
    DatumGewijzigd DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE failed_jobs (
    Id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    uuid VARCHAR(255) NOT NULL,
    connection TEXT NOT NULL,
    queue TEXT NOT NULL,
    payload LONGTEXT NOT NULL,
    exception LONGTEXT NOT NULL,
    failed_at DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
    IsActief BIT(1) NOT NULL DEFAULT b'1',
    Opmerking VARCHAR(255) NULL,
    DatumAangemaakt DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
    DatumGewijzigd DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
    UNIQUE KEY uk_failed_jobs_uuid (uuid)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE user_profiles (
    Id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    telefoon VARCHAR(30) NULL,
    adres VARCHAR(255) NULL,
    afdeling VARCHAR(120) NULL,
    beschikbaarheid VARCHAR(120) NULL,
    verantwoordelijkheden VARCHAR(120) NULL,
    bio TEXT NULL,
    IsActief BIT(1) NOT NULL DEFAULT b'1',
    Opmerking VARCHAR(255) NULL,
    DatumAangemaakt DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
    DatumGewijzigd DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
    UNIQUE KEY uk_user_profiles_user (user_id),
    CONSTRAINT fk_user_profiles_user
        FOREIGN KEY (user_id) REFERENCES users(Id)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------
-- 2) Basis tabellen
-- ------------------------------------------

CREATE TABLE Rol (
    Id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    Naam VARCHAR(100) NOT NULL,
    IsActief BIT(1) NOT NULL DEFAULT b'1',
    Opmerking VARCHAR(255) NULL,
    DatumAangemaakt DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
    DatumGewijzigd DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
    UNIQUE KEY uk_rol_naam (Naam)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE Categorie (
    Id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    Naam VARCHAR(50) NOT NULL,
    Omschrijving VARCHAR(255) NOT NULL,
    IsActief BIT(1) NOT NULL DEFAULT b'1',
    Opmerking VARCHAR(255) NULL,
    DatumAangemaakt DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
    DatumGewijzigd DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
    UNIQUE KEY uk_categorie_naam (Naam)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE Contact (
    Id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    Straat VARCHAR(150) NOT NULL,
    Huisnummer VARCHAR(20) NOT NULL,
    Toevoeging VARCHAR(20) NULL,
    Postcode VARCHAR(10) NOT NULL,
    Woonplaats VARCHAR(100) NOT NULL,
    Email VARCHAR(150) NOT NULL,
    Mobiel VARCHAR(20) NOT NULL,
    IsActief BIT(1) NOT NULL DEFAULT b'1',
    Opmerking VARCHAR(255) NULL,
    DatumAangemaakt DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
    DatumGewijzigd DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
    UNIQUE KEY uk_contact_email (Email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE Eetwens (
    Id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    Naam VARCHAR(100) NOT NULL,
    Omschrijving VARCHAR(255) NOT NULL,
    IsActief BIT(1) NOT NULL DEFAULT b'1',
    Opmerking VARCHAR(255) NULL,
    DatumAangemaakt DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
    DatumGewijzigd DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
    UNIQUE KEY uk_eetwens_naam (Naam)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE Gezin (
    Id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    Naam VARCHAR(120) NOT NULL,
    Code VARCHAR(20) NOT NULL,
    Omschrijving VARCHAR(255) NOT NULL,
    AantalVolwassenen INT NOT NULL DEFAULT 0,
    AantalKinderen INT NOT NULL DEFAULT 0,
    AantalBabys INT NOT NULL DEFAULT 0,
    TotaalAantalPersonen INT NOT NULL DEFAULT 0,
    IsActief BIT(1) NOT NULL DEFAULT b'1',
    Opmerking VARCHAR(255) NULL,
    DatumAangemaakt DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
    DatumGewijzigd DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
    UNIQUE KEY uk_gezin_code (Code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE Leverancier (
    Id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    Naam VARCHAR(150) NOT NULL,
    ContactPersoon VARCHAR(150) NOT NULL,
    LeverancierNummer VARCHAR(20) NOT NULL,
    LeverancierType VARCHAR(50) NOT NULL,
    IsActief BIT(1) NOT NULL DEFAULT b'1',
    Opmerking VARCHAR(255) NULL,
    DatumAangemaakt DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
    DatumGewijzigd DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
    UNIQUE KEY uk_leverancier_nummer (LeverancierNummer)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE Magazijn (
    Id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    Ontvangstdatum DATE NOT NULL,
    Uitleveringsdatum DATE NULL,
    VerpakkingsEenheid VARCHAR(50) NOT NULL,
    Aantal INT NOT NULL,
    IsActief BIT(1) NOT NULL DEFAULT b'1',
    Opmerking VARCHAR(255) NULL,
    DatumAangemaakt DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
    DatumGewijzigd DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE Persoon (
    Id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    GezinId BIGINT UNSIGNED NULL,
    Voornaam VARCHAR(100) NOT NULL,
    Tussenvoegsel VARCHAR(50) NULL,
    Achternaam VARCHAR(100) NOT NULL,
    Geboortedatum DATE NOT NULL,
    TypePersoon VARCHAR(50) NOT NULL,
    IsVertegenwoordiger BIT(1) NOT NULL DEFAULT b'0',
    IsActief BIT(1) NOT NULL DEFAULT b'1',
    Opmerking VARCHAR(255) NULL,
    DatumAangemaakt DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
    DatumGewijzigd DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
    CONSTRAINT fk_persoon_gezin
        FOREIGN KEY (GezinId) REFERENCES Gezin(Id)
        ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE Gebruiker (
    Id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    PersoonId BIGINT UNSIGNED NOT NULL,
    InlogNaam VARCHAR(100) NOT NULL,
    Gebruikersnaam VARCHAR(150) NOT NULL,
    Wachtwoord VARCHAR(255) NOT NULL,
    IsIngelogd BIT(1) NOT NULL DEFAULT b'0',
    Ingelogd DATETIME(6) NULL,
    Uitgelogd DATETIME(6) NULL,
    IsActief BIT(1) NOT NULL DEFAULT b'1',
    Opmerking VARCHAR(255) NULL,
    DatumAangemaakt DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
    DatumGewijzigd DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
    UNIQUE KEY uk_gebruiker_persoon (PersoonId),
    UNIQUE KEY uk_gebruiker_gebruikersnaam (Gebruikersnaam),
    CONSTRAINT fk_gebruiker_persoon
        FOREIGN KEY (PersoonId) REFERENCES Persoon(Id)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE Allergie (
    Id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    Naam VARCHAR(100) NOT NULL,
    Omschrijving VARCHAR(255) NOT NULL,
    AnafylactischRisico VARCHAR(50) NOT NULL,
    IsActief BIT(1) NOT NULL DEFAULT b'1',
    Opmerking VARCHAR(255) NULL,
    DatumAangemaakt DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
    DatumGewijzigd DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
    UNIQUE KEY uk_allergie_naam (Naam)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE Product (
    Id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    CategorieId BIGINT UNSIGNED NOT NULL,
    Naam VARCHAR(150) NOT NULL,
    SoortAllergie VARCHAR(100) NULL,
    Barcode VARCHAR(20) NOT NULL,
    Houdbaarheidsdatum DATE NOT NULL,
    Omschrijving VARCHAR(255) NOT NULL,
    Status VARCHAR(50) NOT NULL,
    IsActief BIT(1) NOT NULL DEFAULT b'1',
    Opmerking VARCHAR(255) NULL,
    DatumAangemaakt DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
    DatumGewijzigd DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
    CONSTRAINT fk_product_categorie
        FOREIGN KEY (CategorieId) REFERENCES Categorie(Id)
        ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE Voedselpakket (
    Id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    GezinId BIGINT UNSIGNED NOT NULL,
    PakketNummer INT NOT NULL,
    DatumSamenstelling DATE NOT NULL,
    DatumUitgifte DATE NULL,
    Status VARCHAR(50) NOT NULL,
    IsActief BIT(1) NOT NULL DEFAULT b'1',
    Opmerking VARCHAR(255) NULL,
    DatumAangemaakt DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
    DatumGewijzigd DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
    CONSTRAINT fk_voedselpakket_gezin
        FOREIGN KEY (GezinId) REFERENCES Gezin(Id)
        ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------
-- 3) Koppeltabellen
-- ------------------------------------------

CREATE TABLE AllergiePerPersoon (
    Id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    PersoonId BIGINT UNSIGNED NOT NULL,
    AllergieId BIGINT UNSIGNED NOT NULL,
    IsActief BIT(1) NOT NULL DEFAULT b'1',
    Opmerking VARCHAR(255) NULL,
    DatumAangemaakt DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
    DatumGewijzigd DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
    UNIQUE KEY uk_allergieperpersoon (PersoonId, AllergieId),
    CONSTRAINT fk_allergieperpersoon_persoon
        FOREIGN KEY (PersoonId) REFERENCES Persoon(Id)
        ON DELETE CASCADE,
    CONSTRAINT fk_allergieperpersoon_allergie
        FOREIGN KEY (AllergieId) REFERENCES Allergie(Id)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE RolPerGebruiker (
    Id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    GebruikerId BIGINT UNSIGNED NOT NULL,
    RolId BIGINT UNSIGNED NOT NULL,
    IsActief BIT(1) NOT NULL DEFAULT b'1',
    Opmerking VARCHAR(255) NULL,
    DatumAangemaakt DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
    DatumGewijzigd DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
    UNIQUE KEY uk_rolpergebruiker (GebruikerId, RolId),
    CONSTRAINT fk_rolpergebruiker_gebruiker
        FOREIGN KEY (GebruikerId) REFERENCES Gebruiker(Id)
        ON DELETE CASCADE,
    CONSTRAINT fk_rolpergebruiker_rol
        FOREIGN KEY (RolId) REFERENCES Rol(Id)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE EetwensPerGezin (
    Id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    GezinId BIGINT UNSIGNED NOT NULL,
    EetwensId BIGINT UNSIGNED NOT NULL,
    IsActief BIT(1) NOT NULL DEFAULT b'1',
    Opmerking VARCHAR(255) NULL,
    DatumAangemaakt DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
    DatumGewijzigd DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
    UNIQUE KEY uk_eetwenspergezin (GezinId, EetwensId),
    CONSTRAINT fk_eetwenspergezin_gezin
        FOREIGN KEY (GezinId) REFERENCES Gezin(Id)
        ON DELETE CASCADE,
    CONSTRAINT fk_eetwenspergezin_eetwens
        FOREIGN KEY (EetwensId) REFERENCES Eetwens(Id)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE ContactPerLeverancier (
    Id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    LeverancierId BIGINT UNSIGNED NOT NULL,
    ContactId BIGINT UNSIGNED NOT NULL,
    IsActief BIT(1) NOT NULL DEFAULT b'1',
    Opmerking VARCHAR(255) NULL,
    DatumAangemaakt DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
    DatumGewijzigd DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
    UNIQUE KEY uk_contactperleverancier (LeverancierId, ContactId),
    CONSTRAINT fk_contactperleverancier_leverancier
        FOREIGN KEY (LeverancierId) REFERENCES Leverancier(Id)
        ON DELETE CASCADE,
    CONSTRAINT fk_contactperleverancier_contact
        FOREIGN KEY (ContactId) REFERENCES Contact(Id)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE ContactPerGezin (
    Id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    GezinId BIGINT UNSIGNED NOT NULL,
    ContactId BIGINT UNSIGNED NOT NULL,
    IsActief BIT(1) NOT NULL DEFAULT b'1',
    Opmerking VARCHAR(255) NULL,
    DatumAangemaakt DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
    DatumGewijzigd DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
    UNIQUE KEY uk_contactpergezin (GezinId, ContactId),
    CONSTRAINT fk_contactpergezin_gezin
        FOREIGN KEY (GezinId) REFERENCES Gezin(Id)
        ON DELETE CASCADE,
    CONSTRAINT fk_contactpergezin_contact
        FOREIGN KEY (ContactId) REFERENCES Contact(Id)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE ProductPerVoedselpakket (
    Id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    VoedselpakketId BIGINT UNSIGNED NOT NULL,
    ProductId BIGINT UNSIGNED NOT NULL,
    AantalProductEenheden INT NOT NULL,
    IsActief BIT(1) NOT NULL DEFAULT b'1',
    Opmerking VARCHAR(255) NULL,
    DatumAangemaakt DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
    DatumGewijzigd DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
    UNIQUE KEY uk_productpervoedselpakket (VoedselpakketId, ProductId),
    CONSTRAINT fk_productpervoedselpakket_voedselpakket
        FOREIGN KEY (VoedselpakketId) REFERENCES Voedselpakket(Id)
        ON DELETE CASCADE,
    CONSTRAINT fk_productpervoedselpakket_product
        FOREIGN KEY (ProductId) REFERENCES Product(Id)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE ProductPerLeverancier (
    Id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    LeverancierId BIGINT UNSIGNED NOT NULL,
    ProductId BIGINT UNSIGNED NOT NULL,
    DatumAangeleverd DATE NOT NULL,
    DatumEerstVolgendeLevering DATE NOT NULL,
    IsActief BIT(1) NOT NULL DEFAULT b'1',
    Opmerking VARCHAR(255) NULL,
    DatumAangemaakt DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
    DatumGewijzigd DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
    UNIQUE KEY uk_productperleverancier (LeverancierId, ProductId, DatumAangeleverd),
    CONSTRAINT fk_productperleverancier_leverancier
        FOREIGN KEY (LeverancierId) REFERENCES Leverancier(Id)
        ON DELETE CASCADE,
    CONSTRAINT fk_productperleverancier_product
        FOREIGN KEY (ProductId) REFERENCES Product(Id)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE ProductPerMagazijn (
    Id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    ProductId BIGINT UNSIGNED NOT NULL,
    MagazijnId BIGINT UNSIGNED NOT NULL,
    Locatie VARCHAR(100) NOT NULL,
    IsActief BIT(1) NOT NULL DEFAULT b'1',
    Opmerking VARCHAR(255) NULL,
    DatumAangemaakt DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
    DatumGewijzigd DATETIME(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
    UNIQUE KEY uk_productpermagazijn (ProductId, MagazijnId),
    CONSTRAINT fk_productpermagazijn_product
        FOREIGN KEY (ProductId) REFERENCES Product(Id)
        ON DELETE CASCADE,
    CONSTRAINT fk_productpermagazijn_magazijn
        FOREIGN KEY (MagazijnId) REFERENCES Magazijn(Id)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------
-- 4) Seed data
-- ------------------------------------------

INSERT INTO Rol (Id, Naam, IsActief, Opmerking, DatumAangemaakt, DatumGewijzigd)
VALUES
    (1, 'Manager', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (2, 'Medewerker', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (3, 'Vrijwilliger', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000');

INSERT INTO Categorie (Id, Naam, Omschrijving, IsActief, Opmerking, DatumAangemaakt, DatumGewijzigd)
VALUES
    (1, 'AGF', 'Aardappelen groente en fruit', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (2, 'KV', 'Kaas en vleeswaren', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (3, 'ZPE', 'Zuivel plantaardig en eieren', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (4, 'BB', 'Bakkerij en Banket', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (5, 'FSKT', 'Frisdranken, sappen, koffie en thee', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (6, 'PRW', 'Pasta, rijst en wereldkeuken', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (7, 'SSKO', 'Soepen, sauzen, kruiden en olie', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (8, 'SKCC', 'Snoep, koek, chips en chocolade', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (9, 'BVH', 'Baby, verzorging en hygiëne', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000');

INSERT INTO Contact (Id, Straat, Huisnummer, Toevoeging, Postcode, Woonplaats, Email, Mobiel, IsActief, Opmerking, DatumAangemaakt, DatumGewijzigd)
VALUES
    (1, 'Prinses Irenestraat', '12', 'A', '5271TH', 'Maaskantje', 'j.van.zevenhuizen@gmail.com', '+31 623456123', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (2, 'Gibraltarstraat', '234', NULL, '5271TJ', 'Maaskantje', 'a.bergkamp@hotmail.com', '+31 623456123', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (3, 'Der Kinderenstraat', '456', 'Bis', '5271TH', 'Maaskantje', 's.van.de.heuvel@gmail.com', '+31 623456123', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (4, 'Nachtegaalstraat', '233', 'A', '5271TJ', 'Maaskantje', 'e.scherder@gmail.com', '+31 623456123', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (5, 'Bertram Russellstraat', '45', NULL, '5271TH', 'Maaskantje', 'f.de.jong@hotmail.com', '+31 623456123', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (6, 'Leonardo Da VinciHof', '34', NULL, '5271ZE', 'Maaskantje', 'h.van.der.berg@gmail.com', '+31 623456123', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (7, 'Siegfried Knutsenlaan', '234', NULL, '5271ZE', 'Maaskantje', 'r.ter.weijden@ah.nl', '+31 623456123', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (8, 'Theo de Bokstraat', '256', NULL, '5271ZH', 'Maaskantje', 'l.pastoor@gmail.com', '+31 623456123', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (9, 'Meester van Leerhof', '2', 'A', '5271ZH', 'Maaskantje', 'm.yazidi@gemeenteutrecht.nl', '+31 623456123', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (10, 'Van Wemelenplantsoen', '300', NULL, '5271TH', 'Maaskantje', 'b.van.driel@gmail.com', '+31 623456123', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (11, 'Terlingenhof', '20', NULL, '5271TH', 'Maaskantje', 'j.pastorius@gmail.com', '+31 623456356', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (12, 'Veldhoen', '31', NULL, '5271ZE', 'Maaskantje', 's.dollaard@gmail.com', '+31 623452314', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (13, 'ScheringaDreef', '37', NULL, '5271ZE', 'Vught', 'j.blokker@gemeentevught.nl', '+31 623452314', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000');

INSERT INTO Eetwens (Id, Naam, Omschrijving, IsActief, Opmerking, DatumAangemaakt, DatumGewijzigd)
VALUES
    (1, 'GeenVarken', 'Geen Varkensvlees', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (2, 'Veganistisch', 'Geen zuivelproducten en vlees', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (3, 'Vegetarisch', 'Geen vlees', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (4, 'Omnivoor', 'Geen beperkingen', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000');

INSERT INTO Gezin (Id, Naam, Code, Omschrijving, AantalVolwassenen, AantalKinderen, AantalBabys, TotaalAantalPersonen, IsActief, Opmerking, DatumAangemaakt, DatumGewijzigd)
VALUES
    (1, 'ZevenhuizenGezin', 'G0001', 'Bijstandsgezin', 2, 2, 0, 4, b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (2, 'BergkampGezin', 'G0002', 'Bijstandsgezin', 2, 1, 1, 4, b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (3, 'HeuvelGezin', 'G0003', 'Bijstandsgezin', 2, 0, 0, 2, b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (4, 'ScherderGezin', 'G0004', 'Bijstandsgezin', 1, 0, 2, 3, b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (5, 'DeJongGezin', 'G0005', 'Bijstandsgezin', 1, 1, 0, 2, b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (6, 'VanderBergGezin', 'G0006', 'AlleenGaande', 1, 0, 0, 1, b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000');

INSERT INTO Leverancier (Id, Naam, ContactPersoon, LeverancierNummer, LeverancierType, IsActief, Opmerking, DatumAangemaakt, DatumGewijzigd)
VALUES
    (1, 'Albert Heijn', 'Ruud ter Weijden', 'L0001', 'Bedrijf', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (2, 'Albertus Kerk', 'Leo Pastoor', 'L0002', 'Instelling', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (3, 'Gemeente Utrecht', 'Mohammed Yazidi', 'L0003', 'Overheid', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (4, 'Boerderij Meerhoven', 'Bertus van Driel', 'L0004', 'Particulier', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (5, 'Jan van der Heijden', 'Jan van der Heijden', 'L0005', 'Donor', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (6, 'Vomar', 'Jaco Pastorius', 'L0006', 'Bedrijf', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (7, 'DekaMarkt', 'Sil den Dollaard', 'L0007', 'Bedrijf', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (8, 'Gemeente Vught', 'Jan Blokker', 'L0008', 'Overheid', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000');

INSERT INTO Magazijn (Id, Ontvangstdatum, Uitleveringsdatum, VerpakkingsEenheid, Aantal, IsActief, Opmerking, DatumAangemaakt, DatumGewijzigd)
VALUES
    (1, '2026-03-12', NULL, '5 kg', 20, b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (2, '2026-04-02', NULL, '2.5 kg', 40, b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (3, '2026-03-16', NULL, '1 kg', 30, b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (4, '2026-04-08', NULL, '1.5 kg', 25, b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (5, '2026-04-06', NULL, '4 stuks', 75, b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (6, '2026-03-12', NULL, '1 kg/tros', 60, b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (7, '2026-03-20', NULL, '2 kg/tros', 200, b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (8, '2026-04-02', NULL, '200 g', 45, b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (9, '2026-04-04', NULL, '100 g', 60, b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (10, '2026-04-07', NULL, '1 liter', 120, b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (11, '2026-04-01', NULL, '250 g', 80, b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (12, '2026-03-18', NULL, '6 stuks', 120, b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (13, '2026-03-19', NULL, '800 g', 220, b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (14, '2026-03-10', NULL, '1 stuk', 130, b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (15, '2026-03-13', NULL, '150 ml', 72, b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (16, '2026-03-18', NULL, '1 l', 12, b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (17, '2026-03-11', NULL, '250 g', 300, b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (18, '2026-04-02', NULL, '25 zakjes', 280, b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (19, '2026-04-09', NULL, '500 g', 330, b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (20, '2026-04-03', NULL, '1 kg', 34, b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (21, '2026-04-02', NULL, '50 g', 23, b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (22, '2026-03-16', NULL, '1 l', 46, b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (23, '2026-03-14', NULL, '250 ml', 98, b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (24, '2026-04-07', NULL, '1 potje', 56, b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (25, '2026-03-17', NULL, '1 l', 210, b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (26, '2026-04-05', NULL, '4 stuks', 24, b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (27, '2026-04-07', NULL, '300 g', 87, b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (28, '2026-04-06', NULL, '200 g', 230, b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (29, '2026-04-08', NULL, '80 g', 30, b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000');

INSERT INTO Persoon (Id, GezinId, Voornaam, Tussenvoegsel, Achternaam, Geboortedatum, TypePersoon, IsVertegenwoordiger, IsActief, Opmerking, DatumAangemaakt, DatumGewijzigd)
VALUES
    (1, NULL, 'Hans', 'van', 'Leeuwen', '1958-02-12', 'Manager', b'0', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (2, NULL, 'Jan', 'van der', 'Sluijs', '1993-04-30', 'Medewerker', b'0', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (3, NULL, 'Herman', 'den', 'Duiker', '1989-08-30', 'Vrijwilliger', b'0', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (4, 1, 'Johan', 'van', 'Zevenhuizen', '1990-05-20', 'Klant', b'1', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (5, 1, 'Sarah', 'den', 'Dolder', '1985-03-23', 'Klant', b'0', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (6, 1, 'Theo', 'van', 'Zevenhuizen', '2015-03-08', 'Klant', b'0', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (7, 1, 'Jantien', 'van', 'Zevenhuizen', '2016-09-20', 'Klant', b'0', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (8, 2, 'Arjan', NULL, 'Bergkamp', '1968-07-12', 'Klant', b'1', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (9, 2, 'Janneke', NULL, 'Sanders', '1969-05-11', 'Klant', b'0', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (10, 2, 'Stein', NULL, 'Bergkamp', '2011-02-02', 'Klant', b'0', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (11, 2, 'Judith', NULL, 'Bergkamp', '2026-02-05', 'Klant', b'0', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (12, 3, 'Mazin', 'van', 'Vliet', '1968-08-18', 'Klant', b'0', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (13, 3, 'Selma', 'van de', 'Heuvel', '1965-09-04', 'Klant', b'1', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (14, 4, 'Eva', NULL, 'Scherder', '2000-04-07', 'Klant', b'1', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (15, 4, 'Felicia', NULL, 'Scherder', '2025-11-29', 'Klant', b'0', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (16, 4, 'Devin', NULL, 'Scherder', '2026-03-01', 'Klant', b'0', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (17, 5, 'Frieda', 'de', 'Jong', '1980-09-04', 'Klant', b'1', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (18, 5, 'Simeon', 'de', 'Jong', '2018-05-23', 'Klant', b'0', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (19, 6, 'Hanna', 'van der', 'Berg', '1999-09-09', 'Klant', b'1', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000');

INSERT INTO Gebruiker (Id, PersoonId, InlogNaam, Gebruikersnaam, Wachtwoord, IsIngelogd, Ingelogd, Uitgelogd, IsActief, Opmerking, DatumAangemaakt, DatumGewijzigd)
VALUES
    (1, 1, 'Hans', 'hans@maaskantje.nl', '$2y$10$296RMzqzZqWENu9vyh6axed0DkfsuYkbvoI/AXVowCp/DL6zKiF0i', b'1', '2026-04-10 09:03:06', NULL, b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (2, 2, 'Jan', 'jan@maaskantje.nl', '$2y$10$296RMzqzZqWENu9vyh6axed0DkfsuYkbvoI/AXVowCp/DL3zKiF6i', b'0', '2026-04-9 15:13:23', '2026-04-9 15:23:46', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (3, 3, 'Herman', 'herman@maaskantje.nl', '$2y$10$296RMzqzZqWENu9vyh6axed0DkfsuYkbvoI/AXVuwCp/DL9zKiF2i', b'1', '2026-04-8 12:05:20', NULL, b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000');

INSERT INTO Allergie (Id, Naam, Omschrijving, AnafylactischRisico, IsActief, Opmerking, DatumAangemaakt, DatumGewijzigd)
VALUES
    (1, 'Gluten', 'Allergisch voor gluten', 'zeerlaag', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (2, 'Pindas', 'Allergisch voor pindas', 'Hoog', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (3, 'Schaaldieren', 'Allergisch voor schaaldieren', 'RedelijkHoog', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (4, 'Hazelnoten', 'Allergisch voor hazelnoten', 'laag', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (5, 'Lactose', 'Allergisch voor lactose', 'Zeerlaag', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (6, 'Soja', 'Allergisch voor soja', 'Zeerlaag', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000');

INSERT INTO Product (Id, CategorieId, Naam, SoortAllergie, Barcode, Houdbaarheidsdatum, Omschrijving, Status, IsActief, Opmerking, DatumAangemaakt, DatumGewijzigd)
VALUES
    (1, 1, 'Aardappel', NULL, '8719587321239', '2026-05-12', 'Kruimige aardappel', 'OpVoorraad', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (2, 1, 'Aardappel', NULL, '8719587321239', '2026-05-26', 'Kruimige aardappel', 'OpVoorraad', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (3, 1, 'Ui', NULL, '8719437321335', '2026-05-02', 'Gele ui', 'NietOpVoorraad', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (4, 1, 'Appel', NULL, '8719486321332', '2026-05-16', 'Granny Smith', 'NietLeverbaar', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (5, 1, 'Appel', NULL, '8719486321332', '2026-05-23', 'Granny Smith', 'NietLeverbaar', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (6, 1, 'Banaan', 'Banaan', '8719484321336', '2026-05-12', 'Biologische Banaan', 'OverHoudbaarheidsDatum', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (7, 1, 'Banaan', 'Banaan', '8719484321336', '2026-05-19', 'Biologische Banaan', 'OverHoudbaarheidsDatum', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (8, 2, 'Kaas', 'Lactose', '8719487421338', '2026-05-19', 'Jonge Kaas', 'OpVoorraad', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (9, 2, 'Rosbief', NULL, '8719487421331', '2026-05-23', 'Rundvlees', 'OpVoorraad', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (10, 3, 'Melk', 'Lactose', '8719447321332', '2026-05-23', 'Halfvolle melk', 'OpVoorraad', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (11, 3, 'Margarine', NULL, '8719486321336', '2026-05-02', 'Plantaardige boter', 'OpVoorraad', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (12, 3, 'Ei', 'Eier', '8719487421334', '2026-05-04', 'Scharrelei', 'OpVoorraad', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (13, 4, 'Brood', 'Gluten', '8719487721337', '2026-05-07', 'Volkoren brood', 'OpVoorraad', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (14, 4, 'Gevulde Koek', 'Amandel', '8719483321333', '2026-05-04', 'Banketbakkers kwaliteit', 'OpVoorraad', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (15, 5, 'Fristi', 'Lactose', '8719487121331', '2026-05-28', 'Frisdrank', 'NietOpVoorraad', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (16, 5, 'Appelsap', NULL, '8719487521335', '2026-05-19', '100% vruchtensap', 'OpVoorraad', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (17, 5, 'Koffie', 'Caffeïne', '8719487381338', '2026-05-23', 'Arabica koffie', 'OverHoudbaarheidsDatum', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (18, 5, 'Thee', 'Theïne', '8719487329339', '2026-05-02', 'Ceylon thee', 'OpVoorraad', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (19, 6, 'Pasta', 'Gluten', '8719487321334', '2026-05-16', 'Macaroni', 'NietLeverbaar', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (20, 6, 'Rijst', NULL, '8719487331332', '2026-05-25', 'Basmati Rijst', 'OpVoorraad', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (21, 6, 'Knorr Nasi Mix', NULL, '871948735135', '2026-05-13', 'Nasi kruiden', 'OpVoorraad', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (22, 7, 'Tomatensoep', NULL, '8719487371337', '2026-05-23', 'Romige tomatensoep', 'OpVoorraad', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (23, 7, 'Tomatensaus', NULL, '8719487341334', '2026-05-21', 'Pizza saus', 'NietOpVoorraad', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (24, 7, 'Peterselie', NULL, '8719487321636', '2026-05-31', 'Verse kruidenpot', 'OpVoorraaad', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (25, 8, 'Olie', NULL, '8719487327337', '2026-05-27', 'Olijfolie', 'OpVoorraad', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (26, 8, 'Mars', NULL, '8719487324334', '2026-05-11', 'Snoep', 'OpVoorraad', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (27, 8, 'Biscuit', NULL, '8719487311331', '2026-05-07', 'San Francisco biscuit', 'OpVoorraad', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (28, 8, 'Paprika Chips', NULL, '87194873218398', '2026-05-22', 'Ribbelchips paprika', 'OpVoorraad', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (29, 8, 'Chocolade reep', 'Cacoa', '8719487321533', '2026-05-21', 'Tony Chocolonely', 'OpVoorraad', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000');

INSERT INTO Voedselpakket (Id, GezinId, PakketNummer, DatumSamenstelling, DatumUitgifte, Status, IsActief, Opmerking, DatumAangemaakt, DatumGewijzigd)
VALUES
    (1, 1, 1, '2026-03-21', '2026-03-21', 'Uitgereikt', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (2, 1, 2, '2026-03-19', NULL, 'NietUitgereikt', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (3, 1, 3, '2026-03-17', NULL, 'NietMeerIngeschreven', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (4, 2, 4, '2026-03-10', '2026-03-14', 'Uitgereikt', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (5, 2, 5, '2026-03-18', '2026-03-20', 'Uitgereikt', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (6, 2, 6, '2026-04-08', NULL, 'NietUitgereikt', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000');

INSERT INTO AllergiePerPersoon (Id, PersoonId, AllergieId, IsActief, Opmerking, DatumAangemaakt, DatumGewijzigd)
VALUES
    (1, 4, 1, b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (2, 5, 2, b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (3, 6, 3, b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (4, 7, 4, b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (5, 8, 3, b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (6, 9, 2, b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (7, 10, 5, b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (8, 12, 2, b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (9, 13, 4, b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (10, 14, 1, b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (11, 15, 3, b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (12, 16, 5, b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (13, 17, 1, b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (14, 17, 2, b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (15, 18, 4, b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (16, 19, 4, b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000');

INSERT INTO RolPerGebruiker (Id, GebruikerId, RolId, IsActief, Opmerking, DatumAangemaakt, DatumGewijzigd)
VALUES
    (1, 1, 1, b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (2, 2, 2, b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (3, 3, 3, b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000');

INSERT INTO EetwensPerGezin (Id, GezinId, EetwensId, IsActief, Opmerking, DatumAangemaakt, DatumGewijzigd)
VALUES
    (1, 1, 2, b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (2, 2, 4, b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (3, 3, 4, b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (4, 4, 3, b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (5, 5, 2, b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000');

INSERT INTO ContactPerLeverancier (Id, LeverancierId, ContactId, IsActief, Opmerking, DatumAangemaakt, DatumGewijzigd)
VALUES
    (1, 1, 7, b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (2, 2, 8, b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (3, 3, 9, b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (4, 4, 10, b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (5, 6, 11, b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (6, 7, 12, b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (7, 8, 13, b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000');

INSERT INTO ContactPerGezin (Id, GezinId, ContactId, IsActief, Opmerking, DatumAangemaakt, DatumGewijzigd)
VALUES
    (1, 1, 1, b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (2, 2, 2, b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (3, 3, 3, b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (4, 4, 4, b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (5, 5, 5, b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (6, 6, 6, b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000');

INSERT INTO ProductPerVoedselpakket (Id, VoedselpakketId, ProductId, AantalProductEenheden, IsActief, Opmerking, DatumAangemaakt, DatumGewijzigd)
VALUES
    (1, 1, 7, 1, b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (2, 1, 8, 2, b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (3, 1, 9, 1, b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (4, 2, 12, 1, b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (5, 2, 13, 2, b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (6, 2, 14, 1, b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (7, 3, 3, 1, b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (8, 3, 4, 1, b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (9, 4, 20, 1, b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (10, 4, 19, 1, b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (11, 4, 21, 1, b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (12, 5, 24, 1, b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (13, 5, 25, 1, b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (14, 5, 26, 1, b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (15, 6, 27, 1, b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000');

INSERT INTO ProductPerLeverancier (Id, LeverancierId, ProductId, DatumAangeleverd, DatumEerstVolgendeLevering, IsActief, Opmerking, DatumAangemaakt, DatumGewijzigd)
VALUES
    (1, 4, 1, '2026-03-12', '2026-05-15', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (2, 4, 2, '2026-04-02', '2026-05-05', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (3, 2, 3, '2026-03-16', '2026-05-18', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (4, 1, 4, '2026-04-08', '2026-05-11', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (5, 4, 5, '2026-04-06', '2026-05-10', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (6, 1, 6, '2026-03-12', '2026-05-15', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (7, 4, 7, '2026-03-20', '2026-05-21', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (8, 4, 8, '2026-04-02', '2026-05-08', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (9, 4, 9, '2026-04-04', '2026-05-09', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (10, 3, 10, '2026-04-07', '2026-05-11', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (11, 3, 11, '2026-04-01', '2026-05-06', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (12, 3, 12, '2026-03-18', '2026-05-20', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (13, 3, 13, '2026-03-19', '2026-05-20', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (14, 2, 14, '2026-04-10', '2026-05-12', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (15, 2, 15, '2026-03-13', '2026-05-15', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (16, 1, 16, '2026-03-18', '2026-05-21', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (17, 1, 17, '2026-03-11', '2026-05-15', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (18, 1, 18, '2026-04-02', '2026-05-06', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (19, 1, 19, '2026-04-09', '2026-05-12', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (20, 4, 20, '2026-04-03', '2026-05-06', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (21, 2, 21, '2026-04-02', '2026-05-08', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (22, 1, 22, '2026-03-16', '2026-05-19', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (23, 3, 23, '2026-03-14', '2026-05-18', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (24, 3, 24, '2026-04-07', '2026-05-15', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (25, 1, 25, '2026-03-17', '2026-05-21', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (26, 2, 26, '2026-04-05', '2026-05-12', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (27, 1, 27, '2026-04-07', '2026-05-10', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (28, 2, 28, '2026-04-06', '2026-05-09', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (29, 3, 29, '2026-04-08', '2026-05-11', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000');

INSERT INTO ProductPerMagazijn (Id, ProductId, MagazijnId, Locatie, IsActief, Opmerking, DatumAangemaakt, DatumGewijzigd)
VALUES
    (1, 1, 1, 'Berlicum', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (2, 2, 2, 'Rosmalen', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (3, 3, 3, 'Berlicum', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (4, 4, 4, 'Berlicum', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (5, 5, 5, 'Rosmalen', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (6, 6, 6, 'Berlicum', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (7, 7, 7, 'Rosmalen', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (8, 8, 8, 'Sint-MichelsGestel', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (9, 9, 9, 'Sint-MichelsGestel', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (10, 10, 10, 'Middelrode', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (11, 11, 11, 'Middelrode', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (12, 12, 12, 'Middelrode', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (13, 13, 13, 'Schijndel', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (14, 14, 14, 'Schijndel', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (15, 15, 15, 'Gemonde', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (16, 16, 16, 'Gemonde', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (17, 17, 17, 'Gemonde', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (18, 18, 18, 'Gemonde', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (19, 19, 19, 'Den Bosch', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (20, 20, 20, 'Den Bosch', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (21, 21, 21, 'Den Bosch', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (22, 22, 22, 'Heeswijk Dinther', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (23, 23, 23, 'Heeswijk Dinther', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (24, 24, 24, 'Heeswijk Dinther', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (25, 25, 25, 'Vught', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (26, 26, 26, 'Vught', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (27, 27, 27, 'Vught', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (28, 28, 28, 'Vught', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000'),
    (29, 29, 29, 'Vught', b'1', NULL, '2026-04-10 09:03:06.000000', '2026-04-10 09:03:06.000000');