<?php
// ============================================================
//  VoyageVista — compte.php  (version base de données)
// ============================================================
require_once 'includes/data.php';
require_once 'includes/roles.php';
$pageTitle = 'Compte';

// Si déjà connecté, prépare les infos utilisateur pour affichage
$loggedIn  = isset($_SESSION['user_id']);
$userName  = $_SESSION['user_name']  ?? '';
$userEmail = $_SESSION['user_email'] ?? '';
$userRole  = $_SESSION['user_role']  ?? '';

// Charge l'historique des réservations si connecté
$bookings = [];
if ($loggedIn) {
    try {
        $stmtB = $db->prepare(
            'SELECT b.*,
                    d.name AS dest_name,
                    h.name AS hotel_name,
                    t.city_from, t.city_to
             FROM bookings b
             LEFT JOIN destinations d ON d.id = b.destination_id
             LEFT JOIN hotels h       ON h.id = b.hotel_id
             LEFT JOIN transports t   ON t.id = b.transport_id
             WHERE b.user_id = :u
             ORDER BY b.created_at DESC
             LIMIT 10'
        );
        $stmtB->execute([':u' => $_SESSION['user_id']]);
        $bookings = $stmtB->fetchAll();
    } catch (Exception $e) {
        $bookings = []; // table pas encore créée
    }
}

include 'includes/header.php';
?>

<section class="center-page">
    <div class="account-panel panel">

        <?php if ($loggedIn): ?>
        <!-- ── Espace connecté ───────────────────── -->
        <div>
            <p class="eyebrow">Espace utilisateur</p>
            <h1>Bonjour, <?= htmlspecialchars($userName ?: $userEmail) ?> 👋</h1>
            <p>Rôle : <span class="role-badge role-badge-<?= htmlspecialchars($userRole) ?>"><?= htmlspecialchars(ucfirst($userRole)) ?></span></p>
        </div>

        <div class="account-links">
            <a class="btn btn-secondary" href="favoris.php">Mes favoris</a>
            <a class="btn btn-secondary" href="notifications.php">Mes notifications</a>
            <a class="btn btn-secondary" href="panier.php">Mon panier</a>
            <?php if (isPartner()): ?>
            <a class="btn btn-secondary" href="partenaire.php">Mes offres partenaire</a>
            <?php endif; ?>
            <?php if (isAdmin()): ?>
            <a class="btn btn-primary" href="admin.php">Panneau d'administration</a>
            <?php endif; ?>
        </div>

        <?php if (isAdmin()): ?>
        <div class="role-info-box role-info-admin">
            <strong>Administrateur</strong> — Vous avez accès à la gestion complète des utilisateurs, des contenus et des rôles.
        </div>
        <?php elseif (isPartner()): ?>
        <div class="role-info-box role-info-partenaire">
            <strong>Partenaire</strong> — Vous pouvez proposer et gérer vos hébergements ou activités depuis l'espace partenaire.
        </div>
        <?php elseif (isClient()): ?>
        <div class="role-info-box role-info-client">
            <strong>Client</strong> — Vous pouvez réserver, gérer votre panier et consulter vos favoris.
        </div>
        <?php endif; ?>

        <?php if (!empty($bookings)): ?>
        <div class="bookings-history" style="margin-top:2rem;">
            <h2 style="margin-bottom:1rem;">Mes réservations</h2>
            <div class="bookings-list">
            <?php foreach ($bookings as $bk): ?>
                <article class="booking-item panel" style="padding:1rem;margin-bottom:1rem;border-left:4px solid var(--color-primary,#0077cc);">
                    <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:.5rem;">
                        <div>
                            <strong><?= htmlspecialchars($bk['dest_name'] ?? 'Destination inconnue') ?></strong>
                            <?php if ($bk['hotel_name']): ?>
                            — <?= htmlspecialchars($bk['hotel_name']) ?>
                            <?php endif; ?>
                        </div>
                        <span class="role-badge role-badge-client" style="font-size:.8rem;">
                            <?= $bk['status'] === 'confirmed' ? 'Confirmée' : 'Annulée' ?>
                        </span>
                    </div>
                    <p class="muted" style="margin:.4rem 0 0;">
                        <?php if ($bk['checkin'] && $bk['checkout']): ?>
                        📅 <?= htmlspecialchars($bk['checkin']) ?> → <?= htmlspecialchars($bk['checkout']) ?>
                        (<?= (int)$bk['nights'] ?> nuit<?= $bk['nights'] > 1 ? 's' : '' ?>)
                        &nbsp;·&nbsp;
                        <?php endif; ?>
                        👥 <?= (int)$bk['adults'] ?> adulte<?= $bk['adults'] > 1 ? 's' : '' ?>
                        <?php if ($bk['children'] > 0): ?>
                        + <?= (int)$bk['children'] ?> enfant<?= $bk['children'] > 1 ? 's' : '' ?>
                        <?php endif; ?>
                        &nbsp;·&nbsp; 🗓 Réservé le <?= date('d/m/Y', strtotime($bk['created_at'])) ?>
                    </p>
                </article>
            <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <form class="login-form" id="logout-form" style="margin-top:1.5rem;">
            <button class="btn btn-light" type="submit">Se déconnecter</button>
            <p class="form-message" id="logout-message"></p>
        </form>

        <?php else: ?>
        <!-- ── Formulaire de connexion ───────────── -->
        <div>
            <p class="eyebrow">Espace utilisateur</p>
            <h1>Identification</h1>
            <p>Connectez-vous pour retrouver vos favoris, finaliser votre panier et suivre vos notifications de voyage.</p>
        </div>

        <form class="login-form" id="login-form">
            <label>
                Email
                <input type="email" name="email" placeholder="client@voyagevista.fr" required>
            </label>
            <label>
                Mot de passe
                <input type="password" name="password" placeholder="Mot de passe" required>
            </label>
            <button class="btn btn-primary" type="submit">Se connecter</button>
            <p class="form-message" id="login-message"></p>
        </form>

        <hr style="margin:1.5rem 0">

        <!-- ── Formulaire d'inscription ───────────── -->
        <details>
            <summary style="cursor:pointer;font-weight:600;margin-bottom:1rem">Créer un compte</summary>
            <form class="login-form" id="register-form">
                <label>
                    Prénom
                    <input type="text" name="first_name" placeholder="Marie">
                </label>
                <label>
                    Nom
                    <input type="text" name="last_name" placeholder="Dupont">
                </label>
                <label>
                    Email
                    <input type="email" name="email" placeholder="nouveau@voyagevista.fr" required>
                </label>
                <label>
                    Mot de passe (8 caractères min.)
                    <input type="password" name="password" minlength="8" required>
                </label>
                <button class="btn btn-primary" type="submit">Créer mon compte</button>
                <p class="form-message" id="register-message"></p>
            </form>
        </details>

        <section class="roles-grid" aria-label="Espaces utilisateurs">
            <article><h2>Visiteur</h2><p>Consulte les offres.</p></article>
            <article><h2>Client</h2><p>Ajoute au panier et réserve.</p></article>
            <article><h2>Partenaire</h2><p>Propose hôtels ou activités.</p></article>
            <article><h2>Admin</h2><p>Modère les contenus.</p></article>
        </section>
        <?php endif; ?>

    </div>
</section>

<script>
// ── Connexion ────────────────────────────────────────────────
const loginForm = document.getElementById('login-form');
if (loginForm) {
    loginForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const msg = document.getElementById('login-message');
        msg.textContent = 'Connexion en cours…';
        const data = Object.fromEntries(new FormData(loginForm));
        try {
            const res  = await fetch('includes/auth.php?action=login', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'login', ...data }),
            });
            const json = await res.json();
            if (json.ok) {
                msg.textContent = 'Connecté ! Redirection…';
                setTimeout(() => location.reload(), 700);
            } else {
                msg.textContent = json.error || 'Erreur de connexion.';
            }
        } catch {
            msg.textContent = 'Erreur réseau.';
        }
    });
}

// ── Inscription ──────────────────────────────────────────────
const registerForm = document.getElementById('register-form');
if (registerForm) {
    registerForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const msg = document.getElementById('register-message');
        msg.textContent = 'Création en cours…';
        const data = Object.fromEntries(new FormData(registerForm));
        try {
            const res  = await fetch('includes/auth.php?action=register', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'register', ...data }),
            });
            const json = await res.json();
            if (json.ok) {
                msg.textContent = 'Compte créé ! Redirection…';
                setTimeout(() => location.reload(), 700);
            } else {
                msg.textContent = json.error || 'Erreur lors de l\'inscription.';
            }
        } catch {
            msg.textContent = 'Erreur réseau.';
        }
    });
}

// ── Déconnexion ──────────────────────────────────────────────
const logoutForm = document.getElementById('logout-form');
if (logoutForm) {
    logoutForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const msg = document.getElementById('logout-message');
        msg.textContent = 'Déconnexion…';
        await fetch('includes/auth.php?action=logout', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ action: 'logout' }),
        });
        location.reload();
    });
}
</script>

<?php include 'includes/footer.php'; ?>
