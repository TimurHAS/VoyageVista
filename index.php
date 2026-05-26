<?php
require_once "includes/data.php";
$pageTitle = "Accueil";
$popularDestinations = array_slice($destinations, 0, 4);
include "includes/header.php";
?>

<datalist id="city-list">
    <?php foreach ($cities as $city): ?>
        <option value="<?= htmlspecialchars($city) ?>"></option>
    <?php endforeach; ?>
</datalist>

<section class="hero">
    <div class="hero-content">
        <p class="eyebrow light">VoyageVista</p>
        <h1>Réservez chaque étape de votre voyage au même endroit.</h1>
        <p>Comparez les vols, hébergements, voitures, activités ou packs complets, puis ajoutez uniquement ce dont vous avez besoin.</p>

        <form class="search-panel search-panel-wide" action="resultats.php" method="get" data-service-form>
            <div class="service-tabs" role="tablist" aria-label="Type de recherche">
                <label><input type="radio" name="service" value="vols" checked> Vols</label>
                <label><input type="radio" name="service" value="hebergements"> Hébergement</label>
                <label><input type="radio" name="service" value="voitures"> Voitures</label>
                <label><input type="radio" name="service" value="vacances"> Vacances tout compris</label>
                <label><input type="radio" name="service" value="activites"> Activités</label>
            </div>

            <label data-field="departure">
                <span data-label-depart>Point de départ</span>
                <input type="text" name="departure" list="city-list" data-city-autocomplete placeholder="Paris">
            </label>
            <label data-field="arrival">
                <span data-label-arrival>Point d'arrivée</span>
                <input type="text" name="arrival" list="city-list" data-city-autocomplete placeholder="Lisbonne, Bali, Kyoto">
            </label>
            <label data-field="date_depart">
                Date de départ
                <input type="date" name="date_depart">
            </label>
            <label data-field="date_retour">
                Date de retour
                <input type="date" name="date_retour">
            </label>
            <label data-field="trip_type">
                Trajet
                <select name="trip_type">
                    <option value="aller-retour">Aller-retour</option>
                    <option value="aller-simple">Aller simple</option>
                </select>
            </label>
            <label data-field="adults">
                Adultes
                <input type="number" name="adults" min="1" max="8" value="2">
            </label>
            <label data-field="children">
                Enfants
                <input type="number" name="children" min="0" max="6" value="0">
            </label>
            <label data-field="rooms">
                Chambres
                <input type="number" name="rooms" min="1" value="1">
            </label>
            <button class="btn btn-primary search-submit" type="submit">Rechercher</button>
        </form>
    </div>
</section>

<section class="page-section route-explorer-section">
    <div class="section-head">
        <div>
            <p class="eyebrow">Explorer par ville</p>
            <h2>Où partir, ou d'où partir ?</h2>
        </div>
    </div>

    <div class="route-explorer-grid">
        <form class="panel route-explorer-card" action="ville_options.php" method="get">
            <input type="hidden" name="type" value="from">
            <h3>Je pars depuis</h3>
            <p>Choisissez une ville de départ et voyez les destinations accessibles.</p>
            <label>
                Ville de départ
                <input type="text" name="city" list="city-list" data-city-autocomplete placeholder="Paris">
            </label>
            <button class="btn btn-primary" type="submit">Voir où aller</button>
        </form>

        <form class="panel route-explorer-card" action="ville_options.php" method="get">
            <input type="hidden" name="type" value="to">
            <h3>Je veux aller à</h3>
            <p>Choisissez une destination et voyez les villes de départ possibles.</p>
            <label>
                Destination
                <input type="text" name="city" list="city-list" data-city-autocomplete placeholder="Bali">
            </label>
            <button class="btn btn-primary" type="submit">Voir d'où partir</button>
        </form>
    </div>
</section>

<section class="page-section">
    <div class="section-head">
        <div>
            <p class="eyebrow">Catégories</p>
            <h2>Explorer rapidement</h2>
        </div>
    </div>

    <div class="quick-grid emoji-grid">
        <a href="destinations.php?category=Plage"><span>🏖️</span>Plage</a>
        <a href="destinations.php?category=Ville"><span>🏙️</span>Ville</a>
        <a href="destinations.php?category=Montagne"><span>🏔️</span>Montagne</a>
        <a href="destinations.php?category=Culture"><span>🏛️</span>Culture</a>
        <a href="transports.php?mode=Avion"><span>✈️</span>Avion</a>
        <a href="transports.php?mode=Train"><span>🚆</span>Train</a>
        <a href="transports.php?mode=Bus"><span>🚌</span>Bus</a>
        <a href="transports.php?mode=Ferry"><span>⛴️</span>Ferry</a>
    </div>
</section>

<section class="page-section">
    <div class="section-head">
        <div>
            <p class="eyebrow">Recommandés</p>
            <h2>Séjours populaires</h2>
        </div>
        <a class="text-link" href="destinations.php">Voir toutes les destinations</a>
    </div>

    <div class="card-grid">
        <?php foreach ($popularDestinations as $destination): ?>
            <article class="travel-card">
                <div class="image-wrap">
                    <img src="<?= htmlspecialchars($destination["image"]) ?>" alt="<?= htmlspecialchars($destination["name"]) ?>">
                    <span class="discount-badge"><?= htmlspecialchars($destination["discount"]) ?></span>
                </div>
                <div class="card-body">
                    <span class="tag"><?= htmlspecialchars($destination["pack"]) ?></span>
                    <div class="card-title-row">
                        <h3><?= htmlspecialchars($destination["name"]) ?></h3>
                        <span>⭐ <?= htmlspecialchars($destination["rating"]) ?></span>
                    </div>
                    <p><?= htmlspecialchars($destination["country"]) ?> · <?= htmlspecialchars($destination["duration"]) ?></p>
                    <p><?= htmlspecialchars($destination["description"]) ?></p>
                    <div class="price-stack">
                        <s><?= money($destination["old_price"]) ?></s>
                        <strong>Dès <?= money($destination["price"]) ?></strong>
                    </div>
                    <div class="card-actions">
                        <a class="btn btn-secondary" href="destination_detail.php?id=<?= (int) $destination["id"] ?>">Voir détail</a>
                        <a class="btn btn-primary" href="composer_transport.php?destination=<?= (int) $destination["id"] ?>&arrival=<?= urlencode($destination["name"]) ?>&departure=Paris&adults=2&children=0">Composer</a>
                        <button class="btn btn-light" type="button" data-favorite="<?= htmlspecialchars($destination["name"]) ?>">❤️ Favori</button>
                    </div>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
</section>

<section class="page-section two-column-section">
    <div class="panel">
        <p class="eyebrow">Offres</p>
        <h2>Réductions du moment</h2>
        <div class="offer-mini-list">
            <?php foreach (array_slice($destinations, 0, 3) as $destination): ?>
                <a href="composer_transport.php?destination=<?= (int) $destination["id"] ?>&arrival=<?= urlencode($destination["name"]) ?>&departure=Paris&adults=2&children=0">
                    <span><?= htmlspecialchars($destination["discount"]) ?></span>
                    <strong><?= htmlspecialchars($destination["name"]) ?></strong>
                    <small><?= htmlspecialchars($destination["pack"]) ?> · dès <?= money($destination["price"]) ?></small>
                </a>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="panel">
        <p class="eyebrow">Packs</p>
        <h2>Vacances tout compris</h2>
        <div class="offer-mini-list">
            <?php foreach ($packs as $pack): ?>
                <?php
                $packDestination = findById($destinations, $pack["destination_id"]);
                $packTransport = minItem(byDestination($transports, $pack["destination_id"]));
                $packHotel = minItem(byDestination($hotels, $pack["destination_id"]));
                $packActivity = minItem(byDestination($activities, $pack["destination_id"]));
                ?>
                <a href="panier.php?destination=<?= (int) $pack["destination_id"] ?>&transport=<?= (int) $packTransport["id"] ?>&hotel=<?= (int) $packHotel["id"] ?>&activity=<?= (int) $packActivity["id"] ?>&adults=2&children=0&nights=7">
                    <span><?= htmlspecialchars($pack["discount"]) ?></span>
                    <strong><?= htmlspecialchars($pack["name"]) ?></strong>
                    <small><?= htmlspecialchars($packDestination["name"]) ?> · <?= htmlspecialchars($pack["label"]) ?></small>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="page-section">
    <div class="section-head">
        <div>
            <p class="eyebrow">Avis</p>
            <h2>Ils ont composé leur séjour</h2>
        </div>
    </div>

    <div class="review-grid">
        <?php foreach ($reviews as $review): ?>
            <article class="review-card">
                <strong><?= str_repeat("⭐", (int) $review["rating"]) ?></strong>
                <p><?= htmlspecialchars($review["text"]) ?></p>
                <span><?= htmlspecialchars($review["name"]) ?> · <?= htmlspecialchars($review["destination"]) ?></span>
            </article>
        <?php endforeach; ?>
    </div>
</section>

<?php include "includes/footer.php"; ?>
