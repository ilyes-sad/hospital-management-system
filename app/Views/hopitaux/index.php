<?php
// ============================================================
//  app/Views/hopitaux/index.php
// ============================================================
$baseUrl = '/medapp2/public';
?>

<div class="section-header">
    <div>
        <div class="section-title">Hopitaux</div>
        <div class="section-subtitle"><?= count($hopitaux) ?> etablissement(s)</div>
    </div>
    <button class="btn btn-primary" onclick="HopitauxModule.openNew()">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14M5 12h14"/></svg>
        Ajouter
    </button>
</div>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <?php foreach ($errors as $error): ?>
            <p><?= htmlspecialchars($error) ?></p>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<div class="card" style="padding:0">
    <div class="table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Region</th>
                    <th>Ville</th>
                    <th>Type</th>
                    <th>Lits</th>
                    <th>Telephone</th>
                    <th>Medecins</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($hopitaux)): ?>
                    <tr><td colspan="8"><div class="empty-state"><p>Aucun hopital</p></div></td></tr>
                <?php else: ?>
                    <?php
                    $typeClass = ['CHU' => 'badge-info', 'Public' => 'badge-success', 'Prive' => 'badge-warning'];
                    foreach ($hopitaux as $h): ?>
                        <tr>
                            <td style="font-weight:600"><?= htmlspecialchars($h['nom']) ?></td>
                            <td style="font-size:0.83rem;color:var(--text-secondary)"><?= htmlspecialchars($h['region']) ?></td>
                            <td><?= htmlspecialchars($h['ville']) ?></td>
                            <td><span class="badge <?= $typeClass[$h['type']] ?? 'badge-neutral' ?>"><?= htmlspecialchars($h['type']) ?></span></td>
                            <td><?= htmlspecialchars($h['lits'] ?? '—') ?></td>
                            <td style="font-size:0.83rem"><?= htmlspecialchars($h['telephone'] ?? '—') ?></td>
                            <td><span class="badge badge-teal"><?= (int) ($h['nb_medecins'] ?? 0) ?></span></td>
                            <td>
                                <div class="table-actions">
                                    <a href="<?= $baseUrl ?>/hopitaux/edit/<?= $h['id'] ?>" class="tbl-btn" title="Modifier">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4z"/></svg>
                                    </a>
                                    <form method="POST" action="<?= $baseUrl ?>/hopitaux/delete/<?= $h['id'] ?>" style="display:inline" onsubmit="return confirm('Supprimer cet hopital et toutes ses donnees associees ?')">
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
