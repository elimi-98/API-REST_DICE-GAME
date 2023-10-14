<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();
        $this->call(RoleSeeder::class);

        User::create([
            'name' => 'Karesse',
            'email' => 'karese@gmail.com',
            'email_verified_at' => now(),
            'password' => bcrypt(987654321), // password
            'remember_token' => Str::random(10),
 
         ])->assignRole('admin');

        User::create([
        'name' => 'Virginia',
        'email' => 'virginia@gmail.com',
        'email_verified_at' => now(),
        'password' => bcrypt(123456789), // password
        'remember_token' => Str::random(10),

        ])->assignRole('player');


    }
}