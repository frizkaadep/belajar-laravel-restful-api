<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'username' => 'test',
            'name' => 'test',
            'password' => Hash::make('test'), // Use bcrypt for password hashing
            'token' => 'test'
        ]);

        User::create([
            'username' => 'test2',
            'name' => 'test2',
            'password' => Hash::make('test2'), // Use bcrypt for password hashing
            'token' => 'test2'
        ]);
    }
}
