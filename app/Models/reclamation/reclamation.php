<?php

require_once ROOT_PATH . 'config/database.php';

class Reclamation
{
    private PDO $conn;

    public function __construct()
    {
        $this->conn = Database::getInstance();
    }

    public function getCategories(): array
    {
        $sql = "SELECT * FROM categoriereclamation ORDER BY libelleCategorie ASC";
        return $this->conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getServices(): array
    {
        $sql = "SELECT s.*, h.nomHopital
                FROM servicehospitalier s
                INNER JOIN hopital h ON h.idHopital = s.idHopital
                ORDER BY s.nomService ASC";

        return $this->conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create(
        int $idUser,
        int $idCategorie,
        int $idServiceHosp,
        string $objet,
        string $description
    ): bool {
        $sql = "INSERT INTO reclamation 
                (idUser, idCategorie, idServiceHosp, objet, description)
                VALUES 
                (:idUser, :idCategorie, :idServiceHosp, :objet, :description)";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(':idUser', $idUser, PDO::PARAM_INT);
        $stmt->bindValue(':idCategorie', $idCategorie, PDO::PARAM_INT);
        $stmt->bindValue(':idServiceHosp', $idServiceHosp, PDO::PARAM_INT);
        $stmt->bindValue(':objet', $objet, PDO::PARAM_STR);
        $stmt->bindValue(':description', $description, PDO::PARAM_STR);

        return $stmt->execute();
    }

    public function getByUser(int $idUser): array
    {
        $sql = "SELECT r.*, c.libelleCategorie, s.nomService
                FROM reclamation r
                INNER JOIN categoriereclamation c ON c.idCategorie = r.idCategorie
                INNER JOIN servicehospitalier s ON s.idServiceHosp = r.idServiceHosp
                WHERE r.idUser = :idUser
                ORDER BY r.dateDepot DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':idUser', $idUser, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAll(): array
    {
        $sql = "SELECT r.*, 
                       u.nom, u.prenom, u.email,
                       c.libelleCategorie,
                       s.nomService
                FROM reclamation r
                INNER JOIN users u ON u.idUser = r.idUser
                INNER JOIN categoriereclamation c ON c.idCategorie = r.idCategorie
                INNER JOIN servicehospitalier s ON s.idServiceHosp = r.idServiceHosp
                ORDER BY r.dateDepot DESC";

        return $this->conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById(int $idReclamation): ?array
    {
        $sql = "SELECT r.*, 
                       u.nom, u.prenom, u.email,
                       c.libelleCategorie,
                       s.nomService
                FROM reclamation r
                INNER JOIN users u ON u.idUser = r.idUser
                INNER JOIN categoriereclamation c ON c.idCategorie = r.idCategorie
                INNER JOIN servicehospitalier s ON s.idServiceHosp = r.idServiceHosp
                WHERE r.idReclamation = :idReclamation
                LIMIT 1";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':idReclamation', $idReclamation, PDO::PARAM_INT);
        $stmt->execute();

        $reclamation = $stmt->fetch(PDO::FETCH_ASSOC);

        return $reclamation ?: null;
    }

    public function updateStatut(int $idReclamation, string $statut): bool
    {
        $sql = "UPDATE reclamation
                SET statutReclamation = :statut
                WHERE idReclamation = :idReclamation";

        $stmt = $this->conn->prepare($sql);

        $stmt->bindValue(':statut', $statut, PDO::PARAM_STR);
        $stmt->bindValue(':idReclamation', $idReclamation, PDO::PARAM_INT);

        return $stmt->execute();
    }
}