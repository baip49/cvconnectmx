<?php

return [
    'view' => 'View',
    'tickets' => [
        'reply_received' => ['title' => 'New reply on your ticket', 'body' => ':name replied on ":title".'],
        'claimed' => ['title' => 'Your ticket is being handled', 'body' => ':name claimed your ticket ":title" and will help you now.'],
        'joined' => ['title' => 'A second agent joined', 'body' => ':name joined ticket ":title" as second agent.'],
        'released' => ['title' => 'An agent left the ticket', 'body' => ':name stopped handling ticket ":title".'],
        'closed' => ['title' => 'Ticket closed', 'body' => ':name closed ticket ":title".'],
        'reopened' => ['title' => 'Ticket reopened', 'body' => ':name reopened ticket ":title".'],
        'assigned' => ['title' => 'You have an assigned ticket', 'body' => 'Ticket ":title" was assigned to you.'],
    ],
    'applications' => [
        'a_candidate' => 'A candidate',
        'a_vacancy' => 'a vacancy',
        'received' => ['title' => 'New application received', 'body' => ':name applied to ":vacancy".'],
        'status_changed' => ['title' => 'Your application status changed', 'body' => 'Your application to ":vacancy" is now :status.'],
        'statuses' => [
            'pending' => 'pending',
            'interview' => 'in interview',
            'accepted' => 'accepted',
            'rejected' => 'rejected',
            'offered' => 'offered',
        ],
    ],
];
