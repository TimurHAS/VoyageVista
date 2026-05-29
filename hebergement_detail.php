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

<?php if (!empty($hotel['recent_reviews'])): ?>
<section class="reviews-section panel" style="max-width:900px;margin:24px auto;">
    <h2>Avis clients</h2>
    <div class="reviews-list" id="reviews-list">
        <?php foreach ($hotel['recent_reviews'] as $review): ?>
        <article class="review-item">
            <div class="review-header">
                <strong><?= htmlspecialchars($review['author']) ?></strong>
                <span class="review-stars"><?= str_repeat('★', (int)$review['rating']) ?><?= str_repeat('☆', 5 - (int)$review['rating']) ?></span>
            </div>
            <p><?= htmlspecialchars($review['text']) ?></p>
        </article>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

<section class="review-form-section panel" style="max-width:900px;margin:24px auto;">
    <h2>Laisser un avis</h2>
    <?php if (!empty($_SESSION['user_id'])): ?>
    <form id="hotel-review-form">
        <input type="hidden" id="review-hotel-id" value="<?= (int)$hotel['id'] ?>">
        <label>Note
            <select id="review-rating">
                <option value="5">★★★★★ Excellent</option>
                <option value="4">★★★★☆ Très bien</option>
                <option value="3">★★★☆☆ Bien</option>
                <option value="2">★★☆☆☆ Moyen</option>
                <option value="1">★☆☆☆☆ Décevant</option>
            </select>
        </label>
        <label>Votre commentaire
            <textarea id="review-text" rows="4" placeholder="Partagez votre expérience..." style="width:100%;margin-top:6px;padding:10px;border-radius:8px;border:1px solid var(--color-border,#ddd);resize:vertical;"></textarea>
        </label>
        <button class="btn btn-primary" type="submit" style="margin-top:12px;">Publier l'avis</button>
        <p id="review-msg" style="margin-top:8px;color:green;"></p>
    </form>
    <script>
    document.getElementById('hotel-review-form').addEventListener('submit', function(e) {
        e.preventDefault();
        var btn = this.querySelector('button[type=submit]');
        btn.disabled = true;
        fetch('includes/api_avis.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({
                action: 'hotel',
                hotel_id: parseInt(document.getElementById('review-hotel-id').value),
                rating: parseInt(document.getElementById('review-rating').value),
                text: document.getElementById('review-text').value
            })
        })
        .then(r => r.json())
        .then(data => {
            if (data.ok) {
                document.getElementById('review-msg').textContent = 'Avis publié, merci !';
                var list = document.getElementById('reviews-list');
                if (!list) { list = document.createElement('div'); list.id='reviews-list'; this.closest('section').before(list); }
                var art = document.createElement('article');
                art.className = 'review-item';
                art.innerHTML = '<div class="review-header"><strong>' + data.author + '</strong><span class="review-stars">' + '★'.repeat(data.rating) + '☆'.repeat(5-data.rating) + '</span></div><p>' + data.text + '</p>';
                list.prepend(art);
                document.getElementById('review-text').value = '';
            } else {
                document.getElementById('review-msg').style.color = 'red';
                document.getElementById('review-msg').textContent = data.error || 'Erreur.';
            }
            btn.disabled = false;
        })
        .catch(() => { document.getElementById('review-msg').style.color='red'; document.getElementById('review-msg').textContent='Erreur réseau.'; btn.disabled=false; });
    });
    </script>
    <?php else: ?>
    <p><a href="compte.php">Connectez-vous</a> pour laisser un avis.</p>
    <?php endif; ?>
</section>

<?php include "includes/footer.php"; ?>
