// ============================================================
//  js/utils/api.js
//  Couche d'accès à l'API — fetch() vers le backend MVC
// ============================================================

const BASE = (typeof BASE_URL !== 'undefined' ? BASE_URL : '/hospital-management-system-main/public') + '/api';


const Api = {

  async get(endpoint, params = {}) {
    const url = new URL(`${BASE}/${endpoint}`, window.location.origin);
    Object.entries(params).forEach(([k, v]) => v !== '' && v !== undefined && url.searchParams.set(k, v));
    const res = await fetch(url);
    if (!res.ok) throw new Error(`HTTP ${res.status}`);
    return res.json();
  },

  async post(endpoint, body) {
    const res = await fetch(`${BASE}/${endpoint}`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(body),
    });
    if (!res.ok) throw new Error(`HTTP ${res.status}`);
    return res.json();
  },

  async put(endpoint, id, body) {
    const res = await fetch(`${BASE}/${endpoint}/${id}`, {
      method: 'PUT',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(body),
    });
    if (!res.ok) throw new Error(`HTTP ${res.status}`);
    return res.json();
  },

  async delete(endpoint, id) {
    const res = await fetch(`${BASE}/${endpoint}/${id}`, { method: 'DELETE' });
    if (!res.ok) throw new Error(`HTTP ${res.status}`);
    return res.json();
  },

Hopitaux: {
    getAll:      ()       => Api.get('hopitaux'),
    getById:     (id)     => Api.get(`hopitaux/${id}`),
    search:      (q)      => Api.get('hopitaux/search', { search: q }),
    getRegions:  ()       => Api.get('hopitaux/regions'),
    getByRegion: (region) => Api.get('hopitaux/by-region', { region }),
    create:      (data)   => Api.post('hopitaux', data),
    update:      (id, d)  => Api.put('hopitaux', id, d),
    delete:      (id)    => Api.delete('hopitaux', id),
  },

  Medecins: {
    getAll:        ()        => Api.get('medecins'),
    getById:       (id)      => Api.get(`medecins/${id}`),
    search:        (q)        => Api.get('medecins/search', { search: q }),
    getByHopital:  (hid)     => Api.get('medecins/by-hopital', { hopital_id: hid }),
    getSpecialites:()        => Api.get('medecins/specialites'),
    create:        (data)    => Api.post('medecins', data),
    update:        (id, d)   => Api.put('medecins', id, d),
    delete:        (id)      => Api.delete('medecins', id),
  },

  Patients: {
    getAll:   ()       => Api.get('patients'),
    getById:  (id)     => Api.get(`patients/${id}`),
    search:   (q)      => Api.get('patients/search', { search: q }),
    create:   (data)   => Api.post('patients', data),
    update:   (id, d)  => Api.put('patients', id, d),
    delete:   (id)     => Api.delete('patients', id),
  },

  Rendezvous: {
    getAll:      (filters = {}) => Api.get('rendezvous', filters),
    getById:     (id)           => Api.get(`rendezvous/${id}`),
    getStats:    ()             => Api.get('rendezvous/stats'),
    getBusySlots:(mid, date)    => Api.get('rendezvous/busy-slots', { medecin_id: mid, date }),
    create:      (data)         => Api.post('rendezvous', data),
    update:      (id, d)        => Api.put('rendezvous', id, d),
    delete:      (id)           => Api.delete('rendezvous', id),
  },
};
