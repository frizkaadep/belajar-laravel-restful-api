<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\Contact;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AddressSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $contact = Contact::query()->limit(1)->first();
        Address::create([
            'street' => 'streetTest',
            'city' => 'citiTest',
            'province' => 'provinceTest',
            'country' => 'countryTest',
            'postal_code' => '12345',
            'contact_id' => $contact->id // Assuming the Address model has a contact_id
        ]);
    }
}
