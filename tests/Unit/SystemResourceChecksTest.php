<?php

use App\Health\Checks\SystemCpuLoadCheck;
use App\Health\Checks\SystemMemoryUsageCheck;
use Spatie\Health\Enums\Status;

it('evaluates memory usage thresholds', function (int $used, int $total, Status $expectedStatus) {
    $check = SystemMemoryUsageCheck::new()
        ->warnWhenUsedMemoryIsAbovePercentage(80)
        ->failWhenUsedMemoryIsAbovePercentage(90)
        ->resolveMemoryUsageUsing(fn (): array => [
            'used' => $used,
            'total' => $total,
            'source' => 'test',
        ]);

    $result = $check->run();

    expect($result->status)->toEqual($expectedStatus);
})->with([
    'ok' => [70, 100, Status::ok()],
    'warning' => [85, 100, Status::warning()],
    'failed' => [95, 100, Status::failed()],
]);

it('evaluates cpu load thresholds', function (int $percentage, Status $expectedStatus) {
    $check = SystemCpuLoadCheck::new()
        ->warnWhenLoadIsAbovePercentage(80)
        ->failWhenLoadIsAbovePercentage(90)
        ->resolveCpuUsageUsing(fn (): array => [
            'percentage' => $percentage,
            'cores' => 4,
            'load_5m' => 3.2,
            'source' => 'test',
        ]);

    $result = $check->run();

    expect($result->status)->toEqual($expectedStatus);
})->with([
    'ok' => [70, Status::ok()],
    'warning' => [80, Status::warning()],
    'failed' => [90, Status::failed()],
]);
