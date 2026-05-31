<?php
// ============================================================
//  VoyageVista — partenaire.php
//  Espace partenaire : gestion des offres proposées.
// ============================================================
require_once 'includes/data.php';
require_once 'includes/roles.php';

requireRole('partenaire');

$pageTitle = 'Espace partenaire';
$partnerId = $_SESSION['user_id'];

// Ajoute partner_id si la colonne n'existe pas encore (compatibilité BDD ancienne)
try { $db->exec("ALTER TABLE hotels     ADD COLUMN partner_id INT UNSIGNED DEFAULT NULL"); } catch (Exception $e) {}
try { $db->exec("ALTER TABLE activities ADD COLUMN partner_id INT UNSIGNED DEFAULT NULL"); } catch (Exception $e) {}

// Charge les hôtels et activités proposés par ce partenaire
try {
    $stmtHotels = $db->prepare('SELECT * FROM hotels WHERE partner_id = :pid ORDER BY id DESC');
    $stmtHotels->execute([':pid' => $partnerId]);
    $myHotels = $stmtHotels->fetchAll();
} catch (Exception $e) {
    $myHotels = [];
}

try {
    $stmtActs = $db->prepare('SELECT * FROM activities WHERE partner_id = :pid ORDER BY id DESC');
    $stmtActs->execute([':pid' => $partnerId]);
    $myActivities = $stmtActs->fetchAll();
} catch (Exception $e) {
    $myActivities = [];
}

// Charge les réservations liées aux offres de ce partenaire
try {
    $stmtBook = $db->prepare(
        'SELECT b.id, b.checkin, b.checkout, b.adults, b.children, b.nights,
                b.created_at, b.status,
                h.name AS hotel_name, d.name AS dest_name
         FROM bookings b
         JOIN hotels h ON h.id = b.hotel_id
         JOIN destinations d ON d.id = b.destination_id
         WHERE h.partner_id = :pid AND b.status = "confirmed"
         ORDER BY b.created_at DESC
         LIMIT 20'
    );
    $stmtBook->execute([':pid' => $partnerId]);
    $myBookings = $stmtBook->fetchAll();
} catch (Exception $e) {
    $myBookings = [];
}

$message = '';
$msgType = 'ok';

// Traitement ajout hôtel
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'add_hotel') {
    $name    = trim($_POST['name'] ?? '');
    $type    = trim($_POST['type'] ?? '');
    $price   = (float)($_POST['price'] ?? 0);
    $rooms   = max(1, (int)($_POST['rooms'] ?? 1));
    $destId  = (int)($_POST['destination_id'] ?? 0);
    $image   = trim($_POST['image'] ?? '');

    if ($name && $type && $price > 0 && $destId > 0) {
        try {
            $ins = $db->prepare(
                'INSERT INTO hotels (name, type, price, rooms, destination_id, image, rating, reviews, partner_id)
                 VALUES (:name, :type, :price, :rooms, :dest, :img, 0, 0, :pid)'
            );
            $ins->execute([
                ':name'  => $name,
                ':type'  => $type,
                ':price' => $price,
                ':rooms' => $rooms,
                ':dest'  => $destId,
                ':img'   => $image ?: 'assets/images/hotel-default.jpg',
                ':pid'   => $partnerId,
            ]);
            $message = 'Hébergement ajouté avec succès.';
        } catch (Exception $e) {
            $message = 'Erreur : ' . $e->getMessage();
            $msgType = 'error';
        }
    } else {
        $message = 'Veuillez remplir tous les champs obligatoires.';
        $msgType = 'error';
    }
}

// Traitement ajout activité
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'add_activity') {
    $name    = trim($_POST['name'] ?? '');
    $cat     = trim($_POST['category'] ?? '');
    $price   = (float)($_POST['price'] ?? 0);
    $destId  = (int)($_POST['destination_id'] ?? 0);
    $image   = trim($_POST['image'] ?? '');
    $dur     = trim($_POST['duration'] ?? '');

    if ($name && $cat && $price > 0 && $destId > 0) {
        try {
            $ins = $db->prepare(
                'INSERT INTO activities (name, category, price, destination_id, image, duration, rating, partner_id)
                 VALUES (:name, :cat, :price, :dest, :img, :dur, 0, :pid)'
            );
            $ins->execute([
                ':name'  => $name,
                ':cat'   => $cat,
                ':price' => $price,
                ':dest'  => $destId,
                ':img'   => $image ?: 'assets/images/activity-default.jpg',
                ':dur'   => $dur ?: '2h',
                ':pid'   => $partnerId,
            ]);
            $message = 'Activité ajoutée avec succès.';
        } catch (Exception $e) {
            $message = 'Erreur : ' . $e->getMessage();
            $msgType = 'error';
        }
    } else {
        $message = 'Veuillez remplir tous les champs obligatoires.';
        $msgType = 'error';
    }
}

include 'includes/header.php';
?>

<section class="center-page" style="max-width:1000px;">
    <div class="panel" style="padding:2rem;">
        <p class="eyebrow">Espace partenaire</p>
        <h1>Bienvenue, <?= htmlspecialchars($_SESSION['user_name'] ?? 'Partenaire') ?></h1>
        <p class="muted">Gérez vos hébergements et activités, et suivez les réservations liées à vos offres.</p>

        <?php if ($message): ?>
        <div class="form-message" style="padding:.75rem 1rem;border-radius:8px;margin:1rem 0;background:<?= $msgType === 'ok' ? '#d1fae5' : '#fee2e2' ?>;color:<?= $msgType === 'ok' ? '#065f46' : '#991b1b' ?>;">
            <?= htmlspecialchars($message) ?>
        </div>
        <?php endif; ?>
    </div>

    <!-- Réservations reçues -->
    <?php if (!empty($myBookings)): ?>
    <div class="panel" style="padding:2rem;margin-top:1.5rem;">
        <h2>Réservations reçues (<?= count($myBookings) ?>)</h2>
        <div style="overflow-x:auto;">
            <table style="width:100%;border-collapse:collapse;margin-top:1rem;">
                <thead>
                    <tr style="text-align:left;border-bottom:2px solid var(--color-border,#eee);">
                        <th style="padding:.5rem;">Hébergement</th>
                        <th style="padding:.5rem;">Destination</th>
                        <th style="padding:.5rem;">Arrivée</th>
                        <th style="padding:.5rem;">Départ</th>
                        <th style="padding:.5rem;">Voyageurs</th>
                        <th style="padding:.5rem;">Statut</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($myBookings as $bk): ?>
                    <tr style="border-bottom:1px solid var(--color-border,#eee);">
                        <td style="padding:.5rem;"><?= htmlspecialchars($bk['hotel_name']) ?></td>
                        <td style="padding:.5rem;"><?= htmlspecialchars($bk['dest_name']) ?></td>
                        <td style="padding:.5rem;"><?= htmlspecialchars($bk['checkin'] ?? '—') ?></td>
                        <td style="padding:.5rem;"><?= htmlspecialchars($bk['checkout'] ?? '—') ?></td>
                        <td style="padding:.5rem;"><?= (int)$bk['adults'] ?> adulte(s) <?= $bk['children'] > 0 ? '+ ' . (int)$bk['children'] . ' enfant(s)' : '' ?></td>
                        <td style="padding:.5rem;"><span class="role-badge role-badge-client">Confirmée</span></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>

    <!-- Mes hébergements -->
    <div class="panel" style="padding:2rem;margin-top:1.5rem;">
        <h2>Mes hébergements (<?= count($myHotels) ?>)</h2>

        <?php if (!empty($myHotels)): ?>
        <div style="display:grid;gap:1rem;margin:1rem 0;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));">
            <?php foreach ($myHotels as $h): ?>
            <article class="panel" style="padding:1rem;">
                <strong><?= htmlspecialchars($h['name']) ?></strong>
                <p class="muted" style="font-size:.9rem;"><?= htmlspecialchars($h['type']) ?> · <?= number_format((float)$h['price'], 0, ',', ' ') ?> €/nuit · <?= (int)$h['rooms'] ?> chambre(s)</p>
                <p class="muted" style="font-size:.85rem;">Note : <?= (float)$h['rating'] ?>/5 (<?= (int)$h['reviews'] ?> avis)</p>
            </article>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <p class="muted" style="margin:1rem 0;">Vous n'avez pas encore d'hébergement publié.</p>
        <?php endif; ?>

        <details style="margin-top:1.5rem;">
            <summary style="cursor:pointer;font-weight:600;margin-bottom:1rem;">+ Ajouter un hébergement</summary>
            <form method="post" class="login-form" style="max-width:500px;">
                <input type="hidden" name="action" value="add_hotel">
                <label>Nom de l'hébergement *
                    <input type="text" name="name" required placeholder="Hôtel des Alpes">
                </label>
                <label>Type *
                    <select name="type" required>
                        <option value="">— Choisir —</option>
                        <option value="Hôtel">Hôtel</option>
                        <option value="Appartement">Appartement</option>
                        <option value="Villa">Villa</option>
                        <option value="Gîte">Gîte</option>
                        <option value="Chalet">Chalet</option>
                        <option value="Camping">Camping</option>
                    </select>
                </label>
                <label>Prix par nuit (€) *
                    <input type="number" name="price" min="1" step="1" required placeholder="120">
                </label>
                <label>Nombre de chambres *
                    <input type="number" name="rooms" min="1" max="500" required placeholder="10">
                </label>
                <label>Destination *
                    <select name="destination_id" required>
                        <option value="">— Choisir —</option>
                        <?php foreach ($destinations as $dest): ?>
                        <option value="<?= (int)$dest['id'] ?>"><?= htmlspecialchars($dest['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <label>URL de l'image (optionnel)
                    <input type="url" name="image" placeholder="https://...">
                </label>
                <button class="btn btn-primary" type="submit">Publier l'hébergement</button>
            </form>
        </details>
    </div>

    <!-- Mes activités -->
    <div class="panel" style="padding:2rem;margin-top:1.5rem;">
        <h2>Mes activités (<?= count($myActivities) ?>)</h2>

        <?php if (!empty($myActivities)): ?>
        <div style="display:grid;gap:1rem;margin:1rem 0;grid-template-columns:repeat(auto-fill,minmax(260px,1fr));">
            <?php foreach ($myActivities as $a): ?>
            <article class="panel" style="padding:1rem;">
                <strong><?= htmlspecialchars($a['name']) ?></strong>
                <p class="muted" style="font-size:.9rem;"><?= htmlspecialchars($a['category']) ?> · <?= number_format((float)$a['price'], 0, ',', ' ') ?> €/pers · <?= htmlspecialchars($a['duration'] ?? '') ?></p>
            </article>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <p class="muted" style="margin:1rem 0;">Vous n'avez pas encore d'activité publiée.</p>
        <?php endif; ?>

        <details style="margin-top:1.5rem;">
            <summary style="cursor:pointer;font-weight:600;margin-bottom:1rem;">+ Ajouter une activité</summary>
            <form method="post" class="login-form" style="max-width:500px;">
                <input type="hidden" name="action" value="add_activity">
                <label>Nom de l'activité *
                    <input type="text" name="name" required placeholder="Randonnée en montagne">
                </label>
                <label>Catégorie *
                    <select name="category" required>
                        <option value="">— Choisir —</option>
                        <option value="Aventure">Aventure</option>
                        <option value="Culture">Culture</option>
                        <option value="Gastronomie">Gastronomie</option>
                        <option value="Nature">Nature</option>
                        <option value="Sport">Sport</option>
                        <option value="Bien-être">Bien-être</option>
                        <option value="Visite">Visite</option>
                    </select>
                </label>
                <label>Prix par personne (€) *
                    <input type="number" name="price" min="1" step="1" required placeholder="45">
                </label>
                <label>Durée
                    <input type="text" name="duration" placeholder="3h30">
                </label>
                <label>Destination *
                    <select name="destination_id" required>
                        <option value="">— Choisir —</option>
                        <?php foreach ($destinations as $dest): ?>
                        <option value="<?= (int)$dest['id'] ?>"><?= htmlspecialchars($dest['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <label>URL de l'image (optionnel)
                    <input type="url" name="image" placeholder="https://...">
                </label>
                <button class="btn btn-primary" type="submit">Publier l'activité</button>
            </form>
        </details>
    </div>

    <div style="text-align:center;margin:2rem 0;">
        <a class="btn btn-light" href="compte.php">← Retour à mon compte</a>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
