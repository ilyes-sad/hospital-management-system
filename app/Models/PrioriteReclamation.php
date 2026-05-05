<?php

class PrioriteReclamation
{
    public const HAUTE  = 'haute';
    public const MOYENNE = 'moyenne';
    public const BASSE  = 'basse';

    public static function getLabels(): array
    {
        return [
            self::HAUTE   => '🔴 Haute',
            self::MOYENNE => '🟡 Moyenne',
            self::BASSE   => '🟢 Basse',
        ];
    }

    public static function values(): array
    {
        return [self::HAUTE, self::MOYENNE, self::BASSE];
    }

    public static function getBadgeClass(string $priorite): string
    {
        return match($priorite) {
            self::HAUTE   => 'badge-danger',
            self::MOYENNE => 'badge-warning',
            self::BASSE   => 'badge-success',
            default       => 'badge-neutral',
        };
    }

    /** Priorités applicables uniquement aux réclamations non clôturées. */
    public static function isActive(string $priorite): bool
    {
        return in_array($priorite, self::values(), true);
    }
}
