<?php

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Create a default user for testing/login
        if (User::where('email', 'admin@gmail.com')->doesntExist()) {
            User::create([
                'name' => 'Admin',
                'email' => 'admin@gmail.com',
                'role' => 'admin',
                'password' => Hash::make('password'), // password: "password"
            ]);
        }

        if (User::where('email', 'user@gmail.com')->doesntExist()) {
            User::create([
                'name' => 'User',
                'email' => 'user@gmail.com',
                'role' => 'user',
                'password' => Hash::make('password'), // password: "password"
            ]);
        }
    }
}
