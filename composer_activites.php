<?php
require_once "includes/data.php";
$pageTitle = "Choix des activités";

$destinationId = isset($_GET["destination"]) ? (int) $_GET["destination"] : 1;
$destination = findById($destinations, $destinationId);
$linkedActivities = byDestination($activities, $destination["id"]);
$persons = max(1, (int) ($_GET["persons"] ?? 2));
$adults = max(1, (int) ($_GET["adults"] ?? $persons));
$children = max(0, (int) ($_GET["children"] ?? 0));
$persons = max(1, $adults + $children);
$nights = max(1, (int) ($_GET["nights"] ?? 7));
$selectedIds = [];
if (!empty($_GET["activities"])) {
    $selectedIds = array_filter(array_map("intval", explode(",", $_GET["activities"])));
}

$category = trim($_GET["category"] ?? "");
$period = trim($_GET["period"] ?? "");
$maxPrice = isset($_GET["price"]) && $_GET["price"] !== "" ? (int) $_GET["price"] : 140;
$rating = isset($_GET["rating"]) && $_GET["rating"] !== "" ? (float) $_GET["rating"] : 0;
$sort = $_GET["sort"] ?? "recommended";

$filteredActivities = array_filter($linkedActivities, function ($activity) use ($category, $period, $maxPrice, $rating) {
    $matchesCategory = $category === "" || $activity["category"] === $category;
    $matchesPeriod = $period === "" || $activity["period"] === $period;
    $matchesPrice = $activity["price"] <= $maxPrice;
    $matchesRating = $rating <= 0 || $activity["rating"] >= $rating;

    return $matchesCategory && $matchesPeriod && $matchesPrice && $matchesRating;
});

usort($filteredActivities, function ($a, $b) use ($sort) {
    return match ($sort) {
        "price" => $a["price"] <=> $b["price"],
        "rating" => $b["rating"] <=> $a["rating"],
        "reviews" => $b["reviews"] <=> $a["reviews"],
        default => [$b["rating"], $b["reviews"]] <=> [$a["rating"], $a["reviews"]],
    };
});

$selectedActivities = array_values(array_filter($linkedActivities, function ($activity) use ($selectedIds) {
    return in_array((int) $activity["id"], $selectedIds, true);
}));

$journeyParams = [
    "destination" => $destination["id"],
    "departure" => $_GET["departure"] ?? "Paris",
    "arrival" => $_GET["arrival"] ?? $destination["name"],
    "persons" => $persons,
    "adults" => $adults,
    "children" => $children,
    "nights" => $nights,
    "date_depart" => $_GET["date_depart"] ?? "",
    "date_retour" => $_GET["date_retour"] ?? "",
    "transport" => $_GET["transport"] ?? "",
    "bags" => $_GET["bags"] ?? "",
    "ticket" => $_GET["ticket"] ?? "",
    "seat" => $_GET["seat"] ?? "",
    "hotel" => $_GET["hotel"] ?? "",
    "activities" => implode(",", $selectedIds)
];

function composerActivityUrl($page, $extra = [], $remove = [])
{
    $params = $_GET;
    foreach ($remove as $key) {
        unset($params[$key]);
    }
    foreach ($extra as $key => $value) {
        if ($value === "") {
            unset($params[$key]);
        } else {
            $params[$key] = $value;
        }
    }

    return $page . "?" . http_build_query($params);
}

function hiddenActivityInputs($params)
{
    foreach ($params as $key => $value) {
        echo '<input type="hidden" name="' . htmlspecialchars($key) . '" value="' . htmlspecialchars((string) $value) . '">' . PHP_EOL;
    }
}

include "includes/header.php";
?>

<section class="composer-page">
    <div class="composer-heading">
        <p class="eyebrow">Étape 3 sur 4</p>
        <h1>Ajoutez des activités à <?= htmlspecialchars($destination["name"]) ?></h1>
        <p>Vous pouvez en choisir plusieurs ou passer cette étape.</p>
    </div>

    <div class="composer-progress">
        <span class="done">1 Transport</span>
        <span class="done">2 Hébergement</span>
        <span class="active">3 Activités</span>
        <span>4 Récapitulatif</span>
    </div>

    <div class="category-pills composer-tabs">
        <a class="<?= $category === "" ? "active" : "" ?>" href="<?= composerActivityUrl("composer_activites.php", ["category" => ""], ["category"]) ?>">Toutes</a>
        <?php foreach (uniqueValues($linkedActivities, "category") as $value): ?>
            <a class="<?= $category === $value ? "active" : "" ?>" href="<?= composerActivityUrl("composer_activites.php", ["category" => $value]) ?>"><?= htmlspecialchars($value) ?></a>
        <?php endforeach; ?>
    </div>

    <div class="composer-layout composer-with-filters">
        <aside class="filters composer-filters">
            <h1>Filtrer les activités</h1>
            <form method="get" class="filter-form">
                <?php hiddenActivityInputs($journeyParams); ?>
                <label>
                    Catégorie
                    <select name="category">
                        <option value="">Toutes</option>
                        <?php foreach (uniqueValues($linkedActivities, "category") as $value): ?>
                            <option value="<?= htmlspecialchars($value) ?>" <?= $category === $value ? "selected" : "" ?>>
                                <?= htmlspecialchars($value) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <label>
                    Moment
                    <select name="period">
                        <option value="">Tous</option>
                        <?php foreach (uniqueValues($linkedActivities, "period") as $value): ?>
                            <option value="<?= htmlspecialchars($value) ?>" <?= $period === $value ? "selected" : "" ?>>
                                <?= htmlspecialchars($value) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <label>
                    Prix par personne
                    <input type="range" name="price" min="20" max="140" step="5" value="<?= (int) $maxPrice ?>" data-range-output="composer-activity-price">
                    <output id="composer-activity-price"><?= money($maxPrice) ?></output>
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
                <button class="btn btn-primary" type="submit">Appliquer</button>
                <a class="btn btn-light" href="composer_activites.php?<?= http_build_query($journeyParams) ?>">Réinitialiser</a>
            </form>
        </aside>

        <section class="composer-list">
            <div class="section-head no-padding">
                <div>
                    <p class="eyebrow"><?= count($filteredActivities) ?> activité(s)</p>
                    <h2>Expériences disponibles</h2>
                </div>
                <form method="get" class="sort-form">
                    <?php hiddenActivityInputs(array_merge($journeyParams, ["category" => $category, "period" => $period, "price" => $maxPrice, "rating" => $rating])); ?>
                    <label>
                        Trier par
                        <select name="sort" onchange="this.form.submit()">
                            <option value="recommended" <?= $sort === "recommended" ? "selected" : "" ?>>Recommandé</option>
                            <option value="price" <?= $sort === "price" ? "selected" : "" ?>>Prix</option>
                            <option value="rating" <?= $sort === "rating" ? "selected" : "" ?>>Note</option>
                            <option value="reviews" <?= $sort === "reviews" ? "selected" : "" ?>>Avis</option>
                        </select>
                    </label>
                </form>
            </div>

            <?php if (!$filteredActivities): ?>
                <div class="empty-state">Aucune activité ne correspond à ces filtres.</div>
            <?php endif; ?>

            <div class="activity-grid-large composer-activity-grid">
                <?php foreach ($filteredActivities as $activity): ?>
                    <?php
                    $isSelected = in_array((int) $activity["id"], $selectedIds, true);
                    $nextIds = $selectedIds;
                    if ($isSelected) {
                        $nextIds = array_values(array_filter($nextIds, fn($id) => (int) $id !== (int) $activity["id"]));
                    } else {
                        $nextIds[] = (int) $activity["id"];
                    }
                    ?>
                    <article class="travel-card <?= $isSelected ? "selected-card" : "" ?>">
                        <img src="<?= htmlspecialchars($activity["image"]) ?>" alt="<?= htmlspecialchars($activity["name"]) ?>">
                        <div class="card-body">
                            <span class="tag"><?= htmlspecialchars($activity["category"]) ?></span>
                            <h3><?= htmlspecialchars($activity["name"]) ?></h3>
                            <p><?= htmlspecialchars($activity["duration"]) ?> · <?= htmlspecialchars($activity["period"]) ?> · ⭐ <?= htmlspecialchars($activity["rating"]) ?> (<?= (int) $activity["reviews"] ?> avis)</p>
                            <div class="icon-meta">
                                <?php foreach ($activity["included"] as $included): ?>
                                    <span><?= htmlspecialchars($included) ?></span>
                                <?php endforeach; ?>
                            </div>
                            <span class="price-box"><strong><?= money($activity["price"]) ?></strong><small>par personne</small></span>
                            <a class="btn <?= $isSelected ? "btn-secondary" : "btn-primary" ?> full" href="<?= composerActivityUrl("composer_activites.php", ["activities" => implode(",", $nextIds)]) ?>">
                                <?= $isSelected ? "Retirer" : "Ajouter" ?>
                            </a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>

        <aside class="composer-side panel">
            <h2>Votre séjour</h2>
            <div class="stay-summary compact">
                <div><span>Adultes</span><strong><?= (int) $adults ?></strong></div>
                <div><span>Enfants</span><strong><?= (int) $children ?></strong></div>
                <div><span>Nuits</span><strong><?= (int) $nights ?></strong></div>
                <div><span>Personnes</span><strong><?= (int) $persons ?></strong></div>
            </div>
            <h2>Activités sélectionnées</h2>
            <p><?= count($selectedIds) ?> activité(s)</p>
            <?php if ($selectedActivities): ?>
                <div class="mini-selected-list">
                    <?php foreach ($selectedActivities as $activity): ?>
                        <article>
                            <img src="<?= htmlspecialchars($activity["image"]) ?>" alt="<?= htmlspecialchars($activity["name"]) ?>">
                            <div>
                                <strong><?= htmlspecialchars($activity["name"]) ?></strong>
                                <span><?= money($activity["price"]) ?> / pers.</span>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            <a class="btn btn-primary full" href="<?= composerActivityUrl("composer_recap.php", ["activities" => implode(",", $selectedIds)], ["category", "period", "price", "rating", "sort"]) ?>">Voir le récapitulatif</a>
            <a class="btn btn-light full" href="<?= composerActivityUrl("composer_recap.php", ["activities" => ""], ["category", "period", "price", "rating", "sort"]) ?>">Passer les activités</a>
        </aside>
    </div>
</section>

<?php include "includes/footer.php"; ?>
