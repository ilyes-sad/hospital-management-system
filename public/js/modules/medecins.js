// ============================================================
//  js/modules/medecins.js
// ============================================================
const MedecinsModule = (() => {
  const ALL_H=['08:00','09:00','10:00','11:00','12:00','13:00','14:00','15:00','16:00','17:00','18:00'];

  async function render() {
    const area=document.getElementById('content-area');
    if (!area) return;
    area.innerHTML='<div class="loading-spinner"><div class="spinner"></div></div>';
    const [mRes,sRes]=await Promise.all([Api.Medecins.getAll(),Api.Medecins.getSpecialites()]);
    const medecins=mRes.data||[], specs=sRes.data||[];
    area.innerHTML=`
      <div class="section-header">
        <div><div class="section-title">Corps medical</div><div class="section-subtitle">${medecins.length} medecin(s) · ${specs.length} specialites</div></div>
        <button class="btn btn-primary" onclick="MedecinsModule.openNew()">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14M5 12h14"/></svg> Ajouter medecin
        </button>
      </div>
      <div class="filter-bar">
        <div class="filter-input"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
          <input type="text" id="med-q" placeholder="Nom, specialite..." oninput="MedecinsModule.filter()"/>
        </div>
        <select class="filter-select" id="med-spec" onchange="MedecinsModule.filter()">
          <option value="">Toutes specialites</option>${specs.map(s=>`<option>${s}</option>`).join('')}
        </select>
      </div>
      <div class="card" style="padding:0"><div class="table-wrapper" id="med-table">${renderTable(medecins)}</div></div>`;
  }

  function renderTable(medecins) {
    if(!medecins.length) return `<div class="empty-state"><p>Aucun medecin</p></div>`;
    return `<table><thead><tr><th>Medecin</th><th>Specialite</th><th>Hopital</th><th>Telephone</th><th>Email</th><th>Creneaux</th><th>Actions</th></tr></thead><tbody>
    ${medecins.map(m=>`<tr>
      <td><div class="avatar-cell"><div class="avatar avatar-teal">${initials(m.prenom,m.nom)}</div><span class="avatar-name">Dr. ${m.prenom} ${m.nom}</span></div></td>
      <td><span class="badge badge-teal">${m.specialite}</span></td>
      <td>${m.hopital_nom||'—'}</td>
      <td style="font-size:0.83rem">${m.telephone||'—'}</td>
      <td style="font-size:0.82rem;color:var(--text-secondary)">${m.email||'—'}</td>
      <td><span class="badge badge-neutral">${(m.horaires||[]).length}</span></td>
      <td><div class="table-actions">
        <button class="tbl-btn" onclick="MedecinsModule.edit(${m.id})"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4z"/></svg></button>
        <button class="tbl-btn delete" onclick="MedecinsModule.confirmDelete(${m.id})"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6M10 11v6M14 11v6M9 6V4h6v2"/></svg></button>
      </div></td>
    </tr>`).join('')}
    </tbody></table>`;
  }

  async function filter() {
    const q=document.getElementById('med-q')?.value.toLowerCase()||'';
    const spec=document.getElementById('med-spec')?.value||'';
    const res=await Api.Medecins.getAll();
    let data=(res.data||[]);
    if(q) data=data.filter(m=>`${m.prenom} ${m.nom} ${m.specialite}`.toLowerCase().includes(q));
    if(spec) data=data.filter(m=>m.specialite===spec);
    document.getElementById('med-table').innerHTML=renderTable(data);
  }

  function openNew(){Modal.open('Nouveau medecin',form(null),'modal-lg');}
  async function edit(id){
    Modal.loading('Modifier');
    const [mRes,hRes]=await Promise.all([Api.Medecins.getById(id),Api.Hopitaux.getAll()]);
    Modal.open('Modifier le medecin',form(mRes.data,hRes.data),'modal-lg');
  }

  function form(m,hopitaux){
    const v=m||{}; const hops=hopitaux||[];
    return `
      <div class="form-row">
        <div class="form-group"><label class="form-label">Prenom *</label><input type="text" class="form-control" id="mf-prenom" value="${v.prenom||''}"/></div>
        <div class="form-group"><label class="form-label">Nom *</label><input type="text" class="form-control" id="mf-nom" value="${v.nom||''}"/></div>
      </div>
      <div class="form-row">
        <div class="form-group"><label class="form-label">Specialite *</label><input type="text" class="form-control" id="mf-spec" value="${v.specialite||''}"/></div>
        <div class="form-group"><label class="form-label">Hopital *</label>
          <select class="form-control" id="mf-hop">
            <option value="">— Selectionner —</option>
            ${hops.map(h=>`<option value="${h.id}" ${v.hopital_id==h.id?'selected':''}>${h.nom}</option>`).join('')}
          </select></div>
      </div>
      <div class="form-row">
        <div class="form-group"><label class="form-label">Telephone</label><input type="text" class="form-control" id="mf-tel" value="${v.telephone||''}"/></div>
        <div class="form-group"><label class="form-label">Email</label><input type="text" class="form-control" id="mf-email" value="${v.email||''}"/></div>
      </div>
      <div class="form-group"><label class="form-label">Creneaux horaires</label>
        <div style="display:flex;flex-wrap:wrap;gap:8px;margin-top:4px">
          ${ALL_H.map(h=>`<label style="display:inline-flex;align-items:center;gap:5px;cursor:pointer;font-size:0.85rem;background:var(--bg);border:1px solid var(--border);padding:5px 11px;border-radius:7px">
            <input type="checkbox" name="hor" value="${h}" ${(v.horaires||[]).includes(h)?'checked':''}/> ${h}
          </label>`).join('')}
        </div>
      </div>
      <div class="form-actions">
        <button class="btn btn-outline" onclick="Modal.close()">Annuler</button>
        <button class="btn btn-primary" onclick="MedecinsModule.save('${v.id||''}')">Enregistrer</button>
      </div>`;
  }

  async function save(id){
    const prenom=document.getElementById('mf-prenom').value.trim();
    const nom=document.getElementById('mf-nom').value.trim();
    const spec=document.getElementById('mf-spec').value.trim();
    const hopId=document.getElementById('mf-hop').value;
    if(!prenom||!nom||!spec||!hopId){showToast('Champs obligatoires manquants','error');return;}
    const horaires=[...document.querySelectorAll('input[name="hor"]:checked')].map(c=>c.value);
    const data={prenom,nom,specialite:spec,hopital_id:hopId,telephone:document.getElementById('mf-tel').value.trim(),email:document.getElementById('mf-email').value.trim(),horaires};
    const res=id?await Api.Medecins.update(id,data):await Api.Medecins.create(data);
    if(handleApiError(res))return;
    Modal.close(); showToast(id?'Medecin mis a jour':'Medecin ajoute','success'); render();
  }

  async function confirmDelete(id){
    Modal.loading('Supprimer');
    const res=await Api.Medecins.getById(id);
    const m=res.data;
    Modal.open('Supprimer le medecin',`<p style="color:var(--text-secondary);margin-bottom:20px">Supprimer <strong>Dr. ${m?.prenom} ${m?.nom}</strong> ?</p>
      <div class="form-actions" style="border:none;padding:0;margin:0">
        <button class="btn btn-outline" onclick="Modal.close()">Annuler</button>
        <button class="btn btn-danger" onclick="MedecinsModule.doDelete(${id})">Supprimer</button>
      </div>`);
  }

  async function doDelete(id){
    const res=await Api.Medecins.delete(id);
    if(handleApiError(res))return;
    Modal.close(); showToast('Medecin supprime','error'); render();
  }

  return{render,filter,openNew,edit,save,confirmDelete,doDelete};
})();
