CREATE DATABASE IF NOT EXISTS voyagevista;
USE voyagevista;


CREATE TABLE UTILISATEUR (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('visiteur', 'client', 'partenaire', 'admin') DEFAULT 'client',
    date_inscription DATETIME DEFAULT CURRENT_TIMESTAMP
);


CREATE TABLE DESTINATION (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    country VARCHAR(255) NOT NULL,
    category VARCHAR(100),
    duration VARCHAR(100),
    price DECIMAL(10,2),
    old_price DECIMAL(10,2),
    rating FLOAT DEFAULT 0,
    reviews INT DEFAULT 0,
    description TEXT,
    image VARCHAR(255),
    stay_type VARCHAR(100),
    recommendation INT DEFAULT 0
);

CREATE TABLE HEBERGEMENT (
    id INT AUTO_INCREMENT PRIMARY KEY,
    destination_id INT,
    name VARCHAR(255) NOT NULL,
    type VARCHAR(100),
    price DECIMAL(10,2),
    rooms INT,
    airport_distance INT,
    stars INT,
    rating FLOAT,
    reviews INT,
    image VARCHAR(255),
    description TEXT,
    FOREIGN KEY (destination_id) REFERENCES DESTINATION(id) ON DELETE CASCADE
);

CREATE TABLE TRANSPORT (
    id INT AUTO_INCREMENT PRIMARY KEY,
    destination_id INT,
    mode VARCHAR(100),
    company VARCHAR(100),
    from_city VARCHAR(100),
    to_city VARCHAR(100),
    depart_hour INT,
    return_hour INT,
    duration VARCHAR(50),
    duration_minutes INT,
    stops INT,
    price DECIMAL(10,2),
    rating FLOAT,
    reviews INT,
    FOREIGN KEY (destination_id) REFERENCES DESTINATION(id) ON DELETE CASCADE
);

CREATE TABLE ACTIVITE (
    id INT AUTO_INCREMENT PRIMARY KEY,
    destination_id INT,
    name VARCHAR(255),
    category VARCHAR(100),
    duration VARCHAR(100),
    period VARCHAR(100),
    price DECIMAL(10,2),
    rating FLOAT,
    reviews INT,
    image VARCHAR(255),
    FOREIGN KEY (destination_id) REFERENCES DESTINATION(id) ON DELETE CASCADE
);


CREATE TABLE EQUIPEMENT (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) UNIQUE,
    label VARCHAR(100)
);

CREATE TABLE HEBERGEMENT_EQUIPEMENT (
    hebergement_id INT,
    equipement_id INT,
    PRIMARY KEY (hebergement_id, equipement_id),
    FOREIGN KEY (hebergement_id) REFERENCES HEBERGEMENT(id) ON DELETE CASCADE,
    FOREIGN KEY (equipement_id) REFERENCES EQUIPEMENT(id) ON DELETE CASCADE
);

CREATE TABLE FAVORI (
    utilisateur_id INT,
    destination_id INT,
    date_ajout DATETIME DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (utilisateur_id, destination_id),
    FOREIGN KEY (utilisateur_id) REFERENCES UTILISATEUR(id) ON DELETE CASCADE,
    FOREIGN KEY (destination_id) REFERENCES DESTINATION(id) ON DELETE CASCADE
);

CREATE TABLE AVIS (
    id INT AUTO_INCREMENT PRIMARY KEY,
    utilisateur_id INT,
    destination_id INT,
    note INT CHECK(note >= 1 AND note <= 5),
    commentaire TEXT,
    date_avis DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (utilisateur_id) REFERENCES UTILISATEUR(id) ON DELETE CASCADE,
    FOREIGN KEY (destination_id) REFERENCES DESTINATION(id) ON DELETE CASCADE
);


CREATE TABLE RESERVATION (
    id INT AUTO_INCREMENT PRIMARY KEY,
    utilisateur_id INT,
    total_price DECIMAL(10,2),
    status VARCHAR(50) DEFAULT 'validee',
    date_creation DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (utilisateur_id) REFERENCES UTILISATEUR(id)
);

CREATE TABLE RESERVATION_HEBERGEMENT (
    reservation_id INT,
    hebergement_id INT,
    nights INT,
    adults INT,
    children INT,
    room_type VARCHAR(100),
    price DECIMAL(10,2),
    PRIMARY KEY (reservation_id, hebergement_id),
    FOREIGN KEY (reservation_id) REFERENCES RESERVATION(id) ON DELETE CASCADE,
    FOREIGN KEY (hebergement_id) REFERENCES HEBERGEMENT(id)
);

CREATE TABLE NOTIFICATION (
    id INT AUTO_INCREMENT PRIMARY KEY,
    utilisateur_id INT,
    title VARCHAR(255),
    message TEXT,
    category VARCHAR(100),
    lue BOOLEAN DEFAULT FALSE,
    date_envoi DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (utilisateur_id) REFERENCES UTILISATEUR(id) ON DELETE CASCADE
);