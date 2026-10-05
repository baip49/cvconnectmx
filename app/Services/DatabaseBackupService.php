<?php

namespace App\Services;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class DatabaseBackupService
{
    /**
     * Dump the current database to the local disk, always encrypted.
     *
     * @return array{relative_path: string, size_bytes: int, checksum_sha256: string}
     */
    public function run(): array
    {
        $connection = (string) config('database.default');
        $timestamp = now()->format('Y-m-d_His');
        $directory = 'backups/'.now()->format('Y-m-d');

        Storage::disk('local')->makeDirectory($directory);

        if ($connection === 'sqlite') {
            $relativePath = $directory."/cvconnectmx-{$timestamp}.sqlite";
            $this->dumpSqlite($relativePath);
        } else {
            $relativePath = $directory."/cvconnectmx-{$timestamp}.sql";
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
