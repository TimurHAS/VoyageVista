<?php
require_once "includes/data.php";
$pageTitle = "Choisir un transport";

$id = (int) ($_GET["id"] ?? 1);
$transport = findById($transports, $id);
$destination = findById($destinations, $transport["destination_id"]);

$adults = max(1, (int) ($_GET["adults"] ?? 1));
$children = max(0, (int) ($_GET["children"] ?? 0));
$bags = $_GET["bags"] ?? "0";
$ticket = $_GET["ticket"] ?? "Basic";
$seat = $_GET["seat"] ?? "Standard";
$fare = calculateTransportFare($transport, $adults, $children, $bags, $ticket, $seat);

$cartParams = [
    "destination" => $destination["id"],
    "transport" => $transport["id"],
    "transport_only" => 1,
    "adults" => $adults,
    "children" => $children,
    "persons" => $adults + $children,
    "bags" => $bags,
    "ticket" => $ticket,
    "seat" => $seat
];

include "includes/header.php";
?>

<section class="standalone-detail">
    <div class="detail-panel panel">
        <div>
            <p class="eyebrow">Billet uniquement</p>
            <h1><?= htmlspecialchars($transport["mode"]) ?> · <?= htmlspecialchars($transport["company"]) ?></h1>
            <p><?= htmlspecialchars($transport["from"]) ?> → <?= htmlspecialchars($transport["to"]) ?> · <?= htmlspecialchars($transport["duration"]) ?></p>
            <p class="muted">Cette page ajoute seulement le billet au panier, pas un voyage complet.</p>
        </div>

        <form method="get" class="pricing-form">
            <input type="hidden" name="id" value="<?= (int) $transport["id"] ?>">
            <label>Adultes
                <input type="number" name="adults" min="1" max="8" value="<?= (int) $adults ?>">
            </label>
            <label>Enfants
                <input type="number" name="children" min="0" max="6" value="<?= (int) $children ?>">
            </label>
            <label>Bagages
                <select name="bags">
                    <option value="0" <?= $bags === "0" ? "selected" : "" ?>>Aucun bagage</option>
                    <option value="cabine" <?= $bags === "cabine" ? "selected" : "" ?>>Cabine incluse</option>
                    <option value="soute" <?= $bags === "soute" ? "selected" : "" ?>>1 bagage soute (+40 €/pers.)</option>
                </select>
            </label>
            <label>Billet
                <select name="ticket">
                    <option value="Basic" <?= $ticket === "Basic" ? "selected" : "" ?>>Basic</option>
                    <option value="Flex" <?= $ticket === "Flex" ? "selected" : "" ?>>Flex (+35 €/adulte, +20 €/enfant)</option>
                    <option value="Premium" <?= $ticket === "Premium" ? "selected" : "" ?>>Premium (+80 €/adulte, +50 €/enfant)</option>
                </select>
            </label>
            <label>Siège
                <select name="seat">
                    <option value="Standard" <?= $seat === "Standard" ? "selected" : "" ?>>Standard</option>
                    <option value="Fenêtre" <?= $seat === "Fenêtre" ? "selected" : "" ?>>Fenêtre (+12 €/pers.)</option>
                    <option value="Couloir" <?= $seat === "Couloir" ? "selected" : "" ?>>Couloir (+10 €/pers.)</option>
                    <option value="Espace+" <?= $seat === "Espace+" ? "selected" : "" ?>>Espace+ (+25 €/pers.)</option>
                </select>
            </label>
            <button class="btn btn-secondary" type="submit">Recalculer</button>
        </form>
    </div>

    <aside class="price-panel panel">
        <h2>Tarif détaillé</h2>
        <div class="summary-line"><span>Adulte x <?= (int) $adults ?></span><strong><?= money($fare["adults_total"]) ?></strong></div>
        <div class="summary-line"><span>Enfant x <?= (int) $children ?></span><strong><?= money($fare["children_total"]) ?></strong></div>
        <div class="summary-line"><span>Options adulte</span><strong><?= money($fare["options_per_adult"]) ?>/adulte</strong></div>
        <div class="summary-line"><span>Options enfant</span><strong><?= money($fare["options_per_child"]) ?>/enfant</strong></div>
        <div class="summary-line total-line"><span>Total billet</span><strong><?= money($fare["total"]) ?></strong></div>
        <a class="btn btn-primary full" href="panier.php?<?= http_build_query($cartParams) ?>">Ajouter ce billet au panier</a>
        <a class="btn btn-light full" href="transports.php">Retour aux transports</a>
    </aside>
</section>

<?php include "includes/footer.php"; ?>
