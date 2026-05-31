<?php
require_once "includes/data.php";
$pageTitle = "Transports";

$search = trim($_GET["search"] ?? "");
$mode = trim($_GET["mode"] ?? "");
$stops = trim($_GET["stops"] ?? "");
$departHour = isset($_GET["depart_hour"]) && $_GET["depart_hour"] !== "" ? (int) $_GET["depart_hour"] : 23;
$returnHour = isset($_GET["return_hour"]) && $_GET["return_hour"] !== "" ? (int) $_GET["return_hour"] : 23;
$maxPrice = isset($_GET["price"]) && $_GET["price"] !== "" ? (int) $_GET["price"] : 1000;
$sort = $_GET["sort"] ?? "recommended";

$modeIcons = ["Avion" => "✈️", "Train" => "🚆", "Bus" => "🚌", "Voiture" => "🚗", "Ferry" => "⛴️"];

$popularTransports = $transports;
usort($popularTransports, fn($a, $b) => ($b["reviews"] ?? 0) <=> ($a["reviews"] ?? 0));
$popularTransports = array_slice($popularTransports, 0, 4);

$filteredTransports = array_filter($transports, function ($transport) use ($search, $mode, $stops, $departHour, $returnHour, $maxPrice, $destinations) {
    $destination = findById($destinations, $transport["destination_id"]);
    $destinationName = $destination["name"] ?? "";
    $haystack = implode(" ", [
        $transport["mode"] ?? "",
        $transport["company"] ?? "",
        $transport["from"] ?? "",
        $transport["to"] ?? "",
        $destinationName
    ]);

    $matchesSearch = $search === "" || stripos($haystack, $search) !== false;
    $matchesMode = $mode === "" || $transport["mode"] === $mode;
    $matchesStops = $stops === ""
        || ($stops === "direct" && (int) $transport["stops"] === 0)
        || ($stops === "one" && (int) $transport["stops"] <= 1)
        || ($stops === "more" && (int) $transport["stops"] >= 1);
    $matchesHours = (int) $transport["depart_hour"] <= $departHour && (int) $transport["return_hour"] <= $returnHour;
    $matchesPrice = $transport["price"] <= $maxPrice;

    return $matchesSearch && $matchesMode && $matchesStops && $matchesHours && $matchesPrice;
});

usort($filteredTransports, function ($a, $b) use ($sort) {
    return match ($sort) {
        "price" => $a["price"] <=> $b["price"],
        "fast" => $a["duration_minutes"] <=> $b["duration_minutes"],
        "reviews" => ($b["reviews"] ?? 0) <=> ($a["reviews"] ?? 0),
        default => [($b["reviews"] ?? 0), (int) $a["stops"], (float) $a["price"]] <=> [($a["reviews"] ?? 0), (int) $b["stops"], (float) $b["price"]],
    };
});

include "includes/header.php";
?>

<datalist id="city-list">
    <?php foreach ($cities as $city): ?>
        <option value="<?= htmlspecialchars($city) ?>"></option>
    <?php endforeach; ?>
</datalist>

<div class="catalog-layout transport-page">
    <aside class="filters">
        <h1>Rechercher un trajet</h1>
        <form method="get" class="filter-form">
            <label>
                Recherche
                <input type="search" name="search" list="city-list" data-city-autocomplete value="<?= htmlspecialchars($search) ?>" placeholder="Ville de départ, arrivée, compagnie">
            </label>
            <label>
                Catégorie
                <select name="mode">
                    <option value="">Tous</option>
                    <?php foreach (uniqueValues($transports, "mode") as $value): ?>
                        <option value="<?= htmlspecialchars($value) ?>" <?= $mode === $value ? "selected" : "" ?>>
                            <?= htmlspecialchars($value) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>
            <label>
                Escales
                <select name="stops">
                    <option value="">Toutes</option>
                    <option value="direct" <?= $stops === "direct" ? "selected" : "" ?>>Direct</option>
                    <option value="one" <?= $stops === "one" ? "selected" : "" ?>>1 escale max</option>
                    <option value="more" <?= $stops === "more" ? "selected" : "" ?>>1 escale ou +</option>
                </select>
            </label>
            <label>
                Départ avant
                <input type="range" name="depart_hour" min="0" max="23" step="1" value="<?= (int) $departHour ?>" data-hour-output="depart-hour-output">
                <output id="depart-hour-output"><?= sprintf("%02d:00", $departHour) ?></output>
            </label>
            <label>
                Retour avant
                <input type="range" name="return_hour" min="0" max="23" step="1" value="<?= (int) $returnHour ?>" data-hour-output="return-hour-output">
                <output id="return-hour-output"><?= sprintf("%02d:00", $returnHour) ?></output>
            </label>
            <label>
                Budget par personne
                <input type="range" name="price" min="40" max="1000" step="10" value="<?= (int) $maxPrice ?>" data-range-output="transport-price">
                <output id="transport-price"><?= money($maxPrice) ?></output>
            </label>
            <button class="btn btn-primary" type="submit">Rechercher</button>
            <a class="btn btn-light" href="transports.php">Réinitialiser</a>
        </form>
    </aside>

    <section class="catalog-content">
        <section class="popular-strip panel">
            <div class="section-head no-padding">
                <div>
                    <p class="eyebrow">Le plus d'avis</p>
                    <h2>Trajets populaires</h2>
                </div>
                <form method="get" class="sort-form">
                    <input type="hidden" name="search" value="<?= htmlspecialchars($search) ?>">
                    <input type="hidden" name="mode" value="<?= htmlspecialchars($mode) ?>">
                    <input type="hidden" name="stops" value="<?= htmlspecialchars($stops) ?>">
                    <input type="hidden" name="depart_hour" value="<?= (int) $departHour ?>">
                    <input type="hidden" name="return_hour" value="<?= (int) $returnHour ?>">
                    <input type="hidden" name="price" value="<?= (int) $maxPrice ?>">
                    <label>
                        Trier par
                        <select name="sort" onchange="this.form.submit()">
                            <option value="recommended" <?= $sort === "recommended" ? "selected" : "" ?>>Recommandé</option>
                            <option value="reviews" <?= $sort === "reviews" ? "selected" : "" ?>>Avis</option>
                            <option value="price" <?= $sort === "price" ? "selected" : "" ?>>Le moins cher</option>
                            <option value="fast" <?= $sort === "fast" ? "selected" : "" ?>>Le plus rapide</option>
                        </select>
                    </label>
                </form>
            </div>
            <div class="popular-row">
                <?php foreach ($popularTransports as $transport): ?>
                    <?php $destination = findById($destinations, $transport["destination_id"]); ?>
                    <a class="popular-mini" href="transport_detail.php?id=<?= (int) $transport["id"] ?>">
                        <img src="<?= htmlspecialchars($destination["image"] ?? "") ?>" alt="<?= htmlspecialchars($transport["company"]) ?>">
                        <span><?= htmlspecialchars($transport["mode"]) ?></span>
                        <strong><?= htmlspecialchars($transport["from"]) ?> → <?= htmlspecialchars($transport["to"]) ?></strong>
                        <small><?= htmlspecialchars($transport["company"]) ?> · <?= (int) $transport["reviews"] ?> avis</small>
                    </a>
                <?php endforeach; ?>
            </div>
        </section>

        <div class="transport-hero panel">
            <div>
                <p class="eyebrow">Transport & planification</p>
                <h2>Comparez les trajets</h2>
                <p>Vols, trains, bus, voitures et ferries avec filtres d'horaires, d'escales et de budget.</p>
            </div>
        </div>

        <div class="transport-tabs">
            <a class="<?= $mode === "" ? "active" : "" ?>" href="transports.php">Tous</a>
            <?php foreach ($modeIcons as $label => $icon): ?>
                <a class="<?= $mode === $label ? "active" : "" ?>" href="transports.php?mode=<?= urlencode($label) ?>"><?= $icon ?> <?= htmlspecialchars($label) ?></a>
            <?php endforeach; ?>
        </div>

        <div class="section-head compact">
            <div>
                <p class="eyebrow"><?= count($filteredTransports) ?> offre(s)</p>
                <h2>Meilleurs résultats</h2>
            </div>
        </div>

        <div class="offer-list">
            <?php if (empty($filteredTransports)): ?>
                <p class="empty-state">Aucun transport ne correspond à votre recherche.</p>
            <?php endif; ?>

            <?php foreach ($filteredTransports as $transport): ?>
                <?php $destination = findById($destinations, $transport["destination_id"]); ?>
                <article class="offer-row transport-row">
                    <div class="mode-badge"><?= $modeIcons[$transport["mode"]] ?? "📍" ?><br><?= htmlspecialchars($transport["mode"]) ?></div>
                    <div>
                        <h3><?= htmlspecialchars($transport["company"]) ?></h3>
                        <p><?= htmlspecialchars($transport["from"]) ?> → <?= htmlspecialchars($transport["to"]) ?></p>
                        <small>⭐ <?= htmlspecialchars($transport["rating"]) ?> · <?= (int) $transport["reviews"] ?> avis</small>
                    </div>
                    <div>
                        <span>Départ</span>
                        <strong><?= sprintf("%02d:%02d", (int) $transport["depart_hour"], (int) $transport["depart_minute"]) ?></strong>
                    </div>
                    <div>
                        <span>Durée</span>
                        <strong><?= htmlspecialchars($transport["duration"]) ?></strong>
                        <small><?= (int) $transport["stops"] === 0 ? "Direct" : (int) $transport["stops"] . " escale(s)" ?></small>
                    </div>
                    <div>
                        <span>Destination</span>
                        <strong><?= htmlspecialchars($destination["name"]) ?></strong>
                    </div>
                    <div class="price-cell">
                        <strong><?= money($transport["price"]) ?></strong>
                        <small>par personne</small>
                        <a class="btn btn-primary" href="transport_detail.php?id=<?= (int) $transport["id"] ?>">Choisir</a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </section>
</div>

<?php include "includes/footer.php"; ?>
