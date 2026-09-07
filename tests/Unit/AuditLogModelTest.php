<?php

use App\Models\AuditLog;

test('el modelo de auditoria conserva sus cargas estructuradas', function () {
    $log = new AuditLog([
        'old_data' => ['status' => 'draft'],
        'new_data' => ['status' => 'published'],
    ]);

    expect($log->old_data)->toBe(['status' => 'draft'])
        ->and($log->new_data)->toBe(['status' => 'published']);
});
