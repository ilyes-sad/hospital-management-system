<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$user = $_SESSION['user'] ?? null;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Medicare</title>
    <link rel="stylesheet" href="/medicare/styles.css">
</head>
<body class="auth-page"></body>