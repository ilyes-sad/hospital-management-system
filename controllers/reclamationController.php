<?php

require_once __DIR__ . '/../models/Reclamation.php';
require_once __DIR__ . '/../models/ReponseReclamation.php';
require_once __DIR__ . '/../models/ReclamationNotifier.php';

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
            $this->redirect('index.php?action=login');
        }
    }

    private function requireAdmin(): void
    {
        if (!isset($_SESSION['user']) || $_SESSION['user']['nomRole'] !== 'admin') {
            $this->redirect('index.php?action=login');
        }
    }

    public function create(): void
    {
        $this->requireLogin();

        $categories = $this->reclamationModel->getCategories();
        $services = $this->reclamationModel->getServices();

        $errors = [];
        $old = [];

        require __DIR__ . '/../views/front/reclamation/create.php';
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

            require __DIR__ . '/../views/front/reclamation/create.php';
            return;
        }

        $this->reclamationModel->create(
            $idUser,
            $idCategorie,
            $idServiceHosp,
            $objet,
            $description
        );

        $this->redirect('index.php?action=myReclamations');
    }

    public function mine(): void
    {
        $this->requireLogin();

        $idUser = (int) $_SESSION['user']['idUser'];
        $reclamations = $this->reclamationModel->getByUser($idUser);

        require __DIR__ . '/../views/front/reclamation/mine.php';
    }

    public function showMine(int $idReclamation): void
    {
        $this->requireLogin();

        $reclamation = $this->reclamationModel->getById($idReclamation);

        if (!$reclamation || $reclamation['idUser'] != $_SESSION['user']['idUser']) {
            $this->redirect('index.php?action=myReclamations');
        }

        $reponses = $this->reponseModel->getByReclamation($idReclamation);

        require __DIR__ . '/../views/front/reclamation/show.php';
    }

    public function adminIndex(): void
    {
        $this->requireAdmin();

        $reclamations = $this->reclamationModel->getAll();

        require __DIR__ . '/../views/BackOffice/reclamation/index.php';
    }

    public function adminShow(int $idReclamation): void
{
    $this->requireAdmin();

    $reclamation = $this->reclamationModel->getById($idReclamation);

    if (!$reclamation) {
        $this->redirect('index.php?action=adminReclamations');
    }

    $reponses = $this->reponseModel->getByReclamation($idReclamation);

    $currentStatut = $reclamation['statutReclamation'];

    $isFinal = in_array($currentStatut, ['resolue', 'rejetee']);

    $reponseCount = $this->reponseModel->countByReclamation($idReclamation);

    $canChangeStatus = !$isFinal;

    $canRespond = !$isFinal
        && $currentStatut === 'en_cours'
        && $reponseCount === 0;

    require __DIR__ . '/../views/BackOffice/reclamation/show.php';
}
public function updateStatus(int $idReclamation): void
{
    $this->requireAdmin();

    $reclamation = $this->reclamationModel->getById($idReclamation);

    if (!$reclamation) {
        $this->redirect('index.php?action=adminReclamations');
    }

    $currentStatut = $reclamation['statutReclamation'];
    $newStatut = $_POST['statutReclamation'] ?? '';

    if (in_array($currentStatut, ['resolue', 'rejetee'])) {
        $_SESSION['error'] = "Cette réclamation est déjà clôturée.";
        $this->redirect('index.php?action=showReclamation&id=' . $idReclamation);
    }

    if ($currentStatut === 'ouverte' && $newStatut !== 'en_cours') {
        $_SESSION['error'] = "Vous devez d'abord passer la réclamation en cours.";
        $this->redirect('index.php?action=showReclamation&id=' . $idReclamation);
    }

    if ($currentStatut === 'en_cours' && !in_array($newStatut, ['resolue', 'rejetee'])) {
        $_SESSION['error'] = "Après en cours, vous devez choisir résolue ou rejetée.";
        $this->redirect('index.php?action=showReclamation&id=' . $idReclamation);
    }

    $this->reclamationModel->updateStatut($idReclamation, $newStatut);

    $_SESSION['success'] = "Statut mis à jour avec succès.";
    $this->redirect('index.php?action=showReclamation&id=' . $idReclamation);
}
public function respond(int $idReclamation): void
{
    $this->requireAdmin();

    $reclamation = $this->reclamationModel->getById($idReclamation);

    if (!$reclamation) {
        $this->redirect('index.php?action=adminReclamations');
    }

    $currentStatut = $reclamation['statutReclamation'];
    $contenu = trim($_POST['contenu'] ?? '');
    $decision = $_POST['decision'] ?? '';

    if (in_array($currentStatut, ['resolue', 'rejetee'])) {
        $_SESSION['error'] = "Cette réclamation est déjà clôturée.";
        $this->redirect('index.php?action=showReclamation&id=' . $idReclamation);
    }

    if ($currentStatut !== 'en_cours') {
        $_SESSION['error'] = "Vous devez d'abord passer la réclamation en cours avant de répondre.";
        $this->redirect('index.php?action=showReclamation&id=' . $idReclamation);
    }

    if ($this->reponseModel->countByReclamation($idReclamation) > 0) {
        $_SESSION['error'] = "Une réponse existe déjà pour cette réclamation.";
        $this->redirect('index.php?action=showReclamation&id=' . $idReclamation);
    }

    if ($contenu === '') {
        $_SESSION['error'] = "La réponse est obligatoire.";
        $this->redirect('index.php?action=showReclamation&id=' . $idReclamation);
    }

    if (!in_array($decision, ['resolue', 'rejetee'])) {
        $_SESSION['error'] = "Veuillez choisir une décision valide.";
        $this->redirect('index.php?action=showReclamation&id=' . $idReclamation);
    }

    $this->reponseModel->create($idReclamation, $contenu);
    $this->reclamationModel->updateStatut($idReclamation, $decision);

    $_SESSION['success'] = "Réponse enregistrée et réclamation clôturée.";
    $this->redirect('index.php?action=showReclamation&id=' . $idReclamation);
}
    
    
}