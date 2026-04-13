<?php
require __DIR__ . '/../../../templates/front/header.php';
?>

<div class="auth-container">

    <div class="auth-card">
        <h2 class="auth-title">Connexion</h2>

        <?php if (!empty($errors['general'])): ?>
            <div class="alert alert-danger">
                <?= htmlspecialchars($errors['general']) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="index.php?action=doLogin">

            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email"
                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                <?php if (!empty($errors['email'])): ?>
                    <small class="error"><?= $errors['email'] ?></small>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label>Mot de passe</label>
                <input type="password" name="motDePasse" required>
                <?php if (!empty($errors['motDePasse'])): ?>
                    <small class="error"><?= $errors['motDePasse'] ?></small>
                <?php endif; ?>
            </div>

            <button type="submit" class="btn-primary">
                Se connecter
            </button>

        </form>

        <p class="auth-footer">
            Pas de compte ?
            <a href="index.php?action=register">Créer un compte</a>
        </p>
    </div>

</div>

<?php require __DIR__ . '/../../../templates/front/footer.php'; ?>