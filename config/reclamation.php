<?php
// ============================================================
//  config/reclamation.php
//  Configuration for reclamation lifecycle management
// ============================================================

return [
    // Nombre de jours avant qu'une réclamation 'en_cours' soit considérée en retard
    'overdue_threshold_days' => 7,
    
    // Nombre de jours entre deux rappels automatiques pour la même réclamation
    'reminder_interval_days' => 3,
    
    // Email du responsable des réclamations (recevra les alertes)
    'admin_email' => getenv('RECLAMATION_ADMIN_EMAIL') ?: 'admin@hospital.local',
    
    // Nom du responsable
    'admin_name' => getenv('RECLAMATION_ADMIN_NAME') ?: 'Responsable Réclamations',
    
    // Activer/désactiver les rappels automatiques
    'auto_reminders_enabled' => getenv('AUTO_REMINDERS_ENABLED') !== 'false',
];
