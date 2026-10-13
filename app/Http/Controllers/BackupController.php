<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class BackupController extends Controller
{
    /**
     * Download the latest database backup ZIP file instantly.
     * Uses pre-generated zip if available, or generates fresh if missing.
     */
    public function download(Request $request)
    {
        // Restrict to authorized head office users or system admins
        if (auth()->user()?->employee?->user_of && auth()->user()->employee->user_of !== 'ho') {
            abort(403, 'Unauthorized access to database backup.');
        }

        $backupDir = storage_path('app/backups');
        $latestFile = $this->getLatestBackupZip($backupDir);

        // If no pre-generated backup exists, create one now
        if (!$latestFile || !file_exists($latestFile)) {
            $exitCode = Artisan::call('backup:database');
            $latestFile = $this->getLatestBackupZip($backupDir);
        }

        if (!$latestFile || !file_exists($latestFile)) {
            abort(500, 'Unable to locate or generate database backup.');
        }

        $downloadName = basename($latestFile);

        $response = new BinaryFileResponse($latestFile);
        $response->setContentDisposition(
            ResponseHeaderBag::DISPOSITION_ATTACHMENT,
            $downloadName
        );
        $response->headers->set('Content-Type', 'application/zip');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', '0');
        $response->headers->set('Cache-Control', 'must-revalidate, post-check=0, pre-check=0');

        return $response;
    }

    /**
     * Optional endpoint to generate a brand new backup immediately
     */
    public function generateNow(Request $request)
    {
        if (auth()->user()?->employee?->user_of && auth()->user()->employee->user_of !== 'ho') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        Artisan::call('backup:database');
        $backupDir = storage_path('app/backups');
        $latestFile = $this->getLatestBackupZip($backupDir);

        return response()->json([
            'success' => true,
            'message' => 'Fresh backup generated successfully.',
            'filename' => $latestFile ? basename($latestFile) : null,
            'size_mb' => $latestFile && file_exists($latestFile) ? round(filesize($latestFile) / 1024 / 1024, 2) : 0,
        ]);
    }

    /**
     * Helper to find the newest backup zip file
     */
    private function getLatestBackupZip(string $backupDir): ?string
    {
        $pointer = $backupDir . '/latest_backup.txt';
        if (file_exists($pointer)) {
            $path = trim(file_get_contents($pointer));
            if ($path && file_exists($path)) {
                return $path;
            }
        }

        $files = glob($backupDir . '/*.zip');
        if (!$files) {
            return null;
        }

        usort($files, function ($a, $b) {
            return filemtime($b) - filemtime($a);
        });

        return $files[0] ?? null;
    }
}


