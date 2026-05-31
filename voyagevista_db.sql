-- =============================================================
--  VoyageVista — Base de données complète
--  À importer dans phpMyAdmin ou via : mysql -u root -p < voyagevista_db.sql
-- =============================================================

CREATE DATABASE IF NOT EXISTS voyagevista CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE voyagevista;

-- -------------------------------------------------------------
-- TABLE : destinations
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS destinations (
    id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name            VARCHAR(100)  NOT NULL,
    country         VARCHAR(100)  NOT NULL,
    category        VARCHAR(50)   NOT NULL,
    stay_type       VARCHAR(50)   NOT NULL,
    duration        VARCHAR(30)   NOT NULL,
    duration_days   TINYINT UNSIGNED NOT NULL DEFAULT 1,
    price           DECIMAL(10,2) NOT NULL,
    old_price       DECIMAL(10,2) DEFAULT NULL,
    discount        VARCHAR(10)   DEFAULT NULL,
    pack            VARCHAR(100)  DEFAULT NULL,
    rating          DECIMAL(3,1)  NOT NULL DEFAULT 0.0,
    reviews         INT UNSIGNED  NOT NULL DEFAULT 0,
    recommendation  TINYINT UNSIGNED NOT NULL DEFAULT 0,
    image           TEXT          NOT NULL,
    description     TEXT          DEFAULT NULL,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT INTO destinations (id, name, country, category, stay_type, duration, duration_days, price, old_price, discount, pack, rating, reviews, recommendation, image, description) VALUES
(1,'Bali','Indonésie','Plage','Relaxation','10 jours',10,890.00,1120.00,'-21%','Évasion plage',4.8,312,98,'https://images.unsplash.com/photo-1537996194471-e657df975ab4?auto=format&fit=crop&w=1200&q=80','Rizières, plages, temples et villages tropicaux pour un séjour entre repos et découverte.'),
(2,'Lisbonne','Portugal','Ville','City break','4 jours',4,390.00,470.00,'-17%','City break',4.6,204,86,'https://images.unsplash.com/photo-1548707309-dcebeab9ea9b?auto=format&fit=crop&w=1200&q=80','Quartiers historiques, tramways, belvédères et escapade urbaine au bord de l''Atlantique.'),
(3,'Chamonix','France','Montagne','Aventure','6 jours',6,620.00,760.00,'-18%','Nature active',4.7,184,91,'https://images.unsplash.com/photo-1500534314209-a25ddb2bd429?auto=format&fit=crop&w=1200&q=80','Panoramas alpins, randonnées, train de montagne et hébergements proches des pistes.'),
(4,'Marrakech','Maroc','Culture','Culture','5 jours',5,480.00,590.00,'-19%','Culture et soleil',4.5,228,84,'https://images.unsplash.com/photo-1518548419970-58e3b4079ab2?auto=format&fit=crop&w=1200&q=80','Souks, palais, jardins, riads et excursion dans le désert d''Agafay.'),
(5,'Santorin','Grèce','Plage','Romantique','7 jours',7,760.00,940.00,'-19%','Mer romantique',4.7,396,94,'https://images.unsplash.com/photo-1570077188670-e3a8d69ac5ff?auto=format&fit=crop&w=1200&q=80','Villages blancs, mer Égée, ferry et couchers de soleil pour un voyage lumineux.'),
(6,'Kyoto','Japon','Culture','Culture','8 jours',8,1180.00,1390.00,'-15%','Culture premium',4.9,538,96,'https://images.unsplash.com/photo-1493976040374-85c8e12f0c0e?auto=format&fit=crop&w=1200&q=80','Temples, jardins, ruelles traditionnelles et immersion dans la culture japonaise.');

-- -------------------------------------------------------------
-- TABLE : transports
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS transports (
    id               INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    destination_id   INT UNSIGNED NOT NULL,
    mode             VARCHAR(30)  NOT NULL,
    company          VARCHAR(100) NOT NULL,
    city_from        VARCHAR(100) NOT NULL,
    city_to          VARCHAR(100) NOT NULL,
    duration         VARCHAR(20)  NOT NULL,
    duration_minutes SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    stops            TINYINT UNSIGNED NOT NULL DEFAULT 0,
    depart_hour      TINYINT UNSIGNED NOT NULL DEFAULT 8,
    return_hour      TINYINT UNSIGNED NOT NULL DEFAULT 18,
    price            DECIMAL(10,2) NOT NULL,
    rating           DECIMAL(3,1)  NOT NULL DEFAULT 4.3,
    reviews          INT UNSIGNED  NOT NULL DEFAULT 50,
    created_at       TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (destination_id) REFERENCES destinations(id) ON DELETE CASCADE
) ENGINE=InnoDB;

INSERT INTO transports (id, destination_id, mode, company, city_from, city_to, duration, duration_minutes, stops, depart_hour, return_hour, price, rating, reviews) VALUES
(1,1,'Avion','Air Horizon','Paris CDG','Denpasar','17h45',1065,0,11,5,640.00,4.5,140),
(2,2,'Avion','Lusitania Air','Paris Orly','Lisbonne','2h30',150,0,8,18,150.00,4.4,97),
(3,2,'Bus','EuroRoute','Bordeaux','Lisbonne','15h20',920,1,22,12,58.00,4.2,50),
(4,3,'Train','SNCF Connect','Paris Gare de Lyon','Chamonix','6h10',370,1,7,16,94.00,4.3,87),
(5,4,'Avion','Atlas Wings','Paris Orly','Marrakech','3h20',200,0,9,20,130.00,4.6,111),
(6,5,'Ferry','Blue Sea Ferries','Athènes Pirée','Santorin','7h30',450,0,7,15,48.00,4.2,166),
(7,5,'Avion','Aegean Sky','Paris CDG','Santorin','3h25',205,0,14,19,260.00,4.5,180),
(8,6,'Avion','Japan Lines','Paris CDG','Osaka Kansai','13h15',795,0,18,16,820.00,4.7,234),
(9,3,'Voiture','Alpine Drive','Genève','Chamonix','2h10',130,0,10,17,82.00,4.4,82),
(10,4,'Voiture','Atlas Car','Aéroport Marrakech','Médina','35 min',35,0,12,18,44.00,4.3,58),
(11,1,'Avion','Qatar Airways','Paris CDG','Denpasar','18h30',1110,1,15,6,690.00,4.6,167);

-- -------------------------------------------------------------
-- TABLE : hotels
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS hotels (
    id               INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    destination_id   INT UNSIGNED NOT NULL,
    name             VARCHAR(150) NOT NULL,
    type             VARCHAR(50)  NOT NULL,
    price            DECIMAL(10,2) NOT NULL,
    rating           DECIMAL(3,1)  NOT NULL DEFAULT 4.0,
    stars            TINYINT UNSIGNED NOT NULL DEFAULT 3,
    rooms            TINYINT UNSIGNED NOT NULL DEFAULT 1,
    airport_distance SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    amenities        JSON         DEFAULT NULL,
    image            TEXT         NOT NULL,
    description      TEXT         DEFAULT NULL,
    official_url     VARCHAR(255) DEFAULT '#',
    reviews          INT UNSIGNED NOT NULL DEFAULT 50,
    partner_id       INT UNSIGNED DEFAULT NULL,
    created_at       TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (destination_id) REFERENCES destinations(id) ON DELETE CASCADE
) ENGINE=InnoDB;

INSERT INTO hotels (id, destination_id, name, type, price, rating, stars, rooms, airport_distance, amenities, image, description, official_url, reviews) VALUES
(1,1,'Ubud Garden Hotel','Hôtel',92.00,4.6,4,2,38,'["wifi","piscine","petit_dejeuner","clim"]','https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=1200&q=80','Adresse calme au milieu des jardins d''Ubud, avec chambres lumineuses, navette locale et espaces communs ouverts sur la végétation.','https://www.ubudgardenhotel.com',193),
(2,2,'Alfama Blue House','Maison d''hôtes',74.00,4.4,3,1,9,'["wifi","petit_dejeuner","clim"]','https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?auto=format&fit=crop&w=1200&q=80','Maison d''hôtes dans l''Alfama, idéale pour un séjour à pied entre belvédères, restaurants et tramways historiques.','https://www.alfamabluehouse.com',148),
(3,3,'Mont Blanc Lodge','Lodge',128.00,4.7,4,2,88,'["wifi","piscine","parking"]','https://images.unsplash.com/photo-1602002418816-5c0aeef426aa?auto=format&fit=crop&w=1200&q=80','Lodge alpin avec salons boisés, vue montagne, parking et accès pratique aux sentiers autour de Chamonix.','https://www.montblanclodge.com',162),
(4,4,'Riad Atlas Medina','Riad',66.00,4.5,4,1,6,'["wifi","petit_dejeuner","clim"]','https://images.unsplash.com/photo-1602002418679-43121356bf41?auto=format&fit=crop&w=1200&q=80','Riad central près de la médina, patio intérieur, chambres climatisées et petit-déjeuner marocain inclus.','https://www.riadatlasmedina.com',136),
(5,5,'Aegean View Hotel','Hôtel',154.00,4.8,5,2,12,'["wifi","piscine","petit_dejeuner","clim"]','https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?auto=format&fit=crop&w=1200&q=80','Hôtel avec vue mer, piscine et chambres lumineuses pour profiter de Santorin sans multiplier les trajets.','https://www.aegeanviewhotel.com',219),
(6,6,'Kyoto Maple Inn','Maison d''hôtes',118.00,4.7,4,1,44,'["wifi","petit_dejeuner","clim"]','https://images.unsplash.com/photo-1564501049412-61c2a3083791?auto=format&fit=crop&w=1200&q=80','Maison d''hôtes japonaise, chambres sobres, accès rapide aux temples et petit-déjeuner local.','https://www.kyotomapleinn.com',183),
(7,1,'Bali Beach Resort','Hôtel',135.00,4.8,5,3,18,'["wifi","piscine","petit_dejeuner","clim","spa"]','https://images.unsplash.com/photo-1582268611958-ebfd161ef9cf?auto=format&fit=crop&w=1200&q=80','Resort balinais près de la plage, spa, piscine, grandes chambres et restauration sur place.','https://www.balibeachresort.com',224);

-- -------------------------------------------------------------
-- TABLE : hotel_photos
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS hotel_photos (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    hotel_id   INT UNSIGNED NOT NULL,
    url        TEXT         NOT NULL,
    sort_order TINYINT UNSIGNED NOT NULL DEFAULT 0,
    FOREIGN KEY (hotel_id) REFERENCES hotels(id) ON DELETE CASCADE
) ENGINE=InnoDB;

INSERT INTO hotel_photos (hotel_id, url, sort_order) VALUES
(1,'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=1200&q=80',0),
(1,'https://images.unsplash.com/photo-1578683010236-d716f9a3f461?auto=format&fit=crop&w=1200&q=80',1),
(1,'https://images.unsplash.com/photo-1540541338287-41700207dee6?auto=format&fit=crop&w=1200&q=80',2),
(2,'https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?auto=format&fit=crop&w=1200&q=80',0),
(2,'https://images.unsplash.com/photo-1590490360182-c33d57733427?auto=format&fit=crop&w=1200&q=80',1),
(2,'https://images.unsplash.com/photo-1568495248636-6432b97bd949?auto=format&fit=crop&w=1200&q=80',2),
(3,'https://images.unsplash.com/photo-1602002418816-5c0aeef426aa?auto=format&fit=crop&w=1200&q=80',0),
(3,'https://images.unsplash.com/photo-1518733057094-95b53143d2a7?auto=format&fit=crop&w=1200&q=80',1),
(3,'https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?auto=format&fit=crop&w=1200&q=80',2),
(4,'https://images.unsplash.com/photo-1602002418679-43121356bf41?auto=format&fit=crop&w=1200&q=80',0),
(4,'https://images.unsplash.com/photo-1561501900-3701fa6a0864?auto=format&fit=crop&w=1200&q=80',1),
(4,'https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?auto=format&fit=crop&w=1200&q=80',2),
(5,'https://images.unsplash.com/photo-1542314831-068cd1dbfeeb?auto=format&fit=crop&w=1200&q=80',0),
(5,'https://images.unsplash.com/photo-1571896349842-33c89424de2d?auto=format&fit=crop&w=1200&q=80',1),
(5,'https://images.unsplash.com/photo-1566073771259-6a8506099945?auto=format&fit=crop&w=1200&q=80',2),
(6,'https://images.unsplash.com/photo-1564501049412-61c2a3083791?auto=format&fit=crop&w=1200&q=80',0),
(6,'https://images.unsplash.com/photo-1596394516093-501ba68a0ba6?auto=format&fit=crop&w=1200&q=80',1),
(6,'https://images.unsplash.com/photo-1596178065887-1198b6148b2b?auto=format&fit=crop&w=1200&q=80',2),
(7,'https://images.unsplash.com/photo-1582268611958-ebfd161ef9cf?auto=format&fit=crop&w=1200&q=80',0),
(7,'https://images.unsplash.com/photo-1540541338287-41700207dee6?auto=format&fit=crop&w=1200&q=80',1),
(7,'https://images.unsplash.com/photo-1520250497591-112f2f40a3f4?auto=format&fit=crop&w=1200&q=80',2);

-- -------------------------------------------------------------
-- TABLE : activities
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS activities (
    id             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    destination_id INT UNSIGNED NOT NULL,
    name           VARCHAR(150) NOT NULL,
    category       VARCHAR(50)  NOT NULL,
    duration       VARCHAR(20)  NOT NULL,
    price          DECIMAL(10,2) NOT NULL,
    rating         DECIMAL(3,1)  NOT NULL DEFAULT 4.0,
    reviews        INT UNSIGNED  NOT NULL DEFAULT 0,
    period         VARCHAR(30)   DEFAULT NULL,
    included       JSON          DEFAULT NULL,
    image          TEXT          NOT NULL,
    partner_id     INT UNSIGNED  DEFAULT NULL,
    created_at     TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (destination_id) REFERENCES destinations(id) ON DELETE CASCADE
) ENGINE=InnoDB;

INSERT INTO activities (id, destination_id, name, category, duration, price, rating, reviews, period, included, image) VALUES
(1,1,'Balade dans les rizières','Nature','4h',38.00,4.8,256,'Matin','["Guide local","Eau minérale"]','https://images.unsplash.com/photo-1539367628448-4bc5c9d171c8?auto=format&fit=crop&w=1200&q=80'),
(2,2,'Tour des miradouros','Culture','3h',22.00,4.6,118,'Après-midi','["Guide","Quartiers historiques"]','https://images.unsplash.com/photo-1555881400-74d7acaacd8b?auto=format&fit=crop&w=1200&q=80'),
(3,3,'Randonnée balcon du Mont Blanc','Aventure','6h',55.00,4.7,142,'Matin','["Guide montagne","Petit groupe"]','https://images.unsplash.com/photo-1522163182402-834f871fd851?auto=format&fit=crop&w=1200&q=80'),
(4,4,'Excursion désert Agafay','Aventure','6h',59.00,4.6,156,'Soirée','["Transfert","Dîner"]','https://images.unsplash.com/photo-1518548419970-58e3b4079ab2?auto=format&fit=crop&w=1200&q=80'),
(5,5,'Croisière au coucher du soleil','Détente','4h',86.00,4.9,240,'Soirée','["Boisson","Transfert"]','https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=1200&q=80'),
(6,6,'Visite des temples de l''est','Culture','5h',48.00,4.8,210,'Matin','["Guide","Entrées"]','https://images.unsplash.com/photo-1524413840807-0c3cb6fa808d?auto=format&fit=crop&w=1200&q=80'),
(7,1,'Snorkeling à Blue Lagoon','Nature','3h',52.00,4.7,189,'Après-midi','["Équipement","Guide"]','https://images.unsplash.com/photo-1544551763-46a013bb70d5?auto=format&fit=crop&w=1200&q=80'),
(8,1,'Temple de Tanah Lot','Culture','4h',36.00,4.9,312,'Soirée','["Guide","Entrée"]','https://images.unsplash.com/photo-1555400038-63f5ba517a47?auto=format&fit=crop&w=1200&q=80');

-- -------------------------------------------------------------
-- TABLE : hotel_reviews
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS hotel_reviews (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    hotel_id   INT UNSIGNED NOT NULL,
    author     VARCHAR(80)  NOT NULL,
    rating     TINYINT UNSIGNED NOT NULL DEFAULT 5,
    text       TEXT         NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (hotel_id) REFERENCES hotels(id) ON DELETE CASCADE
) ENGINE=InnoDB;

INSERT INTO hotel_reviews (hotel_id, author, rating, text) VALUES
(1,'Nora',5,'Très bon accueil, piscine propre et accès rapide aux rizières.'),
(1,'Yanis',4,'Chambre confortable, petit-déjeuner varié et Wi-Fi stable.'),
(1,'Léa',5,'Bon équilibre entre calme, prix et proximité du centre d''Ubud.'),
(2,'Sofia',4,'Quartier parfait pour découvrir Lisbonne sans voiture.'),
(2,'Maxime',4,'Accueil simple, chambre propre et climatisation appréciable.'),
(2,'Inès',5,'Très bon rapport qualité-prix pour un city break.'),
(3,'Arthur',5,'Très pratique pour rayonner dans la vallée.'),
(3,'Emma',4,'Ambiance chaleureuse et parking facile.'),
(3,'Noé',5,'Vue superbe le matin depuis la terrasse.'),
(4,'Imane',5,'Patio très agréable après les visites.'),
(4,'Paul',4,'Personnel disponible et emplacement pratique.'),
(4,'Sarah',4,'Bonne adresse pour découvrir la médina.'),
(5,'Clara',5,'Vue superbe et service très régulier.'),
(5,'Hugo',5,'Piscine calme, chambre impeccable.'),
(5,'Mila',4,'Très bien situé pour les couchers de soleil.'),
(6,'Aya',5,'Très calme et facile d''accès en transport.'),
(6,'Lucas',4,'Chambre compacte mais très bien pensée.'),
(6,'Mina',5,'Accueil attentionné et quartier agréable.'),
(7,'Jade',5,'Très belle piscine et accès plage rapide.'),
(7,'Ilyes',5,'Chambres spacieuses, spa agréable.'),
(7,'Manon',4,'Bon choix pour un séjour reposant.');

-- -------------------------------------------------------------
-- TABLE : packs
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS packs (
    id             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    destination_id INT UNSIGNED NOT NULL,
    name           VARCHAR(150) NOT NULL,
    label          VARCHAR(150) NOT NULL,
    discount       VARCHAR(10)  DEFAULT NULL,
    FOREIGN KEY (destination_id) REFERENCES destinations(id) ON DELETE CASCADE
) ENGINE=InnoDB;

INSERT INTO packs (id, destination_id, name, label, discount) VALUES
(1,1,'Pack plage essentiel','Vol + hôtel + activité','-21%'),
(2,4,'Pack culture soleil','Vol + riad + excursion','-19%'),
(3,5,'Pack mer Égée','Ferry + hôtel + croisière','-19%');

-- -------------------------------------------------------------
-- TABLE : users
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS users (
    id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    email        VARCHAR(180) NOT NULL UNIQUE,
    password     VARCHAR(255) NOT NULL,   -- bcrypt hash
    first_name   VARCHAR(80)  DEFAULT NULL,
    last_name    VARCHAR(80)  DEFAULT NULL,
    role         ENUM('visiteur','client','partenaire','admin') NOT NULL DEFAULT 'client',
    created_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Mot de passe : password123  (à changer en production)
INSERT INTO users (email, password, first_name, last_name, role) VALUES
('client@voyagevista.fr', '$2y$12$K9wKm3BXoBQqbZ6rX1MFZO.xA.mB5tkTVAQl9bxe0dj1wPSZlQTCm', 'Marie', 'Dupont', 'client'),
('admin@voyagevista.fr',  '$2y$12$K9wKm3BXoBQqbZ6rX1MFZO.xA.mB5tkTVAQl9bxe0dj1wPSZlQTCm', 'Admin', 'VV', 'admin');

-- -------------------------------------------------------------
-- TABLE : notifications
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS notifications (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id    INT UNSIGNED DEFAULT NULL,             -- NULL = globale / tous
    category   VARCHAR(50)  NOT NULL,
    title      VARCHAR(150) NOT NULL,
    message    TEXT         NOT NULL,
    icon       VARCHAR(10)  DEFAULT 'ℹ️',
    color      VARCHAR(30)  DEFAULT 'slate',
    is_read    TINYINT(1)   NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

INSERT INTO notifications (user_id, category, title, message, icon, color) VALUES
(1,'Réservations','Réservation confirmée','Votre réservation à Ubud Garden Hotel est confirmée.','📅','teal'),
(1,'Trajets','Départ dans 2 jours','Votre vol Paris CDG → Denpasar décolle bientôt.','✈️','blue'),
(1,'Hébergements','Modification d''hébergement','Les dates de votre séjour ont été mises à jour.','🛏️','teal'),
(1,'Activités','Activité à venir','Votre balade dans les rizières est prévue le 17 juin.','🎟️','purple'),
(NULL,'Promotions','Offre spéciale pour vous','Profitez de 10% de réduction sur certaines activités.','🏷️','orange'),
(NULL,'Système','Mise à jour des conditions','Consultez les changements récents.','ℹ️','slate');

-- -------------------------------------------------------------
-- TABLE : reviews  (avis globaux sur la plateforme)
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS reviews (
    id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_name   VARCHAR(80) NOT NULL,
    destination VARCHAR(100) NOT NULL,
    rating      TINYINT UNSIGNED NOT NULL DEFAULT 5,
    text        TEXT        NOT NULL,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT INTO reviews (user_name, destination, rating, text) VALUES
('Camille','Bali',5,'Le choix par étape rend la préparation très simple.'),
('Nassim','Marrakech',4,'Les filtres budget et activités aident à composer un séjour rapide.'),
('Lina','Santorin',5,'Le pack mer avec ferry et hôtel vue mer est très clair.');

-- -------------------------------------------------------------
-- TABLE : city_routes (liaisons de transport entre villes)
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS city_routes (
    id             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    city_from      VARCHAR(100) NOT NULL,
    city_to        VARCHAR(100) NOT NULL,
    destination_id INT UNSIGNED NOT NULL,
    transport_id   INT UNSIGNED NOT NULL,
    FOREIGN KEY (destination_id) REFERENCES destinations(id) ON DELETE CASCADE,
    FOREIGN KEY (transport_id)   REFERENCES transports(id)   ON DELETE CASCADE
) ENGINE=InnoDB;

INSERT INTO city_routes (city_from, city_to, destination_id, transport_id) VALUES
('Paris','Bali',1,1),
('Paris','Lisbonne',2,2),
('Bordeaux','Lisbonne',2,3),
('Paris','Chamonix',3,4),
('Paris','Marrakech',4,5),
('Athènes','Santorin',5,6),
('Paris','Santorin',5,7),
('Paris','Kyoto',6,8),
('Genève','Chamonix',3,9);

-- -------------------------------------------------------------
-- TABLE : city_coordinates
-- -------------------------------------------------------------
CREATE TABLE IF NOT EXISTS city_coordinates (
    city  VARCHAR(100) NOT NULL PRIMARY KEY,
    lat   DECIMAL(9,6) NOT NULL,
    lng   DECIMAL(9,6) NOT NULL
) ENGINE=InnoDB;

INSERT INTO city_coordinates (city, lat, lng) VALUES
('Paris',48.856600,2.352200),('Lyon',45.764000,4.835700),('Marseille',43.296500,5.369800),
('Bordeaux',44.837800,-0.579200),('Lille',50.629200,3.057300),('Nice',43.710200,7.262000),
('Nantes',47.218400,-1.553600),('Toulouse',43.604700,1.444200),('Lisbonne',38.722300,-9.139300),
('Marrakech',31.629500,-7.981100),('Santorin',36.393200,25.461500),('Bali',-8.340500,115.092000),
('Denpasar',-8.650000,115.216700),('Kyoto',35.011600,135.768100),('Chamonix',45.923700,6.869400),
('Genève',46.204400,6.143200),('Athènes',37.983800,23.727500),('Rome',41.902800,12.496400),
('Barcelone',41.387400,2.168600),('Amsterdam',52.367600,4.904100),('Londres',51.507200,-0.127600),
('New York',40.712800,-74.006000),('Tokyo',35.676200,139.650300);
