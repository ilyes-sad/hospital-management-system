<?php

class StatutReclamation
{
    public const OUVERTE = 'ouverte';
    public const EN_COURS = 'en_cours';
    public const RESOLUE = 'resolue';
    public const REJETEE = 'rejetee';

    public static function all(): array
    {
        return [
            self::OUVERTE,
            self::EN_COURS,
            self::RESOLUE,
            self::REJETEE
        ];
    }

    public static function badgeClass(string $statut): string
    {
        return match ($statut) {
            self::OUVERTE => 'badge-info',
            self::EN_COURS => 'badge-warning',
            self::RESOLUE => 'badge-success',
            self::REJETEE => 'badge-danger',
            default => 'badge-secondary'
        };
    }

    public static function canBeClosed(string $statut): bool
    {
        return in_array($statut, [
            self::RESOLUE,
            self::REJETEE
        ]);
    }
}