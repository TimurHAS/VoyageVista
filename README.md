# VoyageVista - Maquette finale Personne 1

VoyageVista est une interface de planification de voyages permettant de rechercher, comparer et composer un séjour selon plusieurs services : vols, hébergements, voitures, vacances tout compris et activités.

## Rôle de la Personne 1

Cette version livre la base front-end complète :

- page d'accueil avec recherche multi-services ;
- autocomplétion de villes connues ;
- résultats par étapes ;
- catalogues destinations, transports, hébergements et activités ;
- détail destination avec activités liées cliquables ;
- panier capable de contenir un pack complet ou seulement certains éléments ;
- compte, favoris et notifications ;
- filtres, curseurs de prix, catégories, offres, avis, réductions et packs.

## Structure

```text
voyagevista/
├── index.php
├── resultats.php
├── composer_transport.php
├── composer_hebergement.php
├── composer_activites.php
├── composer_recap.php
├── ville_options.php
├── destinations.php
├── destination_detail.php
├── transports.php
├── hebergements.php
├── activites.php
├── panier.php
├── compte.php
├── notifications.php
├── favoris.php
├── includes/
│   ├── data.php
│   ├── header.php
│   └── footer.php
├── assets/
│   ├── css/
│   │   └── style.css
│   ├── js/
│   │   └── main.js
│   └── images/
└── README.md
```

## Lancer le site

Avec PHP :

```bash
cd voyagevista
php -S localhost:8000
```

Puis ouvrir :

```text
http://localhost:8000/index.php
```

Avec WAMP :

Mettre le dossier dans :

```text
C:\wamp64\www\
```

Puis ouvrir :

```text
http://localhost/voyagevista/index.php
```

## Pages principales

- `index.php` : recherche multi-services et séjours recommandés.
- `resultats.php` : choix par étapes du transport, de l'hébergement et des activités.
- `composer_transport.php` : étape 1, choix du transport.
- `composer_hebergement.php` : étape 2, choix et détail de l'hébergement.
- `composer_activites.php` : étape 3, choix des activités liées.
- `composer_recap.php` : étape 4, récapitulatif avec carte du trajet.
- `ville_options.php` : options pour partir depuis une ville ou aller vers une ville.
- `destinations.php` : catalogue avec catégories, budget et cartes.
- `destination_detail.php` : destination, transports, hébergements, activités et avis.
- `panier.php` : résumé final de la sélection.
