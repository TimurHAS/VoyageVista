# Nettoyage effectué

Cette version garde l'interface existante et supprime uniquement les doublons de structure.

Modifications réalisées :
- conservation d'un seul dossier projet `VoyageVista_clean` ;
- suppression du dossier projet imbriqué en double ;
- suppression des dossiers `.git` exportés dans l'archive ;
- conservation des pages PHP, du CSS, du JavaScript, des includes et des logos ;
- aucun renommage des classes CSS ni modification volontaire du rendu visuel.

Structure principale :
- `includes/header.php` et `includes/footer.php` : éléments communs ;
- `assets/css/style.css` : styles de l'interface ;
- `assets/js/main.js` : interactions côté client ;
- `includes/data.php` : données utilisées par les pages ;
- pages racine `index.php`, `destinations.php`, `transports.php`, etc. : vues principales.
