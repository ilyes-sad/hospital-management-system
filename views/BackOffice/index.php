<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require __DIR__ . '/../../templates/back/header.php';
$users = $users ?? [];
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
        'admin' => 'badge badge-info',
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
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                    </svg>
                </div>
                <div class="stat-info">
                    <div class="stat-label">Total utilisateurs</div>
                    <div class="stat-value"><?= $totalUsers ?></div>
                    <div class="stat-trend">Comptes enregistrés</div>
                </div>
            </div>

            <div class="stat-card" style="--stat-color: #16a34a; --stat-bg: #dcfce7;">
                <div class="stat-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M20 6 9 17l-5-5"></path>
                    </svg>
                </div>
                <div class="stat-info">
                    <div class="stat-label">Actifs</div>
                    <div class="stat-value"><?= $totalActifs ?></div>
                    <div class="stat-trend">Comptes validés</div>
                </div>
            </div>

            <div class="stat-card" style="--stat-color: #f59e0b; --stat-bg: #fef3c7;">
                <div class="stat-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M12 9v4"></path>
                        <path d="M12 17h.01"></path>
                        <circle cx="12" cy="12" r="10"></circle>
                    </svg>
                </div>
                <div class="stat-info">
                    <div class="stat-label">En attente</div>
                    <div class="stat-value"><?= $totalAttente ?></div>
                    <div class="stat-trend">Validation requise</div>
                </div>
            </div>

            <div class="stat-card" style="--stat-color: #dc2626; --stat-bg: #fee2e2;">
                <div class="stat-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="m18 6-12 12"></path>
                        <path d="m6 6 12 12"></path>
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

            <form class="filter-bar" id="liveFilterForm" onsubmit="return false;">
                <div class="filter-input">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="11" cy="11" r="7"></circle>
                        <path d="m20 20-3.5-3.5"></path>
                    </svg>
                    <input
                        type="text"
                        name="search"
                        id="liveSearch"
                        placeholder="Nom, prénom, email, téléphone ou adresse..."
                    >
                </div>

                <select class="filter-select" id="roleFilter" name="role">
                    <option value="">Tous les rôles</option>
                    <option value="Patient">Patient</option>
                    <option value="Medecin">Médecin</option>
                    <option value="admin">Administrateur</option>
                    <option value="agent_hospitalier">Agent hospitalier</option>
                </select>

                <select class="filter-select" id="statutFilter" name="statut">
                    <option value="">Tous les statuts</option>
                    <option value="actif">Actif</option>
                    <option value="en_attente">En attente</option>
                    <option value="bloque">Bloqué</option>
                </select>

                <button type="button" id="resetFilters" class="btn btn-outline btn-reset-filter">
                    Réinitialiser
                </button>
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
                    <tbody id="usersTableBody">
                    <?php if (!empty($users)): ?>
                        <?php foreach ($users as $user): ?>
                            <tr
                                data-role="<?= htmlspecialchars(strtolower($user['nomRole'] ?? '')) ?>"
                                data-statut="<?= htmlspecialchars(strtolower($user['statutCompte'] ?? '')) ?>"
                            >
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

        <!-- Voir -->
        <a class="tbl-btn" href="index.php?action=show&id=<?= (int)$user['idUser'] ?>" title="Voir">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                 fill="none" stroke="currentColor" stroke-width="2"
                 viewBox="0 0 24 24">
                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8S1 12 1 12z"></path>
                <circle cx="12" cy="12" r="3"></circle>
            </svg>
        </a>

        <!-- Modifier -->
        <a class="tbl-btn" href="index.php?action=edit&id=<?= (int)$user['idUser'] ?>" title="Modifier">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                 fill="none" stroke="currentColor" stroke-width="2"
                 viewBox="0 0 24 24">
                <path d="M12 20h9"></path>
                <path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"></path>
            </svg>
        </a>

        <!-- Supprimer -->
        <a
            class="tbl-btn delete"
            href="index.php?action=delete&id=<?= (int)$user['idUser'] ?>"
            title="Supprimer"
            onclick="return confirm('Voulez-vous vraiment supprimer cet utilisateur ?');"
        >
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                 fill="none" stroke="currentColor" stroke-width="2"
                 viewBox="0 0 24 24">
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
                        <tr id="noDataRow">
                            <td colspan="7">
                                <div class="empty-state">
                                    <p>Aucun utilisateur trouvé.</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>

                    <tr id="noResultRow" style="display: none;">
                        <td colspan="7">
                            <div class="empty-state">
                                <p>Aucun utilisateur ne correspond à la recherche.</p>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const searchInput = document.getElementById('liveSearch');
    const roleFilter = document.getElementById('roleFilter');
    const statutFilter = document.getElementById('statutFilter');
    const resetBtn = document.getElementById('resetFilters');
    const rows = document.querySelectorAll('#usersTableBody tr[data-role]');
    const noResultRow = document.getElementById('noResultRow');

    function normalize(value) {
        return (value || '').toLowerCase().trim();
    }

    function filterTable() {
        const searchValue = normalize(searchInput.value);
        const roleValue = normalize(roleFilter.value);
        const statutValue = normalize(statutFilter.value);

        let visibleCount = 0;

        rows.forEach(row => {
            const rowText = normalize(row.textContent);
            const rowRole = normalize(row.dataset.role);
            const rowStatut = normalize(row.dataset.statut);

            const matchSearch = searchValue === '' || rowText.includes(searchValue);
            const matchRole = roleValue === '' || rowRole === normalize(roleValue);
            const matchStatut = statutValue === '' || rowStatut === normalize(statutValue);

            if (matchSearch && matchRole && matchStatut) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        if (noResultRow) {
            noResultRow.style.display = visibleCount === 0 ? '' : 'none';
        }
    }

    searchInput.addEventListener('input', filterTable);
    roleFilter.addEventListener('change', filterTable);
    statutFilter.addEventListener('change', filterTable);

    resetBtn.addEventListener('click', function () {
        searchInput.value = '';
        roleFilter.value = '';
        statutFilter.value = '';
        filterTable();
    });
});
</script>

<?php require __DIR__ . '/../../templates/back/footer.php'; ?>