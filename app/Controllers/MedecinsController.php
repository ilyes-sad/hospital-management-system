<?php
// ============================================================
//  app/Controllers/MedecinsController.php
// ============================================================

class MedecinsController extends Controller
{
    private Medecin $model;

    public function __construct()
    {
        $this->model = new Medecin();
    }

    // ---- WEB PAGES ----

    public function index(): void
    {
        $medecins = $this->model->findAllWithHopital();
        $specs    = $this->model->getSpecialites();

        $this->layout('main', 'medecins/index', [
            'pageTitle'    => 'Medecins',
            'pageSub'      => 'Corps medical',
            'activeModule' => 'medecins',
            'medecins'     => $medecins,
            'specs'        => $specs,
            'errors'       => $this->getErrors(),
            'old'          => $this->oldInput,
        ]);
    }

    public function create(): void
    {
        $hopitaux = (new Hopital())->findAllWithStats();

        $this->layout('main', 'medecins/create', [
            'pageTitle'    => 'Nouveau medecin',
            'pageSub'      => 'Ajouter un medecin',
            'activeModule' => 'medecins',
            'hopitaux'     => $hopitaux,
            'errors'       => $this->getErrors(),
            'old'          => $this->oldInput,
        ]);
    }

    public function store(): void
    {
        $data = [
            'prenom'     => $_POST['prenom']     ?? '',
            'nom'        => $_POST['nom']        ?? '',
            'specialite' => $_POST['specialite'] ?? '',
            'hopital_id' => $_POST['hopital_id'] ?? '',
            'telephone'  => $_POST['telephone']  ?? '',
            'email'      => $_POST['email']      ?? '',
            'horaires'   => $_POST['horaires']   ?? [],
        ];

        $validated = $this->validate($data, [
            'prenom'     => 'required|max:80',
            'nom'        => 'required|max:80',
            'specialite' => 'required|max:100',
            'hopital_id' => 'required|integer',
            'telephone'  => 'phone',
            'email'      => 'email',
        ]);

        if ($validated === false) {
            $this->redirect('/medecins/create');
        }

        if (empty($validated['telephone'])) $validated['telephone'] = null;
        if (empty($validated['email']))     $validated['email']     = null;
        if (!is_array($validated['horaires'])) $validated['horaires'] = [];

        $this->model->createWithHoraires($validated);
        $this->redirect('/medecins');
    }

    public function edit(string $id): void
    {
        $medecin  = $this->model->findByIdWithHopital((int) $id);
        $hopitaux = (new Hopital())->findAllWithStats();

        if (!$medecin) {
            $this->redirect('/medecins');
        }

        $this->layout('main', 'medecins/edit', [
            'pageTitle'    => 'Modifier le medecin',
            'pageSub'      => "Dr. {$medecin['prenom']} {$medecin['nom']}",
            'activeModule' => 'medecins',
            'medecin'      => $medecin,
            'hopitaux'     => $hopitaux,
            'errors'       => $this->getErrors(),
            'old'          => $this->oldInput,
        ]);
    }

    public function update(string $id): void
    {
        $data = [
            'prenom'     => $_POST['prenom']     ?? '',
            'nom'        => $_POST['nom']        ?? '',
            'specialite' => $_POST['specialite'] ?? '',
            'hopital_id' => $_POST['hopital_id'] ?? '',
            'telephone'  => $_POST['telephone']  ?? '',
            'email'      => $_POST['email']      ?? '',
            'horaires'   => $_POST['horaires']   ?? [],
        ];

        $validated = $this->validate($data, [
            'prenom'     => 'required|max:80',
            'nom'        => 'required|max:80',
            'specialite' => 'required|max:100',
            'hopital_id' => 'required|integer',
            'telephone'  => 'phone',
            'email'      => 'email',
        ]);

        if ($validated === false) {
            $this->redirect("/medecins/edit/{$id}");
        }

        if (empty($validated['telephone'])) $validated['telephone'] = null;
        if (empty($validated['email']))     $validated['email']     = null;
        if (!is_array($validated['horaires'])) $validated['horaires'] = [];

        $this->model->updateWithHoraires((int) $id, $validated);
        $this->redirect('/medecins');
    }

    public function delete(string $id): void
    {
        $this->model->delete((int) $id);
        $this->redirect('/medecins');
    }

    // ---- API ENDPOINTS ----

    public function apiIndex(): void
    {
        $this->sendSuccess($this->model->findAllWithHopital());
    }

    public function apiShow(string $id): void
    {
        $med = $this->model->findByIdWithHopital((int) $id);
        if (!$med) {
            $this->sendError('Medecin introuvable', 404);
        }
        $this->sendSuccess($med);
    }

    public function apiByHopital(): void
    {
        $hid = $_GET['hopital_id'] ?? '';
        if (empty($hid)) {
            $this->sendError('Parametre hopital_id requis');
        }
        $this->sendSuccess($this->model->findByHopital((int) $hid));
    }

    public function apiSpecialites(): void
    {
        $this->sendSuccess($this->model->getSpecialites());
    }

    public function apiStore(): void
    {
        $data = $this->getJsonBody();

        $validated = $this->validate($data, [
            'prenom'     => 'required|max:80',
            'nom'        => 'required|max:80',
            'specialite' => 'required|max:100',
            'hopital_id' => 'required|integer',
            'telephone'  => 'phone',
            'email'      => 'email',
        ]);

        if ($validated === false) {
            $this->sendError($this->firstError());
        }

        if (empty($validated['telephone'])) $validated['telephone'] = null;
        if (empty($validated['email']))     $validated['email']     = null;

        $id = $this->model->createWithHoraires($validated);
        $this->sendSuccess($this->model->findByIdWithHopital($id), 'Medecin ajoute');
    }

    public function apiUpdate(string $id): void
    {
        $data = $this->getJsonBody();

        $validated = $this->validate($data, [
            'prenom'     => 'required|max:80',
            'nom'        => 'required|max:80',
            'specialite' => 'required|max:100',
            'hopital_id' => 'required|integer',
            'telephone'  => 'phone',
            'email'      => 'email',
        ]);

        if ($validated === false) {
            $this->sendError($this->firstError());
        }

        if (empty($validated['telephone'])) $validated['telephone'] = null;
        if (empty($validated['email']))     $validated['email']     = null;

        $this->model->updateWithHoraires((int) $id, $validated);
        $this->sendSuccess($this->model->findByIdWithHopital((int) $id), 'Medecin mis a jour');
    }

    public function apiDelete(string $id): void
    {
        if (!$this->model->findByIdWithHopital((int) $id)) {
            $this->sendError('Medecin introuvable', 404);
        }
        $this->model->delete((int) $id);
        $this->sendSuccess(null, 'Medecin supprime');
    }

    private function firstError(): string
    {
        return !empty($this->errors) ? reset($this->errors) : 'Erreur de validation';
    }
}
