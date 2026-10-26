<?php

namespace App\Support;

class TicketLabels
{
    /**
     * @return array<string, string>
     */
    public static function statuses(): array
    {
        return [
            'open' => __('shared.tickets.status_open'),
            'in_progress' => __('shared.tickets.status_in_progress'),
            'closed' => __('shared.tickets.status_closed'),
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function statusColors(): array
    {
        return [
            'open' => 'success',
            'in_progress' => 'info',
            'closed' => 'gray',
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function levels(): array
    {
        return [
            'low' => __('shared.tickets.level_low'),
            'medium' => __('shared.tickets.level_medium'),
            'high' => __('shared.tickets.level_high'),
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function levelColors(): array
    {
        return [
            'low' => 'success',
            'medium' => 'warning',
            'high' => 'danger',
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function types(): array
    {
        return [
            'general' => 'Soporte general',
            'soporte' => 'Soporte general',
            'cuenta' => 'Cuenta y acceso',
            'cv' => 'CV y perfil',
            'vacantes' => 'Vacantes y postulaciones',
            'tecnico' => 'Problema técnico',
        ];
    }
}
