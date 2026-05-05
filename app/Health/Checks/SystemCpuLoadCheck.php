<?php

namespace App\Health\Checks;

use Closure;
use Spatie\Health\Checks\Check;
use Spatie\Health\Checks\Result;

class SystemCpuLoadCheck extends Check
{
    private int $warnAbovePercentage = 80;

    private int $failAbovePercentage = 90;

    private ?Closure $cpuResolver = null;

    public function warnWhenLoadIsAbovePercentage(int $percentage): static
    {
        $this->warnAbovePercentage = $percentage;

        return $this;
    }

    public function failWhenLoadIsAbovePercentage(int $percentage): static
    {
        $this->failAbovePercentage = $percentage;

        return $this;
    }

    public function resolveCpuUsageUsing(callable $resolver): static
    {
        $this->cpuResolver = $resolver instanceof Closure
            ? $resolver
            : Closure::fromCallable($resolver);

        return $this;
    }

    public function run(): Result
    {
        $cpu = $this->resolveCpuUsage();

        if ($cpu === null) {
            return Result::make()->warning('Unable to determine system CPU load.');
        }

        $percentage = (int) $cpu['percentage'];
        $message = $cpu['message'] ?? "System CPU load is {$percentage}%.";

        $result = Result::make()
            ->shortSummary("{$percentage}% load")
            ->meta(array_filter([
                'cpu_usage_percentage' => $percentage,
                'cpu_cores' => $cpu['cores'] ?? null,
                'load_1m' => $cpu['load_1m'] ?? null,
                'load_5m' => $cpu['load_5m'] ?? null,
                'load_15m' => $cpu['load_15m'] ?? null,
                'source' => $cpu['source'] ?? null,
            ], fn ($value) => $value !== null));

        if ($percentage >= $this->failAbovePercentage) {
            return $result->failed($message);
        }

        if ($percentage >= $this->warnAbovePercentage) {
            return $result->warning($message);
        }

        return $result->ok($message);
    }

    /**
     * @return array{percentage: int, source: string, message?: string, cores?: int, load_1m?: float, load_5m?: float, load_15m?: float}|null
     */
    private function resolveCpuUsage(): ?array
    {
        if ($this->cpuResolver) {
            $resolved = ($this->cpuResolver)();

            return $resolved ?: null;
        }

        return PHP_OS_FAMILY === 'Windows'
            ? $this->resolveWindowsCpuUsage()
            : $this->resolveUnixCpuUsage();
    }

    /**
     * @return array{percentage: int, source: string, message?: string, cores?: int, load_1m?: float, load_5m?: float, load_15m?: float}|null
     */
    private function resolveUnixCpuUsage(): ?array
    {
        if (! function_exists('sys_getloadavg')) {
            return null;
        }

        $loads = sys_getloadavg();

        if ($loads === false || count($loads) < 3) {
            return null;
        }

        $cores = $this->resolveUnixCpuCores();

        if ($cores === null || $cores <= 0) {
            return null;
        }

        $load5 = (float) $loads[1];
        $percentage = (int) round(($load5 / $cores) * 100);

        return [
            'percentage' => $percentage,
            'cores' => $cores,
            'load_1m' => (float) $loads[0],
            'load_5m' => $load5,
            'load_15m' => (float) $loads[2],
            'source' => 'sys_getloadavg',
            'message' => sprintf(
                'CPU load (5m avg) is %.2f over %d cores (~%d%%).',
                $load5,
                $cores,
                $percentage
            ),
        ];
    }

    private function resolveUnixCpuCores(): ?int
    {
        if (PHP_OS_FAMILY === 'Darwin') {
            return $this->resolveDarwinCpuCores();
        }

        $contents = @file_get_contents('/proc/cpuinfo');

        if ($contents === false) {
            return null;
        }

        preg_match_all('/^processor\s*:/m', $contents, $matches);

        $count = count($matches[0]);

        return $count > 0 ? $count : null;
    }

    private function resolveDarwinCpuCores(): ?int
    {
        if (! function_exists('shell_exec')) {
            return null;
        }

        $output = shell_exec('sysctl -n hw.logicalcpu');
        $cores = is_string($output) ? (int) trim($output) : 0;

        return $cores > 0 ? $cores : null;
    }

    /**
     * @return array{percentage: int, source: string, message?: string, cores?: int}|null
     */
    private function resolveWindowsCpuUsage(): ?array
    {
        if (! function_exists('shell_exec')) {
            return null;
        }

        $output = shell_exec('wmic cpu get LoadPercentage,NumberOfLogicalProcessors /Value');

        if (! is_string($output)) {
            return null;
        }

        if (! preg_match_all('/LoadPercentage=(\d+)/i', $output, $loadMatches) || empty($loadMatches[1])) {
            return null;
        }

        $percentage = (int) $loadMatches[1][0];

        preg_match_all('/NumberOfLogicalProcessors=(\d+)/i', $output, $coreMatches);

        $cores = ! empty($coreMatches[1])
            ? array_sum(array_map('intval', $coreMatches[1]))
            : null;

        if ($cores === null) {
            $envCores = getenv('NUMBER_OF_PROCESSORS');
            $cores = $envCores !== false ? (int) $envCores : null;
        }

        return [
            'percentage' => $percentage,
            'cores' => $cores,
            'source' => 'wmic',
            'message' => "CPU load is {$percentage}%.",
        ];
    }
}
