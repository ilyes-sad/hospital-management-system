<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Réclamations en retard — MediCare</title>
    <link rel="stylesheet" href="/hospital-management-system/public/css/style.css">
    <link rel="stylesheet" href="/hospital-management-system/app/Views/frontoffise/reclamation_form.css">
</head>
<body class="page-body">
<div class="page-wrapper">
    <div class="page-card">
        <div class="page-header">
            <div>
                <h1 class="page-title">⚠️ Réclamations en retard</h1>
                <p class="page-subtitle">
                    Réclamations « En attente » depuis plus de
                    <strong><?= (int)$thresholdDays ?> jours</strong> sans réponse.
                </p>
            </div>
            <div class="detail-header-actions">
                <a href="/reclamations/stats" class="page-button page-button--secondary">Statistiques</a>
                <a href="/reclamations" class="page-button page-button--secondary">← Liste</a>
                <?php if (!empty($overdue)): ?>
                    <form method="post" action="/reclamations/overdue/send-reminders"
                          onsubmit="return confirm('Envoyer un rappel par e-mail pour toutes les réclamations en retard ?')">
                        <button type="submit" class="page-button" style="background:#D97706;border-color:#D97706">
                            📧 Envoyer les rappels (<?= count($overdue) ?>)
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        </div>

        <?php if (isset($_GET['reminders_sent'])): ?>
            <div class="message-box message-success">
                ✅ <?= (int)$_GET['sent'] ?> rappel(s) envoyé(s),
                <?= (int)$_GET['failed'] ?> échec(s).
            </div>
        <?php endif; ?>

        <!-- Summary cards -->
        <div class="stats-grid" style="margin-bottom:24px">
            <div class="stat-card" style="--stat-color:#D97706;--stat-bg:#FEF3C7">
                <div class="stat-icon">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/>
                        <line x1="12" y1="16" x2="12.01" y2="16"/>
                    </svg>
                </div>
                <div class="stat-info">
                    <div class="stat-label">En retard</div>
                    <div class="stat-value"><?= count($overdue) ?></div>
                    <div class="stat-trend" style="color:var(--warning)">Nécessitent une action</div>
                </div>
            </div>

            <?php foreach ($overdueStats['by_service'] as $service => $count): ?>
                <div class="stat-card" style="--stat-color:#DC2626;--stat-bg:#FEE2E2">
                    <div class="stat-info">
                        <div class="stat-label"><?= htmlspecialchars($service) ?></div>
                        <div class="stat-value"><?= (int)$count ?></div>
                        <div class="stat-trend" style="color:var(--danger)">en retard</div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Overdue table -->
        <?php if (empty($overdue)): ?>
            <div style="text-align:center;padding:64px;color:var(--text-muted)">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="opacity:.3;margin-bottom:16px">
                    <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                    <polyline points="22 4 12 14.01 9 11.01"/>
                </svg>
                <p style="font-size:1.1rem;font-weight:600;margin-bottom:8px">Aucune réclamation en retard</p>
                <p style="font-size:.9rem">Toutes les réclamations sont traitées dans les délais. Excellent travail !</p>
            </div>
        <?php else: ?>
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Patient</th>
                            <th>Hôpital</th>
                            <th>Service</th>
                            <th>Objet</th>
                            <th>Déposée le</th>
                            <th>Retard</th>
                            <th>Dernier rappel</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($overdue as $r): ?>
                            <?php
                                $rid      = (int)$r->getIdReclamation();
                                $days     = (int)(new DateTime())->diff($r->getDateDepot())->days;
                                $urgency  = $days >= 14 ? 'var(--danger)' : 'var(--warning)';
                                $service  = $r->getServiceHospitalier();
                                $lastRem  = $r->getDateLastReminder();
                            ?>
                            <tr>
                                <td>
                                    <a href="/reclamations/<?= $rid ?>" style="font-weight:600;color:var(--accent)">#<?= $rid ?></a>
                                </td>
                                <td>
                                    <div><?= htmlspecialchars($r->getNomPatient() ?: '—') ?></div>
                                    <small style="color:var(--text-muted)"><?= htmlspecialchars($r->getEmailPatient() ?: '') ?></small>
                                </td>
                                <td><?= htmlspecialchars($r->getNomHopital() ?: '—') ?></td>
                                <td>
                                    <span class="badge badge-teal"><?= htmlspecialchars($service ? $service->getNomService() : '—') ?></span>
                                </td>
                                <td title="<?= htmlspecialchars($r->getObjet()) ?>">
                                    <?= htmlspecialchars(mb_strlen($r->getObjet()) > 40 ? mb_substr($r->getObjet(), 0, 38) . '…' : $r->getObjet()) ?>
                                </td>
                                <td><?= htmlspecialchars($r->getDateDepot()->format('d/m/Y')) ?></td>
                                <td>
                                    <span style="font-weight:700;color:<?= $urgency ?>">
                                        <?= $days ?> j
                                    </span>
                                </td>
                                <td style="color:var(--text-muted);font-size:.82rem">
                                    <?= $lastRem ? htmlspecialchars($lastRem->format('d/m/Y H:i')) : '<em>Jamais</em>' ?>
                                </td>
                                <td>
                                    <div class="table-actions">
                                        <a href="/reclamations/<?= $rid ?>" class="tbl-btn" title="Voir le dossier">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M2 12s4-7 10-7 10 7 10 7-4 7-10 7-10-7-10-7z"/></svg>
                                        </a>
                                        <form method="post" action="/reclamations/<?= $rid ?>/send-reminder" style="display:inline"
                                              onsubmit="return confirm('Envoyer un rappel pour la réclamation #<?= $rid ?> ?')">
                                            <button type="submit" class="tbl-btn" title="Envoyer un rappel" style="color:var(--warning)">
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4l16 8-16 8V4z"/></svg>
                                            </button>
                                        </form>
                                        <a href="/reclamations/<?= $rid ?>#repondre" class="tbl-btn" title="Répondre" style="color:var(--success)">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

        <!-- Config info -->
        <div style="margin-top:24px;padding:16px 20px;background:var(--info-light);border-radius:var(--radius-sm);font-size:.83rem;color:#1D4ED8">
            <strong>Configuration actuelle :</strong>
            Seuil de retard = <strong><?= (int)$thresholdDays ?> jours</strong> •
            Intervalle entre rappels = <strong><?= (int)$reminderIntervalDays ?> jours</strong> •
            Rappels automatiques = <strong><?= $remindersEnabled ? '✅ Activés' : '❌ Désactivés' ?></strong>
        </div>
    </div>
</div>
</body>
</html>
