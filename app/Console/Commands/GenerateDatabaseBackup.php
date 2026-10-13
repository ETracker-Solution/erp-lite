<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GenerateDatabaseBackup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:database {--keep=5 : Number of backup files to keep}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate and pre-compress database backup into storage/app/backups';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting database backup generation...');

        $config = config('database.connections.mysql');
        $database = $config['database'] ?? env('DB_DATABASE', 'forge');
        $username = $config['username'] ?? env('DB_USERNAME', 'root');
        $password = $config['password'] ?? env('DB_PASSWORD', '');
        $host = $config['host'] ?? env('DB_HOST', '127.0.0.1');
        $port = $config['port'] ?? env('DB_PORT', '3306');

        $backupDir = storage_path('app/backups');
        if (!file_exists($backupDir)) {
            @mkdir($backupDir, 0755, true);
        }

        $timestamp = date('Y-m-d_H-i-s');
        $baseName = "{$database}_{$timestamp}";
        $sqlFileName = "{$baseName}.sql";
        $zipFileName = "{$baseName}.zip";

        $tempSqlPath = $backupDir . '/' . $sqlFileName;
        $finalZipPath = $backupDir . '/' . $zipFileName;

        // Temporary credentials file
        $tempCnf = tempnam(sys_get_temp_dir(), 'my_dump_');
        $cnfContent = "[client]\nuser=\"{$username}\"\npassword=\"{$password}\"\nhost=\"{$host}\"\nport=\"{$port}\"\n";
        file_put_contents($tempCnf, $cnfContent);

        try {
            // 1. Dump database
            $dumpCommand = 'mysqldump --defaults-extra-file=' . escapeshellarg($tempCnf)
                . ' --single-transaction --quick --skip-lock-tables '
                . escapeshellarg($database)
                . ' > ' . escapeshellarg($tempSqlPath);

            exec($dumpCommand, $dumpOutput, $dumpReturnCode);

            if (file_exists($tempCnf)) {
                @unlink($tempCnf);
            }

            if ($dumpReturnCode !== 0 || !file_exists($tempSqlPath) || filesize($tempSqlPath) === 0) {
                @unlink($tempSqlPath);
                $this->error('Failed to dump database.');
                return 1;
            }

            // 2. Fast ZIP compression (-1 level)
            $zipCommand = 'zip -j -1 ' . escapeshellarg($finalZipPath) . ' ' . escapeshellarg($tempSqlPath);
            exec($zipCommand, $zipOutput, $zipReturnCode);

            @unlink($tempSqlPath);

            if ($zipReturnCode !== 0 || !file_exists($finalZipPath) || filesize($finalZipPath) === 0) {
                @unlink($finalZipPath);
                $this->error('Failed to create zip archive.');
                return 1;
            }

            // 3. Keep latest symlink or pointer
            $latestPointer = $backupDir . '/latest_backup.txt';
            file_put_contents($latestPointer, $finalZipPath);

            // 4. Prune old backups
            $keep = (int) $this->option('keep');
            $files = glob($backupDir . '/*.zip');
            if ($files && count($files) > $keep) {
                usort($files, function ($a, $b) {
                    return filemtime($b) - filemtime($a);
                });
                $toDelete = array_slice($files, $keep);
                foreach ($toDelete as $f) {
                    @unlink($f);
                }
            }

            $sizeMb = round(filesize($finalZipPath) / 1024 / 1024, 2);
            $this->info("Backup created successfully: {$zipFileName} ({$sizeMb} MB)");
            return 0;

        } catch (\Throwable $e) {
            if (file_exists($tempCnf)) {
                @unlink($tempCnf);
            }
            if (file_exists($tempSqlPath)) {
                @unlink($tempSqlPath);
            }
            $this->error('Exception: ' . $e->getMessage());
            return 1;
        }
    }
}
