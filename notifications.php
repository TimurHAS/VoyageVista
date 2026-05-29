<?php
// ============================================================
//  VoyageVista — notifications.php  (version base de données)
// ============================================================
require_once 'includes/data.php';   // charge la session + $notifications
$pageTitle = 'Notifications';

$loggedIn = isset($_SESSION['user_id']);

$category = trim($_GET['category'] ?? 'Toutes');
$visibleNotifications = array_filter(
    $notifications,
    fn($n) => $category === 'Toutes' || $n['category'] === $category
);

$notificationCategories = ['Toutes','Réservations','Trajets','Hébergements','Activités','Promotions','Système'];
$selectedNotification   = array_values($visibleNotifications)[0] ?? ($notifications[0] ?? []);
$detailDestination      = findById($destinations, 1);
$detailHotel            = findById($hotels, 1);

include 'includes/header.php';
?>

<section>
<?php if (!$loggedIn): ?>
<div class="center-page">
    <div class="panel auth-locked">
        <p class="eyebrow">Accès connecté</p>
        <h1>Connectez-vous pour voir vos notifications</h1>
        <p>Les alertes de réservation, trajet, hébergement et activité sont disponibles dans votre espace.</p>
        <a class="btn btn-primary" href="compte.php">Se connecter</a>
    </div>
</div>
<?php else: ?>
<div class="notifications-board">

    <aside class="notification-sidebar panel">
        <h1>🔔 Notifications</h1>
        <nav class="side-nav notification-nav">
            <?php foreach ($notificationCategories as $item): ?>
                <?php
                $count = $item === 'Toutes'
                    ? count($notifications)
                    : count(array_filter($notifications, fn($n) => $n['category'] === $item));
                ?>
                <a class="<?= $category === $item ? 'active' : '' ?>"
                   href="notifications.php?category=<?= urlencode($item) ?>">
                    <span><?= htmlspecialchars($item) ?></span>
                    <b><?= (int) $count ?></b>
                </a>
            <?php endforeach; ?>
        </nav>
        <a class="preferences-link" href="compte.php">⚙️ Préférences</a>
    </aside>

    <section class="notification-center panel">
        <div class="section-head no-padding">
            <div>
                <p class="eyebrow">Centre de messages</p>
                <h2>Toutes les notifications</h2>
            </div>
            <button class="btn btn-light" id="mark-all-read">Marquer tout comme lu</button>
        </div>

        <div class="notification-list clean-list">
            <?php foreach ($visibleNotifications as $notification): ?>
            <article class="notification-card rich-notification <?= $notification['is_read'] ? 'is-read' : '' ?>"
                     data-notif-id="<?= (int) $notification['id'] ?>">
                <span class="notif-round <?= htmlspecialchars($notification['color']) ?>">
                    <?= htmlspecialchars($notification['icon']) ?>
                </span>
                <div>
                    <h3><?= htmlspecialchars($notification['title']) ?> <small>•</small></h3>
                    <p><?= htmlspecialchars($notification['message']) ?></p>
                </div>
                <time><?= htmlspecialchars(
                    date('d/m H:i', strtotime($notification['created_at']))
                ) ?></time>
            </article>
            <?php endforeach; ?>
        </div>
    </section>

    <?php if ($selectedNotification): ?>
    <aside class="notification-detail panel">
        <div class="detail-heading">
            <h2><?= htmlspecialchars($selectedNotification['title'] ?? '') ?></h2>
            <span><?= htmlspecialchars($selectedNotification['category'] ?? '') ?></span>
        </div>
        <?php if ($detailDestination): ?>
        <img src="<?= htmlspecialchars($detailDestination['image']) ?>"
             alt="<?= htmlspecialchars($detailDestination['name']) ?>">
        <?php endif; ?>
        <?php if ($detailHotel): ?>
        <h3><?= htmlspecialchars($detailHotel['name']) ?></h3>
        <p><?= htmlspecialchars($detailDestination['name'] ?? '') ?> · <?= htmlspecialchars($detailDestination['country'] ?? '') ?></p>
        <?php endif; ?>
        <p><?= htmlspecialchars($selectedNotification['message'] ?? '') ?></p>
        <div class="notification-summary">
            <div><span>Arrivée</span><strong>15 juin 2026</strong></div>
            <div><span>Départ</span><strong>22 juin 2026</strong></div>
            <div><span>Voyageurs</span><strong>2 adultes</strong></div>
            <div><span>Chambre</span><strong>Suite Deluxe</strong></div>
        </div>
        <a class="btn btn-primary full"
           href="panier.php?destination=1&transport=1&hotel=1&activities=1,7">Voir ma réservation</a>
        <div class="notification-actions">
            <button type="button" id="mark-one-read"
                    data-id="<?= (int) ($selectedNotification['id'] ?? 0) ?>">Marquer comme lu</button>
            <button type="button" id="delete-one"
                    data-id="<?= (int) ($selectedNotification['id'] ?? 0) ?>">Supprimer</button>
        </div>
    </aside>
    <?php endif; ?>

</div>
<?php endif; ?>
</section>

<?php if ($loggedIn): ?>
<script>
async function notifAction(action, id = null) {
    return fetch('includes/api_notifications.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ action, id }),
    }).then(r => r.json());
}

document.getElementById('mark-all-read')?.addEventListener('click', async () => {
    await notifAction('read_all');
    location.reload();
});

document.getElementById('mark-one-read')?.addEventListener('click', async (e) => {
    await notifAction('read', Number(e.currentTarget.dataset.id));
    location.reload();
});

document.getElementById('delete-one')?.addEventListener('click', async (e) => {
    await notifAction('delete', Number(e.currentTarget.dataset.id));
    location.reload();
});
</script>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>
