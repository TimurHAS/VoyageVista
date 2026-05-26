<?php
require_once "includes/data.php";
$pageTitle = "Résultats";

$service = $_GET["service"] ?? "vacances";
$departure = trim($_GET["departure"] ?? "");
$arrival = trim($_GET["arrival"] ?? "");
$category = trim($_GET["category"] ?? "");
$budget = isset($_GET["budget"]) && $_GET["budget"] !== "" ? (int) $_GET["budget"] : 1600;
$destinationId = isset($_GET["destination"]) ? (int) $_GET["destination"] : 0;
$tripType = $_GET["trip_type"] ?? "aller-retour";
$adults = max(1, (int) ($_GET["adults"] ?? ($_GET["persons"] ?? 2)));
$children = max(0, (int) ($_GET["children"] ?? 0));
$persons = max(1, $adults + $children);
$rooms = max(1, (int) ($_GET["rooms"] ?? 1));
$nights = 7;

$selectedActivityIds = [];
if (!empty($_GET["activities"])) {
    $selectedActivityIds = array_filter(array_map("intval", explode(",", $_GET["activities"])));
} elseif (!empty($_GET["activity"])) {
    $selectedActivityIds = [(int) $_GET["activity"]];
}

function resultUrl($updates = [], $remove = [])
{
    $params = $_GET;
    foreach ($remove as $key) {
        unset($params[$key]);
    }
    foreach ($updates as $key => $value) {
        if ($value === null || $value === "") {
            unset($params[$key]);
        } else {
            $params[$key] = $value;
        }
    }

    return "resultats.php?" . http_build_query($params);
}

function cartUrl($destinationId, $transport, $hotel, $activityIds, $persons, $nights, $adults = 2, $children = 0)
{
    $params = ["destination" => $destinationId, "persons" => $persons, "adults" => $adults, "children" => $children, "nights" => $nights];
    if ($transport) {
        $params["transport"] = $transport["id"];
    }
    if ($hotel) {
        $params["hotel"] = $hotel["id"];
    }
    if (!empty($activityIds)) {
        $params["activities"] = implode(",", $activityIds);
    }

    return "panier.php?" . http_build_query($params);
}

$filteredDestinations = array_filter($destinations, function ($destination) use ($arrival, $category, $budget) {
    $matchesArrival = $arrival === ""
        || stripos($destination["name"], $arrival) !== false
        || stripos($destination["country"], $arrival) !== false
        || stripos($destination["category"], $arrival) !== false;
    $matchesCategory = $category === "" || $destination["category"] === $category;
    $matchesBudget = $destination["price"] <= $budget;

    return $matchesArrival && $matchesCategory && $matchesBudget;
});

$selectedDestination = $destinationId ? findById($destinations, $destinationId) : null;
$linkedTransports = $selectedDestination ? byDestination($transports, $selectedDestination["id"]) : [];
$linkedHotels = $selectedDestination ? byDestination($hotels, $selectedDestination["id"]) : [];
$linkedActivities = $selectedDestination ? byDestination($activities, $selectedDestination["id"]) : [];
$selectedTransport = !empty($_GET["transport"]) ? findById($linkedTransports, (int) $_GET["transport"]) : null;
$selectedHotel = !empty($_GET["hotel"]) ? findById($linkedHotels, (int) $_GET["hotel"]) : null;
$selectedActivities = array_values(array_filter($linkedActivities, function ($activity) use ($selectedActivityIds) {
    return in_array((int) $activity["id"], $selectedActivityIds, true);
}));

$transportTotal = $selectedTransport ? $selectedTransport["price"] * $persons : 0;
$hotelTotal = $selectedHotel ? $selectedHotel["price"] * $nights : 0;
$activitiesTotal = array_sum(array_map(function ($activity) use ($persons) {
    return $activity["price"] * $persons;
}, $selectedActivities));
$cartTotal = $transportTotal + $hotelTotal + $activitiesTotal;

include "includes/header.php";
?>

<datalist id="city-list">
    <?php foreach ($cities as $city): ?>
        <option value="<?= htmlspecialchars($city) ?>"></option>
    <?php endforeach; ?>
</datalist>

<div class="catalog-layout results-layout">
    <aside class="filters">
        <h1>Filtres</h1>
        <form method="get" class="filter-form">
            <input type="hidden" name="service" value="<?= htmlspecialchars($service) ?>">
            <label>
                Départ
                <input type="text" name="departure" list="city-list" data-city-autocomplete value="<?= htmlspecialchars($departure) ?>" placeholder="Paris">
            </label>
            <label>
                Arrivée
                <input type="text" name="arrival" list="city-list" data-city-autocomplete value="<?= htmlspecialchars($arrival) ?>" placeholder="Bali">
            </label>
            <label>
                Catégorie
                <select name="category">
                    <option value="">Toutes</option>
                    <?php foreach (uniqueValues($destinations, "category") as $value): ?>
                        <option value="<?= htmlspecialchars($value) ?>" <?= $category === $value ? "selected" : "" ?>>
                            <?= htmlspecialchars($value) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>
            <label>
                Budget maximum
                <input type="range" name="budget" min="300" max="1600" step="50" value="<?= (int) $budget ?>" data-range-output="result-budget">
                <output id="result-budget"><?= money($budget) ?></output>
            </label>
            <button class="btn btn-primary" type="submit">Mettre à jour</button>
        </form>
    </aside>

    <section class="catalog-content">
        <div class="section-head compact">
            <div>
                <p class="eyebrow">Résultats <?= htmlspecialchars($service) ?></p>
                <h2><?= $selectedDestination ? "Composer " . htmlspecialchars($selectedDestination["name"]) : "Choisir une destination" ?></h2>
            </div>
            <?php if ($selectedDestination): ?>
                <a class="btn btn-light" href="resultats.php?service=<?= urlencode($service) ?>&departure=<?= urlencode($departure) ?>&arrival=<?= urlencode($arrival) ?>">Changer de destination</a>
            <?php endif; ?>
        </div>

        <?php if (!$selectedDestination): ?>
            <div class="catalog-grid">
                <?php if (empty($filteredDestinations)): ?>
                    <p class="empty-state">Aucune destination ne correspond à votre recherche.</p>
                <?php endif; ?>

                <?php foreach ($filteredDestinations as $destination): ?>
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
                            <p><?= htmlspecialchars($destination["country"]) ?> · <?= htmlspecialchars($destination["duration"]) ?></p>
                            <p><?= htmlspecialchars($destination["description"]) ?></p>
                            <div class="price-stack">
                                <s><?= money($destination["old_price"]) ?></s>
                                <strong>Dès <?= money($destination["price"]) ?></strong>
                            </div>
                            <div class="card-actions">
                                <a class="btn btn-primary" href="composer_transport.php?destination=<?= (int) $destination["id"] ?>&arrival=<?= urlencode($destination["name"]) ?>&departure=<?= urlencode($departure ?: "Paris") ?>&adults=<?= (int) $adults ?>&children=<?= (int) $children ?>&persons=<?= (int) $persons ?>&date_depart=<?= urlencode($_GET["date_depart"] ?? "") ?>&date_retour=<?= urlencode($_GET["date_retour"] ?? "") ?>">Composer</a>
                                <a class="btn btn-secondary" href="activites.php?destination=<?= (int) $destination["id"] ?>">Activités liées</a>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="compose-grid">
                <div class="compose-main">
                    <article class="selected-trip panel">
                        <img src="<?= htmlspecialchars($selectedDestination["image"]) ?>" alt="<?= htmlspecialchars($selectedDestination["name"]) ?>">
                        <div>
                            <p class="eyebrow"><?= htmlspecialchars($selectedDestination["pack"]) ?></p>
                            <h3><?= htmlspecialchars($selectedDestination["name"]) ?>, <?= htmlspecialchars($selectedDestination["country"]) ?></h3>
                            <p><?= htmlspecialchars($departure ?: "Votre ville") ?> → <?= htmlspecialchars($selectedDestination["name"]) ?> · <?= htmlspecialchars($tripType) ?> · <?= (int) $adults ?> adulte(s) · <?= (int) $children ?> enfant(s) · <?= $rooms ?> chambre(s)</p>
                        </div>
                    </article>

                    <section class="step-panel panel">
                        <div class="step-title"><span>1</span><h3>Vol aller/retour ou transport</h3></div>
                        <div class="choice-grid">
                            <?php foreach ($linkedTransports as $transport): ?>
                                <article class="choice-card <?= $selectedTransport && (int) $selectedTransport["id"] === (int) $transport["id"] ? "selected" : "" ?>">
                                    <span class="tag"><?= htmlspecialchars($transport["mode"]) ?></span>
                                    <h4><?= htmlspecialchars($transport["company"]) ?></h4>
                                    <p><?= htmlspecialchars($transport["from"]) ?> → <?= htmlspecialchars($transport["to"]) ?></p>
                                    <p><?= htmlspecialchars($transport["duration"]) ?> · <?= htmlspecialchars($tripType) ?></p>
                                    <strong><?= money($transport["price"]) ?> / pers.</strong>
                                    <a class="btn btn-primary full" href="<?= resultUrl(["transport" => $transport["id"]]) ?>">Choisir</a>
                                </article>
                            <?php endforeach; ?>
                        </div>
                    </section>

                    <section class="step-panel panel">
                        <div class="step-title"><span>2</span><h3>Hébergement</h3></div>
                        <div class="choice-grid">
                            <?php foreach ($linkedHotels as $hotel): ?>
                                <article class="choice-card with-image <?= $selectedHotel && (int) $selectedHotel["id"] === (int) $hotel["id"] ? "selected" : "" ?>">
                                    <img src="<?= htmlspecialchars($hotel["image"]) ?>" alt="<?= htmlspecialchars($hotel["name"]) ?>">
                                    <span class="tag"><?= htmlspecialchars($hotel["type"]) ?></span>
                                    <h4><?= htmlspecialchars($hotel["name"]) ?></h4>
                                    <p>⭐ <?= htmlspecialchars($hotel["rating"]) ?> · <?= $rooms ?> chambre(s)</p>
                                    <strong><?= money($hotel["price"]) ?> / nuit</strong>
                                    <a class="btn btn-primary full" href="<?= resultUrl(["hotel" => $hotel["id"]]) ?>">Choisir</a>
                                </article>
                            <?php endforeach; ?>
                        </div>
                    </section>

                    <section class="step-panel panel">
                        <div class="step-title"><span>3</span><h3>Activités liées</h3></div>
                        <div class="choice-grid">
                            <?php foreach ($linkedActivities as $activity): ?>
                                <?php
                                $isSelected = in_array((int) $activity["id"], $selectedActivityIds, true);
                                $newActivityIds = $selectedActivityIds;
                                if (!$isSelected) {
                                    $newActivityIds[] = (int) $activity["id"];
                                }
                                ?>
                                <article class="choice-card with-image <?= $isSelected ? "selected" : "" ?>">
                                    <a href="activites.php?destination=<?= (int) $selectedDestination["id"] ?>#activity-<?= (int) $activity["id"] ?>">
                                        <img src="<?= htmlspecialchars($activity["image"]) ?>" alt="<?= htmlspecialchars($activity["name"]) ?>">
                                    </a>
                                    <span class="tag"><?= htmlspecialchars($activity["category"]) ?></span>
                                    <h4><a href="activites.php?destination=<?= (int) $selectedDestination["id"] ?>#activity-<?= (int) $activity["id"] ?>"><?= htmlspecialchars($activity["name"]) ?></a></h4>
                                    <p><?= htmlspecialchars($activity["duration"]) ?></p>
                                    <strong><?= money($activity["price"]) ?> / pers.</strong>
                                    <a class="btn <?= $isSelected ? "btn-secondary" : "btn-primary" ?> full" href="<?= resultUrl(["activities" => implode(",", $newActivityIds)], ["activity"]) ?>">
                                        <?= $isSelected ? "Ajoutée" : "Ajouter" ?>
                                    </a>
                                </article>
                            <?php endforeach; ?>
                        </div>
                    </section>
                </div>

                <aside class="compose-cart panel">
                    <p class="eyebrow">Votre panier</p>
                    <h3>À finaliser</h3>
                    <div class="summary-line">
                        <span>Transport</span>
                        <strong><?= $selectedTransport ? htmlspecialchars($selectedTransport["company"]) . " · " . money($transportTotal) : "Optionnel" ?></strong>
                    </div>
                    <div class="summary-line">
                        <span>Hébergement</span>
                        <strong><?= $selectedHotel ? htmlspecialchars($selectedHotel["name"]) . " · " . money($hotelTotal) : "Optionnel" ?></strong>
                    </div>
                    <div class="summary-line">
                        <span>Activités</span>
                        <strong><?= count($selectedActivities) ? count($selectedActivities) . " activité(s) · " . money($activitiesTotal) : "Optionnel" ?></strong>
                    </div>
                    <div class="summary-line total">
                        <span>Total</span>
                        <strong><?= money($cartTotal) ?></strong>
                    </div>
                    <a class="btn btn-primary full" href="<?= cartUrl($selectedDestination["id"], $selectedTransport, $selectedHotel, $selectedActivityIds, $persons, $nights, $adults, $children) ?>">Voir le panier</a>
                </aside>
            </div>
        <?php endif; ?>
    </section>
</div>

<?php include "includes/footer.php"; ?>
