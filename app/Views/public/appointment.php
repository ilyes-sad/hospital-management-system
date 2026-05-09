<?php
// ============================================================
//  app/Views/public/appointment.php
//  Appointment confirmation - Professional Design
// ============================================================
$baseUrl = '/hospital-management-system-main/public';
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
        
        .main-content {
            margin-top: 100px;
            padding: 60px 32px;
            max-width: 700px;
            margin-left: auto;
            margin-right: auto;
        }
        
        .success-card {
            background: white;
            border-radius: 28px;
            padding: 48px;
            text-align: center;
            box-shadow: 0 10px 50px rgba(0,0,0,0.1);
        }
        
        .success-icon {
            width: 100px;
            height: 100px;
            margin: 0 auto 32px;
            background: linear-gradient(135deg, var(--success), #059669);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
        
        .success-icon i {
            font-size: 44px;
            color: white;
        }
        
        .success-card h1 {
            font-family: 'Outfit', sans-serif;
            font-size: 32px;
            font-weight: 700;
            color: var(--success);
            margin-bottom: 8px;
        }
        
        .success-card > p {
            color: var(--gray-500);
            font-size: 16px;
            margin-bottom: 32px;
        }
        
        .rdv-details {
            background: var(--gray-50);
            border-radius: 20px;
            padding: 28px;
            text-align: left;
            margin-bottom: 32px;
        }
        
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 16px 0;
            border-bottom: 1px solid var(--gray-200);
        }
        
        .detail-row:first-child { padding-top: 0; }
        .detail-row:last-child { padding-bottom: 0; border-bottom: none; }
        
        .detail-row .label {
            color: var(--gray-500);
            font-size: 15px;
        }
        
        .detail-row .value {
            font-weight: 600;
            color: var(--dark);
            font-size: 15px;
        }
        
        .status-badge {
            display: inline-block;
            padding: 8px 20px;
            border-radius: 24px;
            font-size: 14px;
            font-weight: 600;
        }
        
        .status-badge.en-attente { background: #fef3c7; color: #92400e; }
        
        .action-btns {
            display: flex;
            gap: 16px;
        }
        
        .btn {
            flex: 1;
            padding: 18px 28px;
            border-radius: 14px;
            font-weight: 600;
            font-size: 16px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: all 0.3s;
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
            border-color: var(--primary);
            color: var(--primary);
        }
        
        footer {
            background: var(--dark);
            color: white;
            padding: 40px;
            text-align: center;
            margin-top: 60px;
        }
        
        footer p { opacity: 0.7; font-size: 14px; }
        
        @media (max-width: 768px) {
            .header-inner { padding: 16px 20px; }
            .main-content { padding: 100px 20px 40px; }
            .success-card { padding: 32px 24px; }
            .action-btns { flex-direction: column; }
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
        </div>
    </header>

    <div class="main-content">
        <div class="success-card">
            <div class="success-icon">
                <i class="fas fa-check"></i>
            </div>
            <h1>Rendez-vous Confirmé!</h1>
            <p>Votre reservation a été enregistrée avec succes. Une confirmation vous sera envoyée par SMS.</p>
            
            <div class="rdv-details">
                <div class="detail-row">
                    <span class="label">Patient</span>
                    <span class="value"><?= htmlspecialchars(($patient['prenom'] ?? '') . ' ' . ($patient['nom'] ?? '')) ?></span>
                </div>
                <div class="detail-row">
                    <span class="label">Médecin</span>
                    <span class="value">Dr. <?= htmlspecialchars(($medecin['prenom'] ?? '') . ' ' . ($medecin['nom'] ?? '')) ?></span>
                </div>
                <div class="detail-row">
                    <span class="label">Spécialité</span>
                    <span class="value"><?= htmlspecialchars($medecin['specialite'] ?? '') ?></span>
                </div>
                <div class="detail-row">
                    <span class="label">Hôpital</span>
                    <span class="value"><?= htmlspecialchars($hopital['nom'] ?? '') ?></span>
                </div>
                <div class="detail-row">
                    <span class="label">Date</span>
                    <span class="value"><?= date('d/m/Y', strtotime($rdv['date_rdv'])) ?></span>
                </div>
                <div class="detail-row">
                    <span class="label">Heure</span>
                    <span class="value"><?= htmlspecialchars($rdv['heure']) ?></span>
                </div>
                <div class="detail-row">
                    <span class="label">Statut</span>
                    <span class="status-badge <?= $rdv['statut'] ?>"><?= htmlspecialchars($rdv['statut']) ?></span>
                </div>
            </div>
            
            <div class="action-btns">
                <a href="<?= $baseUrl ?>/public/portal" class="btn btn-primary">
                    <i class="fas fa-user"></i>
                    Mes RDV
                </a>
                <a href="<?= $baseUrl ?>/public" class="btn btn-outline">
                    <i class="fas fa-home"></i>
                    Accueil
                </a>
            </div>
        </div>
    </div>

    <footer>
        <p>&copy; 2024 MediCare - Tous droits réservés</p>
    </footer>
</body>
</html>