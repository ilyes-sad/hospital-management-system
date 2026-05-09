// ============================================================
//  js/modules/rendezvous.js  — Module 1 (complet)
//  Toutes les requêtes vont vers api/rendezvous.php (PHP+PDO)
// ============================================================

const RendezVousModule = (() => {
  let state = {};

  function resetState() {
    state = { step:1, region:null, hopitalId:null, medecinId:null, patientId:null, date:null, heure:null, motif:'' };
  }

  // ---- LISTE ----
  async function render() {
    const area = document.getElementById('content-area');
    area.innerHTML = '<div class="loading-spinner"><div class="spinner"></div></div>';

    const [rdvsRes, regionsRes] = await Promise.all([
      Api.Rendezvous.getAll(),
      Api.Hopitaux.getRegions(),
    ]);
    const rdvs    = rdvsRes.data    || [];
    const regions = regionsRes.data || [];

    area.innerHTML = `
      <div class="section-header">
        <div>
          <div class="section-title">Gestion des rendez-vous</div>
          <div class="section-subtitle">Jointure N-N : Patient ↔ rendez_vous ↔ Médecin</div>
        </div>
        <button class="btn btn-primary" onclick="RendezVousModule.openNew()">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14M5 12h14"/></svg>
          Nouveau rendez-vous
        </button>
      </div>
      <div class="filter-bar">
        <div class="filter-input" style="max-width:280px">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
          <input type="text" id="rdv-search" placeholder="Patient, médecin, motif…" oninput="RendezVousModule.filter()" />
        </div>
        <select class="filter-select" id="rdv-statut" onchange="RendezVousModule.filter()">
          <option value="">Tous statuts</option>
          <option>confirmé</option><option>en attente</option><option>annulé</option><option>terminé</option>
        </select>
        <select class="filter-select" id="rdv-region" onchange="RendezVousModule.filter()">
          <option value="">Toutes régions</option>
          ${regions.map(r=>`<option value="${r}">${r}</option>`).join('')}
        </select>
      </div>
      <div class="card" style="padding:0">
        <div class="table-wrapper" id="rdv-table">${renderTable(rdvs)}</div>
      </div>`;
  }

  function renderTable(rdvs) {
    if (!rdvs.length) return `<div class="empty-state"><svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M3 10h18"/></svg><p>Aucun rendez-vous</p></div>`;
    return `<table><thead><tr>
      <th>#</th><th>Patient</th><th>Médecin</th><th>Spécialité</th><th>Hôpital</th><th>Date</th><th>Heure</th><th>Statut</th><th>Actions</th>
    </tr></thead><tbody>
    ${rdvs.map((r,i)=>`<tr>
      <td><span style="color:var(--text-muted);font-size:0.8rem">#${String(i+1).padStart(3,'0')}</span></td>
      <td><div class="avatar-cell"><div class="avatar avatar-blue">${(r.patient_nom||'?')[0]}</div><span class="avatar-name">${r.patient_nom||'—'}</span></div></td>
      <td><div class="avatar-cell"><div class="avatar avatar-teal">${(r.medecin_nom||'Dr?')[4]||'?'}</div><span>${r.medecin_nom||'—'}</span></div></td>
      <td><span class="badge badge-teal">${r.specialite||'—'}</span></td>
      <td style="font-size:0.85rem">${r.hopital_nom||'—'}</td>
      <td>${formatDate(r.date_rdv)}</td>
      <td><span class="info-pill">${r.heure}</span></td>
      <td>${statusBadge(r.statut)}</td>
      <td><div class="table-actions">
        <button class="tbl-btn" onclick="RendezVousModule.view(${r.id})" title="Voir">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/></svg>
        </button>
        <button class="tbl-btn" onclick="RendezVousModule.edit(${r.id})" title="Modifier">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4z"/></svg>
        </button>
        <button class="tbl-btn delete" onclick="RendezVousModule.confirmDelete(${r.id})" title="Supprimer">
          <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6M10 11v6M14 11v6M9 6V4h6v2"/></svg>
        </button>
      </div></td>
    </tr>`).join('')}
    </tbody></table>`;
  }

  async function filter() {
    const q      = document.getElementById('rdv-search')?.value.trim() || '';
    const statut = document.getElementById('rdv-statut')?.value || '';
    const region = document.getElementById('rdv-region')?.value || '';
    const res    = await Api.Rendezvous.getAll({ search:q, statut, region });
    document.getElementById('rdv-table').innerHTML = renderTable(res.data||[]);
  }

  // ---- VIEW ----
  async function view(id) {
    Modal.loading('Détails du rendez-vous');
    const res = await Api.Rendezvous.getById(id);
    if (handleApiError(res)) return;
    const r = res.data;
    Modal.open('Détails du rendez-vous', `
      <div class="rdv-summary">
        ${row('Patient',    r.patient_nom)  }
        ${row('CIN',        r.patient_cin)  }
        ${row('Médecin',    r.medecin_nom)  }
        ${row('Spécialité', r.specialite)   }
        ${row('Hôpital',    r.hopital_nom)  }
        ${row('Région',     r.hopital_region)}
        ${row('Date',       formatDate(r.date_rdv))}
        ${row('Heure',      r.heure)        }
        ${row('Motif',      r.motif||'—')   }
        ${row('Statut',     statusBadge(r.statut))}
      </div>
      <div class="form-actions" style="border:none;padding:0;margin-top:16px">
        <button class="btn btn-outline" onclick="Modal.close()">Fermer</button>
        <button class="btn btn-primary" onclick="Modal.close();RendezVousModule.edit(${r.id})">Modifier</button>
      </div>`);
  }
  function row(k,v){ return `<div class="rdv-summary-row"><span>${k}</span><span>${v||'—'}</span></div>`; }

  // ---- EDIT ----
  async function edit(id) {
    Modal.loading('Modifier le rendez-vous');
    const [rdvRes, patientsRes, medecinsRes] = await Promise.all([
      Api.Rendezvous.getById(id),
      Api.Patients.getAll(),
      Api.Medecins.getAll(),
    ]);
    if (handleApiError(rdvRes)) return;
    const r = rdvRes.data;
    const patients = patientsRes.data||[];
    const medecins = medecinsRes.data||[];
    Modal.open('Modifier le rendez-vous', `
      <div class="form-group"><label class="form-label">Patient</label>
        <select class="form-control" id="e-pat">
          ${patients.map(p=>`<option value="${p.id}" ${p.id==r.patient_id?'selected':''}>${p.prenom} ${p.nom} — ${p.cin||''}</option>`).join('')}
        </select></div>
      <div class="form-group"><label class="form-label">Médecin</label>
        <select class="form-control" id="e-med">
          ${medecins.map(m=>`<option value="${m.id}" ${m.id==r.medecin_id?'selected':''}>${m.prenom} ${m.nom} — ${m.specialite}</option>`).join('')}
        </select></div>
      <div class="form-row">
        <div class="form-group"><label class="form-label">Date</label>
          <input type="date" class="form-control" id="e-date" value="${r.date_rdv}" min="${todayStr()}"/></div>
        <div class="form-group"><label class="form-label">Heure</label>
          <input type="time" class="form-control" id="e-heure" value="${r.heure}"/></div>
      </div>
      <div class="form-group"><label class="form-label">Motif</label>
        <input type="text" class="form-control" id="e-motif" value="${r.motif||''}"/></div>
      <div class="form-group"><label class="form-label">Statut</label>
        <select class="form-control" id="e-statut">
          ${['en attente','confirmé','annulé','terminé'].map(s=>`<option value="${s}" ${s===r.statut?'selected':''}>${s}</option>`).join('')}
        </select></div>
      <div class="form-actions">
        <button class="btn btn-outline" onclick="Modal.close()">Annuler</button>
        <button class="btn btn-primary" onclick="RendezVousModule.saveEdit(${id},${r.hopital_id})">Enregistrer</button>
      </div>`);
  }

  async function saveEdit(id, hopitalId) {
    const data = {
      patient_id: document.getElementById('e-pat').value,
      medecin_id: document.getElementById('e-med').value,
      hopital_id: hopitalId,
      date_rdv:   document.getElementById('e-date').value,
      heure:      document.getElementById('e-heure').value,
      motif:      document.getElementById('e-motif').value.trim(),
      statut:     document.getElementById('e-statut').value,
    };
    const res = await Api.Rendezvous.update(id, data);
    if (handleApiError(res)) return;
    Modal.close(); showToast('Rendez-vous modifié','success'); render(); Router.refreshBadge();
  }

  // ---- DELETE ----
  async function confirmDelete(id) {
    Modal.loading('Supprimer');
    const res = await Api.Rendezvous.getById(id);
    if (handleApiError(res)) return;
    const r = res.data;
    Modal.open('Confirmer la suppression', `
      <p style="color:var(--text-secondary);margin-bottom:20px">
        Supprimer le rendez-vous de <strong>${r.patient_nom}</strong> avec <strong>${r.medecin_nom}</strong>
        le <strong>${formatDate(r.date_rdv)}</strong> à <strong>${r.heure}</strong> ?
      </p>
      <div class="form-actions" style="border:none;padding:0;margin:0">
        <button class="btn btn-outline" onclick="Modal.close()">Annuler</button>
        <button class="btn btn-danger" onclick="RendezVousModule.doDelete(${id})">Supprimer</button>
      </div>`);
  }

  async function doDelete(id) {
    const res = await Api.Rendezvous.delete(id);
    if (handleApiError(res)) return;
    Modal.close(); showToast('Rendez-vous supprimé','error'); render(); Router.refreshBadge();
  }

  // ============================================================
  //  WIZARD NOUVEAU RDV — 4 étapes
  // ============================================================
  async function openNew() {
    resetState();
    Modal.loading('Nouveau rendez-vous');
    const regionsRes = await Api.Hopitaux.getRegions();
    const regions = regionsRes.data||[];
    Modal.open('Nouveau rendez-vous', buildWizardHTML(regions), 'modal-lg');
    updateStepUI();
    renderWizardStep();
  }

  function buildWizardHTML(regions) {
    window._wiz_regions = regions;
    return `
      <div class="wizard-steps" id="wiz-steps">
        <div class="wizard-step"><div class="step-circle active" id="wsc-1">1</div><span class="step-label active" id="wsl-1">Région & Hôpital</span></div>
        <div class="step-line" id="wsl-line-1"></div>
        <div class="wizard-step"><div class="step-circle" id="wsc-2">2</div><span class="step-label" id="wsl-2">Médecin</span></div>
        <div class="step-line" id="wsl-line-2"></div>
        <div class="wizard-step"><div class="step-circle" id="wsc-3">3</div><span class="step-label" id="wsl-3">Patient & Date</span></div>
        <div class="step-line" id="wsl-line-3"></div>
        <div class="wizard-step"><div class="step-circle" id="wsc-4">4</div><span class="step-label" id="wsl-4">Confirmation</span></div>
      </div>
      <div id="wiz-body"></div>
      <div class="form-actions" id="wiz-actions"></div>`;
  }

  function updateStepUI() {
    for (let i=1;i<=4;i++){
      const c=document.getElementById(`wsc-${i}`), l=document.getElementById(`wsl-${i}`), ln=document.getElementById(`wsl-line-${i}`);
      if(!c)continue;
      c.classList.remove('active','done'); l.classList.remove('active','done'); ln&&ln.classList.remove('done');
      if(i<state.step){c.classList.add('done');c.innerHTML='✓';l.classList.add('done');ln&&ln.classList.add('done');}
      if(i===state.step){c.classList.add('active');l.classList.add('active');}
    }
  }

  function renderWizardStep() {
    updateStepUI();
    const body=document.getElementById('wiz-body'), actions=document.getElementById('wiz-actions');
    if(!body)return;
    if(state.step===1) step1(body,actions);
    else if(state.step===2) step2(body,actions);
    else if(state.step===3) step3(body,actions);
    else if(state.step===4) step4(body,actions);
  }

  // STEP 1 — Région + Hôpital
  function step1(body, actions) {
    const regions = window._wiz_regions||[];
    body.innerHTML=`
      <div class="form-group"><label class="form-label">Choisir une région</label>
        <div class="option-grid" id="wiz-regions">
          ${regions.map(r=>`<div class="option-card ${state.region===r?'selected':''}" onclick="RendezVousModule.wSelectRegion('${r}')">
            <div class="option-card-icon">🗺️</div>
            <div class="option-card-label">${r.split('-')[0]}</div>
            <div class="option-card-sub" style="font-size:0.7rem">${r}</div>
          </div>`).join('')}
        </div>
      </div>
      <div id="wiz-hopitaux" style="display:${state.region?'block':'none'}">
        <div class="form-group" style="margin-top:14px"><label class="form-label">Choisir un hôpital</label>
          <div class="option-grid" id="wiz-hop-grid">${state.region?'<div class="loading-spinner small"><div class="spinner"></div></div>':''}</div>
        </div>
      </div>`;
    actions.innerHTML=`<button class="btn btn-outline" onclick="Modal.close()">Annuler</button>
      <button class="btn btn-primary" id="wiz-next" onclick="RendezVousModule.wNext()" ${!state.hopitalId?'disabled':''}>Suivant →</button>`;
    if(state.region) loadHopitaux(state.region);
  }

  async function loadHopitaux(region) {
    const res = await Api.Hopitaux.getByRegion(region);
    const hopitaux = res.data||[];
    const grid = document.getElementById('wiz-hop-grid');
    if(!grid)return;
    grid.innerHTML = hopitaux.map(h=>`<div class="option-card ${state.hopitalId==h.id?'selected':''}" onclick="RendezVousModule.wSelectHopital(${h.id})">
      <div class="option-card-icon">🏥</div>
      <div class="option-card-label">${h.nom}</div>
      <div class="option-card-sub">${h.type} · ${h.ville}</div>
    </div>`).join('');
  }

  function wSelectRegion(region) {
    state.region=region; state.hopitalId=null; state.medecinId=null;
    document.querySelectorAll('#wiz-regions .option-card').forEach(c=>c.classList.remove('selected'));
    event.currentTarget.classList.add('selected');
    document.getElementById('wiz-hopitaux').style.display='block';
    document.getElementById('wiz-hop-grid').innerHTML='<div class="loading-spinner small"><div class="spinner"></div></div>';
    document.getElementById('wiz-next').disabled=true;
    loadHopitaux(region);
  }

  function wSelectHopital(id) {
    state.hopitalId=id; state.medecinId=null;
    document.querySelectorAll('#wiz-hop-grid .option-card').forEach(c=>c.classList.remove('selected'));
    event.currentTarget.classList.add('selected');
    document.getElementById('wiz-next').disabled=false;
  }

  // STEP 2 — Médecin
  async function step2(body, actions) {
    body.innerHTML='<div class="loading-spinner small"><div class="spinner"></div></div>';
    actions.innerHTML=`<button class="btn btn-outline" onclick="RendezVousModule.wPrev()">← Retour</button>
      <button class="btn btn-primary" id="wiz-next" onclick="RendezVousModule.wNext()" disabled>Suivant →</button>`;
    const res = await Api.Medecins.getByHopital(state.hopitalId);
    const meds = res.data||[];
    body.innerHTML=`
      <div style="margin-bottom:12px"><span class="info-pill">🏥 ${meds[0]?.hopital_nom||''}</span></div>
      <div class="form-group"><label class="form-label">Choisir un médecin</label>
        ${meds.length ? `<div class="option-grid" style="grid-template-columns:repeat(auto-fill,minmax(200px,1fr))">
          ${meds.map(m=>`<div class="option-card ${state.medecinId==m.id?'selected':''}" onclick="RendezVousModule.wSelectMedecin(${m.id})">
            <div class="option-card-icon">👨‍⚕️</div>
            <div class="option-card-label">Dr. ${m.prenom} ${m.nom}</div>
            <div class="option-card-sub">${m.specialite}</div>
          </div>`).join('')}
        </div>` : '<p style="color:var(--text-muted)">Aucun médecin disponible.</p>'}
      </div>`;
    if(state.medecinId) document.getElementById('wiz-next').disabled=false;
  }

  function wSelectMedecin(id) {
    state.medecinId=id;
    document.querySelectorAll('#wiz-body .option-card').forEach(c=>c.classList.remove('selected'));
    event.currentTarget.classList.add('selected');
    document.getElementById('wiz-next').disabled=false;
  }

  // STEP 3 — Patient + Date + Créneaux
  async function step3(body, actions) {
    body.innerHTML='<div class="loading-spinner small"><div class="spinner"></div></div>';
    actions.innerHTML=`<button class="btn btn-outline" onclick="RendezVousModule.wPrev()">← Retour</button>
      <button class="btn btn-primary" id="wiz-next" onclick="RendezVousModule.wNext()" disabled>Récapitulatif →</button>`;
    const [pRes, mRes] = await Promise.all([Api.Patients.getAll(), Api.Medecins.getById(state.medecinId)]);
    const patients = pRes.data||[];
    const medecin  = mRes.data;
    body.innerHTML=`
      <div style="margin-bottom:14px"><span class="info-pill">👨‍⚕️ Dr. ${medecin?.prenom||''} ${medecin?.nom||''} — ${medecin?.specialite||''}</span></div>
      <div class="form-group"><label class="form-label">Patient *</label>
        <select class="form-control" id="w-pat" onchange="RendezVousModule.wCheckStep3()">
          <option value="">— Sélectionner —</option>
          ${patients.map(p=>`<option value="${p.id}" ${state.patientId==p.id?'selected':''}>${p.prenom} ${p.nom} — CIN ${p.cin||'N/A'}</option>`).join('')}
        </select>
        <div style="margin-top:6px;text-align:right">
          <button class="btn btn-outline btn-sm" onclick="RendezVousModule.wQuickPatient()">+ Nouveau patient</button>
        </div>
      </div>
      <div class="form-row">
        <div class="form-group"><label class="form-label">Date *</label>
          <input type="date" class="form-control" id="w-date" min="${todayStr()}" value="${state.date||''}" onchange="RendezVousModule.wLoadSlots()"/></div>
        <div class="form-group"><label class="form-label">Motif</label>
          <input type="text" class="form-control" id="w-motif" value="${state.motif||''}" placeholder="Ex: Douleur thoracique…"/></div>
      </div>
      <div id="w-slots-wrap" style="display:${state.date?'block':'none'}">
        <div class="form-group"><label class="form-label">Créneaux disponibles</label>
          <div class="time-slots" id="w-slots">${state.date?'<div class="loading-spinner small"><div class="spinner"></div></div>':''}</div>
        </div>
      </div>`;
    if(state.date) wLoadSlots();
    wCheckStep3();
  }

  async function wLoadSlots() {
    const dateEl = document.getElementById('w-date');
    if(!dateEl) return;
    state.date = dateEl.value;
    state.heure = null;
    const wrap = document.getElementById('w-slots-wrap');
    if(!state.date){if(wrap)wrap.style.display='none';return;}
    wrap.style.display='block';
    document.getElementById('w-slots').innerHTML='<div class="loading-spinner small"><div class="spinner"></div></div>';
    const [mRes, busyRes] = await Promise.all([
      Api.Medecins.getById(state.medecinId),
      Api.Rendezvous.getBusySlots(state.medecinId, state.date),
    ]);
    const horaires = mRes.data?.horaires||[];
    const busy     = busyRes.data||[];
    document.getElementById('w-slots').innerHTML = horaires.map(h=>`
      <div class="time-slot ${busy.includes(h)?'busy':''} ${state.heure===h?'selected':''}"
           onclick="${busy.includes(h)?'':` RendezVousModule.wSelectSlot('${h}')`}">${h}</div>`).join('');
    wCheckStep3();
  }

  function wSelectSlot(h){
    state.heure=h;
    document.querySelectorAll('.time-slot:not(.busy)').forEach(el=>el.classList.toggle('selected',el.textContent.trim()===h));
    wCheckStep3();
  }

  function wCheckStep3(){
    state.patientId = document.getElementById('w-pat')?.value||null;
    const ok = !!(state.patientId && state.date && state.heure);
    const btn = document.getElementById('wiz-next');
    if(btn) btn.disabled=!ok;
  }

  async function wQuickPatient(){
    const prev = document.getElementById('modal-body').innerHTML;
    const prevAct = document.getElementById('wiz-actions').innerHTML;
    window._wiz_prev_body    = prev;
    window._wiz_prev_actions = prevAct;
    document.getElementById('wiz-body').innerHTML=`
      <p style="font-weight:500;margin-bottom:14px">Créer un patient rapidement</p>
      <div class="form-row">
        <div class="form-group"><label class="form-label">Prénom *</label><input type="text" class="form-control" id="qp-prenom"/></div>
        <div class="form-group"><label class="form-label">Nom *</label><input type="text" class="form-control" id="qp-nom"/></div>
      </div>
      <div class="form-row">
        <div class="form-group"><label class="form-label">CIN</label><input type="text" class="form-control" id="qp-cin"/></div>
        <div class="form-group"><label class="form-label">Téléphone</label><input type="text" class="form-control" id="qp-tel"/></div>
      </div>`;
    document.getElementById('wiz-actions').innerHTML=`
      <button class="btn btn-outline" onclick="RendezVousModule.wRestoreStep3()">Annuler</button>
      <button class="btn btn-primary" onclick="RendezVousModule.wSaveQuickPatient()">Créer & choisir</button>`;
  }

  async function wSaveQuickPatient(){
    const prenom=document.getElementById('qp-prenom').value.trim();
    const nom=document.getElementById('qp-nom').value.trim();
    if(!prenom||!nom){showToast('Prénom et nom obligatoires','error');return;}
    const res=await Api.Patients.create({prenom,nom,cin:document.getElementById('qp-cin').value,telephone:document.getElementById('qp-tel').value});
    if(handleApiError(res))return;
    state.patientId=res.data.id;
    showToast(`Patient ${prenom} ${nom} créé`,'success');
    wRestoreStep3(true);
  }

  async function wRestoreStep3(reload=false){
    const body=document.getElementById('wiz-body'), actions=document.getElementById('wiz-actions');
    await step3(body,actions);
    if(reload && state.patientId){
      const sel=document.getElementById('w-pat');
      if(sel) sel.value=state.patientId;
      wCheckStep3();
    }
  }

  // STEP 4 — Résumé
  async function step4(body, actions) {
    body.innerHTML='<div class="loading-spinner small"><div class="spinner"></div></div>';
    const motifEl=document.getElementById('w-motif');
    if(motifEl) state.motif=motifEl.value.trim();
    const patEl=document.getElementById('w-pat');
    if(patEl) state.patientId=patEl.value;

    const [pRes, mRes, hRes] = await Promise.all([
      Api.Patients.getById(state.patientId),
      Api.Medecins.getById(state.medecinId),
      Api.Hopitaux.getById(state.hopitalId),
    ]);
    const p=pRes.data, m=mRes.data, h=hRes.data;
    body.innerHTML=`
      <p style="font-size:0.9rem;color:var(--text-secondary);margin-bottom:16px">Vérifiez avant de confirmer.</p>
      <div class="rdv-summary">
        ${row('Patient',    `${p?.prenom||''} ${p?.nom||''}`)}
        ${row('Médecin',    `Dr. ${m?.prenom||''} ${m?.nom||''}`)}
        ${row('Spécialité', m?.specialite||'—')}
        ${row('Hôpital',    h?.nom||'—')}
        ${row('Région',     h?.region||'—')}
        ${row('Date',       formatDate(state.date))}
        ${row('Heure',      state.heure)}
        ${row('Motif',      state.motif||'—')}
      </div>`;
    actions.innerHTML=`
      <button class="btn btn-outline" onclick="RendezVousModule.wPrev()">← Retour</button>
      <button class="btn btn-teal" onclick="RendezVousModule.wConfirm()">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
        Confirmer le rendez-vous
      </button>`;
  }

  async function wConfirm(){
    const res=await Api.Rendezvous.create({
      patient_id:state.patientId, medecin_id:state.medecinId,
      hopital_id:state.hopitalId, date_rdv:state.date,
      heure:state.heure, motif:state.motif,
    });
    if(handleApiError(res))return;
    Modal.close(); showToast('Rendez-vous réservé !','success'); render(); Router.refreshBadge();
  }

  function wNext(){
    const motifEl=document.getElementById('w-motif');
    if(motifEl) state.motif=motifEl.value.trim();
    const patEl=document.getElementById('w-pat');
    if(patEl) state.patientId=patEl.value;
    state.step++;
    renderWizardStep();
  }
  function wPrev(){ state.step--; renderWizardStep(); }

  return { render, filter, openNew, view, edit, saveEdit, confirmDelete, doDelete,
           wSelectRegion, wSelectHopital, wSelectMedecin, wLoadSlots, wSelectSlot,
           wCheckStep3, wQuickPatient, wSaveQuickPatient, wRestoreStep3,
           wNext, wPrev, wConfirm };
})();
