<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;

class BackupStatusCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tampilkan status backup database';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $backupDir = storage_path('backups');
        
        if (!is_dir($backupDir)) {
            $this->error('📁 Folder backup tidak ditemukan');
            return 1;
        }

        $this->info('📊 Status Backup Database Survey Kepuasan');
        $this->line('=====================================');

        $files = glob($backupDir . '/survey_kepuasan_*.sql*');
        
        if (empty($files)) {
            $this->warn('⚠️ Tidak ada file backup ditemukan');
            return 0;
        }

        // Sort by modification time (newest first)
        usort($files, function($a, $b) {
            return filemtime($b) - filemtime($a);
        });

        $totalSize = 0;
        $table = [];

        foreach ($files as $file) {
            $fileName = basename($file);
            $fileSize = filesize($file);
            $fileDate = Carbon::createFromTimestamp(filemtime($file));
            $daysOld = $fileDate->diffInDays(Carbon::now());
            
            $totalSize += $fileSize;
            
            $status = $daysOld > 7 ? '⚠️ Lama' : '✅ Baru';
            
            $table[] = [
                $fileName,
                $fileDate->format('d/m/Y H:i:s'),
                $this->formatBytes($fileSize),
                $daysOld . ' hari',
                $status
            ];
        }

        $this->table(
            ['File', 'Tanggal', 'Ukuran', 'Usia', 'Status'],
            $table
        );

        $this->line('');
        $this->info("📈 Total file backup: " . count($files));
        $this->info("💾 Total ukuran: " . $this->formatBytes($totalSize));
        
        $newestBackup = Carbon::createFromTimestamp(filemtime($files[0]));
        $this->info("🕒 Backup terbaru: " . $newestBackup->format('d/m/Y H:i:s'));

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
