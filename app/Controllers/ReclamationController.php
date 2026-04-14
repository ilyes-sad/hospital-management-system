<?php

require_once __DIR__ . '/../models/Reclamation.php';
require_once __DIR__ . '/../models/ServiceHospitalier.php';
require_once __DIR__ . '/../models/StatutReclamation.php';

class ReclamationController
{
    public function showNew(): void
    {
        $services = ServiceHospitalier::findAll();
        $formData = [
            'objet' => '',
            'description' => '',
            'service_hospitalier' => '',
        ];
        $errors = [];
        require __DIR__ . '/../views/frontoffise/reclamation_form.php';
    }

    public function save(): void
    {
        $formData = [
            'objet' => trim($_POST['objet'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'service_hospitalier' => (int)($_POST['service_hospitalier'] ?? 0),
        ];

        $errors = Reclamation::validate($formData);

        if (!empty($errors)) {
            $services = ServiceHospitalier::findAll();
            require __DIR__ . '/../views/frontoffise/reclamation_form.php';
            return;
        }

        $reclamation = new Reclamation(
            null,
            new DateTime(),
            $formData['objet'],
            $formData['description'],
            StatutReclamation::OUVERTE,
            $formData['service_hospitalier']
        );

        $reclamation->save();

        $services = ServiceHospitalier::findAll();
        $successMessage = 'Votre réclamation a bien été enregistrée. Nous vous répondrons bientôt.';
        $formData = [
            'objet' => '',
            'description' => '',
            'service_hospitalier' => '',
        ];
        $errors = [];

        require __DIR__ . '/../views/frontoffise/reclamation_form.php';
        return;
    }

    public function adminList(): void
    {
        $reclamations = Reclamation::findAll();
        require __DIR__ . '/../views/backoffise/reclamations_list.php';
    }

    public function adminDetail(int $id): void
    {
        $reclamation = Reclamation::findById($id);

        if (!$reclamation) {
            http_response_code(404);
            echo '<h1>Réclamation introuvable</h1>';
            return;
        }

        require __DIR__ . '/../views/backoffise/reclamation_detail.php';
    }

    public function adminEdit(int $id): void
    {
        $reclamation = Reclamation::findById($id);

        if (!$reclamation) {
            http_response_code(404);
            echo '<h1>Réclamation introuvable</h1>';
            return;
        }

        $services = ServiceHospitalier::findAll();
        $statusOptions = StatutReclamation::getLabels();
        $errors = [];
        $formData = [
            'objet' => $reclamation->getObjet(),
            'description' => $reclamation->getDescription(),
            'service_hospitalier' => $reclamation->getIdServiceHosp(),
            'statut_reclamation' => $reclamation->getStatutReclamation(),
        ];
        $successMessage = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $formData = [
                'objet' => trim($_POST['objet'] ?? ''),
                'description' => trim($_POST['description'] ?? ''),
                'service_hospitalier' => (int)($_POST['service_hospitalier'] ?? 0),
                'statut_reclamation' => trim($_POST['statut_reclamation'] ?? ''),
            ];

            $errors = Reclamation::validate($formData);

            if (!in_array($formData['statut_reclamation'], StatutReclamation::values(), true)) {
                $errors['statut_reclamation'] = 'Le statut sélectionné est invalide.';
            }

            if (empty($errors)) {
                $reclamation->setObjet($formData['objet']);
                $reclamation->setDescription($formData['description']);
                $reclamation->setIdServiceHosp($formData['service_hospitalier']);
                $reclamation->setStatutReclamation($formData['statut_reclamation']);
                $reclamation->save();

                $successMessage = 'La réclamation a été mise à jour avec succès.';
                $errors = [];
                $reclamation = Reclamation::findById($id);
                $formData = [
                    'objet' => $reclamation->getObjet(),
                    'description' => $reclamation->getDescription(),
                    'service_hospitalier' => $reclamation->getIdServiceHosp(),
                    'statut_reclamation' => $reclamation->getStatutReclamation(),
                ];
            }
        }

        require __DIR__ . '/../views/backoffise/reclamation_edit.php';
    }

    public function adminDelete(int $id): void
    {
        $reclamation = Reclamation::findById($id);

        if (!$reclamation) {
            http_response_code(404);
            echo '<h1>Réclamation introuvable</h1>';
            return;
        }

        $reclamation->delete();
        $reclamations = Reclamation::findAll();
        $successMessage = 'La réclamation a été supprimée avec succès.';
        require __DIR__ . '/../views/backoffise/reclamations_list.php';
        return;
    }

    private function buildRouteUrl(string $route): string
    {
        return $route;
    }
}
