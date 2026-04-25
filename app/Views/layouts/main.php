<?php
$baseUrl = '/hospital-management-system-main/public';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>MediCare</title>
    <link rel="preconnect" href="https://fonts.googleapis.com"/>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;500;600;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet"/>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <link rel="stylesheet" href="<?= $baseUrl ?>/css/style.css"/>
</head>
<body>

<aside class="sidebar">
    <div class="sidebar-logo">
        <div class="logo-mark">
            <svg width="28" height="28" viewBox="0 0 28 28" fill="none">
                <rect width="28" height="28" rx="8" fill="var(--accent)"/>
                <path d="M14 6v16M6 14h16" stroke="white" stroke-width="2.5" stroke-linecap="round"/>
            </svg>
        </div>
        <span class="logo-text">MediCare</span>
    </div>
    <nav class="sidebar-nav">
        <div class="nav-section-label">Navigation</div>
        <a href="#" data-module="dashboard" class="nav-item active">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
            <span>Tableau de bord</span>
        </a>
        <a href="#" data-module="rendezvous" class="nav-item">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
            <span>Rendez-vous</span>
        </a>
        <a href="#" data-module="patients" class="nav-item">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg>
            <span>Patients</span>
        </a>
        <a href="#" data-module="medecins" class="nav-item">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg>
            <span>Medecins</span>
        </a>
        <a href="#" data-module="hopitaux" class="nav-item">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 21V7l9-4 9 4v14"/><path d="M9 21V13h6v8"/></svg>
            <span>Hopitaux</span>
        </a>
        <a href="<?= $baseUrl ?>/public/map" target="_blank" class="nav-item">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 6v16h22"/><path d="M5 2v4"/><path d="M19 2v4"/><path d="M5 22v-4"/><path d="M19 22v-4"/><circle cx="12" cy="10" r="3"/></svg>
            <span>Carte</span>
        </a>
    </nav>
    <div class="sidebar-footer">
        <div class="user-card">
            <div class="user-avatar">A</div>
            <div class="user-info">
                <div class="user-name">Admin</div>
                <div class="user-role">MVC PHP8 + MySQL</div>
            </div>
        </div>
    </div>
</aside>

<main class="main-content">
    <header class="topbar">
        <div class="topbar-left">
            <h1 class="page-title" id="page-title"><?= htmlspecialchars($pageTitle ?? '') ?></h1>
            <p class="page-sub" id="page-sub"><?= htmlspecialchars($pageSub ?? '') ?></p>
        </div>
        <div class="topbar-right">
            <div class="search-bar">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                <input type="text" placeholder="Rechercher..." id="global-search"/>
            </div>
        </div>
    </header>
    <div class="content-area" id="content-area">
        <!-- SPA content rendered here by JS -->
    </div>
</main>

<!-- Toast -->
<div id="toast-container" style="position:fixed;bottom:24px;right:24px;z-index:9999;display:flex;flex-direction:column;gap:8px"></div>

<!-- Modal -->
<div id="modal-overlay" style="display:none">
    <div id="modal" class="modal">
        <div class="modal-header">
            <h2 class="modal-title" id="modal-title"></h2>
<button id="modal-close" class="modal-close">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M18 6 6 18M6 6l12 12"/>
    </svg>
</button>        </div>
        <div class="modal-body" id="modal-body"></div>
    </div>
</div>

<!-- ✅ Scripts chargés UNE SEULE FOIS -->
<script>console.log('BASE_URL:', '<?= $baseUrl ?>');</script>
<script src="<?= $baseUrl ?>/js/app.js"></script>
<script src="<?= $baseUrl ?>/js/utils/api.js"></script>
<script src="<?= $baseUrl ?>/js/modules/dashboard.js"></script>
<script src="<?= $baseUrl ?>/js/modules/patients.js"></script>
<script src="<?= $baseUrl ?>/js/modules/medecins.js"></script>
<script src="<?= $baseUrl ?>/js/modules/hopitaux.js"></script>
<script src="<?= $baseUrl ?>/js/modules/rendezvous.js"></script>
<script>
    Router.navigate('dashboard');
</script>

</body>
</html>