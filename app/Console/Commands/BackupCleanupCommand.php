<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;

class BackupCleanupCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:cleanup {--days=7 : Hapus backup lebih dari X hari}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Hapus backup database lama';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $days = $this->option('days');
        $backupDir = storage_path('backups');
        
        if (!is_dir($backupDir)) {
            $this->info('📁 Folder backup tidak ditemukan');
            return 0;
        }

        $this->info("🧹 Membersihkan backup lebih dari {$days} hari...");

        $files = glob($backupDir . '/survey_kepuasan_*.sql*');
        $deletedCount = 0;
        $totalSize = 0;

        foreach ($files as $file) {
            $fileAge = Carbon::createFromTimestamp(filemtime($file));
            $daysOld = $fileAge->diffInDays(Carbon::now());

            if ($daysOld > $days) {
                $fileSize = filesize($file);
                $totalSize += $fileSize;
                
                if (unlink($file)) {
                    $deletedCount++;
                    $this->line("🗑️ Dihapus: " . basename($file) . " ({$this->formatBytes($fileSize)})");
                }
            }
        }

        if ($deletedCount > 0) {
            $this->info("✅ {$deletedCount} file backup lama telah dihapus");
            $this->info("💾 Ruang yang dibebaskan: {$this->formatBytes($totalSize)}");
        } else {
            $this->info("ℹ️ Tidak ada backup lama yang perlu dihapus");
        }

        return 0;
    }

    /**
     * Format ukuran file
     */
    private function formatBytes($size, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $size > 1024 && $i < count($units) - 1; $i++) {
            $size /= 1024;
        }
        
        return round($size, $precision) . ' ' . $units[$i];
    }
}
