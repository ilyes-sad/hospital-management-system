<?php
// ============================================================
//  app/Views/rendezvous/index.php
// ============================================================
$baseUrl = '/medapp2/public';
?>

<div class="section-header">
    <div>
        <div class="section-title">Gestion des rendez-vous</div>
        <div class="section-subtitle">Jointure N-N : Patient ↔ rendez_vous ↔ Medecin</div>
    </div>
    <button class="btn btn-primary" onclick="RendezVousModule.openNew()">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14M5 12h14"/></svg>
        Nouveau rendez-vous
    </button>
</div>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <?php foreach ($errors as $error): ?><p><?= htmlspecialchars($error) ?></p><?php endforeach; ?>
    </div>
<?php endif; ?>

<div class="card" style="padding:0">
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>#</th><th>Patient</th><th>Medecin</th><th>Specialite</th>
                    <th>Hopital</th><th>Date</th><th>Heure</th><th>Statut</th><th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($rdvs)): ?>
                    <tr><td colspan="9"><div class="empty-state"><p>Aucun rendez-vous</p></div></td></tr>
                <?php else: ?>
                    <?php foreach ($rdvs as $i => $r): ?>
                        <tr>
                            <td><span style="color:var(--text-muted);font-size:0.8rem">#<?= str_pad($i + 1, 3, '0', STR_PAD_LEFT) ?></span></td>
                            <td>
                                <div class="avatar-cell">
                                    <div class="avatar avatar-blue"><?= htmlspecialchars(mb_substr($r['patient_nom'] ?? '?', 0, 1)) ?></div>
                                    <span class="avatar-name"><?= htmlspecialchars($r['patient_nom'] ?? '—') ?></span>
                                </div>
                            </td>
                            <td>
                                <div class="avatar-cell">
                                    <div class="avatar avatar-teal"><?= htmlspecialchars(mb_substr($r['medecin_nom'] ?? 'Dr?', 4, 1)) ?></div>
                                    <span><?= htmlspecialchars($r['medecin_nom'] ?? '—') ?></span>
                                </div>
                            </td>
                            <td><span class="badge badge-teal"><?= htmlspecialchars($r['specialite'] ?? '—') ?></span></td>
                            <td style="font-size:0.85rem"><?= htmlspecialchars($r['hopital_nom'] ?? '—') ?></td>
                            <td><?= htmlspecialchars(formatDate($r['date_rdv'])) ?></td>
                            <td><span class="info-pill"><?= htmlspecialchars($r['heure']) ?></span></td>
                            <td><?= statusBadge($r['statut']) ?></td>
                            <td>
                                <div class="table-actions">
                                    <a href="<?= $baseUrl ?>/rendezvous/edit/<?= $r['id'] ?>" class="tbl-btn" title="Modifier">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4z"/></svg>
                                    </a>
                                    <form method="POST" action="<?= $baseUrl ?>/rendezvous/delete/<?= $r['id'] ?>" style="display:inline" onsubmit="return confirm('Supprimer ce rendez-vous ?')">
                                        <button type="submit" class="tbl-btn delete" title="Supprimer">
                                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6M10 11v6M14 11v6M9 6V4h6v2"/></svg>
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
