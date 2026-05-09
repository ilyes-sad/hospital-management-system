<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Statistiques — Réclamations | MediCare</title>
    <link rel="stylesheet" href="/hospital-management-system/public/css/style.css">
    <link rel="stylesheet" href="/hospital-management-system/app/Views/frontoffise/reclamation_form.css">
</head>
<body class="page-body">
<div class="page-wrapper">
    <div class="page-card">
        <div class="page-header">
            <div>
                <h1 class="page-title">Statistiques des réclamations</h1>
                <p class="page-subtitle">Vue globale et répartition par statut et par hôpital.</p>
            </div>
            <a href="/reclamations" class="page-button page-button--secondary">← Liste</a>
        </div>

        <div class="stats-grid">
            <div class="stat-card" style="--stat-color:#0EA5E9;--stat-bg:#E0F2FE">
                <div class="stat-icon">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                </div>
                <div class="stat-info">
                    <div class="stat-label">Total</div>
                    <div class="stat-value"><?= (int)($stats['total'] ?? 0) ?></div>
                </div>
            </div>
            <?php
            $colors = [
                'ouverte'  => ['#0EA5E9', '#E0F2FE'],
                'en_cours' => ['#D97706', '#FEF3C7'],
                'resolue'  => ['#059669', '#D1FAE5'],
                'rejetee'  => ['#DC2626', '#FEE2E2'],
            ];
            foreach ($stats['par_statut'] ?? [] as $key => $count):
                [$c, $bg] = $colors[$key] ?? ['#64748B', '#F1F5F9'];
            ?>
                <div class="stat-card" style="--stat-color:<?= $c ?>;--stat-bg:<?= $bg ?>">
                    <div class="stat-info">
                        <div class="stat-label"><?= htmlspecialchars($statusLabels[$key] ?? $key) ?></div>
                        <div class="stat-value"><?= (int)$count ?></div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="card" style="margin-top:24px">
            <div class="card-header"><div class="card-title">Répartition par hôpital</div></div>
            <?php if (empty($stats['par_hopital'])): ?>
                <p style="color:var(--text-muted);padding:16px">Aucune donnée.</p>
            <?php else: ?>
                <div class="table-wrapper">
                    <table>
                        <thead><tr><th>Hôpital</th><th>Réclamations</th></tr></thead>
                        <tbody>
                            <?php foreach ($stats['par_hopital'] as $nom => $nb): ?>
                                <tr>
                                    <td><?= htmlspecialchars($nom) ?></td>
                                    <td><?= (int)$nb ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
</body>
</html>
