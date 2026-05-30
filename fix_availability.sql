-- =============================================================
--  VoyageVista — Disponibilités hôtels + places activités
--  Importer dans phpMyAdmin : base voyagevista → Importer
-- =============================================================

USE voyagevista;

-- -------------------------------------------------------------
-- TABLE : hotel_bookings
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS hotel_bookings (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    hotel_id   INT UNSIGNED NOT NULL,
    user_id    INT UNSIGNED DEFAULT NULL,
    checkin    DATE NOT NULL,
    checkout   DATE NOT NULL,
    rooms_used TINYINT UNSIGNED NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (hotel_id) REFERENCES hotels(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- -------------------------------------------------------------
-- TABLE : activity_bookings
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS activity_bookings (
    id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    activity_id  INT UNSIGNED NOT NULL,
    user_id      INT UNSIGNED DEFAULT NULL,
    booking_date DATE NOT NULL,
    places       TINYINT UNSIGNED NOT NULL DEFAULT 1,
    created_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (activity_id) REFERENCES activities(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- -------------------------------------------------------------
-- ACTIVITÉS : ajout du nombre de places max par activité
-- -------------------------------------------------------------
ALTER TABLE activities ADD COLUMN IF NOT EXISTS max_places TINYINT UNSIGNED NOT NULL DEFAULT 20;

UPDATE activities SET max_places = 15 WHERE id = 1;  -- Balade rizières
UPDATE activities SET max_places = 20 WHERE id = 2;  -- Tour miradouros
UPDATE activities SET max_places = 10 WHERE id = 3;  -- Randonnée Mont Blanc
UPDATE activities SET max_places = 12 WHERE id = 4;  -- Désert Agafay
UPDATE activities SET max_places = 25 WHERE id = 5;  -- Croisière Santorin
UPDATE activities SET max_places = 18 WHERE id = 6;  -- Temples Kyoto
UPDATE activities SET max_places = 12 WHERE id = 7;  -- Snorkeling Bali
UPDATE activities SET max_places = 20 WHERE id = 8;  -- Temple Tanah Lot
