<?php
require_once "includes/data.php";
$pageTitle = "Choisir un hébergement";

$id = (int) ($_GET["id"] ?? 1);
$hotel = findById($hotels, $id);
$destination = findById($destinations, $hotel["destination_id"]);

$adults = max(1, (int) ($_GET["adults"] ?? 2));
$children = max(0, (int) ($_GET["children"] ?? 0));
$nights = max(1, (int) ($_GET["nights"] ?? 7));
$checkin = $_GET["checkin"] ?? "";
$checkout = $_GET["checkout"] ?? "";
$roomType = $_GET["room_type"] ?? "standard";
$options = $_GET["hotel_options"] ?? [];
if (!is_array($options)) {
    $options = $options !== "" ? [$options] : [];
}

$fare = calculateHotelFare($hotel, $nights, $adults, $children, $roomType, $options);

$cartParams = [
    "destination" => $destination["id"],
    "hotel" => $hotel["id"],
    "hotel_only" => 1,
    "adults" => $adults,
    "children" => $children,
    "persons" => $adults + $children,
    "nights" => $nights,
    "checkin" => $checkin,
    "checkout" => $checkout,
    "room_type" => $roomType
];

foreach ($options as $option) {
    $cartParams["hotel_options"][] = $option;
}

include "includes/header.php";
?>

<section class="standalone-detail">
    <div class="detail-panel panel">
        <img class="detail-cover" src="<?= htmlspecialchars($hotel["image"]) ?>" alt="<?= htmlspecialchars($hotel["name"]) ?>">
        <p class="eyebrow">Hébergement uniquement</p>
        <h1><?= htmlspecialchars($hotel["name"]) ?></h1>
        <p><?= htmlspecialchars($destination["name"]) ?> · <?= htmlspecialchars($hotel["type"]) ?> · <?= (float) $hotel["rating"] ?>/5</p>
        <p class="muted">Ici, vous réservez seulement l’hébergement. Le transport et les activités ne sont pas ajoutés automatiquement.</p>

        <form method="get" class="pricing-form">
            <input type="hidden" name="id" value="<?= (int) $hotel["id"] ?>">
            <label>Arrivée
                <input type="date" name="checkin" value="<?= htmlspecialchars($checkin) ?>">
            </label>
            <label>Départ
                <input type="date" name="checkout" value="<?= htmlspecialchars($checkout) ?>">
            </label>
            <label>Nombre de nuits
                <input type="number" name="nights" min="1" max="30" value="<?= (int) $nights ?>">
            </label>
            <label>Adultes
                <input type="number" name="adults" min="1" max="8" value="<?= (int) $adults ?>">
            </label>
            <label>Enfants
                <input type="number" name="children" min="0" max="6" value="<?= (int) $children ?>">
            </label>
            <label>Type de chambre
                <select name="room_type">
                    <option value="standard" <?= $roomType === "standard" ? "selected" : "" ?>>Chambre standard</option>
                    <option value="family" <?= $roomType === "family" ? "selected" : "" ?>>Chambre familiale (+35 €/nuit)</option>
                    <option value="view" <?= $roomType === "view" ? "selected" : "" ?>>Chambre avec vue (+55 €/nuit)</option>
                    <option value="suite" <?= $roomType === "suite" ? "selected" : "" ?>>Suite (+90 €/nuit)</option>
                </select>
            </label>

            <fieldset class="option-box">
                <legend>Options liées</legend>
                <label><input type="checkbox" name="hotel_options[]" value="breakfast" <?= in_array("breakfast", $options, true) ? "checked" : "" ?>> Petit-déjeuner (+12 €/adulte/nuit, +7 €/enfant/nuit)</label>
                <label><input type="checkbox" name="hotel_options[]" value="cancel" <?= in_array("cancel", $options, true) ? "checked" : "" ?>> Annulation flexible (+8 €/nuit)</label>
                <label><input type="checkbox" name="hotel_options[]" value="parking" <?= in_array("parking", $options, true) ? "checked" : "" ?>> Parking (+10 €/nuit)</label>
                <label><input type="checkbox" name="hotel_options[]" value="spa" <?= in_array("spa", $options, true) ? "checked" : "" ?>> Accès spa (+25 €/adulte)</label>
            </fieldset>

            <button class="btn btn-secondary" type="submit">Recalculer</button>
        </form>
    </div>

    <aside class="price-panel panel">
        <h2>Tarif hébergement</h2>
        <div class="summary-line"><span>Base hôtel</span><strong><?= money($fare["base"]) ?>/nuit</strong></div>
        <div class="summary-line"><span>Type de chambre</span><strong><?= htmlspecialchars(roomTypeLabel($roomType)) ?> · <?= money($fare["room_extra"]) ?>/nuit</strong></div>
        <div class="summary-line"><span>Options par nuit</span><strong><?= money($fare["option_night"]) ?>/nuit</strong></div>
        <div class="summary-line"><span>Options séjour</span><strong><?= money($fare["option_stay"]) ?></strong></div>
        <div class="summary-line"><span>Nuits</span><strong><?= (int) $nights ?></strong></div>
        <div class="summary-line"><span>Voyageurs</span><strong><?= (int) $adults ?> adulte(s) · <?= (int) $children ?> enfant(s)</strong></div>
        <div class="summary-line total-line"><span>Total hébergement</span><strong><?= money($fare["total"]) ?></strong></div>
        <a class="btn btn-primary full" href="panier.php?<?= http_build_query($cartParams) ?>">Ajouter cet hébergement au panier</a>
        <a class="btn btn-light full" href="hebergements.php">Retour aux hébergements</a>
    </aside>
</section>

<?php include "includes/footer.php"; ?>
