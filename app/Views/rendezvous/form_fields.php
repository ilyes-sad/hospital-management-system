<?php
// ============================================================
//  app/Views/rendezvous/form_fields.php
// ============================================================

function rdvOld(string $field, array $old, $rdv = null): string {
    if (!empty($old[$field])) return htmlspecialchars($old[$field]);
    if ($rdv && isset($rdv[$field]) && $rdv[$field] !== null) return htmlspecialchars($rdv[$field]);
    return '';
}

function rdvError(string $field, array $errors): string {
    return $errors[$field] ?? '';
}
?>

<div class="form-group">
    <label class="form-label">Patient *</label>
    <select class="form-control <?= !empty($errors['patient_id']) ? 'input-error' : '' ?>" name="patient_id">
        <option value="">— Selectionner —</option>
        <?php foreach ($patients as $p): ?>
            <?php $currentId = !empty($old['patient_id']) ? $old['patient_id'] : ($rdv['patient_id'] ?? ''); ?>
            <option value="<?= $p['id'] ?>" <?= $currentId == $p['id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($p['prenom'] . ' ' . $p['nom']) ?> — <?= htmlspecialchars($p['cin'] ?? '') ?>
            </option>
        <?php endforeach; ?>
    </select>
    <?php if (rdvError('patient_id', $errors)): ?>
        <span class="field-error"><?= htmlspecialchars(rdvError('patient_id', $errors)) ?></span>
    <?php endif; ?>
</div>

<div class="form-group">
    <label class="form-label">Medecin *</label>
    <select class="form-control <?= !empty($errors['medecin_id']) ? 'input-error' : '' ?>" name="medecin_id">
        <option value="">— Selectionner —</option>
        <?php foreach ($medecins as $m): ?>
            <?php $currentId = !empty($old['medecin_id']) ? $old['medecin_id'] : ($rdv['medecin_id'] ?? ''); ?>
            <option value="<?= $m['id'] ?>" <?= $currentId == $m['id'] ? 'selected' : '' ?>>
                Dr. <?= htmlspecialchars($m['prenom'] . ' ' . $m['nom']) ?> — <?= htmlspecialchars($m['specialite']) ?>
            </option>
        <?php endforeach; ?>
    </select>
    <?php if (rdvError('medecin_id', $errors)): ?>
        <span class="field-error"><?= htmlspecialchars(rdvError('medecin_id', $errors)) ?></span>
    <?php endif; ?>
</div>

<div class="form-row">
    <div class="form-group">
        <label class="form-label">Date *</label>
        <input type="text" class="form-control <?= !empty($errors['date_rdv']) ? 'input-error' : '' ?>"
               name="date_rdv" placeholder="AAAA-MM-JJ" value="<?= rdvOld('date_rdv', $old ?? [], $rdv ?? null) ?>"/>
        <?php if (rdvError('date_rdv', $errors ?? [])): ?>
            <span class="field-error"><?= htmlspecialchars(rdvError('date_rdv', $errors)) ?></span>
        <?php endif; ?>
    </div>
    <div class="form-group">
        <label class="form-label">Heure *</label>
        <input type="text" class="form-control <?= !empty($errors['heure']) ? 'input-error' : '' ?>"
               name="heure" placeholder="HH:MM" value="<?= rdvOld('heure', $old ?? [], $rdv ?? null) ?>"/>
        <?php if (rdvError('heure', $errors ?? [])): ?>
            <span class="field-error"><?= htmlspecialchars(rdvError('heure', $errors)) ?></span>
        <?php endif; ?>
    </div>
</div>

<div class="form-group">
    <label class="form-label">Motif</label>
    <input type="text" class="form-control" name="motif" value="<?= rdvOld('motif', $old ?? [], $rdv ?? null) ?>" placeholder="Ex: Douleur thoracique..."/>
</div>

<?php if (isset($rdv) && !empty($rdv)): ?>
<div class="form-group">
    <label class="form-label">Statut</label>
    <select class="form-control" name="statut">
        <?php $currentStatut = !empty($old['statut']) ? $old['statut'] : ($rdv['statut'] ?? 'en attente'); ?>
        <?php foreach (['en attente', 'confirme', 'annule', 'termine'] as $s): ?>
            <option value="<?= $s ?>" <?= $currentStatut === $s ? 'selected' : '' ?>><?= $s ?></option>
        <?php endforeach; ?>
    </select>
</div>
<?php endif; ?>
