<?php
require __DIR__ . '/../../../templates/front/header.php';
?>

<div class="auth-container">
    <div class="auth-card-register">
        <div class="register-top">
            <span class="register-badge">Inscription</span>
            <h2 class="auth-title">Créer un compte</h2>
           
        </div>

        <?php if (!empty($errors['general'])): ?>
            <div class="alert alert-danger">
                <?= htmlspecialchars($errors['general']) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="index.php?action=storeRegister" id="registerForm" class="register-form">

            <div class="register-grid">
                <div class="form-group">
                    <label for="nom">Nom</label>
                    <div class="input-wrap">
                        <span class="input-icon">👤</span>
                        <input
                            type="text"
                            name="nom"
                            id="nom"
                            value="<?= htmlspecialchars($old['nom'] ?? '') ?>"
                            placeholder="Votre nom"
                            required
                        >
                    </div>
                    <?php if (!empty($errors['nom'])): ?>
                        <small class="error"><?= $errors['nom'] ?></small>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="prenom">Prénom</label>
                    <div class="input-wrap">
                        <span class="input-icon">👤</span>
                        <input
                            type="text"
                            name="prenom"
                            id="prenom"
                            value="<?= htmlspecialchars($old['prenom'] ?? '') ?>"
                            placeholder="Votre prénom"
                            required
                        >
                    </div>
                    <?php if (!empty($errors['prenom'])): ?>
                        <small class="error"><?= $errors['prenom'] ?></small>
                    <?php endif; ?>
                </div>
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <div class="input-wrap">
                    <span class="input-icon">✉️</span>
                    <input
                        type="email"
                        name="email"
                        id="email"
                        value="<?= htmlspecialchars($old['email'] ?? '') ?>"
                        placeholder="exemple@email.com"
                        required
                    >
                </div>
                <?php if (!empty($errors['email'])): ?>
                    <small class="error"><?= $errors['email'] ?></small>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="motDePasse">Mot de passe</label>
                <div class="input-wrap">
                    <span class="input-icon">🔒</span>
                    <input
                        type="password"
                        name="motDePasse"
                        id="motDePasse"
                        placeholder="Choisissez un mot de passe"
                        required
                    >
                </div>
                <?php if (!empty($errors['motDePasse'])): ?>
                    <small class="error"><?= $errors['motDePasse'] ?></small>
                <?php endif; ?>
            </div>

            <div class="register-grid">
                <div class="form-group">
                    <label for="telephone">Téléphone</label>
                    <div class="input-wrap">
                        <span class="input-icon">📞</span>
                        <input
                            type="text"
                            name="telephone"
                            id="telephone"
                            value="<?= htmlspecialchars($old['telephone'] ?? '') ?>"
                            placeholder="Votre téléphone"
                        >
                    </div>
                    <?php if (!empty($errors['telephone'])): ?>
                        <small class="error"><?= $errors['telephone'] ?></small>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="adresse">Adresse</label>
                    <div class="input-wrap">
                        <span class="input-icon">📍</span>
                        <input
                            type="text"
                            name="adresse"
                            id="adresse"
                            value="<?= htmlspecialchars($old['adresse'] ?? '') ?>"
                            placeholder="Votre adresse"
                        >
                    </div>
                </div>
            </div>

            <button type="submit" class="auth-btn-register">
                S'inscrire
            </button>
        </form>

        <p class="auth-footer">
            Déjà un compte ?
            <a href="index.php?action=login">Se connecter</a>
        </p>
    </div>
</div>

<?php require __DIR__ . '/../../../templates/front/footer.php'; ?>