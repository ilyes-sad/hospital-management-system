<?php
// ============================================================
//  app/Models/Medecin.php
// ============================================================

class Medecin extends Model
{
    protected string $table = 'medecins';

    public function findAllWithHopital(): array
    {
        $stmt = $this->db->query("
            SELECT m.*, h.nom AS hopital_nom, h.ville AS hopital_ville,
                   h.region AS hopital_region
            FROM {$this->table} m
            JOIN hopitaux h ON h.id = m.hopital_id
            ORDER BY m.nom, m.prenom
        ");
        $medecins = $stmt->fetchAll();

        foreach ($medecins as &$med) {
            $med['horaires'] = $this->getHoraires((int) $med['id']);
        }

        return $medecins;
    }

    public function findByIdWithHopital(int $id): array|false
    {
        $stmt = $this->db->prepare("
            SELECT m.*, h.nom AS hopital_nom, h.region AS hopital_region
            FROM {$this->table} m
            JOIN hopitaux h ON h.id = m.hopital_id
            WHERE m.id = ?
        ");
        $stmt->execute([$id]);
        $med = $stmt->fetch();

        if ($med) {
            $med['horaires'] = $this->getHoraires($id);
        }

        return $med;
    }

    public function findByHopital(int $hopitalId): array
    {
        $stmt = $this->db->prepare("
            SELECT m.*, h.nom AS hopital_nom
            FROM {$this->table} m
            JOIN hopitaux h ON h.id = m.hopital_id
            WHERE m.hopital_id = ?
            ORDER BY m.nom
        ");
        $stmt->execute([$hopitalId]);
        $medecins = $stmt->fetchAll();

        foreach ($medecins as &$med) {
            $med['horaires'] = $this->getHoraires((int) $med['id']);
        }

        return $medecins;
    }

    public function getSpecialites(): array
    {
        $stmt = $this->db->query("SELECT DISTINCT specialite FROM {$this->table} ORDER BY specialite");
        return array_column($stmt->fetchAll(), 'specialite');
    }

    private function getHoraires(int $medecinId): array
    {
        $stmt = $this->db->prepare("SELECT heure FROM horaires_medecin WHERE medecin_id = ? ORDER BY heure");
        $stmt->execute([$medecinId]);
        return array_column($stmt->fetchAll(), 'heure');
    }

    public function createWithHoraires(array $data): int
    {
        $horaires = $data['horaires'] ?? [];
        unset($data['horaires']);

        $id = $this->create($data);
        $this->saveHoraires($id, $horaires);

        return $id;
    }

    public function updateWithHoraires(int $id, array $data): bool
    {
        $horaires = $data['horaires'] ?? [];
        unset($data['horaires']);

        $ok = $this->update($id, $data);
        $this->saveHoraires($id, $horaires);

        return $ok;
    }

    private function saveHoraires(int $medecinId, array $horaires): void
    {
        $this->db->prepare("DELETE FROM horaires_medecin WHERE medecin_id = ?")
             ->execute([$medecinId]);

        if (empty($horaires)) {
            return;
        }

        $stmt = $this->db->prepare(
            "INSERT INTO horaires_medecin (medecin_id, heure) VALUES (?, ?)"
        );

        foreach ($horaires as $h) {
            $stmt->execute([$medecinId, $h]);
        }
    }
}
