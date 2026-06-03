<?php

namespace App\Services;

use Carbon\Carbon;

class BackupService
{
    protected string $backupDir;

    public function __construct()
    {
        $this->backupDir = storage_path('app/backups');

        if (!is_dir($this->backupDir)) {
            mkdir($this->backupDir, 0755, true);
        }
    }

    /**
     * Create a database backup using mysqldump
     */
    public function createBackup(): string
    {
        $timestamp = Carbon::now()->format('Y-m-d_H-i-s');
        $filename = "bumdes_{$timestamp}.sql";
        $filePath = $this->backupDir . '/' . $filename;

        $host = config('database.connections.mysql.host', '127.0.0.1');
        $port = config('database.connections.mysql.port', '3306');
        $database = config('database.connections.mysql.database', 'bumdes');
        $username = config('database.connections.mysql.username', 'bumdes');
        $password = config('database.connections.mysql.password', '');

        $cmd = sprintf(
            'mysqldump -h %s -P %s -u %s %s > %s 2>&1',
            escapeshellarg($host),
            escapeshellarg($port),
            escapeshellarg($username),
            escapeshellarg($database),
            escapeshellarg($filePath)
        );

        // Set password via env
        $env = "MYSQL_PWD=" . escapeshellarg($password) . " ";
        $fullCmd = $env . $cmd;

        exec($fullCmd, $output, $returnCode);

        if ($returnCode !== 0) {
            $error = implode("\n", $output);
            throw new \RuntimeException("Backup gagal: {$error}");
        }

        if (!file_exists($filePath)) {
            throw new \RuntimeException("File backup tidak ditemukan setelah dump.");
        }

        return $filePath;
    }

    /**
     * Restore database from a .sql file
     */
    public function restoreBackup(string $filePath): bool
    {
        if (!file_exists($filePath)) {
            throw new \RuntimeException("File backup tidak ditemukan: {$filePath}");
        }

        $host = config('database.connections.mysql.host', '127.0.0.1');
        $port = config('database.connections.mysql.port', '3306');
        $database = config('database.connections.mysql.database', 'bumdes');
        $username = config('database.connections.mysql.username', 'bumdes');
        $password = config('database.connections.mysql.password', '');

        $cmd = sprintf(
            'mysql -h %s -P %s -u %s %s < %s 2>&1',
            escapeshellarg($host),
            escapeshellarg($port),
            escapeshellarg($username),
            escapeshellarg($database),
            escapeshellarg($filePath)
        );

        $env = "MYSQL_PWD=" . escapeshellarg($password) . " ";
        $fullCmd = $env . $cmd;

        exec($fullCmd, $output, $returnCode);

        if ($returnCode !== 0) {
            $error = implode("\n", $output);
            throw new \RuntimeException("Restore gagal: {$error}");
        }

        return true;
    }

    /**
     * List all backup files with metadata
     */
    public function listBackups(): array
    {
        $backups = [];

        if (!is_dir($this->backupDir)) {
            return $backups;
        }

        $files = glob($this->backupDir . '/*.sql');

        foreach ($files as $file) {
            $backups[] = [
                'name' => basename($file),
                'path' => $file,
                'size' => $this->formatBytes(filesize($file)),
                'size_bytes' => filesize($file),
                'date' => Carbon::createFromTimestamp(filemtime($file))->format('d-m-Y H:i:s'),
                'timestamp' => filemtime($file),
            ];
        }

        // Sort by date descending (newest first)
        usort($backups, fn ($a, $b) => $b['timestamp'] - $a['timestamp']);

        return $backups;
    }

    /**
     * Delete a backup file
     */
    public function deleteBackup(string $filePath): bool
    {
        if (!file_exists($filePath)) {
            throw new \RuntimeException("File backup tidak ditemukan: {$filePath}");
        }

        // Security: only allow deleting from backup directory
        if (strpos(realpath($filePath), realpath($this->backupDir)) !== 0) {
            throw new \RuntimeException("Akses ditolak: file di luar direktori backup.");
        }

        return unlink($filePath);
    }

    protected function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));
        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}
