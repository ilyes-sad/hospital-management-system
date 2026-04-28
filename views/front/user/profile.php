<?php require __DIR__ . '/../../../templates/front/header.php'; ?>

<?php
$user = $_SESSION['user'] ?? [];

$nom = $user['nom'] ?? '';
$prenom = $user['prenom'] ?? '';
$email = $user['email'] ?? '';
$role = $user['nomRole'] ?? 'Utilisateur';
$telephone = $user['telephone'] ?? 'Non renseigné';
$adresse = $user['adresse'] ?? 'Non renseignée';
$statut = $user['statutCompte'] ?? 'actif';

$initials = strtoupper(substr($nom, 0, 1) . substr($prenom, 0, 1));

$badgeClass = 'badge badge-success';
if ($statut === 'bloque') {
    $badgeClass = 'badge badge-danger';
} elseif ($statut === 'en_attente') {
    $badgeClass = 'badge badge-warning';
}
?>

<div class="profile-page">
    <div class="profile-shell">

        <div class="profile-hero">
            <div class="profile-hero-left">
                <div class="profile-avatar-lg">
                    <?= htmlspecialchars($initials) ?>
                </div>

                <div>
                    <h1 class="profile-name">
                        <?= htmlspecialchars(trim($nom . ' ' . $prenom)) ?>
                    </h1>
                    <p class="profile-role">
                        <?= htmlspecialchars($role) ?>
                    </p>

                    <div class="profile-badges">
                        <span class="<?= $badgeClass ?>">
                            <?= htmlspecialchars($statut) ?>
                        </span>

                        <span class="badge badge-info">
                            Compte personnel
                        </span>
                    </div>
                </div>
            </div>
            <div class="profile-hero-actions">
            <?php if (($user['nomRole'] ?? '') === 'admin') : ?>
            <a href="index.php?action=index" class="btn btn-primary">
            Espace Admin
            </a>
            <?php endif; ?>
            <a href="index.php?action=logout" class="btn btn-outline">
        Déconnexion
    </a>
</div>
            

        </div>

        <div class="profile-grid">

            <section class="card">
                <div class="card-header">
                    <div>
                        <h2 class="card-title">Informations personnelles</h2>
                        <p class="card-subtitle">Résumé de votre compte Medicare</p>
                    </div>
                </div>

                <div class="detail-grid">
                    <div class="detail-item">
                        <div class="detail-key">Nom</div>
                        <div class="detail-val"><?= htmlspecialchars($nom ?: '-') ?></div>
                    </div>

                    <div class="detail-item">
                        <div class="detail-key">Prénom</div>
                        <div class="detail-val"><?= htmlspecialchars($prenom ?: '-') ?></div>
                    </div>

                    <div class="detail-item">
                        <div class="detail-key">Email</div>
                        <div class="detail-val"><?= htmlspecialchars($email ?: '-') ?></div>
                    </div>

                    <div class="detail-item">
                        <div class="detail-key">Téléphone</div>
                        <div class="detail-val"><?= htmlspecialchars($telephone ?: '-') ?></div>
                    </div>

                    <div class="detail-item">
                        <div class="detail-key">Adresse</div>
                        <div class="detail-val"><?= htmlspecialchars($adresse ?: '-') ?></div>
                    </div>

                    <div class="detail-item">
                        <div class="detail-key">Rôle</div>
                        <div class="detail-val"><?= htmlspecialchars($role) ?></div>
                    </div>
                </div>
            </section>

            <section class="card">
                <div class="card-header">
                    <div>
                        <h2 class="card-title">Actions rapides</h2>
                        <p class="card-subtitle">Accès direct aux fonctions principales</p>
                    </div>
                </div>

                <div class="quick-actions">
                    <a href="index.php?action=edit" class="quick-action">
                        <div class="quick-action-icon" style="background: var(--accent-light); color: var(--accent-dark);">
                            👤
                        </div>
                        <div>
                            <div class="quick-action-label">Modifier profil</div>
                            <div class="quick-action-sub">Mettre à jour vos informations</div>
                        </div>
                    </a>

                    <a href="#" class="quick-action">
                        <div class="quick-action-icon" style="background: var(--teal-light); color: var(--teal);">
                            📅
                        </div>
                        <div>
                            <div class="quick-action-label">Mes rendez-vous</div>
                            <div class="quick-action-sub">Consulter vos réservations</div>
                        </div>
                    </a>

                    <a href="#" class="quick-action">
                        <div class="quick-action-icon" style="background: var(--warning-light); color: var(--warning);">
                            📄
                        </div>
                        <div>
                            <div class="quick-action-label">Mes demandes</div>
                            <div class="quick-action-sub">Suivre les demandes administratives</div>
                        </div>
                    </a>

                    <a href="#" class="quick-action">
                        <div class="quick-action-icon" style="background: var(--danger-light); color: var(--danger);">
                            ⚠️
                        </div>
                        <div>
                            <div class="quick-action-label">Mes réclamations</div>
                            <div class="quick-action-sub">Suivre les réclamations envoyées</div>
                        </div>
                    </a>
                </div>
            </section>

            <section class="card profile-wide">
                <div class="card-header">
                    <div>
                        <h2 class="card-title">Vue d’ensemble</h2>
                        <p class="card-subtitle">Votre espace personnel sur la plateforme</p>
                    </div>
                </div>

                <div class="stats-grid profile-stats">
                    <div class="stat-card" style="--stat-color: var(--accent); --stat-bg: var(--accent-light);">
                        <div class="stat-icon">📅</div>
                        <div class="stat-info">
                            <div class="stat-label">Rendez-vous</div>
                            <div class="stat-value">0</div>
                            <div class="stat-trend">À connecter plus tard</div>
                        </div>
                    </div>

                    <div class="stat-card" style="--stat-color: var(--teal); --stat-bg: var(--teal-light);">
                        <div class="stat-icon">📄</div>
                        <div class="stat-info">
                            <div class="stat-label">Demandes</div>
                            <div class="stat-value">0</div>
                            <div class="stat-trend">À connecter plus tard</div>
                        </div>
                    </div>

                    <div class="stat-card" style="--stat-color: var(--warning); --stat-bg: var(--warning-light);">
                        <div class="stat-icon">🕒</div>
                        <div class="stat-info">
                            <div class="stat-label">En attente</div>
                            <div class="stat-value">0</div>
                            <div class="stat-trend">Éléments à traiter</div>
                        </div>
                    </div>

                    <div class="stat-card" style="--stat-color: var(--danger); --stat-bg: var(--danger-light);">
                        <div class="stat-icon">🔔</div>
                        <div class="stat-info">
                            <div class="stat-label">Notifications</div>
                            <div class="stat-value">0</div>
                            <div class="stat-trend">À connecter plus tard</div>
                        </div>
                    </div>
                </div>
            </section>

        </div>
    </div>
</div>

<?php require __DIR__ . '/../../../templates/front/footer.php'; ?>