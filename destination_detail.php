<?php
require_once "includes/data.php";
$id = $_GET["id"] ?? 1;
$destination = findById($destinations, $id);
$linkedTransports = byDestination($transports, $destination["id"]);
$linkedHotels = byDestination($hotels, $destination["id"]);
$linkedActivities = byDestination($activities, $destination["id"]);
$pageTitle = $destination["name"];
include "includes/header.php";
?>

<section class="detail-hero" style="background-image: linear-gradient(90deg, rgba(13, 34, 64, .82), rgba(13, 34, 64, .28)), url('<?= htmlspecialchars($destination["image"]) ?>');">
    <div>
        <p class="eyebrow light"><?= htmlspecialchars($destination["category"]) ?></p>
        <h1><?= htmlspecialchars($destination["name"]) ?></h1>
        <p><?= htmlspecialchars($destination["country"]) ?> · <?= htmlspecialchars($destination["duration"]) ?> · Note <?= htmlspecialchars($destination["rating"]) ?>/5</p>
        <a class="btn btn-primary" href="composer_transport.php?destination=<?= (int) $destination["id"] ?>&arrival=<?= urlencode($destination["name"]) ?>&departure=Paris&adults=2&children=0&nights=7">Composer ce séjour</a>
    </div>
</section>

<div class="detail-layout">
    <section class="panel">
        <h2>Présentation</h2>
        <p><?= htmlspecialchars($destination["description"]) ?></p>
        <div class="info-grid">
            <div><span>Durée conseillée</span><strong><?= htmlspecialchars($destination["duration"]) ?></strong></div>
            <div><span>Prix de base</span><strong><?= money($destination["price"]) ?></strong></div>
            <div><span>Note moyenne</span><strong><?= htmlspecialchars($destination["rating"]) ?>/5</strong></div>
        </div>

        <form class="trip-quick-form" action="composer_transport.php" method="get">
            <h3>Paramétrer le séjour</h3>
            <input type="hidden" name="destination" value="<?= (int) $destination["id"] ?>">
            <input type="hidden" name="arrival" value="<?= htmlspecialchars($destination["name"]) ?>">
            <label>Départ
                <input type="text" name="departure" value="Paris">
            </label>
            <label>Adultes
                <input type="number" name="adults" min="1" max="8" value="2">
            </label>
            <label>Enfants
                <input type="number" name="children" min="0" max="6" value="0">
            </label>
            <label>Nuits
                <input type="number" name="nights" min="1" max="30" value="7">
            </label>
            <button class="btn btn-primary" type="submit">Composer ce séjour</button>
        </form>
    </section>

    <section class="panel">
        <h2>Transports liés</h2>
        <div class="list-grid">
            <?php foreach ($linkedTransports as $transport): ?>
                <article class="small-item">
                    <span class="tag"><?= htmlspecialchars($transport["mode"]) ?></span>
                    <h3><?= htmlspecialchars($transport["company"]) ?></h3>
                    <p><?= htmlspecialchars($transport["from"]) ?> vers <?= htmlspecialchars($transport["to"]) ?></p>
                    <strong><?= money($transport["price"]) ?></strong>
                    <a class="text-link" href="panier.php?destination=<?= (int) $destination["id"] ?>&transport=<?= (int) $transport["id"] ?>">Choisir ce transport</a>
                </article>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="panel">
        <h2>Hébergements liés</h2>
        <div class="list-grid">
            <?php foreach ($linkedHotels as $hotel): ?>
                <article class="small-item with-image">
                    <img src="<?= htmlspecialchars($hotel["image"]) ?>" alt="<?= htmlspecialchars($hotel["name"]) ?>">
                    <h3><?= htmlspecialchars($hotel["name"]) ?></h3>
                    <p><?= htmlspecialchars($hotel["type"]) ?> · <?= htmlspecialchars($hotel["rating"]) ?>/5</p>
                    <strong><?= money($hotel["price"]) ?> / nuit</strong>
                    <a class="text-link" href="panier.php?destination=<?= (int) $destination["id"] ?>&hotel=<?= (int) $hotel["id"] ?>">Choisir cet hébergement</a>
                </article>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="panel">
        <h2>Activités liées</h2>
        <div class="list-grid">
            <?php foreach ($linkedActivities as $activity): ?>
                <article class="small-item with-image" id="activity-<?= (int) $activity["id"] ?>">
                    <a href="activites.php?destination=<?= (int) $destination["id"] ?>#activity-<?= (int) $activity["id"] ?>">
                        <img src="<?= htmlspecialchars($activity["image"]) ?>" alt="<?= htmlspecialchars($activity["name"]) ?>">
                    </a>
                    <h3><a href="activites.php?destination=<?= (int) $destination["id"] ?>#activity-<?= (int) $activity["id"] ?>"><?= htmlspecialchars($activity["name"]) ?></a></h3>
                    <p><?= htmlspecialchars($activity["category"]) ?> · <?= htmlspecialchars($activity["duration"]) ?></p>
                    <strong><?= money($activity["price"]) ?></strong>
                    <a class="text-link" href="panier.php?destination=<?= (int) $destination["id"] ?>&activity=<?= (int) $activity["id"] ?>">Ajouter cette activité</a>
                </article>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="panel">
        <h2>Avis voyageurs</h2>
        <div class="review-grid">
            <?php foreach ($reviews as $review): ?>
                <article class="review-card">
                    <strong><?= str_repeat("⭐", (int) $review["rating"]) ?></strong>
                    <p><?= htmlspecialchars($review["text"]) ?></p>
                    <span><?= htmlspecialchars($review["name"]) ?> · <?= htmlspecialchars($review["destination"]) ?></span>
                </article>
            <?php endforeach; ?>
        </div>
    </section>
</div>

<?php include "includes/footer.php"; ?>
