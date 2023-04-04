<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::factory()->count(10)->create();
        User::create([
            'name' => 'Cuytamvan',
            'email' => 'test@example.local',
            'password' => Hash::make('password'),
        ]);
    }
}
