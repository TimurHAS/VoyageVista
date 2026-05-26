<?php
require_once "includes/data.php";
$pageTitle = "Destinations";

$search = trim($_GET["search"] ?? "");
$category = trim($_GET["category"] ?? "");
$budget = isset($_GET["budget"]) && $_GET["budget"] !== "" ? (int) $_GET["budget"] : 1600;
$duration = isset($_GET["duration"]) && $_GET["duration"] !== "" ? (int) $_GET["duration"] : 15;
$stayType = trim($_GET["stay_type"] ?? "");
$hotelStars = isset($_GET["stars"]) && $_GET["stars"] !== "" ? (int) $_GET["stars"] : 0;
$sort = $_GET["sort"] ?? "popular";

$popularDestinations = $destinations;
usort($popularDestinations, fn($a, $b) => ($b["reviews"] ?? 0) <=> ($a["reviews"] ?? 0));
$popularDestinations = array_slice($popularDestinations, 0, 4);

$filteredDestinations = array_filter($destinations, function ($destination) use ($search, $category, $budget, $duration, $stayType, $hotelStars, $hotels) {
    $destinationHotels = byDestination($hotels, $destination["id"]);
    $bestHotelStars = 0;
    foreach ($destinationHotels as $hotel) {
        $bestHotelStars = max($bestHotelStars, (int) ($hotel["stars"] ?? 0));
    }

    $matchesSearch = $search === ""
        || stripos($destination["name"], $search) !== false
        || stripos($destination["country"], $search) !== false
        || stripos($destination["description"], $search) !== false;
    $matchesCategory = $category === "" || $destination["category"] === $category;
    $matchesBudget = $destination["price"] <= $budget;
    $matchesDuration = $destination["duration_days"] <= $duration;
    $matchesStayType = $stayType === "" || $destination["stay_type"] === $stayType;
    $matchesStars = $hotelStars <= 0 || $bestHotelStars >= $hotelStars;

    return $matchesSearch && $matchesCategory && $matchesBudget && $matchesDuration && $matchesStayType && $matchesStars;
});

usort($filteredDestinations, function ($a, $b) use ($sort) {
    return match ($sort) {
        "price_asc" => $a["price"] <=> $b["price"],
        "price_desc" => $b["price"] <=> $a["price"],
        "rating" => $b["rating"] <=> $a["rating"],
        "recommendation" => $b["recommendation"] <=> $a["recommendation"],
        "country" => strcmp($a["country"], $b["country"]),
        default => $b["reviews"] <=> $a["reviews"],
    };
});

include "includes/header.php";
?>

<datalist id="city-list">
    <?php foreach ($cities as $city): ?>
        <option value="<?= htmlspecialchars($city) ?>"></option>
    <?php endforeach; ?>
</datalist>

<div class="catalog-layout destinations-page">
    <aside class="filters">
        <h1>Explorer</h1>
        <form method="get" class="filter-form">
            <label>
                Recherche
                <input type="search" name="search" list="city-list" data-city-autocomplete value="<?= htmlspecialchars($search) ?>" placeholder="Destination, pays, ambiance, hôtel">
            </label>
            <label>
                Catégorie
                <select name="category">
                    <option value="">Toutes les destinations</option>
                    <?php foreach (uniqueValues($destinations, "category") as $value): ?>
                        <option value="<?= htmlspecialchars($value) ?>" <?= $category === $value ? "selected" : "" ?>>
                            <?= htmlspecialchars($value) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>
            <label>
                Budget
                <input type="range" name="budget" min="300" max="1600" step="50" value="<?= (int) $budget ?>" data-range-output="destination-budget">
                <output id="destination-budget"><?= money($budget) ?></output>
            </label>
            <label>
                Durée maximum
                <input type="range" name="duration" min="3" max="15" step="1" value="<?= (int) $duration ?>" data-days-output="destination-duration">
                <output id="destination-duration"><?= (int) $duration ?> jours</output>
            </label>
            <label>
                Type de séjour
                <select name="stay_type">
                    <option value="">Tous</option>
                    <?php foreach (uniqueValues($destinations, "stay_type") as $value): ?>
                        <option value="<?= htmlspecialchars($value) ?>" <?= $stayType === $value ? "selected" : "" ?>>
                            <?= htmlspecialchars($value) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>
            <label>
                Étoiles hôtel minimum
                <select name="stars">
                    <option value="0">Toutes</option>
                    <?php for ($i = 2; $i <= 5; $i++): ?>
                        <option value="<?= $i ?>" <?= $hotelStars === $i ? "selected" : "" ?>><?= $i ?>★ et plus</option>
                    <?php endfor; ?>
                </select>
            </label>
            <button class="btn btn-primary" type="submit">Filtrer</button>
            <a class="btn btn-light" href="destinations.php">Réinitialiser</a>
        </form>
    </aside>

    <section class="catalog-content">
        <section class="popular-strip panel">
            <div class="section-head no-padding">
                <div>
                    <p class="eyebrow">Le plus d'avis</p>
                    <h2>Destinations populaires</h2>
                </div>
                <form method="get" class="sort-form">
                    <input type="hidden" name="search" value="<?= htmlspecialchars($search) ?>">
                    <input type="hidden" name="category" value="<?= htmlspecialchars($category) ?>">
                    <input type="hidden" name="budget" value="<?= (int) $budget ?>">
                    <input type="hidden" name="duration" value="<?= (int) $duration ?>">
                    <input type="hidden" name="stay_type" value="<?= htmlspecialchars($stayType) ?>">
                    <input type="hidden" name="stars" value="<?= (int) $hotelStars ?>">
                    <label>
                        Trier par
                        <select name="sort" onchange="this.form.submit()">
                            <option value="popular" <?= $sort === "popular" ? "selected" : "" ?>>Avis</option>
                            <option value="recommendation" <?= $sort === "recommendation" ? "selected" : "" ?>>Recommandation</option>
                            <option value="price_asc" <?= $sort === "price_asc" ? "selected" : "" ?>>Prix croissant</option>
                            <option value="price_desc" <?= $sort === "price_desc" ? "selected" : "" ?>>Prix décroissant</option>
                            <option value="rating" <?= $sort === "rating" ? "selected" : "" ?>>Note</option>
                            <option value="country" <?= $sort === "country" ? "selected" : "" ?>>Pays</option>
                        </select>
                    </label>
                </form>
            </div>
            <div class="popular-row">
                <?php foreach ($popularDestinations as $destination): ?>
                    <a class="popular-mini" href="destination_detail.php?id=<?= (int) $destination["id"] ?>">
                        <img src="<?= htmlspecialchars($destination["image"]) ?>" alt="<?= htmlspecialchars($destination["name"]) ?>">
                        <span><?= htmlspecialchars($destination["category"]) ?></span>
                        <strong><?= htmlspecialchars($destination["name"]) ?></strong>
                        <small>⭐ <?= htmlspecialchars($destination["rating"]) ?> (<?= (int) $destination["reviews"] ?> avis)</small>
                    </a>
                <?php endforeach; ?>
            </div>
        </section>

        <div class="section-head compact">
            <div>
                <p class="eyebrow"><?= count($filteredDestinations) ?> résultat(s)</p>
                <h2>Séjours recommandés</h2>
            </div>
        </div>

        <div class="catalog-grid">
            <?php if (empty($filteredDestinations)): ?>
                <p class="empty-state">Aucune destination ne correspond aux filtres.</p>
            <?php endif; ?>

            <?php foreach ($filteredDestinations as $destination): ?>
                <?php
                $destinationHotels = byDestination($hotels, $destination["id"]);
                $bestHotel = minItem($destinationHotels);
                ?>
                <article class="travel-card">
                    <div class="image-wrap">
                        <img src="<?= htmlspecialchars($destination["image"]) ?>" alt="<?= htmlspecialchars($destination["name"]) ?>">
                        <span class="discount-badge"><?= htmlspecialchars($destination["discount"]) ?></span>
                    </div>
                    <div class="card-body">
                        <span class="tag"><?= htmlspecialchars($destination["category"]) ?></span>
                        <div class="card-title-row">
                            <h3><?= htmlspecialchars($destination["name"]) ?></h3>
                            <span>⭐ <?= htmlspecialchars($destination["rating"]) ?></span>
                        </div>
                        <p><?= htmlspecialchars($destination["country"]) ?> · <?= htmlspecialchars($destination["stay_type"]) ?></p>
                        <p><?= htmlspecialchars($destination["description"]) ?></p>
                        <div class="icon-meta">
                            <span>🕒 <?= htmlspecialchars($destination["duration"]) ?></span>
                            <span>🏨 <?= $bestHotel ? (int) $bestHotel["stars"] . "★" : "Hôtel" ?></span>
                            <span>💬 <?= (int) $destination["reviews"] ?> avis</span>
                        </div>
                        <div class="meta-row">
                            <span class="price-stack"><s><?= money($destination["old_price"]) ?></s><strong><?= money($destination["price"]) ?></strong></span>
                            <span><?= (int) $destination["recommendation"] ?>% recommandé</span>
                        </div>
                        <div class="card-actions">
                            <a class="btn btn-secondary" href="destination_detail.php?id=<?= (int) $destination["id"] ?>">Voir détail</a>
                            <a class="btn btn-primary" href="composer_transport.php?destination=<?= (int) $destination["id"] ?>&arrival=<?= urlencode($destination["name"]) ?>&departure=Paris">Composer</a>
                            <button class="btn btn-light" type="button" data-favorite="<?= htmlspecialchars($destination["name"]) ?>">❤️ Favori</button>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </section>
</div>

<?php include "includes/footer.php"; ?>
