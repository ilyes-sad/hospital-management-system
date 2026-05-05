<?php
if (!isset($successMessage)) {
    $successMessage = isset($_GET['updated']) ? 'La réclamation a été mise à jour avec succès.' : null;
}
$objet          = $formData['objet']               ?? '';
$description    = $formData['description']         ?? '';
$selectedService = $formData['service_hospitalier'] ?? '';
$selectedStatus = $formData['statut_reclamation']  ?? '';
$nomPatient     = $formData['nom_patient']         ?? '';
$emailPatient   = $formData['email_patient']       ?? '';
$nomHopital     = $formData['nom_hopital']         ?? '';
$editStatutCourant = $reclamation->getStatutReclamation();
$editStatutFinal   = StatutReclamation::isFinal($editStatutCourant);
$editStatutChoices = StatutReclamation::getManualStatusChoices($editStatutCourant);
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Modifier réclamation #<?= htmlspecialchars($reclamation->getIdReclamation()) ?> — MediCare</title>
    <link rel="stylesheet" href="/hospital-management-system/public/css/style.css">
    <link rel="stylesheet" href="/hospital-management-system/app/Views/frontoffise/reclamation_form.css">
</head>
<body class="page-body">
<div class="page-wrapper">
    <div class="page-card">
        <div class="page-header">
            <div>
                <h1 class="page-title">Modifier la réclamation</h1>
                <p class="page-subtitle">ID #<?= htmlspecialchars($reclamation->getIdReclamation()) ?></p>
            </div>
            <a href="/reclamations/<?= htmlspecialchars($reclamation->getIdReclamation()) ?>" class="page-button">Retour au détail</a>
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

            <form action="/reclamations/<?= htmlspecialchars($reclamation->getIdReclamation()) ?>/edit" method="post" novalidate>
                <?php
                $fields = [
                    ['nom_patient',   'Nom du patient',          'text',  $nomPatient],
                    ['email_patient', 'E-mail',                  'email', $emailPatient],
                    ['nom_hopital',   'Hôpital / établissement', 'text',  $nomHopital],
                    ['objet',         'Objet',                   'text',  $objet],
                ];
                foreach ($fields as [$name, $label, $type, $val]): ?>
                    <div class="field-group">
                        <label for="<?= $name ?>"><?= $label ?></label>
                        <input id="<?= $name ?>" name="<?= $name ?>" type="<?= $type ?>"
                               class="field-input <?= isset($errors[$name]) ? 'field-invalid' : '' ?>"
                               value="<?= htmlspecialchars($val) ?>">
                        <?php if (isset($errors[$name])): ?>
                            <div class="field-error"><?= htmlspecialchars($errors[$name]) ?></div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>

                <div class="field-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" rows="5"
                              class="field-input field-textarea <?= isset($errors['description']) ? 'field-invalid' : '' ?>"><?= htmlspecialchars($description) ?></textarea>
                    <?php if (isset($errors['description'])): ?>
                        <div class="field-error"><?= htmlspecialchars($errors['description']) ?></div>
                    <?php endif; ?>
                </div>

                <div class="field-group">
                    <label for="service_hospitalier">Service</label>
                    <select id="service_hospitalier" name="service_hospitalier"
                            class="field-input field-select <?= isset($errors['service_hospitalier']) ? 'field-invalid' : '' ?>">
                        <option value="">Sélectionnez un service</option>
                        <?php foreach ($services as $s): ?>
                            <option value="<?= $s->getIdService() ?>" <?= $selectedService == $s->getIdService() ? 'selected' : '' ?>><?= htmlspecialchars($s->getNomService()) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (isset($errors['service_hospitalier'])): ?>
                        <div class="field-error"><?= htmlspecialchars($errors['service_hospitalier']) ?></div>
                    <?php endif; ?>
                </div>

                <div class="field-group">
                    <label for="statut_reclamation">Statut</label>
                    <?php if ($editStatutFinal): ?>
                        <p class="field-hint"><strong><?= htmlspecialchars($reclamation->getStatutLabel()) ?></strong> — clôturée, non modifiable ici.</p>
                        <input type="hidden" name="statut_reclamation" value="<?= htmlspecialchars($editStatutCourant) ?>">
                    <?php elseif (count($editStatutChoices) <= 1): ?>
                        <p class="field-hint"><?= htmlspecialchars($reclamation->getStatutLabel()) ?> — pour Acceptée / Refusée, utilisez « Répondre » sur la fiche.</p>
                        <input type="hidden" name="statut_reclamation" value="<?= htmlspecialchars($editStatutCourant) ?>">
                    <?php else: ?>
                        <select id="statut_reclamation" name="statut_reclamation"
                                class="field-input field-select <?= isset($errors['statut_reclamation']) ? 'field-invalid' : '' ?>">
                            <?php foreach ($editStatutChoices as $k => $v): ?>
                                <option value="<?= htmlspecialchars($k) ?>" <?= $selectedStatus === $k ? 'selected' : '' ?>><?= htmlspecialchars($v) ?></option>
                            <?php endforeach; ?>
                        </select>
                    <?php endif; ?>
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
