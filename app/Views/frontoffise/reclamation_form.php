<?php
if (!isset($successMessage)) {
    $successMessage = isset($_GET['success']) ? 'Votre réclamation a bien été enregistrée. Nous vous répondrons bientôt.' : null;
}
$objet          = $formData['objet']               ?? '';
$description    = $formData['description']         ?? '';
$selectedService = isset($formData['service_hospitalier']) ? (string)$formData['service_hospitalier'] : '';
$nomPatient     = $formData['nom_patient']         ?? '';
$emailPatient   = $formData['email_patient']       ?? '';
$nomHopital     = $formData['nom_hopital']         ?? '';
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Nouvelle réclamation | MediCare</title>
    <link rel="stylesheet" href="/hospital-management-system/public/css/style.css">
    <link rel="stylesheet" href="/hospital-management-system/app/Views/frontoffise/reclamation_form.css">
</head>
<body class="reclamation-body">
<div class="reclamation-wrapper">
    <div class="reclamation-layout">
        <aside class="reclamation-sidebar">
            <div class="sidebar-brand">
                <div class="sidebar-icon">+</div>
                <div>
                    <p class="sidebar-label">MediCare</p>
                    <p class="sidebar-subtitle">Réclamations</p>
                </div>
            </div>
            <nav class="sidebar-menu">
                <a href="/reclamations/new" class="sidebar-link sidebar-link--active">Nouvelle réclamation</a>
                <a href="/reclamations" class="sidebar-link">Administration</a>
            </nav>
        </aside>

        <div class="reclamation-card">
            <div class="reclamation-header">
                <div class="brand-circle">+</div>
                <div class="brand-text">
                    <p class="brand-label">PLATEFORME WEB NATIONALE</p>
                    <h1 class="brand-title">MULTI-HÔPITAUX</h1>
                </div>
            </div>

            <?php if ($successMessage): ?>
                <div class="message-box message-success" id="successMessage"><?= htmlspecialchars($successMessage) ?></div>
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

            <form action="/reclamations/save" method="post" novalidate class="reclamation-form">
                <div class="field-group">
                    <label for="nom_patient">Nom du patient *</label>
                    <input id="nom_patient" name="nom_patient" type="text"
                           class="field-input <?= isset($errors['nom_patient']) ? 'field-invalid' : '' ?>"
                           value="<?= htmlspecialchars($nomPatient) ?>" autocomplete="name">
                    <?php if (isset($errors['nom_patient'])): ?>
                        <div class="field-error"><?= htmlspecialchars($errors['nom_patient']) ?></div>
                    <?php endif; ?>
                </div>

                <div class="field-group">
                    <label for="email_patient">E-mail *</label>
                    <input id="email_patient" name="email_patient" type="email"
                           class="field-input <?= isset($errors['email_patient']) ? 'field-invalid' : '' ?>"
                           value="<?= htmlspecialchars($emailPatient) ?>" autocomplete="email">
                    <?php if (isset($errors['email_patient'])): ?>
                        <div class="field-error"><?= htmlspecialchars($errors['email_patient']) ?></div>
                    <?php endif; ?>
                </div>

                <div class="field-group">
                    <label for="nom_hopital">Hôpital / établissement *</label>
                    <input id="nom_hopital" name="nom_hopital" type="text"
                           class="field-input <?= isset($errors['nom_hopital']) ? 'field-invalid' : '' ?>"
                           value="<?= htmlspecialchars($nomHopital) ?>" placeholder="Ex. CHU Ibn Sina">
                    <?php if (isset($errors['nom_hopital'])): ?>
                        <div class="field-error"><?= htmlspecialchars($errors['nom_hopital']) ?></div>
                    <?php endif; ?>
                </div>

                <div class="field-group">
                    <label for="objet">Objet *</label>
                    <input id="objet" name="objet" type="text"
                           class="field-input <?= isset($errors['objet']) ? 'field-invalid' : '' ?>"
                           value="<?= htmlspecialchars($objet) ?>" placeholder="Ex : Problème avec ma prise en charge">
                    <?php if (isset($errors['objet'])): ?>
                        <div class="field-error"><?= htmlspecialchars($errors['objet']) ?></div>
                    <?php endif; ?>
                </div>

                <div class="field-group">
                    <label for="description">Description *</label>
                    <textarea id="description" name="description" rows="5"
                              class="field-input field-textarea <?= isset($errors['description']) ? 'field-invalid' : '' ?>"
                              placeholder="Décrivez votre demande avec précision..."><?= htmlspecialchars($description) ?></textarea>
                    <?php if (isset($errors['description'])): ?>
                        <div class="field-error"><?= htmlspecialchars($errors['description']) ?></div>
                    <?php endif; ?>
                </div>

                <div class="field-group">
                    <label for="service_hospitalier">Catégorie *</label>
                    <select id="service_hospitalier" name="service_hospitalier"
                            class="field-input field-select <?= isset($errors['service_hospitalier']) ? 'field-invalid' : '' ?>" required>
                        <option value="" <?= $selectedService === '' ? 'selected' : '' ?> disabled>Sélectionnez un service</option>
                        <?php if (empty($services)): ?>
                            <option value="" disabled>Aucun service disponible</option>
                        <?php else: ?>
                            <?php foreach ($services as $service): ?>
                                <option value="<?= $service->getIdService() ?>"
                                    <?= $selectedService === (string)$service->getIdService() ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($service->getNomService()) ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                    <?php if (isset($errors['service_hospitalier'])): ?>
                        <div class="field-error"><?= htmlspecialchars($errors['service_hospitalier']) ?></div>
                    <?php endif; ?>
                </div>

                <button type="submit" class="submit-button">ENVOYER</button>
            </form>

            <div class="card-footer">Copyright &copy; 2026 MediCare</div>
        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var msg = document.getElementById('successMessage');
    if (!msg) return;
    setTimeout(function () {
        msg.style.transition = 'opacity 0.5s ease';
        msg.style.opacity = '0';
        setTimeout(function () { if (msg.parentNode) msg.parentNode.removeChild(msg); }, 500);
    }, 5000);
});
</script>
</body>
</html>
