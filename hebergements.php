<?php
require_once "includes/data.php";
$pageTitle = "Hébergements";

$search = trim($_GET["search"] ?? "");
$destinationId = isset($_GET["destination"]) && $_GET["destination"] !== "" ? (int) $_GET["destination"] : 0;
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
$adults = max(1, (int) ($_GET["adults"] ?? 2));
$children = max(0, (int) ($_GET["children"] ?? 0));
$persons = max(1, $adults + $children);
$nights = max(1, (int) ($_GET["nights"] ?? 7));

$amenityLabels = [
    "wifi" => "Wi-Fi",
    "piscine" => "Piscine",
    "petit_dejeuner" => "Petit-déjeuner inclus",
    "clim" => "Climatisation",
    "parking" => "Parking",
    "spa" => "Spa"
];

$popularHotels = $hotels;
usort($popularHotels, fn($a, $b) => ($b["reviews"] ?? 0) <=> ($a["reviews"] ?? 0));
$popularHotels = array_slice($popularHotels, 0, 4);

$filteredHotels = array_filter($hotels, function ($hotel) use ($search, $destinationId, $type, $maxPrice, $rooms, $airportDistance, $stars, $amenities, $destinations, $amenityLabels) {
    $destination = findById($destinations, $hotel["destination_id"]);
    $amenityText = implode(" ", array_map(fn($amenity) => $amenityLabels[$amenity] ?? $amenity, $hotel["amenities"] ?? []));
    $haystack = implode(" ", [
        $hotel["name"] ?? "",
        $hotel["type"] ?? "",
        $hotel["description"] ?? "",
        $destination["name"] ?? "",
        $destination["country"] ?? "",
        $amenityText
    ]);

    $matchesSearch = $search === "" || stripos($haystack, $search) !== false;
    $matchesDestination = $destinationId <= 0 || (int) $hotel["destination_id"] === $destinationId;
    $matchesType = $type === "" || $hotel["type"] === $type;
    $matchesPrice = $hotel["price"] <= $maxPrice;
    $matchesRooms = (int) $hotel["rooms"] >= $rooms;
    $matchesDistance = (int) $hotel["airport_distance"] <= $airportDistance;
    $matchesStars = $stars <= 0 || (int) $hotel["stars"] >= $stars;
    $matchesAmenities = empty($amenities) || empty(array_diff($amenities, $hotel["amenities"] ?? []));

    return $matchesSearch && $matchesDestination && $matchesType && $matchesPrice && $matchesRooms && $matchesDistance && $matchesStars && $matchesAmenities;
});

usort($filteredHotels, function ($a, $b) use ($sort) {
    return match ($sort) {
        "price_asc" => $a["price"] <=> $b["price"],
        "price_desc" => $b["price"] <=> $a["price"],
        "rating" => $b["rating"] <=> $a["rating"],
        "reviews" => ($b["reviews"] ?? 0) <=> ($a["reviews"] ?? 0),
        default => [($b["reviews"] ?? 0), $b["stars"], $b["rating"]] <=> [($a["reviews"] ?? 0), $a["stars"], $a["rating"]],
    };
});

include "includes/header.php";
?>

<datalist id="city-list">
    <?php foreach ($cities as $city): ?>
        <option value="<?= htmlspecialchars($city) ?>"></option>
    <?php endforeach; ?>
</datalist>

<div class="catalog-layout lodging-page">
    <aside class="filters">
        <h1>Rechercher un hébergement</h1>
        <form method="get" class="filter-form">
            <label>
                Recherche
                <input type="search" name="search" list="city-list" data-city-autocomplete value="<?= htmlspecialchars($search) ?>" placeholder="Nom de l'hôtel, ville, équipement">
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
            <div class="stay-settings">
                <h2>Séjour</h2>
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
                    <?php foreach (uniqueValues($hotels, "type") as $value): ?>
                        <option value="<?= htmlspecialchars($value) ?>" <?= $type === $value ? "selected" : "" ?>>
                            <?= htmlspecialchars($value) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>
            <label>
                Prix par nuit
                <input type="range" name="price" min="50" max="220" step="5" value="<?= (int) $maxPrice ?>" data-range-output="hotel-price">
                <output id="hotel-price"><?= money($maxPrice) ?></output>
            </label>
            <label>
                Chambres
                <input type="range" name="rooms" min="1" max="4" step="1" value="<?= (int) $rooms ?>" data-rooms-output="hotel-rooms">
                <output id="hotel-rooms"><?= (int) $rooms ?> chambre(s)</output>
            </label>
            <label>
                Distance aéroport
                <input type="range" name="airport_distance" min="5" max="100" step="5" value="<?= (int) $airportDistance ?>" data-km-output="hotel-airport">
                <output id="hotel-airport"><?= (int) $airportDistance ?> km max</output>
            </label>
            <label>
                Note étoiles
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
            <button class="btn btn-primary" type="submit">Rechercher</button>
            <a class="btn btn-light" href="hebergements.php">Réinitialiser</a>
        </form>
    </aside>

    <section class="catalog-content">
        <section class="popular-strip panel">
            <div class="section-head no-padding">
                <div>
                    <p class="eyebrow">Le plus d'avis</p>
                    <h2>Hôtels recommandés</h2>
                </div>
                <form method="get" class="sort-form">
                    <input type="hidden" name="search" value="<?= htmlspecialchars($search) ?>">
                    <input type="hidden" name="destination" value="<?= (int) $destinationId ?>">
                    <input type="hidden" name="type" value="<?= htmlspecialchars($type) ?>">
                    <input type="hidden" name="price" value="<?= (int) $maxPrice ?>">
                    <input type="hidden" name="rooms" value="<?= (int) $rooms ?>">
                    <input type="hidden" name="airport_distance" value="<?= (int) $airportDistance ?>">
                    <input type="hidden" name="stars" value="<?= (int) $stars ?>">
                    <?php foreach ($amenities as $amenity): ?>
                        <input type="hidden" name="amenities[]" value="<?= htmlspecialchars($amenity) ?>">
                    <?php endforeach; ?>
                    <input type="hidden" name="adults" value="<?= (int) $adults ?>">
                    <input type="hidden" name="children" value="<?= (int) $children ?>">
                    <input type="hidden" name="nights" value="<?= (int) $nights ?>">
                    <label>
                        Trier par
                        <select name="sort" onchange="this.form.submit()">
                            <option value="recommended" <?= $sort === "recommended" ? "selected" : "" ?>>Recommandé</option>
                            <option value="reviews" <?= $sort === "reviews" ? "selected" : "" ?>>Avis</option>
                            <option value="price_asc" <?= $sort === "price_asc" ? "selected" : "" ?>>Prix croissant</option>
                            <option value="price_desc" <?= $sort === "price_desc" ? "selected" : "" ?>>Prix décroissant</option>
                            <option value="rating" <?= $sort === "rating" ? "selected" : "" ?>>Note voyageurs</option>
                        </select>
                    </label>
                </form>
            </div>
            <div class="popular-row">
                <?php foreach ($popularHotels as $hotel): ?>
                    <?php $destination = findById($destinations, $hotel["destination_id"]); ?>
                    <a class="popular-mini" href="hebergement_detail.php?id=<?= (int) $hotel["id"] ?>">
                        <img src="<?= htmlspecialchars($hotel["image"]) ?>" alt="<?= htmlspecialchars($hotel["name"]) ?>">
                        <span><?= (int) $hotel["stars"] ?>★ · <?= htmlspecialchars($destination["name"] ?? "") ?></span>
                        <strong><?= htmlspecialchars($hotel["name"]) ?></strong>
                        <small>⭐ <?= htmlspecialchars($hotel["rating"]) ?> · <?= (int) $hotel["reviews"] ?> avis</small>
                    </a>
                <?php endforeach; ?>
            </div>
        </section>

        <div class="lodging-hero panel">
            <div>
                <p class="eyebrow">Hébergements & disponibilités</p>
                <h2>Trouvez l'hôtel idéal</h2>
                <p>Filtrez par nom d'hôtel, destination, équipements, chambres et note étoile.</p>
            </div>
        </div>

        <div class="section-head compact">
            <div>
                <p class="eyebrow"><?= count($filteredHotels) ?> hébergement(s)</p>
                <h2>Disponibilités</h2>
            </div>
        </div>

        <div class="hotel-list">
            <?php if (empty($filteredHotels)): ?>
                <p class="empty-state">Aucun hébergement ne correspond à votre recherche.</p>
            <?php endif; ?>

            <?php foreach ($filteredHotels as $hotel): ?>
                <?php $destination = findById($destinations, $hotel["destination_id"]); ?>
                <article class="hotel-row">
                    <img src="<?= htmlspecialchars($hotel["image"]) ?>" alt="<?= htmlspecialchars($hotel["name"]) ?>">
                    <div class="hotel-info">
                        <span class="tag"><?= htmlspecialchars($hotel["type"]) ?></span>
                        <h3><?= htmlspecialchars($hotel["name"]) ?></h3>
                        <p><?= htmlspecialchars($destination["name"]) ?> · <?= (int) $hotel["airport_distance"] ?> km de l'aéroport</p>
                        <p class="stars"><?= str_repeat("★", (int) $hotel["stars"]) ?> <span>⭐ <?= htmlspecialchars($hotel["rating"]) ?> · <?= (int) $hotel["reviews"] ?> avis</span></p>
                        <div class="amenity-row">
                            <?php foreach ($hotel["amenities"] as $amenity): ?>
                                <span><?= htmlspecialchars($amenityLabels[$amenity] ?? $amenity) ?></span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="hotel-price">
                        <small>à partir de</small>
                        <strong><?= money($hotel["price"]) ?></strong>
                        <span>par nuit</span>
                        <a class="btn btn-primary" href="hebergement_detail.php?id=<?= (int) $hotel["id"] ?>&adults=<?= (int) $adults ?>&children=<?= (int) $children ?>&nights=<?= (int) $nights ?>">Sélectionner</a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </section>
</div>

<?php include "includes/footer.php"; ?>
