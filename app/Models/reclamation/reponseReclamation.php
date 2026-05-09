<?php

require_once __DIR__ . '/../config.php';

class ReponseReclamation
{
    private PDO $conn;

    public function __construct()
    {
        $this->conn = Database::connect();
    }

    public function create(int $idReclamation, string $contenu): bool
    {
        $sql = "INSERT INTO reponsereclamation (idReclamation, contenu)
                VALUES (:idReclamation, :contenu)";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(':idReclamation', $idReclamation, PDO::PARAM_INT);
        $stmt->bindValue(':contenu', $contenu, PDO::PARAM_STR);

        return $stmt->execute();
    }
    public function countByReclamation(int $idReclamation): int
{
    $sql = "SELECT COUNT(*) 
            FROM reponsereclamation 
            WHERE idReclamation = :idReclamation";

    $stmt = $this->conn->prepare($sql);
    $stmt->bindValue(':idReclamation', $idReclamation, PDO::PARAM_INT);
    $stmt->execute();

    return (int) $stmt->fetchColumn();
}

    public function getByReclamation(int $idReclamation): array
    {
        $sql = "SELECT *
                FROM reponsereclamation
                WHERE idReclamation = :idReclamation
                ORDER BY dateReponse DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':idReclamation', $idReclamation, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}