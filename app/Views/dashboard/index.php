<?php
// ============================================================
//  app/Views/dashboard/index.php
// ============================================================
$baseUrl = '/medapp2/public';
?>

<div class="stats-grid">
    <div class="stat-card" style="--stat-color:#0EA5E9;--stat-bg:#E0F2FE">
        <div class="stat-icon">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
        </div>
        <div class="stat-info">
            <div class="stat-label">Rendez-vous</div>
            <div class="stat-value"><?= (int) ($stats['total'] ?? 0) ?></div>
            <div class="stat-trend">↑ <?= (int) ($stats['aujourd_hui'] ?? 0) ?> aujourd'hui</div>
        </div>
    </div>
    <div class="stat-card" style="--stat-color:#0D9488;--stat-bg:#CCFBF1">
        <div class="stat-icon">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg>
        </div>
        <div class="stat-info">
            <div class="stat-label">Patients</div>
            <div class="stat-value"><?= $patientCount ?></div>
            <div class="stat-trend" style="color:var(--teal)">Enregistres</div>
        </div>
    </div>
    <div class="stat-card" style="--stat-color:#7C3AED;--stat-bg:#EDE9FE">
        <div class="stat-icon">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg>
        </div>
        <div class="stat-info">
            <div class="stat-label">Medecins</div>
            <div class="stat-value"><?= $medecinCount ?></div>
            <div class="stat-trend" style="color:#7C3AED"><?= count($specialtyCounts) ?> specialites</div>
        </div>
    </div>
    <div class="stat-card" style="--stat-color:#D97706;--stat-bg:#FEF3C7">
        <div class="stat-icon">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 21V7l9-4 9 4v14"/><path d="M9 21V13h6v8"/></svg>
        </div>
        <div class="stat-info">
            <div class="stat-label">Hopitaux</div>
            <div class="stat-value"><?= $hopitalCount ?></div>
            <div class="stat-trend" style="color:var(--warning)"><?= $regionCount ?> regions</div>
        </div>
    </div>
</div>

<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-bottom:24px">
    <div class="card" style="padding:16px 20px;border-top:3px solid var(--success)">
        <div class="stat-label">Confirmes</div>
        <div class="stat-value" style="font-size:1.6rem;color:var(--success)"><?= (int) ($stats['confirmes'] ?? 0) ?></div>
    </div>
    <div class="card" style="padding:16px 20px;border-top:3px solid var(--warning)">
        <div class="stat-label">En attente</div>
        <div class="stat-value" style="font-size:1.6rem;color:var(--warning)"><?= (int) ($stats['en_attente'] ?? 0) ?></div>
    </div>
    <div class="card" style="padding:16px 20px;border-top:3px solid var(--danger)">
        <div class="stat-label">Annules</div>
        <div class="stat-value" style="font-size:1.6rem;color:var(--danger)"><?= (int) ($stats['annules'] ?? 0) ?></div>
    </div>
</div>

<div class="dashboard-grid">
    <div class="card full">
        <div class="card-header">
            <div>
                <div class="card-title">Rendez-vous recents</div>
                <div class="card-subtitle">5 derniers</div>
            </div>
            <a href="<?= $baseUrl ?>/rendezvous" class="btn btn-primary btn-sm">Voir tout →</a>
        </div>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Patient</th>
                        <th>Medecin</th>
                        <th>Specialite</th>
                        <th>Hopital</th>
                        <th>Date</th>
                        <th>Heure</th>
                        <th>Statut</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($recent)): ?>
                        <tr><td colspan="7"><div class="empty-state"><p>Aucun rendez-vous</p></div></td></tr>
                    <?php else: ?>
                        <?php foreach ($recent as $r): ?>
                            <tr>
                                <td>
                                    <div class="avatar-cell">
                                        <div class="avatar avatar-blue"><?= htmlspecialchars(mb_substr($r['patient_nom'] ?? '?', 0, 1)) ?></div>
                                        <span class="avatar-name"><?= htmlspecialchars($r['patient_nom'] ?? '—') ?></span>
                                    </div>
                                </td>
                                <td><?= htmlspecialchars($r['medecin_nom'] ?? '—') ?></td>
                                <td><span class="badge badge-teal"><?= htmlspecialchars($r['specialite'] ?? '—') ?></span></td>
                                <td><?= htmlspecialchars($r['hopital_nom'] ?? '—') ?></td>
                                <td><?= htmlspecialchars(formatDate($r['date_rdv'])) ?></td>
                                <td><span class="info-pill"><?= htmlspecialchars($r['heure']) ?></span></td>
                                <td><?= statusBadge($r['statut']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><div class="card-title">Actions rapides</div></div>
        <div class="quick-actions">
            <div class="quick-action" onclick="Router.navigate('rendezvous');setTimeout(()=>RendezVousModule.openNew(),100)">
                <div class="quick-action-icon" style="background:var(--accent-light);color:var(--accent)">📅</div>
                <div>
                    <div class="quick-action-label">Nouveau RDV</div>
                    <div class="quick-action-sub">Reserver</div>
                </div>
            </div>
            <div class="quick-action" onclick="Router.navigate('patients');setTimeout(()=>PatientsModule.openNew(),100)">
                <div class="quick-action-icon" style="background:var(--teal-light);color:var(--teal)">👤</div>
                <div>
                    <div class="quick-action-label">Ajouter patient</div>
                    <div class="quick-action-sub">Nouveau dossier</div>
                </div>
            </div>
            <div class="quick-action" onclick="Router.navigate('medecins')">
                <div class="quick-action-icon" style="background:#EDE9FE;color:#7C3AED">👨‍⚕️</div>
                <div>
                    <div class="quick-action-label">Medecins</div>
                    <div class="quick-action-sub">Consulter</div>
                </div>
            </div>
            <div class="quick-action" onclick="Router.navigate('hopitaux')">
                <div class="quick-action-icon" style="background:var(--warning-light);color:var(--warning)">🏥</div>
                <div>
                    <div class="quick-action-label">Hopitaux</div>
                    <div class="quick-action-sub">Voir la liste</div>
                </div>
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><div class="card-title">Medecins par specialite</div></div>
        <?php
        $max = max(array_values($specialtyCounts) ?: [1]);
        $colors = ['var(--accent)','var(--teal)','#7C3AED','var(--warning)','#EC4899'];
        $i = 0;
        foreach ($specialtyCounts as $spec => $cnt): ?>
            <div style="margin-bottom:12px">
                <div style="display:flex;justify-content:space-between;font-size:0.82rem;margin-bottom:4px">
                    <span style="font-weight:500"><?= htmlspecialchars($spec) ?></span>
                    <span style="color:var(--text-muted)"><?= $cnt ?></span>
                </div>
                <div class="progress-bar">
                    <div class="progress-fill" style="width:<?= ($cnt / $max) * 100 ?>%;background:<?= $colors[$i % count($colors)] ?>"></div>
                </div>
            </div>
            <?php $i++; ?>
        <?php endforeach; ?>
    </div>
</div>
