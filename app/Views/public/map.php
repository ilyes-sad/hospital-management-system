<?php
// ============================================================
//  app/Views/public/map.php
//  Public hospitals map - Leaflet.js
// ============================================================
$baseUrl = '/hospital-management-system-main/public';
$hopitaux = $hopitaux ?? [];
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
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>
    <style>
        :root {
            --primary: #2563eb;
            --primary-dark: #1d4ed8;
            --primary-light: #3b82f6;
            --accent: #3b82f6;
            --success: #10b981;
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
        
        nav { display: flex; gap: 32px; }
        
        nav a {
            color: var(--gray-700);
            text-decoration: none;
            font-weight: 500;
            font-size: 15px;
            padding: 8px 16px;
            border-radius: 8px;
            transition: all 0.3s;
        }
        
        nav a:hover { color: var(--primary); background: rgba(37, 99, 235, 0.08); }
        
        .btn-nav {
            background: var(--primary);
            color: white !important;
            padding: 10px 20px;
            border-radius: 10px;
        }
        
        .btn-nav:hover { background: var(--primary-dark); }
        
        .main-content {
            margin-top: 80px;
            padding: 24px;
            display: grid;
            grid-template-columns: 380px 1fr;
            gap: 24px;
            max-width: 1600px;
            margin-left: auto;
            margin-right: auto;
            height: calc(100vh - 80px);
        }
        
        .sidebar-panel {
            background: white;
            border-radius: 16px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }
        
        .sidebar-header {
            padding: 20px 24px;
            border-bottom: 1px solid var(--gray-100);
        }
        
        .sidebar-header h2 {
            font-family: 'Outfit', sans-serif;
            font-size: 20px;
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 4px;
        }
        
        .sidebar-header p {
            color: var(--gray-500);
            font-size: 14px;
        }
        
        .search-box {
            padding: 16px 20px;
            border-bottom: 1px solid var(--gray-100);
        }
        
        .search-input {
            width: 100%;
            padding: 12px 16px;
            border: 1px solid var(--gray-300);
            border-radius: 10px;
            font-size: 14px;
            font-family: inherit;
        }
        
        .search-input:focus {
            outline: none;
            border-color: var(--primary);
        }
        
        .hospitals-list {
            flex: 1;
            overflow-y: auto;
            padding: 12px;
        }
        
        .hospital-item {
            padding: 16px;
            border-radius: 12px;
            margin-bottom: 8px;
            cursor: pointer;
            transition: all 0.2s;
            border: 1px solid transparent;
        }
        
        .hospital-item:hover,
        .hospital-item.active {
            background: rgba(37, 99, 235, 0.05);
            border-color: var(--primary);
        }
        
        .hospital-item h3 {
            font-family: 'Outfit', sans-serif;
            font-size: 15px;
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 4px;
        }
        
        .hospital-item .type {
            display: inline-block;
            padding: 3px 10px;
            background: var(--gray-100);
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
            color: var(--gray-600);
            margin-bottom: 8px;
        }
        
        .hospital-item .type.CHU { background: #dbeafe; color: #1e40af; }
        .hospital-item .type.Public { background: #d1fae5; color: #065f46; }
        .hospital-item .type.Privé { background: #fef3c7; color: #92400e; }
        
        .hospital-item .info {
            font-size: 13px;
            color: var(--gray-500);
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .hospital-item .info i {
            width: 16px;
            color: var(--primary);
        }
        
        .map-container {
            background: white;
            border-radius: 16px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            overflow: hidden;
            position: relative;
        }
        
        #map {
            width: 100%;
            height: 100%;
        }
        
        .leaflet-popup-content-wrapper {
            border-radius: 12px;
        }
        
        .popup-content {
            min-width: 220px;
        }
        
        .popup-content h3 {
            font-family: 'Outfit', sans-serif;
            font-size: 16px;
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 8px;
        }
        
        .popup-content .type-badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 500;
            margin-bottom: 12px;
        }
        
        .popup-content .info-row {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 13px;
            color: var(--gray-600);
            margin-bottom: 6px;
        }
        
        .popup-content .info-row i {
            width: 16px;
            color: var(--primary);
        }
        
        .popup-btn {
            display: block;
            width: 100%;
            padding: 10px 16px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 13px;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
            margin-top: 12px;
            transition: background 0.2s;
        }
        
        .popup-btn:hover {
            background: var(--primary-dark);
        }
        
        .no-location {
            color: var(--gray-500);
            font-size: 13px;
            text-align: center;
            padding: 20px;
        }
        
        @media (max-width: 1024px) {
            .main-content {
                grid-template-columns: 1fr;
                height: auto;
            }
            .sidebar-panel {
                max-height: 400px;
            }
            .map-container {
                height: 500px;
            }
        }
        
        @media (max-width: 768px) {
            .header-inner { padding: 16px 20px; }
            nav { display: none; }
            .main-content { margin-top: 80px; padding: 16px; }
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
                <a href="<?= $baseUrl ?>/public/book">RDV</a>
                <a href="<?= $baseUrl ?>/public/portal">Espace Patient</a>
                <a href="<?= $baseUrl ?>/public/map" class="btn-nav" style="background: var(--primary);">Carte</a>
            </nav>
        </div>
    </header>

    <div class="main-content">
        <div class="sidebar-panel">
            <div class="sidebar-header">
                <h2>Hôpitaux</h2>
                <p><?= count($hopitaux) ?> établissements</p>
            </div>
            <div class="search-box">
                <input type="text" class="search-input" placeholder="Rechercher un hôpital..." id="search"/>
            </div>
            <div class="hospitals-list" id="hospitals-list">
                <?php foreach ($hopitaux as $h): ?>
                    <div class="hospital-item" data-id="<?= $h['id'] ?>" data-lat="<?= $h['latitude'] ?? '' ?>" data-lng="<?= $h['longitude'] ?? '' ?>">
                        <h3><?= htmlspecialchars($h['nom']) ?></h3>
                        <span class="type <?= htmlspecialchars($h['type'] ?? '') ?>"><?= htmlspecialchars($h['type'] ?? 'Public') ?></span>
                        <div class="info">
                            <i class="fas fa-map-marker-alt"></i>
                            <?= htmlspecialchars(($h['ville'] ?? '') . ' - ' . ($h['region'] ?? '')) ?>
                        </div>
                        <?php if (!empty($h['telephone'])): ?>
                        <div class="info">
                            <i class="fas fa-phone"></i>
                            <?= htmlspecialchars($h['telephone']) ?>
                        </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <div class="map-container">
            <div id="map"></div>
        </div>
    </div>

    <script>
    const BASE_URL = '<?= $baseUrl ?>';
    
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize map centered on Tunisia
        const map = L.map('map').setView([34.0, -9.5], 6);
        
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors',
            maxZoom: 19
        }).addTo(map);
        
        const hospitals = <?= json_encode(array_map(function($h) {
            return [
                'id' => $h['id'],
                'nom' => $h['nom'],
                'type' => $h['type'] ?? 'Public',
                'ville' => $h['ville'] ?? '',
                'region' => $h['region'] ?? '',
                'telephone' => $h['telephone'] ?? '',
                'latitude' => $h['latitude'] ?? null,
                'longitude' => $h['longitude'] ?? null
            ];
        }, $hopitaux)) ?>;
        
        const markers = {};
        
        hospitals.forEach(function(h) {
            if (h.latitude && h.longitude) {
                const popupContent = `
                    <div class="popup-content">
                        <h3>${h.nom}</h3>
                        <span class="type-badge" style="background:${h.type==='CHU'?'#dbeafe':h.type==='Public'?'#d1fae5':'#fef3c7'};color:${h.type==='CHU'?'#1e40af':h.type==='Public'?'#065f46':'#92400e'}">${h.type}</span>
                        <div class="info-row"><i class="fas fa-map-marker-alt"></i>${h.ville} - ${h.region}</div>
                        ${h.telephone ? '<div class="info-row"><i class="fas fa-phone"></i>' + h.telephone + '</div>' : ''}
                        <a href="${BASE_URL}/public/book?hopital=${h.id}" class="popup-btn"><i class="fas fa-calendar-plus"></i> Prendre RDV</a>
                    </div>
                `;
                
                const marker = L.marker([parseFloat(h.latitude), parseFloat(h.longitude)], {
                    title: h.nom
                }).bindPopup(popupContent).addTo(map);
                
                markers[h.id] = marker;
            }
        });
        
        // Click on hospital item to show marker
        document.querySelectorAll('.hospital-item').forEach(function(item) {
            item.addEventListener('click', function() {
                document.querySelectorAll('.hospital-item').forEach(i => i.classList.remove('active'));
                this.classList.add('active');
                
                const id = this.dataset.id;
                const lat = this.dataset.lat;
                const lng = this.dataset.lng;
                
                if (lat && lng && markers[id]) {
                    map.setView([lat, lng], 14);
                    markers[id].openPopup();
                }
            });
        });
        
        // Search functionality
        document.getElementById('search').addEventListener('input', function(e) {
            const query = e.target.value.toLowerCase();
            document.querySelectorAll('.hospital-item').forEach(function(item) {
                const text = item.textContent.toLowerCase();
                item.style.display = text.includes(query) ? 'block' : 'none';
            });
        });
    });
    </script>
</body>
</html>