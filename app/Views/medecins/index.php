<?php
// ============================================================
//  app/Views/medecins/index.php
// ============================================================
$baseUrl = '/medapp2/public';
?>

<div class="section-header">
    <div>
        <div class="section-title">Corps medical</div>
        <div class="section-subtitle"><?= count($medecins) ?> medecin(s) · <?= count($specs) ?> specialites</div>
    </div>
    <button class="btn btn-primary" onclick="MedecinsModule.openNew()">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14M5 12h14"/></svg>
        Ajouter medecin
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
                    <th>Medecin</th><th>Specialite</th><th>Hopital</th>
                    <th>Telephone</th><th>Email</th><th>Creneaux</th><th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($medecins)): ?>
                    <tr><td colspan="7"><div class="empty-state"><p>Aucun medecin</p></div></td></tr>
                <?php else: ?>
                    <?php foreach ($medecins as $m): ?>
                        <tr>
                            <td>
                                <div class="avatar-cell">
                                    <div class="avatar avatar-teal"><?= htmlspecialchars(initials($m['prenom'], $m['nom'])) ?></div>
                                    <span class="avatar-name">Dr. <?= htmlspecialchars($m['prenom'] . ' ' . $m['nom']) ?></span>
                                </div>
                            </td>
                            <td><span class="badge badge-teal"><?= htmlspecialchars($m['specialite']) ?></span></td>
                            <td><?= htmlspecialchars($m['hopital_nom'] ?? '—') ?></td>
                            <td style="font-size:0.83rem"><?= htmlspecialchars($m['telephone'] ?? '—') ?></td>
                            <td style="font-size:0.82rem;color:var(--text-secondary)"><?= htmlspecialchars($m['email'] ?? '—') ?></td>
                            <td><span class="badge badge-neutral"><?= count($m['horaires'] ?? []) ?></span></td>
                            <td>
                                <div class="table-actions">
                                    <a href="<?= $baseUrl ?>/medecins/edit/<?= $m['id'] ?>" class="tbl-btn" title="Modifier">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4z"/></svg>
                                    </a>
                                    <form method="POST" action="<?= $baseUrl ?>/medecins/delete/<?= $m['id'] ?>" style="display:inline" onsubmit="return confirm('Supprimer ce medecin ?')">
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
