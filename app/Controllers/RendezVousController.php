<?php
// ============================================================
//  app/Controllers/RendezVousController.php
// ============================================================

class RendezVousController extends Controller
{
    private RendezVous $model;

    public function __construct()
    {
        $this->model = new RendezVous();
    }

    // ---- WEB PAGES ----

    public function index(): void
    {
        $rdvs    = $this->model->findAllWithFilters();
        $regions = (new Hopital())->getRegions();

        $this->layout('main', 'rendezvous/index', [
            'pageTitle'    => 'Rendez-vous',
            'pageSub'      => 'Gestion des rendez-vous (N-N)',
            'activeModule' => 'rendezvous',
            'rdvs'         => $rdvs,
            'regions'      => $regions,
            'errors'       => $this->getErrors(),
            'old'          => $this->oldInput,
        ]);
    }

    public function create(): void
    {
        $regions  = (new Hopital())->getRegions();
        $patients = (new Patient())->findAll();
        $medecins = (new Medecin())->findAllWithHopital();

        $this->layout('main', 'rendezvous/create', [
            'pageTitle'    => 'Nouveau rendez-vous',
            'pageSub'      => 'Reserver un rendez-vous',
            'activeModule' => 'rendezvous',
            'regions'      => $regions,
            'patients'     => $patients,
            'medecins'     => $medecins,
            'errors'       => $this->getErrors(),
            'old'          => $this->oldInput,
        ]);
    }

    public function store(): void
    {
        $data = [
            'patient_id' => $_POST['patient_id'] ?? '',
            'medecin_id' => $_POST['medecin_id'] ?? '',
            'hopital_id' => $_POST['hopital_id'] ?? '',
            'date_rdv'   => $_POST['date_rdv']   ?? '',
            'heure'      => $_POST['heure']      ?? '',
            'motif'      => $_POST['motif']      ?? '',
        ];

        $validated = $this->validate($data, [
            'patient_id' => 'required|integer',
            'medecin_id' => 'required|integer',
            'hopital_id' => 'required|integer',
            'date_rdv'   => 'required|date',
            'heure'      => 'required|regex:/^\d{2}:\d{2}$/',
            'motif'      => 'max:255',
        ]);

        if ($validated === false) {
            $this->redirect('/rendezvous/create');
        }

        // Server-side duplicate slot check
        if ($this->model->isSlotBusy(
            (int) $validated['medecin_id'],
            $validated['date_rdv'],
            $validated['heure']
        )) {
            $this->errors['heure'] = 'Ce creneau est deja pris pour ce medecin a cette date.';
            $this->redirect('/rendezvous/create');
        }

        $validated['motif'] = empty($validated['motif']) ? null : $validated['motif'];

        $this->model->createRdv($validated);
        $this->redirect('/rendezvous');
    }

    public function edit(string $id): void
    {
        $rdv      = $this->model->findByIdWithDetails((int) $id);
        $patients = (new Patient())->findAll();
        $medecins = (new Medecin())->findAllWithHopital();

        if (!$rdv) {
            $this->redirect('/rendezvous');
        }

        $this->layout('main', 'rendezvous/edit', [
            'pageTitle'    => 'Modifier le rendez-vous',
            'pageSub'      => "#{$id}",
            'activeModule' => 'rendezvous',
            'rdv'          => $rdv,
            'patients'     => $patients,
            'medecins'     => $medecins,
            'errors'       => $this->getErrors(),
            'old'          => $this->oldInput,
        ]);
    }

    public function update(string $id): void
    {
        $data = [
            'patient_id' => $_POST['patient_id'] ?? '',
            'medecin_id' => $_POST['medecin_id'] ?? '',
            'hopital_id' => $_POST['hopital_id'] ?? '',
            'date_rdv'   => $_POST['date_rdv']   ?? '',
            'heure'      => $_POST['heure']      ?? '',
            'motif'      => $_POST['motif']      ?? '',
            'statut'     => $_POST['statut']     ?? 'en attente',
        ];

        $validated = $this->validate($data, [
            'patient_id' => 'required|integer',
            'medecin_id' => 'required|integer',
            'hopital_id' => 'required|integer',
            'date_rdv'   => 'required|date',
            'heure'      => 'required|regex:/^\d{2}:\d{2}$/',
            'motif'      => 'max:255',
            'statut'     => 'required|in:en attente,confirme,annule,termine',
        ]);

        if ($validated === false) {
            $this->redirect("/rendezvous/edit/{$id}");
        }

        // Server-side duplicate slot check (exclude current RDV)
        $existing = $this->model->findByIdWithDetails((int) $id);
        if ($existing && (
            $existing['medecin_id'] != $validated['medecin_id'] ||
            $existing['date_rdv']   !== $validated['date_rdv']  ||
            $existing['heure']      !== $validated['heure']
        )) {
            if ($this->model->isSlotBusy(
                (int) $validated['medecin_id'],
                $validated['date_rdv'],
                $validated['heure']
            )) {
                $this->errors['heure'] = 'Ce creneau est deja pris pour ce medecin a cette date.';
                $this->redirect("/rendezvous/edit/{$id}");
            }
        }

        $validated['motif'] = empty($validated['motif']) ? null : $validated['motif'];

        $this->model->update((int) $id, $validated);
        $this->redirect('/rendezvous');
    }

    public function delete(string $id): void
    {
        $this->model->delete((int) $id);
        $this->redirect('/rendezvous');
    }

    // ---- API ENDPOINTS ----

    public function apiIndex(): void
    {
        $filters = array_filter([
            'statut'     => $_GET['statut']     ?? '',
            'region'     => $_GET['region']     ?? '',
            'patient_id' => $_GET['patient_id'] ?? '',
            'medecin_id' => $_GET['medecin_id'] ?? '',
            'search'     => $_GET['search']     ?? '',
        ]);

        $this->sendSuccess($this->model->findAllWithFilters($filters));
    }

    public function apiShow(string $id): void
    {
        $r = $this->model->findByIdWithDetails((int) $id);
        if (!$r) {
            $this->sendError('Rendez-vous introuvable', 404);
        }
        $this->sendSuccess($r);
    }

    public function apiStats(): void
    {
        $this->sendSuccess($this->model->getStats());
    }

    public function apiBusySlots(): void
    {
        $medecinId = $_GET['medecin_id'] ?? '';
        $date      = $_GET['date']       ?? '';

        if (empty($medecinId) || empty($date)) {
            $this->sendError('Parametres medecin_id et date requis');
        }

        $this->sendSuccess($this->model->getBusySlots((int) $medecinId, $date));
    }

    public function apiStore(): void
    {
        $data = $this->getJsonBody();

        $validated = $this->validate($data, [
            'patient_id' => 'required|integer',
            'medecin_id' => 'required|integer',
            'hopital_id' => 'required|integer',
            'date_rdv'   => 'required|date',
            'heure'      => 'required|regex:/^\d{2}:\d{2}$/',
            'motif'      => 'max:255',
        ]);

        if ($validated === false) {
            $this->sendError($this->firstError());
        }

        // Server-side duplicate slot check
        if ($this->model->isSlotBusy(
            (int) $validated['medecin_id'],
            $validated['date_rdv'],
            $validated['heure']
        )) {
            $this->sendError('Ce creneau est deja pris pour ce medecin a cette date.');
        }

        $validated['motif'] = $validated['motif'] ?? null;

        $id = $this->model->createRdv($validated);
        $this->sendSuccess($this->model->findByIdWithDetails($id), 'Rendez-vous cree');
    }

    public function apiUpdate(string $id): void
    {
        $data = $this->getJsonBody();

        $validated = $this->validate($data, [
            'patient_id' => 'required|integer',
            'medecin_id' => 'required|integer',
            'hopital_id' => 'required|integer',
            'date_rdv'   => 'required|date',
            'heure'      => 'required|regex:/^\d{2}:\d{2}$/',
            'motif'      => 'max:255',
            'statut'     => 'in:en attente,confirme,annule,termine',
        ]);

        if ($validated === false) {
            $this->sendError($this->firstError());
        }

        $validated['motif']  = $validated['motif']  ?? null;
        $validated['statut'] = $validated['statut'] ?? 'en attente';

        $this->model->update((int) $id, $validated);
        $this->sendSuccess($this->model->findByIdWithDetails((int) $id), 'Rendez-vous mis a jour');
    }

    public function apiDelete(string $id): void
    {
        if (!$this->model->findByIdWithDetails((int) $id)) {
            $this->sendError('Rendez-vous introuvable', 404);
        }
        $this->model->delete((int) $id);
        $this->sendSuccess(null, 'Rendez-vous supprime');
    }

    private function firstError(): string
    {
        return !empty($this->errors) ? reset($this->errors) : 'Erreur de validation';
    }
}
