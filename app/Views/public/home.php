<?php
// ============================================================
//  app/Views/public/home.php
//  Public home page - Professional Hospital Design
// ============================================================
$baseUrl = '/hospital-management-system-main/public';
$medecins = $medecins ?? [];
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
            --bg-gradient: linear-gradient(135deg, #1d4ed8 0%, #1e3a8a 50%, #0f172a 100%);
        }
        
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: var(--bg);
            color: var(--gray-700);
            line-height: 1.6;
        }
        
        /* Header */
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
        
        .logo-icon i {
            color: white;
            font-size: 22px;
        }
        
        .logo-text {
            font-family: 'Outfit', sans-serif;
            font-size: 24px;
            font-weight: 700;
            color: var(--dark);
        }
        
        .logo-text span {
            color: var(--primary);
        }
        
        nav {
            display: flex;
            align-items: center;
            gap: 32px;
        }
        
        nav a {
            color: var(--gray-700);
            text-decoration: none;
            font-weight: 500;
            font-size: 15px;
            padding: 8px 16px;
            border-radius: 8px;
            transition: all 0.3s;
        }
        
        nav a:hover {
            color: var(--primary);
            background: rgba(15, 118, 110, 0.08);
        }
        
        .btn-nav {
            background: var(--primary);
            color: white !important;
            padding: 10px 20px;
            border-radius: 10px;
        }
        
        .btn-nav:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
        }
        
        /* Hero Section */
        .hero {
            margin-top: 80px;
            background: var(--bg-gradient);
            padding: 100px 40px;
            position: relative;
            overflow: hidden;
        }
        
        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='%23ffffff' fill-opacity='0.03'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/svg%3E");
        }
        
        .hero-content {
            max-width: 1400px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 80px;
            align-items: center;
            position: relative;
        }
        
        .hero-text h1 {
            font-family: 'Outfit', sans-serif;
            font-size: 56px;
            font-weight: 800;
            color: white;
            line-height: 1.1;
            margin-bottom: 24px;
        }
        
        .hero-text p {
            font-size: 18px;
            color: rgba(255,255,255,0.85);
            margin-bottom: 40px;
            max-width: 500px;
        }
        
        .hero-btns {
            display: flex;
            gap: 16px;
            flex-wrap: wrap;
        }
        
        .btn {
            padding: 16px 32px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 16px;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s;
        }
        
        .btn-primary {
            background: var(--primary-light);
            color: var(--dark);
        }
        
        .btn-primary:hover {
            background: var(--white);
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
        
        .btn-outline {
            background: transparent;
            color: white;
            border: 2px solid rgba(255,255,255,0.3);
        }
        
        .btn-outline:hover {
            background: rgba(255,255,255,0.1);
            border-color: white;
        }
        
        .hero-visual {
            position: relative;
        }
        
        .hero-card {
            background: white;
            border-radius: 24px;
            padding: 40px;
            box-shadow: 0 25px 80px rgba(0,0,0,0.3);
        }
        
        .hero-card-header {
            display: flex;
            align-items: center;
            gap: 16px;
            margin-bottom: 24px;
            padding-bottom: 24px;
            border-bottom: 1px solid var(--gray-100);
        }
        
        .hero-avatar {
            width: 64px;
            height: 64px;
            background: linear-gradient(135deg, var(--primary), var(--primary-light));
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
            font-weight: 700;
        }
        
        .hero-card h3 {
            font-family: 'Outfit', sans-serif;
            font-size: 20px;
            color: var(--dark);
        }
        
        .hero-card .specialty {
            color: var(--primary);
            font-weight: 500;
            font-size: 14px;
        }
        
        .hero-stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
        }
        
        .stat-item {
            text-align: center;
            padding: 16px;
            background: var(--gray-100);
            border-radius: 12px;
        }
        
        .stat-item .number {
            font-family: 'Outfit', sans-serif;
            font-size: 28px;
            font-weight: 700;
            color: var(--primary);
        }
        
        .stat-item .label {
            font-size: 13px;
            color: var(--gray-500);
        }
        
        /* Stats Bar */
        .stats-bar {
            background: white;
            padding: 40px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
        }
        
        .stats-inner {
            max-width: 1400px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 40px;
            text-align: center;
        }
        
        .stat-box i {
            font-size: 32px;
            color: var(--primary);
            margin-bottom: 12px;
        }
        
        .stat-box .num {
            font-family: 'Outfit', sans-serif;
            font-size: 36px;
            font-weight: 700;
            color: var(--dark);
        }
        
        .stat-box .txt {
            color: var(--gray-500);
            font-size: 14px;
        }
        
        /* Section */
        .section {
            padding: 80px 40px;
        }
        
        .section-inner {
            max-width: 1400px;
            margin: 0 auto;
        }
        
        .section-header {
            text-align: center;
            margin-bottom: 60px;
        }
        
        .section-header h2 {
            font-family: 'Outfit', sans-serif;
            font-size: 40px;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 16px;
        }
        
        .section-header p {
            color: var(--gray-500);
            font-size: 18px;
            max-width: 600px;
            margin: 0 auto;
        }
        
        /* Doctors Grid */
        .doctors-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 32px;
        }
        
        .doctor-card {
            background: white;
            border-radius: 20px;
            padding: 32px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
            transition: all 0.3s;
            border: 1px solid var(--gray-100);
        }
        
        .doctor-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 50px rgba(0,0,0,0.1);
        }
        
        .doctor-card .top {
            display: flex;
            align-items: center;
            gap: 16px;
            margin-bottom: 20px;
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
        
        .doctor-card h3 {
            font-family: 'Outfit', sans-serif;
            font-size: 20px;
            color: var(--dark);
        }
        
        .doctor-card .spec {
            color: var(--primary);
            font-weight: 600;
            font-size: 14px;
        }
        
        .doctor-card .hospital {
            color: var(--gray-500);
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 20px;
        }
        
        .doctor-card .bottom {
            display: flex;
            gap: 12px;
        }
        
        .doctor-card .btn {
            flex: 1;
            justify-content: center;
            padding: 14px;
            font-size: 14px;
        }
        
        .btn-book {
            background: var(--primary);
            color: white;
        }
        
        .btn-book:hover {
            background: var(--primary-dark);
        }
        
        .btn-view {
            background: var(--gray-100);
            color: var(--gray-700);
        }
        
        .btn-view:hover {
            background: var(--gray-200);
        }
        
        /* Features */
        .features {
            background: linear-gradient(180deg, var(--white) 0%, var(--gray-100) 100%);
        }
        
        .features-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 32px;
        }
        
        .feature-card {
            background: white;
            border-radius: 20px;
            padding: 40px;
            text-align: center;
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
        }
        
        .feature-card .icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 24px;
            background: linear-gradient(135deg, rgba(15, 118, 110, 0.1), rgba(20, 184, 166, 0.1));
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .feature-card .icon i {
            font-size: 32px;
            color: var(--primary);
        }
        
        .feature-card h3 {
            font-family: 'Outfit', sans-serif;
            font-size: 20px;
            color: var(--dark);
            margin-bottom: 12px;
        }
        
        .feature-card p {
            color: var(--gray-500);
            line-height: 1.7;
        }
        
        /* CTA */
        .cta {
            background: var(--bg-gradient);
            padding: 80px 40px;
            text-align: center;
        }
        
        .cta h2 {
            font-family: 'Outfit', sans-serif;
            font-size: 40px;
            font-weight: 700;
            color: white;
            margin-bottom: 16px;
        }
        
        .cta p {
            color: rgba(255,255,255,0.8);
            font-size: 18px;
            margin-bottom: 32px;
        }
        
        /* Footer */
        footer {
            background: var(--dark);
            color: white;
            padding: 60px 40px 30px;
        }
        
        .footer-inner {
            max-width: 1400px;
            margin: 0 auto;
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr;
            gap: 60px;
            margin-bottom: 40px;
        }
        
        .footer-brand p {
            color: var(--gray-300);
            margin-top: 16px;
            max-width: 300px;
        }
        
        .footer-col h4 {
            font-family: 'Outfit', sans-serif;
            font-size: 16px;
            margin-bottom: 20px;
            color: white;
        }
        
        .footer-col a {
            display: block;
            color: var(--gray-300);
            text-decoration: none;
            padding: 8px 0;
            font-size: 14px;
            transition: color 0.3s;
        }
        
        .footer-col a:hover {
            color: var(--primary-light);
        }
        
        .footer-bottom {
            border-top: 1px solid rgba(255,255,255,0.1);
            padding-top: 30px;
            text-align: center;
            color: var(--gray-300);
            font-size: 14px;
        }
        
        /* View All */
        .view-all {
            text-align: center;
            margin-top: 48px;
        }
        
        @media (max-width: 1024px) {
            .hero-content { grid-template-columns: 1fr; text-align: center; }
            .hero-text p { margin: 0 auto 40px; }
            .hero-btns { justify-content: center; }
            .hero-visual { display: none; }
            .features-grid { grid-template-columns: 1fr; }
            .footer-inner { grid-template-columns: 1fr 1fr; }
        }
        
        @media (max-width: 768px) {
            .header-inner { padding: 16px 20px; }
            nav { display: none; }
            .hero { padding: 60px 20px; }
            .hero-text h1 { font-size: 36px; }
            .section { padding: 60px 20px; }
            .stats-inner { grid-template-columns: repeat(2, 1fr); }
            .footer-inner { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <header>
        <div class="header-inner">
            <a href="<?= $baseUrl ?>/public" class="logo">
                <div class="logo-icon">
                    <i class="fas fa-hospital-user"></i>
                </div>
                <div class="logo-text">Medi<span>Care</span></div>
            </a>
            <nav>
                <a href="<?= $baseUrl ?>/public">Accueil</a>
                <a href="<?= $baseUrl ?>/public/doctors">Médecins</a>
                <a href="<?= $baseUrl ?>/public/book">Rendez-vous</a>
                <a href="<?= $baseUrl ?>/public/map">Carte</a>
                <a href="<?= $baseUrl ?>/public/portal" class="btn-nav">Espace Patient</a>
            </nav>
        </div>
    </header>

    <section class="hero">
        <div class="hero-content">
            <div class="hero-text">
                <h1>Votre Santé,<br>Notre Priorité</h1>
                <p>Consultez nos meilleurs médecins spécialistes et prenez rendez-vous en ligne en quelques clics. Votre bien-être est notre engagement.</p>
                <div class="hero-btns">
                    <a href="<?= $baseUrl ?>/public/book" class="btn btn-primary">
                        <i class="fas fa-calendar-check"></i>
                        Prendre RDV
                    </a>
                    <a href="<?= $baseUrl ?>/public/doctors" class="btn btn-outline">
                        <i class="fas fa-user-md"></i>
                        Nos Médecins
                    </a>
                </div>
            </div>
            <div class="hero-visual">
                <div class="hero-card">
                    <div class="hero-card-header">
                        <div class="hero-avatar">DB</div>
                        <div>
                            <h3>Dr. Benali Doct.</h3>
                            <div class="specialty">Cardiologie</div>
                        </div>
                    </div>
                    <div class="hero-stats">
                        <div class="stat-item">
                            <div class="number">15+</div>
                            <div class="label">Ans Exp.</div>
                        </div>
                        <div class="stat-item">
                            <div class="number">1200+</div>
                            <div class="label">Patients</div>
                        </div>
                        <div class="stat-item">
                            <div class="number">98%</div>
                            <div class="label">Satisfaction</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="stats-bar">
        <div class="stats-inner">
            <div class="stat-box">
                <i class="fas fa-user-md"></i>
                <div class="num">50+</div>
                <div class="txt">Médecins Spécialistes</div>
            </div>
            <div class="stat-box">
                <i class="fas fa-hospital"></i>
                <div class="num">6</div>
                <div class="txt">Hôpitaux Partenaires</div>
            </div>
            <div class="stat-box">
                <i class="fas fa-users"></i>
                <div class="num">10K+</div>
                <div class="txt">Patients Satisfaits</div>
            </div>
            <div class="stat-box">
                <i class="fas fa-star"></i>
                <div class="num">4.9/5</div>
                <div class="txt">Note Moyenne</div>
            </div>
        </div>
    </div>

    <section class="section">
        <div class="section-inner">
            <div class="section-header">
                <h2>Nos Médecins Spécialistes</h2>
                <p>Une équipe de professionnels qualifiés à votre service</p>
            </div>
            <div class="doctors-grid">
                <?php $displayed = 0; foreach ($medecins as $m): ?>
                    <?php if ($displayed >= 6) break; $displayed++; ?>
                    <div class="doctor-card">
                        <div class="top">
                            <div class="avatar"><?= strtoupper(substr($m['prenom'], 0, 1) . substr($m['nom'], 0, 1)) ?></div>
                            <div>
                                <h3>Dr. <?= htmlspecialchars($m['prenom'] . ' ' . $m['nom']) ?></h3>
                                <div class="spec"><?= htmlspecialchars($m['specialite']) ?></div>
                            </div>
                        </div>
                        <div class="hospital">
                            <i class="fas fa-map-marker-alt"></i>
                            <?= htmlspecialchars(($m['hopital_nom'] ?? '') . ' - ' . ($m['hopital_ville'] ?? '')) ?>
                        </div>
                        <div class="bottom">
                            <a href="<?= $baseUrl ?>/public/book?medecin=<?= $m['id'] ?>" class="btn btn-book">
                                <i class="fas fa-calendar-plus"></i>
                                RDV
                            </a>
                            <a href="<?= $baseUrl ?>/public/doctors" class="btn btn-view">Profil</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="view-all">
                <a href="<?= $baseUrl ?>/public/doctors" class="btn btn-primary">
                    <i class="fas fa-users"></i>
                    Voir Tous les Médecins
                </a>
            </div>
        </div>
    </section>

    <section class="section features">
        <div class="section-inner">
            <div class="section-header">
                <h2>Comment Ça Marche</h2>
                <p>Prendre rendez-vous n'a jamais été aussi simple</p>
            </div>
            <div class="features-grid">
                <div class="feature-card">
                    <div class="icon"><i class="fas fa-search"></i></div>
                    <h3>1. Choisissez</h3>
                    <p>Parcourez notre liste de médecins spécialistes et filtrez par spécialité ou hôpital.</p>
                </div>
                <div class="feature-card">
                    <div class="icon"><i class="fas fa-calendar-day"></i></div>
                    <h3>2. Planifiez</h3>
                    <p>Sélectionnez la date et l'horaire qui vous conviennent parmi les créneaux disponibles.</p>
                </div>
                <div class="feature-card">
                    <div class="icon"><i class="fas fa-check-circle"></i></div>
                    <h3>3. Consultez</h3>
                    <p>Venez à votre rendez-vous et recevez les soins appropriés de nos experts.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="cta">
        <h2>Prêt à Consultation?</h2>
        <p>Rejoignez des milliers de patients satisfaits</p>
        <a href="<?= $baseUrl ?>/public/book" class="btn btn-primary" style="background: white; color: var(--primary);">
            <i class="fas fa-calendar-plus"></i>
            Prendre Rendez-vous
        </a>
    </section>

    <footer>
        <div class="footer-inner">
            <div class="footer-brand">
                <a href="<?= $baseUrl ?>/public" class="logo">
                    <div class="logo-icon">
                        <i class="fas fa-hospital-user"></i>
                    </div>
                    <div class="logo-text">Medi<span>Care</span></div>
                </a>
                <p>Votre partenaire santé de confiance. Des professionnels à votre service.</p>
            </div>
            <div class="footer-col">
                <h4>Liens Rapides</h4>
                <a href="<?= $baseUrl ?>/public">Accueil</a>
                <a href="<?= $baseUrl ?>/public/doctors">Médecins</a>
                <a href="<?= $baseUrl ?>/public/book">RDV</a>
            </div>
            <div class="footer-col">
                <h4>Services</h4>
                <a href="#">Cardiologie</a>
                <a href="#">Pédiatrie</a>
                <a href="#">Neurologie</a>
            </div>
            <div class="footer-col">
                <h4>Contact</h4>
                <a href="#"><i class="fas fa-phone"></i> +212 5XX XXXXXX</a>
                <a href="#"><i class="fas fa-envelope"></i> contact@medicare.ma</a>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2024 MediCare - Tous droits réservés</p>
        </div>
    </footer>
</body>
</html>