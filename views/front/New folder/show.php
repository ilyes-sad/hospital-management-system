<?php require __DIR__ . '/../../../templates/front/header.php'; ?>

<div class="profile-page">
    <div class="profile-shell">

        <div class="profile-hero">
            <div class="profile-hero-left">
                <div class="profile-avatar-lg">💬</div>

                <div>
                    <h1 class="profile-name">Détail réclamation</h1>
                    <p class="profile-role">
                        Consultez la réponse de l’administration.
                    </p>
                </div>
            </div>

            <div class="profile-hero-actions">
                <a href="index.php?action=myReclamations" class="btn btn-outline">
                    Retour
                </a>
            </div>
        </div>

       <section class="modern-reclamation-card">

    <div class="modern-reclamation-header">

        <div class="modern-reclamation-title-wrap">

            <div class="modern-reclamation-icon">
                ⚠️
            </div>

            <div>
                <h2 class="modern-reclamation-title">
                    <?= htmlspecialchars($reclamation['objet']) ?>
                </h2>

                <div class="modern-reclamation-date">
                    Déposée le <?= htmlspecialchars($reclamation['dateDepot']) ?>
                </div>
            </div>

        </div>

        <?php
    $statut = $reclamation['statutReclamation'];
$statutClass = str_replace('é', 'e', $statut);
?>

<span class="badge badge-reclamation-<?= htmlspecialchars($statutClass) ?>">
    <?= htmlspecialchars($statut) ?>
</span>

    </div>

    <div class="modern-reclamation-infos">

        <div class="modern-info-box">

            <div class="modern-info-label">
                Catégorie
            </div>

            <div class="modern-info-value">
                <?= htmlspecialchars($reclamation['libelleCategorie']) ?>
            </div>

        </div>

        <div class="modern-info-box">

            <div class="modern-info-label">
                Service
            </div>

            <div class="modern-info-value">
                <?= htmlspecialchars($reclamation['nomService']) ?>
            </div>

        </div>

    </div>

    <div class="modern-description-box">

        <div class="modern-description-head">
            Description
        </div>

        <div class="modern-description-body">
            <?= nl2br(htmlspecialchars($reclamation['description'])) ?>
        </div>

    </div>

</section>

        <section class="card">
            <div class="card-header">
                <div>
                    <h2 class="card-title">Réponses de l’administration</h2>
                    <p class="card-subtitle">Suivi de votre réclamation</p>
                </div>
            </div>

            <?php if (empty($reponses)): ?>
                <div class="empty-box">
                    Aucune réponse pour le moment.
                </div>
            <?php endif; ?>

            <?php foreach ($reponses as $rep): ?>
                <div class="response-item">

    <div class="response-dot"></div>

    <div class="response-card">

        <div class="response-card-head">

            <div class="response-admin">

                <div class="response-avatar">
                    A
                </div>

                <div>
                    <strong>Administration</strong>

                    <div class="response-role">
                        Support Medicare
                    </div>
                </div>

            </div>

            <span class="response-date">
                <?= htmlspecialchars($rep['dateReponse']) ?>
            </span>

        </div>

        <div class="response-content">
            <?= nl2br(htmlspecialchars($rep['contenu'])) ?>
        </div>

    </div>

</div>
            <?php endforeach; ?>
        </section>

    </div>
</div>
<style>
    /* ===== MODERN RECLAMATION CARD ===== */

.modern-reclamation-card {
    background: white;
    border: 1px solid var(--border);
    border-radius: 22px;
    padding: 24px;
    box-shadow: var(--shadow);
    margin-bottom: 24px;
}

.modern-reclamation-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 18px;
    margin-bottom: 24px;
}

.modern-reclamation-title-wrap {
    display: flex;
    align-items: center;
    gap: 16px;
}

.modern-reclamation-icon {
    width: 58px;
    height: 58px;
    border-radius: 18px;
    background: var(--danger-light);
    color: var(--danger);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.4rem;
    flex-shrink: 0;
}

.modern-reclamation-title {
    margin: 0 0 6px;
    font-size: 1.35rem;
    color: var(--navy);
}

.modern-reclamation-date {
    color: var(--text-muted);
    font-size: 0.88rem;
}

.modern-reclamation-infos {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 16px;
    margin-bottom: 24px;
}

.modern-info-box {
    padding: 18px;
    background: #f8fbff;
    border: 1px solid var(--border);
    border-radius: 16px;
}

.modern-info-label {
    font-size: 0.8rem;
    color: var(--text-muted);
    margin-bottom: 8px;
}

.modern-info-value {
    font-weight: 600;
    color: var(--text-primary);
}

.modern-description-box {
    border: 1px solid var(--border);
    border-radius: 18px;
    overflow: hidden;
    background: white;
}

.modern-description-head {
    padding: 16px 20px;
    background: #f8fbff;
    border-bottom: 1px solid var(--border);
    font-weight: 700;
    color: var(--navy);
}

.modern-description-body {
    padding: 22px;
    line-height: 1.8;
    color: var(--text-secondary);
    white-space: pre-wrap;
    word-break: break-word;
}

@media (max-width: 768px) {

    .modern-reclamation-header {
        flex-direction: column;
        align-items: stretch;
    }

    .modern-reclamation-title-wrap {
        align-items: flex-start;
    }

}
    /* ===== RESPONSE CARD IMPROVED ===== */

.response-item {
    position: relative;
    margin-bottom: 18px;
    padding-left: 26px;
}

.response-dot {
    position: absolute;
    left: 0;
    top: 24px;
    width: 14px;
    height: 14px;
    border-radius: 50%;
    background: var(--accent);
    border: 3px solid white;
    box-shadow: 0 0 0 4px rgba(14,165,233,0.12);
}

.response-card {
    background: linear-gradient(180deg, #ffffff 0%, #fbfdff 100%);
    border: 1px solid var(--border);
    border-radius: 18px;
    padding: 20px;
    box-shadow: var(--shadow);
    transition: all var(--transition);
}

.response-card:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}

.response-card-head {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    gap: 14px;
    margin-bottom: 16px;
}

.response-admin {
    display: flex;
    align-items: center;
    gap: 12px;
}

.response-avatar {
    width: 46px;
    height: 46px;
    border-radius: 14px;
    background: linear-gradient(
        135deg,
        var(--accent),
        var(--accent-dark)
    );
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 1rem;
    flex-shrink: 0;
    box-shadow: 0 10px 20px rgba(14,165,233,0.18);
}

.response-role {
    margin-top: 2px;
    font-size: 0.78rem;
    color: var(--text-muted);
}

.response-date {
    font-size: 0.8rem;
    color: var(--text-muted);
    white-space: nowrap;
}

.response-content {
    line-height: 1.8;
    color: var(--text-primary);
    font-size: 0.93rem;
    white-space: pre-wrap;
    word-break: break-word;
}
.badge-reclamation-rejetee,
.badge-reclamation-rejetée {
    background: var(--danger-light);
    color: var(--danger);
    border: 1px solid rgba(220, 38, 38, 0.18);
}
</style>
<?php require __DIR__ . '/../../../templates/front/footer.php'; ?>