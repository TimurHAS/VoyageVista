<?php
require_once "includes/data.php";
$pageTitle = "Choix du transport";

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

$mode = trim($_GET["mode"] ?? "");
$stops = trim($_GET["stops"] ?? "");
$departHour = isset($_GET["depart_hour"]) && $_GET["depart_hour"] !== "" ? (int) $_GET["depart_hour"] : 23;
$returnHour = isset($_GET["return_hour"]) && $_GET["return_hour"] !== "" ? (int) $_GET["return_hour"] : 23;
$maxPrice = isset($_GET["price"]) && $_GET["price"] !== "" ? (int) $_GET["price"] : 1200;
$sort = $_GET["sort"] ?? "recommended";

$linkedTransports = byDestination($transports, $destination["id"]);
$modeIcons = ["Avion" => "✈️", "Train" => "🚆", "Bus" => "🚌", "Voiture" => "🚗", "Ferry" => "⛴️"];

$filteredTransports = array_filter($linkedTransports, function ($transport) use ($mode, $stops, $departHour, $returnHour, $maxPrice) {
    $matchesMode = $mode === "" || $transport["mode"] === $mode;
    $matchesStops = $stops === ""
        || ($stops === "direct" && (int) $transport["stops"] === 0)
        || ($stops === "one" && (int) $transport["stops"] <= 1)
        || ($stops === "more" && (int) $transport["stops"] >= 1);
    $matchesHours = (int) $transport["depart_hour"] <= $departHour && (int) $transport["return_hour"] <= $returnHour;
    $matchesPrice = $transport["price"] <= $maxPrice;

    return $matchesMode && $matchesStops && $matchesHours && $matchesPrice;
});

usort($filteredTransports, function ($a, $b) use ($sort) {
    return match ($sort) {
        "price" => $a["price"] <=> $b["price"],
        "fast" => $a["duration_minutes"] <=> $b["duration_minutes"],
        default => [$a["stops"], $a["price"]] <=> [$b["stops"], $b["price"]],
    };
});

$cheapest = minItem($filteredTransports);
$fastest = $filteredTransports ? array_reduce($filteredTransports, function ($best, $item) {
    return !$best || $item["duration_minutes"] < $best["duration_minutes"] ? $item : $best;
}) : null;
$bestChoice = $filteredTransports[0] ?? null;

$journeyParams = [
    "destination" => $destination["id"],
    "departure" => $departure,
    "arrival" => $arrival,
    "persons" => $persons,
    "adults" => $adults,
    "children" => $children,
    "nights" => $nights,
    "date_depart" => $dateDepart,
    "date_retour" => $dateRetour
];

$filterJourneyParams = $journeyParams;
unset($filterJourneyParams["persons"], $filterJourneyParams["adults"], $filterJourneyParams["children"]);

function composerTransportUrl($page, $extra = [], $remove = [])
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

function hiddenComposerInputs($params)
{
    foreach ($params as $key => $value) {
        echo '<input type="hidden" name="' . htmlspecialchars($key) . '" value="' . htmlspecialchars((string) $value) . '">' . PHP_EOL;
    }
}

include "includes/header.php";
?>

<section class="composer-page">
    <div class="composer-heading">
        <p class="eyebrow">Étape 1 sur 4</p>
        <h1>Choisissez votre transport vers <?= htmlspecialchars($destination["name"]) ?></h1>
        <p><?= htmlspecialchars($departure) ?> → <?= htmlspecialchars($arrival) ?> · <span data-traveler-heading><?= $persons ?> personne(s)</span></p>
    </div>

    <div class="composer-progress">
        <span class="active">1 Transport</span>
        <span>2 Hébergement</span>
        <span>3 Activités</span>
        <span>4 Récapitulatif</span>
    </div>

    <div class="transport-tabs composer-tabs">
        <a class="<?= $mode === "" ? "active" : "" ?>" href="<?= composerTransportUrl("composer_transport.php", ["mode" => ""], ["mode"]) ?>">Tous</a>
        <?php foreach ($modeIcons as $label => $icon): ?>
            <a class="<?= $mode === $label ? "active" : "" ?>" href="<?= composerTransportUrl("composer_transport.php", ["mode" => $label]) ?>"><?= $icon ?> <?= htmlspecialchars($label) ?></a>
        <?php endforeach; ?>
    </div>

    <div class="composer-layout composer-with-filters">
        <aside class="filters composer-filters">
            <h1>Filtrer les offres</h1>
            <form method="get" class="filter-form">
                <?php hiddenComposerInputs($filterJourneyParams); ?>
                <div class="stay-settings" data-live-travelers>
                    <h2>Voyageurs</h2>
                    <label>
                        Adultes
                        <input type="number" name="adults" min="1" max="8" value="<?= (int) $adults ?>" data-live-adults>
                    </label>
                    <label>
                        Enfants
                        <input type="number" name="children" min="0" max="6" value="<?= (int) $children ?>" data-live-children>
                    </label>
                    <p class="form-hint">Le total des billets se recalcule directement dans les offres.</p>
                </div>
                <label>
                    Catégorie
                    <select name="mode">
                        <option value="">Tous</option>
                        <?php foreach (uniqueValues($linkedTransports, "mode") as $value): ?>
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
                    <input type="range" name="depart_hour" min="0" max="23" step="1" value="<?= (int) $departHour ?>" data-hour-output="composer-depart-hour">
                    <output id="composer-depart-hour"><?= sprintf("%02d:00", $departHour) ?></output>
                </label>
                <label>
                    Retour avant
                    <input type="range" name="return_hour" min="0" max="23" step="1" value="<?= (int) $returnHour ?>" data-hour-output="composer-return-hour">
                    <output id="composer-return-hour"><?= sprintf("%02d:00", $returnHour) ?></output>
                </label>
                <label>
                    Budget par personne
                    <input type="range" name="price" min="40" max="1200" step="10" value="<?= (int) $maxPrice ?>" data-range-output="composer-transport-price">
                    <output id="composer-transport-price"><?= money($maxPrice) ?></output>
                </label>
                <button class="btn btn-primary" type="submit">Appliquer</button>
                <a class="btn btn-light" href="composer_transport.php?<?= http_build_query($journeyParams) ?>">Réinitialiser</a>
            </form>
        </aside>

        <section class="composer-list kayak-results">
            <div class="panel transport-ranking">
                <div class="<?= $cheapest ? "" : "muted" ?>">
                    <strong>Le moins cher</strong>
                    <span><?= $cheapest ? money($cheapest["price"]) . " · " . htmlspecialchars($cheapest["duration"]) : "Aucune offre" ?></span>
                </div>
                <div class="<?= $bestChoice ? "active" : "muted" ?>">
                    <strong>Le meilleur choix</strong>
                    <span><?= $bestChoice ? money($bestChoice["price"]) . " · " . htmlspecialchars($bestChoice["duration"]) : "Aucune offre" ?></span>
                </div>
                <div class="<?= $fastest ? "" : "muted" ?>">
                    <strong>Le plus rapide</strong>
                    <span><?= $fastest ? money($fastest["price"]) . " · " . htmlspecialchars($fastest["duration"]) : "Aucune offre" ?></span>
                </div>
                <form method="get" class="sort-form">
                    <?php hiddenComposerInputs(array_merge($journeyParams, ["mode" => $mode, "stops" => $stops, "depart_hour" => $departHour, "return_hour" => $returnHour, "price" => $maxPrice])); ?>
                    <label>
                        Trier par
                        <select name="sort" onchange="this.form.submit()">
                            <option value="recommended" <?= $sort === "recommended" ? "selected" : "" ?>>Recommandé</option>
                            <option value="price" <?= $sort === "price" ? "selected" : "" ?>>Le moins cher</option>
                            <option value="fast" <?= $sort === "fast" ? "selected" : "" ?>>Le plus rapide</option>
                        </select>
                    </label>
                </form>
            </div>

            <?php if (!$filteredTransports): ?>
                <div class="empty-state">Aucune offre ne correspond à ces filtres.</div>
            <?php endif; ?>

            <?php foreach ($filteredTransports as $transport): ?>
                <?php $previewFare = calculateTransportFare($transport, $adults, $children, "0", "Basic", "Standard"); ?>
                <article class="kayak-offer kayak-offer-fixed">
                    <div class="kayak-airline">
                        <div class="mode-badge"><?= $modeIcons[$transport["mode"]] ?? "📍" ?><br><?= htmlspecialchars($transport["mode"]) ?></div>
                        <strong><?= htmlspecialchars($transport["company"]) ?></strong>
                        <span><?= (int) $transport["stops"] === 0 ? "Direct" : (int) $transport["stops"] . " escale(s)" ?></span>
                    </div>
                    <div class="kayak-route">
                        <div>
                            <strong><?= sprintf("%02d:%02d", (int) $transport["depart_hour"], (int) $transport["depart_minute"]) ?></strong>
                            <span><?= htmlspecialchars($transport["from"]) ?></span>
                        </div>
                        <div class="route-line">
                            <span><?= htmlspecialchars($transport["duration"]) ?></span>
                        </div>
                        <div>
                            <strong><?= sprintf("%02d:%02d", (int) $transport["return_hour"], (int) $transport["arrival_minute"]) ?></strong>
                            <span><?= htmlspecialchars($transport["to"]) ?></span>
                        </div>
                    </div>
                    <form method="get" action="composer_hebergement.php" class="transport-options" data-fare-card data-base-price="<?= (float) $transport["price"] ?>">
                        <?php hiddenComposerInputs($journeyParams); ?>
                        <input type="hidden" name="transport" value="<?= (int) $transport["id"] ?>">
                        <label>
                            Bagages
                            <select name="bags" data-fare-option="bags">
                                <option value="0">Aucun bagage</option>
                                <option value="cabine">Cabine incluse</option>
                                <option value="soute">1 bagage soute</option>
                            </select>
                        </label>
                        <label>
                            Billet
                            <select name="ticket" data-fare-option="ticket">
                                <option value="Basic">Basic</option>
                                <option value="Flex">Flex</option>
                                <option value="Premium">Premium</option>
                            </select>
                        </label>
                        <label>
                            Siège
                            <select name="seat" data-fare-option="seat">
                                <option value="Standard">Standard</option>
                                <option value="Fenêtre">Fenêtre</option>
                                <option value="Couloir">Couloir</option>
                                <option value="Espace+">Espace+</option>
                            </select>
                        </label>
                        <div class="kayak-price">
                            <span class="price-box">
                                <strong data-fare-total><?= money($previewFare["total"]) ?></strong>
                                <small data-fare-travelers><?= (int) $adults ?> adulte(s) · <?= (int) $children ?> enfant(s)</small>
                            </span>
                            <span class="fare-breakdown" data-fare-breakdown>
                                Adulte : <?= money($previewFare["adult_unit"]) ?> · Enfant : <?= money($previewFare["child_unit"]) ?>
                            </span>
                            <button class="btn btn-primary" type="submit">Choisir</button>
                        </div>
                    </form>
                </article>
            <?php endforeach; ?>
        </section>

        <aside class="composer-side panel">
            <h2>Votre trajet</h2>
            <div class="route-card">
                <strong><?= htmlspecialchars($departure) ?></strong>
                <span>→</span>
                <strong><?= htmlspecialchars($arrival) ?></strong>
            </div>
            <div class="stay-summary">
                <div><span>Adultes</span><strong data-live-summary="adults"><?= (int) $adults ?></strong></div>
                <div><span>Enfants</span><strong data-live-summary="children"><?= (int) $children ?></strong></div>
                <div><span>Personnes</span><strong data-live-summary="persons"><?= (int) $persons ?></strong></div>
            </div>
            <p><?= $dateDepart ? "Départ : " . htmlspecialchars($dateDepart) : "Date de départ à préciser" ?></p>
            <p><?= $dateRetour ? "Retour : " . htmlspecialchars($dateRetour) : "Retour flexible" ?></p>
            <a class="btn btn-light full" href="composer_hebergement.php?<?= http_build_query(array_merge($journeyParams, ["transport" => ""])) ?>">Passer le transport</a>
        </aside>
    </div>
</section>

<?php include "includes/footer.php"; ?>
