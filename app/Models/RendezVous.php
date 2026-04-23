<?php
// ============================================================
//  app/Models/RendezVous.php
// ============================================================

class RendezVous extends Model
{
    protected string $table = 'rendez_vous';

    private function baseQuery(): string
    {
        return "
            SELECT
                r.id, r.date_rdv, r.heure, r.motif, r.statut, r.created_at,
                r.patient_id, r.medecin_id, r.hopital_id,
                CONCAT(p.prenom, ' ', p.nom) AS patient_nom,
                p.telephone                  AS patient_tel,
                p.cin                        AS patient_cin,
                CONCAT('Dr. ', m.prenom, ' ', m.nom) AS medecin_nom,
                m.specialite,
                h.nom                        AS hopital_nom,
                h.ville                      AS hopital_ville,
                h.region                     AS hopital_region
            FROM {$this->table} r
            JOIN patients  p ON p.id = r.patient_id
            JOIN medecins  m ON m.id = r.medecin_id
            JOIN hopitaux  h ON h.id = r.hopital_id
        ";
    }

    public function findAllWithFilters(array $filters = []): array
    {
        $sql    = $this->baseQuery();
        $params = [];
        $where  = [];

        if (!empty($filters['statut'])) {
            $where[]         = "r.statut = :statut";
            $params['statut'] = $filters['statut'];
        }

        if (!empty($filters['region'])) {
            $where[]        = "h.region = :region";
            $params['region'] = $filters['region'];
        }

        if (!empty($filters['patient_id'])) {
            $where[]          = "r.patient_id = :patient_id";
            $params['patient_id'] = (int) $filters['patient_id'];
        }

        if (!empty($filters['medecin_id'])) {
            $where[]         = "r.medecin_id = :medecin_id";
            $params['medecin_id'] = (int) $filters['medecin_id'];
        }

        if (!empty($filters['search'])) {
            $like                 = '%' . $filters['search'] . '%';
            $where[]              = "(CONCAT(p.prenom,' ',p.nom) LIKE :s1
                             OR CONCAT(m.prenom,' ',m.nom) LIKE :s2
                             OR r.motif LIKE :s3)";
            $params['s1'] = $like;
            $params['s2'] = $like;
            $params['s3'] = $like;
        }

        if ($where) {
            $sql .= " WHERE " . implode(" AND ", $where);
        }

        $sql .= " ORDER BY r.date_rdv DESC, r.heure DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function findByIdWithDetails(int $id): array|false
    {
        $sql  = $this->baseQuery() . " WHERE r.id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function getStats(): array
    {
        $stmt = $this->db->query("
            SELECT
                COUNT(*) AS total,
                SUM(statut = 'confirmé')   AS confirmes,
                SUM(statut = 'en attente') AS en_attente,
                SUM(statut = 'annulé')     AS annules,
                SUM(statut = 'terminé')    AS termines,
                SUM(date_rdv = CURDATE())  AS aujourd_hui
            FROM {$this->table}
        ");
        return $stmt->fetch();
    }

    public function getBusySlots(int $medecinId, string $date): array
    {
        $stmt = $this->db->prepare("
            SELECT heure FROM {$this->table}
            WHERE medecin_id = ? AND date_rdv = ? AND statut != 'annulé'
        ");
        $stmt->execute([$medecinId, $date]);
        return array_column($stmt->fetchAll(), 'heure');
    }

    public function isSlotBusy(int $medecinId, string $date, string $heure): bool
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as cnt FROM {$this->table}
            WHERE medecin_id = ? AND date_rdv = ? AND heure = ? AND statut != 'annulé'
        ");
        $stmt->execute([$medecinId, $date, $heure]);
        return (int) $stmt->fetch()['cnt'] > 0;
    }

    public function createRdv(array $data): int
    {
        $stmt = $this->db->prepare("
            INSERT INTO {$this->table}
                (patient_id, medecin_id, hopital_id, date_rdv, heure, motif, statut)
            VALUES
                (:patient_id, :medecin_id, :hopital_id, :date_rdv, :heure, :motif, 'en attente')
        ");

        $stmt->execute([
            ':patient_id' => (int) $data['patient_id'],
            ':medecin_id' => (int) $data['medecin_id'],
            ':hopital_id' => (int) $data['hopital_id'],
            ':date_rdv'   => $data['date_rdv'],
            ':heure'      => $data['heure'],
            ':motif'      => $data['motif'] ?? null,
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function getRecent(int $limit = 5): array
    {
        $sql  = $this->baseQuery() . " ORDER BY r.date_rdv DESC, r.heure DESC LIMIT ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }
}
