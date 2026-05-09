<?php
// ============================================================
//  app/Views/public/doctors.php
//  Public doctors listing - Professional Design
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
            --accent-light: #38bdf8;
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
            --bg: #f8fafc;
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
            max-width: 1400px;
            margin: 0 auto;
            padding: 16px 40px;
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
        
        nav { display: flex; align-items: center; gap: 32px; }
        
        nav a {
            color: var(--gray-700);
            text-decoration: none;
            font-weight: 500;
            font-size: 15px;
            padding: 8px 16px;
            border-radius: 8px;
            transition: all 0.3s;
        }
        
        nav a:hover { color: var(--primary); background: rgba(15, 118, 110, 0.08); }
        
        .btn-nav {
            background: var(--primary);
            color: white !important;
            padding: 10px 20px;
            border-radius: 10px;
        }
        
        .btn-nav:hover { background: var(--primary-dark); }
        
        .main-content {
            margin-top: 100px;
            padding: 60px 40px;
            max-width: 1400px;
            margin-left: auto;
            margin-right: auto;
        }
        
        .page-header {
            text-align: center;
            margin-bottom: 48px;
        }
        
        .page-header h1 {
            font-family: 'Outfit', sans-serif;
            font-size: 44px;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 12px;
        }
        
        .page-header p {
            color: var(--gray-500);
            font-size: 18px;
        }
        
        .filters-card {
            background: white;
            border-radius: 20px;
            padding: 24px 32px;
            margin-bottom: 40px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .filters-row {
            display: flex;
            gap: 20px;
            align-items: end;
            flex-wrap: wrap;
        }
        
        .filter-group { flex: 1; min-width: 200px; }
        
        .filter-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 8px;
            color: var(--gray-700);
            font-size: 14px;
        }
        
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
        
        select.form-control { cursor: pointer; }
        
        .btn {
            padding: 14px 28px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 15px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s;
            border: none;
            cursor: pointer;
        }
        
        .btn-primary {
            background: var(--primary);
            color: white;
        }
        
        .btn-primary:hover { background: var(--primary-dark); }
        
        .btn-secondary {
            background: var(--gray-100);
            color: var(--gray-700);
        }
        
        .btn-secondary:hover { background: var(--gray-200); }
        
        .doctors-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(360px, 1fr));
            gap: 28px;
        }
        
        .doctor-card {
            background: white;
            border-radius: 20px;
            padding: 28px;
            box-shadow: 0 2px 15px rgba(0,0,0,0.04);
            transition: all 0.3s;
            border: 1px solid var(--gray-100);
        }
        
        .doctor-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.1);
        }
        
        .doctor-card .card-header {
            display: flex;
            align-items: center;
            gap: 18px;
            margin-bottom: 20px;
            padding-bottom: 20px;
            border-bottom: 1px solid var(--gray-100);
        }
        
        .doctor-card .avatar {
            width: 72px;
            height: 72px;
            background: linear-gradient(135deg, var(--accent), var(--accent-light));
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
            font-weight: 700;
        }
        
        .doctor-card .info h3 {
            font-family: 'Outfit', sans-serif;
            font-size: 20px;
            color: var(--dark);
            margin-bottom: 4px;
        }
        
        .doctor-card .specialty {
            color: var(--primary);
            font-weight: 600;
            font-size: 14px;
        }
        
        .doctor-card .details {
            margin-bottom: 20px;
        }
        
        .doctor-card .detail-row {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 0;
            color: var(--gray-500);
            font-size: 14px;
        }
        
        .doctor-card .detail-row i {
            width: 20px;
            color: var(--primary);
        }
        
        .doctor-card .actions {
            display: flex;
            gap: 12px;
        }
        
        .doctor-card .btn {
            flex: 1;
            justify-content: center;
            padding: 14px;
        }
        
        .empty-state {
            text-align: center;
            padding: 80px 20px;
            background: white;
            border-radius: 20px;
        }
        
        .empty-state i {
            font-size: 60px;
            color: var(--gray-300);
            margin-bottom: 20px;
        }
        
        .empty-state h3 {
            font-family: 'Outfit', sans-serif;
            font-size: 24px;
            color: var(--gray-700);
            margin-bottom: 8px;
        }
        
        .empty-state p { color: var(--gray-500); }
        
        footer {
            background: var(--dark);
            color: white;
            padding: 40px;
            text-align: center;
            margin-top: 60px;
        }
        
        footer p {
            opacity: 0.7;
            font-size: 14px;
        }
        
        @media (max-width: 768px) {
            .header-inner { padding: 16px 20px; }
            nav { display: none; }
            .main-content { padding: 40px 20px; }
            .filters-row { flex-direction: column; }
            .filter-group { width: 100%; }
            .doctors-grid { grid-template-columns: 1fr; }
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
                <a href="<?= $baseUrl ?>/public/doctors" style="color: var(--primary);">Médecins</a>
                <a href="<?= $baseUrl ?>/public/book">Rendez-vous</a>
                <a href="<?= $baseUrl ?>/public/portal" class="btn-nav">Espace Patient</a>
            </nav>
        </div>
    </header>

    <div class="main-content">
        <div class="page-header">
            <h1>Nos Médecins Spécialistes</h1>
            <p>Choisissez le médecin qui correspond à vos besoins</p>
        </div>

        <div class="filters-card">
            <form method="GET" action="<?= $baseUrl ?>/public/doctors" class="filters-row">
                <div class="filter-group">
                    <label><i class="fas fa-stethoscope"></i> Spécialité</label>
                    <select name="specialite" class="form-control">
                        <option value="">Toutes les spécialités</option>
                        <?php foreach ($specialites ?? [] as $s): ?>
                            <option value="<?= htmlspecialchars($s) ?>" <?= ($selectedSpecialite ?? '') === $s ? 'selected' : '' ?>><?= htmlspecialchars($s) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="filter-group">
                    <label><i class="fas fa-hospital"></i> Hôpital</label>
                    <select name="hopital" class="form-control">
                        <option value="">Tous les hôpitaux</option>
                        <?php foreach ($hopitaux ?? [] as $h): ?>
                            <option value="<?= $h['id'] ?>" <?= ($selectedHopital ?? '') == $h['id'] ? 'selected' : '' ?>><?= htmlspecialchars($h['nom']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-filter"></i> Filtrer
                </button>
                <a href="<?= $baseUrl ?>/public/doctors" class="btn btn-secondary">
                    <i class="fas fa-redo"></i> Réinitialiser
                </a>
            </form>
        </div>

        <?php if (empty($medecins)): ?>
            <div class="empty-state">
                <i class="fas fa-user-md"></i>
                <h3>Aucun médecin trouvé</h3>
                <p>Essayez avec d'autres critères de recherche</p>
            </div>
        <?php else: ?>
            <div class="doctors-grid">
                <?php foreach ($medecins as $m): ?>
                    <div class="doctor-card">
                        <div class="card-header">
                            <div class="avatar"><?= strtoupper(substr($m['prenom'], 0, 1) . substr($m['nom'], 0, 1)) ?></div>
                            <div class="info">
                                <h3>Dr. <?= htmlspecialchars($m['prenom'] . ' ' . $m['nom']) ?></h3>
                                <div class="specialty"><?= htmlspecialchars($m['specialite']) ?></div>
                            </div>
                        </div>
                        <div class="details">
                            <div class="detail-row">
                                <i class="fas fa-hospital"></i>
                                <?= htmlspecialchars($m['hopital_nom'] ?? '') ?>
                            </div>
                            <div class="detail-row">
                                <i class="fas fa-map-marker-alt"></i>
                                <?= htmlspecialchars($m['hopital_ville'] ?? '') ?>
                            </div>
                            <?php if (!empty($m['telephone'])): ?>
                            <div class="detail-row">
                                <i class="fas fa-phone"></i>
                                <?= htmlspecialchars($m['telephone']) ?>
                            </div>
                            <?php endif; ?>
                        </div>
                        <div class="actions">
                            <a href="<?= $baseUrl ?>/public/book?medecin=<?= $m['id'] ?>" class="btn btn-primary">
                                <i class="fas fa-calendar-plus"></i> RDV
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <footer>
        <p>&copy; 2024 MediCare - Tous droits réservés</p>
    </footer>
</body>
</html>