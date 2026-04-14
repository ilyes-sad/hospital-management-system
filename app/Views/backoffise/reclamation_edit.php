<?php
if (!isset($successMessage)) {
    $successMessage = isset($_GET['updated']) ? 'La réclamation a été mise à jour avec succès.' : null;
}
$objet = $formData['objet'] ?? '';
$description = $formData['description'] ?? '';
$selectedService = $formData['service_hospitalier'] ?? '';
$selectedStatus = $formData['statut_reclamation'] ?? '';
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Modifier la réclamation #<?= htmlspecialchars($reclamation->getIdReclamation()) ?></title>
    <link rel="stylesheet" href="views/frontoffise/reclamation_form.css">
</head>
<body class="page-body">
<div class="page-wrapper">
    <div class="page-card">
        <div class="page-header">
            <div>
                <h1 class="page-title">Modifier la réclamation</h1>
                <p class="page-subtitle">ID #<?= htmlspecialchars($reclamation->getIdReclamation()) ?></p>
            </div>
            <a href="reclamation.php?route=admin/reclamation/<?= htmlspecialchars($reclamation->getIdReclamation()) ?>" class="page-button">Retour au détail</a>
        </div>

        <div class="form-card">
            <?php if ($successMessage): ?>
                <div class="message-box message-success"><?= htmlspecialchars($successMessage) ?></div>
            <?php endif; ?>

            <?php if (!empty($errors)): ?>
                <div class="message-box message-error">
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?= htmlspecialchars($error) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form action="reclamation.php?route=admin/reclamation/<?= htmlspecialchars($reclamation->getIdReclamation()) ?>/edit" method="post" novalidate>
                <div class="field-group">
                    <label for="objet">Objet</label>
                    <input id="objet" name="objet" type="text" class="field-input <?= isset($errors['objet']) ? 'field-invalid' : '' ?>" value="<?= htmlspecialchars($objet) ?>">
                    <?php if (isset($errors['objet'])): ?>
                        <div class="field-error"><?= htmlspecialchars($errors['objet']) ?></div>
                    <?php endif; ?>
                </div>

                <div class="field-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" rows="5" class="field-input field-textarea <?= isset($errors['description']) ? 'field-invalid' : '' ?>"><?= htmlspecialchars($description) ?></textarea>
                    <?php if (isset($errors['description'])): ?>
                        <div class="field-error"><?= htmlspecialchars($errors['description']) ?></div>
                    <?php endif; ?>
                </div>

                <div class="field-group">
                    <label for="service_hospitalier">Service</label>
                    <select id="service_hospitalier" name="service_hospitalier" class="field-input field-select <?= isset($errors['service_hospitalier']) ? 'field-invalid' : '' ?>">
                        <option value="">Sélectionnez un service</option>
                        <?php foreach ($services as $service): ?>
                            <option value="<?= $service->getIdService() ?>" <?= $selectedService == $service->getIdService() ? 'selected' : '' ?>><?= htmlspecialchars($service->getNomService()) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (isset($errors['service_hospitalier'])): ?>
                        <div class="field-error"><?= htmlspecialchars($errors['service_hospitalier']) ?></div>
                    <?php endif; ?>
                </div>

                <div class="field-group">
                    <label for="statut_reclamation">Statut</label>
                    <select id="statut_reclamation" name="statut_reclamation" class="field-input field-select <?= isset($errors['statut_reclamation']) ? 'field-invalid' : '' ?>">
                        <?php foreach ($statusOptions as $value => $label): ?>
                            <option value="<?= htmlspecialchars($value) ?>" <?= $selectedStatus === $value ? 'selected' : '' ?>><?= htmlspecialchars($label) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (isset($errors['statut_reclamation'])): ?>
                        <div class="field-error"><?= htmlspecialchars($errors['statut_reclamation']) ?></div>
                    <?php endif; ?>
                </div>

                <div class="field-group">
                    <button type="submit" class="submit-button">ENREGISTRER</button>
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>
