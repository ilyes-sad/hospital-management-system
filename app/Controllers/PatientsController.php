<?php
// ============================================================
//  app/Controllers/PatientsController.php
// ============================================================

class PatientsController extends Controller
{
    private Patient $model;

    public function __construct()
    {
        $this->model = new Patient();
    }

    // ---- WEB PAGES ----

    public function index(): void
    {
        $patients = $this->model->findAllWithRdvCount();

        $this->layout('main', 'patients/index', [
            'pageTitle'    => 'Patients',
            'pageSub'      => 'Dossiers patients',
            'activeModule' => 'patients',
            'patients'     => $patients,
            'errors'       => $this->getErrors(),
            'old'          => $this->oldInput,
        ]);
    }

    public function create(): void
    {
        $this->layout('main', 'patients/create', [
            'pageTitle'    => 'Nouveau patient',
            'pageSub'      => 'Ajouter un patient',
            'activeModule' => 'patients',
            'errors'       => $this->getErrors(),
            'old'          => $this->oldInput,
        ]);
    }

    public function store(): void
    {
        $data = [
            'prenom'          => $_POST['prenom']          ?? '',
            'nom'             => $_POST['nom']             ?? '',
            'cin'             => $_POST['cin']             ?? '',
            'date_naissance'  => $_POST['date_naissance']  ?? '',
            'sexe'            => $_POST['sexe']            ?? '',
            'groupe_sanguin'  => $_POST['groupe_sanguin']  ?? '',
            'telephone'       => $_POST['telephone']       ?? '',
            'email'           => $_POST['email']           ?? '',
            'ville'           => $_POST['ville']           ?? '',
        ];

        $validated = $this->validate($data, [
            'prenom'         => 'required|max:80',
            'nom'            => 'required|max:80',
            'cin'            => 'max:20|unique:patients,cin',
            'date_naissance' => 'date',
            'sexe'           => 'in:M,F',
            'groupe_sanguin' => 'in:A+,A-,B+,B-,AB+,AB-,O+,O-',
            'telephone'      => 'phone',
            'email'          => 'email',
            'ville'          => 'max:100',
        ]);

        if ($validated === false) {
           $this->redirect('/patients');
        }

        foreach (['cin', 'date_naissance', 'sexe', 'groupe_sanguin', 'telephone', 'email', 'ville'] as $field) {
            if (empty($validated[$field])) {
                $validated[$field] = null;
            }
        }

        $this->model->create($validated);
        $this->redirect('/patients');
    }

    public function edit(string $id): void
    {
        $patient = $this->model->findById((int) $id);

        if (!$patient) {
            $this->redirect('/patients');
        }

        $this->layout('main', 'patients/edit', [
            'pageTitle'    => 'Modifier le patient',
            'pageSub'      => "{$patient['prenom']} {$patient['nom']}",
            'activeModule' => 'patients',
            'patient'      => $patient,
            'errors'       => $this->getErrors(),
            'old'          => $this->oldInput,
        ]);
    }

    public function update(string $id): void
    {
        $data = [
            'prenom'         => $_POST['prenom']         ?? '',
            'nom'            => $_POST['nom']            ?? '',
            'cin'            => $_POST['cin']            ?? '',
            'date_naissance' => $_POST['date_naissance'] ?? '',
            'sexe'           => $_POST['sexe']           ?? '',
            'groupe_sanguin' => $_POST['groupe_sanguin'] ?? '',
            'telephone'      => $_POST['telephone']      ?? '',
            'email'          => $_POST['email']          ?? '',
            'ville'          => $_POST['ville']          ?? '',
        ];

        $validated = $this->validate($data, [
            'prenom'         => 'required|max:80',
            'nom'            => 'required|max:80',
            'cin'            => "max:20|unique:patients,cin,{$id}",
            'date_naissance' => 'date',
            'sexe'           => 'in:M,F',
            'groupe_sanguin' => 'in:A+,A-,B+,B-,AB+,AB-,O+,O-',
            'telephone'      => 'phone',
            'email'          => 'email',
            'ville'          => 'max:100',
        ]);

        if ($validated === false) {
            $this->redirect("/patients/edit/{$id}");
        }

        foreach (['cin', 'date_naissance', 'sexe', 'groupe_sanguin', 'telephone', 'email', 'ville'] as $field) {
            if (empty($validated[$field])) {
                $validated[$field] = null;
            }
        }

        $this->model->update((int) $id, $validated);
        $this->redirect('/patients');
    }

    public function delete(string $id): void
    {
        $this->model->delete((int) $id);
        $this->redirect('/patients');
    }

    // ---- API ENDPOINTS ----

    public function apiIndex(): void
    {
        $this->sendSuccess($this->model->findAllWithRdvCount());
    }

    public function apiShow(string $id): void
    {
        $p = $this->model->findById((int) $id);
        if (!$p) {
            $this->sendError('Patient introuvable', 404);
        }
        $this->sendSuccess($p);
    }

    public function apiSearch(): void
    {
        $q = $_GET['search'] ?? '';
        if (empty($q)) {
            $this->sendSuccess($this->model->findAllWithRdvCount());
        } else {
            $this->sendSuccess($this->model->search($q));
        }
    }

    public function apiStore(): void
    {
        $data = $this->getJsonBody();
        if (empty($data)) {
            $this->sendError('Body JSON vide - donnees non recues');
            return;
        }
        error_log('apiStore data: ' . json_encode($data)); // check apache/php error log

        $validated = $this->validate($data, [
            'prenom' => 'required|max:80',
            'nom'    => 'required|max:80',
            'cin'    => 'max:20|unique:patients,cin',
            'email'  => 'email',
        ]);

        if ($validated === false) {
            $this->sendError($this->firstError());
        }

        foreach (['cin', 'date_naissance', 'sexe', 'groupe_sanguin', 'telephone', 'email', 'ville'] as $field) {
            if (!isset($validated[$field]) || $validated[$field] === '') {
                $validated[$field] = null;
            }
        }

        $id = $this->model->create($validated);
        $this->sendSuccess($this->model->findById($id), 'Patient cree');
    }

    public function apiUpdate(string $id): void
    {
        $data = $this->getJsonBody();

        $validated = $this->validate($data, [
            'prenom' => 'required|max:80',
            'nom'    => 'required|max:80',
            'cin'    => "max:20|unique:patients,cin,{$id}",
            'email'  => 'email',
        ]);

        if ($validated === false) {
            $this->sendError($this->firstError());
        }

        foreach (['cin', 'date_naissance', 'sexe', 'groupe_sanguin', 'telephone', 'email', 'ville'] as $field) {
            if (!isset($validated[$field]) || $validated[$field] === '') {
                $validated[$field] = null;
            }
        }

        $this->model->update((int) $id, $validated);
        $this->sendSuccess($this->model->findById((int) $id), 'Patient mis a jour');
    }

    public function apiDelete(string $id): void
    {
        if (!$this->model->findById((int) $id)) {
            $this->sendError('Patient introuvable', 404);
        }
        $this->model->delete((int) $id);
        $this->sendSuccess(null, 'Patient supprime');
    }

    private function firstError(): string
    {
        return !empty($this->errors) ? reset($this->errors) : 'Erreur de validation';
    }
}
