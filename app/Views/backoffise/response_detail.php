<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Réponse #<?= (int)$response['id_reponse'] ?> — MediCare</title>
    <link rel="stylesheet" href="/hospital-management-system/public/css/style.css">
    <link rel="stylesheet" href="/hospital-management-system/app/Views/frontoffise/reclamation_form.css">
</head>
<body class="page-body">
<div class="page-wrapper">
    <div class="page-card">
        <div class="page-header">
            <div>
                <h1 class="page-title">Réponse #<?= (int)$response['id_reponse'] ?></h1>
                <p class="page-subtitle">Détail de la réponse</p>
            </div>
            <div class="detail-header-actions">
                <a href="/reponses" class="page-button page-button--secondary">← Liste</a>
                <a href="/reponses/<?= (int)$response['id_reponse'] ?>/edit" class="page-button page-button--secondary">Éditer</a>
                <form method="post" action="/reponses/<?= (int)$response['id_reponse'] ?>/delete" style="display:inline" onsubmit="return confirm('Supprimer cette réponse ?')">
                    <button type="submit" class="page-button page-button--danger">Supprimer</button>
                </form>
            </div>
        </div>

        <div class="detail-grid">
            <div class="detail-card">
                <h2 class="detail-section-title">Réclamation associée</h2>
                <?php
                $recRows = [
                    'ID réclamation' => '#' . (int)$response['id_reclamation'],
                    'Objet'          => $response['objet'],
                    'Description'    => $response['description'],
                    'Statut'         => $statusOptions[$response['statutReclamation']] ?? $response['statutReclamation'],
                    'Date de dépôt'  => (new DateTime($response['dateDepot']))->format('d/m/Y H:i'),
                ];
                foreach ($recRows as $label => $val): ?>
                    <div class="detail-row">
                        <span class="detail-label"><?= $label ?></span>
                        <span class="detail-value"><?= htmlspecialchars($val) ?></span>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="detail-card">
                <h2 class="detail-section-title">Réponse</h2>
                <div class="detail-row">
                    <span class="detail-label">Message</span>
                    <span class="detail-value" style="white-space:pre-wrap"><?= htmlspecialchars($response['message']) ?></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Date</span>
                    <span class="detail-value"><?= htmlspecialchars((new DateTime($response['date_reponse']))->format('d/m/Y H:i')) ?></span>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
