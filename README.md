# 🏥 MediCare — Hospital Management System

A full-stack hospital management web application built with **PHP 8 MVC**, **MySQL**, and **Vanilla JavaScript**. It allows administrators to manage hospitals, doctors, patients, and appointments through a clean and responsive SPA interface.

---

## 🚀 Tech Stack

| Layer | Technology |
|-------|-----------|
| Frontend | HTML5, CSS3, JavaScript (Vanilla, Fetch API) |
| Backend | PHP 8 (OOP, MVC pattern, PDO) |
| Database | MySQL |
| Server | Apache (XAMPP) |

---

## ✨ Features

- 📊 **Dashboard** — Overview with statistics (hospitals, doctors, patients, appointments)
- 🏥 **Hospitals** — Full CRUD with region, city, type, and capacity management
- 👨‍⚕️ **Doctors** — Manage doctors linked to hospitals with specialties
- 🧑‍💼 **Patients** — Patient records with medical info (blood type, CIN, etc.)
- 📅 **Appointments** — 4-step wizard to book appointments with busy-slot detection
- 🔌 **REST API** — Full JSON API for all modules
- 🌍 **Tunisian regions** — Localized for Tunisia (24 governorates)

---

## 🗂️ Project Structure

```
hospital-management-system-main/
├── app/
│   ├── Controllers/
│   │   ├── DashboardController.php
│   │   ├── HopitauxController.php
│   │   ├── MedecinsController.php
│   │   ├── PatientsController.php
│   │   └── RendezVousController.php
│   ├── Core/
│   │   ├── Controller.php
│   │   ├── Database.php
│   │   ├── Model.php
│   │   ├── Router.php
│   │   └── Validator.php
│   ├── Models/
│   │   ├── Hopital.php
│   │   ├── Medecin.php
│   │   ├── Patient.php
│   │   └── RendezVous.php
│   └── Views/
│       ├── layouts/main.php
│       ├── dashboard/
│       ├── hopitaux/
│       ├── medecins/
│       ├── patients/
│       └── rendezvous/
├── config/
│   └── database.php
├── public/
│   ├── index.php          ← Entry point
│   ├── css/style.css
│   └── js/
│       ├── app.js
│       ├── utils/api.js
│       └── modules/
│           ├── dashboard.js
│           ├── hopitaux.js
│           ├── medecins.js
│           ├── patients.js
│           └── rendezvous.js
├── sql/
│   └── medicare.sql       ← Database schema + test data
├── .htaccess
└── README.md
```

---

## ⚙️ Installation

### Prerequisites
- [XAMPP](https://www.apachefriends.org/) with Apache and MySQL
- PHP 8.0+

---

### Step 1 — Start XAMPP
Open the XAMPP Control Panel and start **Apache** and **MySQL**.

---

### Step 2 — Clone the repository
```bash
git clone https://github.com/ilyes-sad/hospital-management-system.git
```
Place the project folder in your XAMPP htdocs directory:
```
C:\xaammpp\htdocs\hospital-management-system-main\       (Windows)
/opt/lampp/htdocs/hospital-management-system-main/        (Linux)
/Applications/XAMPP/htdocs/hospital-management-system-main/  (Mac)
```

---

### Step 3 — Import the database
1. Open your browser → `http://localhost/phpmyadmin`
2. Click **"Import"** in the top menu
3. Click **"Choose file"** → select `sql/medicare.sql`
4. Click **"Execute"**
5. ✅ The `medicare` database is created with all tables and test data

---

### Step 4 — Configure the base URL

In `app/Views/layouts/main.php`, set the correct base URL on line 2:
```php
$baseUrl = '/hospital-management-system-main/public';
```

In `public/js/utils/api.js`, update the fallback URL on line 7:
```javascript
const BASE = (typeof BASE_URL !== 'undefined' ? BASE_URL : '/hospital-management-system-main/public') + '/api';
```

---

### Step 5 — Open the application
```
http://localhost/hospital-management-system-main/public/
```

> ⚠️ If XAMPP runs on a custom port (e.g. 90), use:
> `http://localhost:90/hospital-management-system-main/public/`

---

## 🗄️ Database Schema

```
hopitaux (id, nom, ville, region, type, lits, telephone)
    |
    | 1..N
    |
medecins (id, prenom, nom, specialite, hopital_id, telephone, email)
    |
    | N
    |
rendez_vous (id, patient_id, medecin_id, hopital_id, date_rdv, heure, motif, statut)
    |
    | N
    |
patients (id, prenom, nom, cin, date_naissance, sexe, groupe_sanguin, telephone, email, ville)
```

**N-N Relationship:** A patient can have multiple doctors (via `rendez_vous`), and a doctor can have multiple patients.

---

## 🔌 REST API Endpoints

### Hospitals
| Method | URL | Action |
|--------|-----|--------|
| GET | `/api/hopitaux` | List all hospitals |
| GET | `/api/hopitaux/{id}` | Get hospital by ID |
| GET | `/api/hopitaux/regions` | List all regions |
| GET | `/api/hopitaux/by-region?region=Tunis` | Filter by region |
| POST | `/api/hopitaux` | Create hospital |
| PUT | `/api/hopitaux/{id}` | Update hospital |
| DELETE | `/api/hopitaux/{id}` | Delete hospital |

### Doctors
| Method | URL | Action |
|--------|-----|--------|
| GET | `/api/medecins` | List all doctors |
| GET | `/api/medecins/{id}` | Get doctor by ID |
| GET | `/api/medecins/specialites` | List specialties |
| GET | `/api/medecins/by-hopital?hopital_id=1` | Filter by hospital |
| POST | `/api/medecins` | Create doctor |
| PUT | `/api/medecins/{id}` | Update doctor |
| DELETE | `/api/medecins/{id}` | Delete doctor |

### Patients
| Method | URL | Action |
|--------|-----|--------|
| GET | `/api/patients` | List all patients |
| GET | `/api/patients/{id}` | Get patient by ID |
| GET | `/api/patients/search?q=name` | Search patients |
| POST | `/api/patients` | Create patient |
| PUT | `/api/patients/{id}` | Update patient |
| DELETE | `/api/patients/{id}` | Delete patient |

### Appointments
| Method | URL | Action |
|--------|-----|--------|
| GET | `/api/rendezvous` | List all appointments |
| GET | `/api/rendezvous/{id}` | Get appointment by ID |
| GET | `/api/rendezvous/stats` | Global statistics |
| GET | `/api/rendezvous/busy-slots?medecin_id=1&date=2026-04-13` | Get busy slots |
| POST | `/api/rendezvous` | Create appointment |
| PUT | `/api/rendezvous/{id}` | Update appointment |
| DELETE | `/api/rendezvous/{id}` | Delete appointment |

---

## 🛠️ Common Issues

**500 Internal Server Error**
→ Check that MySQL is running in XAMPP
→ Check `config/database.php`: `DB_USER='root'`, `DB_PASS=''`
→ Make sure the `medicare` database has been imported

**404 on CSS/JS files**
→ Check that `$baseUrl` in `main.php` matches your folder name
→ Make sure `.htaccess` has the correct `RewriteBase`

**Redirect loop / ERR_TOO_MANY_REDIRECTS**
→ Make sure you have TWO `.htaccess` files: one at the root and one inside `public/`
→ See the `.htaccess` configuration section above

**"Access-Control-Allow-Origin" error**
→ Never open `index.html` directly via `file://`
→ Always use `http://localhost/...`



---

## 📄 License

This project is for educational purposes.
