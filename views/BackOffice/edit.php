<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require __DIR__ . '/../../templates/back/header.php';

$data = $old ?? $user ?? [];
?>

<div class="main-content">
    <header class="topbar">
        <div>
            <h1 class="page-title">Modifier un utilisateur</h1>
            <p class="page-sub">Mise à jour des informations du compte</p>
        </div>

        <div class="topbar-right">
            <a href="index.php?action=index" class="btn btn-outline">Retour</a>
        </div>
    </header>

    <div class="content-area">
        <section class="card">
            <div class="card-header">
                <div>
                    <h2 class="card-title">Formulaire de modification</h2>
                    <p class="card-subtitle">Modifier les informations de l’utilisateur #<?= (int)($data['idUser'] ?? 0) ?></p>
                </div>
            </div>

            <?php if (!empty($errors['general'])): ?>
                <div class="alert alert-danger">
                    <p><?= htmlspecialchars($errors['general']) ?></p>
                </div>
            <?php endif; ?>

            <form method="POST" action="index.php?action=update&id=<?= (int)($data['idUser'] ?? 0) ?>">
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Nom</label>
                        <input
                            type="text"
                            name="nom"
                            class="form-control <?= !empty($errors['nom']) ? 'input-error' : '' ?>"
                            value="<?= htmlspecialchars($data['nom'] ?? '') ?>"
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
                            value="<?= htmlspecialchars($data['prenom'] ?? '') ?>"
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
                            value="<?= htmlspecialchars($data['email'] ?? '') ?>"
                        >
                        <?php if (!empty($errors['email'])): ?>
                            <small class="field-error"><?= htmlspecialchars($errors['email']) ?></small>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Nouveau mot de passe</label>
                        <input
                            type="password"
                            name="motDePasse"
                            class="form-control <?= !empty($errors['motDePasse']) ? 'input-error' : '' ?>"
                            placeholder="Laisser vide pour ne pas changer"
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
                            value="<?= htmlspecialchars($data['telephone'] ?? '') ?>"
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
                            value="<?= htmlspecialchars($data['adresse'] ?? '') ?>"
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
                            <option value="actif" <?= (($data['statutCompte'] ?? '') === 'actif') ? 'selected' : '' ?>>Actif</option>
                            <option value="en_attente" <?= (($data['statutCompte'] ?? '') === 'en_attente') ? 'selected' : '' ?>>En attente</option>
                            <option value="bloque" <?= (($data['statutCompte'] ?? '') === 'bloque') ? 'selected' : '' ?>>Bloqué</option>
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
                            <?php foreach ($roles as $role): ?>
                                <option
                                    value="<?= (int)$role['idRole'] ?>"
                                    <?= (($data['idRole'] ?? '') == $role['idRole']) ? 'selected' : '' ?>
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
                    <a href="index.php?action=show&id=<?= (int)($data['idUser'] ?? 0) ?>" class="btn btn-outline">Voir fiche</a>
                    <button type="submit" class="btn btn-primary">Mettre à jour</button>
                </div>
            </form>
        </section>
    </div>
</div>

<?php require __DIR__ . '/../../templates/back/footer.php'; ?>