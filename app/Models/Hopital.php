<?php
// ============================================================
//  app/Models/Hopital.php
// ============================================================

class Hopital extends Model
{
    protected string $table = 'hopitaux';

    public function findAllWithStats(): array
    {
        $stmt = $this->db->query("
            SELECT h.*,
                   COUNT(DISTINCT m.id) AS nb_medecins
            FROM {$this->table} h
            LEFT JOIN medecins m ON m.hopital_id = h.id
            GROUP BY h.id
            ORDER BY h.nom
        ");
        return $stmt->fetchAll();
    }

    public function getRegions(): array
    {
        $stmt = $this->db->query("SELECT DISTINCT region FROM {$this->table} ORDER BY region");
        return array_column($stmt->fetchAll(), 'region');
    }

    public function findByRegion(string $region): array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE region = ? ORDER BY nom");
        $stmt->execute([$region]);
        return $stmt->fetchAll();
    }
}
