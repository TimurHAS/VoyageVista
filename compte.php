<?php
require_once "includes/data.php";
$pageTitle = "Compte";
include "includes/header.php";
?>

<section class="center-page">
    <div class="account-panel panel">
        <div>
            <p class="eyebrow">Espace utilisateur</p>
            <h1>Identification</h1>
            <p>Connectez-vous pour retrouver vos favoris, finaliser votre panier et suivre vos notifications de voyage.</p>
        </div>

        <form class="login-form" data-login-form>
            <label>
                Email
                <input type="email" name="email" placeholder="client@voyagevista.fr" required>
            </label>
            <label>
                Mot de passe
                <input type="password" name="password" placeholder="Mot de passe" required>
            </label>
            <button class="btn btn-primary" type="submit">Se connecter</button>
            <p class="form-message" data-login-message></p>
        </form>

        <section class="roles-grid" aria-label="Espaces utilisateurs">
            <article>
                <h2>Visiteur</h2>
                <p>Consulte les offres.</p>
            </article>
            <article>
                <h2>Client</h2>
                <p>Ajoute au panier et réserve.</p>
            </article>
            <article>
                <h2>Partenaire</h2>
                <p>Propose hôtels ou activités.</p>
            </article>
            <article>
                <h2>Admin</h2>
                <p>Modère les contenus.</p>
            </article>
        </section>
    </div>
</section>

<?php include "includes/footer.php"; ?>
