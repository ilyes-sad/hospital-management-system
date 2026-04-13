# MediCare — Guide d'installation XAMPP

## Stack technique
- **Frontend** : HTML5 + CSS3 + JavaScript (vanilla, fetch API)
- **Backend** : PHP 8 (OOP, classes Repository, PDO)
- **Base de données** : MySQL (via phpMyAdmin)
- **Serveur** : Apache (XAMPP)

---

## Installation en 4 étapes

### Étape 1 — Démarrer XAMPP
1. Ouvrir le panneau de contrôle XAMPP
2. Démarrer **Apache** et **MySQL**

### Étape 2 — Créer la base de données
1. Ouvrir votre navigateur → `http://localhost/phpmyadmin`
2. Cliquer sur **"Importer"** dans la barre du haut
3. Cliquer sur **"Choisir un fichier"** → sélectionner `sql/medicare.sql`
4. Cliquer sur **"Exécuter"**
5. ✅ La base `medicare` est créée avec toutes les tables et données de test

### Étape 3 — Copier le projet dans htdocs
Copier tout le dossier `medapp2/` dans :
```
C:\xampp\htdocs\medapp2\         (Windows)
/opt/lampp/htdocs/medapp2/       (Linux)
/Applications/XAMPP/htdocs/medapp2/  (Mac)
```

### Étape 4 — Ouvrir l'application
Dans votre navigateur : `http://localhost/medapp2/`

---

## Structure du projet
```
medapp2/
├── index.html                  ← Page principale (SPA)
├── css/
│   └── style.css               ← Thème médical bleu/teal
├── js/
│   ├── utils/
│   │   └── api.js              ← Client fetch() vers PHP
│   ├── modules/
│   │   ├── dashboard.js
│   │   ├── rendezvous.js       ← Module 1 (wizard 4 étapes)
│   │   └── all_modules.js      ← Patients, Médecins, Hôpitaux
│   └── app.js                  ← Router + Modal + Toast
├── api/
│   ├── hopitaux.php            ← CRUD Hôpitaux (OOP + PDO)
│   ├── medecins.php            ← CRUD Médecins + horaires
│   ├── patients.php            ← CRUD Patients
│   └── rendezvous.php          ← Jointure N-N (Module 1)
├── config/
│   ├── database.php            ← Singleton PDO
│   └── api_helper.php          ← Headers + helpers JSON
└── sql/
    └── medicare.sql            ← Schéma + données de test
```

---

## Modèle de données (Diagramme)

```
hopitaux (id, nom, ville, region, type, lits, telephone)
    |
    | 1..N
    |
medecins (id, prenom, nom, specialite, hopital_id, telephone, email)
    |                |
    |                | 1..N
    |                |
    |        horaires_medecin (id, medecin_id, heure)
    |
    | N
    |
rendez_vous (id, patient_id, medecin_id, hopital_id, date_rdv, heure, motif, statut)
    |                                                      [TABLE DE JOINTURE N-N]
    | N
    |
patients (id, prenom, nom, cin, date_naissance, sexe, groupe_sanguin, telephone, email, ville)
```

**Relation N-N :** Un patient peut avoir plusieurs médecins (via rendez_vous), et un médecin peut avoir plusieurs patients.

---

## API REST — Endpoints

| Méthode | URL | Action |
|---------|-----|--------|
| GET | `api/rendezvous.php` | Liste tous les RDV (avec jointures) |
| GET | `api/rendezvous.php?id=1` | Un RDV par ID |
| GET | `api/rendezvous.php?stats=1` | Statistiques globales |
| GET | `api/rendezvous.php?busy_slots=1&medecin_id=1&date=2026-03-25` | Créneaux pris |
| POST | `api/rendezvous.php` | Créer un RDV |
| PUT | `api/rendezvous.php?id=1` | Modifier un RDV |
| DELETE | `api/rendezvous.php?id=1` | Supprimer un RDV |

Même structure pour `hopitaux.php`, `medecins.php`, `patients.php`.

---

## Problèmes fréquents

**"Connexion DB échouée"**
→ Vérifier que MySQL est démarré dans XAMPP
→ Vérifier `config/database.php` : DB_USER='root', DB_PASS=''

**Page blanche ou erreur 404**
→ Vérifier que le dossier est bien dans `htdocs/`
→ URL correcte : `http://localhost/medapp2/`

**"Access-Control-Allow-Origin"**
→ Normal si vous ouvrez `index.html` directement (file://)
→ Toujours passer par `http://localhost/...`
