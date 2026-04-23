// ============================================================
//  js/modules/patients.js
// ============================================================
const PatientsModule = (() => {
  async function render() {
    const area = document.getElementById('content-area');
    if (!area) return;
    area.innerHTML = '<div class="loading-spinner"><div class="spinner"></div></div>';
    const res = await Api.Patients.getAll();
    const patients = res.data||[];
    area.innerHTML = `
      <div class="section-header">
        <div><div class="section-title">Patients</div><div class="section-subtitle">${patients.length} patient(s)</div></div>
        <button class="btn btn-primary" onclick="PatientsModule.openNew()">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14M5 12h14"/></svg>
          Nouveau patient
        </button>
      </div>
      <div class="filter-bar">
        <div class="filter-input">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
          <input type="text" id="pat-q" placeholder="Nom, CIN, ville..." oninput="PatientsModule.filter()" />
        </div>
        <select class="filter-select" id="pat-sexe" onchange="PatientsModule.filter()">
          <option value="">Tous</option><option value="M">Homme</option><option value="F">Femme</option>
        </select>
      </div>
      <div class="card" style="padding:0"><div class="table-wrapper" id="pat-table">${renderTable(patients)}</div></div>`;
  }

  function renderTable(patients) {
    if (!patients.length) return `<div class="empty-state"><p>Aucun patient</p></div>`;
    return `<table><thead><tr><th>Patient</th><th>CIN</th><th>Naissance</th><th>Sexe</th><th>Ville</th><th>Groupe sg.</th><th>Tel.</th><th>RDV</th><th>Actions</th></tr></thead><tbody>
    ${patients.map(p=>`<tr>
      <td><div class="avatar-cell"><div class="avatar avatar-blue">${initials(p.prenom,p.nom)}</div><div><div class="avatar-name">${p.prenom} ${p.nom}</div><div style="font-size:0.75rem;color:var(--text-muted)">${p.email||''}</div></div></div></td>
      <td><code style="font-size:0.82rem;background:var(--bg);padding:2px 7px;border-radius:5px;border:1px solid var(--border)">${p.cin||'—'}</code></td>
      <td>${formatDate(p.date_naissance)}</td>
      <td><span class="badge ${p.sexe==='F'?'badge-info':'badge-neutral'}">${p.sexe==='M'?'Homme':p.sexe==='F'?'Femme':'—'}</span></td>
      <td>${p.ville||'—'}</td>
      <td><span class="badge" style="background:#FEF2F2;color:#991B1B">${p.groupe_sanguin||'—'}</span></td>
      <td style="font-size:0.83rem">${p.telephone||'—'}</td>
      <td><span class="badge badge-teal">${p.nb_rdv||0}</span></td>
      <td><div class="table-actions">
        <button class="tbl-btn" onclick="PatientsModule.edit(${p.id})" title="Modifier"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4z"/></svg></button>
        <button class="tbl-btn delete" onclick="PatientsModule.confirmDelete(${p.id})" title="Supprimer"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6M10 11v6M14 11v6M9 6V4h6v2"/></svg></button>
      </div></td>
    </tr>`).join('')}
    </tbody></table>`;
  }

  async function filter() {
    const q=document.getElementById('pat-q')?.value.trim()||'';
    const sexe=document.getElementById('pat-sexe')?.value||'';
    const res= q ? await Api.Patients.search(q) : await Api.Patients.getAll();
    let data=(res.data||[]);
    if(sexe) data=data.filter(p=>p.sexe===sexe);
    document.getElementById('pat-table').innerHTML=renderTable(data);
  }

  function openNew() { Modal.open('Nouveau patient', form(null), 'modal-lg'); }
  async function edit(id) {
    Modal.loading('Modifier');
    const res=await Api.Patients.getById(id);
    Modal.open('Modifier le patient', form(res.data), 'modal-lg');
  }

  function form(p) {
    const v=p||{};
    return `
      <div class="form-row">
        <div class="form-group"><label class="form-label">Prenom *</label><input type="text" class="form-control" id="pf-prenom" value="${v.prenom||''}"/></div>
        <div class="form-group"><label class="form-label">Nom *</label><input type="text" class="form-control" id="pf-nom" value="${v.nom||''}"/></div>
      </div>
      <div class="form-row">
        <div class="form-group"><label class="form-label">CIN</label><input type="text" class="form-control" id="pf-cin" value="${v.cin||''}"/></div>
        <div class="form-group"><label class="form-label">Date de naissance</label><input type="text" class="form-control" id="pf-dn" placeholder="AAAA-MM-JJ" value="${v.date_naissance||''}"/></div>
      </div>
      <div class="form-row">
        <div class="form-group"><label class="form-label">Sexe</label>
          <select class="form-control" id="pf-sexe"><option value="">—</option>
            <option value="M" ${v.sexe==='M'?'selected':''}>Homme</option>
            <option value="F" ${v.sexe==='F'?'selected':''}>Femme</option>
          </select></div>
        <div class="form-group"><label class="form-label">Groupe sanguin</label>
          <select class="form-control" id="pf-gs">
            ${['','A+','A-','B+','B-','AB+','AB-','O+','O-'].map(g=>`<option value="${g}" ${v.groupe_sanguin===g?'selected':''}>${g||'—'}</option>`).join('')}
          </select></div>
      </div>
      <div class="form-row">
        <div class="form-group"><label class="form-label">Telephone</label><input type="text" class="form-control" id="pf-tel" value="${v.telephone||''}"/></div>
        <div class="form-group"><label class="form-label">Email</label><input type="text" class="form-control" id="pf-email" value="${v.email||''}"/></div>
      </div>
      <div class="form-group"><label class="form-label">Ville</label><input type="text" class="form-control" id="pf-ville" value="${v.ville||''}"/></div>
      <div class="form-actions">
        <button class="btn btn-outline" onclick="Modal.close()">Annuler</button>
        <button class="btn btn-primary" onclick="PatientsModule.save('${v.id||''}')">Enregistrer</button>
      </div>`;
  }

  async function save(id) {
    const prenom=document.getElementById('pf-prenom').value.trim();
    const nom=document.getElementById('pf-nom').value.trim();
    if(!prenom||!nom){showToast('Prenom et nom obligatoires','error');return;}
    const data={prenom,nom,cin:document.getElementById('pf-cin').value.trim(),date_naissance:document.getElementById('pf-dn').value,sexe:document.getElementById('pf-sexe').value,groupe_sanguin:document.getElementById('pf-gs').value,telephone:document.getElementById('pf-tel').value.trim(),email:document.getElementById('pf-email').value.trim(),ville:document.getElementById('pf-ville').value.trim()};
    const res=id ? await Api.Patients.update(id,data) : await Api.Patients.create(data);
    if(handleApiError(res))return;
    Modal.close(); showToast(id?'Patient mis a jour':'Patient cree','success'); render();
  }

  async function confirmDelete(id) {
    Modal.loading('Supprimer');
    const res=await Api.Patients.getById(id);
    const p=res.data;
    Modal.open('Supprimer le patient',`<p style="color:var(--text-secondary);margin-bottom:20px">Supprimer <strong>${p?.prenom} ${p?.nom}</strong> et tous ses rendez-vous ?</p>
      <div class="form-actions" style="border:none;padding:0;margin:0">
        <button class="btn btn-outline" onclick="Modal.close()">Annuler</button>
        <button class="btn btn-danger" onclick="PatientsModule.doDelete(${id})">Supprimer</button>
      </div>`);
  }

  async function doDelete(id) {
    const res=await Api.Patients.delete(id);
    if(handleApiError(res))return;
    Modal.close(); showToast('Patient supprime','error'); render();
  }

  return { render, filter, openNew, edit, save, confirmDelete, doDelete };
})();
