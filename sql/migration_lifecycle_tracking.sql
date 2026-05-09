-- ============================================================
-- MIGRATION: Lifecycle Tracking & Automatic Reminders
-- ============================================================
-- Add tracking column for automatic reminders

USE medicare;

ALTER TABLE reclamation 
    ADD COLUMN IF NOT EXISTS dateLastReminder DATETIME NULL DEFAULT NULL
    COMMENT 'Date du dernier rappel automatique envoyé';

-- Index for performance when querying overdue complaints
CREATE INDEX IF NOT EXISTS idx_statut_date ON reclamation(statutReclamation, dateDepot);
