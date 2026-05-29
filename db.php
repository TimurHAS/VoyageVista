<?php
// ============================================================
//  VoyageVista — Connexion PDO à la base de données
//  Fichier : includes/db.php
//  Adapter DB_HOST / DB_NAME / DB_USER / DB_PASS selon votre env.
// ============================================================

define('DB_HOST', 'localhost');
define('DB_NAME', 'voyagevista');
define('DB_USER', 'root');       // ← à changer
define('DB_PASS', 'root');        // ← mot de passe MAMP par défaut
define('DB_CHARSET', 'utf8mb4');

function getDB(): PDO
{
    static $pdo = null;

    if ($pdo === null) {
        $dsn = sprintf(
            'mysql:host=%s;dbname=%s;charset=%s',
            DB_HOST, DB_NAME, DB_CHARSET
        );
        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        } catch (PDOException $e) {
            error_log('VoyageVista DB error: ' . $e->getMessage());
            http_response_code(503);
            exit('<p style="font-family:sans-serif;padding:2rem">
                Service temporairement indisponible. Veuillez réessayer plus tard.
              </p>');
        }
    }

    return $pdo;
}
