<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            QualificationSeeder::class,
            IraqiUniversitiesSeeder::class,
            PortalSeeder::class,
        ]);

        User::create([
            'name' => 'مدير النظام',
            'email' => 'admin@admin.com',
            'national_id' => '0000000000',
            'password' => Hash::make('password'),
            'phone' => '0500000000',
            'role' => 'admin',
            'status' => 'active',
            'points' => 0,
        ]);
    }
}
