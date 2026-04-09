-- ==========================================
-- VOEDSELBANK MAASKANTJE - CORRECTE MYSQL SCRIPT
-- Past bij de huidige Laravel app (3 rollen + Laravel systeemtabellen)
-- ==========================================

CREATE DATABASE IF NOT EXISTS voedselbank_maaskantje
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE voedselbank_maaskantje;

-- ------------------------------------------
-- 1) Laravel Core Tables
-- ------------------------------------------

CREATE TABLE IF NOT EXISTS users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    role VARCHAR(50) NOT NULL DEFAULT 'vrijwilliger',
    email_verified_at TIMESTAMP NULL,
    password VARCHAR(255) NOT NULL,
    remember_token VARCHAR(100) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS password_reset_tokens (
    email VARCHAR(255) PRIMARY KEY,
    token VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS sessions (
    id VARCHAR(255) PRIMARY KEY,
    user_id BIGINT UNSIGNED NULL,
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    payload LONGTEXT NOT NULL,
    last_activity INT NOT NULL,
    INDEX sessions_user_id_index (user_id),
    INDEX sessions_last_activity_index (last_activity)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS cache (
    `key` VARCHAR(255) PRIMARY KEY,
    `value` MEDIUMTEXT NOT NULL,
    expiration BIGINT NOT NULL,
    INDEX cache_expiration_index (expiration)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS cache_locks (
    `key` VARCHAR(255) PRIMARY KEY,
    `owner` VARCHAR(255) NOT NULL,
    expiration BIGINT NOT NULL,
    INDEX cache_locks_expiration_index (expiration)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS jobs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    queue VARCHAR(255) NOT NULL,
    payload LONGTEXT NOT NULL,
    attempts TINYINT UNSIGNED NOT NULL,
    reserved_at INT UNSIGNED NULL,
    available_at INT UNSIGNED NOT NULL,
    created_at INT UNSIGNED NOT NULL,
    INDEX jobs_queue_index (queue)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS job_batches (
    id VARCHAR(255) PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    total_jobs INT NOT NULL,
    pending_jobs INT NOT NULL,
    failed_jobs INT NOT NULL,
    failed_job_ids LONGTEXT NOT NULL,
    options MEDIUMTEXT NULL,
    cancelled_at INT NULL,
    created_at INT NOT NULL,
    finished_at INT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS failed_jobs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    uuid VARCHAR(255) NOT NULL UNIQUE,
    connection TEXT NOT NULL,
    queue TEXT NOT NULL,
    payload LONGTEXT NOT NULL,
    exception LONGTEXT NOT NULL,
    failed_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS user_profiles (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL UNIQUE,
    telefoon VARCHAR(30) NULL,
    adres VARCHAR(255) NULL,
    afdeling VARCHAR(120) NULL,
    beschikbaarheid VARCHAR(120) NULL,
    verantwoordelijkheden VARCHAR(120) NULL,
    bio TEXT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    CONSTRAINT fk_user_profiles_user
        FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------
-- 2) Business Tables
-- ------------------------------------------

CREATE TABLE IF NOT EXISTS categories (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    naam VARCHAR(100) NOT NULL UNIQUE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS leveranciers (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    bedrijfsnaam VARCHAR(150) NOT NULL,
    adres VARCHAR(255) NOT NULL,
    contactpersoon_naam VARCHAR(100) NOT NULL,
    contactpersoon_email VARCHAR(150) NOT NULL,
    telefoonnummer VARCHAR(20) NOT NULL,
    volgende_levering DATETIME NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS products (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    productnaam VARCHAR(150) NOT NULL UNIQUE,
    ean_nummer VARCHAR(13) NOT NULL UNIQUE,
    aantal_in_voorraad INT NOT NULL DEFAULT 0,
    categorie_id BIGINT UNSIGNED NOT NULL,
    leverancier_id BIGINT UNSIGNED NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    CONSTRAINT fk_products_categorie
        FOREIGN KEY (categorie_id) REFERENCES categories(id)
        ON DELETE RESTRICT,
    CONSTRAINT fk_products_leverancier
        FOREIGN KEY (leverancier_id) REFERENCES leveranciers(id)
        ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS klanten (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    gezinsnaam VARCHAR(100) NOT NULL,
    adres VARCHAR(255) NOT NULL,
    telefoonnummer VARCHAR(20) NOT NULL,
    emailadres VARCHAR(150) NULL,
    allergieen TEXT NULL,
    aantal_volwassenen INT NOT NULL DEFAULT 0,
    aantal_kinderen INT NOT NULL DEFAULT 0,
    aantal_babys INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS wens_allergies (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    beschrijving VARCHAR(100) NOT NULL UNIQUE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS klant_wens (
    klant_id BIGINT UNSIGNED NOT NULL,
    wens_id BIGINT UNSIGNED NOT NULL,
    PRIMARY KEY (klant_id, wens_id),
    CONSTRAINT fk_klant_wens_klant
        FOREIGN KEY (klant_id) REFERENCES klanten(id)
        ON DELETE CASCADE,
    CONSTRAINT fk_klant_wens_wens
        FOREIGN KEY (wens_id) REFERENCES wens_allergies(id)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS voedselpakketten (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    datum_samenstelling DATE NOT NULL,
    datum_uitgifte DATE NULL,
    klant_id BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    CONSTRAINT fk_voedselpakketten_klant
        FOREIGN KEY (klant_id) REFERENCES klanten(id)
        ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS pakket_product (
    pakket_id BIGINT UNSIGNED NOT NULL,
    product_id BIGINT UNSIGNED NOT NULL,
    aantal INT NOT NULL,
    PRIMARY KEY (pakket_id, product_id),
    CONSTRAINT fk_pakket_product_pakket
        FOREIGN KEY (pakket_id) REFERENCES voedselpakketten(id)
        ON DELETE CASCADE,
    CONSTRAINT fk_pakket_product_product
        FOREIGN KEY (product_id) REFERENCES products(id)
        ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------
-- 3) Demo data
-- ------------------------------------------
-- Wachtwoord voor alle demo-users: Wachtwoord123!

INSERT INTO users (name, email, password, role, email_verified_at, created_at, updated_at)
VALUES
    ('Directie Demo', 'directie@voedselbank.local', '$2y$10$fRo07QS6W1uZQPMr670tPe4QeMQuIlVlTXrUnWYueTwQ/WcBbZIoe', 'directie', NOW(), NOW(), NOW()),
    ('Magazijn Demo', 'magazijn@voedselbank.local', '$2y$10$fRo07QS6W1uZQPMr670tPe4QeMQuIlVlTXrUnWYueTwQ/WcBbZIoe', 'magazijn_medewerker', NOW(), NOW(), NOW()),
    ('Vrijwilliger Demo', 'vrijwilliger@voedselbank.local', '$2y$10$fRo07QS6W1uZQPMr670tPe4QeMQuIlVlTXrUnWYueTwQ/WcBbZIoe', 'vrijwilliger', NOW(), NOW(), NOW())
ON DUPLICATE KEY UPDATE
    name = VALUES(name),
    password = VALUES(password),
    role = VALUES(role),
    updated_at = NOW();

INSERT INTO user_profiles (user_id, telefoon, adres, afdeling, beschikbaarheid, verantwoordelijkheden, bio, created_at, updated_at)
VALUES
    ((SELECT id FROM users WHERE email = 'directie@voedselbank.local' LIMIT 1), '06-12345678', 'Maaskantje 1', 'Bestuur', 'Maandag en woensdag', 'Operationeel beleid en planning', 'Demo account voor de applicatie.', NOW(), NOW()),
    ((SELECT id FROM users WHERE email = 'magazijn@voedselbank.local' LIMIT 1), '06-12345678', 'Maaskantje 1', 'Magazijn A', 'Maandag en woensdag', NULL, 'Demo account voor de applicatie.', NOW(), NOW()),
    ((SELECT id FROM users WHERE email = 'vrijwilliger@voedselbank.local' LIMIT 1), '06-12345678', 'Maaskantje 1', NULL, 'Maandag en woensdag', NULL, 'Demo account voor de applicatie.', NOW(), NOW())
ON DUPLICATE KEY UPDATE
    telefoon = VALUES(telefoon),
    adres = VALUES(adres),
    afdeling = VALUES(afdeling),
    beschikbaarheid = VALUES(beschikbaarheid),
    verantwoordelijkheden = VALUES(verantwoordelijkheden),
    bio = VALUES(bio),
    updated_at = NOW();

INSERT INTO categories (naam, created_at, updated_at)
VALUES
    ('Aardappelen, groente, fruit', NOW(), NOW()),
    ('Kaas, vleeswaren', NOW(), NOW()),
    ('Zuivel, plantaardig en eieren', NOW(), NOW()),
    ('Bakkerij en banket', NOW(), NOW()),
    ('Frisdrank, sappen, koffie en thee', NOW(), NOW()),
    ('Pasta, rijst en wereldkeuken', NOW(), NOW()),
    ('Soepen, sauzen, kruiden en olie', NOW(), NOW()),
    ('Snoep, koek, chips en chocolade', NOW(), NOW()),
    ('Baby, verzorging en hygiëne', NOW(), NOW())
ON DUPLICATE KEY UPDATE
    updated_at = NOW();

INSERT INTO wens_allergies (beschrijving, created_at, updated_at)
VALUES
    ('Geen varkensvlees', NOW(), NOW()),
    ('Gluten', NOW(), NOW()),
    ('Pinda''s', NOW(), NOW()),
    ('Schaaldieren', NOW(), NOW()),
    ('Hazelnoten', NOW(), NOW()),
    ('Lactose', NOW(), NOW()),
    ('Veganistisch', NOW(), NOW()),
    ('Vegetarisch', NOW(), NOW())
ON DUPLICATE KEY UPDATE
    updated_at = NOW();

INSERT INTO klanten (gezinsnaam, adres, telefoonnummer, emailadres, allergieen, aantal_volwassenen, aantal_kinderen, aantal_babys, created_at, updated_at)
SELECT 'Familie Jansen', 'Dorpsstraat 12, Maaskantje', '06-11111111', 'jansen@voorbeeld.nl', 'Geen varkensvlees', 2, 2, 0, NOW(), NOW()
WHERE NOT EXISTS (
    SELECT 1 FROM klanten WHERE gezinsnaam = 'Familie Jansen' AND telefoonnummer = '06-11111111'
);

INSERT INTO klanten (gezinsnaam, adres, telefoonnummer, emailadres, allergieen, aantal_volwassenen, aantal_kinderen, aantal_babys, created_at, updated_at)
SELECT 'Familie De Vries', 'Molenweg 8, Maaskantje', '06-22222222', 'devries@voorbeeld.nl', 'Lactose', 1, 3, 1, NOW(), NOW()
WHERE NOT EXISTS (
    SELECT 1 FROM klanten WHERE gezinsnaam = 'Familie De Vries' AND telefoonnummer = '06-22222222'
);

INSERT INTO klanten (gezinsnaam, adres, telefoonnummer, emailadres, allergieen, aantal_volwassenen, aantal_kinderen, aantal_babys, created_at, updated_at)
SELECT 'Familie Bakker', 'Stationslaan 21, Maaskantje', '06-33333333', 'bakker@voorbeeld.nl', 'Gluten', 2, 1, 0, NOW(), NOW()
WHERE NOT EXISTS (
    SELECT 1 FROM klanten WHERE gezinsnaam = 'Familie Bakker' AND telefoonnummer = '06-33333333'
);

INSERT INTO klanten (gezinsnaam, adres, telefoonnummer, emailadres, allergieen, aantal_volwassenen, aantal_kinderen, aantal_babys, created_at, updated_at)
SELECT 'Familie El Idrissi', 'Waterweg 4, Maaskantje', '06-44444444', 'elidrissi@voorbeeld.nl', 'Vegetarisch', 2, 2, 1, NOW(), NOW()
WHERE NOT EXISTS (
    SELECT 1 FROM klanten WHERE gezinsnaam = 'Familie El Idrissi' AND telefoonnummer = '06-44444444'
);

INSERT INTO klanten (gezinsnaam, adres, telefoonnummer, emailadres, allergieen, aantal_volwassenen, aantal_kinderen, aantal_babys, created_at, updated_at)
SELECT 'Familie Van Dijk', 'Kerkstraat 2, Maaskantje', '06-55555555', 'vandijk@voorbeeld.nl', 'Pinda''s', 1, 1, 0, NOW(), NOW()
WHERE NOT EXISTS (
    SELECT 1 FROM klanten WHERE gezinsnaam = 'Familie Van Dijk' AND telefoonnummer = '06-55555555'
);

INSERT INTO klant_wens (klant_id, wens_id)
SELECT k.id, w.id
FROM klanten AS k
JOIN wens_allergies AS w ON w.beschrijving = 'Geen varkensvlees'
WHERE k.gezinsnaam = 'Familie Jansen'
    AND NOT EXISTS (
        SELECT 1 FROM klant_wens kw WHERE kw.klant_id = k.id AND kw.wens_id = w.id
    );

INSERT INTO klant_wens (klant_id, wens_id)
SELECT k.id, w.id
FROM klanten AS k
JOIN wens_allergies AS w ON w.beschrijving = 'Lactose'
WHERE k.gezinsnaam = 'Familie De Vries'
    AND NOT EXISTS (
        SELECT 1 FROM klant_wens kw WHERE kw.klant_id = k.id AND kw.wens_id = w.id
    );

INSERT INTO klant_wens (klant_id, wens_id)
SELECT k.id, w.id
FROM klanten AS k
JOIN wens_allergies AS w ON w.beschrijving = 'Gluten'
WHERE k.gezinsnaam = 'Familie Bakker'
    AND NOT EXISTS (
        SELECT 1 FROM klant_wens kw WHERE kw.klant_id = k.id AND kw.wens_id = w.id
    );

INSERT INTO klant_wens (klant_id, wens_id)
SELECT k.id, w.id
FROM klanten AS k
JOIN wens_allergies AS w ON w.beschrijving = 'Vegetarisch'
WHERE k.gezinsnaam = 'Familie El Idrissi'
    AND NOT EXISTS (
        SELECT 1 FROM klant_wens kw WHERE kw.klant_id = k.id AND kw.wens_id = w.id
    );

INSERT INTO klant_wens (klant_id, wens_id)
SELECT k.id, w.id
FROM klanten AS k
JOIN wens_allergies AS w ON w.beschrijving = 'Pinda''s'
WHERE k.gezinsnaam = 'Familie Van Dijk'
    AND NOT EXISTS (
        SELECT 1 FROM klant_wens kw WHERE kw.klant_id = k.id AND kw.wens_id = w.id
    );

INSERT INTO products (productnaam, ean_nummer, aantal_in_voorraad, categorie_id, leverancier_id, created_at, updated_at)
VALUES
    ('Appels Elstar (1kg)', '8710400000001', 50, (SELECT id FROM categories WHERE naam = 'Aardappelen, groente, fruit' LIMIT 1), NULL, NOW(), NOW()),
    ('Jong Belegen Kaas (400g)', '8710400000002', 20, (SELECT id FROM categories WHERE naam = 'Kaas, vleeswaren' LIMIT 1), NULL, NOW(), NOW()),
    ('Halfvolle Melk (1L)', '8710400000003', 100, (SELECT id FROM categories WHERE naam = 'Zuivel, plantaardig en eieren' LIMIT 1), NULL, NOW(), NOW())
ON DUPLICATE KEY UPDATE
    productnaam = VALUES(productnaam),
    aantal_in_voorraad = VALUES(aantal_in_voorraad),
    categorie_id = VALUES(categorie_id),
    leverancier_id = VALUES(leverancier_id),
    updated_at = NOW();
