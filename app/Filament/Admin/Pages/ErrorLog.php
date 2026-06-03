<?php

namespace App\Filament\Admin\Pages;

use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\File;

class ErrorLog extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = '⚙️ System';

    protected static ?string $navigationLabel = 'Error Log';

    protected static ?string $title = 'Error Log';

    protected static ?string $slug = 'error-log';

    protected static ?int $navigationSort = 101;

    protected static string $view = 'filament.pages.error-log';

    public ?string $selectedFile = null;
    public ?string $logContent = '';
    public array $logFiles = [];
    public int $totalLines = 0;
    public int $errorCount = 0;
    public int $warningCount = 0;

    public function mount(): void
    {
        $this->loadLogFiles();
        $this->selectDefaultFile();
    }

    public function getFormSchema(): array
    {
        return [
            Forms\Components\Select::make('selectedFile')
                ->label('Pilih File Log')
                ->options($this->logFiles)
                ->reactive()
                ->afterStateUpdated(function ($state) {
                    $this->loadLogFile($state);
                })
                ->live(),
        ];
    }

    protected function loadLogFiles(): void
    {
        $logPath = storage_path('logs');
        $files = File::files($logPath);
        $this->logFiles = [];

        foreach ($files as $file) {
            if ($file->getExtension() === 'log') {
                $this->logFiles[$file->getPathname()] = $file->getFilename();
            }
        }

        // Sort newest first
        arsort($this->logFiles);
    }

    protected function selectDefaultFile(): void
    {
        $latest = storage_path('logs/laravel.log');
        if (File::exists($latest)) {
            $this->selectedFile = $latest;
            $this->loadLogFile($latest);
        } elseif (!empty($this->logFiles)) {
            $first = array_key_first($this->logFiles);
            $this->selectedFile = $first;
            $this->loadLogFile($first);
        }
    }

    public function loadLogFile(?string $path): void
    {
        if (!$path || !File::exists($path)) {
            $this->logContent = 'File tidak ditemukan.';
            $this->totalLines = 0;
            $this->errorCount = 0;
            $this->warningCount = 0;
            return;
        }

        $content = File::get($path);
        $lines = explode("\n", $content);
        $this->totalLines = count($lines);

        // Count errors and warnings
        $this->errorCount = 0;
        $this->warningCount = 0;
        foreach ($lines as $line) {
            if (str_contains($line, 'local.ERROR')) $this->errorCount++;
            if (str_contains($line, 'local.WARNING')) $this->warningCount++;
        }

        // Show last 200 lines (newest)
        $recentLines = array_slice($lines, -200);
        $this->logContent = implode("\n", $recentLines);
    }

    public function clearLog(): void
    {
        if ($this->selectedFile && File::exists($this->selectedFile)) {
            File::put($this->selectedFile, '');
            $this->loadLogFile($this->selectedFile);

            Notification::make()
                ->title('Log dibersihkan')
                ->success()
                ->send();
        }
    }

    public function refreshLog(): void
    {
        $this->loadLogFile($this->selectedFile);

        Notification::make()
            ->title('Log di-refresh')
            ->success()
            ->send();
    }
}
