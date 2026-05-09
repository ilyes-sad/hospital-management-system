<?php
require __DIR__ . '/../../templates/back/header.php';
?>

<div class="content-area">
    <div class="section-header">
        <div>
            <h2 class="section-title">Utilisateurs actifs</h2>
            <p class="section-subtitle">
                Utilisateurs connectés ou actifs durant les 5 dernières minutes.
            </p>
        </div>

        <span class="info-pill">
            <?= !empty($users) ? count($users) : 0 ?> en ligne
        </span>
    </div>

    <div class="card">
        <div class="card-header">
            <div>
                <h3 class="card-title">Liste des utilisateurs actifs</h3>
                <p class="card-subtitle">Actualisation automatique toutes les 5 secondes</p>
            </div>
        </div>

        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Utilisateur</th>
                        <th>Email</th>
                        <th>Dernière activité</th>
                        <th>Statut</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if (!empty($users)): ?>
                        <?php foreach ($users as $u): ?>
                            <?php
                                $nom = $u['nom'] ?? '';
                                $prenom = $u['prenom'] ?? '';
                                $initials = strtoupper(substr($nom, 0, 1) . substr($prenom, 0, 1));
                            ?>
                            <tr>
                                <td><?= htmlspecialchars($u['idUser']) ?></td>

                                <td>
                                    <div class="avatar-cell">
                                        <div class="avatar avatar-blue">
                                            <?= htmlspecialchars($initials) ?>
                                        </div>
                                        <div>
                                            <div class="avatar-name">
                                                <?= htmlspecialchars($nom . ' ' . $prenom) ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <td><?= htmlspecialchars($u['email']) ?></td>

                                <td><?= htmlspecialchars($u['last_activity']) ?></td>

                                <td>
                                    <span class="badge badge-success">En ligne</span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5">
                                <div class="empty-state">
                                    <p>Aucun utilisateur actif actuellement.</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
setTimeout(() => {
    window.location.reload();
}, 5000);
</script>

<?php
require __DIR__ . '/../../templates/back/footer.php';
?>