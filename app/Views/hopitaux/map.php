<?php
// ============================================================
//  app/Views/hopitaux/map.php
//  Admin map page
// ============================================================
$hopitaux = $hopitaux ?? [];
?>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<style>
.hopitals-map{height:calc(100vh - 140px);border-radius:12px}
.info-card{position:absolute;top:20px;right:20px;z-index:1000;background:white;padding:16px 20px;border-radius:12px;box-shadow:0 2px 12px rgba(0,0,0,0.15)}
.info-card h3{font-size:16px;font-weight:600;margin-bottom:4px}
.info-card p{font-size:13px;color:var(--text-secondary)}
.leaflet-popup-content h3{font-size:15px;font-weight:600;margin-bottom:8px}
.popup-row{display:flex;align-items:center;gap:8px;font-size:13px;color:var(--text-secondary);margin-bottom:4px}
.popup-btn{display:block;width:100%;padding:10px;background:var(--accent);color:white;text-align:center;border-radius:8px;text-decoration:none;font-weight:500;margin-top:10px}
</style>
<div class="hopitals-map" id="map"></div>
<div class="info-card">
    <h3>Hôpitaux</h3>
    <p><?= count($hopitaux) ?> établissements</p>
</div>
<script>
(function(){
var map=L.map('map').setView([34.0,-9.5],6);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',{attribution:'© OSM',maxZoom:19}).addTo(map);
var hopitaux=<?= json_encode(array_map(function($h){return['id'=>$h['id'],'nom'=>$h['nom'],'type'=>$h['type'],'ville'=>$h['ville'],'region'=>$h['region'],'telephone'=>$h['telephone'],'latitude'=>$h['latitude'],'longitude'=>$h['longitude']];},$hopitaux)) ?>;
hopitaux.forEach(function(h){
if(h.latitude&&h.longitude){
var popup='<h3>'+h.nom+'</h3>';
popup+='<div class="popup-row">'+h.type+' - '+h.ville+'</div>';
if(h.telephone)popup+='<div class="popup-row">'+h.telephone+'</div>';
popup+='<a href="/hospital-management-system-main/public/public/map" class="popup-btn" target="_blank">Voir surCarte</a>';
L.marker([parseFloat(h.latitude),parseFloat(h.longitude)]).bindPopup(popup).addTo(map);
}
});
})();
</script>