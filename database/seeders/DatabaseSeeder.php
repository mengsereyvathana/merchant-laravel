<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Enums\RoleTypeEnum;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        \App\Models\Scheme::create([
            'name' => 'VIP',
            'type' => 'large',
            'scheme_price' => 100,
        ]);

        \App\Models\User::factory()->create([
            'name' => 'admin',
            'email' => 'admin@merchant.com',
            'verified' => true,
            'role' => RoleTypeEnum::ADMIN,
            'password' => Hash::make('12345678')
        ]);
    }
}
