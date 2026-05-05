<?php
if (!isset($successMessage)) {
    $successMessage = isset($_GET['deleted']) ? 'La réclamation a été supprimée.'
        : (isset($_GET['updated']) ? 'La réclamation a été mise à jour.' : null);
}
$hopitalOptions  = $hopitalOptions  ?? [];
$prioriteOptions = $prioriteOptions ?? [];
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Administration — Réclamations | MediCare</title>
    <link rel="stylesheet" href="/hospital-management-system/public/css/style.css">
    <link rel="stylesheet" href="/hospital-management-system/app/Views/frontoffise/reclamation_form.css">
    <style>
        /* ── Pastille de priorité dans la colonne ID ── */
        .prio-dot {
            display: inline-block;
            width: 9px; height: 9px;
            border-radius: 50%;
            vertical-align: middle;
            margin-right: 5px;
            flex-shrink: 0;
        }
        .prio-dot.haute   { background: #EF4444; box-shadow: 0 0 0 0 rgba(239,68,68,.6); animation: pulse-red 1.4s infinite; }
        .prio-dot.moyenne { background: #F59E0B; }
        .prio-dot.basse   { background: #10B981; }

        @keyframes pulse-red {
            0%   { box-shadow: 0 0 0 0   rgba(239,68,68,.6); }
            70%  { box-shadow: 0 0 0 7px rgba(239,68,68,0);  }
            100% { box-shadow: 0 0 0 0   rgba(239,68,68,0);  }
        }

        /* ── Dropdown inline priorité ── */
        .priority-select {
            display: inline-flex; align-items: center; gap: 5px;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: .76rem;
            font-weight: 600;
            cursor: pointer;
            border: none;
            outline: none;
            appearance: none;
            -webkit-appearance: none;
        }
        .priority-select.haute   { background: #FEE2E2; color: #DC2626; }
        .priority-select.moyenne { background: #FEF3C7; color: #D97706; }
        .priority-select.basse   { background: #D1FAE5; color: #059669; }
        .priority-select:focus   { box-shadow: 0 0 0 2px rgba(14,165,233,.3); }
    </style>
</head>
<body class="page-body">
<div class="page-wrapper">
    <div class="page-card">
        <div class="page-header">
            <div>
                <h1 class="page-title">Gestion des réclamations</h1>
                <p class="page-subtitle">Liste, filtres et actions sur les dossiers.</p>
            </div>
            <div class="detail-header-actions">
                <a href="/reclamations/overdue" class="page-button page-button--secondary">⚠️ En retard</a>
                <a href="/reclamations/stats"   class="page-button page-button--secondary">Statistiques</a>
                <a href="/reclamations/new"      class="page-button">+ Nouvelle</a>
            </div>
        </div>

        <?php if ($successMessage): ?>
            <div class="message-box message-success"><?= htmlspecialchars($successMessage) ?></div>
        <?php endif; ?>

        <!-- Filters -->
        <div class="filter-card page-card mb-3">
            <form method="get" action="/reclamations" class="filter-form">
                <div class="filter-row">
                    <label for="status">Statut</label>
                    <select name="status" id="status">
                        <option value="">Tous</option>
                        <?php foreach ($statusOptions as $k => $v): ?>
                            <option value="<?= htmlspecialchars($k) ?>"
                                <?= $currentFilters['status'] === $k ? 'selected' : '' ?>>
                                <?= htmlspecialchars($v) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="filter-row">
                    <label for="priorite">Priorité</label>
                    <select name="priorite" id="priorite">
                        <option value="">Toutes</option>
                        <?php foreach ($prioriteOptions as $k => $v): ?>
                            <option value="<?= htmlspecialchars($k) ?>"
                                <?= ($currentFilters['priorite'] ?? '') === $k ? 'selected' : '' ?>>
                                <?= htmlspecialchars($v) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="filter-row">
                    <label for="service">Service</label>
                    <select name="service" id="service">
                        <option value="">Tous</option>
                        <?php foreach ($services as $s): ?>
                            <option value="<?= $s->getIdService() ?>"
                                <?= (string)$currentFilters['service'] === (string)$s->getIdService() ? 'selected' : '' ?>>
                                <?= htmlspecialchars($s->getNomService()) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="filter-row">
                    <label for="hopital">Hôpital</label>
                    <select name="hopital" id="hopital">
                        <option value="">Tous</option>
                        <option value="__empty__" <?= ($currentFilters['hopital'] ?? '') === '__empty__' ? 'selected' : '' ?>>(Non renseigné)</option>
                        <?php foreach ($hopitalOptions as $h): ?>
                            <option value="<?= htmlspecialchars($h) ?>"
                                <?= ($currentFilters['hopital'] ?? '') === $h ? 'selected' : '' ?>>
                                <?= htmlspecialchars($h) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="filter-row">
                    <label for="date_from">Du</label>
                    <input type="date" id="date_from" name="date_from" value="<?= htmlspecialchars($currentFilters['date_from']) ?>">
                </div>
                <div class="filter-row">
                    <label for="date_to">Au</label>
                    <input type="date" id="date_to" name="date_to" value="<?= htmlspecialchars($currentFilters['date_to']) ?>">
                </div>

                <div class="filter-row filter-search">
                    <label for="search">Recherche</label>
                    <input type="search" id="search" name="search"
                           placeholder="Nom, e-mail, ID, objet…"
                           value="<?= htmlspecialchars($currentFilters['search']) ?>">
                </div>

                <div class="filter-row">
                    <label for="sort_date">Tri date</label>
                    <select name="sort_date" id="sort_date">
                        <option value="desc" <?= $currentFilters['sort_date'] === 'desc' ? 'selected' : '' ?>>Plus récent</option>
                        <option value="asc"  <?= $currentFilters['sort_date'] === 'asc'  ? 'selected' : '' ?>>Plus ancien</option>
                    </select>
                </div>

                <div class="filter-row">
                    <label for="sort_statut">Groupe</label>
                    <select name="sort_statut" id="sort_statut">
                        <option value=""      <?= empty($currentFilters['sort_statut'])                    ? 'selected' : '' ?>>Par date</option>
                        <option value="group" <?= ($currentFilters['sort_statut'] ?? '') === 'group'       ? 'selected' : '' ?>>Par statut</option>
                    </select>
                </div>

                <div class="filter-row">
                    <label for="sort_priorite">Trier par priorité</label>
                    <select name="sort_priorite" id="sort_priorite">
                        <option value=""  <?= empty($currentFilters['sort_priorite']) ? 'selected' : '' ?>>Non</option>
                        <option value="1" <?= ($currentFilters['sort_priorite'] ?? '') === '1' ? 'selected' : '' ?>>Haute → Basse</option>
                    </select>
                </div>

                <div class="filter-actions">
                    <button type="submit" class="page-button">Filtrer</button>
                    <a href="/reclamations" class="page-button page-button--secondary">Réinitialiser</a>
                </div>
            </form>
        </div>

        <!-- Table -->
        <div class="table-card">
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Priorité</th>
                            <th>Date</th>
                            <th>Patient</th>
                            <th>Hôpital</th>
                            <th>Objet</th>
                            <th>Service</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (empty($reclamations)): ?>
                        <tr>
                            <td colspan="9" style="text-align:center;padding:32px;color:var(--text-muted)">
                                Aucune réclamation trouvée.
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($reclamations as $r): ?>
                            <?php
                                $rid      = (int)$r->getIdReclamation();
                                $statut   = $r->getStatutReclamation();
                                $priorite = $r->getPriorite();
                                $badge    = $statut === 'resolue'  ? 'badge-success'
                                          : ($statut === 'rejetee' ? 'badge-danger'
                                          : ($statut === 'en_cours'? 'badge-warning' : 'badge-info'));
                                $pbadge   = PrioriteReclamation::getBadgeClass($priorite);
                                $service  = $r->getServiceHospitalier();
                                $objet    = mb_strlen($r->getObjet()) > 40
                                          ? mb_substr($r->getObjet(), 0, 38) . '…'
                                          : $r->getObjet();
                                // Only show priority on active (non-closed) complaints
                                $isActive = !StatutReclamation::isFinal($statut);
                            ?>
                            <tr>
                                <td>
                                    <?php if ($isActive): ?>
                                        <!-- Pastille animée collée au numéro -->
                                        <span class="prio-dot <?= htmlspecialchars($priorite) ?>"
                                              title="Priorité : <?= htmlspecialchars($r->getPrioriteLabel()) ?>"></span>
                                    <?php endif; ?>
                                    <a href="/reclamations/<?= $rid ?>" style="font-weight:600">#<?= $rid ?></a>
                                </td>
                                <td>
                                    <?php if ($isActive): ?>
                                        <!-- Inline priority changer -->
                                        <form method="post" action="/reclamations/<?= $rid ?>/priority" style="display:inline">
                                            <select name="priorite"
                                                    class="priority-select <?= htmlspecialchars($priorite) ?>"
                                                    onchange="this.form.submit()"
                                                    title="Changer la priorité">
                                                <?php foreach (PrioriteReclamation::getLabels() as $pk => $pv): ?>
                                                    <option value="<?= $pk ?>" <?= $priorite === $pk ? 'selected' : '' ?>>
                                                        <?= htmlspecialchars($pv) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </form>
                                    <?php else: ?>
                                        <span class="badge <?= $pbadge ?>" style="opacity:.6">
                                            <?= htmlspecialchars($r->getPrioriteLabel()) ?>
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($r->getDateDepot()->format('d/m/Y H:i')) ?></td>
                                <td><?= htmlspecialchars($r->getNomPatient() ?: '—') ?></td>
                                <td><?= htmlspecialchars($r->getNomHopital() ?: '—') ?></td>
                                <td title="<?= htmlspecialchars($r->getObjet()) ?>"><?= htmlspecialchars($objet) ?></td>
                                <td><?= htmlspecialchars($service ? $service->getNomService() : '—') ?></td>
                                <td><span class="badge <?= $badge ?>"><?= htmlspecialchars($r->getStatutLabel()) ?></span></td>
                                <td>
                                    <div class="table-actions">
                                        <a href="/reclamations/<?= $rid ?>" class="tbl-btn" title="Voir">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M2 12s4-7 10-7 10 7 10 7-4 7-10 7-10-7-10-7z"/></svg>
                                        </a>
                                        <a href="/reclamations/<?= $rid ?>/edit" class="tbl-btn" title="Modifier">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                        </a>
                                        <?php if ($isActive): ?>
                                        <a href="/reclamations/<?= $rid ?>#repondre" class="tbl-btn" title="Répondre" style="color:var(--success)">
                                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                                        </a>
                                        <?php endif; ?>
                                        <form method="post" action="/reclamations/<?= $rid ?>/delete"
                                              style="display:inline"
                                              onsubmit="return confirm('Supprimer cette réclamation ?')">
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
</div>
</body>
</html>
