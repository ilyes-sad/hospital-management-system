<?php require __DIR__ . '/../../../templates/front/header.php'; ?>

<div class="profile-page">
    <div class="profile-shell">

        <div class="profile-hero">
            <div class="profile-hero-left">
                <div class="profile-avatar-lg">⚠️</div>

                <div>
                    <h1 class="profile-name">Nouvelle réclamation</h1>
                    <p class="profile-role">
                        Décrivez votre problème pour que l’administration puisse le traiter.
                    </p>
                </div>
            </div>

            <div class="profile-hero-actions">
                <a href="index.php?action=myReclamations" class="btn btn-outline">
                    Mes réclamations
                </a>
            </div>
        </div>

        <section class="card reclamation-card">
            <div class="card-header">
                <div>
                    <h2 class="card-title">Formulaire de réclamation</h2>
                    <p class="card-subtitle">Merci de remplir les informations ci-dessous</p>
                </div>
            </div>

            <form method="POST" action="index.php?action=storeReclamation">

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Catégorie</label>
                        <select name="idCategorie" class="form-control">
                            <option value="">Choisir une catégorie</option>

                            <?php foreach ($categories as $categorie): ?>
                                <option value="<?= $categorie['idCategorie'] ?>"
                                    <?= (($old['idCategorie'] ?? '') == $categorie['idCategorie']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($categorie['libelleCategorie']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>

                        <?php if (!empty($errors['idCategorie'])): ?>
                            <small class="form-error"><?= htmlspecialchars($errors['idCategorie']) ?></small>
                        <?php endif; ?>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Service hospitalier</label>
                        <select name="idServiceHosp" class="form-control">
                            <option value="">Choisir un service</option>

                            <?php foreach ($services as $service): ?>
                                <option value="<?= $service['idServiceHosp'] ?>"
                                    <?= (($old['idServiceHosp'] ?? '') == $service['idServiceHosp']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($service['nomService'] . ' - ' . $service['nomHopital']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>

                        <?php if (!empty($errors['idServiceHosp'])): ?>
                            <small class="form-error"><?= htmlspecialchars($errors['idServiceHosp']) ?></small>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Objet</label>
                    <input 
                        type="text" 
                        name="objet" 
                        class="form-control"
                        placeholder="Exemple : Retard important au service cardiologie"
                        value="<?= htmlspecialchars($old['objet'] ?? '') ?>"
                    >

                    <?php if (!empty($errors['objet'])): ?>
                        <small class="form-error"><?= htmlspecialchars($errors['objet']) ?></small>
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label class="form-label">Description</label>
                    <textarea 
                        name="description" 
                        class="form-control textarea-large"
                        placeholder="Expliquez votre problème en détail..."
                    ><?= htmlspecialchars($old['description'] ?? '') ?></textarea>

                    <?php if (!empty($errors['description'])): ?>
                        <small class="form-error"><?= htmlspecialchars($errors['description']) ?></small>
                    <?php endif; ?>
                </div>

                <div class="form-actions">
                    <a href="index.php?action=profile" class="btn btn-outline">Annuler</a>
                    <button type="submit" class="btn btn-primary">Envoyer la réclamation</button>
                </div>

            </form>
        </section>

    </div>
</div>

<?php require __DIR__ . '/../../../templates/front/footer.php'; ?>