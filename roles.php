<?php
// ============================================================
//  VoyageVista — includes/roles.php
//  Fonctions d'accès et de contrôle des rôles utilisateur.
//  Hiérarchie : visiteur < client < partenaire < admin
// ============================================================

const ROLE_HIERARCHY = ['visiteur' => 0, 'client' => 1, 'partenaire' => 2, 'admin' => 3];

function currentRole(): string
{
    return $_SESSION['user_role'] ?? 'visiteur';
}

function isLoggedIn(): bool
{
    return isset($_SESSION['user_id']);
}

function hasRole(string|array $roles): bool
{
    $current = currentRole();
    foreach ((array) $roles as $role) {
        if ($current === $role) return true;
    }
    return false;
}

function hasMinRole(string $minRole): bool
{
    $current = ROLE_HIERARCHY[currentRole()] ?? 0;
    $min     = ROLE_HIERARCHY[$minRole] ?? 0;
    return $current >= $min;
}

function isAdmin(): bool    { return hasRole('admin'); }
function isPartner(): bool  { return hasMinRole('partenaire'); }
function isClient(): bool   { return hasMinRole('client'); }

/**
 * Bloque l'accès si l'utilisateur n'est pas connecté.
 */
function requireAuth(string $redirect = 'compte.php'): void
{
    if (!isLoggedIn()) {
        header('Location: ' . $redirect);
        exit;
    }
}

/**
 * Bloque l'accès si l'utilisateur n'a pas le rôle requis (ou supérieur).
 * $roles peut être une chaîne ou un tableau de rôles acceptés.
 */
function requireRole(string|array $roles, string $redirect = 'compte.php'): void
{
    requireAuth($redirect);
    foreach ((array) $roles as $role) {
        if (hasMinRole($role)) return;
    }
    http_response_code(403);
    header('Location: ' . $redirect);
    exit;
}
