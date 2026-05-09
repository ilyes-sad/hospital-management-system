<?php
// ============================================================
//  app/Views/public/book.php
//  Public appointment booking - Professional Design
// ============================================================
$baseUrl = '/hospital-management-system-main/public';
$errors = $errors ?? [];
$old = $old ?? [];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title><?= $pageTitle ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com"/>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
    <style>
        :root {
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
            --primary-light: #3b82f6;
            --accent: #3b82f6;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --dark: #0f172a;
            --gray-900: #111827;
            --gray-700: #374151;
            --gray-500: #6b7280;
            --gray-300: #d1d5db;
            --gray-100: #f3f4f6;
            --white: #ffffff;
            --bg: #f0f9ff;
        }
        
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--bg);
            color: var(--gray-700);
        }
        
        header {
            background: rgba(255,255,255,0.95);
            backdrop-filter: blur(20px);
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }
        
        .header-inner {
            max-width: 1200px;
            margin: 0 auto;
            padding: 16px 32px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .logo {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
        }
        
        .logo-icon {
            width: 48px;
            height: 48px;
            background: var(--primary);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .logo-icon i { color: white; font-size: 22px; }
        
        .logo-text {
            font-family: 'Outfit', sans-serif;
            font-size: 24px;
            font-weight: 700;
            color: var(--dark);
        }
        
        .logo-text span { color: var(--primary); }
        
        nav { display: flex; gap: 24px; }
        
        nav a {
            color: var(--gray-700);
            text-decoration: none;
            font-weight: 500;
            padding: 8px 14px;
            border-radius: 8px;
        }
        
        nav a:hover { color: var(--primary); background: rgba(15,118,110,0.08); }
        
        .btn-nav {
            background: var(--primary);
            color: white !important;
            padding: 10px 20px;
            border-radius: 10px;
        }
        
        .main-content {
            margin-top: 100px;
            padding: 40px 32px;
            max-width: 900px;
            margin-left: auto;
            margin-right: auto;
        }
        
        .page-header {
            text-align: center;
            margin-bottom: 40px;
        }
        
        .page-header h1 {
            font-family: 'Outfit', sans-serif;
            font-size: 36px;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 8px;
        }
        
        .page-header p { color: var(--gray-500); }
        
        .booking-card {
            background: white;
            border-radius: 24px;
            box-shadow: 0 4px 30px rgba(0,0,0,0.08);
            overflow: hidden;
        }
        
        .steps-indicator {
            display: flex;
            background: var(--gray-100);
            padding: 16px 32px;
            gap: 8px;
        }
        
        .step {
            flex: 1;
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 500;
            color: var(--gray-500);
            background: transparent;
        }
        
        .step.active {
            background: white;
            color: var(--primary);
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }
        
        .step.completed {
            background: var(--primary);
            color: white;
        }
        
        .step-num {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: currentColor;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            font-weight: 700;
        }
        
        .step.completed .step-num { background: white; color: var(--primary); }
        
        .form-content {
            padding: 40px;
        }
        
        .form-section {
            margin-bottom: 36px;
        }
        
        .form-section-title {
            font-family: 'Outfit', sans-serif;
            font-size: 18px;
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .form-section-title i {
            width: 36px;
            height: 36px;
            background: rgba(15, 118, 110, 0.1);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary);
        }
        
        .form-section-title::after {
            content: '';
            flex: 1;
            height: 1px;
            background: var(--gray-100);
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .form-group { }
        
        .form-group.full { grid-column: 1 / -1; }
        
        .form-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 8px;
            color: var(--gray-700);
            font-size: 14px;
        }
        
        .form-group label .required { color: var(--danger); }
        
        .form-control {
            width: 100%;
            padding: 14px 18px;
            border: 1px solid var(--gray-300);
            border-radius: 12px;
            font-size: 15px;
            font-family: inherit;
            transition: all 0.3s;
            background: white;
        }
        
        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(15, 118, 110, 0.1);
        }
        
        .form-control.error {
            border-color: var(--danger);
        }
        
        select.form-control { cursor: pointer; }
        
        select.form-control:disabled {
            background: var(--gray-100);
            cursor: not-allowed;
        }
        
        .field-error {
            color: var(--danger);
            font-size: 13px;
            margin-top: 6px;
        }
        
        .slots-section { margin-top: 16px; }
        
        .slots-label {
            font-weight: 600;
            margin-bottom: 12px;
            color: var(--gray-700);
            display: block;
        }
        
        .slots-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(90px, 1fr));
            gap: 10px;
        }
        
        .slot-btn {
            padding: 14px 12px;
            text-align: center;
            border: 2px solid var(--gray-200);
            border-radius: 12px;
            background: white;
            cursor: pointer;
            transition: all 0.2s;
            font-family: inherit;
            font-size: 14px;
            font-weight: 500;
            color: var(--gray-700);
        }
        
        .slot-btn:hover:not(:disabled) {
            border-color: var(--primary);
            color: var(--primary);
        }
        
        .slot-btn.selected {
            background: var(--primary);
            border-color: var(--primary);
            color: white;
        }
        
        .slot-btn.busy {
            background: var(--gray-100);
            border-color: var(--gray-100);
            color: var(--gray-400);
            cursor: not-allowed;
            text-decoration: line-through;
        }
        
        .slots-note {
            font-size: 13px;
            color: var(--gray-500);
            margin-top: 12px;
        }
        
        .form-actions {
            display: flex;
            gap: 16px;
            margin-top: 36px;
            padding-top: 24px;
            border-top: 1px solid var(--gray-100);
        }
        
        .btn {
            flex: 1;
            padding: 16px 28px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 15px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
        }
        
        .btn-primary {
            background: var(--primary);
            color: white;
        }
        
        .btn-primary:hover {
            background: var(--primary-dark);
        }
        
        .btn-outline {
            background: transparent;
            border: 2px solid var(--gray-300);
            color: var(--gray-700);
        }
        
        .btn-outline:hover {
            border-color: var(--gray-400);
        }
        
        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: var(--gray-500);
            text-decoration: none;
            font-size: 14px;
        }
        
        .back-link:hover { color: var(--primary); }
        
        @media (max-width: 768px) {
            .header-inner { padding: 16px 20px; }
            nav { display: none; }
            .main-content { padding: 100px 20px 40px; }
            .form-row { grid-template-columns: 1fr; }
            .steps-indicator { display: none; }
            .form-content { padding: 24px; }
        }
    </style>
</head>
<body>
    <header>
        <div class="header-inner">
            <a href="<?= $baseUrl ?>/public" class="logo">
                <div class="logo-icon"><i class="fas fa-hospital-user"></i></div>
                <div class="logo-text">Medi<span>Care</span></div>
            </a>
            <nav>
                <a href="<?= $baseUrl ?>/public">Accueil</a>
                <a href="<?= $baseUrl ?>/public/doctors">Médecins</a>
                <a href="<?= $baseUrl ?>/public/book" style="color: var(--primary);">Rendez-vous</a>
                <a href="<?= $baseUrl ?>/public/portal" class="btn-nav">Espace Patient</a>
            </nav>
        </div>
    </header>

    <div class="main-content">
        <div class="page-header">
            <h1>Prendre un Rendez-vous</h1>
            <p>Remplissez le formulaire ci-dessous pour réserver votre consultation</p>
        </div>

        <div class="booking-card">
            <div class="steps-indicator">
                <div class="step active">
                    <span class="step-num">1</span>
                    Informations
                </div>
                <div class="step">
                    <span class="step-num">2</span>
                    Médecin
                </div>
                <div class="step">
                    <span class="step-num">3</span>
                    Horaire
                </div>
            </div>

            <form method="POST" action="<?= $baseUrl ?>/public/book" novalidate id="booking-form">
                <?php if (!empty($error)): ?>
                    <div style="background:#fee2e2;color:#dc2626;padding:16px;border-radius:8px;margin-bottom:20px;display:flex;align-items:center;gap:12px">
                        <i class="fas fa-exclamation-circle" style="font-size:20px"></i>
                        <span><?= htmlspecialchars($error) ?></span>
                    </div>
                <?php endif; ?>
                <div class="form-content">
                    <input type="hidden" name="medecin_id" id="medecin_id" value="<?= htmlspecialchars($old['medecin_id'] ?? ($selectedMedecin['id'] ?? '')) ?>"/>
                    <input type="hidden" name="hopital_id" id="hopital_id" value="<?= htmlspecialchars($old['hopital_id'] ?? ($selectedMedecin['hopital_id'] ?? '')) ?>"/>
                    <input type="hidden" name="heure" id="selected-heure" value="<?= htmlspecialchars($old['heure'] ?? '') ?>"/>

                    <div class="form-section">
                        <div class="form-section-title">
                            <i class="fas fa-user"></i>
                            Vos Informations
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Nom <span class="required">*</span></label>
                                <input type="text" name="patient_nom" id="patient_nom" class="form-control" value="<?= htmlspecialchars($old['patient_nom'] ?? '') ?>" required placeholder="Votre nom"/>
                            </div>
                            <div class="form-group">
                                <label>Prénom <span class="required">*</span></label>
                                <input type="text" name="patient_prenom" id="patient_prenom" class="form-control" value="<?= htmlspecialchars($old['patient_prenom'] ?? '') ?>" required placeholder="Votre prénom"/>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label>CIN <span class="required">*</span></label>
                                <input type="text" name="patient_cin" id="patient_cin" class="form-control" value="<?= htmlspecialchars($old['patient_cin'] ?? '') ?>" required placeholder="Ex: 12345678" minlength="8" maxlength="8" title="Le CIN doit contenir exactement 8 chiffres"/>
                            </div>
                            <div class="form-group">
                                <label>Téléphone <span class="required">*</span></label>
                                <input type="tel" name="patient_telephone" id="patient_telephone" class="form-control" value="<?= htmlspecialchars($old['patient_telephone'] ?? '') ?>" required placeholder="0612 345 678"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="patient_email" id="patient_email" class="form-control" value="<?= htmlspecialchars($old['patient_email'] ?? '') ?>" placeholder="email@exemple.com"/>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Date de naissance</label>
                                <input type="date" name="patient_date_naissance" id="patient_date_naissance" class="form-control" value="<?= htmlspecialchars($old['patient_date_naissance'] ?? '') ?>"/>
                            </div>
                            <div class="form-group">
                                <label>Sexe</label>
                                <select name="patient_sexe" id="patient_sexe" class="form-control">
                                    <option value="">Sélectionner...</option>
                                    <option value="M" <?= ($old['patient_sexe'] ?? '') === 'M' ? 'selected' : '' ?>>Masculin</option>
                                    <option value="F" <?= ($old['patient_sexe'] ?? '') === 'F' ? 'selected' : '' ?>>Féminin</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Ville</label>
                                <input type="text" name="patient_ville" id="patient_ville" class="form-control" value="<?= htmlspecialchars($old['patient_ville'] ?? '') ?>" placeholder="Votre ville"/>
                            </div>
                            <div class="form-group">
                                <label>Groupe sanguin</label>
                                <select name="patient_groupe_sanguin" id="patient_groupe_sanguin" class="form-control">
                                    <option value="">Sélectionner...</option>
                                    <?php foreach (['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'] as $gs): ?>
                                        <option value="<?= $gs ?>" <?= ($old['patient_groupe_sanguin'] ?? '') === $gs ? 'selected' : '' ?>><?= $gs ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <div class="form-section-title">
                            <i class="fas fa-user-md"></i>
                            Médecin & Spécialité
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Spécialité <span class="required">*</span></label>
                                <select name="specialite" id="specialte-select" class="form-control" required>
                                    <option value="">Sélectionner...</option>
                                    <?php foreach ($specialites ?? [] as $s): ?>
                                        <option value="<?= htmlspecialchars($s) ?>"><?= htmlspecialchars($s) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Médecin <span class="required">*</span></label>
                                <select name="medecin_select" id="medecin-select" class="form-control" required disabled>
                                    <option value="">Sélectionner...</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <div class="form-section-title">
                            <i class="fas fa-calendar"></i>
                            Date & Horaire
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Date <span class="required">*</span></label>
                                <input type="date" name="date_rdv" id="date-rdv" class="form-control" value="<?= htmlspecialchars($old['date_rdv'] ?? '') ?>" required min="<?= date('Y-m-d') ?>"/>
                            </div>
                        </div>
                        <div class="slots-section">
                            <label class="slots-label">Horaire disponible <span class="required">*</span></label>
                            <div class="slots-grid" id="slots-container">
                                <div style="grid-column:1/-1;color:var(--gray-500);font-size:14px;">Sélectionnez un médecin et une date pour voir les horaires.</div>
                            </div>
                            <p class="slots-note"><i class="fas fa-info-circle"></i> Les créneaux barrés sont déjà réservés</p>
                        </div>
                    </div>

                    <div class="form-section">
                        <div class="form-section-title">
                            <i class="fas fa-comment"></i>
                            Motif de Consultation
                        </div>
                        <div class="form-group">
                            <textarea name="motif" id="motif" class="form-control" rows="3" placeholder="Décrivez brièvement votre motif de consultation..."><?= htmlspecialchars($old['motif'] ?? '') ?></textarea>
                        </div>
                    </div>

                    <div class="form-actions">
                        <a href="<?= $baseUrl ?>/public" class="btn btn-outline">Annuler</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-check"></i>
                            Confirmer le RDV
                        </button>
                    </div>
                </div>
            </form>
        </div>
        
        <a href="<?= $baseUrl ?>/public" class="back-link">← Retour à l'accueil</a>
    </div>

    <script>
    const BASE_URL = '<?= $baseUrl ?>';
    const ALL_SLOTS = ['08:00','09:00','10:00','11:00','12:00','13:00','14:00','15:00','16:00','17:00','18:00'];
    let medecinsData = [];

    async function loadMedecins() {
        const spec = document.getElementById('specialte-select').value;
        if (!spec) return;
        const res = await fetch(`${BASE_URL}/api/public/doctors?specialite=${encodeURIComponent(spec)}`);
        const data = await res.json();
        medecinsData = data.data || [];
        const select = document.getElementById('medecin-select');
        select.innerHTML = '<option value="">Sélectionner...</option>';
        medecinsData.forEach(m => {
            select.innerHTML += `<option value="${m.id}" data-hopital="${m.hopital_id}">Dr. ${m.prenom} ${m.nom}</option>`;
        });
        select.disabled = false;
    }

    async function loadSlots() {
        const medecinId = document.getElementById('medecin_id').value;
        const date = document.getElementById('date-rdv').value;
        const container = document.getElementById('slots-container');
        
        if (!medecinId || !date) {
            container.innerHTML = '<div style="grid-column:1/-1;color:var(--gray-500);font-size:14px;">Sélectionnez un médecin et une date pour voir les horaires.</div>';
            return;
        }
        
        container.innerHTML = '<div style="grid-column:1/-1;">Chargement...</div>';
        
        try {
            const res = await fetch(`${BASE_URL}/api/public/busy-slots?medecin_id=${medecinId}&date=${date}`);
            const data = await res.json();
            const busySlots = data.data || [];
            
            container.innerHTML = ALL_SLOTS.map(h => {
                const isBusy = busySlots.includes(h);
                return `<button type="button" class="slot-btn ${isBusy ? 'busy' : ''}" data-heure="${h}" ${isBusy ? 'disabled' : ''}>${h}</button>`;
            }).join('');
            
            container.querySelectorAll('.slot-btn:not(.busy)').forEach(btn => {
                btn.onclick = () => {
                    container.querySelectorAll('.slot-btn').forEach(b => b.classList.remove('selected'));
                    btn.classList.add('selected');
                    document.getElementById('selected-heure').value = btn.dataset.heure;
                };
            });
        } catch(e) {
            container.innerHTML = '<div style="grid-column:1/-1;color:var(--danger);">Erreur chargement</div>';
        }
    }

    document.getElementById('specialte-select').addEventListener('change', loadMedecins);
    document.getElementById('medecin-select').addEventListener('change', (e) => {
        const opt = e.target.options[e.target.selectedIndex];
        document.getElementById('medecin_id').value = e.target.value;
        document.getElementById('hopital_id').value = opt.dataset.hopital || '';
        loadSlots();
    });
    document.getElementById('date-rdv').addEventListener('change', loadSlots);

    <?php if (!empty($selectedMedecin)): ?>
    (async () => {
        const res = await fetch(`${BASE_URL}/api/public/doctors?specialite=${encodeURIComponent('<?= addslashes($selectedMedecin['specialite'] ?? '') ?>')}`);
        const data = await res.json();
        medecinsData = data.data || [];
        const select = document.getElementById('medecin-select');
        select.innerHTML = '<option value="">Sélectionner...</option>';
        medecinsData.forEach(m => {
            select.innerHTML += `<option value="${m.id}" data-hopital="${m.hopital_id}">Dr. ${m.prenom} ${m.nom}</option>`;
        });
        select.disabled = false;
        select.value = '<?= $selectedMedecin['id'] ?>';
        document.getElementById('medecin_id').value = '<?= $selectedMedecin['id'] ?>';
        document.getElementById('hopital_id').value = '<?= $selectedMedecin['hopital_id'] ?>';
        document.getElementById('specialte-select').value = '<?= addslashes($selectedMedecin['specialite'] ?? '') ?>';
    })();
    <?php endif; ?>
    </script>
</body>
</html>