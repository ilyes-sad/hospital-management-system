<?php
if (!isset($successMessage)) {
    $successMessage = isset($_GET['deleted']) ? 'La réponse a été supprimée.'
        : (isset($_GET['updated']) ? 'La réponse a été mise à jour.' : null);
}
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gestion des réponses — MediCare</title>
    <link rel="stylesheet" href="/hospital-management-system/public/css/style.css">
    <link rel="stylesheet" href="/hospital-management-system/app/Views/frontoffise/reclamation_form.css">
</head>
<body class="page-body">
<div class="page-wrapper">
    <div class="page-card">
        <div class="page-header">
            <div>
                <h1 class="page-title">Gestion des réponses</h1>
                <p class="page-subtitle">Toutes les réponses enregistrées.</p>
            </div>
            <a href="/reclamations" class="page-button page-button--secondary">← Réclamations</a>
        </div>

        <?php if ($successMessage): ?>
            <div class="message-box message-success"><?= htmlspecialchars($successMessage) ?></div>
        <?php endif; ?>

        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Réclamation</th>
                        <th>Statut</th>
                        <th>Message</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($responses)): ?>
                        <tr><td colspan="6" style="text-align:center;padding:32px;color:var(--text-muted)">Aucune réponse enregistrée.</td></tr>
                    <?php else: ?>
                        <?php foreach ($responses as $r): ?>
                            <tr>
                                <td>#<?= (int)$r['id_reponse'] ?></td>
                                <td>
                                    <strong><a href="/reclamations/<?= (int)$r['id_reclamation'] ?>">#<?= (int)$r['id_reclamation'] ?></a></strong><br>
                                    <small><?= htmlspecialchars(mb_substr($r['objet'], 0, 50)) ?></small>
                                </td>
                                <td><?= htmlspecialchars($statusOptions[$r['statutReclamation']] ?? $r['statutReclamation']) ?></td>
                                <td><small><?= htmlspecialchars(mb_substr($r['message'], 0, 60)) ?>…</small></td>
                                <td><?= htmlspecialchars((new DateTime($r['date_reponse']))->format('d/m/Y H:i')) ?></td>
                                <td>
                                    <div class="table-actions">
                                        <a href="/reponses/<?= (int)$r['id_reponse'] ?>" class="tbl-btn" title="Voir">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M2 12s4-7 10-7 10 7 10 7-4 7-10 7-10-7-10-7z"/></svg>
                                        </a>
                                        <a href="/reponses/<?= (int)$r['id_reponse'] ?>/edit" class="tbl-btn" title="Éditer">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                        </a>
                                        <form method="post" action="/reponses/<?= (int)$r['id_reponse'] ?>/delete" style="display:inline" onsubmit="return confirm('Supprimer cette réponse ?')">
                                            <button type="submit" class="tbl-btn delete" title="Supprimer">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6M14 11v6"/><path d="M9 6V4h6v2"/></svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>
