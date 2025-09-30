<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class BackupController extends Controller
{
    public function index()
    {
        $backupDir = storage_path('backups');
        
        // Buat folder backup jika belum ada
        if (!File::exists($backupDir)) {
            File::makeDirectory($backupDir, 0755, true);
        }

        $backups = $this->getBackupFiles();
        $totalSize = $this->formatTotalSize($backups);
        
        return view('admin.backup.index', compact('backups', 'totalSize'));
    }

    public function create()
    {
        try {
            // Jalankan command backup
            \Artisan::call('backup:database', ['--compress' => true]);
            
            $output = \Artisan::output();
            
            return response()->json([
                'success' => true,
                'message' => 'Backup berhasil dibuat!',
                'output' => $output
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat backup: ' . $e->getMessage()
            ], 500);
        }
    }

    public function download($filename)
    {
        $backupDir = storage_path('backups');
        $filePath = $backupDir . '/' . $filename;
        
        if (!File::exists($filePath)) {
            abort(404, 'File backup tidak ditemukan');
        }

        return response()->download($filePath, $filename);
    }

    public function delete($filename)
    {
        try {
            $backupDir = storage_path('backups');
            $filePath = $backupDir . '/' . $filename;
            
            if (!File::exists($filePath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'File backup tidak ditemukan'
                ], 404);
            }

            File::delete($filePath);
            
            return response()->json([
                'success' => true,
                'message' => 'Backup berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus backup: ' . $e->getMessage()
            ], 500);
        }
    }

    public function cleanup()
    {
        try {
            \Artisan::call('backup:cleanup');
            $output = \Artisan::output();
            
            return response()->json([
                'success' => true,
                'message' => 'Cleanup backup lama berhasil!',
                'output' => $output
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal melakukan cleanup: ' . $e->getMessage()
            ], 500);
        }
    }

    public function status()
    {
        try {
            \Artisan::call('backup:status');
            $output = \Artisan::output();
            
            return response()->json([
                'success' => true,
                'output' => $output
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mendapatkan status: ' . $e->getMessage()
            ], 500);
        }
    }

    private function getBackupFiles()
    {
        $backupDir = storage_path('backups');
        $files = glob($backupDir . '/survey_kepuasan_*.sql*');
        
        $backups = [];
        
        foreach ($files as $file) {
            $fileName = basename($file);
            $fileSize = File::size($file);
            $fileDate = Carbon::createFromTimestamp(File::lastModified($file));
            $daysOld = $fileDate->diffInDays(Carbon::now());
            
            $backups[] = [
                'filename' => $fileName,
                'size' => $this->formatBytes($fileSize),
                'size_bytes' => $fileSize,
                'created_at' => $fileDate,
                'days_old' => $daysOld,
                'is_old' => $daysOld > 7,
                'is_compressed' => str_ends_with($fileName, '.gz')
            ];
        }
        
        // Sort by creation time (newest first)
        usort($backups, function($a, $b) {
            return $b['created_at']->timestamp - $a['created_at']->timestamp;
        });
        
        return collect($backups);
    }

    private function formatBytes($size, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $size > 1024 && $i < count($units) - 1; $i++) {
            $size /= 1024;
        }
        
        return round($size, $precision) . ' ' . $units[$i];
    }

    private function formatTotalSize($backups)
    {
        $totalBytes = collect($backups)->sum('size_bytes');
        return $this->formatBytes($totalBytes);
    }
}
