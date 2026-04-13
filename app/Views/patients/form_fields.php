<?php
// ============================================================
//  app/Views/patients/form_fields.php
// ============================================================

function patOld(string $field, array $old, $patient = null): string {
    if (!empty($old[$field])) return htmlspecialchars($old[$field]);
    if ($patient && isset($patient[$field]) && $patient[$field] !== null) return htmlspecialchars($patient[$field]);
    return '';
}

function patError(string $field, array $errors): string {
    return $errors[$field] ?? '';
}
?>

<div class="form-row">
    <div class="form-group">
        <label class="form-label">Prenom *</label>
        <input type="text" class="form-control <?= !empty($errors['prenom']) ? 'input-error' : '' ?>"
               name="prenom" value="<?= patOld('prenom', $old ?? [], $patient ?? null) ?>"/>
        <?php if (patError('prenom', $errors ?? [])): ?>
            <span class="field-error"><?= htmlspecialchars(patError('prenom', $errors)) ?></span>
        <?php endif; ?>
    </div>
    <div class="form-group">
        <label class="form-label">Nom *</label>
        <input type="text" class="form-control <?= !empty($errors['nom']) ? 'input-error' : '' ?>"
               name="nom" value="<?= patOld('nom', $old ?? [], $patient ?? null) ?>"/>
        <?php if (patError('nom', $errors ?? [])): ?>
            <span class="field-error"><?= htmlspecialchars(patError('nom', $errors)) ?></span>
        <?php endif; ?>
    </div>
</div>

<div class="form-row">
    <div class="form-group">
        <label class="form-label">CIN</label>
        <input type="text" class="form-control <?= !empty($errors['cin']) ? 'input-error' : '' ?>"
               name="cin" value="<?= patOld('cin', $old ?? [], $patient ?? null) ?>"/>
        <?php if (patError('cin', $errors ?? [])): ?>
            <span class="field-error"><?= htmlspecialchars(patError('cin', $errors)) ?></span>
        <?php endif; ?>
    </div>
    <div class="form-group">
        <label class="form-label">Date de naissance</label>
        <input type="text" class="form-control <?= !empty($errors['date_naissance']) ? 'input-error' : '' ?>"
               name="date_naissance" placeholder="AAAA-MM-JJ" value="<?= patOld('date_naissance', $old ?? [], $patient ?? null) ?>"/>
        <?php if (patError('date_naissance', $errors ?? [])): ?>
            <span class="field-error"><?= htmlspecialchars(patError('date_naissance', $errors)) ?></span>
        <?php endif; ?>
    </div>
</div>

<div class="form-row">
    <div class="form-group">
        <label class="form-label">Sexe</label>
        <select class="form-control" name="sexe">
            <?php $val = patOld('sexe', $old ?? [], $patient ?? null); ?>
            <option value="">—</option>
            <option value="M" <?= $val === 'M' ? 'selected' : '' ?>>Homme</option>
            <option value="F" <?= $val === 'F' ? 'selected' : '' ?>>Femme</option>
        </select>
    </div>
    <div class="form-group">
        <label class="form-label">Groupe sanguin</label>
        <select class="form-control" name="groupe_sanguin">
            <?php $val = patOld('groupe_sanguin', $old ?? [], $patient ?? null); ?>
            <?php foreach (['','A+','A-','B+','B-','AB+','AB-','O+','O-'] as $g): ?>
                <option value="<?= $g ?>" <?= $val === $g ? 'selected' : '' ?>><?= $g ?: '—' ?></option>
            <?php endforeach; ?>
        </select>
    </div>
</div>

<div class="form-row">
    <div class="form-group">
        <label class="form-label">Telephone</label>
        <input type="text" class="form-control <?= !empty($errors['telephone']) ? 'input-error' : '' ?>"
               name="telephone" value="<?= patOld('telephone', $old ?? [], $patient ?? null) ?>"/>
        <?php if (patError('telephone', $errors ?? [])): ?>
            <span class="field-error"><?= htmlspecialchars(patError('telephone', $errors)) ?></span>
        <?php endif; ?>
    </div>
    <div class="form-group">
        <label class="form-label">Email</label>
        <input type="text" class="form-control <?= !empty($errors['email']) ? 'input-error' : '' ?>"
               name="email" value="<?= patOld('email', $old ?? [], $patient ?? null) ?>"/>
        <?php if (patError('email', $errors ?? [])): ?>
            <span class="field-error"><?= htmlspecialchars(patError('email', $errors)) ?></span>
        <?php endif; ?>
    </div>
</div>

<div class="form-group">
    <label class="form-label">Ville</label>
    <input type="text" class="form-control" name="ville" value="<?= patOld('ville', $old ?? [], $patient ?? null) ?>"/>
</div>
