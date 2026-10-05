<?php

namespace Database\Factories\Concerns;

/**
 * Contenido en español para factories, ya que el locale es_ES de Faker
 * no cubre todos los proveedores (títulos, descripciones, etc.).
 */
trait HasSpanishContent
{
    protected static array $spanishJobTitles = [
        'Desarrollador Backend Laravel',
        'Desarrolladora Frontend React',
        'Diseñador UX Senior',
        'Analista de Datos',
        'Soporte Técnico',
        'Contador General',
        'Vendedor de Piso',
        'Gerente de Sucursal',
        'Auxiliar Administrativo',
        'Técnico en Redes',
        'Community Manager',
        'Reclutador de Personal',
    ];

    protected static array $spanishTrainingTitles = [
        'Capacitación en Seguridad Informática',
        'Inducción al Puesto de Trabajo',
        'Cumplimiento y Normativa Interna',
        'Atención al Cliente',
        'Manejo de Herramientas Digitales',
        'Prevención de Riesgos Laborales',
    ];

    protected static array $spanishInstitutions = [
        'UNACH',
        'UNAM',
        'Instituto Tecnológico de Tuxtla Gutiérrez',
        'ITESM Campus Chiapas',
        'Universidad Politécnica de Chiapas',
        'Colegio de Bachilleres de Chiapas',
    ];

    protected static array $spanishCities = [
        'Tuxtla Gutiérrez',
        'San Cristóbal de las Casas',
        'Tapachula',
        'Comitán',
        'Chiapa de Corzo',
        'Palenque',
    ];

    protected static array $spanishVacancyDescriptions = [
        'Buscamos una persona comprometida para integrarse a nuestro equipo de trabajo en Chiapas.',
        'Ofrecemos sueldo competitivo, prestaciones de ley y oportunidades de crecimiento profesional.',
        'Puesto de tiempo completo con horario de lunes a viernes y excelente ambiente laboral.',
    ];

    protected static array $spanishVacancyRequirements = [
        'Experiencia mínima de 1 año en el puesto, disponibilidad de horario y ganas de aprender.',
        'Licenciatura terminada o trunca, manejo básico de computadora y buena comunicación.',
        'Documentación en regla, referencias laborales y disponibilidad inmediata.',
    ];

    protected static array $spanishIncidentDescriptions = [
        'Se detectaron múltiples intentos de acceso fallidos a una cuenta de candidato.',
        'Actividad inusual detectada fuera del horario laboral habitual.',
        'Un usuario reportó un posible acceso no autorizado a su perfil.',
    ];

    protected static array $spanishAlertMessages = [
        'Revisar los intentos de acceso fallidos de las últimas 24 horas.',
        'Hay respaldos pendientes de verificación esta semana.',
        'Se recomienda revisar los incidentes abiertos del sistema.',
    ];

    protected static array $spanishActionTexts = [
        'Se bloqueó temporalmente la cuenta afectada.',
        'Se notificó al usuario sobre la actividad detectada.',
        'Se revisaron los registros de acceso del sistema.',
        'Se documentó la evidencia del incidente.',
    ];

    protected function spanish(array $pool): string
    {
        return $this->faker->randomElement($pool);
    }
}
