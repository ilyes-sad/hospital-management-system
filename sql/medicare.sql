-- ============================================================
--  medicare.sql
--  Importer dans phpMyAdmin : onglet "Importer" > choisir ce fichier
-- ============================================================

CREATE DATABASE IF NOT EXISTS medicare CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE medicare;

-- -------------------------------------------------------
-- TABLE : hopitaux
-- -------------------------------------------------------
CREATE TABLE IF NOT EXISTS hopitaux (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    nom         VARCHAR(150) NOT NULL,
    ville       VARCHAR(100) NOT NULL,
    region      VARCHAR(150) NOT NULL,
    type        ENUM('CHU','Public','Privé') DEFAULT 'Public',
    lits        INT DEFAULT 0,
    telephone   VARCHAR(20),
    created_at  DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- -------------------------------------------------------
-- TABLE : medecins
-- -------------------------------------------------------
CREATE TABLE IF NOT EXISTS medecins (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    prenom      VARCHAR(80)  NOT NULL,
    nom         VARCHAR(80)  NOT NULL,
    specialite  VARCHAR(100) NOT NULL,
    hopital_id  INT NOT NULL,
    telephone   VARCHAR(20),
    email       VARCHAR(150),
    created_at  DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (hopital_id) REFERENCES hopitaux(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- -------------------------------------------------------
-- TABLE : horaires_medecin
-- -------------------------------------------------------
CREATE TABLE IF NOT EXISTS horaires_medecin (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    medecin_id  INT NOT NULL,
    heure       VARCHAR(5) NOT NULL,
    FOREIGN KEY (medecin_id) REFERENCES medecins(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- -------------------------------------------------------
-- TABLE : patients
-- -------------------------------------------------------
CREATE TABLE IF NOT EXISTS patients (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    prenom          VARCHAR(80)  NOT NULL,
    nom             VARCHAR(80)  NOT NULL,
    cin             VARCHAR(20)  UNIQUE,
    date_naissance  DATE,
    sexe            ENUM('M','F'),
    groupe_sanguin  VARCHAR(5),
    telephone       VARCHAR(20),
    email           VARCHAR(150),
    ville           VARCHAR(100),
    created_at      DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- -------------------------------------------------------
-- TABLE : rendez_vous  (table de JOINTURE N-N)
--   Patient N ---- N Médecin  via  RendezVous
-- -------------------------------------------------------
CREATE TABLE IF NOT EXISTS rendez_vous (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    patient_id  INT NOT NULL,
    medecin_id  INT NOT NULL,
    hopital_id  INT NOT NULL,
    date_rdv    DATE NOT NULL,
    heure       VARCHAR(5) NOT NULL,
    motif       VARCHAR(255),
    statut      ENUM('en attente','confirmé','annulé','terminé') DEFAULT 'en attente',
    created_at  DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id) REFERENCES patients(id)  ON DELETE CASCADE,
    FOREIGN KEY (medecin_id) REFERENCES medecins(id)  ON DELETE CASCADE,
    FOREIGN KEY (hopital_id) REFERENCES hopitaux(id)  ON DELETE CASCADE,
    UNIQUE KEY unique_slot (medecin_id, date_rdv, heure)
) ENGINE=InnoDB;

-- ============================================================
--  DONNÉES DE TEST
-- ============================================================

INSERT INTO hopitaux (nom, ville, region, type, lits, telephone) VALUES
('CHU Ibn Sina',          'Rabat',       'Rabat-Salé-Kénitra',        'CHU',    850, '0537 67 28 71'),
('Hôpital Avicenne',      'Casablanca',  'Casablanca-Settat',          'Public', 420, '0522 48 20 20'),
('CHU Mohammed VI',       'Marrakech',   'Marrakech-Safi',             'CHU',    700, '0524 30 99 99'),
('Hôpital Al Ghassani',   'Fès',         'Fès-Meknès',                 'Public', 390, '0535 65 83 50'),
('CHU Hassan II',         'Fès',         'Fès-Meknès',                 'CHU',    960, '0535 61 85 00'),
('Clinique Al Farabi',    'Oujda',       'Oriental',                   'Privé',  120, '0536 68 84 00');

INSERT INTO medecins (prenom, nom, specialite, hopital_id, telephone, email) VALUES
('Ahmed',   'Benali',    'Cardiologie',   1, '0661 12 34 56', 'a.benali@ibnsina.ma'),
('Fatima',  'Chraibi',   'Pédiatrie',     1, '0662 23 45 67', 'f.chraibi@ibnsina.ma'),
('Youssef', 'El Fassi',  'Neurologie',    2, '0663 34 56 78', 'y.fassi@avicenne.ma'),
('Nadia',   'Hajji',     'Dermatologie',  2, '0664 45 67 89', 'n.hajji@avicenne.ma'),
('Omar',    'Tazi',      'Orthopédie',    3, '0665 56 78 90', 'o.tazi@chu6.ma'),
('Salma',   'Benkiran',  'Gynécologie',   3, '0666 67 89 01', 's.benkiran@chu6.ma'),
('Khalid',  'Amrani',    'Ophtalmologie', 4, '0667 78 90 12', 'k.amrani@alghassani.ma'),
('Zineb',   'Ouali',     'Radiologie',    5, '0668 89 01 23', 'z.ouali@chuhassan.ma');

INSERT INTO horaires_medecin (medecin_id, heure) VALUES
(1,'08:00'),(1,'09:00'),(1,'10:00'),(1,'11:00'),(1,'14:00'),(1,'15:00'),(1,'16:00'),
(2,'09:00'),(2,'10:00'),(2,'11:00'),(2,'15:00'),(2,'16:00'),
(3,'08:00'),(3,'09:00'),(3,'14:00'),(3,'15:00'),(3,'16:00'),(3,'17:00'),
(4,'10:00'),(4,'11:00'),(4,'12:00'),(4,'15:00'),(4,'16:00'),
(5,'08:00'),(5,'09:00'),(5,'10:00'),(5,'14:00'),(5,'15:00'),
(6,'09:00'),(6,'11:00'),(6,'14:00'),(6,'16:00'),
(7,'08:00'),(7,'09:00'),(7,'10:00'),(7,'11:00'),(7,'15:00'),(7,'16:00'),
(8,'09:00'),(8,'10:00'),(8,'14:00'),(8,'15:00'),(8,'16:00'),(8,'17:00');

INSERT INTO patients (prenom, nom, cin, date_naissance, sexe, groupe_sanguin, telephone, email, ville) VALUES
('Mohammed', 'Alaoui',     'AB123456', '1985-03-12', 'M', 'A+',  '0612 11 22 33', 'm.alaoui@gmail.com',    'Rabat'),
('Aicha',    'Mansouri',   'CD234567', '1992-07-25', 'F', 'O+',  '0623 22 33 44', 'a.mansouri@gmail.com',  'Casablanca'),
('Karim',    'Berrada',    'EF345678', '1978-11-08', 'M', 'B-',  '0634 33 44 55', 'k.berrada@yahoo.fr',    'Fès'),
('Hafsa',    'Ezzahraoui', 'GH456789', '2001-05-17', 'F', 'AB+', '0645 44 55 66', 'h.ezz@gmail.com',       'Marrakech'),
('Rachid',   'Moussaoui',  'IJ567890', '1965-09-30', 'M', 'A-',  '0656 55 66 77', 'r.moussaoui@hotmail.com','Oujda');

INSERT INTO rendez_vous (patient_id, medecin_id, hopital_id, date_rdv, heure, motif, statut) VALUES
(1, 1, 1, '2026-03-25', '09:00', 'Consultation cardiaque',   'confirmé'),
(2, 3, 2, '2026-03-26', '14:00', 'Maux de tête persistants', 'en attente'),
(3, 5, 3, '2026-03-24', '10:00', 'Douleur genou droit',      'confirmé'),
(4, 6, 3, '2026-03-27', '11:00', 'Suivi grossesse',          'en attente'),
(1, 2, 1, '2026-04-01', '15:00', 'Vaccination enfant',       'confirmé');
