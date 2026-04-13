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

// ---- BOOT ----
function boot() {
  if (typeof Router !== 'undefined' && typeof DashboardModule !== 'undefined') {
    // Router.navigate('dashboard');
  } else {
    setTimeout(boot, 50);
  }
}
boot();
