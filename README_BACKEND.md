# VoyageVista — Backend : Guide d'intégration

## Ce qui a changé

| Avant | Après |
|-------|-------|
| Tableaux statiques dans `includes/data.php` | Requêtes PDO vers MySQL |
| Authentification simulée en JS | Sessions PHP + bcrypt |
| Favoris dans `localStorage` | Table `favorites` en base |
| Panier uniquement en `localStorage` | Table `carts` + API JSON |
| Notifications statiques | Table `notifications` + API JSON |

---

## 1. Mise en place de la base de données

### Prérequis
- PHP 8.1+ avec extensions `pdo_mysql`, `json`
- MySQL 8+ ou MariaDB 10.6+

### Import

```bash
mysql -u root -p < voyagevista_db.sql
```

Ou dans phpMyAdmin : **Importer → voyagevista_db.sql**.

---

## 2. Configuration de la connexion

Éditez **`includes/db.php`** et adaptez les 4 constantes :

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'voyagevista');
define('DB_USER', 'root');    // votre user MySQL
define('DB_PASS', '');        // votre mot de passe
```

---

## 3. Fichiers à remplacer / créer

### Fichiers à **remplacer** (écraser l'original) :

| Fichier | Action |
|---------|--------|
| `includes/data.php` | Remplacer |
| `compte.php` | Remplacer |
| `favoris.php` | Remplacer |
| `notifications.php` | Remplacer |

### Nouveaux fichiers à **créer** (copier dans votre projet) :

| Fichier | Rôle |
|---------|------|
| `includes/db.php` | Connexion PDO (singleton) |
| `includes/auth.php` | API login / register / logout / status |
| `includes/api_favoris.php` | API CRUD favoris |
| `includes/api_notifications.php` | API marquer lu / supprimer |
| `includes/api_panier.php` | API sauvegarde panier |

### Fichier SQL :
| Fichier | Rôle |
|---------|------|
| `voyagevista_db.sql` | Schéma complet + données initiales |

---

## 4. Architecture des API (appels AJAX)

### Authentification — `includes/auth.php`

```js
// Connexion
fetch('includes/auth.php', {
  method: 'POST',
  headers: { 'Content-Type': 'application/json' },
  body: JSON.stringify({ action: 'login', email: '…', password: '…' })
})

// Inscription
{ action: 'register', email, password, first_name, last_name }

// Déconnexion
{ action: 'logout' }

// Vérifier la session
{ action: 'status' }
```

### Favoris — `includes/api_favoris.php`

```js
// Ajouter
{ action: 'add', destination_id: 3 }
// Retirer
{ action: 'remove', destination_id: 3 }
// Lister
{ action: 'list' }
// Vider
{ action: 'clear' }
```

### Notifications — `includes/api_notifications.php`

```js
{ action: 'read',     id: 5 }   // marquer une notif lue
{ action: 'read_all' }          // tout marquer lu
{ action: 'delete',  id: 5 }   // supprimer (notifs perso seulement)
```

### Panier — `includes/api_panier.php`

```js
// Sauvegarder
{
  action: 'save',
  destination_id: 1, transport_id: 1, hotel_id: 1,
  activity_ids: [1, 7],
  adults: 2, children: 0, nights: 10,
  bags: 'soute', ticket: 'Basic', seat: 'Standard',
  room_type: 'view', hotel_options: ['breakfast'],
  checkin: '2026-06-15', checkout: '2026-06-25'
}
// Charger
{ action: 'load' }
// Vider
{ action: 'clear' }
// Confirmer
{ action: 'confirm' }
```

---

## 5. Tables créées automatiquement

Les tables `favorites` et `carts` sont créées automatiquement au premier appel
des API correspondantes (`api_favoris.php` et `api_panier.php`).
Toutes les autres tables sont créées par le fichier `voyagevista_db.sql`.

---

## 6. Comptes de test

| Email | Mot de passe | Rôle |
|-------|-------------|------|
| `client@voyagevista.fr` | `password123` | client |
| `admin@voyagevista.fr`  | `password123` | admin |

> ⚠️ Changez ces mots de passe en production !

---

## 7. Structure finale du dossier

```
VoyageVista/
├── includes/
│   ├── db.php                   ← NOUVEAU
│   ├── data.php                 ← REMPLACÉ
│   ├── auth.php                 ← NOUVEAU
│   ├── api_favoris.php          ← NOUVEAU
│   ├── api_notifications.php    ← NOUVEAU
│   ├── api_panier.php           ← NOUVEAU
│   ├── header.php               (inchangé)
│   └── footer.php               (inchangé)
├── compte.php                   ← REMPLACÉ
├── favoris.php                  ← REMPLACÉ
├── notifications.php            ← REMPLACÉ
├── voyagevista_db.sql           ← NOUVEAU (importer)
└── … (autres pages inchangées)
```

---

## 8. Ce que les autres pages `.php` n'ont PAS besoin de changer

`destinations.php`, `transports.php`, `hebergements.php`, `activites.php`,
`resultats.php`, `panier.php`, `destination_detail.php`, etc. utilisent tous
`require_once 'includes/data.php'` et les variables `$destinations`, `$hotels`,
`$transports`, `$activities`, `$notifications` — qui gardent **exactement la
même structure** qu'avant, juste chargées depuis MySQL.

**Aucune modification nécessaire sur ces fichiers.**
