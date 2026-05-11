<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$baseUrl = '/hospital-management-system-main/public';

$error = $_SESSION['error'] ?? '';
$success = $_SESSION['success'] ?? '';
unset($_SESSION['error'], $_SESSION['success']);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medicare - Connexion</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Plus Jakarta Sans', 'Inter', sans-serif;
            min-height: 100vh;
            background: 
                linear-gradient(135deg, rgba(15, 76, 117, 0.97) 0%, rgba(50, 130, 184, 0.9) 100%),
                url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse"><path d="M 10 0 L 0 0 0 10" fill="none" stroke="rgba(255,255,255,0.05)" stroke-width="0.5"/></pattern></defs><rect fill="url(%23grid)" width="100" height="100"/></svg>');
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
            overflow: hidden;
        }

        /* Decorative circles */
        body::before {
            content: '';
            position: absolute;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            top: -200px;
            right: -200px;
            animation: float 20s ease-in-out infinite;
        }

        body::after {
            content: '';
            position: absolute;
            width: 400px;
            height: 400px;
            background: radial-gradient(circle, rgba(5, 150, 105, 0.15) 0%, transparent 70%);
            bottom: -100px;
            left: -100px;
            animation: float 15s ease-in-out infinite reverse;
        }

        @keyframes float {
            0%, 100% { transform: translate(0, 0); }
            50% { transform: translate(30px, 30px); }
        }

        .login-container {
            width: 100%;
            max-width: 420px;
            position: relative;
            z-index: 10;
        }

        .login-card {
            background: rgba(255, 255, 255, 0.97);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            padding: 48px 40px;
            box-shadow: 
                0 25px 50px -12px rgba(0, 0, 0, 0.25),
                0 0 0 1px rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .login-header {
            text-align: center;
            margin-bottom: 36px;
        }

        .login-header .logo-wrapper {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #0f4c75 0%, #3282b8 100%);
            border-radius: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            box-shadow: 0 8px 32px rgba(15, 76, 117, 0.3);
            position: relative;
            overflow: hidden;
        }

        .login-header .logo-wrapper::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 50%;
            background: linear-gradient(to bottom, rgba(255,255,255,0.2), transparent);
            border-radius: 24px 24px 0 0;
        }

        .login-header .logo-wrapper i {
            font-size: 40px;
            color: white;
            position: relative;
            z-index: 1;
        }

        .login-header h1 {
            color: #0f172a;
            font-size: 28px;
            font-weight: 700;
            letter-spacing: -0.5px;
            margin-bottom: 8px;
        }

        .login-header p {
            color: #64748b;
            font-size: 15px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #334155;
            font-size: 14px;
            font-weight: 500;
            margin-bottom: 8px;
        }

        .form-group label i {
            color: #0f4c75;
            font-size: 14px;
        }

        .input-wrapper {
            position: relative;
        }

        .input-wrapper i {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 16px;
            transition: color 0.3s;
            z-index: 1;
        }

        .input-wrapper input {
            width: 100%;
            padding: 16px 16px 16px 48px;
            border: 2px solid #e2e8f0;
            border-radius: 14px;
            font-size: 15px;
            font-weight: 500;
            color: #1e293b;
            background: #f8fafc;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .input-wrapper input::placeholder {
            color: #94a3b8;
            font-weight: 400;
        }

        .input-wrapper input:focus {
            outline: none;
            border-color: #0f4c75;
            background: white;
            box-shadow: 0 0 0 4px rgba(15, 76, 117, 0.1), 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .input-wrapper input:focus + i,
        .input-wrapper:focus-within i {
            color: #0f4c75;
        }

        .form-options {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin: 24px 0;
            padding: 0 4px;
        }

        .remember-me {
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
        }

        .remember-me input[type="checkbox"] {
            width: 20px;
            height: 20px;
            accent-color: #0f4c75;
            cursor: pointer;
            border-radius: 6px;
        }

        .remember-me span {
            color: #475569;
            font-size: 14px;
            font-weight: 500;
        }

        .forgot-link {
            color: #0f4c75;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            transition: color 0.2s;
        }

        .forgot-link:hover {
            color: #3282b8;
        }

        .login-btn {
            width: 100%;
            padding: 18px;
            background: linear-gradient(135deg, #0f4c75 0%, #3282b8 100%);
            color: white;
            border: none;
            border-radius: 14px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 16px rgba(15, 76, 117, 0.3);
            position: relative;
            overflow: hidden;
        }

        .login-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }

        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(15, 76, 117, 0.4);
        }

        .login-btn:hover::before {
            left: 100%;
        }

        .login-btn:active {
            transform: translateY(0);
        }

        .divider {
            display: flex;
            align-items: center;
            margin: 28px 0;
            color: #94a3b8;
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: linear-gradient(to right, transparent, #e2e8f0, transparent);
        }

        .divider span {
            padding: 0 16px;
            font-size: 13px;
            font-weight: 500;
        }

        .register-prompt {
            text-align: center;
            color: #64748b;
            font-size: 15px;
        }

        .register-prompt a {
            color: #0f4c75;
            font-weight: 600;
            text-decoration: none;
            transition: color 0.2s;
        }

        .register-prompt a:hover {
            color: #3282b8;
            text-decoration: underline;
        }

        .alert {
            padding: 16px 20px;
            border-radius: 12px;
            margin-bottom: 24px;
            font-size: 14px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .alert-error {
            background: linear-gradient(135deg, #fef2f2, #fee2e2);
            color: #dc2626;
            border: 1px solid #fecaca;
        }

        .alert-error i {
            font-size: 18px;
        }

        .alert-success {
            background: linear-gradient(135deg, #f0fdf4, #dcfce7);
            color: #16a34a;
            border: 1px solid #bbf7d0;
        }

        /* Decorative medical elements */
        .floating-icons {
            position: absolute;
            width: 100%;
            height: 100%;
            pointer-events: none;
            overflow: hidden;
        }

        .floating-icon {
            position: absolute;
            opacity: 0.1;
            color: white;
            animation: drift 20s linear infinite;
        }

        .floating-icon:nth-child(1) { top: 10%; left: 10%; animation-delay: 0s; font-size: 60px; }
        .floating-icon:nth-child(2) { top: 20%; right: 15%; animation-delay: -5s; font-size: 40px; }
        .floating-icon:nth-child(3) { bottom: 30%; left: 20%; animation-delay: -10s; font-size: 50px; }
        .floating-icon:nth-child(4) { bottom: 10%; right: 10%; animation-delay: -15s; font-size: 35px; }

        @keyframes drift {
            0% { transform: translateY(0) rotate(0deg); opacity: 0.1; }
            50% { opacity: 0.15; }
            100% { transform: translateY(-100px) rotate(360deg); opacity: 0.1; }
        }
    </style>
</head>
<body>

    <div class="floating-icons">
        <i class="fas fa-user-md floating-icon"></i>
        <i class="fas fa-heartbeat floating-icon"></i>
        <i class="fas fa-ambulance floating-icon"></i>
        <i class="fas fa-notes-medical floating-icon"></i>
    </div>

    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <div class="logo-wrapper">
                    <i class="fas fa-heart-pulse"></i>
                </div>
                <h1>Medicare</h1>
                <p>Connectez-vous pour prendre rendez-vous</p>
            </div>

            <?php if ($error): ?>
                <div class="alert alert-error">
                    <i class="fas fa-circle-exclamation"></i>
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="alert alert-success">
                    <i class="fas fa-circle-check"></i>
                    <?= htmlspecialchars($success) ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="<?= $baseUrl ?>/login">
                <div class="form-group">
                    <label>
                        <i class="fas fa-envelope"></i>
                        Adresse email
                    </label>
                    <div class="input-wrapper">
                        <input type="email" name="email" placeholder="exemple@email.com" required>
                        <i class="fas fa-envelope"></i>
                    </div>
                </div>

                <div class="form-group">
                    <label>
                        <i class="fas fa-lock"></i>
                        Mot de passe
                    </label>
                    <div class="input-wrapper">
                        <input type="password" name="motDePasse" placeholder="••••••••••••" required>
                        <i class="fas fa-lock"></i>
                    </div>
                </div>

                <div class="form-options">
                    <label class="remember-me">
                        <input type="checkbox" name="remember_me" value="1">
                        <span>Se souvenir de moi</span>
                    </label>
                    <a href="<?= $baseUrl ?>/forgot-password" class="forgot-link">
                        Mot de passe oublié?
                    </a>
                </div>

                <button type="submit" class="login-btn">
                    <i class="fas fa-sign-in-alt"></i>
                    Se connecter
                </button>
            </form>

            <div class="divider">
                <span>OU</span>
            </div>

            <div class="register-prompt">
                Vous n'avez pas de compte? 
                <a href="<?= $baseUrl ?>/register">Créer un compte</a>
            </div>
        </div>
    </div>

</body>
</html>