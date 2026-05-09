
<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once ROOT_PATH . 'config/database.php';

$pdo = Database::getInstance();
$user = $_SESSION['users'] ?? null;
if (isset($_SESSION['user']['idUser'])) {
    $stmt = $pdo->prepare("
        UPDATE users 
        SET last_activity = NOW()
        WHERE idUser = ?
    ");
    $stmt->execute([$_SESSION['user']['idUser']]);
}

if (!isset($_SESSION['users']) && !empty($_COOKIE['remember_me'])) {

    [$selector, $token] = explode(':', $_COOKIE['remember_me']);

    $stmt = $pdo->prepare("
    SELECT rt.*, u.*, r.nomRole
    FROM remember_tokens rt
    JOIN users u ON u.idUser = rt.user_id
    JOIN role r ON r.idRole = u.idRole
    WHERE rt.selector = ?
    AND rt.expires_at > NOW()
    LIMIT 1
");
$stmt->execute([$selector]);

    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($data && password_verify($token, $data['token_hash'])) {
        $_SESSION['users'] = [
            'idUser' => $data['idUser'],
            'nom' => $data['nom'],
            'prenom' => $data['prenom'],
            'email' => $data['email'],
            'idRole' => $data['idRole']
        ];
    } else {
        setcookie('remember_me', '', time() - 3600, '/', '', false, true);
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Medicare</title>
    <link rel="stylesheet" href="/medicare/styles.css">
</head>
<body class="auth-page"></body>