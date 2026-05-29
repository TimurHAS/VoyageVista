<?php
// ============================================================
//  VoyageVista — includes/auth.php
//  API JSON pour connexion, inscription et déconnexion.
//  Appel : POST includes/auth.php  (action dans le body JSON)
// ============================================================

require_once __DIR__ . '/db.php';

header('Content-Type: application/json; charset=utf-8');
session_start();

$body   = json_decode(file_get_contents('php://input'), true) ?? [];
$action = $body['action'] ?? ($_GET['action'] ?? '');

switch ($action) {

    // ── Connexion ─────────────────────────────────────────────
    case 'login':
        $email    = trim($body['email']    ?? '');
        $password = trim($body['password'] ?? '');

        if (!$email || !$password) {
            http_response_code(400);
            exit(json_encode(['ok' => false, 'error' => 'Champs requis manquants.']));
        }

        $db   = getDB();
        $stmt = $db->prepare('SELECT id, email, password, first_name, last_name, role FROM users WHERE email = :email LIMIT 1');
        $stmt->execute([':email' => $email]);
        $user = $stmt->fetch();

        if (!$user || !password_verify($password, $user['password'])) {
            http_response_code(401);
            exit(json_encode(['ok' => false, 'error' => 'Email ou mot de passe incorrect.']));
        }

        // Régénère l'ID de session pour prévenir la fixation
        session_regenerate_id(true);
        $_SESSION['user_id']    = $user['id'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role']  = $user['role'];
        $_SESSION['user_name']  = trim($user['first_name'] . ' ' . $user['last_name']);

        exit(json_encode([
            'ok'   => true,
            'user' => [
                'id'        => $user['id'],
                'email'     => $user['email'],
                'name'      => $_SESSION['user_name'],
                'role'      => $user['role'],
            ],
        ]));

    // ── Inscription ───────────────────────────────────────────
    case 'register':
        $email     = trim($body['email']      ?? '');
        $password  = trim($body['password']   ?? '');
        $firstName = trim($body['first_name'] ?? '');
        $lastName  = trim($body['last_name']  ?? '');

        if (!$email || !$password) {
            http_response_code(400);
            exit(json_encode(['ok' => false, 'error' => 'Email et mot de passe requis.']));
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            http_response_code(400);
            exit(json_encode(['ok' => false, 'error' => 'Email invalide.']));
        }
        if (strlen($password) < 8) {
            http_response_code(400);
            exit(json_encode(['ok' => false, 'error' => 'Mot de passe trop court (8 caractères min).']));
        }

        $db   = getDB();
        $chk  = $db->prepare('SELECT id FROM users WHERE email = :email LIMIT 1');
        $chk->execute([':email' => $email]);
        if ($chk->fetch()) {
            http_response_code(409);
            exit(json_encode(['ok' => false, 'error' => 'Cet email est déjà utilisé.']));
        }

        $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
        $ins  = $db->prepare(
            'INSERT INTO users (email, password, first_name, last_name, role) VALUES (:email, :pwd, :fn, :ln, "client")'
        );
        $ins->execute([
            ':email' => $email,
            ':pwd'   => $hash,
            ':fn'    => $firstName,
            ':ln'    => $lastName,
        ]);
        $newId = (int) $db->lastInsertId();

        session_regenerate_id(true);
        $_SESSION['user_id']    = $newId;
        $_SESSION['user_email'] = $email;
        $_SESSION['user_role']  = 'client';
        $_SESSION['user_name']  = trim($firstName . ' ' . $lastName);

        exit(json_encode([
            'ok'   => true,
            'user' => [
                'id'    => $newId,
                'email' => $email,
                'name'  => $_SESSION['user_name'],
                'role'  => 'client',
            ],
        ]));

    // ── Déconnexion ───────────────────────────────────────────
    case 'logout':
        session_unset();
        session_destroy();
        exit(json_encode(['ok' => true]));

    // ── Statut session ────────────────────────────────────────
    case 'status':
        if (isset($_SESSION['user_id'])) {
            exit(json_encode([
                'ok'        => true,
                'logged_in' => true,
                'user'      => [
                    'id'    => $_SESSION['user_id'],
                    'email' => $_SESSION['user_email'],
                    'name'  => $_SESSION['user_name'],
                    'role'  => $_SESSION['user_role'],
                ],
            ]));
        }
        exit(json_encode(['ok' => true, 'logged_in' => false]));

    default:
        http_response_code(400);
        exit(json_encode(['ok' => false, 'error' => 'Action inconnue.']));
}
