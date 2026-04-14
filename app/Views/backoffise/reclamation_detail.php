<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Réclamation #<?= htmlspecialchars($reclamation->getIdReclamation()) ?> - Administration</title>
    <link rel="stylesheet" href="views/frontoffise/reclamation_form.css">
</head>
<body class="page-body">
<div class="page-wrapper">
    <div class="page-card">
        <div class="page-header">
            <div>
                <h1 class="page-title">Réclamation #<?= htmlspecialchars($reclamation->getIdReclamation()) ?></h1>
                <p class="page-subtitle">Détails de la réclamation.</p>
            </div>
            <a href="reclamation.php?route=admin/reclamations" class="page-button">Retour à la liste</a>
        </div>

        <div class="row gy-4">
            <div class="col-lg-8">
                <div class="detail-card">
                    <h2 class="page-title" style="font-size: 20px; margin-bottom: 18px;">Informations de la réclamation</h2>
                    <dl class="row">
                        <dt class="col-sm-4 text-muted">Objet</dt>
                        <dd class="col-sm-8"><?= htmlspecialchars($reclamation->getObjet()) ?></dd>

                        <dt class="col-sm-4 text-muted">Description</dt>
                        <dd class="col-sm-8"><?= nl2br(htmlspecialchars($reclamation->getDescription())) ?></dd>

                        <dt class="col-sm-4 text-muted">Date de dépôt</dt>
                        <dd class="col-sm-8"><?= htmlspecialchars($reclamation->getDateDepot()->format('d/m/Y H:i')) ?></dd>

                        <dt class="col-sm-4 text-muted">Statut</dt>
                        <dd class="col-sm-8">
                            <span class="badge bg-info text-dark"><?= htmlspecialchars($reclamation->getStatutLabel()) ?></span>
                        </dd>

                        <dt class="col-sm-4 text-muted">Service</dt>
                        <dd class="col-sm-8"><?= htmlspecialchars($reclamation->getServiceHospitalier()?->getNomService() ?? 'Non défini') ?></dd>
                    </dl>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="actions-card">
                    <h2 class="page-title" style="font-size: 20px; margin-bottom: 18px;">Actions</h2>
                    <a href="reclamation.php?route=admin/reclamation/<?= htmlspecialchars($reclamation->getIdReclamation()) ?>/edit" class="page-button" style="display:block; width:100%; margin-bottom:12px; text-align:center;">Modifier</a>
                    <form action="reclamation.php?route=admin/reclamation/<?= htmlspecialchars($reclamation->getIdReclamation()) ?>/delete" method="post" onsubmit="return confirm('Supprimer cette réclamation ?');">
                        <button type="submit" class="submit-button" style="width:100%;">Supprimer</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
