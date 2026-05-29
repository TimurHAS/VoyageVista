<?php
// ============================================================
//  VoyageVista — includes/api_favoris.php
//  API JSON pour gérer les favoris en base de données.
//  POST  { action: 'add'|'remove'|'list'|'clear', destination_id: N }
// ============================================================

require_once __DIR__ . '/db.php';

header('Content-Type: application/json; charset=utf-8');
session_start();

$userId = $_SESSION['user_id'] ?? null;
if (!$userId) {
    http_response_code(401);
    exit(json_encode(['ok' => false, 'error' => 'Non connecté.']));
}

$body   = json_decode(file_get_contents('php://input'), true) ?? [];
$action = $body['action'] ?? ($_GET['action'] ?? 'list');
$destId = isset($body['destination_id']) ? (int) $body['destination_id'] : 0;

$db = getDB();

// Crée la table si elle n'existe pas encore (idempotent)
$db->exec("
    CREATE TABLE IF NOT EXISTS favorites (
        user_id        INT UNSIGNED NOT NULL,
        destination_id INT UNSIGNED NOT NULL,
        created_at     TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (user_id, destination_id),
        FOREIGN KEY (user_id)        REFERENCES users(id)        ON DELETE CASCADE,
        FOREIGN KEY (destination_id) REFERENCES destinations(id) ON DELETE CASCADE
    ) ENGINE=InnoDB
");

switch ($action) {

    case 'add':
        if (!$destId) { http_response_code(400); exit(json_encode(['ok'=>false,'error'=>'destination_id requis.'])); }
        $stmt = $db->prepare('INSERT IGNORE INTO favorites (user_id, destination_id) VALUES (:u, :d)');
        $stmt->execute([':u' => $userId, ':d' => $destId]);
        exit(json_encode(['ok' => true]));

    case 'remove':
        if (!$destId) { http_response_code(400); exit(json_encode(['ok'=>false,'error'=>'destination_id requis.'])); }
        $stmt = $db->prepare('DELETE FROM favorites WHERE user_id = :u AND destination_id = :d');
        $stmt->execute([':u' => $userId, ':d' => $destId]);
        exit(json_encode(['ok' => true]));

    case 'clear':
        $stmt = $db->prepare('DELETE FROM favorites WHERE user_id = :u');
        $stmt->execute([':u' => $userId]);
        exit(json_encode(['ok' => true]));

    case 'list':
    default:
        $stmt = $db->prepare(
            'SELECT d.* FROM favorites f
             JOIN destinations d ON d.id = f.destination_id
             WHERE f.user_id = :u
             ORDER BY f.created_at DESC'
        );
        $stmt->execute([':u' => $userId]);
        exit(json_encode(['ok' => true, 'favorites' => $stmt->fetchAll()]));
}
