# VoyageVista — Projet Web Dynamique 2026

VoyageVista est une plateforme web dynamique de planification de voyages et de séjours.  
Le site permet à un utilisateur de rechercher une destination, comparer des transports, choisir un hébergement, ajouter des activités, composer un panier, se connecter, valider une réservation simulée et recevoir des notifications.

Projet réalisé dans le cadre du module **ING2 — Web Dynamique 2026**.

---

## Fonctionnalités principales

- Recherche de destinations
- Catalogue de destinations avec filtres
- Consultation des transports
- Consultation des hébergements
- Consultation des activités
- Composition d’un séjour en plusieurs étapes
- Gestion d’un panier
- Connexion / inscription utilisateur
- Favoris
- Notifications
- Paiement simulé
- Mode sombre
- Interface responsive
- Base de données MySQL
- API PHP pour panier, favoris, notifications et authentification

---

## Structure du projet

```text
VoyageVista/
│
├── index.php
├── resultats.php
├── destinations.php
├── destination_detail.php
├── transports.php
├── transport_detail.php
├── hebergements.php
├── hebergement_detail.php
├── activites.php
├── activity_detail.php
│
├── composer_transport.php
├── composer_hebergement.php
├── composer_activites.php
├── composer_recap.php
│
├── panier.php
├── confirmation.php
├── compte.php
├── favoris.php
├── notifications.php
├── admin.php
├── partenaire.php
├── roles.php
│
├── ville_options.php
├── resume.php
├── test.php
├── read_pdf.php
│
├── includes/
│   ├── db.php
│   ├── data.php
│   ├── auth.php
│   ├── roles.php
│   ├── api_admin.php
│   ├── api_avis.php
│   ├── api_favoris.php
│   ├── api_notifications.php
│   ├── api_panier.php
│   ├── header.php
│   └── footer.php
│
├── assets/
│   ├── css/
│   │   └── style.css
│   │
│   ├── js/
│   │   └── main.js
│   │
│   └── images/
│       └── .gitkeep
│
├── logo/
│   ├── VV_logo_clair.png
│   └── VV_logo_sombre.png
│
├── voyagevista_db.sql
├── voyagevista.sql
├── fix_data.sql
├── fix_availability.sql
│
├── README.md
├── README_BACKEND.md
└── README_NETTOYAGE.md
