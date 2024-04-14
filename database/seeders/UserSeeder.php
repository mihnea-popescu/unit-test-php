<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Admin accounts
        User::factory()->create([
            'name' => 'Mihnea',
            'email' => 'mihnea@fmi.unibuc.ro',
            'password' => Hash::make('mihnea24'),
            'admin' => 1
        ]);

        User::factory()->create([
            'name' => 'Razvan',
            'email' => 'razvan@fmi.unibuc.ro',
            'password' => Hash::make('razvan24'),
            'admin' => 1
        ]);

        User::factory()->create([
            'name' => 'George',
            'email' => 'george@fmi.unibuc.ro',
            'password' => Hash::make('george24'),
            'admin' => 1
        ]);

        User::factory()->create([
            'name' => 'Albert',
            'email' => 'albert@fmi.unibuc.ro',
            'password' => Hash::make('albert24'),
            'admin' => 1
        ]);

        User::factory()->create([
            'name' => 'Robert',
            'email' => 'robert@fmi.unibuc.ro',
            'password' => Hash::make('robert24'),
            'admin' => 1
        ]);

        User::factory()->count(100)->create();
    }
}
