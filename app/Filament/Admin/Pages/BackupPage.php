<?php

namespace App\Filament\Admin\Pages;

use App\Services\BackupService;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;

class BackupPage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-down-tray';
    protected static ?string $navigationGroup = '🔧 Sistem';
    protected static ?string $navigationLabel = 'Backup';
    protected static ?string $title = 'Backup & Restore';
    protected static ?string $slug = 'backup-restore';
    protected static ?int $navigationSort = 3;
    protected static string $view = 'filament.pages.backup';

    public array $backups = [];

    public ?string $backupResult = null;

    public function mount(): void
    {
        $this->loadBackups();
    }

    public function loadBackups(): void
    {
        $service = new BackupService();
        $this->backups = $service->listBackups();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\FileUpload::make('restore_file')
                    ->label('File Backup (.sql)')
                    ->acceptedFileTypes(['application/sql', 'text/plain'])
                    ->rules(['required', 'file', 'max:102400'])
                    ->directory('temp-backups')
                    ->visibility('private'),
            ]);
    }

    /**
     * Download database backup
     */
    public function downloadBackup(): void
    {
        try {
            $service = new BackupService();
            $filePath = $service->createBackup();

            $this->loadBackups();

            $filename = basename($filePath);

            Notification::make()
                ->title('Backup Berhasil')
                ->body("File backup {$filename} berhasil dibuat.")
                ->success()
                ->send();

            // Return file download
            response()->download($filePath, $filename, [
                'Content-Type' => 'application/sql',
            ])->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Backup Gagal')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    /**
     * Restore from uploaded backup file
     */
    public function restoreBackup(): void
    {
        try {
            $data = $this->form->getState();
            $uploadedFile = $data['restore_file'] ?? null;

            if (!$uploadedFile) {
                throw new \RuntimeException('Pilih file backup terlebih dahulu.');
            }

            // Handle UploadedFile or string path
            if ($uploadedFile instanceof UploadedFile) {
                $tempPath = $uploadedFile->getRealPath();
            } else {
                $tempPath = storage_path('app/' . $uploadedFile);
            }

            if (!file_exists($tempPath)) {
                throw new \RuntimeException('File backup tidak ditemukan.');
            }

            $service = new BackupService();
            $service->restoreBackup($tempPath);

            // Clean up temp file
            if ($uploadedFile instanceof UploadedFile) {
                @unlink($tempPath);
            } elseif (file_exists($tempPath)) {
                @unlink($tempPath);
            }

            $this->loadBackups();

            Notification::make()
                ->title('Restore Berhasil')
                ->body('Database berhasil direstore dari file backup.')
                ->success()
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Restore Gagal')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    /**
     * Delete a backup file
     */
    public function deleteBackup(string $filePath): void
    {
        try {
            $service = new BackupService();
            $service->deleteBackup($filePath);

            $this->loadBackups();

            Notification::make()
                ->title('Backup Dihapus')
                ->body('File backup berhasil dihapus.')
                ->success()
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Gagal Menghapus')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }
}
