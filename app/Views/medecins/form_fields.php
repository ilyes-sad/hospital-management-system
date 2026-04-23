<?php
// ============================================================
//  app/Views/medecins/form_fields.php
// ============================================================

$ALL_H = ['08:00','09:00','10:00','11:00','12:00','13:00','14:00','15:00','16:00','17:00','18:00'];

function medOld(string $field, array $old, $medecin = null): string {
    if (!empty($old[$field])) return htmlspecialchars($old[$field]);
    if ($medecin && isset($medecin[$field]) && $medecin[$field] !== null) return htmlspecialchars($medecin[$field]);
    return '';
}

function medError(string $field, array $errors): string {
    return $errors[$field] ?? '';
}

function medHasHoraire(string $heure, array $old, $medecin = null): bool {
    if (!empty($old['horaires']) && is_array($old['horaires']) && in_array($heure, $old['horaires'])) return true;
    if ($medecin && isset($medecin['horaires']) && is_array($medecin['horaires']) && in_array($heure, $medecin['horaires'])) return true;
    return false;
}
?>

<div class="form-row">
    <div class="form-group">
        <label class="form-label">Prenom *</label>
        <input type="text" class="form-control <?= !empty($errors['prenom']) ? 'input-error' : '' ?>"
               name="prenom" value="<?= medOld('prenom', $old ?? [], $medecin ?? null) ?>"/>
        <?php if (medError('prenom', $errors ?? [])): ?>
            <span class="field-error"><?= htmlspecialchars(medError('prenom', $errors)) ?></span>
        <?php endif; ?>
    </div>
    <div class="form-group">
        <label class="form-label">Nom *</label>
        <input type="text" class="form-control <?= !empty($errors['nom']) ? 'input-error' : '' ?>"
               name="nom" value="<?= medOld('nom', $old ?? [], $medecin ?? null) ?>"/>
        <?php if (medError('nom', $errors ?? [])): ?>
            <span class="field-error"><?= htmlspecialchars(medError('nom', $errors)) ?></span>
        <?php endif; ?>
    </div>
</div>

<div class="form-row">
    <div class="form-group">
        <label class="form-label">Specialite *</label>
        <input type="text" class="form-control <?= !empty($errors['specialite']) ? 'input-error' : '' ?>"
               name="specialite" value="<?= medOld('specialite', $old ?? [], $medecin ?? null) ?>"/>
        <?php if (medError('specialite', $errors ?? [])): ?>
            <span class="field-error"><?= htmlspecialchars(medError('specialite', $errors)) ?></span>
        <?php endif; ?>
    </div>
    <div class="form-group">
        <label class="form-label">Hopital *</label>
        <select class="form-control <?= !empty($errors['hopital_id']) ? 'input-error' : '' ?>" name="hopital_id">
            <option value="">— Selectionner —</option>
            <?php foreach ($hopitaux as $h): ?>
                <?php $currentId = !empty($old['hopital_id']) ? $old['hopital_id'] : ($medecin['hopital_id'] ?? ''); ?>
                <option value="<?= $h['id'] ?>" <?= $currentId == $h['id'] ? 'selected' : '' ?>><?= htmlspecialchars($h['nom']) ?></option>
            <?php endforeach; ?>
        </select>
        <?php if (medError('hopital_id', $errors ?? [])): ?>
            <span class="field-error"><?= htmlspecialchars(medError('hopital_id', $errors)) ?></span>
        <?php endif; ?>
    </div>
</div>

<div class="form-row">
    <div class="form-group">
        <label class="form-label">Telephone</label>
        <input type="text" class="form-control <?= !empty($errors['telephone']) ? 'input-error' : '' ?>"
               name="telephone" value="<?= medOld('telephone', $old ?? [], $medecin ?? null) ?>"/>
        <?php if (medError('telephone', $errors ?? [])): ?>
            <span class="field-error"><?= htmlspecialchars(medError('telephone', $errors)) ?></span>
        <?php endif; ?>
    </div>
    <div class="form-group">
        <label class="form-label">Email</label>
        <input type="text" class="form-control <?= !empty($errors['email']) ? 'input-error' : '' ?>"
               name="email" value="<?= medOld('email', $old ?? [], $medecin ?? null) ?>"/>
        <?php if (medError('email', $errors ?? [])): ?>
            <span class="field-error"><?= htmlspecialchars(medError('email', $errors)) ?></span>
        <?php endif; ?>
    </div>
</div>

<div class="form-group">
    <label class="form-label">Creneaux horaires</label>
    <div style="display:flex;flex-wrap:wrap;gap:8px;margin-top:4px">
        <?php foreach ($ALL_H as $h): ?>
            <label style="display:inline-flex;align-items:center;gap:5px;cursor:pointer;font-size:0.85rem;background:var(--bg);border:1px solid var(--border);padding:5px 11px;border-radius:7px">
                <input type="checkbox" name="horaires[]" value="<?= $h ?>" <?= medHasHoraire($h, $old ?? [], $medecin ?? null) ? 'checked' : '' ?>/>
                <?= $h ?>
            </label>
        <?php endforeach; ?>
    </div>
</div>
