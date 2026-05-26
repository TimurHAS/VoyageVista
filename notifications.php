<?php
require_once "includes/data.php";
$pageTitle = "Notifications";

$category = trim($_GET["category"] ?? "Toutes");
$visibleNotifications = array_filter($notifications, function ($notification) use ($category) {
    return $category === "Toutes" || $notification["category"] === $category;
});

$notificationCategories = ["Toutes", "Réservations", "Trajets", "Hébergements", "Activités", "Promotions", "Système"];
$selectedNotification = array_values($visibleNotifications)[0] ?? $notifications[0];
$detailDestination = findById($destinations, 1);
$detailHotel = findById($hotels, 1);
?>
<?php include "includes/header.php"; ?>

<section data-auth-required>
<div class="center-page" data-auth-locked>
    <div class="panel auth-locked">
        <p class="eyebrow">Accès connecté</p>
        <h1>Connectez-vous pour voir vos notifications</h1>
        <p>Les alertes de réservation, trajet, hébergement et activité sont disponibles dans votre espace.</p>
        <a class="btn btn-primary" href="compte.php">Se connecter</a>
    </div>
</div>

<div class="notifications-board" data-auth-content>
    <aside class="notification-sidebar panel">
        <h1>🔔 Notifications</h1>
        <nav class="side-nav notification-nav">
            <?php foreach ($notificationCategories as $item): ?>
                <?php
                $count = $item === "Toutes"
                    ? count($notifications)
                    : count(array_filter($notifications, fn($notification) => $notification["category"] === $item));
                ?>
                <a class="<?= $category === $item ? "active" : "" ?>" href="notifications.php?category=<?= urlencode($item) ?>">
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
            <button class="btn btn-light" type="button" data-toast-button="Toutes les notifications sont marquées comme lues.">Marquer tout comme lu</button>
        </div>

        <div class="notification-list clean-list">
            <?php foreach ($visibleNotifications as $notification): ?>
                <article class="notification-card rich-notification">
                    <span class="notif-round <?= htmlspecialchars($notification["color"]) ?>"><?= htmlspecialchars($notification["icon"]) ?></span>
                    <div>
                        <h3><?= htmlspecialchars($notification["title"]) ?> <small>•</small></h3>
                        <p><?= htmlspecialchars($notification["message"]) ?></p>
                    </div>
                    <time><?= htmlspecialchars($notification["date"]) ?></time>
                </article>
            <?php endforeach; ?>
        </div>
    </section>

    <aside class="notification-detail panel">
        <div class="detail-heading">
            <h2><?= htmlspecialchars($selectedNotification["title"]) ?></h2>
            <span><?= htmlspecialchars($selectedNotification["category"]) ?></span>
        </div>
        <img src="<?= htmlspecialchars($detailDestination["image"]) ?>" alt="<?= htmlspecialchars($detailDestination["name"]) ?>">
        <h3><?= htmlspecialchars($detailHotel["name"]) ?></h3>
        <p><?= htmlspecialchars($detailDestination["name"]) ?> · <?= htmlspecialchars($detailDestination["country"]) ?></p>
        <p><?= htmlspecialchars($selectedNotification["message"]) ?></p>
        <div class="notification-summary">
            <div><span>Arrivée</span><strong>15 juin 2026</strong></div>
            <div><span>Départ</span><strong>22 juin 2026</strong></div>
            <div><span>Voyageurs</span><strong>2 adultes</strong></div>
            <div><span>Chambre</span><strong>Suite Deluxe</strong></div>
        </div>
        <a class="btn btn-primary full" href="panier.php?destination=1&transport=1&hotel=1&activities=1,7">Voir ma réservation</a>
        <div class="notification-actions">
            <button type="button" data-toast-button="Notification marquée comme lue.">Marquer comme lu</button>
            <button type="button" data-toast-button="Notification supprimée.">Supprimer</button>
        </div>
    </aside>
</div>
</section>

<?php include "includes/footer.php"; ?>
