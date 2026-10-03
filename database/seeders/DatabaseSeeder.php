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
                'username' => 'administrator',
                'role' => 'admin',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
        );

        User::firstOrCreate(
            ['email' => 'vikaarya21@gmail.com'],
            [
                'name' => 'Setya',
                'username' => 'setya',
                'role' => 'prioritas_dana',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
        );

        User::firstOrCreate(
            ['email' => 'dina@vikaarya07.my.id'],
            [
                'name' => 'Dina Gendz',
                'username' => 'dina-gendz',
                'role' => 'prioritas_dana',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
        );

        User::firstOrCreate(
            ['email' => 'tissa@vikaarya07.my.id'],
            [
                'name' => 'Tissa',
                'username' => 'tissa',
                'role' => 'prioritas_dana',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
        );

        User::firstOrCreate(
            ['email' => 'putri@vikaarya07.my.id'],
            [
                'name' => 'Putri GAJE',
                'username' => 'putri-gaje',
                'role' => 'prioritas_dana',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
        );

        User::firstOrCreate(
            ['email' => 'tata@vikaarya07.my.id'],
            [
                'name' => 'Tata MESUM',
                'username' => 'tata-mesum',
                'role' => 'prioritas_dana',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
        );

        User::firstOrCreate(
            ['email' => 'mgu@vikaarya07.my.id'],
            [
                'name' => 'Multiguna',
                'username' => 'multiguna',
                'role' => 'multiguna',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
        );

        User::firstOrCreate(
            ['email' => 'lp@vikaarya07.my.id'],
            [
                'name' => 'Landing Page',
                'username' => 'landing-page',
                'role' => 'landing_page',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
        );

        User::firstOrCreate(
            ['email' => 'guest@vikaarya07.my.id'],
            [
                'name' => 'Tamu',
                'username' => 'tamu',
                'role' => 'multiguna',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ],
        );
    }
}
