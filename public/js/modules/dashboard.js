// ============================================================
//  js/modules/dashboard.js
// ============================================================

const DashboardModule = (() => {

  async function render() {
    const area = document.getElementById('content-area');
    area.innerHTML = '<div class="loading-spinner"><div class="spinner"></div></div>';

    const [statsRes, rdvsRes, patientsRes, medecinsRes, hopitauxRes] = await Promise.all([
      Api.Rendezvous.getStats(),
      Api.Rendezvous.getAll(),
      Api.Patients.getAll(),
      Api.Medecins.getAll(),
      Api.Hopitaux.getAll(),
    ]);

    const stats    = statsRes.data    || {};
    const rdvs     = rdvsRes.data     || [];
    const patients = patientsRes.data || [];
    const medecins = medecinsRes.data || [];
    const hopitaux = hopitauxRes.data || [];
    const recent   = rdvs.slice(0, 5);

    area.innerHTML = `
      <div class="stats-grid">
        <div class="stat-card" style="--stat-color:#0EA5E9;--stat-bg:#E0F2FE">
          <div class="stat-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg></div>
          <div class="stat-info"><div class="stat-label">Rendez-vous</div><div class="stat-value">${stats.total||0}</div><div class="stat-trend">↑ ${stats.aujourd_hui||0} aujourd'hui</div></div>
        </div>
        <div class="stat-card" style="--stat-color:#0D9488;--stat-bg:#CCFBF1">
          <div class="stat-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg></div>
          <div class="stat-info"><div class="stat-label">Patients</div><div class="stat-value">${patients.length}</div><div class="stat-trend" style="color:var(--teal)">Enregistrés</div></div>
        </div>
        <div class="stat-card" style="--stat-color:#7C3AED;--stat-bg:#EDE9FE">
          <div class="stat-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/></svg></div>
          <div class="stat-info"><div class="stat-label">Médecins</div><div class="stat-value">${medecins.length}</div><div class="stat-trend" style="color:#7C3AED">${[...new Set(medecins.map(m=>m.specialite))].length} spécialités</div></div>
        </div>
        <div class="stat-card" style="--stat-color:#D97706;--stat-bg:#FEF3C7">
          <div class="stat-icon"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 21V7l9-4 9 4v14"/><path d="M9 21V13h6v8"/></svg></div>
          <div class="stat-info"><div class="stat-label">Hôpitaux</div><div class="stat-value">${hopitaux.length}</div><div class="stat-trend" style="color:var(--warning)">${[...new Set(hopitaux.map(h=>h.region))].length} régions</div></div>
        </div>
      </div>

      <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-bottom:24px">
        <div class="card" style="padding:16px 20px;border-top:3px solid var(--success)">
          <div class="stat-label">Confirmés</div><div class="stat-value" style="font-size:1.6rem;color:var(--success)">${stats.confirmes||0}</div>
        </div>
        <div class="card" style="padding:16px 20px;border-top:3px solid var(--warning)">
          <div class="stat-label">En attente</div><div class="stat-value" style="font-size:1.6rem;color:var(--warning)">${stats.en_attente||0}</div>
        </div>
        <div class="card" style="padding:16px 20px;border-top:3px solid var(--danger)">
          <div class="stat-label">Annulés</div><div class="stat-value" style="font-size:1.6rem;color:var(--danger)">${stats.annules||0}</div>
        </div>
      </div>

      <div class="dashboard-grid">
        <div class="card full">
          <div class="card-header">
            <div><div class="card-title">Rendez-vous récents</div><div class="card-subtitle">5 derniers</div></div>
            <button class="btn btn-primary btn-sm" onclick="Router.navigate('rendezvous')">Voir tout →</button>
          </div>
          <div class="table-wrapper">
            <table>
              <thead><tr><th>Patient</th><th>Médecin</th><th>Spécialité</th><th>Hôpital</th><th>Date</th><th>Heure</th><th>Statut</th></tr></thead>
              <tbody>
                ${recent.length ? recent.map(r=>`
                  <tr>
                    <td><div class="avatar-cell"><div class="avatar avatar-blue">${(r.patient_nom||'?')[0]}</div><span class="avatar-name">${r.patient_nom||'—'}</span></div></td>
                    <td>${r.medecin_nom||'—'}</td>
                    <td><span class="badge badge-teal">${r.specialite||'—'}</span></td>
                    <td>${r.hopital_nom||'—'}</td>
                    <td>${formatDate(r.date_rdv)}</td>
                    <td><span class="info-pill">${r.heure}</span></td>
                    <td>${statusBadge(r.statut)}</td>
                  </tr>`).join('') : '<tr><td colspan="7"><div class="empty-state"><p>Aucun rendez-vous</p></div></td></tr>'}
              </tbody>
            </table>
          </div>
        </div>

        <div class="card">
          <div class="card-header"><div class="card-title">Actions rapides</div></div>
          <div class="quick-actions">
            <div class="quick-action" onclick="Router.navigate('rendezvous');setTimeout(()=>RendezVousModule.openNew(),200)">
              <div class="quick-action-icon" style="background:var(--accent-light);color:var(--accent)">📅</div>
              <div><div class="quick-action-label">Nouveau RDV</div><div class="quick-action-sub">Réserver</div></div>
            </div>
            <div class="quick-action" onclick="Router.navigate('patients');setTimeout(()=>PatientsModule.openNew(),200)">
              <div class="quick-action-icon" style="background:var(--teal-light);color:var(--teal)">👤</div>
              <div><div class="quick-action-label">Ajouter patient</div><div class="quick-action-sub">Nouveau dossier</div></div>
            </div>
            <div class="quick-action" onclick="Router.navigate('medecins')">
              <div class="quick-action-icon" style="background:#EDE9FE;color:#7C3AED">👨‍⚕️</div>
              <div><div class="quick-action-label">Médecins</div><div class="quick-action-sub">Consulter</div></div>
            </div>
            <div class="quick-action" onclick="Router.navigate('hopitaux')">
              <div class="quick-action-icon" style="background:var(--warning-light);color:var(--warning)">🏥</div>
              <div><div class="quick-action-label">Hôpitaux</div><div class="quick-action-sub">Voir la liste</div></div>
            </div>
          </div>
        </div>

        <div class="card">
          <div class="card-header"><div class="card-title">Médecins par spécialité</div></div>
          ${renderSpecChart(medecins)}
        </div>
      </div>
    `;
  }

  function renderSpecChart(medecins) {
    const counts = {};
    medecins.forEach(m => { counts[m.specialite] = (counts[m.specialite]||0)+1; });
    const entries = Object.entries(counts).sort((a,b)=>b[1]-a[1]);
    const max = Math.max(...entries.map(e=>e[1]),1);
    const colors = ['var(--accent)','var(--teal)','#7C3AED','var(--warning)','#EC4899'];
    return entries.map(([spec,cnt],i)=>`
      <div style="margin-bottom:12px">
        <div style="display:flex;justify-content:space-between;font-size:0.82rem;margin-bottom:4px">
          <span style="font-weight:500">${spec}</span><span style="color:var(--text-muted)">${cnt}</span>
        </div>
        <div class="progress-bar"><div class="progress-fill" style="width:${(cnt/max)*100}%;background:${colors[i%colors.length]}"></div></div>
      </div>`).join('');
  }

  return { render };
})();
