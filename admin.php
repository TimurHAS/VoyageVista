<?php
// ============================================================
//  VoyageVista — admin.php
//  Panneau d'administration — gestion des utilisateurs.
// ============================================================
require_once 'includes/data.php';
require_once 'includes/roles.php';

requireRole('admin');

$pageTitle = 'Administration';
include 'includes/header.php';
?>

<section class="center-page" style="padding:2rem 1rem">
    <div class="panel" style="max-width:900px;margin:0 auto;padding:2rem">

        <div style="display:flex;align-items:center;gap:1rem;margin-bottom:2rem">
            <a href="compte.php" style="color:var(--blue);font-size:1.2rem">&#8592;</a>
            <div>
                <p class="eyebrow" style="margin:0">Administration</p>
                <h1 style="margin:0">Gestion des utilisateurs</h1>
            </div>
        </div>

        <div id="admin-message" style="display:none" class="form-message"></div>

        <table class="admin-table" id="users-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Rôle</th>
                    <th>Inscrit le</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <tr><td colspan="6" style="text-align:center;color:var(--gray-500)">Chargement…</td></tr>
            </tbody>
        </table>

    </div>
</section>

<style>
.admin-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.93rem;
}
.admin-table th {
    text-align: left;
    padding: 0.5rem 0.75rem;
    color: var(--gray-500);
    font-weight: 600;
    border-bottom: 2px solid var(--gray-200);
    white-space: nowrap;
}
.admin-table td {
    padding: 0.6rem 0.75rem;
    border-bottom: 1px solid var(--gray-100);
    vertical-align: middle;
}
.admin-table tr:last-child td { border-bottom: none; }

.role-badge {
    display: inline-block;
    padding: 0.15rem 0.6rem;
    border-radius: 99px;
    font-size: 0.78rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .04em;
}
.role-badge.admin      { background:#f0e0ff; color:#7c3aed; }
.role-badge.partenaire { background:#fff3cd; color:#92600a; }
.role-badge.client     { background:#d1f0e4; color:#155c3a; }
.role-badge.visiteur   { background:var(--gray-100); color:var(--gray-500); }

.admin-table select {
    padding: 0.25rem 0.5rem;
    border: 1px solid var(--gray-200);
    border-radius: var(--radius);
    background: var(--white);
    color: var(--text);
    font-size: 0.88rem;
}
.btn-sm {
    padding: 0.25rem 0.75rem;
    font-size: 0.82rem;
    border-radius: var(--radius);
    border: none;
    cursor: pointer;
    font-weight: 600;
}
.btn-save   { background: var(--blue); color: #fff; }
.btn-delete { background: var(--danger); color: #fff; margin-left: 0.4rem; }
.btn-save:hover   { opacity: 0.88; }
.btn-delete:hover { opacity: 0.88; }
</style>

<script>
const ROLES = ['visiteur', 'client', 'partenaire', 'admin'];
const CURRENT_USER_ID = <?= (int) $_SESSION['user_id'] ?>;

async function apiAdmin(action, body = {}) {
    const res  = await fetch('includes/api_admin.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ action, ...body }),
    });
    return res.json();
}

function showMessage(text, ok = true) {
    const el = document.getElementById('admin-message');
    el.textContent = text;
    el.style.display = 'block';
    el.style.color = ok ? 'var(--success)' : 'var(--danger)';
    setTimeout(() => { el.style.display = 'none'; }, 3500);
}

function badgeHtml(role) {
    return `<span class="role-badge ${role}">${role}</span>`;
}

function renderRow(user) {
    const isSelf = user.id === CURRENT_USER_ID;
    const options = ROLES.map(r =>
        `<option value="${r}" ${r === user.role ? 'selected' : ''}>${r}</option>`
    ).join('');
    const date = user.created_at ? user.created_at.split(' ')[0] : '—';

    return `<tr data-id="${user.id}">
        <td style="color:var(--gray-500)">${user.id}</td>
        <td>${user.first_name ?? ''} ${user.last_name ?? ''}</td>
        <td>${user.email}</td>
        <td>${badgeHtml(user.role)}</td>
        <td style="color:var(--gray-500)">${date}</td>
        <td>
            <select class="role-select">${options}</select>
            <button class="btn-sm btn-save" onclick="saveRole(${user.id}, this)">Enregistrer</button>
            ${!isSelf ? `<button class="btn-sm btn-delete" onclick="deleteUser(${user.id}, this)">Supprimer</button>` : ''}
        </td>
    </tr>`;
}

async function loadUsers() {
    const json = await apiAdmin('list_users');
    const tbody = document.querySelector('#users-table tbody');
    if (!json.ok) {
        tbody.innerHTML = `<tr><td colspan="6" style="color:var(--danger)">Erreur chargement.</td></tr>`;
        return;
    }
    tbody.innerHTML = json.users.map(renderRow).join('');
}

async function saveRole(userId, btn) {
    const row    = btn.closest('tr');
    const select = row.querySelector('.role-select');
    const role   = select.value;

    btn.disabled = true;
    const json = await apiAdmin('set_role', { user_id: userId, role });
    btn.disabled = false;

    if (json.ok) {
        row.querySelector('.role-badge').className = `role-badge ${role}`;
        row.querySelector('.role-badge').textContent = role;
        showMessage('Rôle mis à jour.');
    } else {
        showMessage(json.error || 'Erreur.', false);
    }
}

async function deleteUser(userId, btn) {
    if (!confirm('Supprimer cet utilisateur ? Cette action est irréversible.')) return;
    btn.disabled = true;
    const json = await apiAdmin('delete_user', { user_id: userId });
    if (json.ok) {
        btn.closest('tr').remove();
        showMessage('Utilisateur supprimé.');
    } else {
        btn.disabled = false;
        showMessage(json.error || 'Erreur.', false);
    }
}

loadUsers();
</script>

<?php include 'includes/footer.php'; ?>
