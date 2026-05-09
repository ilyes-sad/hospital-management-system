<?php

require_once __DIR__ . '/../Core/Database.php';

class ReponseReclamation
{
    private ?int $idReponse;
    private int $idReclamation;
    private string $message;
    private DateTime $dateReponse;

    public function __construct(?int $idReponse, int $idReclamation, string $message, ?DateTime $dateReponse = null)
    {
        $this->idReponse      = $idReponse;
        $this->idReclamation  = $idReclamation;
        $this->message        = $message;
        $this->dateReponse    = $dateReponse ?? new DateTime();
    }

    public function getIdReponse(): ?int      { return $this->idReponse; }
    public function getIdReclamation(): int   { return $this->idReclamation; }
    public function getMessage(): string      { return $this->message; }
    public function getDateReponse(): DateTime { return $this->dateReponse; }

    public function save(): bool
    {
        $pdo  = Database::getInstance();
        $stmt = $pdo->prepare(
            'INSERT INTO reponse_reclamation (idReclamation, message, dateReponse) VALUES (:idReclamation, :message, :dateReponse)'
        );
        $result = $stmt->execute([
            'idReclamation' => $this->idReclamation,
            'message'       => $this->message,
            'dateReponse'   => $this->dateReponse->format('Y-m-d H:i:s'),
        ]);
        if ($result && $this->idReponse === null) {
            $this->idReponse = (int)$pdo->lastInsertId();
        }
        return $result;
    }

    public static function findByReclamationId(int $idReclamation): array
    {
        $pdo  = Database::getInstance();
        $stmt = $pdo->prepare(
            'SELECT idReponse, idReclamation, message, dateReponse FROM reponse_reclamation WHERE idReclamation = :id ORDER BY dateReponse DESC'
        );
        $stmt->execute(['id' => $idReclamation]);

        $responses = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $responses[] = [
                'id_reponse'     => (int)$row['idReponse'],
                'id_reclamation' => (int)$row['idReclamation'],
                'message'        => $row['message'],
                'date_reponse'   => $row['dateReponse'],
            ];
        }
        return $responses;
    }

    public static function findById(int $id): ?array
    {
        $pdo  = Database::getInstance();
        $stmt = $pdo->prepare(
            'SELECT r.idReponse, r.idReclamation, r.message, r.dateReponse,
                    rec.objet, rec.description, rec.statutReclamation, rec.dateDepot
             FROM reponse_reclamation r
             JOIN reclamation rec ON r.idReclamation = rec.idReclamation
             WHERE r.idReponse = :id LIMIT 1'
        );
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) return null;

        return [
            'id_reponse'          => (int)$row['idReponse'],
            'id_reclamation'      => (int)$row['idReclamation'],
            'message'             => $row['message'],
            'date_reponse'        => $row['dateReponse'],
            'objet'               => $row['objet'],
            'description'         => $row['description'],
            'statutReclamation'   => $row['statutReclamation'],
            'dateDepot'           => $row['dateDepot'],
        ];
    }

    public static function findAllWithReclamation(): array
    {
        $pdo  = Database::getInstance();
        $stmt = $pdo->prepare(
            'SELECT r.idReponse, r.idReclamation, r.message, r.dateReponse,
                    rec.objet, rec.statutReclamation
             FROM reponse_reclamation r
             JOIN reclamation rec ON r.idReclamation = rec.idReclamation
             ORDER BY r.dateReponse DESC'
        );
        $stmt->execute();
        $rows = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $rows[] = [
                'id_reponse'        => (int)$row['idReponse'],
                'id_reclamation'    => (int)$row['idReclamation'],
                'message'           => $row['message'],
                'date_reponse'      => $row['dateReponse'],
                'objet'             => $row['objet'],
                'statutReclamation' => $row['statutReclamation'],
            ];
        }
        return $rows;
    }

    public static function countByReclamationId(int $idReclamation): int
    {
        $pdo  = Database::getInstance();
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM reponse_reclamation WHERE idReclamation = :id');
        $stmt->execute(['id' => $idReclamation]);
        return (int)$stmt->fetchColumn();
    }

    public static function hasResponse(int $idReclamation): bool
    {
        return self::countByReclamationId($idReclamation) > 0;
    }

    public static function updateMessage(int $idReponse, string $message): bool
    {
        $pdo  = Database::getInstance();
        $stmt = $pdo->prepare('UPDATE reponse_reclamation SET message = :message WHERE idReponse = :id');
        return $stmt->execute(['message' => $message, 'id' => $idReponse]);
    }

    public static function deleteById(int $idReponse): bool
    {
        $pdo  = Database::getInstance();
        $stmt = $pdo->prepare('DELETE FROM reponse_reclamation WHERE idReponse = :id');
        return $stmt->execute(['id' => $idReponse]);
    }
}
