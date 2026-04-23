// ============================================================
//  js/modules/hopitaux.js
// ============================================================
const HopitauxModule = (() => {
const REGIONS=['Ariana','Béja','Ben Arous','Bizerte','Gabès','Gafsa','Jendouba','Kairouan','Kasserine','Kébili','Kef','Mahdia','Manouba','Médenine','Monastir','Nabeul','Sfax','Sidi Bouzid','Siliana','Sousse','Tataouine','Tozeur','Tunis','Zaghouan'];
  async function render(){
    const area=document.getElementById('content-area');
    if (!area) return;
    area.innerHTML='<div class="loading-spinner"><div class="spinner"></div></div>';
    const res=await Api.Hopitaux.getAll();
    const hopitaux=res.data||[];
    area.innerHTML=`
      <div class="section-header">
        <div><div class="section-title">Hopitaux</div><div class="section-subtitle">${hopitaux.length} etablissement(s)</div></div>
        <button class="btn btn-primary" onclick="HopitauxModule.openNew()">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14M5 12h14"/></svg> Ajouter
        </button>
      </div>
      <div class="filter-bar">
        <div class="filter-input"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
          <input type="text" id="hop-q" placeholder="Nom, ville..." oninput="HopitauxModule.filter()"/>
        </div>
        <select class="filter-select" id="hop-type" onchange="HopitauxModule.filter()">
          <option value="">Tous types</option><option>CHU</option><option>Public</option><option>Prive</option>
        </select>
      </div>
      <div class="card" style="padding:0"><div class="table-wrapper" id="hop-table">${renderTable(hopitaux)}</div></div>`;
  }

  function renderTable(h){
    if(!h.length) return `<div class="empty-state"><p>Aucun hopital</p></div>`;
    const tc={CHU:'badge-info',Public:'badge-success',Prive:'badge-warning'};
    return `<table><thead><tr><th>Nom</th><th>Region</th><th>Ville</th><th>Type</th><th>Lits</th><th>Telephone</th><th>Medecins</th><th>Actions</th></tr></thead><tbody>
    ${h.map(x=>`<tr>
      <td style="font-weight:600">${x.nom}</td>
      <td style="font-size:0.83rem;color:var(--text-secondary)">${x.region}</td>
      <td>${x.ville}</td>
      <td><span class="badge ${tc[x.type]||'badge-neutral'}">${x.type}</span></td>
      <td>${x.lits||'—'}</td>
      <td style="font-size:0.83rem">${x.telephone||'—'}</td>
      <td><span class="badge badge-teal">${x.nb_medecins||0}</span></td>
      <td><div class="table-actions">
        <button class="tbl-btn" onclick="HopitauxModule.edit(${x.id})"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4z"/></svg></button>
        <button class="tbl-btn delete" onclick="HopitauxModule.confirmDelete(${x.id})"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6M10 11v6M14 11v6M9 6V4h6v2"/></svg></button>
      </div></td>
    </tr>`).join('')}
    </tbody></table>`;
  }

  async function filter(){
    const q=document.getElementById('hop-q')?.value.toLowerCase()||'';
    const type=document.getElementById('hop-type')?.value||'';
    const res=await Api.Hopitaux.getAll();
    let data=(res.data||[]);
    if(q) data=data.filter(h=>`${h.nom} ${h.ville}`.toLowerCase().includes(q));
    if(type) data=data.filter(h=>h.type===type);
    document.getElementById('hop-table').innerHTML=renderTable(data);
  }

  function openNew(){Modal.open('Nouvel hopital',form(null));}
  async function edit(id){
    Modal.loading('Modifier');
    const res=await Api.Hopitaux.getById(id);
    Modal.open("Modifier l'hopital",form(res.data));
  }

  function form(h){
    const v=h||{};
    return `
      <div class="form-group"><label class="form-label">Nom *</label><input type="text" class="form-control" id="hf-nom" value="${v.nom||''}"/></div>
      <div class="form-row">
        <div class="form-group"><label class="form-label">Region *</label>
          <select class="form-control" id="hf-region">
            <option value="">— Selectionner —</option>
            ${REGIONS.map(r=>`<option value="${r}" ${v.region===r?'selected':''}>${r}</option>`).join('')}
          </select></div>
        <div class="form-group"><label class="form-label">Ville *</label><input type="text" class="form-control" id="hf-ville" value="${v.ville||''}"/></div>
      </div>
      <div class="form-row">
        <div class="form-group"><label class="form-label">Type</label>
          <select class="form-control" id="hf-type">
            ${['CHU','Public','Prive'].map(t=>`<option value="${t}" ${v.type===t?'selected':''}>${t}</option>`).join('')}
          </select></div>
        <div class="form-group"><label class="form-label">Lits</label><input type="number" class="form-control" id="hf-lits" value="${v.lits||''}" min="0"/></div>
      </div>
      <div class="form-group"><label class="form-label">Telephone</label><input type="text" class="form-control" id="hf-tel" value="${v.telephone||''}"/></div>
      <div class="form-actions">
        <button class="btn btn-outline" onclick="Modal.close()">Annuler</button>
        <button class="btn btn-primary" onclick="HopitauxModule.save('${v.id||''}')">Enregistrer</button>
      </div>`;
  }

  async function save(id){
    const nom=document.getElementById('hf-nom').value.trim();
    const region=document.getElementById('hf-region').value;
    const ville=document.getElementById('hf-ville').value.trim();
    if(!nom||!region||!ville){showToast('Champs obligatoires manquants','error');return;}
    const data={nom,region,ville,type:document.getElementById('hf-type').value,lits:parseInt(document.getElementById('hf-lits').value)||0,telephone:document.getElementById('hf-tel').value.trim()};
    const res=id?await Api.Hopitaux.update(id,data):await Api.Hopitaux.create(data);
    if(handleApiError(res))return;
    Modal.close(); showToast(id?'Hopital mis a jour':'Hopital ajoute','success'); render();
  }

  async function confirmDelete(id){
    Modal.loading('Supprimer');
    const res=await Api.Hopitaux.getById(id);
    const h=res.data;
    Modal.open("Supprimer l'hopital",`<p style="color:var(--text-secondary);margin-bottom:20px">Supprimer <strong>${h?.nom}</strong> ? Tous les medecins et RDV associes seront supprimes.</p>
      <div class="form-actions" style="border:none;padding:0;margin:0">
        <button class="btn btn-outline" onclick="Modal.close()">Annuler</button>
        <button class="btn btn-danger" onclick="HopitauxModule.doDelete(${id})">Supprimer</button>
      </div>`);
  }

  async function doDelete(id){
    const res=await Api.Hopitaux.delete(id);
    if(handleApiError(res))return;
    Modal.close(); showToast('Hopital supprime','error'); render();
  }

  return{render,filter,openNew,edit,save,confirmDelete,doDelete};
})();
