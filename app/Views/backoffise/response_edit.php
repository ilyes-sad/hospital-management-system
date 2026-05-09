<?php
if (!isset($successMessage)) {
    $successMessage = isset($_GET['updated']) ? 'La réponse a été mise à jour avec succès.' : null;
}
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Éditer réponse #<?= (int)$response['id_reponse'] ?> — MediCare</title>
    <link rel="stylesheet" href="/hospital-management-system/public/css/style.css">
    <link rel="stylesheet" href="/hospital-management-system/app/Views/frontoffise/reclamation_form.css">
</head>
<body class="page-body">
<div class="page-wrapper">
    <div class="page-card">
        <div class="page-header">
            <div>
                <h1 class="page-title">Éditer la réponse</h1>
                <p class="page-subtitle">ID #<?= (int)$response['id_reponse'] ?></p>
            </div>
            <a href="/reponses/<?= (int)$response['id_reponse'] ?>" class="page-button page-button--secondary">Retour</a>
        </div>

        <div class="form-card">
            <?php if ($successMessage): ?>
                <div class="message-box message-success"><?= htmlspecialchars($successMessage) ?></div>
            <?php endif; ?>
            <?php if (!empty($errors)): ?>
                <div class="message-box message-error">
                    <ul><?php foreach ($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul>
                </div>
            <?php endif; ?>

            <form action="/reponses/<?= (int)$response['id_reponse'] ?>/update" method="post" novalidate>
                <div class="field-group">
                    <label>Réclamation</label>
                    <div class="field-input" style="background:rgba(0,0,0,.04);cursor:not-allowed">
                        #<?= htmlspecialchars($response['id_reclamation']) ?>
                    </div>
                </div>

                <div class="field-group">
                    <label for="message">Message *</label>
                    <textarea id="message" name="message" rows="8"
                              class="field-input field-textarea <?= isset($errors['message']) ? 'field-invalid' : '' ?>"><?= htmlspecialchars($formData['message'] ?? '') ?></textarea>
                    <?php if (isset($errors['message'])): ?>
                        <div class="field-error"><?= htmlspecialchars($errors['message']) ?></div>
                    <?php endif; ?>
                </div>

                <div class="field-group">
                    <label for="statut_reclamation">Statut de la réclamation</label>
                    <select id="statut_reclamation" name="statut_reclamation"
                            class="field-input <?= isset($errors['statut_reclamation']) ? 'field-invalid' : '' ?>">
                        <?php $sel = $formData['statut_reclamation'] ?? $response['statutReclamation']; ?>
                        <?php foreach ($statusOptions as $k => $v): ?>
                            <option value="<?= htmlspecialchars($k) ?>" <?= $sel === $k ? 'selected' : '' ?>><?= htmlspecialchars($v) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (isset($errors['statut_reclamation'])): ?>
                        <div class="field-error"><?= htmlspecialchars($errors['statut_reclamation']) ?></div>
                    <?php endif; ?>
                </div>

                <div style="display:flex;gap:10px;margin-top:16px">
                    <button type="submit" class="submit-button" style="flex:1">METTRE À JOUR</button>
                    <a href="/reponses" class="page-button page-button--secondary" style="flex:1;text-align:center">Annuler</a>
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>
