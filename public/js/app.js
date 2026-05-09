// ============================================================
//  js/app.js — Router SPA, Modal, Toast, utilitaires globaux
// ============================================================

// ---- TOAST ----
function showToast(message, type = 'info') {
  const c = document.getElementById('toast-container');
  if (!c) return;
  const t = document.createElement('div');
  t.className = 'toast';
  t.innerHTML = `<span class="toast-dot ${type}"></span><span>${message}</span>`;
  c.appendChild(t);
  setTimeout(() => { t.style.opacity='0'; t.style.transform='translateX(20px)'; t.style.transition='.3s'; setTimeout(()=>t.remove(),300); }, 3200);
}

// ---- MODAL ----
const Modal = {
  open(title, bodyHTML, size = '') {
    document.getElementById('modal-title').textContent = title;
    document.getElementById('modal-body').innerHTML = bodyHTML;
    document.getElementById('modal').className = (size ? 'modal ' + size : 'modal');
    const overlay = document.getElementById('modal-overlay');
    overlay.style.display = 'flex';   // ← overrides the inline display:none
    overlay.classList.add('open');
    document.body.style.overflow = 'hidden';
  },
  close() {
    const overlay = document.getElementById('modal-overlay');
    overlay.style.display = 'none';   // ← hides it again
    overlay.classList.remove('open');
    document.body.style.overflow = '';
  },
  loading(title) { 
    this.open(title, '<div class="loading-spinner"><div class="spinner"></div><p>Chargement…</p></div>'); 
  }
};
// Close modal on ESC key
document.addEventListener('keydown', e => {
  if (e.key === 'Escape' && document.getElementById('modal-overlay').classList.contains('open')) {
    Modal.close();
  }
});
// Close modal when clicking outside
const modalOverlay = document.getElementById('modal-overlay');
if (modalOverlay) {
  modalOverlay.addEventListener('click', e => { 
    if (e.target.id === 'modal-overlay') Modal.close(); 
  });
}
const modalClose = document.getElementById('modal-close');
if (modalClose) modalClose.addEventListener('click', () => Modal.close());

// ---- ROUTER ----
const Router = {
  current: 'dashboard',
  modules: {
    dashboard:  { title: 'Tableau de bord',   sub: "Vue d'ensemble du systeme",     render: () => DashboardModule.render() },
    rendezvous: { title: 'Rendez-vous',         sub: 'Gestion des rendez-vous (N-N)', render: () => RendezVousModule.render() },
    patients:   { title: 'Patients',            sub: 'Dossiers patients',             render: () => PatientsModule.render() },
    medecins:   { title: 'Medecins',            sub: 'Corps medical',                 render: () => MedecinsModule.render() },
    hopitaux:   { title: 'Hopitaux',            sub: 'Etablissements de sante',       render: () => HopitauxModule.render() },
  },
  navigate(name) {
    // Always close modal before navigating
    const modalOverlay = document.getElementById('modal-overlay');
    if (modalOverlay) modalOverlay.classList.remove('open');
    document.body.style.overflow = '';
    
    const mod = this.modules[name];
    if (!mod) return;
    this.current = name;
    document.querySelectorAll('.nav-item').forEach(el => el.classList.toggle('active', el.dataset.module === name));
    const titleEl = document.getElementById('page-title');
    const subEl = document.getElementById('page-sub');
    if (titleEl) titleEl.textContent = mod.title;
    if (subEl) subEl.textContent = mod.sub;
    document.getElementById('content-area').innerHTML = '';
    mod.render();
  },
};

// Nav clicks
document.querySelectorAll('.nav-item[data-module]').forEach(link => {
  link.addEventListener('click', e => { e.preventDefault(); Router.navigate(link.dataset.module); });
});

// ---- HELPERS ----
function formatDate(str) {
  if (!str) return '—';
  const d = new Date(str + 'T00:00:00');
  return d.toLocaleDateString('fr-FR', { day:'2-digit', month:'short', year:'numeric' });
}
function todayStr() { return new Date().toISOString().split('T')[0]; }
function initials(prenom, nom) { return ((prenom||'')[0]+(nom||'')[0]).toUpperCase(); }
function statusBadge(s) {
  const m = { 'confirme':'badge-success','en attente':'badge-warning','annule':'badge-danger','termine':'badge-neutral' };
  return `<span class="badge ${m[s]||'badge-neutral'}">${s}</span>`;
}
function handleApiError(res, fallback='Erreur serveur') {
  if (!res.success) { showToast(res.error || fallback, 'error'); return true; }
  return false;
}

// ---- GLOBAL SEARCH ----
function initSearch() {
  var searchInput = document.getElementById('global-search');
  var contentArea = document.getElementById('content-area');
  var searchTimeout;
  
  if (!searchInput || typeof Api === 'undefined') {
    setTimeout(initSearch, 50);
    return;
  }
  
  searchInput.addEventListener('input', function(e) {
    clearTimeout(searchTimeout);
    var query = e.target.value.trim();
    
    if (query.length < 2) {
      contentArea.innerHTML = '';
      return;
    }
    
    searchTimeout = setTimeout(function() {
      performSearch(query);
    }, 300);
  });
  
  function performSearch(query) {
    Promise.all([
      Api.Patients.search(query),
      Api.Medecins.search(query),
      Api.Hopitaux.search(query),
      Api.Rendezvous.getAll()
    ]).then(function(responses) {
      var patientsData = responses[0];
      var doctorsData = responses[1];
      var hopitauxData = responses[2];
      var rdvData = responses[3];
      
      var results = { patients: [], doctors: [], hopitaux: [], rendezvous: [] };
      var q = query.toLowerCase();
      
      if (patientsData.success && patientsData.data) {
        results.patients = patientsData.data.filter(function(p) {
          return (p.nom && p.nom.toLowerCase().startsWith(q)) ||
                 (p.prenom && p.prenom.toLowerCase().startsWith(q)) ||
                 (p.cin && p.cin.toLowerCase().startsWith(q));
        });
      }
      
      if (doctorsData.success && doctorsData.data) {
        results.doctors = doctorsData.data.filter(function(m) {
          return (m.nom && m.nom.toLowerCase().startsWith(q)) ||
                 (m.prenom && m.prenom.toLowerCase().startsWith(q)) ||
                 (m.specialite && m.specialite.toLowerCase().startsWith(q));
        });
      }
      
      if (hopitauxData.success && hopitauxData.data) {
        results.hopitaux = hopitauxData.data.filter(function(h) {
          return (h.nom && h.nom.toLowerCase().startsWith(q)) ||
                 (h.ville && h.ville.toLowerCase().startsWith(q)) ||
                 (h.region && h.region.toLowerCase().startsWith(q));
        });
      }
      
      if (rdvData.success && rdvData.data) {
        results.rendezvous = rdvData.data.slice(0, 10).filter(function(r) {
          return (r.patient_nom && r.patient_nom.toLowerCase().startsWith(q)) ||
                 (r.medecin_nom && r.medecin_nom.toLowerCase().startsWith(q)) ||
                 (r.motif && r.motif.toLowerCase().startsWith(q));
        });
      }
      
      displaySearchResults(results, query);
    }).catch(function(err) {
      console.error('Search error:', err);
    });
  }
}

initSearch();

function displaySearchResults(results, query) {
  const contentArea = document.getElementById('content-area');
  const totalResults = results.patients.length + results.doctors.length + results.hopitaux.length;
  
  if (totalResults === 0) {
    contentArea.innerHTML = `
      <div style="padding:40px;text-align:center;color:var(--gray-500)">
        <i class="fas fa-search" style="font-size:48px;margin-bottom:16px;color:var(--gray-300)"></i>
        <p>Aucun résultat pour "${query}"</p>
      </div>
    `;
    return;
  }
  
  let html = `
    <div style="padding:20px">
      <h2 style="margin-bottom:20px;font-size:20px">Résultats de recherche pour "${query}"</h2>
  `;
  
  // Patients
  if (results.patients.length > 0) {
    html += `
      <div style="margin-bottom:24px">
        <h3 style="font-size:16px;color:var(--primary);margin-bottom:12px;display:flex;align-items:center;gap:8px">
          <i class="fas fa-users"></i> Patients (${results.patients.length})
        </h3>
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:12px">
    `;
    results.patients.forEach(p => {
      html += `
        <div style="background:white;padding:16px;border-radius:8px;box-shadow:0 1px 3px rgba(0,0,0,0.1);cursor:pointer" onclick="Router.navigate('patients')">
          <div style="font-weight:600">${p.prenom} ${p.nom}</div>
          <div style="font-size:13px;color:var(--gray-500)">CIN: ${p.cin || '—'}</div>
          <div style="font-size:13px;color:var(--gray-500)">${p.telephone || ''}</div>
        </div>
      `;
    });
    html += `</div></div>`;
  }
  
  // Doctors
  if (results.doctors.length > 0) {
    html += `
      <div style="margin-bottom:24px">
        <h3 style="font-size:16px;color:var(--primary);margin-bottom:12px;display:flex;align-items:center;gap:8px">
          <i class="fas fa-user-md"></i> Médecins (${results.doctors.length})
        </h3>
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:12px">
    `;
    results.doctors.forEach(m => {
      html += `
        <div style="background:white;padding:16px;border-radius:8px;box-shadow:0 1px 3px rgba(0,0,0,0.1);cursor:pointer" onclick="Router.navigate('medecins')">
          <div style="font-weight:600">Dr. ${m.prenom} ${m.nom}</div>
          <div style="font-size:13px;color:var(--gray-500)">${m.specialite || ''}</div>
          <div style="font-size:13px;color:var(--gray-500)">${m.hopital_nom || ''}</div>
        </div>
      `;
    });
    html += `</div></div>`;
  }
  
  // Hospitals
  if (results.hopitaux.length > 0) {
    html += `
      <div style="margin-bottom:24px">
        <h3 style="font-size:16px;color:var(--primary);margin-bottom:12px;display:flex;align-items:center;gap:8px">
          <i class="fas fa-hospital"></i> Hôpitaux (${results.hopitaux.length})
        </h3>
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:12px">
    `;
    results.hopitaux.forEach(h => {
      html += `
        <div style="background:white;padding:16px;border-radius:8px;box-shadow:0 1px 3px rgba(0,0,0,0.1);cursor:pointer" onclick="Router.navigate('hopitaux')">
          <div style="font-weight:600">${h.nom}</div>
          <div style="font-size:13px;color:var(--gray-500)">${h.ville || ''} - ${h.type || ''}</div>
        </div>
      `;
    });
    html += `</div></div>`;
  }
  
  // Rendez-vous
  if (results.rendezvous && results.rendezvous.length > 0) {
    html += `
      <div style="margin-bottom:24px">
        <h3 style="font-size:16px;color:var(--primary);margin-bottom:12px;display:flex;align-items:center;gap:8px">
          <i class="fas fa-calendar-alt"></i> Rendez-vous (${results.rendezvous.length})
        </h3>
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:12px">
    `;
    results.rendezvous.forEach(r => {
      html += `
        <div style="background:white;padding:16px;border-radius:8px;box-shadow:0 1px 3px rgba(0,0,0,0.1);cursor:pointer" onclick="Router.navigate('rendezvous')">
          <div style="font-weight:600">${r.patient_nom || 'Patient'} ${r.patient_prenom || ''}</div>
          <div style="font-size:13px;color:var(--gray-500)">${r.medecin_nom || ''} - ${r.date_rdv || ''}</div>
          <div style="font-size:13px;color:var(--gray-500)">${r.motif || ''}</div>
        </div>
      `;
    });
    html += `</div></div>`;
  }
  
  html += `</div>`;
  contentArea.innerHTML = html;
}

// ---- BOOT ----
function boot() {
  if (typeof Router !== 'undefined' && typeof DashboardModule !== 'undefined') {
    // Router.navigate('dashboard');
  } else {
    setTimeout(boot, 50);
  }
}
boot();
