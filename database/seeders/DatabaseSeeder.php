<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        User::factory()->create([
            'first_name' => 'Fahim',
            'last_name' => 'al Emroz',
            'email' => 'fahim@demo.com',
            'password' => Hash::make('secret123'),
        ]);

        User::factory()->create([
            'password' => Hash::make('password'),
        ]);

        $this->call(PostSeeder::class);
    }
}
