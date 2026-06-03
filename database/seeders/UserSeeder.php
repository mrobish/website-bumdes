<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Super Admin
        User::updateOrCreate(
            ['email' => 'admin@bumdes.id'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
                'role' => 'super_admin',
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
                'status' => 'active',
            ]
        );

        echo "✓ User seeder completed\n";
    }
}
