<?php

class StatutReclamation
{
    public const OUVERTE = 'ouverte';
    public const EN_COURS = 'en_cours';
    public const RESOLUE = 'resolue';
    public const REJETEE = 'rejetee';

    public static function getLabels(): array
    {
        return [
            self::OUVERTE => 'En attente',
            self::EN_COURS => 'En cours',
            self::RESOLUE => 'Résolue',
            self::REJETEE => 'Rejetée',
        ];
    }

    public static function values(): array
    {
        return array_keys(self::getLabels());
    }
}
