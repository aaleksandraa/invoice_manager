<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'name' => 'Aleksandra',
            'email' => 'wizionar@gmail.com',
            'password' => Hash::make('aleksandra2025'), // Promijenite lozinku po želji
        ]);
    }
}
