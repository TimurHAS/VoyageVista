<?php
// ============================================================
//  VoyageVista — includes/api_notifications.php
//  API JSON : marquer lu / supprimer une notification.
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
$action = $body['action'] ?? 'read_all';
$notifId = isset($body['id']) ? (int) $body['id'] : 0;

$db = getDB();

switch ($action) {

    case 'read':
        if (!$notifId) { exit(json_encode(['ok'=>false,'error'=>'id requis'])); }
        $stmt = $db->prepare(
            'UPDATE notifications SET is_read = 1
             WHERE id = :id AND (user_id = :u OR user_id IS NULL)'
        );
        $stmt->execute([':id' => $notifId, ':u' => $userId]);
        exit(json_encode(['ok' => true]));

    case 'read_all':
        $stmt = $db->prepare(
            'UPDATE notifications SET is_read = 1
             WHERE user_id = :u OR user_id IS NULL'
        );
        $stmt->execute([':u' => $userId]);
        exit(json_encode(['ok' => true]));

    case 'delete':
        if (!$notifId) { exit(json_encode(['ok'=>false,'error'=>'id requis'])); }
        // On ne supprime que les notifs personnelles, pas les globales
        $stmt = $db->prepare(
            'DELETE FROM notifications WHERE id = :id AND user_id = :u'
        );
        $stmt->execute([':id' => $notifId, ':u' => $userId]);
        exit(json_encode(['ok' => true]));

    default:
        http_response_code(400);
        exit(json_encode(['ok' => false, 'error' => 'Action inconnue.']));
}
