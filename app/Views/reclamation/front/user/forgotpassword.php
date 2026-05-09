<?php require __DIR__ . '/../../../templates/front/header.php'; ?>

<div class="auth-container">
    <div class="auth-card auth-card-login">
        <div class="login-top">
            <div class="login-badge">🏥 Medicare</div>
            <h2 class="auth-title">Mot de passe oublié</h2>
            <p class="auth-subtitle">Entrez votre email pour recevoir un code.</p>
        </div>

        <?php if (!empty($errors['general'])): ?>
            <div class="alert alert-danger">
                <?= htmlspecialchars($errors['general']) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="index.php?action=sendResetCode" class="login-form">
            <div class="form-group">
                <label for="email">Email :</label>
                <div class="input-wrap">
                    <span class="input-icon">✉️</span>
                    <input
                        id="email"
                        type="email"
                        name="email"
                        placeholder="exemple@email.com"
                        required
                    >
                </div>

                <?php if (!empty($errors['email'])): ?>
                    <small class="error"><?= htmlspecialchars($errors['email']) ?></small>
                <?php endif; ?>
            </div>

            <button type="submit" class="auth-btn auth-btn-login">
                Envoyer le code
            </button>
        </form>

        <p class="auth-footer">
            <a href="index.php?action=login">Retour à la connexion</a>
        </p>
    </div>
</div>

<?php require __DIR__ . '/../../../templates/front/footer.php'; ?>