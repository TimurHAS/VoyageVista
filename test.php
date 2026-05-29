<?php
require_once "includes/db.php";
try {
    $pdo = getDB();
    $stmt = $pdo->query('SELECT COUNT(*) as nb FROM destinations');
    $row  = $stmt->fetch();
    echo "Connexion réussie ! Destinations en base : " . $row['nb'];
} catch (Exception $e) {
    echo "Erreur : " . $e->getMessage();
}
?>