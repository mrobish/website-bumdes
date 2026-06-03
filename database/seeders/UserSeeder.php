<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $roles = Role::pluck('id', 'slug');

        // Super Admin
        User::updateOrCreate(
            ['email' => 'admin@bumdes.id'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'role' => 'super_admin',
                'role_id' => $roles['super_admin'] ?? null,
                'status' => 'active',
            ]
        );

        // Admin BUMDes
        User::updateOrCreate(
            ['email' => 'admin@bumdeskeude.id'],
            [
                'name' => 'Admin BUMDes',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'role_id' => $roles['admin'] ?? null,
                'status' => 'active',
            ]
        );

        // Bendahara
        User::updateOrCreate(
            ['email' => 'bendahara@bumdeskeude.id'],
            [
                'name' => 'Bendahara',
                'password' => Hash::make('password'),
                'role' => 'bendahara',
                'role_id' => $roles['bendahara'] ?? null,
                'status' => 'active',
            ]
        );

        // Operator
        User::updateOrCreate(
            ['email' => 'operator@bumdeskeude.id'],
            [
                'name' => 'Operator',
                'password' => Hash::make('password'),
                'role' => 'operator',
                'role_id' => $roles['operator'] ?? null,
                'status' => 'active',
            ]
        );

        // Viewer
        User::updateOrCreate(
            ['email' => 'viewer@bumdeskeude.id'],
            [
                'name' => 'Viewer',
                'password' => Hash::make('password'),
                'role' => 'viewer',
                'role_id' => $roles['viewer'] ?? null,
                'status' => 'active',
            ]
        );

        echo "✓ User seeder completed\n";
    }
}
