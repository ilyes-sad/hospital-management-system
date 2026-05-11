<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$baseUrl = '/hospital-management-system-main/public';

$error = $_SESSION['error'] ?? '';
unset($_SESSION['error']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medicare - Inscription</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #0f4c75 0%, #3282b8 50%, #0f4c75 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .register-container {
            width: 100%;
            max-width: 500px;
        }

        .register-card {
            background: white;
            border-radius: 24px;
            padding: 40px;
            box-shadow: 0 25px 80px rgba(0, 0, 0, 0.25);
        }

        .logo {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #0f4c75, #3282b8);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 12px;
            font-size: 28px;
        }

        .logo h1 {
            color: #0f4c75;
            font-size: 24px;
            font-weight: 700;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }

        .form-group {
            margin-bottom: 16px;
        }

        .form-group.full {
            grid-column: 1 / -1;
        }

        .form-group label {
            display: block;
            color: #374151;
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 6px;
        }

        .form-group input {
            width: 100%;
            padding: 14px;
            border: 2px solid #e5e7eb;
            border-radius: 10px;
            font-size: 14px;
            transition: all 0.3s;
        }

        .form-group input:focus {
            outline: none;
            border-color: #3282b8;
            box-shadow: 0 0 0 4px rgba(50, 130, 184, 0.1);
        }

        .register-btn {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #0f4c75, #3282b8);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            margin-top: 10px;
        }

        .register-btn:hover {
            box-shadow: 0 10px 30px rgba(15, 76, 117, 0.3);
        }

        .login-link {
            text-align: center;
            margin-top: 24px;
            color: #6b7280;
            font-size: 14px;
        }

        .login-link a {
            color: #0f4c75;
            font-weight: 600;
            text-decoration: none;
        }

        .alert {
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .alert-error {
            background: #fee2e2;
            color: #dc2626;
        }
    </style>
</head>
<body>

<div class="register-container">
    <div class="register-card">
        <div class="logo">
            <div class="logo-icon">🏥</div>
            <h1>Créer un compte</h1>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-error">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?= $baseUrl ?>/register">
            <div class="form-grid">
                <div class="form-group">
                    <label>Nom *</label>
                    <input type="text" name="nom" placeholder="Votre nom" required>
                </div>
                <div class="form-group">
                    <label>Prénom *</label>
                    <input type="text" name="prenom" placeholder="Votre prénom" required>
                </div>
            </div>

            <div class="form-group">
                <label>Email *</label>
                <input type="email" name="email" placeholder="votre@email.com" required>
            </div>

            <div class="form-group">
                <label>Mot de passe *</label>
                <input type="password" name="motDePasse" placeholder="Minimum 6 caractères" required minlength="6">
            </div>

            <div class="form-grid">
                <div class="form-group">
                    <label>Téléphone</label>
                    <input type="text" name="telephone" placeholder="06 XX XXX XXX">
                </div>
                <div class="form-group">
                    <label>Adresse</label>
                    <input type="text" name="adresse" placeholder="Votre ville">
                </div>
            </div>

            <button type="submit" class="register-btn">S'inscrire</button>
        </form>

        <div class="login-link">
            Déjà un compte? <a href="<?= $baseUrl ?>/login">Se connecter</a>
        </div>
    </div>
</div>

</body>
</html>