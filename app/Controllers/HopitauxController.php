<?php
// ============================================================
//  app/Controllers/HopitauxController.php
// ============================================================

class HopitauxController extends Controller
{
    private Hopital $model;

    public function __construct()
    {
        $this->model = new Hopital();
    }

    // ---- WEB PAGES ----

    public function index(): void
    {
        $hopitaux = $this->model->findAllWithStats();

        $this->layout('main', 'hopitaux/index', [
            'pageTitle'    => 'Hopitaux',
            'pageSub'      => 'Etablissements de sante',
            'activeModule' => 'hopitaux',
            'hopitaux'     => $hopitaux,
            'errors'       => $this->getErrors(),
            'old'          => $this->oldInput,
        ]);
    }

    public function create(): void
    {
        $this->layout('main', 'hopitaux/create', [
            'pageTitle'    => 'Nouvel hopital',
            'pageSub'      => 'Ajouter un etablissement',
            'activeModule' => 'hopitaux',
            'errors'       => $this->getErrors(),
            'old'          => $this->oldInput,
        ]);
    }

    public function store(): void
    {
        $data = [
            'nom'       => $_POST['nom']       ?? '',
            'ville'     => $_POST['ville']     ?? '',
            'region'    => $_POST['region']    ?? '',
            'type'      => $_POST['type']      ?? 'Public',
            'lits'      => $_POST['lits']      ?? '0',
            'telephone' => $_POST['telephone'] ?? '',
        ];

        $validated = $this->validate($data, [
            'nom'       => 'required|max:150',
            'ville'     => 'required|max:100',
            'region'    => 'required|max:150',
            'type'      => 'required|in:CHU,Public,Prive',
            'lits'      => 'numeric',
            'telephone' => 'phone',
        ]);

        if ($validated === false) {
            $this->redirect('/hopitaux/create');
        }

        $validated['lits'] = (int) $validated['lits'];
        if (empty($validated['telephone'])) {
            $validated['telephone'] = null;
        }

        $this->model->create($validated);
        $this->redirect('/hopitaux');
    }

    public function edit(string $id): void
    {
        $hopital = $this->model->findById((int) $id);

        if (!$hopital) {
            $this->redirect('/hopitaux');
        }

        $this->layout('main', 'hopitaux/edit', [
            'pageTitle'    => "Modifier l'hopital",
            'pageSub'      => $hopital['nom'],
            'activeModule' => 'hopitaux',
            'hopital'      => $hopital,
            'errors'       => $this->getErrors(),
            'old'          => $this->oldInput,
        ]);
    }

    public function update(string $id): void
    {
        $data = [
            'nom'       => $_POST['nom']       ?? '',
            'ville'     => $_POST['ville']     ?? '',
            'region'    => $_POST['region']    ?? '',
            'type'      => $_POST['type']      ?? 'Public',
            'lits'      => $_POST['lits']      ?? '0',
            'telephone' => $_POST['telephone'] ?? '',
        ];

        $validated = $this->validate($data, [
            'nom'       => 'required|max:150',
            'ville'     => 'required|max:100',
            'region'    => 'required|max:150',
            'type'      => 'required|in:CHU,Public,Prive',
            'lits'      => 'numeric',
            'telephone' => 'phone',
        ]);

        if ($validated === false) {
            $this->redirect("/hopitaux/edit/{$id}");
        }

        $validated['lits'] = (int) $validated['lits'];
        if (empty($validated['telephone'])) {
            $validated['telephone'] = null;
        }

        $this->model->update((int) $id, $validated);
        $this->redirect('/hopitaux');
    }

    public function delete(string $id): void
    {
        $this->model->delete((int) $id);
        $this->redirect('/hopitaux');
    }

    // ---- API ENDPOINTS (for AJAX) ----

    public function apiIndex(): void
    {
        $this->sendSuccess($this->model->findAllWithStats());
    }

    public function apiShow(string $id): void
    {
        $hopital = $this->model->findById((int) $id);
        if (!$hopital) {
            $this->sendError('Hopital introuvable', 404);
        }
        $this->sendSuccess($hopital);
    }

    public function apiRegions(): void
    {
        $this->sendSuccess($this->model->getRegions());
    }

    public function apiByRegion(): void
    {
        $region = $_GET['region'] ?? '';
        if (empty($region)) {
            $this->sendError('Parametre region requis');
        }
        $this->sendSuccess($this->model->findByRegion($region));
    }

    public function apiStore(): void
    {
        $data = $this->getJsonBody();

        $validated = $this->validate($data, [
            'nom'    => 'required|max:150',
            'ville'  => 'required|max:100',
            'region' => 'required|max:150',
            'type'   => 'in:CHU,Public,Prive',
            'lits'   => 'numeric',
        ]);

        if ($validated === false) {
            $this->sendError($this->firstError());
        }

        $validated['lits'] = (int) ($validated['lits'] ?? 0);
        $id = $this->model->create($validated);
        $this->sendSuccess($this->model->findById($id), 'Hopital cree');
    }

    public function apiUpdate(string $id): void
    {
        $data = $this->getJsonBody();

        $validated = $this->validate($data, [
            'nom'    => 'required|max:150',
            'ville'  => 'required|max:100',
            'region' => 'required|max:150',
            'type'   => 'in:CHU,Public,Prive',
            'lits'   => 'numeric',
        ]);

        if ($validated === false) {
            $this->sendError($this->firstError());
        }

        $validated['lits'] = (int) ($validated['lits'] ?? 0);
        $this->model->update((int) $id, $validated);
        $this->sendSuccess($this->model->findById((int) $id), 'Hopital mis a jour');
    }

    public function apiDelete(string $id): void
    {
        if (!$this->model->findById((int) $id)) {
            $this->sendError('Hopital introuvable', 404);
        }
        $this->model->delete((int) $id);
        $this->sendSuccess(null, 'Hopital supprime');
    }

    private function firstError(): string
    {
        return !empty($this->errors) ? reset($this->errors) : 'Erreur de validation';
    }
}
