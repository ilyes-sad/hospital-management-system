<?php
$id                  = (int)$reclamation->getIdReclamation();
$reponseCount        = $reponseCount ?? 0;
$currentStatut       = $reclamation->getStatutReclamation();
$isFinal             = StatutReclamation::isFinal($currentStatut);
$canShowStatusForm   = StatutReclamation::allowsManualStatusWidget($currentStatut);
$manualStatusChoices = StatutReclamation::getManualStatusChoices($currentStatut);
$postedMessage       = $postedMessage ?? '';
$postedReponseStatut = $postedReponseStatut ?? '';
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Réclamation #<?= $id ?> — MediCare</title>
    <link rel="stylesheet" href="/hospital-management-system/public/css/style.css">
    <link rel="stylesheet" href="/hospital-management-system/app/Views/frontoffise/reclamation_form.css">
</head>
<body class="page-body">
<div class="page-wrapper">
    <div class="page-card">
        <div class="page-header">
            <div>
                <h1 class="page-title">Réclamation #<?= $id ?></h1>
                <p class="page-subtitle">Gestion et suivi du dossier</p>
            </div>
            <div class="detail-header-actions">
                <a href="/reclamations" class="page-button page-button--secondary">← Liste</a>
                <a href="/reclamations/<?= $id ?>/edit" class="page-button page-button--secondary">Modifier</a>
                <a href="#repondre" class="page-button">Répondre</a>
                <form method="post" action="/reclamations/<?= $id ?>/delete" style="display:inline" onsubmit="return confirm('Supprimer définitivement cette réclamation ?')">
                    <button type="submit" class="page-button page-button--danger">Supprimer</button>
                </form>
            </div>
        </div>

        <?php if (isset($_GET['updated'])): ?>
            <div class="message-box message-success">Statut mis à jour avec succès.</div>
        <?php endif; ?>
        <?php if (isset($_GET['responded'])): ?>
            <div class="message-box message-success">Réponse enregistrée et statut mis à jour.</div>
        <?php endif; ?>
        <?php if (isset($_GET['error'])): ?>
            <div class="message-box message-error"><?= htmlspecialchars($_GET['error']) ?></div>
        <?php endif; ?>
        <?php if (!empty($errors)): ?>
            <div class="message-box message-error">
                <ul>
                    <?php foreach ($errors as $err): ?>
                        <?php if (is_string($err) && $err !== ''): ?>
                            <li><?= htmlspecialchars($err) ?></li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <div class="detail-grid">
            <!-- Left: info + responses -->
            <div>
                <div class="detail-card">
                    <?php
                    $rows = [
                        'Patient'       => $reclamation->getNomPatient() ?: '—',
                        'E-mail'        => $reclamation->getEmailPatient() ?: '—',
                        'Hôpital'       => $reclamation->getNomHopital() ?: '—',
                        'Objet'         => $reclamation->getObjet(),
                        'Date de dépôt' => $reclamation->getDateDepot()->format('d/m/Y à H:i'),
                        'Service'       => $reclamation->getServiceHospitalier()?->getNomService() ?? '—',
                    ];
                    foreach ($rows as $label => $val): ?>
                        <div class="detail-row">
                            <span class="detail-label"><?= $label ?></span>
                            <span class="detail-value"><?= htmlspecialchars($val) ?></span>
                        </div>
                    <?php endforeach; ?>
                    <div class="detail-row">
                        <span class="detail-label">Description</span>
                        <span class="detail-value" style="white-space:pre-wrap"><?= htmlspecialchars($reclamation->getDescription()) ?></span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Statut</span>
                        <span class="detail-value">
                            <?php
                            $s = $reclamation->getStatutReclamation();
                            $bc = $s === 'resolue' ? 'badge-success' : ($s === 'rejetee' ? 'badge-danger' : ($s === 'en_cours' ? 'badge-warning' : 'badge-info'));
                            ?>
                            <span class="badge <?= $bc ?>"><?= htmlspecialchars($reclamation->getStatutLabel()) ?></span>
                        </span>
                    </div>
                </div>

                <div class="detail-card detail-card--responses" style="margin-top:16px">
                    <h2 class="detail-section-title">Réponses apportées</h2>
                    <?php if (!empty($responses)): ?>
                        <?php foreach ($responses as $resp): ?>
                            <div class="response-block">
                                <div class="response-meta"><?= htmlspecialchars((new DateTime($resp['date_reponse']))->format('d/m/Y à H:i')) ?></div>
                                <div class="response-text"><?= htmlspecialchars($resp['message']) ?></div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="response-empty">Aucune réponse pour le moment.</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Right: actions -->
            <div>
                <div class="actions-card">
                    <h2 class="detail-section-title">Actions</h2>

                    <?php if ($isFinal): ?>
                        <p class="business-rule-notice business-rule-notice--muted">Réclamation clôturée — le statut ne peut plus être modifié.</p>
                    <?php elseif ($canShowStatusForm): ?>
                        <form method="post" action="/reclamations/<?= $id ?>/status">
                            <label class="field-label-block" for="statut_select">Changer le statut</label>
                            <p class="field-hint" style="margin-bottom:10px">Étape 1 : passez en « En attente » avant de répondre.</p>
                            <select name="statut" id="statut_select" class="field-select-full">
                                <?php foreach ($manualStatusChoices as $k => $v): ?>
                                    <option value="<?= htmlspecialchars($k) ?>" <?= $currentStatut === $k ? 'selected' : '' ?>><?= htmlspecialchars($v) ?></option>
                                <?php endforeach; ?>
                            </select>
                            <button type="submit" class="page-button" style="width:100%;margin-top:10px">Mettre à jour</button>
                        </form>
                    <?php else: ?>
                        <p class="business-rule-notice">Statut : <strong><?= htmlspecialchars($reclamation->getStatutLabel()) ?></strong>. Pour clôturer, utilisez « Répondre » ci-dessous.</p>
                    <?php endif; ?>

                    <hr class="actions-divider">

                    <div id="repondre" class="repondre-section">
                        <?php if ($isFinal): ?>
                            <p class="business-rule-notice business-rule-notice--muted">Dossier terminé — aucune nouvelle réponse.</p>
                        <?php elseif ($reponseCount > 0): ?>
                            <p class="business-rule-notice business-rule-notice--success">Réponse enregistrée — une seule réponse par dossier.</p>
                        <?php elseif ($currentStatut !== StatutReclamation::EN_COURS): ?>
                            <p class="business-rule-notice">Réponse disponible après passage en « En attente ».</p>
                            <button type="button" class="page-button page-button--secondary" style="width:100%;opacity:.65;cursor:not-allowed" disabled>Répondre (étape requise)</button>
                        <?php else: ?>
                            <button type="button" id="toggleReponseForm" class="page-button" style="width:100%;background:#059669">
                                Répondre (Acceptée / Refusée)
                            </button>
                            <div id="reponseFormContainer" style="display:none;margin-top:16px">
                                <h3 class="detail-section-title" style="font-size:.9rem;margin-bottom:8px">Réponse finale</h3>
                                <form method="post" action="/reclamations/<?= $id ?>/respond">
                                    <div class="field-group" style="margin-bottom:14px">
                                        <label for="message">Message *</label>
                                        <textarea name="message" id="message" class="field-textarea" style="width:100%;min-height:140px" required><?= htmlspecialchars($postedMessage) ?></textarea>
                                        <span class="field-hint">10 à 2000 caractères.</span>
                                    </div>
                                    <div class="field-group" style="margin-bottom:14px">
                                        <label for="reponse_statut">Décision *</label>
                                        <select name="reponse_statut" id="reponse_statut" class="field-select-full" required>
                                            <option value="">Choisir</option>
                                            <option value="resolue" <?= $postedReponseStatut === 'resolue' ? 'selected' : '' ?>>Acceptée</option>
                                            <option value="rejetee" <?= $postedReponseStatut === 'rejetee' ? 'selected' : '' ?>>Refusée</option>
                                        </select>
                                    </div>
                                    <div style="display:flex;gap:8px">
                                        <button type="submit" class="page-button" style="flex:1;background:#059669">Enregistrer</button>
                                        <button type="button" id="cancelReponseBtn" class="page-button page-button--secondary" style="flex:1">Annuler</button>
                                    </div>
                                </form>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
(function () {
    var toggle    = document.getElementById('toggleReponseForm');
    var container = document.getElementById('reponseFormContainer');
    var cancel    = document.getElementById('cancelReponseBtn');
    if (toggle && container) {
        toggle.addEventListener('click', function () {
            container.style.display = container.style.display === 'none' ? 'block' : 'none';
        });
    }
    if (cancel && container) {
        cancel.addEventListener('click', function () { container.style.display = 'none'; });
    }
    if (window.location.hash === '#repondre' && container) {
        container.style.display = 'block';
        container.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
})();
</script>
</body>
</html>
