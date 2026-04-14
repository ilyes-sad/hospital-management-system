<?php
if (!isset($successMessage)) {
    $successMessage = isset($_GET['success']) ? 'Votre réclamation a bien été enregistrée. Nous vous répondrons bientôt.' : null;
}
$objet = $formData['objet'] ?? '';
$description = $formData['description'] ?? '';
$selectedService = isset($formData['service_hospitalier']) ? (string)$formData['service_hospitalier'] : '';
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Nouvelle réclamation | Plateforme Multi-Hospitalière</title>
    <link rel="stylesheet" href="views/frontoffise/reclamation_form.css">
</head>
<body class="reclamation-body">
<div class="reclamation-wrapper">
    <div class="reclamation-layout">
        <aside class="reclamation-sidebar">
            <div class="sidebar-brand">
                <div class="sidebar-icon">+</div>
                <div>
                    <p class="sidebar-label">MediCare</p>
                    <p class="sidebar-subtitle">Reclamations</p>
                </div>
            </div>
            <nav class="sidebar-menu">
                <a href="#" class="sidebar-link">Tableau de bord</a>
                <a href="#" class="sidebar-link">Rendez-vous</a>
                <a href="#" class="sidebar-link">Patients</a>
                <a href="#" class="sidebar-link">Médicins</a>
                <a href="#" class="sidebar-link sidebar-link--active">Réclamations</a>
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

        <form action="reclamation.php?route=reclamation/save" method="post" novalidate class="reclamation-form">
            <div class="field-group">
                <label for="objet">Objet</label>
                <input id="objet" name="objet" type="text" class="field-input <?= isset($errors['objet']) ? 'field-invalid' : '' ?>" value="<?= htmlspecialchars($objet) ?>" placeholder="Ex : Problème avec ma prise en charge">
                <?php if (isset($errors['objet'])): ?>
                    <div class="field-error"><?= htmlspecialchars($errors['objet']) ?></div>
                <?php endif; ?>
            </div>

            <div class="field-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" rows="5" class="field-input field-textarea <?= isset($errors['description']) ? 'field-invalid' : '' ?>" placeholder="Décrivez votre demande avec précision..."><?= htmlspecialchars($description) ?></textarea>
                <?php if (isset($errors['description'])): ?>
                    <div class="field-error"><?= htmlspecialchars($errors['description']) ?></div>
                <?php endif; ?>
            </div>

            <div class="field-group">
                <label for="service_hospitalier">Catégorie</label>
                <select id="service_hospitalier" name="service_hospitalier" class="field-input field-select <?= isset($errors['service_hospitalier']) ? 'field-invalid' : '' ?>" required>
                    <option value="" <?= $selectedService === '' ? 'selected' : '' ?> disabled>Sélectionnez un service</option>
                    <?php if (empty($services)): ?>
                        <option value="" disabled>Aucun service disponible</option>
                    <?php else: ?>
                        <?php foreach ($services as $service): ?>
                            <option value="<?= $service->getIdService() ?>" <?= $selectedService === (string)$service->getIdService() ? 'selected' : '' ?>><?= htmlspecialchars($service->getNomService()) ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
                <?php if (isset($errors['service_hospitalier'])): ?>
                    <div class="field-error"><?= htmlspecialchars($errors['service_hospitalier']) ?></div>
                <?php endif; ?>
            </div>

            <button type="submit" class="submit-button">ENVOYER</button>
        </form>

        <div class="card-footer">Copyright (C) 2026 ANMPS</div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var successMessage = document.getElementById('successMessage');
        if (!successMessage) {
            return;
        }
        setTimeout(function() {
            successMessage.style.transition = 'opacity 0.5s ease';
            successMessage.style.opacity = '0';
            setTimeout(function() {
                if (successMessage.parentNode) {
                    successMessage.parentNode.removeChild(successMessage);
                }
            }, 500);
        }, 5000);
    });
</script>
</body>
</html>
