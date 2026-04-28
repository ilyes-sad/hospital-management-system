<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require __DIR__ . '/../../templates/back/header.php';

$statut = $user['statutCompte'] ?? 'inconnu';

$badgeClass = 'badge badge-neutral';
if ($statut === 'actif') {
    $badgeClass = 'badge badge-success';
} elseif ($statut === 'en_attente') {
    $badgeClass = 'badge badge-warning';
} elseif ($statut === 'bloque') {
    $badgeClass = 'badge badge-danger';
}

$initials = strtoupper(
    substr(trim($user['nom'] ?? ''), 0, 1) .
    substr(trim($user['prenom'] ?? ''), 0, 1)
);
?>

<div class="main-content">
    <header class="topbar">
        <div>
            <h1 class="page-title">Fiche utilisateur</h1>
            <p class="page-sub">Consultation détaillée du compte</p>
        </div>

        <div class="topbar-right">
            <a href="index.php?action=edit&id=<?=$id ?>" class="btn btn-primary">Modifier</a>
            <a href="index.php?action=index" class="btn btn-outline">Retour</a>
        </div>
    </header>

    <div class="content-area">
        <div class="profile-shell">
            <div class="profile-hero">
                <div class="profile-hero-left">
                    <div class="profile-avatar-lg">
                        <?= htmlspecialchars($initials) ?>
                    </div>

                    <div>
                        <h1 class="profile-name">
                            <?= htmlspecialchars(($user['nom'] ?? '') . ' ' . ($user['prenom'] ?? '')) ?>
                        </h1>
                        <p class="profile-role">
                            <?= htmlspecialchars($user['nomRole'] ?? 'Rôle non défini') ?>
                        </p>

                        <div class="profile-badges">
                            <span class="<?= $badgeClass ?>">
                                <?= htmlspecialchars($statut) ?>
                            </span>
                            <span class="badge badge-info">
                                ID #<?= (int)$user['idUser'] ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="profile-grid">
                <section class="card">
                    <div class="card-header">
                        <div>
                            <h2 class="card-title">Informations personnelles</h2>
                            <p class="card-subtitle">Données générales du compte</p>
                        </div>
                    </div>

                    <div class="detail-grid">
                        <div class="detail-item">
                            <div class="detail-key">Nom</div>
                            <div class="detail-val"><?= htmlspecialchars($user['nom'] ?? '-') ?></div>
                        </div>

                        <div class="detail-item">
                            <div class="detail-key">Prénom</div>
                            <div class="detail-val"><?= htmlspecialchars($user['prenom'] ?? '-') ?></div>
                        </div>

                        <div class="detail-item">
                            <div class="detail-key">Email</div>
                            <div class="detail-val"><?= htmlspecialchars($user['email'] ?? '-') ?></div>
                        </div>

                        <div class="detail-item">
                            <div class="detail-key">Téléphone</div>
                            <div class="detail-val"><?= htmlspecialchars($user['telephone'] ?? '-') ?></div>
                        </div>

                        <div class="detail-item">
                            <div class="detail-key">Adresse</div>
                            <div class="detail-val"><?= htmlspecialchars($user['adresse'] ?? '-') ?></div>
                        </div>

                        <div class="detail-item">
                            <div class="detail-key">Rôle</div>
                            <div class="detail-val"><?= htmlspecialchars($user['nomRole'] ?? '-') ?></div>
                        </div>
                    </div>
                </section>

                <section class="card">
                    <div class="card-header">
                        <div>
                            <h2 class="card-title">Actions</h2>
                            <p class="card-subtitle">Gestion rapide du compte</p>
                        </div>
                    </div>

                    <div class="quick-actions">
                        <a href="index.php?action=edit&id=<?= (int)$user['idUser'] ?>" class="quick-action">
                            <div class="quick-action-icon" style="background: var(--accent-light); color: var(--accent-dark);">
                                ✏️
                            </div>
                            <div>
                                <div class="quick-action-label">Modifier</div>
                                <div class="quick-action-sub">Mettre à jour les informations</div>
                            </div>
                        </a>

                        <a
                            href="index.php?action=delete&id=<?= (int)$user['idUser'] ?>"
                            class="quick-action"
                            onclick="return confirm('Voulez-vous vraiment supprimer cet utilisateur ?');"
                        >
                            <div class="quick-action-icon" style="background: var(--danger-light); color: var(--danger);">
                                🗑️
                            </div>
                            <div>
                                <div class="quick-action-label">Supprimer</div>
                                <div class="quick-action-sub">Retirer définitivement ce compte</div>
                            </div>
                        </a>
                    </div>
                </section>
            </div>
        </div>
    </div>
</div>
<?php var_dump($id); ?>
<?php var_dump($_GET['id']); ?>

<?php require __DIR__ . '/../../templates/back/footer.php'; ?>