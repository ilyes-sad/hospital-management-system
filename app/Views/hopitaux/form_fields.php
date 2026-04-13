<?php
// ============================================================
//  app/Views/hopitaux/form_fields.php
//  Shared form fields for create/edit (included by both)
// ============================================================

$regions = [
    'Rabat-Sale-Kenitra','Casablanca-Settat','Marrakech-Safi',
    'Fes-Meknes','Oriental','Souss-Massa','Tanger-Tetouan-Al Hoceima',
    'Beni Mellal-Khenifra','Draa-Tafilalet','Guelmim-Oued Noun',
    'Laayoune-Sakia El Hamra','Dakhla-Oued Ed Dahab'
];

function hopOld(string $field, array $old, $hopital = null): string {
    if (!empty($old[$field])) return htmlspecialchars($old[$field]);
    if ($hopital && !empty($hopital[$field])) return htmlspecialchars($hopital[$field]);
    return '';
}

function hopError(string $field, array $errors): string {
    return $errors[$field] ?? '';
}
?>

<div class="form-group">
    <label class="form-label">Nom *</label>
    <input type="text" class="form-control <?= !empty($errors['nom']) ? 'input-error' : '' ?>"
           id="hf-nom" name="nom" value="<?= hopOld('nom', $old ?? [], $hopital ?? null) ?>"/>
    <?php if (hopError('nom', $errors ?? [])): ?>
        <span class="field-error"><?= htmlspecialchars(hopError('nom', $errors)) ?></span>
    <?php endif; ?>
</div>

<div class="form-row">
    <div class="form-group">
        <label class="form-label">Region *</label>
        <select class="form-control <?= !empty($errors['region']) ? 'input-error' : '' ?>" id="hf-region" name="region">
            <option value="">— Selectionner —</option>
            <?php foreach ($regions as $r): ?>
                <?php $selected = (hopOld('region', $old ?? [], $hopital ?? null) === $r) ? 'selected' : ''; ?>
                <option value="<?= htmlspecialchars($r) ?>" <?= $selected ?>><?= htmlspecialchars($r) ?></option>
            <?php endforeach; ?>
        </select>
        <?php if (hopError('region', $errors ?? [])): ?>
            <span class="field-error"><?= htmlspecialchars(hopError('region', $errors)) ?></span>
        <?php endif; ?>
    </div>
    <div class="form-group">
        <label class="form-label">Ville *</label>
        <input type="text" class="form-control <?= !empty($errors['ville']) ? 'input-error' : '' ?>"
               id="hf-ville" name="ville" value="<?= hopOld('ville', $old ?? [], $hopital ?? null) ?>"/>
        <?php if (hopError('ville', $errors ?? [])): ?>
            <span class="field-error"><?= htmlspecialchars(hopError('ville', $errors)) ?></span>
        <?php endif; ?>
    </div>
</div>

<div class="form-row">
    <div class="form-group">
        <label class="form-label">Type</label>
        <select class="form-control" id="hf-type" name="type">
            <?php $currentType = hopOld('type', $old ?? [], $hopital ?? null) ?: 'Public'; ?>
            <?php foreach (['CHU', 'Public', 'Prive'] as $t): ?>
                <option value="<?= $t ?>" <?= $currentType === $t ? 'selected' : '' ?>><?= $t ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="form-group">
        <label class="form-label">Lits</label>
        <input type="number" class="form-control" id="hf-lits" name="lits"
               value="<?= hopOld('lits', $old ?? [], $hopital ?? null) ?>" min="0"/>
        <?php if (hopError('lits', $errors ?? [])): ?>
            <span class="field-error"><?= htmlspecialchars(hopError('lits', $errors)) ?></span>
        <?php endif; ?>
    </div>
</div>

<div class="form-group">
    <label class="form-label">Telephone</label>
    <input type="text" class="form-control <?= !empty($errors['telephone']) ? 'input-error' : '' ?>"
           id="hf-tel" name="telephone" value="<?= hopOld('telephone', $old ?? [], $hopital ?? null) ?>"/>
    <?php if (hopError('telephone', $errors ?? [])): ?>
        <span class="field-error"><?= htmlspecialchars(hopError('telephone', $errors)) ?></span>
    <?php endif; ?>
</div>
