<?php

require_once __DIR__ . '/../Core/Database.php';
require_once __DIR__ . '/Reclamation.php';
require_once __DIR__ . '/StatutReclamation.php';
require_once __DIR__ . '/ServiceHospitalier.php';

/**
 * Gestion du cycle de vie des réclamations et relances automatiques.
 * 
 * Garantit qu'aucune plainte ne tombe dans l'oubli en envoyant des notifications
 * automatiques au responsable du service concerné.
 */
class ReclamationLifecycle
{
    private array $config;

    public function __construct()
    {
        $this->config = require ROOT_PATH . 'config/reclamation.php';
    }

    /**
     * Trouve toutes les réclamations en retard qui nécessitent un rappel.
     * 
     * Critères:
     * - Statut = 'en_cours' (en attente de traitement)
     * - dateDepot > X jours (threshold configuré)
     * - Pas de rappel récent (ou jamais de rappel)
     * 
     * @return array<Reclamation>
     */
    public function findOverdueReclamations(): array
    {
        $pdo = Database::getInstance();
        
        $thresholdDays = (int)$this->config['overdue_threshold_days'];
        $intervalDays  = (int)$this->config['reminder_interval_days'];
        
        // Date limite: réclamations déposées avant cette date sont en retard
        $overdueDate = (new DateTime())->modify("-{$thresholdDays} days")->format('Y-m-d H:i:s');
        
        // Date du dernier rappel acceptable: si rappel envoyé il y a moins de X jours, on ne renvoie pas
        $lastReminderThreshold = (new DateTime())->modify("-{$intervalDays} days")->format('Y-m-d H:i:s');
        
        $sql = "
            SELECT idReclamation, dateDepot, objet, description, statutReclamation, 
                   idServiceHosp, nomPatient, emailPatient, nomHopital, dateLastReminder
            FROM reclamation
            WHERE statutReclamation = :statut
              AND dateDepot <= :overdueDate
              AND (dateLastReminder IS NULL OR dateLastReminder <= :lastReminderThreshold)
            ORDER BY dateDepot ASC
        ";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'statut'                 => StatutReclamation::EN_COURS,
            'overdueDate'            => $overdueDate,
            'lastReminderThreshold'  => $lastReminderThreshold,
        ]);
        
        $reclamations = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $reclamations[] = Reclamation::fromArray($row);
        }
        
        return $reclamations;
    }

    /**
     * Envoie un rappel automatique pour une réclamation en retard.
     * 
     * @param Reclamation $reclamation
     * @return bool True si l'email a été envoyé avec succès
     */
    public function sendReminderForReclamation(Reclamation $reclamation): bool
    {
        $service = $reclamation->getServiceHospitalier();
        $serviceName = $service ? $service->getNomService() : 'Service inconnu';
        
        $daysOverdue = $this->calculateDaysOverdue($reclamation);
        
        $adminEmail = $this->config['admin_email'];
        $adminName  = $this->config['admin_name'];
        
        if (!filter_var($adminEmail, FILTER_VALIDATE_EMAIL)) {
            error_log("ReclamationLifecycle: Invalid admin email: {$adminEmail}");
            return false;
        }
        
        $subject = "⚠️ Réclamation #{$reclamation->getIdReclamation()} en retard ({$daysOverdue} jours)";
        
        $lines = [
            "Bonjour {$adminName},",
            "",
            "Une réclamation nécessite votre attention urgente :",
            "",
            "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━",
            "📋 Réclamation #{$reclamation->getIdReclamation()}",
            "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━",
            "",
            "Patient       : {$reclamation->getNomPatient()}",
            "Email         : {$reclamation->getEmailPatient()}",
            "Hôpital       : {$reclamation->getNomHopital()}",
            "Service       : {$serviceName}",
            "",
            "Objet         : {$reclamation->getObjet()}",
            "Description   : " . mb_substr($reclamation->getDescription(), 0, 200) . "...",
            "",
            "Date de dépôt : {$reclamation->getDateDepot()->format('d/m/Y à H:i')}",
            "Statut        : En attente",
            "⏰ Retard     : {$daysOverdue} jours",
            "",
            "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━",
            "",
            "Cette réclamation est en attente depuis plus de {$this->config['overdue_threshold_days']} jours.",
            "Merci de la traiter dans les plus brefs délais pour garantir la satisfaction du patient.",
            "",
            "🔗 Accéder à la réclamation :",
            $this->buildReclamationUrl($reclamation->getIdReclamation()),
            "",
            "Cordialement,",
            "Système de gestion des réclamations - MediCare",
        ];
        
        $body = implode("\r\n", $lines);
        
        $from     = getenv('MAIL_FROM')      ?: 'noreply@hospital.local';
        $fromName = getenv('MAIL_FROM_NAME') ?: 'MediCare - Réclamations';
        
        $headers = implode("\r\n", [
            'MIME-Version: 1.0',
            'Content-Type: text/plain; charset=UTF-8',
            'From: ' . $this->encodeHeaderName($fromName) . ' <' . $from . '>',
            'Reply-To: ' . $from,
            'X-Mailer: PHP/' . PHP_VERSION,
            'X-Priority: 1',
            'Importance: High',
        ]);
        
        $sent = @mail($adminEmail, $this->encodeSubject($subject), $body, $headers);
        
        if ($sent) {
            $this->markReminderSent($reclamation->getIdReclamation());
        }
        
        return $sent;
    }

    /**
     * Traite toutes les réclamations en retard et envoie les rappels.
     * 
     * @return array Statistiques: ['total' => int, 'sent' => int, 'failed' => int]
     */
    public function processOverdueReclamations(): array
    {
        if (!$this->config['auto_reminders_enabled']) {
            return ['total' => 0, 'sent' => 0, 'failed' => 0, 'disabled' => true];
        }
        
        $overdue = $this->findOverdueReclamations();
        $stats   = ['total' => count($overdue), 'sent' => 0, 'failed' => 0];
        
        foreach ($overdue as $reclamation) {
            if ($this->sendReminderForReclamation($reclamation)) {
                $stats['sent']++;
            } else {
                $stats['failed']++;
            }
        }
        
        return $stats;
    }

    /**
     * Calcule le nombre de jours de retard pour une réclamation.
     */
    private function calculateDaysOverdue(Reclamation $reclamation): int
    {
        $now  = new DateTime();
        $diff = $now->diff($reclamation->getDateDepot());
        return (int)$diff->days;
    }

    /**
     * Marque qu'un rappel a été envoyé pour cette réclamation.
     */
    private function markReminderSent(int $idReclamation): void
    {
        $pdo  = Database::getInstance();
        $stmt = $pdo->prepare('UPDATE reclamation SET dateLastReminder = NOW() WHERE idReclamation = :id');
        $stmt->execute(['id' => $idReclamation]);
    }

    /**
     * Construit l'URL vers la page de détail de la réclamation.
     */
    private function buildReclamationUrl(int $idReclamation): string
    {
        $baseUrl = getenv('APP_URL') ?: 'http://localhost/hospital-management-system/public';
        return rtrim($baseUrl, '/') . '/reclamations/' . $idReclamation;
    }

    private function encodeSubject(string $subject): string
    {
        return '=?UTF-8?B?' . base64_encode($subject) . '?=';
    }

    private function encodeHeaderName(string $name): string
    {
        return '=?UTF-8?B?' . base64_encode($name) . '?=';
    }

    /**
     * Récupère les statistiques sur les réclamations en retard.
     * 
     * @return array
     */
    public static function getOverdueStats(): array
    {
        $config = require ROOT_PATH . 'config/reclamation.php';
        $pdo    = Database::getInstance();
        
        $thresholdDays = (int)$config['overdue_threshold_days'];
        $overdueDate   = (new DateTime())->modify("-{$thresholdDays} days")->format('Y-m-d H:i:s');
        
        // Total en retard
        $stmt = $pdo->prepare("
            SELECT COUNT(*) as total
            FROM reclamation
            WHERE statutReclamation = :statut
              AND dateDepot <= :overdueDate
        ");
        $stmt->execute([
            'statut'      => StatutReclamation::EN_COURS,
            'overdueDate' => $overdueDate,
        ]);
        $total = (int)$stmt->fetchColumn();
        
        // Par service
        $stmt = $pdo->prepare("
            SELECT s.nomService, COUNT(*) as count
            FROM reclamation r
            JOIN service_hospitalier s ON r.idServiceHosp = s.idService
            WHERE r.statutReclamation = :statut
              AND r.dateDepot <= :overdueDate
            GROUP BY s.idService, s.nomService
            ORDER BY count DESC
        ");
        $stmt->execute([
            'statut'      => StatutReclamation::EN_COURS,
            'overdueDate' => $overdueDate,
        ]);
        
        $byService = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $byService[$row['nomService']] = (int)$row['count'];
        }
        
        return [
            'total'      => $total,
            'threshold'  => $thresholdDays,
            'by_service' => $byService,
        ];
    }
}
