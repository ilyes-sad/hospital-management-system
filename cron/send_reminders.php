<?php
// ============================================================
//  cron/send_reminders.php
//  Script de relances automatiques — à exécuter quotidiennement
//
//  Linux/Mac (crontab -e):
//    0 8 * * * php /path/to/hospital-management-system/cron/send_reminders.php
//
//  Windows (Task Scheduler):
//    Program : C:\xampp\php\php.exe
//    Arguments: C:\xampp\htdocs\projetweb\hospital-management-system\cron\send_reminders.php
//    Trigger  : Daily at 08:00
// ============================================================

// Prevent execution from browser
if (PHP_SAPI !== 'cli' && !isset($_GET['cron_token'])) {
    http_response_code(403);
    exit('Access denied. This script must be run from CLI or with a valid cron token.');
}

// Validate cron token if called via HTTP (for testing)
if (PHP_SAPI !== 'cli') {
    $expectedToken = getenv('CRON_SECRET') ?: 'change-me-in-production';
    if (($_GET['cron_token'] ?? '') !== $expectedToken) {
        http_response_code(403);
        exit('Invalid cron token.');
    }
}

// Bootstrap
define('ROOT_PATH', dirname(__DIR__) . DIRECTORY_SEPARATOR);

require ROOT_PATH . 'config/database.php';
require ROOT_PATH . 'app/Core/Database.php';
require ROOT_PATH . 'app/Models/StatutReclamation.php';
require ROOT_PATH . 'app/Models/ServiceHospitalier.php';
require ROOT_PATH . 'app/Models/Reclamation.php';
require ROOT_PATH . 'app/Models/ReclamationLifecycle.php';

// ---- Run ----
$startTime = microtime(true);
$timestamp = date('Y-m-d H:i:s');

echo "[{$timestamp}] Démarrage des relances automatiques...\n";

try {
    $lifecycle = new ReclamationLifecycle();
    
    // Show overdue count before processing
    $overdueStats = ReclamationLifecycle::getOverdueStats();
    echo "[{$timestamp}] Réclamations en retard (>{$overdueStats['threshold']} jours): {$overdueStats['total']}\n";
    
    if (!empty($overdueStats['by_service'])) {
        echo "[{$timestamp}] Répartition par service:\n";
        foreach ($overdueStats['by_service'] as $service => $count) {
            echo "              - {$service}: {$count}\n";
        }
    }
    
    // Process and send reminders
    $result = $lifecycle->processOverdueReclamations();
    
    if (isset($result['disabled']) && $result['disabled']) {
        echo "[{$timestamp}] ⚠️  Relances automatiques désactivées (AUTO_REMINDERS_ENABLED=false)\n";
    } else {
        echo "[{$timestamp}] ✅ Rappels envoyés  : {$result['sent']}\n";
        echo "[{$timestamp}] ❌ Échecs           : {$result['failed']}\n";
        echo "[{$timestamp}] 📊 Total traités    : {$result['total']}\n";
    }
    
} catch (Throwable $e) {
    $errorMsg = "[{$timestamp}] ERREUR: " . $e->getMessage() . " in " . $e->getFile() . ":" . $e->getLine();
    echo $errorMsg . "\n";
    error_log($errorMsg);
    exit(1);
}

$elapsed = round(microtime(true) - $startTime, 2);
echo "[{$timestamp}] Terminé en {$elapsed}s\n";
exit(0);
