<?php
require_once "includes/data.php";
$pageTitle = "Choix de l'hébergement";

$destinationId = isset($_GET["destination"]) ? (int) $_GET["destination"] : 1;
$destination = findById($destinations, $destinationId);
$departure = trim($_GET["departure"] ?? "Paris");
$arrival = trim($_GET["arrival"] ?? $destination["name"]);
$persons = max(1, (int) ($_GET["persons"] ?? 2));
$adults = max(1, (int) ($_GET["adults"] ?? $persons));
$children = max(0, (int) ($_GET["children"] ?? 0));
$persons = max(1, $adults + $children);
$nights = max(1, (int) ($_GET["nights"] ?? 7));
$dateDepart = $_GET["date_depart"] ?? "";
$dateRetour = $_GET["date_retour"] ?? "";
$linkedHotels = byDestination($hotels, $destination["id"]);
$transport = !empty($_GET["transport"]) ? findById(byDestination($transports, $destination["id"]), (int) $_GET["transport"]) : null;

$type = trim($_GET["type"] ?? "");
$maxPrice = isset($_GET["price"]) && $_GET["price"] !== "" ? (int) $_GET["price"] : 220;
$rooms = isset($_GET["rooms"]) && $_GET["rooms"] !== "" ? (int) $_GET["rooms"] : 1;
$airportDistance = isset($_GET["airport_distance"]) && $_GET["airport_distance"] !== "" ? (int) $_GET["airport_distance"] : 100;
$stars = isset($_GET["stars"]) && $_GET["stars"] !== "" ? (int) $_GET["stars"] : 0;
$amenities = $_GET["amenities"] ?? [];
if (!is_array($amenities)) {
    $amenities = [$amenities];
}
$sort = $_GET["sort"] ?? "recommended";

$amenityLabels = [
    "wifi" => "Wi-Fi",
    "piscine" => "Piscine",
    "petit_dejeuner" => "Petit-déjeuner inclus",
    "clim" => "Climatisation",
    "parking" => "Parking",
    "spa" => "Spa"
];

$filteredHotels = array_filter($linkedHotels, function ($hotel) use ($type, $maxPrice, $rooms, $airportDistance, $stars, $amenities) {
    $matchesType = $type === "" || $hotel["type"] === $type;
    $matchesPrice = $hotel["price"] <= $maxPrice;
    $matchesRooms = (int) $hotel["rooms"] >= $rooms;
    $matchesDistance = (int) $hotel["airport_distance"] <= $airportDistance;
    $matchesStars = $stars <= 0 || (int) $hotel["stars"] >= $stars;
    $matchesAmenities = empty($amenities) || empty(array_diff($amenities, $hotel["amenities"] ?? []));

    return $matchesType && $matchesPrice && $matchesRooms && $matchesDistance && $matchesStars && $matchesAmenities;
});

usort($filteredHotels, function ($a, $b) use ($sort) {
    return match ($sort) {
        "price_asc" => $a["price"] <=> $b["price"],
        "price_desc" => $b["price"] <=> $a["price"],
        "rating" => $b["rating"] <=> $a["rating"],
        default => [$b["stars"], $b["rating"]] <=> [$a["stars"], $a["rating"]],
    };
});

$previewSource = $filteredHotels ?: $linkedHotels;
$previewId = isset($_GET["hotel_preview"]) ? (int) $_GET["hotel_preview"] : (int) ($previewSource[0]["id"] ?? 0);
$previewHotel = $previewSource ? findById($previewSource, $previewId) : null;

$journeyParams = [
    "destination" => $destination["id"],
    "departure" => $departure,
    "arrival" => $arrival,
    "persons" => $persons,
    "adults" => $adults,
    "children" => $children,
    "nights" => $nights,
    "date_depart" => $dateDepart,
    "date_retour" => $dateRetour,
    "transport" => $_GET["transport"] ?? "",
    "bags" => $_GET["bags"] ?? "",
    "ticket" => $_GET["ticket"] ?? "",
    "seat" => $_GET["seat"] ?? ""
];

function composerHotelUrl($page, $extra = [], $remove = [])
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

function hiddenHotelInputs($params)
{
    foreach ($params as $key => $value) {
        echo '<input type="hidden" name="' . htmlspecialchars($key) . '" value="' . htmlspecialchars((string) $value) . '">' . PHP_EOL;
    }
}

include "includes/header.php";
?>

<section class="composer-page">
    <div class="composer-heading">
        <p class="eyebrow">Étape 2 sur 4</p>
        <h1>Choisissez votre hébergement à <?= htmlspecialchars($destination["name"]) ?></h1>
        <p><?= $transport ? htmlspecialchars($transport["company"]) . " sélectionné" : "Transport non sélectionné" ?></p>
    </div>

    <div class="composer-progress">
        <span class="done">1 Transport</span>
        <span class="active">2 Hébergement</span>
        <span>3 Activités</span>
        <span>4 Récapitulatif</span>
    </div>

    <div class="composer-layout composer-with-filters composer-hotel-layout">
        <aside class="filters composer-filters">
            <h1>Filtrer les hébergements</h1>
            <form method="get" class="filter-form">
                <?php hiddenHotelInputs($journeyParams); ?>
                <div class="stay-settings">
                    <h2>Paramètres du séjour</h2>
                    <label>
                        Adultes
                        <input type="number" name="adults" min="1" max="8" value="<?= (int) $adults ?>">
                    </label>
                    <label>
                        Enfants
                        <input type="number" name="children" min="0" max="6" value="<?= (int) $children ?>">
                    </label>
                    <label>
                        Nombre de nuits
                        <input type="number" name="nights" min="1" max="30" value="<?= (int) $nights ?>">
                    </label>
                </div>
                <label>
                    Type d'hébergement
                    <select name="type">
                        <option value="">Tous</option>
                        <?php foreach (uniqueValues($linkedHotels, "type") as $value): ?>
                            <option value="<?= htmlspecialchars($value) ?>" <?= $type === $value ? "selected" : "" ?>>
                                <?= htmlspecialchars($value) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </label>
                <label>
                    Prix par nuit
                    <input type="range" name="price" min="50" max="260" step="5" value="<?= (int) $maxPrice ?>" data-range-output="composer-hotel-price">
                    <output id="composer-hotel-price"><?= money($maxPrice) ?></output>
                </label>
                <label>
                    Chambres disponibles
                    <input type="range" name="rooms" min="1" max="4" step="1" value="<?= (int) $rooms ?>" data-rooms-output="composer-hotel-rooms">
                    <output id="composer-hotel-rooms"><?= (int) $rooms ?> chambre(s)</output>
                </label>
                <label>
                    Distance aéroport
                    <input type="range" name="airport_distance" min="5" max="100" step="5" value="<?= (int) $airportDistance ?>" data-km-output="composer-hotel-airport">
                    <output id="composer-hotel-airport"><?= (int) $airportDistance ?> km max</output>
                </label>
                <label>
                    Note étoile
                    <select name="stars">
                        <option value="0">Toutes</option>
                        <?php for ($i = 2; $i <= 5; $i++): ?>
                            <option value="<?= $i ?>" <?= $stars === $i ? "selected" : "" ?>><?= $i ?>★ et plus</option>
                        <?php endfor; ?>
                    </select>
                </label>
                <fieldset class="check-group">
                    <legend>Équipements</legend>
                    <?php foreach ($amenityLabels as $key => $label): ?>
                        <label><input type="checkbox" name="amenities[]" value="<?= htmlspecialchars($key) ?>" <?= in_array($key, $amenities, true) ? "checked" : "" ?>> <?= htmlspecialchars($label) ?></label>
                    <?php endforeach; ?>
                </fieldset>
                <button class="btn btn-primary" type="submit">Appliquer</button>
                <a class="btn btn-light" href="composer_hebergement.php?<?= http_build_query($journeyParams) ?>">Réinitialiser</a>
            </form>
        </aside>

        <section class="composer-list hotel-composer-list">
            <div class="section-head no-padding">
                <div>
                    <p class="eyebrow"><?= count($filteredHotels) ?> hébergement(s)</p>
                    <h2>Disponibilités</h2>
                </div>
                <form method="get" class="sort-form">
                    <?php
                    hiddenHotelInputs(array_merge($journeyParams, [
                        "type" => $type,
                        "price" => $maxPrice,
                        "rooms" => $rooms,
                        "airport_distance" => $airportDistance,
                        "stars" => $stars
                    ]));
                    foreach ($amenities as $amenity):
                    ?>
                        <input type="hidden" name="amenities[]" value="<?= htmlspecialchars($amenity) ?>">
                    <?php endforeach; ?>
                    <label>
                        Trier par
                        <select name="sort" onchange="this.form.submit()">
                            <option value="recommended" <?= $sort === "recommended" ? "selected" : "" ?>>Recommandé</option>
                            <option value="price_asc" <?= $sort === "price_asc" ? "selected" : "" ?>>Prix croissant</option>
                            <option value="price_desc" <?= $sort === "price_desc" ? "selected" : "" ?>>Prix décroissant</option>
                            <option value="rating" <?= $sort === "rating" ? "selected" : "" ?>>Avis voyageurs</option>
                        </select>
                    </label>
                </form>
            </div>

            <?php if (!$filteredHotels): ?>
                <div class="empty-state">Aucun hébergement ne correspond à ces filtres.</div>
            <?php endif; ?>

            <?php foreach ($filteredHotels as $hotel): ?>
                <article class="composer-choice with-photo <?= $previewHotel && (int) $previewHotel["id"] === (int) $hotel["id"] ? "selected" : "" ?>">
                    <img src="<?= htmlspecialchars($hotel["image"]) ?>" alt="<?= htmlspecialchars($hotel["name"]) ?>">
                    <div>
                        <span class="tag"><?= htmlspecialchars($hotel["type"]) ?></span>
                        <h2><?= htmlspecialchars($hotel["name"]) ?></h2>
                        <p><?= (int) $hotel["stars"] ?>★ · ⭐ <?= htmlspecialchars($hotel["rating"]) ?> · <?= (int) $hotel["airport_distance"] ?> km de l'aéroport</p>
                        <div class="amenity-row">
                            <?php foreach (array_slice($hotel["amenities"], 0, 4) as $amenity): ?>
                                <span><?= htmlspecialchars($amenityLabels[$amenity] ?? $amenity) ?></span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="composer-price">
                        <span class="price-box"><strong><?= money($hotel["price"]) ?></strong><small>par nuit</small></span>
                        <a class="btn btn-secondary" href="<?= composerHotelUrl("composer_hebergement.php", ["hotel_preview" => $hotel["id"]]) ?>">Voir détails</a>
                        <a class="btn btn-primary" href="<?= composerHotelUrl("composer_activites.php", ["hotel" => $hotel["id"]], ["hotel_preview", "type", "price", "rooms", "airport_distance", "stars", "amenities", "sort"]) ?>">Choisir</a>
                    </div>
                </article>
            <?php endforeach; ?>
        </section>

        <aside class="composer-side panel hotel-detail-panel">
            <?php if ($previewHotel): ?>
                <div class="hotel-photo-grid">
                    <?php foreach (array_slice($previewHotel["photos"], 0, 3) as $index => $photo): ?>
                        <img class="<?= $index === 0 ? "main" : "" ?>" src="<?= htmlspecialchars($photo) ?>" alt="<?= htmlspecialchars($previewHotel["name"]) ?>">
                    <?php endforeach; ?>
                </div>
                <h2><?= htmlspecialchars($previewHotel["name"]) ?></h2>
                <p><?= htmlspecialchars($previewHotel["type"]) ?> · <?= (int) $previewHotel["stars"] ?>★ · ⭐ <?= htmlspecialchars($previewHotel["rating"]) ?></p>
                <p><?= htmlspecialchars($previewHotel["description"]) ?></p>
                <div class="stay-summary compact">
                    <div><span>Adultes</span><strong><?= (int) $adults ?></strong></div>
                    <div><span>Enfants</span><strong><?= (int) $children ?></strong></div>
                    <div><span>Nuits</span><strong><?= (int) $nights ?></strong></div>
                    <div><span>Total hôtel</span><strong><?= money($previewHotel["price"] * $nights) ?></strong></div>
                </div>
                <div class="detail-stat-grid">
                    <div><span>Wi-Fi</span><strong><?= in_array("wifi", $previewHotel["amenities"], true) ? "Oui" : "Non" ?></strong></div>
                    <div><span>Clim</span><strong><?= in_array("clim", $previewHotel["amenities"], true) ? "Oui" : "Non" ?></strong></div>
                    <div><span>Piscine</span><strong><?= in_array("piscine", $previewHotel["amenities"], true) ? "Oui" : "Non" ?></strong></div>
                    <div><span>Chambres</span><strong><?= (int) $previewHotel["rooms"] ?></strong></div>
                    <div><span>Aéroport</span><strong><?= (int) $previewHotel["airport_distance"] ?> km</strong></div>
                    <div><span>Prix</span><strong><?= money($previewHotel["price"]) ?></strong></div>
                </div>
                <div class="amenity-row">
                    <?php foreach ($previewHotel["amenities"] as $amenity): ?>
                        <span><?= htmlspecialchars($amenityLabels[$amenity] ?? $amenity) ?></span>
                    <?php endforeach; ?>
                </div>
                <div class="review-stack">
                    <h3>Avis récents</h3>
                    <?php foreach (array_slice($previewHotel["recent_reviews"], 0, 3) as $review): ?>
                        <article>
                            <strong><?= htmlspecialchars($review["author"]) ?> · <?= (int) $review["rating"] ?>★</strong>
                            <p><?= htmlspecialchars($review["text"]) ?></p>
                        </article>
                    <?php endforeach; ?>
                </div>
                <a class="text-link" href="<?= htmlspecialchars($previewHotel["official_url"]) ?>" target="_blank" rel="noopener">Site officiel de l'hébergement</a>
                <a class="btn btn-primary full" href="<?= composerHotelUrl("composer_activites.php", ["hotel" => $previewHotel["id"]], ["hotel_preview", "type", "price", "rooms", "airport_distance", "stars", "amenities", "sort"]) ?>">Choisir cet hébergement</a>
                <a class="btn btn-light full" href="<?= composerHotelUrl("composer_activites.php", ["hotel" => ""], ["hotel_preview", "type", "price", "rooms", "airport_distance", "stars", "amenities", "sort"]) ?>">Passer l'hébergement</a>
            <?php else: ?>
                <h2>Aucun hébergement</h2>
                <p>Ajustez les filtres pour afficher des disponibilités.</p>
            <?php endif; ?>
        </aside>
    </div>
</section>

<?php include "includes/footer.php"; ?>
