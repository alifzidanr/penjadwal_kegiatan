<?php
// database/seeders/UserSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Create default users (without role)
        User::create([
            'username' => 'admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'nama_lengkap' => 'Administrator'
        ]);

        User::create([
            'username' => 'user1',
            'email' => 'user1@example.com',
            'password' => Hash::make('password'),
            'nama_lengkap' => 'User Demo 1'
        ]);

        User::create([
            'username' => 'user2',
            'email' => 'user2@example.com',
            'password' => Hash::make('password'),
            'nama_lengkap' => 'User Demo 2'
        ]);
    }
}