<?php
require_once "includes/data.php";
$pageTitle = "Favoris";
include "includes/header.php";
?>

<section class="center-page" data-auth-required>
    <div class="panel auth-locked" data-auth-locked>
        <p class="eyebrow">Accès connecté</p>
        <h1>Connectez-vous pour voir vos favoris</h1>
        <p>Vos destinations sauvegardées sont associées à votre espace VoyageVista.</p>
        <a class="btn btn-primary" href="compte.php">Se connecter</a>
    </div>

    <div class="panel favorites-panel" data-auth-content>
        <p class="eyebrow">Sélections locales</p>
        <h1>Favoris</h1>
        <p>Retrouvez ici les destinations que vous avez mises de côté pendant votre recherche.</p>

        <div class="favorites-grid" data-favorites-list></div>
        <p class="empty-state" data-favorites-empty>Aucun favori pour l'instant.</p>
        <button class="btn btn-light" type="button" data-clear-favorites>Vider les favoris</button>
    </div>
</section>

<?php include "includes/footer.php"; ?>
