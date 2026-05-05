<?php

require_once __DIR__ . '/../Core/Database.php';
require_once __DIR__ . '/StatutReclamation.php';
require_once __DIR__ . '/ServiceHospitalier.php';

class Reclamation
{
    private ?int $idReclamation;
    private DateTime $dateDepot;
    private string $objet;
    private string $description;
    private string $statutReclamation;
    private int $idServiceHosp;
    private string $nomPatient;
    private string $emailPatient;
    private string $nomHopital;
    private ?DateTime $dateLastReminder;

    public function __construct(
        ?int $idReclamation,
        DateTime $dateDepot,
        string $objet,
        string $description,
        string $statutReclamation,
        int $idServiceHosp,
        string $nomPatient = '',
        string $emailPatient = '',
        string $nomHopital = '',
        ?DateTime $dateLastReminder = null
    ) {
        $this->idReclamation    = $idReclamation;
        $this->dateDepot        = $dateDepot;
        $this->objet            = $objet;
        $this->description      = $description;
        $this->statutReclamation = $statutReclamation;
        $this->idServiceHosp    = $idServiceHosp;
        $this->nomPatient       = $nomPatient;
        $this->emailPatient     = $emailPatient;
        $this->nomHopital       = $nomHopital;
        $this->dateLastReminder = $dateLastReminder;
    }

    public function getIdReclamation(): ?int
    {
        return $this->idReclamation;
    }

    public function getDateDepot(): DateTime
    {
        return $this->dateDepot;
    }

    public function getObjet(): string
    {
        return $this->objet;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getStatutReclamation(): string
    {
        return $this->statutReclamation;
    }

    public function getIdServiceHosp(): int
    {
        return $this->idServiceHosp;
    }

    public function getNomPatient(): string
    {
        return $this->nomPatient;
    }

    public function getEmailPatient(): string
    {
        return $this->emailPatient;
    }

    public function getNomHopital(): string
    {
        return $this->nomHopital;
    }

    public function getDateLastReminder(): ?DateTime
    {
        return $this->dateLastReminder;
    }

    public function setDateDepot(DateTime $dateDepot): void
    {
        $this->dateDepot = $dateDepot;
    }

    public function setObjet(string $objet): void
    {
        $this->objet = $objet;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function setStatutReclamation(string $statutReclamation): void
    {
        if (!in_array($statutReclamation, StatutReclamation::values(), true)) {
            throw new InvalidArgumentException('Statut de réclamation invalide.');
        }
        $this->statutReclamation = $statutReclamation;
    }

    public function setIdServiceHosp(int $idServiceHosp): void
    {
        $this->idServiceHosp = $idServiceHosp;
    }

    public function setNomPatient(string $nomPatient): void
    {
        $this->nomPatient = $nomPatient;
    }

    public function setEmailPatient(string $emailPatient): void
    {
        $this->emailPatient = $emailPatient;
    }

    public function setNomHopital(string $nomHopital): void
    {
        $this->nomHopital = $nomHopital;
    }

    public function getServiceHospitalier(): ?ServiceHospitalier
    {
        return ServiceHospitalier::findById($this->idServiceHosp);
    }

    public function getStatutLabel(): string
    {
        $labels = StatutReclamation::getLabels();
        return $labels[$this->statutReclamation] ?? $this->statutReclamation;
    }

    public function __toString(): string
    {
        return sprintf(
            'Reclamation #%s: [%s] %s - %s (%s) - service %d',
            $this->idReclamation ?? 'new',
            $this->dateDepot->format('Y-m-d H:i:s'),
            $this->objet,
            $this->description,
            $this->statutReclamation,
            $this->idServiceHosp
        );
    }

    public static function validate(array $data): array
    {
        $errors = [];

        if (empty(trim($data['objet'] ?? ''))) {
            $errors['objet'] = 'Le champ Objet est requis.';
        }

        if (empty(trim($data['description'] ?? ''))) {
            $errors['description'] = 'Le champ Description est requis.';
        }

        if (empty($data['service_hospitalier']) || !is_numeric($data['service_hospitalier'])) {
            $errors['service_hospitalier'] = 'Veuillez sélectionner une catégorie.';
        }

        $nom = trim($data['nom_patient'] ?? '');
        if ($nom === '') {
            $errors['nom_patient'] = 'Le nom du patient est requis.';
        }

        $email = trim($data['email_patient'] ?? '');
        if ($email === '') {
            $errors['email_patient'] = "L'e-mail est requis.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email_patient'] = 'Adresse e-mail invalide.';
        }

        $hopital = trim($data['nom_hopital'] ?? '');
        if ($hopital === '') {
            $errors['nom_hopital'] = "Le nom de l'hôpital est requis.";
        }

        return $errors;
    }

    public static function fromArray(array $row): self
    {
        $dateLastReminder = null;
        if (!empty($row['dateLastReminder'])) {
            try { $dateLastReminder = new DateTime($row['dateLastReminder']); } catch (Exception $e) {}
        }

        return new self(
            isset($row['idReclamation']) ? (int)$row['idReclamation'] : null,
            new DateTime($row['dateDepot']),
            $row['objet'],
            $row['description'],
            $row['statutReclamation'],
            (int)$row['idServiceHosp'],
            isset($row['nomPatient'])  ? (string)$row['nomPatient']  : '',
            isset($row['emailPatient']) ? (string)$row['emailPatient'] : '',
            isset($row['nomHopital'])  ? (string)$row['nomHopital']  : '',
            $dateLastReminder
        );
    }

    public static function findAll(array $filters = []): array
    {
        return self::findFiltered($filters);
    }

    public static function findFiltered(array $filters = []): array
    {
        $pdo = Database::getInstance();
        $sql = 'SELECT idReclamation, dateDepot, objet, description, statutReclamation, idServiceHosp, nomPatient, emailPatient, nomHopital, dateLastReminder FROM reclamation';
        $conditions = [];
        $params = [];

        if (!empty($filters['status']) && in_array($filters['status'], StatutReclamation::values(), true)) {
            $conditions[] = 'statutReclamation = :status';
            $params['status'] = $filters['status'];
        }

        if (!empty($filters['service']) && is_numeric($filters['service'])) {
            $conditions[] = 'idServiceHosp = :service';
            $params['service'] = (int)$filters['service'];
        }

        if (!empty($filters['hopital'])) {
            $hop = trim((string)$filters['hopital']);
            if ($hop === '__empty__') {
                $conditions[] = "(TRIM(COALESCE(nomHopital, '')) = '')";
            } elseif ($hop !== '') {
                $conditions[] = 'TRIM(nomHopital) LIKE :hopital';
                $params['hopital'] = '%' . $hop . '%';
            }
        }

        if (!empty($filters['date_from'])) {
            try {
                $dateFrom = new DateTime($filters['date_from']);
                $conditions[] = 'dateDepot >= :date_from';
                $params['date_from'] = $dateFrom->format('Y-m-d 00:00:00');
            } catch (Exception $e) {}
        }

        if (!empty($filters['date_to'])) {
            try {
                $dateTo = new DateTime($filters['date_to']);
                $conditions[] = 'dateDepot <= :date_to';
                $params['date_to'] = $dateTo->format('Y-m-d 23:59:59');
            } catch (Exception $e) {}
        }

        if (!empty($filters['search'])) {
            $search = trim($filters['search']);
            if ($search !== '') {
                if (ctype_digit($search)) {
                    $conditions[] = '(idReclamation = :searchId OR objet LIKE :searchText OR description LIKE :searchText OR nomPatient LIKE :searchText OR emailPatient LIKE :searchText OR nomHopital LIKE :searchText)';
                    $params['searchId'] = (int)$search;
                    $params['searchText'] = '%' . $search . '%';
                } else {
                    $conditions[] = '(objet LIKE :searchText OR description LIKE :searchText OR nomPatient LIKE :searchText OR emailPatient LIKE :searchText OR nomHopital LIKE :searchText)';
                    $params['searchText'] = '%' . $search . '%';
                }
            }
        }

        if (count($conditions) > 0) {
            $sql .= ' WHERE ' . implode(' AND ', $conditions);
        }

        $sort = isset($filters['sort_date']) && $filters['sort_date'] === 'asc' ? 'ASC' : 'DESC';
        if (!empty($filters['sort_statut']) && $filters['sort_statut'] === 'group') {
            $sql .= " ORDER BY FIELD(statutReclamation, 'ouverte', 'en_cours', 'resolue', 'rejetee'), dateDepot " . $sort;
        } else {
            $sql .= ' ORDER BY dateDepot ' . $sort;
        }

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        $reclamations = [];
        while ($row = $stmt->fetch()) {
            $reclamations[] = self::fromArray($row);
        }

        return $reclamations;
    }

    public static function distinctHopitaux(): array
    {
        try {
            $pdo = Database::getInstance();
            $stmt = $pdo->query(
                "SELECT DISTINCT TRIM(nomHopital) AS h FROM reclamation WHERE TRIM(COALESCE(nomHopital,'')) <> '' ORDER BY h"
            );
            $list = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $list[] = $row['h'];
            }
            return $list;
        } catch (Throwable $e) {
            return [];
        }
    }

    public static function getGlobalStats(): array
    {
        $empty = [
            'total' => 0,
            'par_statut' => [
                StatutReclamation::OUVERTE  => 0,
                StatutReclamation::EN_COURS => 0,
                StatutReclamation::RESOLUE  => 0,
                StatutReclamation::REJETEE  => 0,
            ],
            'par_hopital' => [],
        ];

        try {
            $pdo = Database::getInstance();
            $total = (int)$pdo->query('SELECT COUNT(*) FROM reclamation')->fetchColumn();

            $parStatut = $empty['par_statut'];
            $st = $pdo->query('SELECT statutReclamation, COUNT(*) AS c FROM reclamation GROUP BY statutReclamation');
            while ($row = $st->fetch(PDO::FETCH_ASSOC)) {
                if (isset($parStatut[$row['statutReclamation']])) {
                    $parStatut[$row['statutReclamation']] = (int)$row['c'];
                }
            }

            $parHopital = [];
            $h = $pdo->query(
                "SELECT TRIM(COALESCE(nomHopital,'')) AS h, COUNT(*) AS c FROM reclamation GROUP BY TRIM(COALESCE(nomHopital,'')) ORDER BY c DESC"
            );
            while ($row = $h->fetch(PDO::FETCH_ASSOC)) {
                $label = $row['h'] === '' ? '(Non renseigné)' : $row['h'];
                $parHopital[$label] = (int)$row['c'];
            }

            return ['total' => $total, 'par_statut' => $parStatut, 'par_hopital' => $parHopital];
        } catch (Throwable $e) {
            return $empty;
        }
    }

    public static function findById(int $id): ?self
    {
        $pdo = Database::getInstance();
        $stmt = $pdo->prepare(
            'SELECT idReclamation, dateDepot, objet, description, statutReclamation, idServiceHosp, nomPatient, emailPatient, nomHopital, dateLastReminder FROM reclamation WHERE idReclamation = :id LIMIT 1'
        );
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        if (!$row) {
            return null;
        }

        return self::fromArray($row);
    }

    public function save(): bool
    {
        $pdo = Database::getInstance();

        if ($this->idReclamation === null) {
            $stmt = $pdo->prepare(
                'INSERT INTO reclamation (dateDepot, objet, description, statutReclamation, idServiceHosp, nomPatient, emailPatient, nomHopital) VALUES (:dateDepot, :objet, :description, :statutReclamation, :idServiceHosp, :nomPatient, :emailPatient, :nomHopital)'
            );
            $result = $stmt->execute([
                'dateDepot'          => $this->dateDepot->format('Y-m-d H:i:s'),
                'objet'              => $this->objet,
                'description'        => $this->description,
                'statutReclamation'  => $this->statutReclamation,
                'idServiceHosp'      => $this->idServiceHosp,
                'nomPatient'         => $this->nomPatient,
                'emailPatient'       => $this->emailPatient,
                'nomHopital'         => $this->nomHopital,
            ]);

            if ($result) {
                $this->idReclamation = (int)$pdo->lastInsertId();
            }

            return $result;
        }

        $stmt = $pdo->prepare(
            'UPDATE reclamation SET objet = :objet, description = :description, statutReclamation = :statutReclamation, idServiceHosp = :idServiceHosp, nomPatient = :nomPatient, emailPatient = :emailPatient, nomHopital = :nomHopital WHERE idReclamation = :idReclamation'
        );

        return $stmt->execute([
            'objet'             => $this->objet,
            'description'       => $this->description,
            'statutReclamation' => $this->statutReclamation,
            'idServiceHosp'     => $this->idServiceHosp,
            'nomPatient'        => $this->nomPatient,
            'emailPatient'      => $this->emailPatient,
            'nomHopital'        => $this->nomHopital,
            'idReclamation'     => $this->idReclamation,
        ]);
    }

    public function delete(): bool
    {
        if ($this->idReclamation === null) {
            return false;
        }

        $pdo = Database::getInstance();
        $stmt = $pdo->prepare('DELETE FROM reclamation WHERE idReclamation = :idReclamation');
        return $stmt->execute(['idReclamation' => $this->idReclamation]);
    }

    public function updateStatus(string $nouveauStatut): bool
    {
        $this->setStatutReclamation($nouveauStatut);

        $pdo = Database::getInstance();
        $stmt = $pdo->prepare('UPDATE reclamation SET statutReclamation = :statutReclamation WHERE idReclamation = :idReclamation');

        return $stmt->execute([
            'statutReclamation' => $this->statutReclamation,
            'idReclamation'     => $this->idReclamation,
        ]);
    }
}
