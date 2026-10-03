<?php

use Carbon\Carbon;
use Illuminate\Support\Facades\Route;

if (! function_exists('route_exists')) {
    function route_exists(string $name): bool
    {
        return Route::has($name);
    }
}

if (! function_exists('event_status_badge')) {
    function event_status_badge(string $status): string
    {
        return match ($status) {
            'draft' => 'neutral',
            'published' => 'primary',
            'active' => 'success',
            'finished' => 'neutral',
            'cancelled' => 'danger',
            'archived' => 'neutral',
            default => 'neutral',
        };
    }
}

if (! function_exists('event_status_label')) {
    function event_status_label(string $status): string
    {
        return match ($status) {
            'draft' => 'Rascunho',
            'published' => 'Publicado',
            'active' => 'Em andamento',
            'finished' => 'Finalizado',
            'cancelled' => 'Cancelado',
            'archived' => 'Arquivado',
            default => ucfirst($status),
        };
    }
}

if (! function_exists('event_status_icon')) {
    function event_status_icon(string $status): string
    {
        return match ($status) {
            'draft' => 'file-text',
            'published' => 'check-circle',
            'active' => 'play-circle',
            'finished' => 'flag',
            'cancelled' => 'x-circle',
            'archived' => 'archive',
            default => 'calendar',
        };
    }
}

if (! function_exists('format_money')) {
    function format_money($value, string $currency = 'BRL'): string
    {
        return (new NumberFormatter('pt_BR', NumberFormatter::CURRENCY))
            ->formatCurrency((float) $value, $currency);
    }
}

if (! function_exists('format_date')) {
    function format_date($date, string $format = 'd/m/Y'): string
    {
        if (! $date) {
            return '—';
        }

        return $date instanceof Carbon ? $date->format($format) : Carbon::parse($date)->format($format);
    }
}

if (! function_exists('format_datetime')) {
    function format_datetime($date): string
    {
        return format_date($date, 'd/m/Y H:i');
    }
}
