<?php
require_once "includes/data.php";
$pageTitle = "Options par ville";

$city = trim($_GET["city"] ?? "Paris");
$type = $_GET["type"] ?? "from";
$routes = array_filter($cityRoutes, function ($route) use ($city, $type) {
    return $type === "to"
        ? stripos($route["to"], $city) !== false
        : stripos($route["from"], $city) !== false;
});

include "includes/header.php";
?>

<section class="page-section">
    <div class="section-head">
        <div>
            <p class="eyebrow">Explorer par ville</p>
            <h1><?= $type === "to" ? "Aller à " : "Partir depuis " ?><?= htmlspecialchars($city) ?></h1>
        </div>
        <a class="btn btn-light" href="index.php">Retour accueil</a>
    </div>

    <div class="catalog-grid">
        <?php if (empty($routes)): ?>
            <p class="empty-state">Aucune option directe trouvée pour cette ville.</p>
        <?php endif; ?>

        <?php foreach ($routes as $route): ?>
            <?php
            $destination = findById($destinations, $route["destination_id"]);
            $transport = findById($transports, $route["transport_id"]);
            ?>
            <article class="travel-card">
                <img src="<?= htmlspecialchars($destination["image"]) ?>" alt="<?= htmlspecialchars($destination["name"]) ?>">
                <div class="card-body">
                    <span class="tag"><?= htmlspecialchars($transport["mode"]) ?></span>
                    <h3><?= htmlspecialchars($route["from"]) ?> → <?= htmlspecialchars($route["to"]) ?></h3>
                    <p><?= htmlspecialchars($transport["company"]) ?> · <?= htmlspecialchars($transport["duration"]) ?></p>
                    <p><?= htmlspecialchars($destination["description"]) ?></p>
                    <div class="meta-row">
                        <strong><?= money($transport["price"]) ?></strong>
                        <span>vers <?= htmlspecialchars($destination["name"]) ?></span>
                    </div>
                    <a class="btn btn-primary full" href="composer_transport.php?destination=<?= (int) $destination["id"] ?>&departure=<?= urlencode($route["from"]) ?>&arrival=<?= urlencode($route["to"]) ?>">Composer ce trajet</a>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
</section>

<?php include "includes/footer.php"; ?>
