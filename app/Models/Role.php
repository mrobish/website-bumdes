<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'permissions',
        'is_default',
        'sort_order',
    ];

    protected $casts = [
        'permissions' => 'array',
        'is_default' => 'boolean',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'role_id');
    }

    /**
     * Check if this role has a specific permission
     */
    public function hasPermission(string $permission): bool
    {
        $permissions = $this->permissions ?? [];

        // Super admin has all permissions
        if (in_array('*', $permissions)) {
            return true;
        }

        return in_array($permission, $permissions);
    }

    /**
     * Check if this role has ANY of the given permissions
     */
    public function hasAnyPermission(array $perms): bool
    {
        foreach ($perms as $perm) {
            if ($this->hasPermission($perm)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Get all available permissions
     */
    public static function getAllPermissions(): array
    {
        return [
            'dashboard' => 'Dashboard',
            'berita' => 'Berita',
            'galeri' => 'Galeri',
            'produk' => 'Produk',
            'unit_usaha' => 'Unit Usaha',
            'keuangan' => 'Keuangan',
            'aset' => 'Aset / Inventaris',
            'penyertaan_modal' => 'Penyertaan Modal',
            'rak' => 'RAK (Rekening Antar Kantor)',
            'konsolidasi' => 'Konsolidasi',
            'laporan' => 'Laporan Keuangan',
            'identitas_bumdes' => 'Identitas BUMDes',
            'struktur' => 'Struktur Kepengurusan',
            'manajemen_user' => 'Manajemen User',
            'manajemen_role' => 'Manajemen Role',
            'system_info' => 'Informasi System',
            'error_log' => 'Error Log',
        ];
    }
}
