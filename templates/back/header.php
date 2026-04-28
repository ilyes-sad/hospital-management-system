<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Sécurité admin
if (!isset($_SESSION['user']) || $_SESSION['user']['nomRole'] !== 'admin') {
    header("Location: /medicare/index.php?action=login");
    exit;
}

// Infos utilisateur connecté
$sessionUser = $_SESSION['user'];
$nom = $sessionUser['nom'] ?? 'Admin';
$prenom = $sessionUser['prenom'] ?? '';
$role = $sessionUser['nomRole'] ?? '';
$id = $sessionUser['idUser'] ?? 0;

$initials = strtoupper(substr($nom, 0, 1) . substr($prenom, 0, 1));
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Medicare Admin</title>

    <link rel="stylesheet" href="/medicare/styles.css">

    <style>
        body {
            margin: 0;
            display: flex;
            font-family: Arial, sans-serif;
        }
    </style>
</head>

<body>

<!-- SIDEBAR -->
<aside class="sidebar">

    <div class="sidebar-logo">
        <h2>Medicare</h2>
    </div>

    <nav class="sidebar-nav">
        <a href="/medicare/index.php?action=index" class="nav-item">👤 Utilisateurs</a>
        <a href="#" class="nav-item">📅 Rendez-vous</a>
        <a href="#" class="nav-item">📄 Demandes</a>
        <a href="#" class="nav-item">⚠️ Réclamations</a>
    </nav>

    <div class="sidebar-footer">
        <a href="/medicare/index.php?action=profile" class="user-card clickable-user">
            <div class="user-avatar"><?= $initials ?></div>
            <div>
                <div><?= htmlspecialchars($nom . ' ' . $prenom) ?></div>
                <small><?= htmlspecialchars($role) ?></small>
            </div>
        </a>

        <a href="/medicare/index.php?action=logout" class="nav-item">
            🚪 Déconnexion
        </a>
    </div>
</aside>

<!-- MAIN CONTENT START -->