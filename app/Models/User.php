<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements FilamentUser
{
    use HasApiTokens, HasFactory, Notifiable;

    public const ROLES = [
        'super_admin' => 'Super Admin',
        'admin' => 'Admin',
        'operator' => 'Operator',
        'bendahara' => 'Bendahara',
        'viewer' => 'Viewer',
    ];

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->status === 'active';
    }

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'avatar',
        'status',
        'last_login_at',
        'last_login_ip',
        'login_count',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'last_login_at' => 'datetime',
        'password' => 'hashed',
    ];

    // Role checking helpers
    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function isAdmin(): bool
    {
        return in_array($this->role, ['super_admin', 'admin']);
    }

    public function isBendahara(): bool
    {
        return in_array($this->role, ['super_admin', 'admin', 'bendahara']);
    }

    public function isOperator(): bool
    {
        return in_array($this->role, ['super_admin', 'admin', 'operator']);
    }

    public function getRoleLabel(): string
    {
        return self::ROLES[$this->role] ?? $this->role;
    }

    public function getFilamentAvatarUrl(): ?string
    {
        return $this->avatar ? asset('storage/' . $this->avatar) : null;
    }

    public function recordLogin(): void
    {
        $this->update([
            'last_login_at' => now(),
            'last_login_ip' => request()->ip(),
            'login_count' => $this->login_count + 1,
        ]);
    }
}
