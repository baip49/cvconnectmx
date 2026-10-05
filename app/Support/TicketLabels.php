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
            'open' => 'Abierto',
            'in_progress' => 'En progreso',
            'closed' => 'Cerrado',
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
            'low' => 'Bajo',
            'medium' => 'Medio',
            'high' => 'Alto',
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
