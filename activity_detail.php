<?php
require_once "includes/data.php";
$pageTitle = "Choisir une activité";

$id = (int) ($_GET["id"] ?? 1);
$activity = findById($activities, $id);
$destination = findById($destinations, $activity["destination_id"]);

$adults = max(1, (int) ($_GET["adults"] ?? 2));
$children = max(0, (int) ($_GET["children"] ?? 0));
$date = $_GET["date"] ?? "";
$time = $_GET["time"] ?? "09:00";

$fare = calculateActivityFare($activity, $adults, $children);

$cartParams = [
    "destination" => $destination["id"],
    "activity" => $activity["id"],
    "activity_only" => 1,
    "adults" => $adults,
    "children" => $children,
    "persons" => $adults + $children,
    "activity_date" => $date,
    "activity_time" => $time
];

include "includes/header.php";
?>

<section class="standalone-detail">
    <div class="detail-panel panel">
        <img class="detail-cover" src="<?= htmlspecialchars($activity["image"]) ?>" alt="<?= htmlspecialchars($activity["name"]) ?>">
        <p class="eyebrow">Activité uniquement</p>
        <h1><?= htmlspecialchars($activity["name"]) ?></h1>
        <p><?= htmlspecialchars($destination["name"]) ?> · <?= htmlspecialchars($activity["category"]) ?> · <?= htmlspecialchars($activity["duration"]) ?></p>

        <form method="get" class="pricing-form">
            <input type="hidden" name="id" value="<?= (int) $activity["id"] ?>">
            <label>Date
                <input type="date" name="date" value="<?= htmlspecialchars($date) ?>">
            </label>
            <label>Heure
                <select name="time">
                    <option value="09:00" <?= $time === "09:00" ? "selected" : "" ?>>09:00</option>
                    <option value="14:00" <?= $time === "14:00" ? "selected" : "" ?>>14:00</option>
                    <option value="18:00" <?= $time === "18:00" ? "selected" : "" ?>>18:00</option>
                </select>
            </label>
            <label>Adultes
                <input type="number" name="adults" min="1" max="8" value="<?= (int) $adults ?>">
            </label>
            <label>Enfants
                <input type="number" name="children" min="0" max="6" value="<?= (int) $children ?>">
            </label>
            <button class="btn btn-secondary" type="submit">Recalculer</button>
        </form>
    </div>

    <aside class="price-panel panel">
        <h2>Tarif activité</h2>
        <div class="summary-line"><span>Adulte x <?= (int) $adults ?></span><strong><?= money($fare["adults_total"]) ?></strong></div>
        <div class="summary-line"><span>Enfant x <?= (int) $children ?></span><strong><?= money($fare["children_total"]) ?></strong></div>
        <div class="summary-line total-line"><span>Total activité</span><strong><?= money($fare["total"]) ?></strong></div>
        <a class="btn btn-primary full" href="panier.php?<?= http_build_query($cartParams) ?>">Ajouter cette activité au panier</a>
        <a class="btn btn-light full" href="activites.php">Retour aux activités</a>
    </aside>
</section>

<?php include "includes/footer.php"; ?>
