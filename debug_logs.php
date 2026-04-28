<?php

use App\Models\AuditLog;
use Illuminate\Contracts\Console\Kernel;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

$log = AuditLog::latest()->first();
if ($log) {
    echo 'ID: '.$log->id."\n";
    echo 'Old Data Type: '.gettype($log->old_data)."\n";
    echo 'New Data Type: '.gettype($log->new_data)."\n";
    print_r($log->toArray());
} else {
    echo 'No logs found';
}
