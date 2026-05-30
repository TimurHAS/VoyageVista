<?php
// ============================================================
//  VoyageVista — includes/api_admin.php
//  API JSON réservée aux administrateurs.
// ============================================================

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/roles.php';

header('Content-Type: application/json; charset=utf-8');
session_start();

if (!isAdmin()) {
    http_response_code(403);
    exit(json_encode(['ok' => false, 'error' => 'Accès refusé.']));
}

$body   = json_decode(file_get_contents('php://input'), true) ?? [];
$action = $body['action'] ?? ($_GET['action'] ?? '');
$db     = getDB();

switch ($action) {

    // ── Liste des utilisateurs ────────────────────────────────
    case 'list_users':
        $stmt  = $db->query('SELECT id, email, first_name, last_name, role, created_at FROM users ORDER BY id');
        $users = $stmt->fetchAll();
        exit(json_encode(['ok' => true, 'users' => $users]));

    // ── Modifier le rôle d'un utilisateur ────────────────────
    case 'set_role':
        $targetId = (int) ($body['user_id'] ?? 0);
        $newRole  = $body['role'] ?? '';
        $allowed  = ['visiteur', 'client', 'partenaire', 'admin'];

        if (!$targetId || !in_array($newRole, $allowed, true)) {
            http_response_code(400);
            exit(json_encode(['ok' => false, 'error' => 'Paramètres invalides.']));
        }
        // Un admin ne peut pas se rétrograder lui-même
        if ($targetId === (int) $_SESSION['user_id'] && $newRole !== 'admin') {
            http_response_code(400);
            exit(json_encode(['ok' => false, 'error' => 'Vous ne pouvez pas modifier votre propre rôle.']));
        }

        $stmt = $db->prepare('UPDATE users SET role = :role WHERE id = :id');
        $stmt->execute([':role' => $newRole, ':id' => $targetId]);
        exit(json_encode(['ok' => true]));

    // ── Supprimer un utilisateur ──────────────────────────────
    case 'delete_user':
        $targetId = (int) ($body['user_id'] ?? 0);
        if (!$targetId || $targetId === (int) $_SESSION['user_id']) {
            http_response_code(400);
            exit(json_encode(['ok' => false, 'error' => 'Action impossible.']));
        }
        $stmt = $db->prepare('DELETE FROM users WHERE id = :id');
        $stmt->execute([':id' => $targetId]);
        exit(json_encode(['ok' => true]));

    default:
        http_response_code(400);
        exit(json_encode(['ok' => false, 'error' => 'Action inconnue.']));
}
