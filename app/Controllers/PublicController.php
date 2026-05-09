<?php
// ============================================================
//  app/Controllers/PublicController.php
//  Front office controller
// ============================================================

class PublicController extends Controller
{
    private Medecin $medecinModel;
    private Hopital $hopitalModel;
    private RendezVous $rdvModel;
    private Patient $patientModel;

    public function __construct()
    {
        $this->medecinModel = new Medecin();
        $this->hopitalModel = new Hopital();
        $this->rdvModel = new RendezVous();
        $this->patientModel = new Patient();
    }

    public function index(): void
    {
        $hopitaux = $this->hopitalModel->findAll();
        $specialites = $this->medecinModel->getSpecialites();
        $medecins = $this->medecinModel->findAllWithHopital();
        
        $this->view('public/home', [
            'pageTitle' => 'MediCare - Accueil',
            'hopitaux' => $hopitaux,
            'specialites' => $specialites,
            'medecins' => $medecins,
        ]);
    }

    public function doctors(): void
    {
        $hopitalId = $_GET['hopital'] ?? null;
        $specialite = $_GET['specialite'] ?? null;
        
        if ($hopitalId) {
            $medecins = $this->medecinModel->findByHopital((int) $hopitalId);
        } elseif ($specialite) {
            $medecins = $this->medecinModel->findBySpecialite($specialite);
        } else {
            $medecins = $this->medecinModel->findAllWithHopital();
        }
        
        $hopitaux = $this->hopitalModel->findAll();
        $specialites = $this->medecinModel->getSpecialites();
        
        $this->view('public/doctors', [
            'pageTitle' => 'Nos Médecins',
            'medecins' => $medecins,
            'hopitaux' => $hopitaux,
            'specialites' => $specialites,
            'selectedHopital' => $hopitalId,
            'selectedSpecialite' => $specialite,
        ]);
    }

    public function book(): void
    {
        $medecinId = $_GET['medecin'] ?? null;
        
        $medecins = $this->medecinModel->findAllWithHopital();
        $hopitaux = $this->hopitalModel->findAll();
        $specialites = $this->medecinModel->getSpecialites();
        
        $selectedMedecin = null;
        if ($medecinId) {
            $selectedMedecin = $this->medecinModel->findByIdWithHopital((int) $medecinId);
        }
        
        $error = $_SESSION['error'] ?? null;
        unset($_SESSION['error']);
        
        $old = $_SESSION['old'] ?? [];
        unset($_SESSION['old']);
        
        $this->view('public/book', [
            'pageTitle' => 'Prendre un RDV',
            'medecins' => $medecins,
            'hopitaux' => $hopitaux,
            'specialites' => $specialites,
            'selectedMedecin' => $selectedMedecin,
            'error' => $error,
            'old' => $old,
        ]);
    }

    public function storeRdv(): void
    {
        $data = [
            'patient_nom' => $_POST['patient_nom'] ?? '',
            'patient_prenom' => $_POST['patient_prenom'] ?? '',
            'patient_cin' => $_POST['patient_cin'] ?? '',
            'patient_telephone' => $_POST['patient_telephone'] ?? '',
            'patient_email' => $_POST['patient_email'] ?? '',
            'patient_date_naissance' => $_POST['patient_date_naissance'] ?? null,
            'patient_sexe' => $_POST['patient_sexe'] ?? null,
            'patient_ville' => $_POST['patient_ville'] ?? null,
            'patient_groupe_sanguin' => $_POST['patient_groupe_sanguin'] ?? null,
            'medecin_id' => $_POST['medecin_id'] ?? '',
            'hopital_id' => $_POST['hopital_id'] ?? '',
            'date_rdv' => $_POST['date_rdv'] ?? '',
            'heure' => $_POST['heure'] ?? '',
            'motif' => $_POST['motif'] ?? '',
        ];

        $validated = $this->validate($data, [
            'patient_nom' => 'required|max:80',
            'patient_prenom' => 'required|max:80',
            'patient_cin' => 'required|max:20',
            'patient_telephone' => 'required|phone',
            'patient_email' => 'email',
            'medecin_id' => 'required|integer',
            'hopital_id' => 'required|integer',
            'date_rdv' => 'required',
            'heure' => 'required',
            'motif' => 'max:255',
        ]);

        if ($validated === false) {
            $_SESSION['old'] = $data;
            $_SESSION['error'] = 'Veuillez corriger les erreurs dans le formulaire.';
            $redirectUrl = '/hospital-management-system-main/public/book';
            if (!empty($_GET)) {
                $redirectUrl .= '?' . http_build_query($_GET);
            }
            header('Location: ' . $redirectUrl);
            exit;
        }

$cin = $validated['patient_cin'];
        
        if (!preg_match('/^\d{8}$/', $cin)) {
            $_SESSION['old'] = $data;
            $_SESSION['error'] = 'Le CIN doit contenir exactement 8 chiffres (ex: 12345678).';
            header('Location: /hospital-management-system-main/public/book?' . http_build_query($_GET));
            exit;
        }
        
        $existingPatient = $this->patientModel->findByCin($cin);
        
        if ($existingPatient) {
            $inputNom = mb_strtolower(trim($validated['patient_nom']), 'UTF-8');
            $inputPrenom = mb_strtolower(trim($validated['patient_prenom']), 'UTF-8');
            $existingNom = mb_strtolower(trim($existingPatient['nom']), 'UTF-8');
            $existingPrenom = mb_strtolower(trim($existingPatient['prenom']), 'UTF-8');
            
            if ($inputNom !== $existingNom || $inputPrenom !== $existingPrenom) {
                $_SESSION['old'] = $data;
                $_SESSION['error'] = 'Ce CIN est déjà enregistré avec un autre nom (' . htmlspecialchars($existingPatient['prenom'] . ' ' . $existingPatient['nom']) . '). Veuillez utiliser votre CIN correct ou contacter l\'administration.';
                header('Location: /hospital-management-system-main/public/book?' . http_build_query($_GET));
                exit;
            }
            $patientId = $existingPatient['id'];
        } else {
            $patientId = $this->patientModel->create([
                'nom' => $validated['patient_nom'],
                'prenom' => $validated['patient_prenom'],
                'cin' => $cin,
                'telephone' => $validated['patient_telephone'],
                'email' => $validated['patient_email'] ?? null,
                'date_naissance' => $validated['patient_date_naissance'] ?? null,
                'sexe' => $validated['patient_sexe'] ?? null,
                'ville' => $validated['patient_ville'] ?? null,
                'groupe_sanguin' => $validated['patient_groupe_sanguin'] ?? null,
            ]);
        }

        $busySlots = $this->rdvModel->getBusySlots((int) $validated['medecin_id'], $validated['date_rdv']);
        if (in_array($validated['heure'], $busySlots)) {
            $_SESSION['error'] = 'Ce créneau horaire est déjà réservé. Veuillez choisir un autre horaire.';
            $_SESSION['old'] = $validated;
            header('Location: /hospital-management-system-main/public/book?' . http_build_query([
                'medecin' => $validated['medecin_id'],
            ]));
            exit;
        }

        $rdvId = $this->rdvModel->create([
            'patient_id' => $patientId,
            'medecin_id' => $validated['medecin_id'],
            'hopital_id' => $validated['hopital_id'],
            'date_rdv' => $validated['date_rdv'],
            'heure' => $validated['heure'],
            'motif' => $validated['motif'] ?? null,
            'statut' => 'en attente',
        ]);

        $this->redirect('/hospital-management-system-main/public/appointment/' . $rdvId);
    }

    public function appointment(string $id): void
    {
        $rdv = $this->rdvModel->findByIdWithDetails((int) $id);
        
        if (!$rdv) {
            $this->redirect('/hospital-management-system-main/public/portal');
        }
        
        $patient = $this->patientModel->findById((int) $rdv['patient_id']);
        $medecin = $this->medecinModel->findByIdWithHopital((int) $rdv['medecin_id']);
        $hopital = $this->hopitalModel->findById((int) $rdv['hopital_id']);
        
        $this->view('public/appointment', [
            'pageTitle' => 'Confirmation RDV',
            'rdv' => $rdv,
            'patient' => $patient,
            'medecin' => $medecin,
            'hopital' => $hopital,
        ]);
    }

    public function portal(): void
    {
        $this->view('public/portal', [
            'pageTitle' => 'Espace Patient',
        ]);
    }

    public function portalSearch(): void
    {
        $cin = $_POST['cin'] ?? '';
        
        if (empty($cin)) {
            $this->oldInput = ['cin' => $cin];
            $this->redirect('/hospital-management-system-main/public/public/portal');
        }
        
        $patient = $this->patientModel->findByCin($cin);
        
        if (!$patient) {
            $this->errors = ['cin' => 'Aucun patient trouvé avec ce CIN'];
            $this->oldInput = ['cin' => $cin];
            $this->redirect('/hospital-management-system-main/public/public/portal');
        }
        
        $rdvs = $this->rdvModel->findByPatient((int) $patient['id']);
        
        $this->view('public/portal', [
            'pageTitle' => 'Espace Patient',
            'patient' => $patient,
            'rdvs' => $rdvs,
        ]);
    }

    public function apiDoctors(): void
    {
        $hopitalId = $_GET['hopital_id'] ?? null;
        $specialite = $_GET['specialite'] ?? null;
        
        if ($hopitalId) {
            $medecins = $this->medecinModel->findByHopital((int) $hopitalId);
        } elseif ($specialite) {
            $medecins = $this->medecinModel->findBySpecialite($specialite);
        } else {
            $medecins = $this->medecinModel->findAllWithHopital();
        }
        
        $this->sendSuccess($medecins);
    }

    public function apiBusySlots(): void
    {
        $medecinId = $_GET['medecin_id'] ?? '';
        $date = $_GET['date'] ?? '';
        
        if (empty($medecinId) || empty($date)) {
            $this->sendError('Paramètres requis');
        }
        
        $slots = $this->rdvModel->getBusySlots((int) $medecinId, $date);
        $this->sendSuccess($slots);
    }

    public function map(): void
    {
        $hopitaux = $this->hopitalModel->findAllWithCoords();
        
        $this->view('public/map', [
            'pageTitle' => 'Carte des Hôpitaux',
            'hopitaux' => $hopitaux,
        ]);
    }

    private function firstError(): string
    {
        return !empty($this->errors) ? reset($this->errors) : 'Erreur';
    }
}