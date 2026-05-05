<?php

namespace App\Providers;

use App\Health\Checks\SystemCpuLoadCheck;
use App\Health\Checks\SystemMemoryUsageCheck;
use Illuminate\Support\ServiceProvider;
use Spatie\Health\Checks\Checks\UsedDiskSpaceCheck;
use Spatie\Health\Facades\Health;

class HealthServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $memoryWarning = (int) config('health.system_resources.memory.warning_percentage', 80);
        $memoryCritical = (int) config('health.system_resources.memory.critical_percentage', 90);
        $cpuWarning = (int) config('health.system_resources.cpu.warning_percentage', 80);
        $cpuCritical = (int) config('health.system_resources.cpu.critical_percentage', 90);
        $diskWarning = (int) config('health.system_resources.disk.warning_percentage', 80);
        $diskCritical = (int) config('health.system_resources.disk.critical_percentage', 90);

        Health::checks([
            SystemMemoryUsageCheck::new()
                ->warnWhenUsedMemoryIsAbovePercentage($memoryWarning)
                ->failWhenUsedMemoryIsAbovePercentage($memoryCritical),
            SystemCpuLoadCheck::new()
                ->warnWhenLoadIsAbovePercentage($cpuWarning)
                ->failWhenLoadIsAbovePercentage($cpuCritical),
            UsedDiskSpaceCheck::new()
                ->warnWhenUsedSpaceIsAbovePercentage($diskWarning)
                ->failWhenUsedSpaceIsAbovePercentage($diskCritical),
        ]);
    }
}
