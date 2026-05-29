<?php
require_once "includes/data.php";
$pageTitle = "Réservation confirmée";
include "includes/header.php";
?>

<section class="confirmation-page">
    <div class="confirmation-card panel">
        <div class="confirmation-icon">✓</div>
        <h1>Merci pour votre réservation !</h1>
        <p class="confirmation-subtitle">Votre commande a bien été enregistrée. Vous recevrez une confirmation dans vos notifications.</p>

        <div class="confirmation-actions">
            <a class="btn btn-primary" href="notifications.php">Voir mes notifications</a>
            <a class="btn btn-secondary" href="index.php">Retour à l'accueil</a>
        </div>

        <p class="confirmation-hint muted">Vous pouvez retrouver vos réservations dans votre <a href="compte.php">espace client</a>.</p>
    </div>
</section>

<?php include "includes/footer.php"; ?>
