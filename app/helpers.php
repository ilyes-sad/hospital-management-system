<?php
// ============================================================
//  app/helpers.php
//  Global helper functions for views
// ============================================================

function formatDate(?string $str): string
{
    if (!$str) return '—';
    $d = DateTime::createFromFormat('Y-m-d', $str);
    if ($d === false) return htmlspecialchars($str);
    return $d->format('d M Y');
}

function todayStr(): string
{
    return date('Y-m-d');
}

function initials(string $prenom, string $nom): string
{
    return mb_strtoupper(mb_substr($prenom, 0, 1) . mb_substr($nom, 0, 1));
}

function statusBadge(string $s): string
{
    $map = [
        'confirme'   => 'badge-success',
        'en attente' => 'badge-warning',
        'annule'     => 'badge-danger',
        'termine'    => 'badge-neutral',
    ];
    $class = $map[$s] ?? 'badge-neutral';
    return '<span class="badge ' . $class . '">' . htmlspecialchars($s) . '</span>';
}

function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}
