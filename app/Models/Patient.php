<?php
// ============================================================
//  app/Models/Patient.php
// ============================================================

class Patient extends Model
{
    protected string $table = 'patients';

    public function findAllWithRdvCount(): array
    {
        $stmt = $this->db->query("
            SELECT p.*,
                   COUNT(r.id) AS nb_rdv
            FROM {$this->table} p
            LEFT JOIN rendez_vous r ON r.patient_id = p.id
            GROUP BY p.id
            ORDER BY p.nom, p.prenom
        ");
        return $stmt->fetchAll();
    }

    public function search(string $query): array
    {
        $like = "%{$query}%";
        $stmt = $this->db->prepare("
            SELECT * FROM {$this->table}
            WHERE prenom LIKE ? OR nom LIKE ? OR cin LIKE ? OR ville LIKE ?
            ORDER BY nom
        ");
        $stmt->execute([$like, $like, $like, $like]);
        return $stmt->fetchAll();
    }
}
