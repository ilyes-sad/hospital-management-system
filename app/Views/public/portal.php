<?php
// ============================================================
//  app/Views/public/portal.php
//  Patient portal - Professional Design
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
            padding: 60px 32px;
            max-width: 800px;
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
        
        .search-card {
            background: white;
            border-radius: 24px;
            padding: 48px;
            box-shadow: 0 4px 30px rgba(0,0,0,0.08);
        }
        
        .search-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 24px;
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .search-icon i {
            font-size: 32px;
            color: white;
        }
        
        .search-card h2 {
            font-family: 'Outfit', sans-serif;
            font-size: 24px;
            color: var(--dark);
            text-align: center;
            margin-bottom: 8px;
        }
        
        .search-card > p {
            color: var(--gray-500);
            text-align: center;
            margin-bottom: 32px;
        }
        
        .form-group { margin-bottom: 20px; }
        
        .form-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 8px;
            color: var(--gray-700);
        }
        
        .form-control {
            width: 100%;
            padding: 16px 20px;
            border: 2px solid var(--gray-200);
            border-radius: 14px;
            font-size: 16px;
            font-family: inherit;
            transition: all 0.3s;
            background: var(--gray-50);
        }
        
        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            background: white;
        }
        
        .btn {
            width: 100%;
            padding: 18px 28px;
            border-radius: 14px;
            font-weight: 600;
            font-size: 16px;
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
        
        .patient-info {
            background: white;
            border-radius: 24px;
            padding: 36px;
            box-shadow: 0 4px 30px rgba(0,0,0,0.08);
        }
        
        .patient-header {
            display: flex;
            align-items: center;
            gap: 20px;
            padding-bottom: 24px;
            margin-bottom: 24px;
            border-bottom: 2px solid var(--gray-100);
        }
        
        .patient-avatar {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, var(--accent), #6366f1);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 28px;
            font-weight: 700;
        }
        
        .patient-header h2 {
            font-family: 'Outfit', sans-serif;
            font-size: 28px;
            color: var(--dark);
        }
        
        .patient-header p {
            color: var(--gray-500);
            font-size: 15px;
        }
        
        .rdv-list { }
        
        .rdv-item {
            display: flex;
            gap: 20px;
            padding: 24px;
            background: var(--gray-50);
            border-radius: 16px;
            margin-bottom: 16px;
            transition: all 0.3s;
        }
        
        .rdv-item:hover {
            background: var(--gray-100);
        }
        
        .rdv-date {
            min-width: 80px;
            text-align: center;
        }
        
        .rdv-date .day {
            font-family: 'Outfit', sans-serif;
            font-size: 32px;
            font-weight: 700;
            color: var(--primary);
            line-height: 1;
        }
        
        .rdv-date .month {
            font-size: 14px;
            color: var(--gray-500);
            text-transform: uppercase;
        }
        
        .rdv-date .time {
            font-size: 14px;
            color: var(--accent);
            font-weight: 600;
            margin-top: 4px;
        }
        
        .rdv-info { flex: 1; }
        
        .rdv-info h3 {
            font-family: 'Outfit', sans-serif;
            font-size: 18px;
            color: var(--dark);
            margin-bottom: 4px;
        }
        
        .rdv-info p {
            color: var(--gray-500);
            font-size: 14px;
            margin-bottom: 2px;
        }
        
        .rdv-status {
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
            height: fit-content;
        }
        
        .rdv-status.en-attente { background: #fef3c7; color: #92400e; }
        .rdv-status.confirmé { background: #d1fae5; color: #065f46; }
        .rdv-status.annulé { background: #fee2e2; color: #991b1b; }
        .rdv-status.terminé { background: var(--gray-200); color: var(--gray-600); }
        
        .empty-state {
            text-align: center;
            padding: 40px;
            color: var(--gray-500);
        }
        
        .new-search {
            display: block;
            text-align: center;
            margin-top: 24px;
            padding: 14px 24px;
            background: var(--gray-100);
            color: var(--gray-700);
            border-radius: 12px;
            text-decoration: none;
            font-weight: 500;
        }
        
        .new-search:hover { background: var(--gray-200); }
        
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
            nav { display: none; }
            .main-content { padding: 100px 20px 40px; }
            .search-card { padding: 32px 24px; }
            .rdv-item { flex-direction: column; }
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
                <a href="<?= $baseUrl ?>/public/book">Rendez-vous</a>
                <a href="<?= $baseUrl ?>/public/portal" class="btn-nav" style="background: var(--primary-dark);">Espace Patient</a>
            </nav>
        </div>
    </header>

    <div class="main-content">
        <div class="page-header">
            <h1>Espace Patient</h1>
            <p>Consultiez vos rendez-vous</p>
        </div>

        <?php if (empty($patient)): ?>
            <div class="search-card">
                <div class="search-icon">
                    <i class="fas fa-user-circle"></i>
                </div>
                <h2>Vos Rendez-vous</h2>
                <p>Entrez votre CIN pour consulter vos rendez-vous</p>
                <form method="POST" action="<?= $baseUrl ?>/public/portal">
                    <div class="form-group">
                        <label>CIN</label>
                        <input type="text" name="cin" id="cin" class="form-control" value="<?= htmlspecialchars($old['cin'] ?? '') ?>" required placeholder="Ex: AB123456"/>
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i>
                        Rechercher
                    </button>
                </form>
            </div>
        <?php else: ?>
            <div class="patient-info">
                <div class="patient-header">
                    <div class="patient-avatar"><?= strtoupper(substr($patient['prenom'], 0, 1) . substr($patient['nom'], 0, 1)) ?></div>
                    <div>
                        <h2><?= htmlspecialchars($patient['prenom'] . ' ' . $patient['nom']) ?></h2>
                        <p>CIN: <?= htmlspecialchars($patient['cin']) ?> | <?= htmlspecialchars($patient['telephone'] ?? '') ?></p>
                    </div>
                </div>
                
                <h3 style="font-family: 'Outfit', sans-serif; font-size: 18px; margin-bottom: 16px; color: var(--dark);">Historique des RDV</h3>
                
                <?php if (empty($rdvs)): ?>
                    <div class="empty-state">
                        <i class="fas fa-calendar-times" style="font-size: 40px; margin-bottom: 16px;"></i>
                        <p>Aucun rendez-vous trouvé</p>
                    </div>
                <?php else: ?>
                    <div class="rdv-list">
                        <?php foreach ($rdvs as $rdv): ?>
                            <div class="rdv-item">
                                <div class="rdv-date">
                                    <div class="day"><?= date('d', strtotime($rdv['date_rdv'])) ?></div>
                                    <div class="month"><?= date('M', strtotime($rdv['date_rdv'])) ?></div>
                                    <div class="time"><?= htmlspecialchars($rdv['heure']) ?></div>
                                </div>
                                <div class="rdv-info">
                                    <h3>Dr. <?= htmlspecialchars($rdv['medecin_nom']) ?></h3>
                                    <p><?= htmlspecialchars($rdv['specialite']) ?></p>
                                    <p><i class="fas fa-hospital"></i> <?= htmlspecialchars($rdv['hopital_nom']) ?> - <?= htmlspecialchars($rdv['hopital_ville']) ?></p>
                                </div>
                                <span class="rdv-status <?= $rdv['statut'] ?>"><?= htmlspecialchars($rdv['statut']) ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                
                <a href="<?= $baseUrl ?>/public/portal" class="new-search">
                    <i class="fas fa-search"></i> Nouvelle recherche
                </a>
            </div>
        <?php endif; ?>
    </div>

    <footer>
        <p>&copy; 2024 MediCare - Tous droits réservés</p>
    </footer>
</body>
</html>