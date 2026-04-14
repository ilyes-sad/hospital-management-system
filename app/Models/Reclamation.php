<?php

require_once __DIR__ . '/Database.php';
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

    public function __construct(
        ?int $idReclamation,
        DateTime $dateDepot,
        string $objet,
        string $description,
        string $statutReclamation,
        int $idServiceHosp
    ) {
        $this->idReclamation = $idReclamation;
        $this->dateDepot = $dateDepot;
        $this->objet = $objet;
        $this->description = $description;
        $this->statutReclamation = $statutReclamation;
        $this->idServiceHosp = $idServiceHosp;
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

        return $errors;
    }

    public static function fromArray(array $row): self
    {
        return new self(
            isset($row['idReclamation']) ? (int)$row['idReclamation'] : null,
            new DateTime($row['dateDepot']),
            $row['objet'],
            $row['description'],
            $row['statutReclamation'],
            (int)$row['idServiceHosp']
        );
    }

    public static function findAll(): array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->query('SELECT idReclamation, dateDepot, objet, description, statutReclamation, idServiceHosp FROM reclamation ORDER BY dateDepot DESC');
        $reclamations = [];

        while ($row = $stmt->fetch()) {
            $reclamations[] = self::fromArray($row);
        }

        return $reclamations;
    }

    public static function findById(int $id): ?self
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('SELECT idReclamation, dateDepot, objet, description, statutReclamation, idServiceHosp FROM reclamation WHERE idReclamation = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        if (!$row) {
            return null;
        }

        return self::fromArray($row);
    }

    public function save(): bool
    {
        $pdo = Database::getConnection();

        if ($this->idReclamation === null) {
            $stmt = $pdo->prepare('INSERT INTO reclamation (dateDepot, objet, description, statutReclamation, idServiceHosp) VALUES (:dateDepot, :objet, :description, :statutReclamation, :idServiceHosp)');
            $result = $stmt->execute([
                'dateDepot' => $this->dateDepot->format('Y-m-d H:i:s'),
                'objet' => $this->objet,
                'description' => $this->description,
                'statutReclamation' => $this->statutReclamation,
                'idServiceHosp' => $this->idServiceHosp,
            ]);

            if ($result) {
                $this->idReclamation = (int)$pdo->lastInsertId();
            }

            return $result;
        }

        $stmt = $pdo->prepare('UPDATE reclamation SET objet = :objet, description = :description, statutReclamation = :statutReclamation, idServiceHosp = :idServiceHosp WHERE idReclamation = :idReclamation');

        return $stmt->execute([
            'objet' => $this->objet,
            'description' => $this->description,
            'statutReclamation' => $this->statutReclamation,
            'idServiceHosp' => $this->idServiceHosp,
            'idReclamation' => $this->idReclamation,
        ]);
    }

    public function delete(): bool
    {
        if ($this->idReclamation === null) {
            return false;
        }

        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('DELETE FROM reclamation WHERE idReclamation = :idReclamation');
        return $stmt->execute(['idReclamation' => $this->idReclamation]);
    }

    public function updateStatus(string $nouveauStatut): bool
    {
        $this->setStatutReclamation($nouveauStatut);

        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('UPDATE reclamation SET statutReclamation = :statutReclamation WHERE idReclamation = :idReclamation');

        return $stmt->execute([
            'statutReclamation' => $this->statutReclamation,
            'idReclamation' => $this->idReclamation,
        ]);
    }
}
