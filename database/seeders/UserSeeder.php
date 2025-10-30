<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => '3DKubic',
            'email' => 'geral@3dkubic.com',
            'password' => Hash::make('280522'),
            'role' => 'admin',
            'email_verified_at' => now()
        ]);

        User::create([
            'name' => 'João Paiva',
            'email' => 'jpaiva@carbuiberia.com',
            'password' => Hash::make('280522'),
            'role' => 'admin',
            'email_verified_at' => now()
        ]);

        User::create([
            'name' => 'User Teste',
            'email' => 'userteste@carbuiberia.com',
            'password' => Hash::make('280522'),
            'role' => 'user',
            'email_verified_at' => now()
        ]);
    }
}