<?php

namespace App\Filament\Admin\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SystemInfo extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-server-stack';

    protected static ?string $navigationGroup = '⚙️ System';

    protected static ?string $navigationLabel = 'Informasi System';

    protected static ?string $title = 'Informasi System';

    protected static ?string $slug = 'system-info';

    protected static ?int $navigationSort = 100;

    protected static string $view = 'filament.pages.system-info';

    public array $serverInfo = [];
    public array $dbInfo = [];
    public array $appInfo = [];
    public array $diskInfo = [];

    public function mount(): void
    {
        $this->loadServerInfo();
        $this->loadDatabaseInfo();
        $this->loadAppInfo();
        $this->loadDiskInfo();
    }

    protected function loadServerInfo(): void
    {
        $this->serverInfo = [
            'PHP Version' => PHP_VERSION,
            'Server Software' => $_SERVER['SERVER_SOFTWARE'] ?? 'N/A',
            'OS' => PHP_OS,
            'Memory Limit' => ini_get('memory_limit'),
            'Max Execution Time' => ini_get('max_execution_time') . ' detik',
            'Upload Max Size' => ini_get('upload_max_filesize'),
            'Post Max Size' => ini_get('post_max_size'),
            'OPcache' => extension_loaded('opcache') ? '✅ Aktif' : '❌ Tidak aktif',
            'OPcache Enabled' => (bool) ini_get('opcache.enable') ? 'Ya' : 'Tidak',
            'Extensions' => count(get_loaded_extensions()),
        ];
    }

    protected function loadDatabaseInfo(): void
    {
        $dbName = config('database.connections.mysql.database', 'bumdes');
        $tables = DB::select("SHOW TABLES FROM `{$dbName}`");
        $tableCount = count($tables);

        $totalSize = DB::selectOne("SELECT 
            ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS total_mb
            FROM information_schema.tables 
            WHERE table_schema = ?", [$dbName]);

        $this->dbInfo = [
            'Database Driver' => config('database.default'),
            'Database Name' => $dbName,
            'Total Tables' => $tableCount,
            'Total Size' => ($totalSize->total_mb ?? 0) . ' MB',
            'MySQL Version' => DB::selectOne('SELECT VERSION() AS ver')->ver ?? 'N/A',
            'Connection' => DB::connection()->getPdo() ? '✅ Terhubung' : '❌ Terputus',
        ];
    }

    protected function loadAppInfo(): void
    {
        $newsCount = DB::table('news')->count();
        $productCount = DB::table('products')->count();
        $unitCount = DB::table('business_units')->count();
        $userCount = DB::table('users')->count();
        $transactionCount = DB::table('financial_transactions')->count();
        $galleryCount = DB::table('galleries')->count();
        $coaCount = DB::table('chart_of_accounts')->count();

        $this->appInfo = [
            'Laravel Version' => app()->version(),
            'Filament Version' => \Filament\Filament::version(),
            'PHP Version' => PHP_VERSION,
            'Users' => $userCount,
            'Berita' => $newsCount,
            'Produk' => $productCount,
            'Unit Usaha' => $unitCount,
            'Galeri' => $galleryCount,
            'Transaksi Keuangan' => $transactionCount,
            'COA (Rekening)' => $coaCount,
        ];
    }

    protected function loadDiskInfo(): void
    {
        $total = disk_total_space('/');
        $free = disk_free_space('/');
        $used = $total - $free;
        $usedPercent = round(($used / $total) * 100, 1);

        $this->diskInfo = [
            'Total Disk' => $this->formatBytes($total),
            'Used' => $this->formatBytes($used),
            'Free' => $this->formatBytes($free),
            'Usage' => $usedPercent . '%',
            'Path Website' => base_path(),
            'Path Storage' => storage_path(),
            'Path Logs' => storage_path('logs'),
            'Writable' => is_writable(storage_path()) ? '✅ Ya' : '❌ Tidak',
        ];
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
