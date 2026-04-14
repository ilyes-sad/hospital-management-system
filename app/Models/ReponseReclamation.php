<?php

require_once __DIR__ . '/Database.php';

class ReponseReclamation
{
    private ?int $idReponse;
    private int $idReclamation;
    private string $message;
    private DateTime $dateReponse;

    public function __construct(?int $idReponse, int $idReclamation, string $message, ?DateTime $dateReponse = null)
    {
        $this->idReponse = $idReponse;
        $this->idReclamation = $idReclamation;
        $this->message = $message;
        $this->dateReponse = $dateReponse ?? new DateTime();
    }

    public function getIdReponse(): ?int
    {
        return $this->idReponse;
    }

    public function getIdReclamation(): int
    {
        return $this->idReclamation;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getDateReponse(): DateTime
    {
        return $this->dateReponse;
    }

    public function save(): bool
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('INSERT INTO reponse_reclamation (id_reclamation, message, date_reponse) VALUES (:id_reclamation, :message, :date_reponse)');
        return $stmt->execute([
            'id_reclamation' => $this->idReclamation,
            'message' => $this->message,
            'date_reponse' => $this->dateReponse->format('Y-m-d H:i:s'),
        ]);
    }

    public static function findByReclamationId(int $idReclamation): array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare('SELECT id_reponse, id_reclamation, message, date_reponse FROM reponse_reclamation WHERE id_reclamation = :id_reclamation ORDER BY date_reponse DESC');
        $stmt->execute(['id_reclamation' => $idReclamation]);

        $responses = [];
        while ($row = $stmt->fetch()) {
            $responses[] = new self(
                (int)$row['id_reponse'],
                (int)$row['id_reclamation'],
                $row['message'],
                new DateTime($row['date_reponse'])
            );
        }

        return $responses;
    }
}
