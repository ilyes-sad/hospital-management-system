<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require __DIR__ . '/../../templates/back/header.php';
?>

<div class="main-content">
    <header class="topbar">
        <div>
            <h1 class="page-title">Ajouter un utilisateur</h1>
            <p class="page-sub">Création d’un nouveau compte utilisateur</p>
        </div>

        <div class="topbar-right">
            <a href="index.php?action=index" class="btn btn-outline">Retour</a>
        </div>
    </header>

    <div class="content-area">
        <section class="card">
            <div class="card-header">
                <div>
                    <h2 class="card-title">Formulaire d’ajout</h2>
                    <p class="card-subtitle">Remplir les informations de l’utilisateur</p>
                </div>
            </div>

            <?php if (!empty($errors['general'])): ?>
                <div class="alert alert-danger">
                    <p><?= htmlspecialchars($errors['general']) ?></p>
                </div>
            <?php endif; ?>

            <form method="POST" action="index.php?action=store">
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Nom</label>
                        <input
                            type="text"
                            name="nom"
                            class="form-control <?= !empty($errors['nom']) ? 'input-error' : '' ?>"
                            value="<?= htmlspecialchars($old['nom'] ?? '') ?>"
                        >
                        <?php if (!empty($errors['nom'])): ?>
                            <small class="field-error"><?= htmlspecialchars($errors['nom']) ?></small>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Prénom</label>
                        <input
                            type="text"
                            name="prenom"
                            class="form-control <?= !empty($errors['prenom']) ? 'input-error' : '' ?>"
                            value="<?= htmlspecialchars($old['prenom'] ?? '') ?>"
                        >
                        <?php if (!empty($errors['prenom'])): ?>
                            <small class="field-error"><?= htmlspecialchars($errors['prenom']) ?></small>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Email</label>
                        <input
                            type="email"
                            name="email"
                            class="form-control <?= !empty($errors['email']) ? 'input-error' : '' ?>"
                            value="<?= htmlspecialchars($old['email'] ?? '') ?>"
                        >
                        <?php if (!empty($errors['email'])): ?>
                            <small class="field-error"><?= htmlspecialchars($errors['email']) ?></small>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Mot de passe</label>
                        <input
                            type="password"
                            name="motDePasse"
                            class="form-control <?= !empty($errors['motDePasse']) ? 'input-error' : '' ?>"
                        >
                        <?php if (!empty($errors['motDePasse'])): ?>
                            <small class="field-error"><?= htmlspecialchars($errors['motDePasse']) ?></small>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Téléphone</label>
                        <input
                            type="text"
                            name="telephone"
                            class="form-control <?= !empty($errors['telephone']) ? 'input-error' : '' ?>"
                            value="<?= htmlspecialchars($old['telephone'] ?? '') ?>"
                        >
                        <?php if (!empty($errors['telephone'])): ?>
                            <small class="field-error"><?= htmlspecialchars($errors['telephone']) ?></small>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Adresse</label>
                        <input
                            type="text"
                            name="adresse"
                            class="form-control"
                            value="<?= htmlspecialchars($old['adresse'] ?? '') ?>"
                        >
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Statut</label>
                        <select
                            name="statutCompte"
                            class="form-control <?= !empty($errors['statutCompte']) ? 'input-error' : '' ?>"
                        >
                            <option value="">Choisir un statut</option>
                            <option value="actif" <?= (($old['statutCompte'] ?? '') === 'actif') ? 'selected' : '' ?>>Actif</option>
                            <option value="en_attente" <?= (($old['statutCompte'] ?? '') === 'en_attente') ? 'selected' : '' ?>>En attente</option>
                            <option value="bloque" <?= (($old['statutCompte'] ?? '') === 'bloque') ? 'selected' : '' ?>>Bloqué</option>
                        </select>
                        <?php if (!empty($errors['statutCompte'])): ?>
                            <small class="field-error"><?= htmlspecialchars($errors['statutCompte']) ?></small>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Rôle</label>
                        <select
                            name="idRole"
                            class="form-control <?= !empty($errors['idRole']) ? 'input-error' : '' ?>"
                        >
                            <option value="">Choisir un rôle</option>
                            <?php foreach ($roles as $role): ?>
                                <option
                                    value="<?= (int)$role['idRole'] ?>"
                                    <?= (($old['idRole'] ?? '') == $role['idRole']) ? 'selected' : '' ?>
                                >
                                    <?= htmlspecialchars($role['nomRole']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (!empty($errors['idRole'])): ?>
                            <small class="field-error"><?= htmlspecialchars($errors['idRole']) ?></small>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="form-actions">
                    <a href="index.php?action=index" class="btn btn-outline">Annuler</a>
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </div>
            </form>
        </section>
    </div>
</div>

<?php require __DIR__ . '/../../templates/back/footer.php'; ?>