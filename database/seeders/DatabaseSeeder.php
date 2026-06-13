<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'مدير النظام',
            'email' => 'admin@admin.com',
            'password' => Hash::make('password'),
            'phone' => '0500000000',
            'role' => 'admin',
            'status' => 'active',
            'points' => 0,
        ]);

        // User::create([
        //     'name' => 'أحمد محمد',
        //     'email' => 'ahmed@example.com',
        //     'password' => Hash::make('password'),
        //     'phone' => '0511111111',
        //     'role' => 'user',
        //     'status' => 'active',
        //     'points' => 120,
        // ]);

        // User::create([
        //     'name' => 'محمد علي',
        //     'email' => 'mohamed@example.com',
        //     'password' => Hash::make('password'),
        //     'phone' => '0522222222',
        //     'role' => 'user',
        //     'status' => 'active',
        //     'points' => 350,
        // ]);

        // User::create([
        //     'name' => 'علي حسن',
        //     'email' => 'ali@example.com',
        //     'password' => Hash::make('password'),
        //     'phone' => '0533333333',
        //     'role' => 'user',
        //     'status' => 'active',
        //     'points' => 75,
        // ]);
    }
}
