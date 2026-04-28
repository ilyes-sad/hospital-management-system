<?php require __DIR__ . '/../../../templates/front/header.php'; ?>

<div class="auth-container">
    <div class="auth-card auth-card-login">
        <div class="login-top">
            <div class="login-badge">🏥 Medicare</div>
            <h2 class="auth-title">Réinitialisation</h2>
            <p class="auth-subtitle">Saisissez le code reçu par email.</p>
        </div>

        <?php if (!empty($errors['general'])): ?>
            <div class="alert alert-danger">
                <?= htmlspecialchars($errors['general']) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="index.php?action=resetPassword" class="login-form">
            <div class="form-group">
                <label for="code">Code :</label>
                <div class="input-wrap">
                    <span class="input-icon">🔐</span>
                    <input
                        id="code"
                        type="text"
                        name="code"
                        placeholder="Code de vérification"
                        required
                    >
                </div>

                <?php if (!empty($errors['code'])): ?>
                    <small class="error"><?= htmlspecialchars($errors['code']) ?></small>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="password">Nouveau mot de passe :</label>
                <div class="input-wrap">
                    <span class="input-icon">🔒</span>
                    <input
                        id="password"
                        type="password"
                        name="password"
                        placeholder="Nouveau mot de passe"
                        required
                    >
                </div>
            </div>

            <button type="submit" class="auth-btn auth-btn-login">
                Réinitialiser
            </button>
        </form>

        <p class="auth-footer">
            <a href="index.php?action=login">Retour à la connexion</a>
        </p>
    </div>
</div>

<?php require __DIR__ . '/../../../templates/front/footer.php'; ?>