<?php
$currentPage = basename($_SERVER["PHP_SELF"]);

if (!function_exists("navActive")) {
    function navActive($page, $currentPage)
    {
        return $page === $currentPage ? "active" : "";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle) . " - VoyageVista" : "VoyageVista" ?></title>
    <meta name="description" content="VoyageVista, planification de voyages, séjours, transports, hébergements et activités.">
    <script>
        (function () {
            try {
                if (localStorage.getItem("voyagevista_theme") === "dark") {
                    document.documentElement.classList.add("dark-mode");
                }
            } catch (error) {}
        })();
    </script>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<header class="site-header">
    <div class="nav-wrap">
        <a class="logo" href="index.php" aria-label="Accueil VoyageVista">
            <img class="brand-logo-image logo-image-light" src="logo/VV_logo_clair.png" alt="VoyageVista">
            <img class="brand-logo-image logo-image-dark" src="logo/VV_logo_sombre.png" alt="VoyageVista">
        </a>

        <button class="menu-toggle" type="button" data-menu-toggle aria-label="Ouvrir le menu">
            <span></span>
            <span></span>
            <span></span>
        </button>

        <nav class="main-nav" data-main-nav>
            <a class="<?= navActive("index.php", $currentPage) ?>" href="index.php">Accueil</a>
            <a class="<?= navActive("destinations.php", $currentPage) ?>" href="destinations.php">Destinations</a>
            <a class="<?= navActive("transports.php", $currentPage) ?>" href="transports.php">Transports</a>
            <a class="<?= navActive("hebergements.php", $currentPage) ?>" href="hebergements.php">Hébergements</a>
            <a class="<?= navActive("activites.php", $currentPage) ?>" href="activites.php">Activités</a>
        </nav>

        <div class="header-actions">
            <button class="header-action theme-toggle" type="button" data-theme-toggle aria-label="Changer de thème">
                <span>Mode</span>
            </button>
            <a class="header-action <?= navActive("notifications.php", $currentPage) ?>" href="notifications.php" aria-label="Notifications">
                <span>Notifications</span><b>3</b>
            </a>
            <a class="header-action <?= navActive("favoris.php", $currentPage) ?>" href="favoris.php"><span>Favoris</span></a>
            <a class="header-action <?= navActive("compte.php", $currentPage) ?>" href="compte.php"><span>Compte</span></a>
            <a class="header-action cart <?= navActive("panier.php", $currentPage) ?>" href="panier.php"><span>Panier</span></a>
        </div>
    </div>
</header>

<main class="site-main">
