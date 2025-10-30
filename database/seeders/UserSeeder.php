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
            'name' => 'Gonçalo Amorim',
            'email' => 'gamorim@hugarogroup.com',
            'password' => Hash::make('gamorim2025'),
            'role' => 'admin',
            'email_verified_at' => now()
        ]);

        User::create([
            'name' => 'Gabriel Galvez',
            'email' => 'gerencia@petroilum.com',
            'password' => Hash::make('gerencia2025'),
            'role' => 'user',
            'email_verified_at' => now()
        ]);

        User::create([
            'name' => 'Lassalete Figueiredo',
            'email' => 'lfigueiredo@gestroilenergy.com',
            'password' => Hash::make('lfigueiredo2025'),
            'role' => 'operator',
            'email_verified_at' => now()
        ]);
    }
}