<?php
// ============================================================
//  VoyageVista — includes/api_panier.php
//  Sauvegarde / lecture du panier en base de données.
//  Actions : save | load | clear | confirm
// ============================================================

ini_set('display_errors', 0);
session_start();
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/db.php';

$userId = $_SESSION['user_id'] ?? null;
if (!$userId) {
    http_response_code(401);
    exit(json_encode(['ok' => false, 'error' => 'Non connecté.']));
}

// Crée la table carts si elle n'existe pas (sans FK pour éviter les erreurs de contrainte)
$db = getDB();
try {
    $db->exec("
        CREATE TABLE IF NOT EXISTS carts (
            id             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            user_id        INT UNSIGNED NOT NULL UNIQUE,
            destination_id INT UNSIGNED DEFAULT NULL,
            transport_id   INT UNSIGNED DEFAULT NULL,
            hotel_id       INT UNSIGNED DEFAULT NULL,
            activity_ids   TEXT         DEFAULT NULL,
            adults         TINYINT UNSIGNED NOT NULL DEFAULT 2,
            children       TINYINT UNSIGNED NOT NULL DEFAULT 0,
            nights         TINYINT UNSIGNED NOT NULL DEFAULT 7,
            bags           VARCHAR(20)  DEFAULT '0',
            ticket         VARCHAR(20)  DEFAULT 'Basic',
            seat           VARCHAR(20)  DEFAULT 'Standard',
            room_type      VARCHAR(20)  DEFAULT 'standard',
            hotel_options  TEXT         DEFAULT NULL,
            checkin        DATE         DEFAULT NULL,
            checkout       DATE         DEFAULT NULL,
            status         ENUM('open','confirmed','cancelled') NOT NULL DEFAULT 'open',
            updated_at     TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB
    ");
} catch (Exception $e) {
    exit(json_encode(['ok' => false, 'error' => 'Erreur création table: ' . $e->getMessage()]));
}

$body   = json_decode(file_get_contents('php://input'), true) ?? [];
$action = $body['action'] ?? ($_GET['action'] ?? 'load');

try { switch ($action) {

    // ── Sauvegarder le panier ──────────────────────────────────
    case 'save':
        $activityIds  = isset($body['activity_ids']) ? implode(',', array_map('intval', (array)$body['activity_ids'])) : '';
        $hotelOptions = isset($body['hotel_options']) ? json_encode((array)$body['hotel_options']) : '[]';

        $stmt = $db->prepare("
            INSERT INTO carts
                (user_id, destination_id, transport_id, hotel_id, activity_ids,
                 adults, children, nights, bags, ticket, seat, room_type,
                 hotel_options, checkin, checkout)
            VALUES
                (:u, :dest, :tr, :ht, :acts,
                 :ad, :ch, :ni, :bags, :ticket, :seat, :room,
                 :hopt, :ci, :co)
            ON DUPLICATE KEY UPDATE
                destination_id = VALUES(destination_id),
                transport_id   = VALUES(transport_id),
                hotel_id       = VALUES(hotel_id),
                activity_ids   = VALUES(activity_ids),
                adults         = VALUES(adults),
                children       = VALUES(children),
                nights         = VALUES(nights),
                bags           = VALUES(bags),
                ticket         = VALUES(ticket),
                seat           = VALUES(seat),
                room_type      = VALUES(room_type),
                hotel_options  = VALUES(hotel_options),
                checkin        = VALUES(checkin),
                checkout       = VALUES(checkout),
                status         = 'open'
        ");
        $stmt->execute([
            ':u'     => $userId,
            ':dest'  => $body['destination_id'] ? (int)$body['destination_id'] : null,
            ':tr'    => $body['transport_id']   ? (int)$body['transport_id']   : null,
            ':ht'    => $body['hotel_id']       ? (int)$body['hotel_id']       : null,
            ':acts'  => $activityIds,
            ':ad'    => max(1, (int)($body['adults']   ?? 2)),
            ':ch'    => max(0, (int)($body['children'] ?? 0)),
            ':ni'    => max(1, (int)($body['nights']   ?? 7)),
            ':bags'  => $body['bags']       ?? '0',
            ':ticket'=> $body['ticket']     ?? 'Basic',
            ':seat'  => $body['seat']       ?? 'Standard',
            ':room'  => $body['room_type']  ?? 'standard',
            ':hopt'  => $hotelOptions,
            ':ci'    => !empty($body['checkin'])  ? $body['checkin']  : null,
            ':co'    => !empty($body['checkout']) ? $body['checkout'] : null,
        ]);
        exit(json_encode(['ok' => true]));

    // ── Charger le panier ──────────────────────────────────────
    case 'load':
        $stmt = $db->prepare('SELECT * FROM carts WHERE user_id = :u AND status = "open" LIMIT 1');
        $stmt->execute([':u' => $userId]);
        $cart = $stmt->fetch();
        if (!$cart) {
            exit(json_encode(['ok' => true, 'cart' => null]));
        }
        // Recompose les ids activités en tableau
        $cart['activity_ids']  = $cart['activity_ids'] ? array_map('intval', explode(',', $cart['activity_ids'])) : [];
        $cart['hotel_options'] = json_decode($cart['hotel_options'] ?? '[]', true);
        exit(json_encode(['ok' => true, 'cart' => $cart]));

    // ── Vider le panier ────────────────────────────────────────
    case 'clear':
        $stmt = $db->prepare('DELETE FROM carts WHERE user_id = :u');
        $stmt->execute([':u' => $userId]);
        exit(json_encode(['ok' => true]));

    // ── Confirmer la commande ──────────────────────────────────
    case 'confirm':
        $stmt = $db->prepare(
            'UPDATE carts SET status = "confirmed" WHERE user_id = :u AND status = "open"'
        );
        $stmt->execute([':u' => $userId]);
        // Optionnel : créer une notification de confirmation
        $notif = $db->prepare(
            'INSERT INTO notifications (user_id, category, title, message, icon, color)
             VALUES (:u, "Réservations", "Réservation confirmée", "Votre panier a été validé avec succès.", "check", "teal")'
        );
        $notif->execute([':u' => $userId]);
        exit(json_encode(['ok' => true]));

    default:
        http_response_code(400);
        exit(json_encode(['ok' => false, 'error' => 'Action inconnue.']));

} } catch (Exception $e) {
    http_response_code(500);
    exit(json_encode(['ok' => false, 'error' => 'Erreur serveur: ' . $e->getMessage()]));
}
