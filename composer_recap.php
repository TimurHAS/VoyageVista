<?php
require_once "includes/data.php";
$pageTitle = "Récapitulatif";

$destinationId = isset($_GET["destination"]) ? (int) $_GET["destination"] : 1;
$destination = findById($destinations, $destinationId);
$departure = trim($_GET["departure"] ?? "Paris");
$arrival = trim($_GET["arrival"] ?? $destination["name"]);
$persons = max(1, (int) ($_GET["persons"] ?? 2));
$adults = max(1, (int) ($_GET["adults"] ?? $persons));
$children = max(0, (int) ($_GET["children"] ?? 0));
$persons = max(1, $adults + $children);
$nights = max(1, (int) ($_GET["nights"] ?? 7));

$transport = !empty($_GET["transport"]) ? findById(byDestination($transports, $destination["id"]), (int) $_GET["transport"]) : null;
$hotel = !empty($_GET["hotel"]) ? findById(byDestination($hotels, $destination["id"]), (int) $_GET["hotel"]) : null;
$activityIds = !empty($_GET["activities"]) ? array_filter(array_map("intval", explode(",", $_GET["activities"]))) : [];
$selectedActivities = array_values(array_filter(byDestination($activities, $destination["id"]), fn($activity) => in_array((int) $activity["id"], $activityIds, true)));

$bags = $_GET["bags"] ?? "";
$ticket = $_GET["ticket"] ?? "";
$seat = $_GET["seat"] ?? "";
$bagLabels = ["0" => "Aucun bagage", "cabine" => "Cabine incluse", "soute" => "1 bagage soute"];
$transportOptions = array_filter([
    $bags !== "" ? ($bagLabels[$bags] ?? $bags) : "",
    $ticket !== "" ? "Billet " . $ticket : "",
    $seat !== "" ? "Siège " . $seat : ""
]);

$transportFare = $transport ? calculateTransportFare($transport, $adults, $children, $bags ?: "0", $ticket ?: "Basic", $seat ?: "Standard") : null;
$hotelFare = $hotel ? calculateHotelFare($hotel, $nights, $adults, $children) : null;
$activityFares = array_map(fn($activity) => calculateActivityFare($activity, $adults, $children), $selectedActivities);

$transportTotal = $transportFare["total"] ?? 0;
$hotelTotal = $hotelFare["total"] ?? 0;
$activitiesTotal = array_sum(array_map(fn($fare) => $fare["total"], $activityFares));
$total = $transportTotal + $hotelTotal + $activitiesTotal;

$fromPoint = cityPoint($departure);
$toPoint = cityPoint($arrival);
$earthImage = "https://upload.wikimedia.org/wikipedia/commons/thumb/9/91/Land_shallow_topo_2048.jpg/1280px-Land_shallow_topo_2048.jpg";

$cartParams = ["destination" => $destination["id"], "persons" => $persons, "adults" => $adults, "children" => $children, "nights" => $nights];
if ($transport) {
    $cartParams["transport"] = $transport["id"];
}
if ($hotel) {
    $cartParams["hotel"] = $hotel["id"];
}
if ($activityIds) {
    $cartParams["activities"] = implode(",", $activityIds);
}
if ($bags !== "") {
    $cartParams["bags"] = $bags;
}
if ($ticket !== "") {
    $cartParams["ticket"] = $ticket;
}
if ($seat !== "") {
    $cartParams["seat"] = $seat;
}

include "includes/header.php";
?>

<section class="composer-page">
    <div class="composer-heading">
        <p class="eyebrow">Étape 4 sur 4</p>
        <h1>Récapitulatif de votre voyage</h1>
        <p><?= htmlspecialchars($departure) ?> → <?= htmlspecialchars($arrival) ?></p>
    </div>

    <div class="composer-progress">
        <span class="done">1 Transport</span>
        <span class="done">2 Hébergement</span>
        <span class="done">3 Activités</span>
        <span class="active">4 Récapitulatif</span>
    </div>

    <div class="recap-layout">
        <section class="panel">
            <h2>Votre sélection</h2>
            <div class="summary-line">
                <span>Voyageurs</span>
                <strong><?= (int) $adults ?> adulte(s) · <?= (int) $children ?> enfant(s)</strong>
            </div>
            <div class="summary-line">
                <span>Durée</span>
                <strong><?= (int) $nights ?> nuit(s)</strong>
            </div>
            <div class="summary-line">
                <span>Transport</span>
                <strong><?= $transport ? htmlspecialchars($transport["company"]) . " · " . money($transportTotal) : "Non sélectionné" ?></strong>
            </div>
            <?php if ($transport && $transportOptions): ?>
                <div class="summary-line">
                    <span>Options transport</span>
                    <strong><?= htmlspecialchars(implode(" · ", $transportOptions)) ?></strong>
                </div>
            <?php endif; ?>
            <div class="summary-line">
                <span>Hébergement</span>
                <strong><?= $hotel ? htmlspecialchars($hotel["name"]) . " · " . money($hotelTotal) : "Non sélectionné" ?></strong>
            </div>
            <div class="summary-line">
                <span>Activités</span>
                <strong><?= count($selectedActivities) ? count($selectedActivities) . " activité(s) · " . money($activitiesTotal) : "Non sélectionné" ?></strong>
            </div>
            <div class="summary-line total">
                <span>Total</span>
                <strong><?= money($total) ?></strong>
            </div>
            <a class="btn btn-primary full" href="panier.php?<?= http_build_query($cartParams) ?>">Ajouter au panier</a>
        </section>

        <section class="panel">
            <h2>Carte du trajet</h2>
            <div class="earth-map">
                <img class="earth-image" src="<?= htmlspecialchars($earthImage) ?>" alt="Carte satellite de la Terre">
                <svg viewBox="0 0 100 100" preserveAspectRatio="none" aria-label="Tracé du trajet">
                    <line class="route-line-map" x1="<?= (float) $fromPoint["x"] ?>" y1="<?= (float) $fromPoint["y"] ?>" x2="<?= (float) $toPoint["x"] ?>" y2="<?= (float) $toPoint["y"] ?>"></line>
                    <circle class="map-dot start" cx="<?= (float) $fromPoint["x"] ?>" cy="<?= (float) $fromPoint["y"] ?>" r="1.25"></circle>
                    <circle class="map-dot end" cx="<?= (float) $toPoint["x"] ?>" cy="<?= (float) $toPoint["y"] ?>" r="1.45"></circle>
                </svg>
                <span class="map-label start" style="left: <?= (float) $fromPoint["x"] ?>%; top: <?= (float) $fromPoint["y"] ?>%;"><?= htmlspecialchars($departure) ?></span>
                <span class="map-label end" style="left: <?= (float) $toPoint["x"] ?>%; top: <?= (float) $toPoint["y"] ?>%;"><?= htmlspecialchars($arrival) ?></span>
            </div>
        </section>
    </div>
</section>

<?php include "includes/footer.php"; ?>
