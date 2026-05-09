<?php

class PrioriteReclamation
{
    public const BASSE = 'basse';
    public const MOYENNE = 'moyenne';
    public const HAUTE = 'haute';

    public static function all(): array
    {
        return [
            self::BASSE,
            self::MOYENNE,
            self::HAUTE
        ];
    }

    public static function badgeClass(string $priorite): string
    {
        return match ($priorite) {
            self::BASSE => 'badge-success',
            self::MOYENNE => 'badge-warning',
            self::HAUTE => 'badge-danger',
            default => 'badge-secondary'
        };
    }
}