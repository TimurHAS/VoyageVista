<?php
require_once "includes/data.php";
$pageTitle = "Activités";

$search = trim($_GET["search"] ?? "");
$destinationId = isset($_GET["destination"]) && $_GET["destination"] !== "" ? (int) $_GET["destination"] : 0;
$category = trim($_GET["category"] ?? "");
$period = trim($_GET["period"] ?? "");
$maxPrice = isset($_GET["price"]) && $_GET["price"] !== "" ? (int) $_GET["price"] : 140;
$rating = isset($_GET["rating"]) && $_GET["rating"] !== "" ? (float) $_GET["rating"] : 0;
$sort = $_GET["sort"] ?? "recommended";

$popularActivities = $activities;
usort($popularActivities, fn($a, $b) => ($b["reviews"] ?? 0) <=> ($a["reviews"] ?? 0));
$popularActivities = array_slice($popularActivities, 0, 4);

$filteredActivities = array_filter($activities, function ($activity) use ($search, $destinationId, $category, $period, $maxPrice, $rating, $destinations) {
    $destination = findById($destinations, $activity["destination_id"]);
    $haystack = implode(" ", [
        $activity["name"] ?? "",
        $activity["category"] ?? "",
        $activity["period"] ?? "",
        $activity["duration"] ?? "",
        $destination["name"] ?? "",
        $destination["country"] ?? "",
        implode(" ", $activity["included"] ?? [])
    ]);

    $matchesSearch = $search === "" || stripos($haystack, $search) !== false;
    $matchesDestination = $destinationId <= 0 || (int) $activity["destination_id"] === $destinationId;
    $matchesCategory = $category === "" || $activity["category"] === $category;
    $matchesPeriod = $period === "" || $activity["period"] === $period;
    $matchesPrice = $activity["price"] <= $maxPrice;
    $matchesRating = $rating <= 0 || $activity["rating"] >= $rating;

    return $matchesSearch && $matchesDestination && $matchesCategory && $matchesPeriod && $matchesPrice && $matchesRating;
});

usort($filteredActivities, function ($a, $b) use ($sort) {
    return match ($sort) {
        "price" => $a["price"] <=> $b["price"],
        "rating" => $b["rating"] <=> $a["rating"],
        "reviews" => $b["reviews"] <=> $a["reviews"],
        default => [$b["reviews"], $b["rating"]] <=> [$a["reviews"], $a["rating"]],
    };
});

include "includes/header.php";
?>

<datalist id="city-list">
    <?php foreach ($cities as $city): ?>
        <option value="<?= htmlspecialchars($city) ?>"></option>
    <?php endforeach; ?>
</datalist>

<div class="catalog-layout activities-page">
    <aside class="filters">
        <h1>Rechercher une activité</h1>
        <form method="get" class="filter-form">
            <label>
                Recherche
                <input type="search" name="search" list="city-list" data-city-autocomplete value="<?= htmlspecialchars($search) ?>" placeholder="Nom de l'activité, ville, expérience">
            </label>
            <label>
                Destination
                <select name="destination">
                    <option value="">Toutes</option>
                    <?php foreach ($destinations as $destination): ?>
                        <option value="<?= (int) $destination["id"] ?>" <?= $destinationId === (int) $destination["id"] ? "selected" : "" ?>>
                            <?= htmlspecialchars($destination["name"]) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>
            <label>
                Catégorie
                <select name="category">
                    <option value="">Toutes</option>
                    <?php foreach (uniqueValues($activities, "category") as $value): ?>
                        <option value="<?= htmlspecialchars($value) ?>" <?= $category === $value ? "selected" : "" ?>>
                            <?= htmlspecialchars($value) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>
            <label>
                Moment de la journée
                <select name="period">
                    <option value="">Tous</option>
                    <?php foreach (uniqueValues($activities, "period") as $value): ?>
                        <option value="<?= htmlspecialchars($value) ?>" <?= $period === $value ? "selected" : "" ?>>
                            <?= htmlspecialchars($value) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>
            <label>
                Prix par personne
                <input type="range" name="price" min="20" max="140" step="5" value="<?= (int) $maxPrice ?>" data-range-output="activity-price">
                <output id="activity-price"><?= money($maxPrice) ?></output>
            </label>
            <label>
                Note voyageurs
                <select name="rating">
                    <option value="0">Toutes</option>
                    <option value="4" <?= $rating === 4.0 ? "selected" : "" ?>>4+ et plus</option>
                    <option value="4.5" <?= $rating === 4.5 ? "selected" : "" ?>>4,5+ et plus</option>
                    <option value="4.8" <?= $rating === 4.8 ? "selected" : "" ?>>4,8+ et plus</option>
                </select>
            </label>
            <button class="btn btn-primary" type="submit">Rechercher</button>
            <a class="btn btn-light" href="activites.php">Réinitialiser</a>
        </form>
    </aside>

    <section class="catalog-content">
        <section class="popular-strip panel">
            <div class="section-head no-padding">
                <div>
                    <p class="eyebrow">Le plus d'avis</p>
                    <h2>Activités populaires</h2>
                </div>
                <form method="get" class="sort-form">
                    <input type="hidden" name="search" value="<?= htmlspecialchars($search) ?>">
                    <input type="hidden" name="destination" value="<?= (int) $destinationId ?>">
                    <input type="hidden" name="category" value="<?= htmlspecialchars($category) ?>">
                    <input type="hidden" name="period" value="<?= htmlspecialchars($period) ?>">
                    <input type="hidden" name="price" value="<?= (int) $maxPrice ?>">
                    <input type="hidden" name="rating" value="<?= htmlspecialchars((string) $rating) ?>">
                    <label>
                        Trier par
                        <select name="sort" onchange="this.form.submit()">
                            <option value="recommended" <?= $sort === "recommended" ? "selected" : "" ?>>Recommandé</option>
                            <option value="reviews" <?= $sort === "reviews" ? "selected" : "" ?>>Avis</option>
                            <option value="price" <?= $sort === "price" ? "selected" : "" ?>>Prix</option>
                            <option value="rating" <?= $sort === "rating" ? "selected" : "" ?>>Note</option>
                        </select>
                    </label>
                </form>
            </div>
            <div class="popular-row">
                <?php foreach ($popularActivities as $activity): ?>
                    <?php $destination = findById($destinations, $activity["destination_id"]); ?>
                    <a class="popular-mini" href="activity_detail.php?id=<?= (int) $activity["id"] ?>">
                        <img src="<?= htmlspecialchars($activity["image"]) ?>" alt="<?= htmlspecialchars($activity["name"]) ?>">
                        <span><?= htmlspecialchars($activity["category"]) ?> · <?= htmlspecialchars($destination["name"] ?? "") ?></span>
                        <strong><?= htmlspecialchars($activity["name"]) ?></strong>
                        <small>⭐ <?= htmlspecialchars($activity["rating"]) ?> · <?= (int) $activity["reviews"] ?> avis</small>
                    </a>
                <?php endforeach; ?>
            </div>
        </section>

        <div class="activity-hero panel">
            <div>
                <p class="eyebrow">Activités & expériences</p>
                <h2>Découvrez les expériences à réserver</h2>
                <p>Recherchez par nom d'activité, destination, catégorie ou moment de la journée.</p>
            </div>
        </div>

        <div class="category-pills">
            <a class="<?= $category === "" ? "active" : "" ?>" href="activites.php">Tous</a>
            <?php foreach (uniqueValues($activities, "category") as $value): ?>
                <a class="<?= $category === $value ? "active" : "" ?>" href="activites.php?category=<?= urlencode($value) ?>"><?= htmlspecialchars($value) ?></a>
            <?php endforeach; ?>
        </div>

        <div class="section-head compact">
            <div>
                <p class="eyebrow"><?= count($filteredActivities) ?> activité(s)</p>
                <h2>Activités disponibles</h2>
            </div>
        </div>

        <div class="activity-grid-large">
            <?php if (empty($filteredActivities)): ?>
                <p class="empty-state">Aucune activité ne correspond à votre recherche.</p>
            <?php endif; ?>

            <?php foreach ($filteredActivities as $activity): ?>
                <?php $destination = findById($destinations, $activity["destination_id"]); ?>
                <article class="travel-card" id="activity-<?= (int) $activity["id"] ?>">
                    <a href="activity_detail.php?id=<?= (int) $activity["id"] ?>">
                        <img src="<?= htmlspecialchars($activity["image"]) ?>" alt="<?= htmlspecialchars($activity["name"]) ?>">
                    </a>
                    <div class="card-body">
                        <span class="tag"><?= htmlspecialchars($activity["category"]) ?></span>
                        <h3><a href="activity_detail.php?id=<?= (int) $activity["id"] ?>"><?= htmlspecialchars($activity["name"]) ?></a></h3>
                        <p><?= htmlspecialchars($destination["name"]) ?> · <?= htmlspecialchars($activity["duration"]) ?> · <?= htmlspecialchars($activity["period"]) ?></p>
                        <div class="icon-meta">
                            <span>⭐ <?= htmlspecialchars($activity["rating"]) ?> (<?= (int) $activity["reviews"] ?> avis)</span>
                            <span><?= money($activity["price"]) ?> / pers.</span>
                        </div>
                        <a class="btn btn-primary full" href="activity_detail.php?id=<?= (int) $activity["id"] ?>">Ajouter</a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </section>
</div>

<?php include "includes/footer.php"; ?>
