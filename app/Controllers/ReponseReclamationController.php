<?php

require_once ROOT_PATH . 'app/Models/ReponseReclamation.php';
require_once ROOT_PATH . 'app/Models/Reclamation.php';
require_once ROOT_PATH . 'app/Models/StatutReclamation.php';

class ReponseReclamationController extends Controller
{
    // ----------------------------------------------------------------
    //  BACK-OFFICE — LIST
    // ----------------------------------------------------------------

    public function adminList(): void
    {
        $successMessage = null;
        if (isset($_GET['deleted'])) $successMessage = 'La réponse a été supprimée.';
        if (isset($_GET['updated'])) $successMessage = 'La réponse a été mise à jour.';

        $responses     = ReponseReclamation::findAllWithReclamation();
        $statusOptions = StatutReclamation::getLabels();

        $this->view('backoffise/responses_list', compact('responses', 'statusOptions', 'successMessage'));
    }

    // ----------------------------------------------------------------
    //  BACK-OFFICE — DETAIL
    // ----------------------------------------------------------------

    public function adminDetail(int $id): void
    {
        $response = ReponseReclamation::findById($id);
        if (!$response) { $this->notFound(); return; }

        $statusOptions = StatutReclamation::getLabels();
        $this->view('backoffise/response_detail', compact('response', 'statusOptions'));
    }

    // ----------------------------------------------------------------
    //  BACK-OFFICE — EDIT / UPDATE
    // ----------------------------------------------------------------

    public function adminEdit(int $id): void
    {
        $response = ReponseReclamation::findById($id);
        if (!$response) { $this->notFound(); return; }

        $formData       = $response;
        $errors         = [];
        $successMessage = isset($_GET['updated']) ? 'La réponse a été mise à jour avec succès.' : null;
        $statusOptions  = StatutReclamation::getLabels();

        $this->view('backoffise/response_edit', compact('response', 'formData', 'errors', 'successMessage', 'statusOptions'));
    }

    public function adminUpdate(int $id): void
    {
        $response = ReponseReclamation::findById($id);
        if (!$response) { $this->notFound(); return; }

        $message        = trim($_POST['message'] ?? '');
        $selectedStatus = trim($_POST['statut_reclamation'] ?? '');
        $errors         = [];

        if ($message === '') {
            $errors['message'] = 'Le message ne peut pas être vide.';
        } elseif (strlen($message) < 10) {
            $errors['message'] = 'Le message doit contenir au moins 10 caractères.';
        } elseif (strlen($message) > 2000) {
            $errors['message'] = 'Le message ne peut pas dépasser 2000 caractères.';
        }

        if (!in_array($selectedStatus, StatutReclamation::values(), true)) {
            $errors['statut_reclamation'] = 'Veuillez sélectionner un statut valide.';
        }

        if (!empty($errors)) {
            $formData      = $_POST;
            $statusOptions = StatutReclamation::getLabels();
            $this->view('backoffise/response_edit', compact('response', 'formData', 'errors', 'statusOptions'));
            return;
        }

        $pdo = Database::getInstance();
        $pdo->beginTransaction();
        try {
            ReponseReclamation::updateMessage($id, $message);

            if ($selectedStatus !== $response['statutReclamation']) {
                $stmt = $pdo->prepare('UPDATE reclamation SET statutReclamation = :s WHERE idReclamation = :id');
                $stmt->execute(['s' => $selectedStatus, 'id' => $response['id_reclamation']]);
            }

            $pdo->commit();
            $this->redirect('/reponses?updated=1');
        } catch (Throwable $e) {
            $pdo->rollBack();
            $errors['save']  = 'Erreur lors de la mise à jour. Veuillez réessayer.';
            $formData        = $_POST;
            $statusOptions   = StatutReclamation::getLabels();
            $this->view('backoffise/response_edit', compact('response', 'formData', 'errors', 'statusOptions'));
        }
    }

    // ----------------------------------------------------------------
    //  BACK-OFFICE — DELETE
    // ----------------------------------------------------------------

    public function adminDelete(int $id): void
    {
        ReponseReclamation::deleteById($id);
        $this->redirect('/reponses?deleted=1');
    }

    // ----------------------------------------------------------------
    //  HELPERS
    // ----------------------------------------------------------------

    private function notFound(): void
    {
        http_response_code(404);
        echo '<h1>Réponse introuvable</h1>';
    }
}
