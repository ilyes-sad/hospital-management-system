<?php

class StatutReclamation
{
    public const OUVERTE  = 'ouverte';
    public const EN_COURS = 'en_cours';
    public const RESOLUE  = 'resolue';
    public const REJETEE  = 'rejetee';

    public static function getLabels(): array
    {
        return [
            self::OUVERTE  => 'Reçue',
            self::EN_COURS => 'En attente',
            self::RESOLUE  => 'Acceptée',
            self::REJETEE  => 'Refusée',
        ];
    }

    public static function values(): array
    {
        return array_keys(self::getLabels());
    }

    /** Acceptée ou Refusée : réclamation clôturée. */
    public static function isFinal(string $statut): bool
    {
        return in_array($statut, [self::RESOLUE, self::REJETEE], true);
    }

    /**
     * Choix autorisés pour le menu « Changer le statut »
     * (les statuts finaux passent uniquement par « Répondre »).
     *
     * @return array<string, string>
     */
    public static function getManualStatusChoices(string $current): array
    {
        $labels = self::getLabels();
        if (self::isFinal($current)) {
            return [];
        }
        if ($current === self::OUVERTE) {
            return [
                self::OUVERTE  => $labels[self::OUVERTE],
                self::EN_COURS => $labels[self::EN_COURS],
            ];
        }
        if ($current === self::EN_COURS) {
            return [
                self::EN_COURS => $labels[self::EN_COURS],
            ];
        }
        return [];
    }

    /** Indique si le widget « Changer le statut » doit être affiché. */
    public static function allowsManualStatusWidget(string $current): bool
    {
        return !self::isFinal($current) && $current === self::OUVERTE;
    }
}
