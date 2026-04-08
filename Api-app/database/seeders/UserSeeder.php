<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'email' => 'admin@gmail.com',
                'name' => 'admin',
                'role' => 'super_admin',
                'password' => Hash::make('admin123'),
                'email_verified_at' => now(),
                'phone_verified_at' => now(),
            ],
            [
                'email' => 'rafaalmaqdis53@gmail.com',
                'name' => 'rafa',
                'role' => 'user',
                'password' => Hash::make('rafa1234'),
                'email_verified_at' => now(),
                'phone_verified_at' => now(),
                'phone' => "6285724215989",

            ]
        ];
        foreach($users as $user){
            User::create($user);
        }
    }
}
