<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Contact;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class SearchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::where('username', 'test')->first();
        for ($i=0; $i < 20; $i++) {
            Contact::create([
                'first_name' => 'First ' . $i,
                'last_name' => 'Last ' . $i,
                'email' => 'test' . $i . '@example.com',
                'phone' => '11111',
                'user_id' => $user->id,
            ])->save();
        }
    }
}
