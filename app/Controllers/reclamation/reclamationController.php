<?php

require_once ROOT_PATH . 'app/Models/reclamation/reclamation.php';
require_once ROOT_PATH . 'app/Models/reclamation/reponseReclamation.php';
require_once ROOT_PATH . 'app/Models/reclamation/ReclamationNotifier.php';

class ReclamationController
{
    private Reclamation $reclamationModel;
    private ReponseReclamation $reponseModel;

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $this->reclamationModel = new Reclamation();
        $this->reponseModel = new ReponseReclamation();
    }

    private function redirect(string $url): void
    {
        header("Location: $url");
        exit;
    }

    private function requireLogin(): void
    {
        if (!isset($_SESSION['user'])) {
            $this->redirect('/login');
        }
    }

    private function requireAdmin(): void
    {
        if (!isset($_SESSION['user']) || $_SESSION['user']['nomRole'] !== 'admin') {
            $this->redirect('/login');
        }
    }

    public function create(): void
    {
        $this->requireLogin();

        $categories = $this->reclamationModel->getCategories();
        $services = $this->reclamationModel->getServices();

        $errors = [];
        $old = [];

        require ROOT_PATH . 'views/front/New folder/create.php';
    }

    public function store(): void
    {
        $this->requireLogin();

        $idUser = (int) $_SESSION['user']['idUser'];
        $idCategorie = (int) ($_POST['idCategorie'] ?? 0);
        $idServiceHosp = (int) ($_POST['idServiceHosp'] ?? 0);
        $objet = trim($_POST['objet'] ?? '');
        $description = trim($_POST['description'] ?? '');

        $errors = [];

        if ($idCategorie <= 0) {
            $errors['idCategorie'] = "La catégorie est obligatoire.";
        }

        if ($idServiceHosp <= 0) {
            $errors['idServiceHosp'] = "Le service est obligatoire.";
        }

        if ($objet === '') {
            $errors['objet'] = "L'objet est obligatoire.";
        }

        if ($description === '') {
            $errors['description'] = "La description est obligatoire.";
        }

        if (!empty($errors)) {
            $categories = $this->reclamationModel->getCategories();
            $services = $this->reclamationModel->getServices();
            $old = $_POST;

            require ROOT_PATH . 'views/front/New folder/create.php';
            return;
        }

        $this->reclamationModel->create(
            $idUser,
            $idCategorie,
            $idServiceHosp,
            $objet,
            $description
        );

        $this->redirect('/reclamations/mes-reclamations');
    }

    public function mine(): void
    {
        $this->requireLogin();

        $idUser = (int) $_SESSION['user']['idUser'];
        $reclamations = $this->reclamationModel->getByUser($idUser);

        require ROOT_PATH . 'views/front/New folder/mine.php';
    }

    public function showMine(int $idReclamation): void
    {
        $this->requireLogin();

        $reclamation = $this->reclamationModel->getById($idReclamation);

        if (!$reclamation || $reclamation['idUser'] != $_SESSION['user']['idUser']) {
            $this->redirect('/reclamations/mes-reclamations');
        }

        $reponses = $this->reponseModel->getByReclamation($idReclamation);

        require ROOT_PATH . 'views/front/New folder/show.php';
    }

    public function adminIndex(): void
    {
        $this->requireAdmin();

        $reclamations = $this->reclamationModel->getAll();

        require ROOT_PATH . 'views/BackOffice/index.php';
    }

    public function adminShow(int $idReclamation): void
{
    $this->requireAdmin();

    $reclamation = $this->reclamationModel->getById($idReclamation);

    if (!$reclamation) {
        $this->redirect('/admin/reclamations');
    }

    $reponses = $this->reponseModel->getByReclamation($idReclamation);

    $currentStatut = $reclamation['statutReclamation'];

    $isFinal = in_array($currentStatut, ['resolue', 'rejetee']);

    $reponseCount = $this->reponseModel->countByReclamation($idReclamation);

    $canChangeStatus = !$isFinal;

    $canRespond = !$isFinal
        && $currentStatut === 'en_cours'
        && $reponseCount === 0;

    require ROOT_PATH . 'views/BackOffice/show.php';
}
public function updateStatus(int $idReclamation): void
{
    $this->requireAdmin();

    $reclamation = $this->reclamationModel->getById($idReclamation);

    if (!$reclamation) {
        $this->redirect('/admin/reclamations');
    }

    $currentStatut = $reclamation['statutReclamation'];
    $newStatut = $_POST['statutReclamation'] ?? '';

    if (in_array($currentStatut, ['resolue', 'rejetee'])) {
        $_SESSION['error'] = "Cette réclamation est déjà clôturée.";
        $this->redirect('/admin/reclamations/show/' . $idReclamation);
    }

    if ($currentStatut === 'ouverte' && $newStatut !== 'en_cours') {
        $_SESSION['error'] = "Vous devez d'abord passer la réclamation en cours.";
        $this->redirect('/admin/reclamations/show/' . $idReclamation);
    }

    if ($currentStatut === 'en_cours' && !in_array($newStatut, ['resolue', 'rejetee'])) {
        $_SESSION['error'] = "Après en cours, vous devez choisir résolue ou rejetée.";
        $this->redirect('/admin/reclamations/show/' . $idReclamation);
    }

    $this->reclamationModel->updateStatut($idReclamation, $newStatut);

    $_SESSION['success'] = "Statut mis à jour avec succès.";
    $this->redirect('/admin/reclamations/show/' . $idReclamation);
}
public function respond(int $idReclamation): void
{
    $this->requireAdmin();

    $reclamation = $this->reclamationModel->getById($idReclamation);

    if (!$reclamation) {
        $this->redirect('/admin/reclamations');
    }

    $currentStatut = $reclamation['statutReclamation'];
    $contenu = trim($_POST['contenu'] ?? '');
    $decision = $_POST['decision'] ?? '';

    if (in_array($currentStatut, ['resolue', 'rejetee'])) {
        $_SESSION['error'] = "Cette réclamation est déjà clôturée.";
        $this->redirect('/admin/reclamations/show/' . $idReclamation);
    }

    if ($currentStatut !== 'en_cours') {
        $_SESSION['error'] = "Vous devez d'abord passer la réclamation en cours avant de répondre.";
        $this->redirect('/admin/reclamations/show/' . $idReclamation);
    }

    if ($this->reponseModel->countByReclamation($idReclamation) > 0) {
        $_SESSION['error'] = "Une réponse existe déjà pour cette réclamation.";
        $this->redirect('/admin/reclamations/show/' . $idReclamation);
    }

    if ($contenu === '') {
        $_SESSION['error'] = "La réponse est obligatoire.";
        $this->redirect('/admin/reclamations/show/' . $idReclamation);
    }

    if (!in_array($decision, ['resolue', 'rejetee'])) {
        $_SESSION['error'] = "Veuillez choisir une décision valide.";
        $this->redirect('/admin/reclamations/show/' . $idReclamation);
    }

    $this->reponseModel->create($idReclamation, $contenu);
    $this->reclamationModel->updateStatut($idReclamation, $decision);

    $_SESSION['success'] = "Réponse enregistrée et réclamation clôturée.";
    $this->redirect('/admin/reclamations/show/' . $idReclamation);
}
    
    
}