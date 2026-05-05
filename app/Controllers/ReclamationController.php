<?php

require_once ROOT_PATH . 'app/Models/Reclamation.php';
require_once ROOT_PATH . 'app/Models/ReponseReclamation.php';
require_once ROOT_PATH . 'app/Models/ServiceHospitalier.php';
require_once ROOT_PATH . 'app/Models/StatutReclamation.php';
require_once ROOT_PATH . 'app/Models/ReclamationNotifier.php';
require_once ROOT_PATH . 'app/Models/ReclamationLifecycle.php';

class ReclamationController extends Controller
{
    // ----------------------------------------------------------------
    //  FRONT-OFFICE
    // ----------------------------------------------------------------

    public function showNew(): void
    {
        $services = ServiceHospitalier::findAll();
        $formData = [
            'objet'               => '',
            'description'         => '',
            'service_hospitalier' => '',
            'nom_patient'         => '',
            'email_patient'       => '',
            'nom_hopital'         => '',
        ];
        $errors = [];
        $this->view('frontoffise/reclamation_form', compact('services', 'formData', 'errors'));
    }

    public function save(): void
    {
        $formData = [
            'objet'               => trim($_POST['objet'] ?? ''),
            'description'         => trim($_POST['description'] ?? ''),
            'service_hospitalier' => (int)($_POST['service_hospitalier'] ?? 0),
            'nom_patient'         => trim($_POST['nom_patient'] ?? ''),
            'email_patient'       => trim($_POST['email_patient'] ?? ''),
            'nom_hopital'         => trim($_POST['nom_hopital'] ?? ''),
        ];

        $errors = Reclamation::validate($formData);

        if (!empty($errors)) {
            $services = ServiceHospitalier::findAll();
            $this->view('frontoffise/reclamation_form', compact('services', 'formData', 'errors'));
            return;
        }

        $reclamation = new Reclamation(
            null,
            new DateTime(),
            $formData['objet'],
            $formData['description'],
            StatutReclamation::OUVERTE,
            $formData['service_hospitalier'],
            $formData['nom_patient'],
            $formData['email_patient'],
            $formData['nom_hopital']
        );
        $reclamation->save();

        $services       = ServiceHospitalier::findAll();
        $successMessage = 'Votre réclamation a bien été enregistrée. Nous vous répondrons bientôt.';
        $formData       = ['objet' => '', 'description' => '', 'service_hospitalier' => '', 'nom_patient' => '', 'email_patient' => '', 'nom_hopital' => ''];
        $errors         = [];
        $this->view('frontoffise/reclamation_form', compact('services', 'formData', 'errors', 'successMessage'));
    }

    // ----------------------------------------------------------------
    //  BACK-OFFICE — OVERDUE / LIFECYCLE
    // ----------------------------------------------------------------

    public function adminOverdue(): void
    {
        $lifecycle   = new ReclamationLifecycle();
        $config      = require ROOT_PATH . 'config/reclamation.php';
        $overdue     = $lifecycle->findOverdueReclamations();
        $overdueStats = ReclamationLifecycle::getOverdueStats();

        $thresholdDays       = (int)$config['overdue_threshold_days'];
        $reminderIntervalDays = (int)$config['reminder_interval_days'];
        $remindersEnabled    = (bool)$config['auto_reminders_enabled'];

        $this->view('backoffise/reclamations_overdue', compact(
            'overdue', 'overdueStats', 'thresholdDays', 'reminderIntervalDays', 'remindersEnabled'
        ));
    }

    public function adminSendAllReminders(): void
    {
        $lifecycle = new ReclamationLifecycle();
        $result    = $lifecycle->processOverdueReclamations();

        $this->redirect('/reclamations/overdue?reminders_sent=1&sent=' . $result['sent'] . '&failed=' . $result['failed']);
    }

    public function adminSendReminder(int $id): void
    {
        $reclamation = Reclamation::findById($id);
        if (!$reclamation) { $this->notFound(); return; }

        $lifecycle = new ReclamationLifecycle();
        $sent      = $lifecycle->sendReminderForReclamation($reclamation);

        $this->redirect('/reclamations/' . $id . '?reminder=' . ($sent ? 'sent' : 'failed'));
    }

    // ----------------------------------------------------------------
    //  BACK-OFFICE — LIST / DASHBOARD
    // ----------------------------------------------------------------

    public function adminList(): void
    {
        $filters = [
            'status'      => $_GET['status']     ?? '',
            'service'     => $_GET['service']    ?? '',
            'hopital'     => $_GET['hopital']    ?? '',
            'search'      => trim($_GET['search'] ?? ''),
            'date_from'   => $_GET['date_from']  ?? '',
            'date_to'     => $_GET['date_to']    ?? '',
            'sort_date'   => in_array($_GET['sort_date'] ?? '', ['asc', 'desc'], true) ? $_GET['sort_date'] : 'desc',
            'sort_statut' => ($_GET['sort_statut'] ?? '') === 'group' ? 'group' : '',
        ];

        $reclamations   = Reclamation::findFiltered($filters);
        $statusOptions  = StatutReclamation::getLabels();
        $services       = ServiceHospitalier::findAll();
        $hopitalOptions = Reclamation::distinctHopitaux();
        $currentFilters = $filters;

        $this->view('backoffise/reclamations_list', compact(
            'reclamations', 'statusOptions', 'services', 'hopitalOptions', 'currentFilters'
        ));
    }

    public function adminDashboard(): void
    {
        $stats        = Reclamation::getGlobalStats();
        $statusLabels = StatutReclamation::getLabels();
        $this->view('backoffise/reclamations_dashboard', compact('stats', 'statusLabels'));
    }

    // ----------------------------------------------------------------
    //  BACK-OFFICE — DETAIL / EDIT / DELETE
    // ----------------------------------------------------------------

    public function adminDetail(int $id): void
    {
        $reclamation = Reclamation::findById($id);
        if (!$reclamation) { $this->notFound(); return; }

        $responses    = ReponseReclamation::findByReclamationId($id);
        $reponseCount = ReponseReclamation::countByReclamationId($id);
        $errors       = [];

        $this->view('backoffise/reclamation_detail', compact('reclamation', 'responses', 'reponseCount', 'errors'));
    }

    public function adminEdit(int $id): void
    {
        $reclamation = Reclamation::findById($id);
        if (!$reclamation) { $this->notFound(); return; }

        $services       = ServiceHospitalier::findAll();
        $statusOptions  = StatutReclamation::getLabels();
        $errors         = [];
        $successMessage = null;
        $formData       = [
            'objet'               => $reclamation->getObjet(),
            'description'         => $reclamation->getDescription(),
            'service_hospitalier' => $reclamation->getIdServiceHosp(),
            'statut_reclamation'  => $reclamation->getStatutReclamation(),
            'nom_patient'         => $reclamation->getNomPatient(),
            'email_patient'       => $reclamation->getEmailPatient(),
            'nom_hopital'         => $reclamation->getNomHopital(),
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $formData = [
                'objet'               => trim($_POST['objet'] ?? ''),
                'description'         => trim($_POST['description'] ?? ''),
                'service_hospitalier' => (int)($_POST['service_hospitalier'] ?? 0),
                'statut_reclamation'  => trim($_POST['statut_reclamation'] ?? ''),
                'nom_patient'         => trim($_POST['nom_patient'] ?? ''),
                'email_patient'       => trim($_POST['email_patient'] ?? ''),
                'nom_hopital'         => trim($_POST['nom_hopital'] ?? ''),
            ];

            $errors = Reclamation::validate($formData);

            $curStat = $reclamation->getStatutReclamation();
            $manual  = StatutReclamation::getManualStatusChoices($curStat);

            if (StatutReclamation::isFinal($curStat)) {
                if ($formData['statut_reclamation'] !== $curStat) {
                    $errors['statut_reclamation'] = 'Une réclamation clôturée ne peut pas changer de statut.';
                }
            } elseif (!array_key_exists($formData['statut_reclamation'], $manual)) {
                $errors['statut_reclamation'] = 'Transition de statut non autorisée. Les statuts « Acceptée » et « Refusée » se définissent via « Répondre » sur la fiche.';
            }

            if (empty($errors)) {
                $reclamation->setObjet($formData['objet']);
                $reclamation->setDescription($formData['description']);
                $reclamation->setIdServiceHosp($formData['service_hospitalier']);
                $reclamation->setStatutReclamation($formData['statut_reclamation']);
                $reclamation->setNomPatient($formData['nom_patient']);
                $reclamation->setEmailPatient($formData['email_patient']);
                $reclamation->setNomHopital($formData['nom_hopital']);
                $reclamation->save();

                $successMessage = 'La réclamation a été mise à jour avec succès.';
                $reclamation    = Reclamation::findById($id);
                $formData       = [
                    'objet'               => $reclamation->getObjet(),
                    'description'         => $reclamation->getDescription(),
                    'service_hospitalier' => $reclamation->getIdServiceHosp(),
                    'statut_reclamation'  => $reclamation->getStatutReclamation(),
                    'nom_patient'         => $reclamation->getNomPatient(),
                    'email_patient'       => $reclamation->getEmailPatient(),
                    'nom_hopital'         => $reclamation->getNomHopital(),
                ];
                $errors = [];
            }
        }

        $this->view('backoffise/reclamation_edit', compact(
            'reclamation', 'services', 'statusOptions', 'formData', 'errors', 'successMessage'
        ));
    }

    public function adminDelete(int $id): void
    {
        $reclamation = Reclamation::findById($id);
        if (!$reclamation) { $this->notFound(); return; }

        $reclamation->delete();
        $this->redirect('/reclamations?deleted=1');
    }

    // ----------------------------------------------------------------
    //  BACK-OFFICE — STATUS UPDATE
    // ----------------------------------------------------------------

    public function adminUpdateStatus(int $id): void
    {
        $reclamation = Reclamation::findById($id);
        if (!$reclamation) { $this->notFound(); return; }

        $newStatus = trim($_POST['statut'] ?? '');
        $current   = $reclamation->getStatutReclamation();
        $allowed   = StatutReclamation::getManualStatusChoices($current);

        if (!array_key_exists($newStatus, $allowed)) {
            $this->redirect('/reclamations/' . $id . '?error=' . urlencode('Ce changement de statut n\'est pas autorisé.'));
            return;
        }

        if ($newStatus !== $current) {
            $reclamation->updateStatus($newStatus);
        }

        $this->redirect('/reclamations/' . $id . '?updated=1');
    }

    // ----------------------------------------------------------------
    //  BACK-OFFICE — RESPOND
    // ----------------------------------------------------------------

    public function adminRespond(int $id): void
    {
        $reclamation = Reclamation::findById($id);
        if (!$reclamation) { $this->notFound(); return; }

        $message         = trim($_POST['message'] ?? '');
        $reponseStatut   = trim($_POST['reponse_statut'] ?? '');
        $errors          = [];
        $current         = $reclamation->getStatutReclamation();
        $msgLen          = mb_strlen($message);

        if (StatutReclamation::isFinal($current)) {
            $errors['message'] = 'Cette réclamation est déjà clôturée.';
        } elseif (ReponseReclamation::hasResponse($id)) {
            $errors['message'] = 'Une réponse a déjà été enregistrée (une seule réponse par dossier).';
        } elseif ($current !== StatutReclamation::EN_COURS) {
            $errors['message'] = 'Passez d\'abord la réclamation en « En attente » via « Changer le statut ».';
        } else {
            if (!in_array($reponseStatut, [StatutReclamation::RESOLUE, StatutReclamation::REJETEE], true)) {
                $errors['reponse_statut'] = 'Indiquez Acceptée ou Refusée pour clôturer la réclamation.';
            }
            if ($message === '') {
                $errors['message'] = 'Le message de réponse est requis.';
            } elseif ($msgLen < 10) {
                $errors['message'] = 'Le message doit contenir au moins 10 caractères.';
            } elseif ($msgLen > 2000) {
                $errors['message'] = 'Le message ne peut pas dépasser 2000 caractères.';
            }
        }

        if (!empty($errors)) {
            $responses    = ReponseReclamation::findByReclamationId($id);
            $reponseCount = ReponseReclamation::countByReclamationId($id);
            $postedMessage       = $_POST['message'] ?? '';
            $postedReponseStatut = $reponseStatut;
            $this->view('backoffise/reclamation_detail', compact(
                'reclamation', 'responses', 'reponseCount', 'errors', 'postedMessage', 'postedReponseStatut'
            ));
            return;
        }

        $response = new ReponseReclamation(null, $id, $message, new DateTime());
        $response->save();
        $reclamation->updateStatus($reponseStatut);

        // Email notification
        $reload  = Reclamation::findById($id);
        $service = $reload ? $reload->getServiceHospitalier() : null;
        ReclamationNotifier::notifyPatientFinalResponse(
            $reload ? $reload->getEmailPatient() : '',
            $reload ? $reload->getNomPatient()   : '',
            $id,
            StatutReclamation::getLabels()[$reponseStatut] ?? $reponseStatut,
            $message,
            (new DateTime())->format('d/m/Y à H:i'),
            $reload ? $reload->getNomHopital() : '',
            $service ? $service->getNomService() : ''
        );

        $this->redirect('/reclamations/' . $id . '?responded=1');
    }

    // ----------------------------------------------------------------
    //  HELPERS
    // ----------------------------------------------------------------

    private function notFound(): void
    {
        http_response_code(404);
        echo '<h1>Réclamation introuvable</h1>';
    }
}
