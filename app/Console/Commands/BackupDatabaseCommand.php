<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class BackupDatabaseCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:database {--compress : Kompres file backup}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backup database survey kepuasan';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('🔄 Memulai backup database...');

        // Konfigurasi database
        $dbName = config('database.connections.mysql.database');
        $dbUser = config('database.connections.mysql.username');
        $dbPass = config('database.connections.mysql.password');
        $dbHost = config('database.connections.mysql.host');
        $dbPort = config('database.connections.mysql.port');

        // Buat folder backup
        $backupDir = storage_path('backups');
        if (!file_exists($backupDir)) {
            mkdir($backupDir, 0755, true);
        }

        // Generate nama file
        $timestamp = Carbon::now()->format('Ymd_His');
        $backupFile = $backupDir . "/survey_kepuasan_{$timestamp}.sql";

        // Command mysqldump untuk Windows
        $command = sprintf(
            'mysqldump -h %s -P %s -u %s %s %s > %s',
            $dbHost,
            $dbPort,
            $dbUser,
            $dbPass ? "-p{$dbPass}" : '',
            $dbName,
            $backupFile
        );

        // Cek apakah mysqldump tersedia
        exec('where mysqldump', $mysqldumpPath, $mysqldumpCheck);
        
        if ($mysqldumpCheck !== 0) {
            // Fallback: gunakan Laravel DB untuk backup
            $this->warn('⚠️ mysqldump tidak ditemukan, menggunakan Laravel DB...');
            return $this->backupUsingLaravel($dbName, $backupFile);
        }

        // Eksekusi backup dengan mysqldump
        exec($command, $output, $returnCode);

        if ($returnCode !== 0) {
            $this->error('❌ Backup dengan mysqldump gagal, mencoba Laravel DB...');
            return $this->backupUsingLaravel($dbName, $backupFile);
        }

        // Kompres jika diminta
        if ($this->option('compress')) {
            $this->info('🗜️ Mengompres file backup...');
            exec("gzip {$backupFile}");
            $backupFile .= '.gz';
        }

        // Hapus backup lama (>7 hari)
        $this->cleanupOldBackups($backupDir);

        // Tampilkan info
        $fileSize = $this->formatBytes(filesize($backupFile));
        $this->info("✅ Backup berhasil: {$backupFile}");
        $this->info("📊 Ukuran: {$fileSize}");

        return 0;
    }

    /**
     * Hapus backup lama (>7 hari)
     */
    private function cleanupOldBackups($backupDir)
    {
        $this->info('🧹 Membersihkan backup lama...');
        
        $files = glob($backupDir . '/survey_kepuasan_*.sql*');
        $deletedCount = 0;
        
        foreach ($files as $file) {
            if (filemtime($file) < strtotime('-7 days')) {
                unlink($file);
                $deletedCount++;
            }
        }
        
        if ($deletedCount > 0) {
            $this->info("🗑️ {$deletedCount} file backup lama telah dihapus");
        }
    }

    /**
     * Backup menggunakan Laravel DB (fallback)
     */
    private function backupUsingLaravel($dbName, $backupFile)
    {
        try {
            $this->info('🔄 Membuat backup menggunakan Laravel DB...');
            
            // Dapatkan semua tabel
            $tables = DB::select('SHOW TABLES');
            $tableKey = 'Tables_in_' . $dbName;
            
            $sql = "-- Backup Database: {$dbName}\n";
            $sql .= "-- Generated: " . Carbon::now()->format('Y-m-d H:i:s') . "\n\n";
            $sql .= "SET SQL_MODE = \"NO_AUTO_VALUE_ON_ZERO\";\n";
            $sql .= "SET AUTOCOMMIT = 0;\n";
            $sql .= "START TRANSACTION;\n";
            $sql .= "SET time_zone = \"+00:00\";\n\n";
            
            foreach ($tables as $table) {
                $tableName = $table->$tableKey;
                $this->info("📋 Memproses tabel: {$tableName}");
                
                // DROP TABLE
                $sql .= "DROP TABLE IF EXISTS `{$tableName}`;\n";
                
                // CREATE TABLE
                $createTable = DB::select("SHOW CREATE TABLE `{$tableName}`");
                $sql .= $createTable[0]->{'Create Table'} . ";\n\n";
                
                // INSERT DATA
                $rows = DB::table($tableName)->get();
                if ($rows->count() > 0) {
                    $sql .= "LOCK TABLES `{$tableName}` WRITE;\n";
                    $sql .= "INSERT INTO `{$tableName}` VALUES ";
                    
                    $values = [];
                    foreach ($rows as $row) {
                        $rowValues = [];
                        foreach ((array)$row as $value) {
                            if ($value === null) {
                                $rowValues[] = 'NULL';
                            } else {
                                $rowValues[] = "'" . addslashes($value) . "'";
                            }
                        }
                        $values[] = '(' . implode(',', $rowValues) . ')';
                    }
                    
                    $sql .= implode(',', $values) . ";\n";
                    $sql .= "UNLOCK TABLES;\n\n";
                }
            }
            
            $sql .= "COMMIT;\n";
            
            // Tulis ke file
            file_put_contents($backupFile, $sql);
            
            // Hapus backup lama
            $this->cleanupOldBackups(dirname($backupFile));
            
            // Tampilkan info
            $fileSize = $this->formatBytes(filesize($backupFile));
            $this->info("✅ Backup berhasil: {$backupFile}");
            $this->info("📊 Ukuran: {$fileSize}");
            
            return 0;
            
        } catch (\Exception $e) {
            $this->error('❌ Backup gagal: ' . $e->getMessage());
            return 1;
        }
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
