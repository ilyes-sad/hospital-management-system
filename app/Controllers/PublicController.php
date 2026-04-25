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
        
        $this->view('public/book', [
            'pageTitle' => 'Prendre un RDV',
            'medecins' => $medecins,
            'hopitaux' => $hopitaux,
            'specialites' => $specialites,
            'selectedMedecin' => $selectedMedecin,
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
            $this->redirect('/hospital-management-system-main/public/public/book?' . http_build_query($_GET));
        }

        $cin = $validated['patient_cin'];
        $patient = $this->patientModel->findByCin($cin);
        
        if (!$patient) {
            $patientId = $this->patientModel->create([
                'nom' => $validated['patient_nom'],
                'prenom' => $validated['patient_prenom'],
                'cin' => $cin,
                'telephone' => $validated['patient_telephone'],
                'email' => $validated['patient_email'] ?? null,
            ]);
        } else {
            $patientId = $patient['id'];
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

        $this->redirect('/hospital-management-system-main/public/public/appointment/' . $rdvId);
    }

    public function appointment(string $id): void
    {
        $rdv = $this->rdvModel->findById((int) $id);
        
        if (!$rdv) {
            $this->redirect('/hospital-management-system-main/public/public/portal');
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

    private function firstError(): string
    {
        return !empty($this->errors) ? reset($this->errors) : 'Erreur';
    }
}