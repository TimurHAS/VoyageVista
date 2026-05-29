<?php
// ============================================================
//  VoyageVista — compte.php  (version base de données)
// ============================================================
require_once 'includes/data.php';   // charge aussi la session
$pageTitle = 'Compte';

// Si déjà connecté, prépare les infos utilisateur pour affichage
$loggedIn  = isset($_SESSION['user_id']);
$userName  = $_SESSION['user_name']  ?? '';
$userEmail = $_SESSION['user_email'] ?? '';
$userRole  = $_SESSION['user_role']  ?? '';

include 'includes/header.php';
?>

<section class="center-page">
    <div class="account-panel panel">

        <?php if ($loggedIn): ?>
        <!-- ── Espace connecté ───────────────────── -->
        <div>
            <p class="eyebrow">Espace utilisateur</p>
            <h1>Bonjour, <?= htmlspecialchars($userName ?: $userEmail) ?> 👋</h1>
            <p>Rôle : <strong><?= htmlspecialchars(ucfirst($userRole)) ?></strong></p>
        </div>

        <div class="account-links">
            <a class="btn btn-secondary" href="favoris.php">Mes favoris</a>
            <a class="btn btn-secondary" href="notifications.php">Mes notifications</a>
            <a class="btn btn-secondary" href="panier.php">Mon panier</a>
        </div>

        <form class="login-form" id="logout-form">
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
