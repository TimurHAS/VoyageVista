-- =============================================================
--  VoyageVista — Correctifs données
--  Importer dans phpMyAdmin : base voyagevista → Importer → fix_data.sql
-- =============================================================

USE voyagevista;

-- -------------------------------------------------------------
-- TRANSPORTS : ajout des colonnes minutes (si pas déjà présentes)
-- -------------------------------------------------------------
ALTER TABLE transports
    ADD COLUMN IF NOT EXISTS depart_minute  TINYINT UNSIGNED NOT NULL DEFAULT 0,
    ADD COLUMN IF NOT EXISTS arrival_minute TINYINT UNSIGNED NOT NULL DEFAULT 0;

-- -------------------------------------------------------------
-- TRANSPORTS : correction des heures ET minutes d'arrivée
--   Calcul : depart + duration_minutes = arrival (modulo 1440 pour rester sur 24h)
--
--   id=1  Paris CDG → Denpasar    11:00 + 17h45 (1065min) = 04:45 j+1
--   id=2  Paris Orly → Lisbonne   08:00 + 2h30  (150min)  = 10:30
--   id=3  Bordeaux → Lisbonne     22:00 + 15h20 (920min)  = 13:20 j+1
--   id=4  Paris → Chamonix        07:00 + 6h10  (370min)  = 13:10
--   id=5  Paris Orly → Marrakech  09:00 + 3h20  (200min)  = 12:20
--   id=6  Athènes → Santorin      07:00 + 7h30  (450min)  = 14:30
--   id=7  Paris CDG → Santorin    14:00 + 3h25  (205min)  = 17:25
--   id=8  Paris CDG → Osaka       18:00 + 13h15 (795min)  = 07:15 j+1
--   id=9  Genève → Chamonix       10:00 + 2h10  (130min)  = 12:10
--   id=10 Aéroport → Médina       12:00 + 0h35  (35min)   = 12:35
--   id=11 Paris CDG → Denpasar    15:00 + 18h30 (1110min) = 09:30 j+1
-- -------------------------------------------------------------

UPDATE transports SET depart_hour=11, depart_minute=0,  return_hour=4,  arrival_minute=45 WHERE id=1;
UPDATE transports SET depart_hour=8,  depart_minute=0,  return_hour=10, arrival_minute=30 WHERE id=2;
UPDATE transports SET depart_hour=22, depart_minute=0,  return_hour=13, arrival_minute=20 WHERE id=3;
UPDATE transports SET depart_hour=7,  depart_minute=0,  return_hour=13, arrival_minute=10 WHERE id=4;
UPDATE transports SET depart_hour=9,  depart_minute=0,  return_hour=12, arrival_minute=20 WHERE id=5;
UPDATE transports SET depart_hour=7,  depart_minute=0,  return_hour=14, arrival_minute=30 WHERE id=6;
UPDATE transports SET depart_hour=14, depart_minute=0,  return_hour=17, arrival_minute=25 WHERE id=7;
UPDATE transports SET depart_hour=18, depart_minute=0,  return_hour=7,  arrival_minute=15 WHERE id=8;
UPDATE transports SET depart_hour=10, depart_minute=0,  return_hour=12, arrival_minute=10 WHERE id=9;
UPDATE transports SET depart_hour=12, depart_minute=0,  return_hour=12, arrival_minute=35 WHERE id=10;
UPDATE transports SET depart_hour=15, depart_minute=0,  return_hour=9,  arrival_minute=30 WHERE id=11;

-- -------------------------------------------------------------
-- DESTINATIONS : correction des images
-- -------------------------------------------------------------

-- Marrakech : médina / place Jemaa el-Fna
UPDATE destinations
SET image = 'https://images.unsplash.com/photo-1489493512598-d08130f49bea?auto=format&fit=crop&w=1200&q=80'
WHERE id = 4;

-- Chamonix : massif du Mont Blanc
UPDATE destinations
SET image = 'https://images.unsplash.com/photo-1506905925346-21bda4d32df4?auto=format&fit=crop&w=1200&q=80'
WHERE id = 3;

-- -------------------------------------------------------------
-- ACTIVITÉS : correction de l'image (Excursion désert Agafay)
-- -------------------------------------------------------------
UPDATE activities
SET image = 'https://images.unsplash.com/photo-1548019979-4bd7cf4fa0ac?auto=format&fit=crop&w=1200&q=80'
WHERE id = 4;
