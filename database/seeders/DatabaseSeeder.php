<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@vikaarya07.my.id'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
        );

        User::firstOrCreate(
            ['email' => 'vikaarya21@gmail.com'],
            [
                'name' => 'Setya',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
        );

        User::firstOrCreate(
            ['email' => 'dina@vikaarya07.my.id'],
            [
                'name' => 'Dina Gendz',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
        );

        User::firstOrCreate(
            ['email' => 'tissa@vikaarya07.my.id'],
            [
                'name' => 'Tissa',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
        );

        User::firstOrCreate(
            ['email' => 'guest@vikaarya07.my.id'],
            [
                'name' => 'Tamu',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
        );
    }
}
