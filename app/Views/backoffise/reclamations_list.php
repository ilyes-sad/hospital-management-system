<?php
if (!isset($successMessage)) {
    $successMessage = isset($_GET['deleted']) ? 'La réclamation a été supprimée.' : (isset($_GET['updated']) ? 'La réclamation a été mise à jour.' : null);
}
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Administration - Réclamations</title>
    <link rel="stylesheet" href="views/frontoffise/reclamation_form.css">
</head>
<body class="page-body">
<div class="page-wrapper">
    <div class="page-card">
        <div class="page-header">
            <div>
                <h1 class="page-title">Gestion des réclamations</h1>
                <p class="page-subtitle">Liste de toutes les réclamations reçues.</p>
            </div>
            <a href="/" class="page-button">Retour au tableau de bord</a>
        </div>

        <?php if ($successMessage): ?>
            <div class="message-box message-success"><?= htmlspecialchars($successMessage) ?></div>
        <?php endif; ?>

        <div class="table-card page-table">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Date</th>
                            <th>Objet</th>
                            <th>Service</th>
                            <th>Statut</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (empty($reclamations)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">Aucune réclamation trouvée.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($reclamations as $reclamation): ?>
                            <?php $service = $reclamation->getServiceHospitalier(); ?>
                            <tr>
                                <td>#<?= htmlspecialchars($reclamation->getIdReclamation()) ?></td>
                                <td><?= htmlspecialchars($reclamation->getDateDepot()->format('d/m/Y H:i')) ?></td>
                                <td><?= htmlspecialchars($reclamation->getObjet()) ?></td>
                                <td><?= htmlspecialchars($service ? $service->getNomService() : '—') ?></td>
                                <td>
                                    <span class="badge bg-<?= $reclamation->getStatutReclamation() === 'resolue' ? 'success' : ($reclamation->getStatutReclamation() === 'en_cours' ? 'warning text-dark' : ($reclamation->getStatutReclamation() === 'rejetee' ? 'danger' : 'secondary')) ?>">
                                        <?= htmlspecialchars($reclamation->getStatutLabel()) ?>
                                    </span>
                                </td>
                                <td class="text-end">
                                    <a href="reclamation.php?route=admin/reclamation/<?= $reclamation->getIdReclamation() ?>/edit" class="action-button action-button--edit me-1">Editer</a>
                                    <a href="reclamation.php?route=admin/reclamation/<?= $reclamation->getIdReclamation() ?>" class="action-button action-button--details me-1">Détails</a>
                                    <form action="reclamation.php?route=admin/reclamation/<?= $reclamation->getIdReclamation() ?>/delete" method="post" class="d-inline" onsubmit="return confirm('Supprimer cette réclamation ?');">
                                        <button type="submit" class="action-button action-button--delete">Supprimer</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</body>
</html>
