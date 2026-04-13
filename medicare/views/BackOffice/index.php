
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require __DIR__ . '/../../templates/back/header.php';
/**
 * $users doit venir du UserController
 * Exemple :
 * $users = $this->userModel->getAll();
 */

$totalUsers = is_array($users) ? count($users) : 0;
$totalActifs = 0;
$totalBloques = 0;
$totalAttente = 0;

if (!empty($users)) {
    foreach ($users as $u) {
        if (($u['statutCompte'] ?? '') === 'actif') {
            $totalActifs++;
        } elseif (($u['statutCompte'] ?? '') === 'bloque') {
            $totalBloques++;
        } elseif (($u['statutCompte'] ?? '') === 'en_attente') {
            $totalAttente++;
        }
    }
}

function badgeClass(string $statut): string
{
    return match ($statut) {
        'actif' => 'badge badge-success',
        'bloque' => 'badge badge-danger',
        'en_attente' => 'badge badge-warning',
        default => 'badge badge-neutral',
    };
}

function roleBadgeClass(string $role): string
{
    return match ($role) {
        'Administrateur' => 'badge badge-info',
        'Patient' => 'badge badge-success',
        'Medecin' => 'badge badge-teal',
        'AgentHospitalier' => 'badge badge-warning',
        default => 'badge badge-neutral',
    };
}

function initials(string $nom, string $prenom): string
{
    $first = strtoupper(substr(trim($nom), 0, 1));
    $second = strtoupper(substr(trim($prenom), 0, 1));
    return $first . $second;
}
?>

<div class="main-content">
    <header class="topbar">
        <div>
            <h1 class="page-title">Gestion des utilisateurs</h1>
            <p class="page-sub">BackOffice / Module User</p>
        </div>

        <div class="topbar-right">
            <form class="search-bar" method="GET" action="index.php">
                <input type="hidden" name="action" value="index">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <circle cx="11" cy="11" r="7"></circle>
                    <path d="m20 20-3.5-3.5"></path>
                </svg>
                <input
                    type="text"
                    name="search"
                    placeholder="Rechercher un utilisateur..."
                    value="<?= htmlspecialchars($_GET['search'] ?? '') ?>"
                >
            </form>

            <a href="index.php?action=create" class="btn btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M12 5v14"></path>
                    <path d="M5 12h14"></path>
                </svg>
                Ajouter
            </a>
        </div>
    </header>

    <div class="content-area">

        <?php if (!empty($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <p><?= htmlspecialchars($_SESSION['success']) ?></p>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <?php if (!empty($_SESSION['error'])): ?>
            <div class="alert alert-danger">
                <p><?= htmlspecialchars($_SESSION['error']) ?></p>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <div class="stats-grid">
            <div class="stat-card" style="--stat-color: var(--accent); --stat-bg: var(--accent-light);">
                <div class="stat-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                    </svg>
                </div>
                <div class="stat-info">
                    <div class="stat-label">Total utilisateurs</div>
                    <div class="stat-value"><?= $totalUsers ?></div>
                    <div class="stat-trend">Comptes enregistrés</div>
                </div>
            </div>

            <div class="stat-card" style="--stat-color: var(--success); --stat-bg: var(--success-light);">
                <div class="stat-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M20 6 9 17l-5-5"></path>
                    </svg>
                </div>
                <div class="stat-info">
                    <div class="stat-label">Actifs</div>
                    <div class="stat-value"><?= $totalActifs ?></div>
                    <div class="stat-trend">Comptes opérationnels</div>
                </div>
            </div>

            <div class="stat-card" style="--stat-color: var(--warning); --stat-bg: var(--warning-light);">
                <div class="stat-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10"></circle>
                        <path d="M12 6v6"></path>
                        <path d="M12 16h.01"></path>
                    </svg>
                </div>
                <div class="stat-info">
                    <div class="stat-label">En attente</div>
                    <div class="stat-value"><?= $totalAttente ?></div>
                    <div class="stat-trend">Comptes à vérifier</div>
                </div>
            </div>

            <div class="stat-card" style="--stat-color: var(--danger); --stat-bg: var(--danger-light);">
                <div class="stat-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10"></circle>
                        <path d="m15 9-6 6"></path>
                        <path d="m9 9 6 6"></path>
                    </svg>
                </div>
                <div class="stat-info">
                    <div class="stat-label">Bloqués</div>
                    <div class="stat-value"><?= $totalBloques ?></div>
                    <div class="stat-trend">Accès suspendus</div>
                </div>
            </div>
        </div>

        <section class="card">
            <div class="card-header">
                <div>
                    <h2 class="card-title">Liste des utilisateurs</h2>
                    <p class="card-subtitle">Consulter, modifier et supprimer les comptes</p>
                </div>

                <a href="index.php?action=create" class="btn btn-outline">
                    Nouveau compte
                </a>
            </div>

            <form class="filter-bar" method="GET" action="index.php">
                <input type="hidden" name="action" value="index">

                <div class="filter-input">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="11" cy="11" r="7"></circle>
                        <path d="m20 20-3.5-3.5"></path>
                    </svg>
                    <input
                        type="text"
                        name="search"
                        placeholder="Nom, prénom ou email..."
                        value="<?= htmlspecialchars($_GET['search'] ?? '') ?>"
                    >
                </div>

                <select class="filter-select" name="role">
                    <option value="">Tous les rôles</option>
                    <option value="Patient" <?= (($_GET['role'] ?? '') === 'Patient') ? 'selected' : '' ?>>Patient</option>
                    <option value="Medecin" <?= (($_GET['role'] ?? '') === 'Medecin') ? 'selected' : '' ?>>Médecin</option>
                    <option value="Administrateur" <?= (($_GET['role'] ?? '') === 'Administrateur') ? 'selected' : '' ?>>Administrateur</option>
                    <option value="AgentHospitalier" <?= (($_GET['role'] ?? '') === 'AgentHospitalier') ? 'selected' : '' ?>>Agent hospitalier</option>
                </select>

                <select class="filter-select" name="statut">
                    <option value="">Tous les statuts</option>
                    <option value="actif" <?= (($_GET['statut'] ?? '') === 'actif') ? 'selected' : '' ?>>Actif</option>
                    <option value="en_attente" <?= (($_GET['statut'] ?? '') === 'en_attente') ? 'selected' : '' ?>>En attente</option>
                    <option value="bloque" <?= (($_GET['statut'] ?? '') === 'bloque') ? 'selected' : '' ?>>Bloqué</option>
                </select>

                <button type="submit" class="btn btn-primary">Filtrer</button>
                <a href="index.php?action=index" class="btn btn-outline">Réinitialiser</a>
            </form>

            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>Utilisateur</th>
                            <th>Email</th>
                            <th>Téléphone</th>
                            <th>Rôle</th>
                            <th>Statut</th>
                            <th>Adresse</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (!empty($users)): ?>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td>
                                    <div class="avatar-cell">
                                        <div class="avatar avatar-blue">
                                            <?= htmlspecialchars(initials($user['nom'] ?? '', $user['prenom'] ?? '')) ?>
                                        </div>
                                        <div>
                                            <div class="avatar-name">
                                                <?= htmlspecialchars(($user['nom'] ?? '') . ' ' . ($user['prenom'] ?? '')) ?>
                                            </div>
                                            <small class="page-sub">
                                                ID #<?= (int)($user['idUser'] ?? 0) ?>
                                            </small>
                                        </div>
                                    </div>
                                </td>

                                <td><?= htmlspecialchars($user['email'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($user['telephone'] ?? '-') ?></td>

                                <td>
                                    <span class="<?= roleBadgeClass($user['nomRole'] ?? '') ?>">
                                        <?= htmlspecialchars($user['nomRole'] ?? 'Non défini') ?>
                                    </span>
                                </td>

                                <td>
                                    <span class="<?= badgeClass($user['statutCompte'] ?? '') ?>">
                                        <?= htmlspecialchars($user['statutCompte'] ?? 'inconnu') ?>
                                    </span>
                                </td>

                                <td><?= htmlspecialchars($user['adresse'] ?? '-') ?></td>

                                <td>
                                    <div class="table-actions">
                                        <a class="tbl-btn" href="index.php?action=show&id=<?= (int)$user['idUser'] ?>" title="Voir">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0"></path>
                                                <circle cx="12" cy="12" r="3"></circle>
                                            </svg>
                                        </a>

                                        <a class="tbl-btn" href="index.php?action=edit&id=<?= (int)$user['idUser'] ?>" title="Modifier">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path d="M12 20h9"></path>
                                                <path d="M16.5 3.5a2.121 2.121 0 1 1 3 3L7 19l-4 1 1-4Z"></path>
                                            </svg>
                                        </a>

                                        <a
                                            class="tbl-btn delete"
                                            href="index.php?action=delete&id=<?= (int)$user['idUser'] ?>"
                                            title="Supprimer"
                                            onclick="return confirm('Voulez-vous vraiment supprimer cet utilisateur ?');"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path d="M3 6h18"></path>
                                                <path d="M8 6V4h8v2"></path>
                                                <path d="M19 6l-1 14H6L5 6"></path>
                                                <path d="M10 11v6"></path>
                                                <path d="M14 11v6"></path>
                                            </svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7">
                                <div class="empty-state">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="42" height="42" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                                        <circle cx="9" cy="7" r="4"></circle>
                                        <path d="M22 21v-2a4 4 0 0 0-3-3.87"></path>
                                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                                    </svg>
                                    <p>Aucun utilisateur trouvé.</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</div>


