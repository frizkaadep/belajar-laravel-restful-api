<?php

namespace Tests\Feature;

use App\Models\Address;
use Tests\TestCase;
use App\Models\Contact;
use Database\Seeders\UserSeeder;
use Database\Seeders\AddressSeeder;
use Database\Seeders\ContactSeeder;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AddressTest extends TestCase
{
    public function testCreateSucess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $this->post('/api/contacts/' . $contact->id . '/addresses',
        [
            'street' => '123 Main St',
            'city' => 'Anytown',
            'province' => 'Anystate',
            'country' => 'Anycountry',
            'postal_code' => '12345'
        ],
        [
            'Authorization' => 'test'
        ])->assertStatus(201)
          ->assertJson([
            'data' => [
                'street' => '123 Main St',
                'city' => 'Anytown',
                'province' => 'Anystate',
                'country' => 'Anycountry',
                'postal_code' => '12345'
            ]
          ]);
    }

    public function testCreateFailed()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $this->post('/api/contacts/' . $contact->id . '/addresses',
        [
            'street' => '123 Main St',
            'city' => 'Anytown',
            'province' => 'Anystate',
            'country' => '',
            'postal_code' => '12345'
        ],
        [
            'Authorization' => 'test'
        ])->assertStatus(400)
          ->assertJson([
            'errors' => [
                'message' => [
                    'country' => ['The country field is required.']
                ]
            ]
          ]);
    }

    public function testCreateContactNotFound()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $this->post('/api/contacts/' . ($contact->id + 1) . '/addresses',
        [
            'street' => '123 Main St',
            'city' => 'Anytown',
            'province' => 'Anystate',
            'country' => 'Anycountry',
            'postal_code' => '12345'
        ],
        [
            'Authorization' => 'test'
        ])->assertStatus(404)
          ->assertJson([
            'errors' => [
                'message' => 'not found'
            ]
          ]);
    }

    public function testGetAddressSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $contact = Contact::query()->limit(1)->first();
        $address = Address::where('contact_id', $contact->id)->first();

        $this->get('/api/contacts/' . $contact->id . '/addresses/' . $address->id, [
            'Authorization' => 'test'
        ])->assertStatus(200)
          ->assertJson([
            'data' => [
                'street' => 'streetTest',
                'city' => 'citiTest',
                'province' => 'provinceTest',
                'country' => 'countryTest',
                'postal_code' => '12345'
            ]
          ]);
    }

    public function testGetAddressNotFound()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $this->get('/api/contacts/' . $contact->id . '/addresses/' . ($contact->id + 1), [
            'Authorization' => 'test'
        ])->assertStatus(404)
          ->assertJson([
            'errors' => [
                'message' => 'not found'
            ]
          ]);
    }

    public function testUpdateAddressSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $contact = Contact::query()->limit(1)->first();
        $address = Address::where('contact_id', $contact->id)->first();

        $this->put('/api/contacts/' . $contact->id . '/addresses/' . $address->id,
            [
                'street' => '456 Elm St',
                'city' => 'Othertown',
                'province' => 'Otherstate',
                'country' => 'Othercountry',
                'postal_code' => '67890'
            ],
            [
                'Authorization' => 'test'
            ]
        )->assertStatus(200)
            ->assertJson([
            'data' => [
                'street' => '456 Elm St',
                'city' => 'Othertown',
                'province' => 'Otherstate',
                'country' => 'Othercountry',
                'postal_code' => '67890'
            ]
          ]);
    }

    public function testUpdateAddressFailed()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $contact = Contact::query()->limit(1)->first();
        $address = Address::where('contact_id', $contact->id)->first();

        $this->put('/api/contacts/' . $contact->id . '/addresses/' . $address->id, [
            'street' => 'streetTest',
            'city' => 'Othertown',
            'province' => 'Otherstate',
            'country' => '',
            'postal_code' => '67890'
        ], [
            'Authorization' => 'test'
        ])->assertStatus(400)
          ->assertJson([
            'errors' => [
                'message' => [
                    'country' => ['The country field is required.']
                ]
            ]
          ]);
    }

    public function testUpdateAddressNotFound()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $address = Address::query()->limit(1)->first();

        $this->put('/api/contacts/' . $address->contact_id . '/addresses/' . ($address->id + 1), [
            'street' => '456 Elm St',
            'city' => 'Othertown',
            'province' => 'Otherstate',
            'country' => 'Othercountry',
            'postal_code' => '67890'
        ], [
            'Authorization' => 'test'
        ])->assertStatus(404)
          ->assertJson([
            'errors' => [
                'message' => 'not found'
            ]
          ]);
    }

    public function testDeleteAddressSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $address = Address::query()->limit(1)->first();

        $this->delete('/api/contacts/' . $address->contact_id . '/addresses/' . $address->id, [], [
            'Authorization' => 'test'
        ])->assertStatus(200)
          ->assertJson([
            'data' => true
          ]);
    }

    public function testDeleteAddressNotFound()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $address = Address::query()->limit(1)->first();

        $this->delete('/api/contacts/' . $address->contact_id . '/addresses/' . ($address->id + 1), [], [
            'Authorization' => 'test'
        ])->assertStatus(404)
          ->assertJson([
            'errors' => [
                'message' => 'not found'
            ]
          ]);
    }

    public function testListAddressSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $this->get('/api/contacts/' . $contact->id . '/addresses', [
            'Authorization' => 'test'
        ])->assertStatus(200)
          ->assertJson([
            'data' => [
                [
                    'street' => 'streetTest',
                    'city' => 'citiTest',
                    'province' => 'provinceTest',
                    'country' => 'countryTest',
                    'postal_code' => '12345'
                ]
            ]
          ]);
    }

    public function testListAddressNotFound()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class, AddressSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $this->get('/api/contacts/' . ($contact->id + 1) . '/addresses', [
            'Authorization' => 'test'
        ])->assertStatus(404)
          ->assertJson([
            'errors' => [
                'message' => 'not found'
            ]
          ]);
    }
}
