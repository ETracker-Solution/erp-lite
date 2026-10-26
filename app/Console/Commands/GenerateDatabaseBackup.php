<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\ExecutableFinder;
use Symfony\Component\Process\Process;
use ZipArchive;

class GenerateDatabaseBackup extends Command
{
    protected $signature = 'backup:database {--keep=5 : Number of backup files to keep}';

    protected $description = 'Generate and pre-compress database backup into storage/app/backups';

    public function handle(): int
    {
        $this->info('Starting database backup generation...');

        $config = config('database.connections.mysql');
        $database = $config['database'] ?? env('DB_DATABASE', 'forge');
        $username = $config['username'] ?? env('DB_USERNAME', 'root');
        $password = $config['password'] ?? env('DB_PASSWORD', '');
        $host = $config['host'] ?? env('DB_HOST', '127.0.0.1');
        $port = $config['port'] ?? env('DB_PORT', '3306');

        $backupDir = storage_path('app/backups');
        if (! is_dir($backupDir) && ! mkdir($backupDir, 0755, true) && ! is_dir($backupDir)) {
            $this->error('Unable to create the backup directory.');

            return self::FAILURE;
        }

        $mysqldump = $this->findMysqldump();
        if ($mysqldump === null) {
            $this->error('mysqldump was not found. Set MYSQLDUMP_PATH in .env when using a custom installation.');

            return self::FAILURE;
        }

        if (! class_exists(ZipArchive::class)) {
            $this->error('The PHP zip extension is required to create compressed backups.');

            return self::FAILURE;
        }

        $timestamp = date('Y-m-d_H-i-s');
        $baseName = "{$database}_{$timestamp}";
        $sqlFileName = "{$baseName}.sql";
        $zipFileName = "{$baseName}.zip";
        $tempSqlPath = $backupDir.DIRECTORY_SEPARATOR.$sqlFileName;
        $finalZipPath = $backupDir.DIRECTORY_SEPARATOR.$zipFileName;
        $tempCnf = tempnam(sys_get_temp_dir(), 'my_dump_');

        if ($tempCnf === false) {
            $this->error('Unable to create a temporary MySQL credentials file.');

            return self::FAILURE;
        }

        $escape = static fn (string $value): string => str_replace(['\\', '"'], ['\\\\', '\\"'], $value);
        $cnfContent = sprintf(
            "[client]\nuser=\"%s\"\npassword=\"%s\"\nhost=\"%s\"\nport=\"%s\"\n",
            $escape((string) $username),
            $escape((string) $password),
            $escape((string) $host),
            $escape((string) $port),
        );
        file_put_contents($tempCnf, $cnfContent, LOCK_EX);

        try {
            $process = new Process([
                $mysqldump,
                "--defaults-extra-file={$tempCnf}",
                '--single-transaction',
                '--quick',
                '--skip-lock-tables',
                '--routines',
                '--triggers',
                '--events',
                '--hex-blob',
                '--default-character-set=utf8mb4',
                "--result-file={$tempSqlPath}",
                (string) $database,
            ]);
            $process->setTimeout(null);
            $process->run();

            if (! $process->isSuccessful() || ! is_file($tempSqlPath) || filesize($tempSqlPath) === 0) {
                @unlink($tempSqlPath);
                $this->error('Failed to dump database: '.trim($process->getErrorOutput()));

                return self::FAILURE;
            }

            $zip = new ZipArchive;
            if ($zip->open($finalZipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
                @unlink($tempSqlPath);
                $this->error('Failed to create zip archive.');

                return self::FAILURE;
            }

            $zip->addFile($tempSqlPath, $sqlFileName);
            $zip->setCompressionName($sqlFileName, ZipArchive::CM_DEFLATE, 1);

            if (! $zip->close() || ! is_file($finalZipPath) || filesize($finalZipPath) === 0) {
                @unlink($tempSqlPath);
                @unlink($finalZipPath);
                $this->error('Failed to finalize zip archive.');

                return self::FAILURE;
            }

            @unlink($tempSqlPath);
            file_put_contents($backupDir.DIRECTORY_SEPARATOR.'latest_backup.txt', $finalZipPath, LOCK_EX);
            $this->pruneBackups($backupDir, max(1, (int) $this->option('keep')));

            $sizeMb = round(filesize($finalZipPath) / 1024 / 1024, 2);
            $this->info("Backup created successfully: {$zipFileName} ({$sizeMb} MB)");

            return self::SUCCESS;
        } catch (\Throwable $exception) {
            @unlink($tempSqlPath);
            @unlink($finalZipPath);
            $this->error('Exception: '.$exception->getMessage());

            return self::FAILURE;
        } finally {
            @unlink($tempCnf);
        }
    }

    private function findMysqldump(): ?string
    {
        $configured = env('MYSQLDUMP_PATH');
        if (is_string($configured) && $configured !== '' && is_file($configured)) {
            return $configured;
        }

        $found = (new ExecutableFinder)->find('mysqldump');
        if ($found !== null) {
            return $found;
        }

        if (PHP_OS_FAMILY === 'Windows') {
            $xampp = 'C:\\xampp\\mysql\\bin\\mysqldump.exe';

            return is_file($xampp) ? $xampp : null;
        }

        return null;
    }

    private function pruneBackups(string $backupDir, int $keep): void
    {
        $files = glob($backupDir.DIRECTORY_SEPARATOR.'*.zip') ?: [];
        usort($files, static fn (string $left, string $right): int => filemtime($right) <=> filemtime($left));

        foreach (array_slice($files, $keep) as $file) {
            @unlink($file);
        }
    }
}
