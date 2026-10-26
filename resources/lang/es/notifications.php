<?php

return [
    'view' => 'Ver',
    'tickets' => [
        'reply_received' => ['title' => 'Nueva respuesta en tu ticket', 'body' => ':name respondió en ":title".'],
        'claimed' => ['title' => 'Tu ticket está siendo atendido', 'body' => ':name reclamó tu ticket ":title" y te atenderá ahora.'],
        'joined' => ['title' => 'Un segundo asistente se unió', 'body' => ':name se unió como segundo asistente al ticket ":title".'],
        'released' => ['title' => 'Un asistente abandonó el ticket', 'body' => ':name dejó de atender el ticket ":title".'],
        'closed' => ['title' => 'Ticket cerrado', 'body' => ':name cerró el ticket ":title".'],
        'reopened' => ['title' => 'Ticket reabierto', 'body' => ':name reabrió el ticket ":title".'],
        'assigned' => ['title' => 'Tienes un ticket asignado', 'body' => 'Se te asignó el ticket ":title".'],
    ],
    'applications' => [
        'a_candidate' => 'Un candidato',
        'a_vacancy' => 'una vacante',
        'received' => ['title' => 'Nueva postulación recibida', 'body' => ':name aplicó a ":vacancy".'],
        'status_changed' => ['title' => 'Tu postulación cambió de estado', 'body' => 'Tu postulación a ":vacancy" ahora está :status.'],
        'statuses' => [
            'pending' => 'pendiente',
            'interview' => 'en entrevista',
            'accepted' => 'aceptada',
            'rejected' => 'rechazada',
            'offered' => 'con oferta',
        ],
    ],
];
