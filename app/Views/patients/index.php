<?php
// app/Views/patients/index.php
$baseUrl = '/medapp2/public';
?>

<div class="section-header">
    <div>
        <div class="section-title">Patients</div>
        <div class="section-subtitle"><?= count($patients) ?> patient(s)</div>
    </div>
    <button class="btn btn-primary" onclick="openPatientModal()">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
            <path d="M12 5v14M5 12h14"/>
        </svg>
        Nouveau patient
    </button>
</div>

<!-- INLINE MODAL (not using #modal-overlay global) -->
<div id="patient-modal-overlay" style="
    display:none; position:fixed; inset:0;
    background:rgba(6,15,30,0.55); backdrop-filter:blur(4px);
    z-index:9999; align-items:center; justify-content:center; padding:24px;
    overflow-y:auto;
" onclick="if(event.target===this) closePatientModal()">
    <div style="
        background:var(--bg-card); border-radius:var(--radius);
        box-shadow:0 20px 60px rgba(6,15,30,0.25);
        width:100%; max-width:560px; max-height:90vh;
        display:flex; flex-direction:column; overflow:hidden;
        animation:slideUp 0.2s ease;
    ">
        <div class="modal-header" style="display:flex;align-items:center;justify-content:space-between;padding:20px 24px 16px;border-bottom:1px solid var(--border)">
            <span class="modal-title" style="font-family:var(--font-display);font-size:1.05rem;font-weight:700;color:var(--navy)">
                Nouveau patient
            </span>
            <button class="modal-close" onclick="closePatientModal()" style="width:32px;height:32px;border-radius:6px;color:var(--text-muted);display:flex;align-items:center;justify-content:center;transition:all .18s ease;border:none;background:none;cursor:pointer">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M18 6 6 18M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <div style="padding:20px 24px;overflow-y:auto;flex:1">
            <form id="patient-form" method="POST" action="<?= $baseUrl ?>/patients/store" novalidate>
                <?php require ROOT_PATH . 'app/Views/patients/form_fields.php'; ?>
                <div class="form-actions">
                    <button type="button" class="btn btn-outline" onclick="closePatientModal()">Annuler</button>
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <?php foreach ($errors as $error): ?><p><?= htmlspecialchars($error) ?></p><?php endforeach; ?>
    </div>
<?php endif; ?>

<!-- table code unchanged ... -->

<script>
function openPatientModal() {
    const overlay = document.getElementById('patient-modal-overlay');
    overlay.style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closePatientModal() {
    const overlay = document.getElementById('patient-modal-overlay');
    overlay.style.display = 'none';
    document.body.style.overflow = '';
}

// Close on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closePatientModal();
});

<?php if (!empty($errors)): ?>
// Re-open modal if there were validation errors on submit
openPatientModal();
<?php endif; ?>
</script>