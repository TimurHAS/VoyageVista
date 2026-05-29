<?php
// ============================================================
//  VoyageVista — includes/data.php
//  Remplace les tableaux statiques par des requêtes MySQL.
//  Toutes les variables ($destinations, $hotels, etc.) restent
//  identiques en structure pour que le reste du front ne change pas.
// ============================================================

require_once __DIR__ . '/db.php';

$db = getDB();

// ── Destinations ────────────────────────────────────────────
$stmt = $db->query('SELECT * FROM destinations ORDER BY id');
$destinations = $stmt->fetchAll();

// ── Transports ──────────────────────────────────────────────
// Renomme city_from/city_to → from/to pour compatibilité front
$stmt = $db->query('SELECT *, city_from AS `from`, city_to AS `to` FROM transports ORDER BY id');
$transports = $stmt->fetchAll();

// ── Hôtels + photos + avis ──────────────────────────────────
$stmt = $db->query('SELECT * FROM hotels ORDER BY id');
$hotels = $stmt->fetchAll();

// Récupère les photos et avis pour chaque hôtel
$stmtPhotos  = $db->prepare('SELECT url FROM hotel_photos WHERE hotel_id = :id ORDER BY sort_order');
$stmtReviews = $db->prepare(
    'SELECT author, rating, text FROM hotel_reviews WHERE hotel_id = :id ORDER BY created_at DESC'
);

foreach ($hotels as &$hotel) {
    $stmtPhotos->execute([':id' => $hotel['id']]);
    $photos = array_column($stmtPhotos->fetchAll(), 'url');
    $hotel['photos'] = $photos ?: [$hotel['image'], $hotel['image'], $hotel['image']];

    $stmtReviews->execute([':id' => $hotel['id']]);
    $hotel['recent_reviews'] = $stmtReviews->fetchAll();

    // Décode le JSON des équipements en tableau PHP
    if (is_string($hotel['amenities'])) {
        $hotel['amenities'] = json_decode($hotel['amenities'], true) ?? [];
    }
}
unset($hotel);

// ── Activités ───────────────────────────────────────────────
$stmt = $db->query('SELECT * FROM activities ORDER BY id');
$activities = $stmt->fetchAll();

foreach ($activities as &$activity) {
    if (is_string($activity['included'])) {
        $activity['included'] = json_decode($activity['included'], true) ?? [];
    }
}
unset($activity);

// ── Notifications ────────────────────────────────────────────
// Charge les notifs globales + celles de l'utilisateur connecté (si session ouverte)
session_start();
$userId = $_SESSION['user_id'] ?? null;

if ($userId) {
    $stmt = $db->prepare(
        'SELECT * FROM notifications
         WHERE user_id = :uid OR user_id IS NULL
         ORDER BY created_at DESC'
    );
    $stmt->execute([':uid' => $userId]);
} else {
    $stmt = $db->query(
        'SELECT * FROM notifications WHERE user_id IS NULL ORDER BY created_at DESC'
    );
}
$notifications = $stmt->fetchAll();

// ── Avis plateforme ──────────────────────────────────────────
$stmt   = $db->query('SELECT user_name AS name, destination, rating, text FROM reviews ORDER BY created_at DESC LIMIT 10');
$reviews = $stmt->fetchAll();

// ── Packs ────────────────────────────────────────────────────
$stmt  = $db->query('SELECT * FROM packs ORDER BY id');
$packs = $stmt->fetchAll();

// ── Coordonnées des villes ────────────────────────────────────
$stmt = $db->query('SELECT city, lat, lng FROM city_coordinates');
$cityCoordinates = [];
foreach ($stmt->fetchAll() as $row) {
    $cityCoordinates[$row['city']] = [
        'lat' => (float) $row['lat'],
        'lng' => (float) $row['lng'],
    ];
}
$cities = array_keys($cityCoordinates);

// ── Liaisons de villes ────────────────────────────────────────
$stmt = $db->query(
    'SELECT city_from AS `from`, city_to AS `to`, destination_id, transport_id FROM city_routes'
);
$cityRoutes = $stmt->fetchAll();

// ============================================================
//  Fonctions utilitaires — identiques à l'original
// ============================================================

function money($value): string
{
    return number_format((float) $value, 0, ',', ' ') . ' €';
}

function findById(array $items, int $id): ?array
{
    foreach ($items as $item) {
        if ((int) $item['id'] === $id) {
            return $item;
        }
    }
    return $items[0] ?? null;
}

/**
 * Recherche un item par ID directement en base (évite de charger tout le tableau).
 * Usage : findByIdDB('destinations', 3)
 */
function findByIdDB(string $table, int $id): ?array
{
    $allowed = ['destinations','transports','hotels','activities','packs'];
    if (!in_array($table, $allowed, true)) {
        return null;
    }
    $db   = getDB();
    $stmt = $db->prepare("SELECT * FROM {$table} WHERE id = :id LIMIT 1");
    $stmt->execute([':id' => $id]);
    return $stmt->fetch() ?: null;
}

function byDestination(array $items, int $destinationId): array
{
    return array_values(array_filter(
        $items,
        fn($item) => (int) $item['destination_id'] === $destinationId
    ));
}

function uniqueValues(array $items, string $key): array
{
    $values = [];
    foreach ($items as $item) {
        if (isset($item[$key]) && !in_array($item[$key], $values, true)) {
            $values[] = $item[$key];
        }
    }
    sort($values);
    return $values;
}

function minItem(array $items, string $key = 'price'): ?array
{
    if (empty($items)) {
        return null;
    }
    $lowest = $items[0];
    foreach ($items as $item) {
        if (($item[$key] ?? PHP_INT_MAX) < ($lowest[$key] ?? PHP_INT_MAX)) {
            $lowest = $item;
        }
    }
    return $lowest;
}

function cityPoint(string $city): array
{
    global $cityCoordinates;
    $city  = trim($city);
    $point = $cityCoordinates[$city] ?? null;

    if (!$point) {
        foreach ($cityCoordinates as $name => $candidate) {
            if (stripos($city, $name) !== false || stripos($name, $city) !== false) {
                $point = $candidate;
                break;
            }
        }
    }

    $point = $point ?? $cityCoordinates['Paris'];

    if (isset($point['lat'], $point['lng'])) {
        $point['x'] = round((($point['lng'] + 180) / 360) * 100, 2);
        $point['y'] = round(((90 - $point['lat']) / 180) * 100, 2);
    }

    return $point;
}

function routeDestinationName(array $transport): string
{
    global $destinations;
    $destination = findById($destinations, (int) $transport['destination_id']);
    return $destination['name'] ?? $transport['to'];
}

function vvAdultChildCounts(): array
{
    $adults   = max(1, (int) ($_GET['adults']   ?? ($_GET['persons'] ?? 2)));
    $children = max(0, (int) ($_GET['children'] ?? 0));
    return [$adults, $children, max(1, $adults + $children)];
}

function transportOptionLabels(string $bags = '', string $ticket = '', string $seat = ''): array
{
    $bagLabels = [
        ''       => 'Aucune option bagage',
        '0'      => 'Aucun bagage',
        'cabine' => 'Cabine incluse',
        'soute'  => '1 bagage soute',
    ];
    return array_values(array_filter([
        $bags   !== '' ? ($bagLabels[$bags] ?? $bags) : '',
        $ticket !== '' ? 'Billet ' . $ticket : '',
        $seat   !== '' ? 'Siège '  . $seat   : '',
    ]));
}

function calculateTransportFare(array $transport, int $adults = 1, int $children = 0, string $bags = '0', string $ticket = 'Basic', string $seat = 'Standard'): array
{
    $base     = (float) ($transport['price'] ?? 0);
    $adults   = max(1, $adults);
    $children = max(0, $children);

    $childBase    = round($base * 0.70);
    $bagFee       = match ($bags)   { 'soute' => 40, default => 0 };
    $ticketAdult  = match ($ticket) { 'Flex' => 35, 'Premium' => 80, default => 0 };
    $ticketChild  = match ($ticket) { 'Flex' => 20, 'Premium' => 50, default => 0 };
    $seatFee      = match ($seat)   { 'Fenêtre' => 12, 'Couloir' => 10, 'Espace+' => 25, default => 0 };

    $adultUnit = $base     + $bagFee + $ticketAdult + $seatFee;
    $childUnit = $childBase + $bagFee + $ticketChild + $seatFee;

    return [
        'adult_base'       => $base,
        'child_base'       => $childBase,
        'adult_unit'       => $adultUnit,
        'child_unit'       => $childUnit,
        'adults_total'     => $adultUnit * $adults,
        'children_total'   => $childUnit * $children,
        'options_per_adult'  => $bagFee + $ticketAdult + $seatFee,
        'options_per_child'  => $bagFee + $ticketChild + $seatFee,
        'total'            => ($adultUnit * $adults) + ($childUnit * $children),
    ];
}

function hotelOptionLabels($options): array
{
    if (!is_array($options)) {
        $options = $options !== '' ? [$options] : [];
    }
    $labels = [
        'breakfast' => 'Petit-déjeuner',
        'cancel'    => 'Annulation flexible',
        'parking'   => 'Parking',
        'spa'       => 'Accès spa',
    ];
    return array_map(fn($o) => $labels[$o] ?? $o, $options);
}

function calculateHotelFare(array $hotel, int $nights = 1, int $adults = 1, int $children = 0, string $roomType = 'standard', array $options = []): array
{
    $base     = (float) ($hotel['price'] ?? 0);
    $nights   = max(1, $nights);
    $adults   = max(1, $adults);
    $children = max(0, $children);

    $roomExtra  = match ($roomType) { 'family' => 35, 'view' => 55, 'suite' => 90, default => 0 };
    $optionNight = 0;
    $optionStay  = 0;

    foreach ($options as $opt) {
        match ($opt) {
            'breakfast' => ($optionNight += ($adults * 12) + ($children * 7)),
            'cancel'    => ($optionNight += 8),
            'parking'   => ($optionNight += 10),
            'spa'       => ($optionStay  += $adults * 25),
            default     => null,
        };
    }

    $nightUnit = $base + $roomExtra + $optionNight;
    return [
        'base'         => $base,
        'room_extra'   => $roomExtra,
        'option_night' => $optionNight,
        'option_stay'  => $optionStay,
        'night_unit'   => $nightUnit,
        'total'        => ($nightUnit * $nights) + $optionStay,
    ];
}

function calculateActivityFare(array $activity, int $adults = 1, int $children = 0): array
{
    $base     = (float) ($activity['price'] ?? 0);
    $adults   = max(1, $adults);
    $children = max(0, $children);
    $child    = round($base * 0.70);

    return [
        'adult_unit'    => $base,
        'child_unit'    => $child,
        'adults_total'  => $base  * $adults,
        'children_total'=> $child * $children,
        'total'         => ($base * $adults) + ($child * $children),
    ];
}

function roomTypeLabel(string $roomType): string
{
    return [
        'standard' => 'Chambre standard',
        'family'   => 'Chambre familiale',
        'view'     => 'Chambre avec vue',
        'suite'    => 'Suite',
    ][$roomType] ?? 'Chambre standard';
}
