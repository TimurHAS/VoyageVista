</main>

<footer class="site-footer">
    <div class="footer-grid">
        <div>
            <a class="logo footer-logo" href="index.php">
                <img class="brand-logo-image logo-image-light" src="logo/VV_logo_clair.png" alt="VoyageVista">
                <img class="brand-logo-image logo-image-dark" src="logo/VV_logo_sombre.png" alt="VoyageVista">
            </a>
            <p>Voyages, vols, hébergements, activités et packs personnalisés dans une interface claire et rapide.</p>
        </div>
        <div>
            <h3>Navigation</h3>
            <a href="destinations.php">Destinations</a>
            <a href="transports.php">Transports</a>
            <a href="hebergements.php">Hébergements</a>
            <a href="activites.php">Activités</a>
        </div>
        <div>
            <h3>Services</h3>
            <p>Composez un séjour complet ou réservez uniquement le service dont vous avez besoin.</p>
        </div>
    </div>
</footer>

<div class="toast-zone" data-toast-zone aria-live="polite" aria-atomic="true"></div>
<script>
    window.voyageVistaCities = <?= json_encode($cities ?? [], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?>;
</script>
<script src="assets/js/main.js"></script>
</body>
</html>
