    <?php
    require __DIR__ . '/../../../templates/front/header.php';
    ?>

    <div class="auth-container">
        <div class="auth-card auth-card-login">
            <div class="login-top">
        <div class="login-badge">🏥 Medicare</div>
        <h2 class="auth-title">Connexion</h2>
        
    </div>

            <?php if (!empty($errors['general'])): ?>
                <div class="alert alert-danger">
                    <?= htmlspecialchars($errors['general']) ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="index.php?action=doLogin" id="loginForm" class="login-form">

                <div class="form-group">
                    <label for="email">Email :</label>
                    <div class="input-wrap">
                        <span class="input-icon">✉️</span>
                        <input
                            id="email"
                            type="email"
                            name="email"
                           value="<?= htmlspecialchars($_POST['email'] ?? $_COOKIE['user_email'] ?? '') ?>"
                            placeholder="exemple@email.com"
                            required
                        >
                    </div>
                    <?php if (!empty($errors['email'])): ?>
                        <small class="error"><?= htmlspecialchars($errors['email']) ?></small>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="motDePasse">Mot de passe :</label>
                    <div class="input-wrap">
                        <span class="input-icon">🔒 </span>
                        <input
                            id="motDePasse"
                            type="password"
                            name="motDePasse"
                            placeholder="Votre mot de passe"
                            required
                        >
                    </div>
                    <?php if (!empty($errors['motDePasse'])): ?>
                        <small class="error"><?= htmlspecialchars($errors['motDePasse']) ?></small>
                    <?php endif; ?>
                </div>
                <div class="form-group remember-me">
    <label>
        <input type="checkbox" name="remember_me" value="1">
        Se souvenir de moi
    </label>
</div>
                
                <button type="submit" class="auth-btn auth-btn-login">
                    Se connecter
                </button>
            </form>

            <p class="auth-footer">
                Pas de compte ?
                <a href="index.php?action=register">Créer un compte</a>
            </p>
            <div class="forgot-container">
        <a href="index.php?action=forgotPassword" class="forgot-link">
            Mot de passe oublié ?
        </a>
    </div>

        </div>
    </div>
    

    <?php require __DIR__ . '/../../../templates/front/footer.php'; ?>