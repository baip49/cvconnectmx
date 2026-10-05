<?php

namespace App\Services;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class DatabaseBackupService
{
    /**
     * Dump the requested scope to the local disk, always encrypted.
     *
     * @return array{relative_path: string, size_bytes: int, checksum_sha256: string}
     */
    public function run(string $scope = 'database'): array
    {
        $connection = (string) config('database.default');
        $timestamp = now()->format('Y-m-d_His');
        $directory = 'backups/'.now()->format('Y-m-d');

        Storage::disk('local')->makeDirectory($directory);

        $relativePath = match ($scope) {
            'files' => $directory."/cvconnectmx-files-{$timestamp}.zip",
            'database' => $connection === 'sqlite'
                ? $directory."/cvconnectmx-{$timestamp}.sqlite"
                : $directory."/cvconnectmx-{$timestamp}.sql",
            default => throw new RuntimeException("Alcance de respaldo desconocido: [{$scope}]."),
        };

        if ($scope === 'files') {
            $this->dumpFiles($relativePath);
        } elseif ($connection === 'sqlite') {
            $this->dumpSqlite($relativePath);
        } else {
            $this->dumpMysql($relativePath);
        }

        $contents = (string) Storage::disk('local')->get($relativePath);

        $encryptedContents = Crypt::encryptString($contents);

        Storage::disk('local')->put($relativePath, $encryptedContents);

        return [
            'relative_path' => $relativePath,
            'size_bytes' => Storage::disk('local')->size($relativePath),
            'checksum_sha256' => hash('sha256', $encryptedContents),
        ];
    }

    /**
     * Zip the uploaded candidate files (CVs and documents) from the local disk.
     */
    protected function dumpFiles(string $relativePath): void
    {
        if (! class_exists(\ZipArchive::class)) {
            throw new RuntimeException('La extensión ZIP de PHP no está instalada.');
        }

        $disk = Storage::disk('local');
        $temporaryPath = tempnam(sys_get_temp_dir(), 'backup-files').'.zip';

        $zip = new \ZipArchive;

        if ($zip->open($temporaryPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            throw new RuntimeException('No se pudo crear el archivo ZIP temporal.');
        }

        foreach (['candidate-cvs', 'candidate-documents'] as $directory) {
            if (! $disk->directoryExists($directory)) {
                continue;
            }

            foreach ($disk->allFiles($directory) as $file) {
                $contents = $disk->get($file);

                if ($contents !== null) {
                    $zip->addFromString($file, $contents);
                }
            }
        }

        $zip->close();

        $contents = file_get_contents($temporaryPath);
        @unlink($temporaryPath);

        if ($contents === false) {
            throw new RuntimeException('No se pudo leer el archivo ZIP temporal.');
        }

        $disk->put($relativePath, $contents);
    }

    protected function dumpSqlite(string $relativePath): void
    {
        $databasePath = (string) config('database.connections.sqlite.database');

        if ($databasePath === ':memory:' || ! is_file($databasePath)) {
            throw new RuntimeException("No hay archivo SQLite que respaldar: [{$databasePath}].");
        }

        $contents = file_get_contents($databasePath);

        if ($contents === false) {
            throw new RuntimeException("No se pudo leer el archivo SQLite: [{$databasePath}].");
        }

        Storage::disk('local')->put($relativePath, $contents);
    }

    protected function dumpMysql(string $relativePath): void
    {
        $binary = (string) (config('services.backup.mysqldump_path') ?: 'mysqldump');

        if ($binary !== 'mysqldump' && ! is_executable($binary)) {
            throw new RuntimeException("mysqldump no encontrado en [{$binary}]. Configura MYSQLDUMP_PATH.");
        }

        $config = config('database.connections.'.config('database.default'));

        $command = [
            $binary,
            '--host='.$config['host'],
            '--port='.$config['port'],
            '--user='.$config['username'],
            '--single-transaction',
            '--routines',
            '--events',
            $config['database'],
        ];

        $environment = [];
        if (! empty($config['password'])) {
            $environment['MYSQL_PWD'] = $config['password'];
        }

        $result = Process::env($environment)->run($command);

        if (! $result->successful()) {
            throw new RuntimeException('mysqldump falló: '.mb_substr(trim($result->errorOutput() ?: $result->output()), 0, 500));
        }

        Storage::disk('local')->put($relativePath, $result->output());
    }
}
