<?php
// ============================================================
//  VoyageVista — favoris.php  (version base de données)
// ============================================================
require_once 'includes/data.php';   // charge la session
$pageTitle = 'Favoris';

$loggedIn  = isset($_SESSION['user_id']);
$userFavorites = [];

if ($loggedIn) {
    // Charge les favoris directement depuis la BDD
    $db   = getDB();
    // La table favorites est créée dans api_favoris.php si elle n'existe pas
    try {
        $stmt = $db->prepare(
            'SELECT d.* FROM favorites f
             JOIN destinations d ON d.id = f.destination_id
             WHERE f.user_id = :u ORDER BY f.created_at DESC'
        );
        $stmt->execute([':u' => $_SESSION['user_id']]);
        $userFavorites = $stmt->fetchAll();
    } catch (Exception $e) {
        // La table n'existe pas encore, favoris vides
        $userFavorites = [];
    }
}

include 'includes/header.php';
?>

<section class="center-page">

    <?php if (!$loggedIn): ?>
    <!-- ── Non connecté ────────────────────────────────── -->
    <div class="panel auth-locked">
        <p class="eyebrow">Accès connecté</p>
        <h1>Connectez-vous pour voir vos favoris</h1>
        <p>Vos destinations sauvegardées sont associées à votre espace VoyageVista.</p>
        <a class="btn btn-primary" href="compte.php">Se connecter</a>
    </div>

    <?php else: ?>
    <!-- ── Connecté : liste des favoris ─────────────────── -->
    <div class="panel favorites-panel">
        <p class="eyebrow">Sélections sauvegardées</p>
        <h1>Favoris</h1>
        <p>Retrouvez ici les destinations que vous avez mises de côté.</p>

        <?php if (empty($userFavorites)): ?>
            <p class="empty-state">Aucun favori pour l'instant.</p>
        <?php else: ?>
        <div class="favorites-grid">
            <?php foreach ($userFavorites as $dest): ?>
            <article class="destination-card">
                <a href="destination_detail.php?id=<?= (int) $dest['id'] ?>">
                    <img src="<?= htmlspecialchars($dest['image']) ?>"
                         alt="<?= htmlspecialchars($dest['name']) ?>">
                </a>
                <div class="card-body">
                    <h3><?= htmlspecialchars($dest['name']) ?></h3>
                    <p><?= htmlspecialchars($dest['country']) ?> · <?= htmlspecialchars($dest['category']) ?></p>
                    <p><?= htmlspecialchars($dest['duration']) ?> · à partir de <strong><?= money($dest['price']) ?></strong></p>
                    <button class="btn btn-light btn-sm remove-fav"
                            data-id="<?= (int) $dest['id'] ?>">
                        Retirer
                    </button>
                </div>
            </article>
            <?php endforeach; ?>
        </div>

        <button class="btn btn-light" id="clear-favorites">Vider les favoris</button>
        <?php endif; ?>
    </div>
    <?php endif; ?>

</section>

<?php if ($loggedIn): ?>
<script>
// ── Retirer un favori ────────────────────────────────────────
document.querySelectorAll('.remove-fav').forEach(btn => {
    btn.addEventListener('click', async () => {
        const id = btn.dataset.id;
        await fetch('includes/api_favoris.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ action: 'remove', destination_id: Number(id) }),
        });
        btn.closest('article').remove();
    });
});

// ── Vider tous les favoris ────────────────────────────────────
const clearBtn = document.getElementById('clear-favorites');
if (clearBtn) {
    clearBtn.addEventListener('click', async () => {
        if (!confirm('Vider tous vos favoris ?')) return;
        await fetch('includes/api_favoris.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ action: 'clear' }),
        });
        location.reload();
    });
}
</script>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>
