<?php
// ============================================================
//  VoyageVista — includes/api_avis.php
//  Soumettre un avis hôtel ou plateforme.
//  Actions : hotel | plateforme
// ============================================================

ini_set('display_errors', 0);
session_start();
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/db.php';

$userId = $_SESSION['user_id'] ?? null;
if (!$userId) {
    http_response_code(401);
    exit(json_encode(['ok' => false, 'error' => 'Connectez-vous pour laisser un avis.']));
}

$body   = json_decode(file_get_contents('php://input'), true) ?? [];
$action = $body['action'] ?? '';

try {
    $db = getDB();

    switch ($action) {

        // ── Avis sur un hôtel ──────────────────────────────────
        case 'hotel':
            $hotelId = (int) ($body['hotel_id'] ?? 0);
            $rating  = (int) ($body['rating']   ?? 0);
            $text    = trim($body['text'] ?? '');

            if (!$hotelId || $rating < 1 || $rating > 5 || strlen($text) < 5) {
                http_response_code(400);
                exit(json_encode(['ok' => false, 'error' => 'Données invalides.']));
            }

            // Récupère le nom de l'utilisateur
            $user = $db->prepare('SELECT first_name, last_name FROM users WHERE id = :id LIMIT 1');
            $user->execute([':id' => $userId]);
            $u = $user->fetch();
            $author = trim(($u['first_name'] ?? '') . ' ' . ($u['last_name'] ?? '')) ?: 'Anonyme';

            $stmt = $db->prepare(
                'INSERT INTO hotel_reviews (hotel_id, author, rating, text) VALUES (:hid, :author, :rating, :text)'
            );
            $stmt->execute([
                ':hid'    => $hotelId,
                ':author' => $author,
                ':rating' => $rating,
                ':text'   => $text,
            ]);

            // Met à jour la note moyenne de l'hôtel
            $avg = $db->prepare(
                'UPDATE hotels SET rating = (SELECT ROUND(AVG(rating),1) FROM hotel_reviews WHERE hotel_id = :hid),
                                   reviews = (SELECT COUNT(*) FROM hotel_reviews WHERE hotel_id = :hid2)
                 WHERE id = :hid3'
            );
            $avg->execute([':hid' => $hotelId, ':hid2' => $hotelId, ':hid3' => $hotelId]);

            exit(json_encode(['ok' => true, 'author' => $author, 'rating' => $rating, 'text' => $text]));

        // ── Avis plateforme (destination) ─────────────────────
        case 'plateforme':
            $destination = trim($body['destination'] ?? '');
            $rating      = (int) ($body['rating']      ?? 0);
            $text        = trim($body['text']          ?? '');

            if (!$destination || $rating < 1 || $rating > 5 || strlen($text) < 5) {
                http_response_code(400);
                exit(json_encode(['ok' => false, 'error' => 'Données invalides.']));
            }

            $user = $db->prepare('SELECT first_name, last_name FROM users WHERE id = :id LIMIT 1');
            $user->execute([':id' => $userId]);
            $u = $user->fetch();
            $userName = trim(($u['first_name'] ?? '') . ' ' . ($u['last_name'] ?? '')) ?: 'Anonyme';

            $stmt = $db->prepare(
                'INSERT INTO reviews (user_name, destination, rating, text) VALUES (:name, :dest, :rating, :text)'
            );
            $stmt->execute([
                ':name'   => $userName,
                ':dest'   => $destination,
                ':rating' => $rating,
                ':text'   => $text,
            ]);

            exit(json_encode(['ok' => true, 'user_name' => $userName, 'rating' => $rating, 'text' => $text]));

        default:
            http_response_code(400);
            exit(json_encode(['ok' => false, 'error' => 'Action inconnue.']));
    }

} catch (Exception $e) {
    http_response_code(500);
    exit(json_encode(['ok' => false, 'error' => 'Erreur serveur: ' . $e->getMessage()]));
}
