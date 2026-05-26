<?php
require_once "includes/data.php";
$pageTitle = "Panier";

$destinationId = isset($_GET["destination"]) ? (int) $_GET["destination"] : 0;
$destination = $destinationId ? findById($destinations, $destinationId) : null;

$destinationTransports = $destination ? byDestination($transports, $destination["id"]) : [];
$destinationHotels = $destination ? byDestination($hotels, $destination["id"]) : [];
$destinationActivities = $destination ? byDestination($activities, $destination["id"]) : [];

$transport = isset($_GET["transport"]) && $destination ? findById($destinationTransports, (int) $_GET["transport"]) : null;
$hotel = isset($_GET["hotel"]) && $destination ? findById($destinationHotels, (int) $_GET["hotel"]) : null;

$activityIds = [];
if (!empty($_GET["activities"])) {
    $activityIds = array_filter(array_map("intval", explode(",", $_GET["activities"])));
} elseif (!empty($_GET["activity"])) {
    $activityIds = [(int) $_GET["activity"]];
}

$selectedActivities = array_values(array_filter($destinationActivities, function ($activity) use ($activityIds) {
    return in_array((int) $activity["id"], $activityIds, true);
}));

$nights = max(1, (int) ($_GET["nights"] ?? 7));
$adults = max(1, (int) ($_GET["adults"] ?? ($_GET["persons"] ?? 2)));
$children = max(0, (int) ($_GET["children"] ?? 0));
$persons = max(1, $adults + $children);

$bags = $_GET["bags"] ?? "0";
$ticket = $_GET["ticket"] ?? "Basic";
$seat = $_GET["seat"] ?? "Standard";
$roomType = $_GET["room_type"] ?? "standard";
$hotelOptions = $_GET["hotel_options"] ?? [];
if (!is_array($hotelOptions)) {
    $hotelOptions = $hotelOptions !== "" ? [$hotelOptions] : [];
}

$checkin = $_GET["checkin"] ?? "";
$checkout = $_GET["checkout"] ?? "";
$activityDate = $_GET["activity_date"] ?? "";
$activityTime = $_GET["activity_time"] ?? "";

$transportFare = $transport ? calculateTransportFare($transport, $adults, $children, $bags, $ticket, $seat) : null;
$hotelFare = $hotel ? calculateHotelFare($hotel, $nights, $adults, $children, $roomType, $hotelOptions) : null;
$activityFares = array_map(fn($activity) => calculateActivityFare($activity, $adults, $children), $selectedActivities);

$transportTotal = $transportFare["total"] ?? 0;
$hotelTotal = $hotelFare["total"] ?? 0;
$activitiesTotal = array_sum(array_map(fn($fare) => $fare["total"], $activityFares));
$total = $transportTotal + $hotelTotal + $activitiesTotal;

$cartType = "Séjour personnalisé";
if ($transport && !$hotel && !count($selectedActivities)) {
    $cartType = "Billet uniquement";
} elseif ($hotel && !$transport && !count($selectedActivities)) {
    $cartType = "Hébergement uniquement";
} elseif (!$hotel && !$transport && count($selectedActivities)) {
    $cartType = "Activité uniquement";
}

include "includes/header.php";
?>

<section class="cart-page">
    <div class="cart-main panel">
        <p class="eyebrow">Votre sélection</p>
        <h1>Panier</h1>

        <?php if (!$destination): ?>
            <div class="empty-state">
                Votre panier est vide. Lancez une recherche ou choisissez une offre dans les catalogues.
            </div>
            <div data-restore-cart class="restore-cart"></div>
            <div class="card-actions">
                <a class="btn btn-primary" href="index.php">Rechercher un voyage</a>
                <a class="btn btn-secondary" href="destinations.php">Voir les destinations</a>
            </div>
        <?php else: ?>
            <article class="cart-destination">
                <img src="<?= htmlspecialchars($destination["image"]) ?>" alt="<?= htmlspecialchars($destination["name"]) ?>">
                <div>
                    <h2><?= htmlspecialchars($cartType) ?></h2>
                    <p><?= htmlspecialchars($destination["name"]) ?> · <?= htmlspecialchars($destination["country"]) ?></p>
                </div>
                <strong><?= money($total) ?></strong>
            </article>

            <div class="cart-lines">
                <div class="summary-line">
                    <span>Voyageurs</span>
                    <strong><?= (int) $adults ?> adulte(s) · <?= (int) $children ?> enfant(s)</strong>
                </div>

                <?php if ($hotel): ?>
                    <div class="summary-line">
                        <span>Période</span>
                        <strong>
                            <?= $checkin ? htmlspecialchars($checkin) : "Arrivée non définie" ?>
                            →
                            <?= $checkout ? htmlspecialchars($checkout) : "Départ non défini" ?>
                            · <?= (int) $nights ?> nuit(s)
                        </strong>
                    </div>
                <?php endif; ?>

                <?php if ($transport): ?>
                    <div class="summary-line">
                        <span>Transport</span>
                        <strong><?= htmlspecialchars($transport["mode"]) ?> · <?= htmlspecialchars($transport["company"]) ?></strong>
                    </div>
                    <div class="summary-line">
                        <span>Trajet</span>
                        <strong><?= htmlspecialchars($transport["from"]) ?> → <?= htmlspecialchars($transport["to"]) ?> · <?= htmlspecialchars($transport["duration"]) ?></strong>
                    </div>
                    <div class="summary-line">
                        <span>Tarif transport</span>
                        <strong>
                            Adultes <?= money($transportFare["adults_total"]) ?>
                            <?php if ($children > 0): ?> · Enfants <?= money($transportFare["children_total"]) ?><?php endif; ?>
                        </strong>
                    </div>
                    <div class="summary-line">
                        <span>Options transport</span>
                        <strong><?= htmlspecialchars(implode(" · ", transportOptionLabels($bags, $ticket, $seat))) ?></strong>
                    </div>
                <?php endif; ?>

                <?php if ($hotel): ?>
                    <div class="summary-line">
                        <span>Hébergement</span>
                        <strong><?= htmlspecialchars($hotel["name"]) ?> · <?= htmlspecialchars(roomTypeLabel($roomType)) ?></strong>
                    </div>
                    <div class="summary-line">
                        <span>Options hébergement</span>
                        <strong><?= hotelOptionLabels($hotelOptions) ? htmlspecialchars(implode(" · ", hotelOptionLabels($hotelOptions))) : "Aucune option" ?></strong>
                    </div>
                    <div class="summary-line">
                        <span>Total hébergement</span>
                        <strong><?= money($hotelTotal) ?></strong>
                    </div>
                <?php endif; ?>

                <?php if (count($selectedActivities)): ?>
                    <div class="summary-line">
                        <span>Activités</span>
                        <strong><?= count($selectedActivities) ?> activité(s) · <?= money($activitiesTotal) ?></strong>
                    </div>
                    <?php if ($activityDate || $activityTime): ?>
                        <div class="summary-line">
                            <span>Date activité</span>
                            <strong><?= $activityDate ? htmlspecialchars($activityDate) : "Date non définie" ?> · <?= htmlspecialchars($activityTime ?: "Heure non définie") ?></strong>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>

            <?php if (count($selectedActivities)): ?>
                <div class="selected-activities">
                    <?php foreach ($selectedActivities as $activity): ?>
                        <article>
                            <img src="<?= htmlspecialchars($activity["image"]) ?>" alt="<?= htmlspecialchars($activity["name"]) ?>">
                            <div>
                                <h3><?= htmlspecialchars($activity["name"]) ?></h3>
                                <p><?= htmlspecialchars($activity["category"]) ?> · <?= htmlspecialchars($activity["duration"]) ?></p>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <aside class="cart-side panel">
        <h2>Total</h2>
        <p class="big-price"><?= money($total) ?></p>
        <?php if ($destination): ?>
            <p class="muted">Le panier est sauvegardé dans ce navigateur. Il reste disponible même si vous n’êtes pas connecté.</p>
            <a class="btn btn-secondary full" href="compte.php">S’identifier avant paiement</a>
            <button class="btn btn-primary full" type="button" data-payment-button>Confirmer le panier</button>
        <?php else: ?>
            <p class="muted">Ajoutez une destination, un transport, un hébergement ou une activité pour commencer.</p>
            <a class="btn btn-primary full" href="index.php">Rechercher</a>
        <?php endif; ?>
    </aside>
</section>

<?php include "includes/footer.php"; ?>
