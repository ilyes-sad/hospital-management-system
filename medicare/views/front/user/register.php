<?php
require __DIR__ . '/../../../templates/front/header.php';
?>

<div class="auth-container">

    <div class="auth-card">
        <h2 class="auth-title">Créer un compte</h2>

        <?php if (!empty($errors['general'])): ?>
            <div class="alert alert-danger">
                <?= htmlspecialchars($errors['general']) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="index.php?action=storeRegister">

            <!-- NOM -->
            <div class="form-group">
                <label>Nom</label>
                <input
                    type="text"
                    name="nom"
                    value="<?= htmlspecialchars($old['nom'] ?? '') ?>"
                    required
                >
                <?php if (!empty($errors['nom'])): ?>
                    <small class="error"><?= $errors['nom'] ?></small>
                <?php endif; ?>
            </div>

            <!-- PRENOM -->
            <div class="form-group">
                <label>Prénom</label>
                <input
                    type="text"
                    name="prenom"
                    value="<?= htmlspecialchars($old['prenom'] ?? '') ?>"
                    required
                >
                <?php if (!empty($errors['prenom'])): ?>
                    <small class="error"><?= $errors['prenom'] ?></small>
                <?php endif; ?>
            </div>

            <!-- EMAIL -->
            <div class="form-group">
                <label>Email</label>
                <input
                    type="email"
                    name="email"
                    value="<?= htmlspecialchars($old['email'] ?? '') ?>"
                    required
                >
                <?php if (!empty($errors['email'])): ?>
                    <small class="error"><?= $errors['email'] ?></small>
                <?php endif; ?>
            </div>

            <!-- PASSWORD -->
            <div class="form-group">
                <label>Mot de passe</label>
                <input
                    type="password"
                    name="motDePasse"
                    required
                >
                <?php if (!empty($errors['motDePasse'])): ?>
                    <small class="error"><?= $errors['motDePasse'] ?></small>
                <?php endif; ?>
            </div>

            <!-- TELEPHONE -->
            <div class="form-group">
                <label>Téléphone</label>
                <input
                    type="text"
                    name="telephone"
                    value="<?= htmlspecialchars($old['telephone'] ?? '') ?>"
                >
                <?php if (!empty($errors['telephone'])): ?>
                    <small class="error"><?= $errors['telephone'] ?></small>
                <?php endif; ?>
            </div>

            <!-- ADRESSE -->
            <div class="form-group">
                <label>Adresse</label>
                <input
                    type="text"
                    name="adresse"
                    value="<?= htmlspecialchars($old['adresse'] ?? '') ?>"
                >
            </div>

            <!-- BUTTON -->
            <button type="submit" class="btn btn-primary btn-block">
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