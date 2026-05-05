<?php

namespace App\Health\Checks;

use Closure;
use Spatie\Health\Checks\Check;
use Spatie\Health\Checks\Result;

class SystemMemoryUsageCheck extends Check
{
    private int $warnAbovePercentage = 80;

    private int $failAbovePercentage = 90;

    private ?Closure $memoryResolver = null;

    public function warnWhenUsedMemoryIsAbovePercentage(int $percentage): static
    {
        $this->warnAbovePercentage = $percentage;

        return $this;
    }

    public function failWhenUsedMemoryIsAbovePercentage(int $percentage): static
    {
        $this->failAbovePercentage = $percentage;

        return $this;
    }

    public function resolveMemoryUsageUsing(callable $resolver): static
    {
        $this->memoryResolver = $resolver instanceof Closure
            ? $resolver
            : Closure::fromCallable($resolver);

        return $this;
    }

    public function run(): Result
    {
        $memory = $this->resolveMemoryUsage();

        if ($memory === null || $memory['total'] <= 0) {
            return Result::make()->warning('Unable to determine system memory usage.');
        }

        $usedPercentage = (int) round(($memory['used'] / $memory['total']) * 100);
        $message = sprintf(
            'System memory usage is %d%% (%s of %s).',
            $usedPercentage,
            $this->formatBytes($memory['used']),
            $this->formatBytes($memory['total'])
        );

        $result = Result::make()
            ->shortSummary("{$usedPercentage}% used")
            ->meta([
                'used_bytes' => $memory['used'],
                'total_bytes' => $memory['total'],
                'used_percentage' => $usedPercentage,
                'source' => $memory['source'],
            ]);

        if ($usedPercentage >= $this->failAbovePercentage) {
            return $result->failed($message);
        }

        if ($usedPercentage >= $this->warnAbovePercentage) {
            return $result->warning($message);
        }

        return $result->ok($message);
    }

    /**
     * @return array{used: int, total: int, source: string}|null
     */
    private function resolveMemoryUsage(): ?array
    {
        if ($this->memoryResolver) {
            $resolved = ($this->memoryResolver)();

            return $resolved ?: null;
        }

        return match (PHP_OS_FAMILY) {
            'Linux' => $this->resolveLinuxMemoryUsage(),
            'Windows' => $this->resolveWindowsMemoryUsage(),
            'Darwin' => $this->resolveDarwinMemoryUsage(),
            default => null,
        };
    }

    /**
     * @return array{used: int, total: int, source: string}|null
     */
    private function resolveLinuxMemoryUsage(): ?array
    {
        $contents = @file_get_contents('/proc/meminfo');

        if ($contents === false) {
            return null;
        }

        $memTotalKb = null;
        $memAvailableKb = null;
        $memFreeKb = null;
        $buffersKb = null;
        $cachedKb = null;

        foreach (preg_split('/\r?\n/', trim($contents)) as $line) {
            if (preg_match('/^MemTotal:\s+(\d+)\s+kB$/', $line, $matches)) {
                $memTotalKb = (int) $matches[1];

                continue;
            }

            if (preg_match('/^MemAvailable:\s+(\d+)\s+kB$/', $line, $matches)) {
                $memAvailableKb = (int) $matches[1];

                continue;
            }

            if (preg_match('/^MemFree:\s+(\d+)\s+kB$/', $line, $matches)) {
                $memFreeKb = (int) $matches[1];

                continue;
            }

            if (preg_match('/^Buffers:\s+(\d+)\s+kB$/', $line, $matches)) {
                $buffersKb = (int) $matches[1];

                continue;
            }

            if (preg_match('/^Cached:\s+(\d+)\s+kB$/', $line, $matches)) {
                $cachedKb = (int) $matches[1];
            }
        }

        if ($memTotalKb === null) {
            return null;
        }

        if ($memAvailableKb === null && $memFreeKb !== null && $buffersKb !== null && $cachedKb !== null) {
            $memAvailableKb = $memFreeKb + $buffersKb + $cachedKb;
        }

        if ($memAvailableKb === null) {
            return null;
        }

        $totalBytes = $memTotalKb * 1024;
        $availableBytes = $memAvailableKb * 1024;

        return [
            'used' => max(0, $totalBytes - $availableBytes),
            'total' => $totalBytes,
            'source' => 'proc_meminfo',
        ];
    }

    /**
     * @return array{used: int, total: int, source: string}|null
     */
    private function resolveWindowsMemoryUsage(): ?array
    {
        if (! function_exists('shell_exec')) {
            return null;
        }

        $output = shell_exec('wmic OS get FreePhysicalMemory,TotalVisibleMemorySize /Value');

        if (! is_string($output)) {
            return null;
        }

        if (! preg_match('/FreePhysicalMemory=(\d+)/i', $output, $freeMatch)) {
            return null;
        }

        if (! preg_match('/TotalVisibleMemorySize=(\d+)/i', $output, $totalMatch)) {
            return null;
        }

        $freeKb = (int) $freeMatch[1];
        $totalKb = (int) $totalMatch[1];

        if ($totalKb <= 0) {
            return null;
        }

        $totalBytes = $totalKb * 1024;
        $freeBytes = $freeKb * 1024;

        return [
            'used' => max(0, $totalBytes - $freeBytes),
            'total' => $totalBytes,
            'source' => 'wmic',
        ];
    }

    /**
     * @return array{used: int, total: int, source: string}|null
     */
    private function resolveDarwinMemoryUsage(): ?array
    {
        if (! function_exists('shell_exec')) {
            return null;
        }

        $totalOutput = shell_exec('sysctl -n hw.memsize');
        $totalBytes = is_string($totalOutput) ? (int) trim($totalOutput) : 0;

        if ($totalBytes <= 0) {
            return null;
        }

        $vmStatOutput = shell_exec('vm_stat');

        if (! is_string($vmStatOutput)) {
            return null;
        }

        if (! preg_match('/page size of (\d+) bytes/i', $vmStatOutput, $pageMatch)) {
            return null;
        }

        $pageSize = (int) $pageMatch[1];
        $freePages = $this->extractVmStatPages($vmStatOutput, 'Pages free');
        $inactivePages = $this->extractVmStatPages($vmStatOutput, 'Pages inactive');
        $speculativePages = $this->extractVmStatPages($vmStatOutput, 'Pages speculative');

        if ($freePages === null || $inactivePages === null || $speculativePages === null) {
            return null;
        }

        $availableBytes = ($freePages + $inactivePages + $speculativePages) * $pageSize;

        return [
            'used' => max(0, $totalBytes - $availableBytes),
            'total' => $totalBytes,
            'source' => 'vm_stat',
        ];
    }

    private function extractVmStatPages(string $vmStatOutput, string $label): ?int
    {
        $pattern = sprintf('/^%s:\s+(\d+)\./mi', preg_quote($label, '/'));

        if (! preg_match($pattern, $vmStatOutput, $matches)) {
            return null;
        }

        return (int) $matches[1];
    }

    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $value = (float) $bytes;
        $unitIndex = 0;

        while ($value >= 1024 && $unitIndex < count($units) - 1) {
            $value /= 1024;
            $unitIndex++;
        }

        return number_format($value, 2).' '.$units[$unitIndex];
    }
}
