*<?php require __DIR__ . '/../../../templates/front/header.php'; ?>

<div class="profile-page">
    <div class="profile-shell">

        <div class="profile-hero">
            <div class="profile-hero-left">
                <div class="profile-avatar-lg">⚠️</div>

                <div>
                    <h1 class="profile-name">Mes réclamations</h1>
                    <p class="profile-role">
                        Consultez l’état de vos réclamations et les réponses reçues.
                    </p>
                </div>
            </div>

            <div class="profile-hero-actions">

    <a href="index.php?action=profile" class="btn btn-outline">
        ← Retour au profil
    </a>

    <a href="index.php?action=newReclamation" class="btn btn-primary">
        Nouvelle réclamation
    </a>

</div>
        </div>

        <section class="card">
            <div class="card-header">
                <div>
                    <h2 class="card-title">Historique des réclamations</h2>
                    <p class="card-subtitle">Liste des réclamations envoyées</p>
                </div>
            </div>

            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>Objet</th>
                            <th>Catégorie</th>
                            <th>Service</th>
                            <th>Statut</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if (empty($reclamations)): ?>
                            <tr>
                                <td colspan="6">Aucune réclamation envoyée.</td>
                            </tr>
                        <?php endif; ?>

                        <?php foreach ($reclamations as $r): ?>
                            <tr>
                                <td><?= htmlspecialchars($r['objet']) ?></td>
                                <td><?= htmlspecialchars($r['libelleCategorie']) ?></td>
                                <td><?= htmlspecialchars($r['nomService']) ?></td>
                                <td>
                                    <span class="badge badge-reclamation-<?= htmlspecialchars($r['statutReclamation']) ?>">
                                        <?= htmlspecialchars($r['statutReclamation']) ?>
                                    </span>
                                </td>
                                <td><?= htmlspecialchars($r['dateDepot']) ?></td>
                                <td>
                                    <a href="index.php?action=showMyReclamation&id=<?= $r['idReclamation'] ?>" class="btn btn-sm btn-outline">
                                        Voir
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>

    </div>
</div>

<?php require __DIR__ . '/../../../templates/front/footer.php'; ?>