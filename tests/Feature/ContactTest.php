<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Contact;
use Database\Seeders\UserSeeder;
use Database\Seeders\SearchSeeder;
use Database\Seeders\ContactSeeder;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ContactTest extends TestCase
{
    public function testCreateSuccess()
    {
        $this->seed([UserSeeder::class]);

        $this->post('/api/contacts', [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'johndoe@pzn.com',
            'phone' => '1234567890'
        ], [
            'Authorization' => 'test'
        ])->assertStatus(201)
            ->assertJson([
                'data' => [
                    'first_name' => 'John',
                    'last_name' => 'Doe',
                    'email' => 'johndoe@pzn.com',
                    'phone' => '1234567890'
                ]
            ]);
    }

    public function testCreateFailed()
    {
        $this->seed([UserSeeder::class]);

        $this->post('/api/contacts', [
            'first_name' => '',
            'last_name' => 'Doe',
            'email' => 'johndoe',
            'phone' => '1234567890'
        ], [
            'Authorization' => 'test'
        ])->assertStatus(400)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'first_name' => ['The first name field is required.'],
                        'email' => ['The email field must be a valid email address.']
                    ]
                ]
            ]);
    }

    public function testCreateUnauthorized()
    {
        $this->seed([UserSeeder::class]);

        $this->post('/api/contacts', [
            'first_name' => '',
            'last_name' => 'Doe',
            'email' => 'johndoe',
            'phone' => '1234567890'
        ], [
            'Authorization' => 'testsalahtoken'
        ])->assertStatus(401)
            ->assertJson([
                'errors' => [
                    'message' => 'Unauthorized'
                ]
            ]);
    }

    public function testGetSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $this->get('/api/contacts/' . $contact->id, [
            'Authorization' => 'test'
        ])->assertStatus(200)
            ->assertJson([
                'data' => [
                    'first_name' => 'test',
                    'last_name' => 'test',
                    'email' => 'test@pzn.com',
                    'phone' => '1234567890'
                ]
            ]);
    }

    public function testGetNotFound()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        // Pastikan contact ada
        $this->assertNotNull($contact, 'Contact tidak ditemukan setelah seeding');

        $this->get('/api/contacts/' . ($contact->id + 1), [
            'Authorization' => 'test'
        ])->assertStatus(404)
            ->assertJson([
                'errors' => [
                    'message' => 'not found'
                ]
            ]);
    }

    public function testGetOtherUserContact()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $this->get('/api/contacts/' . $contact->id, [
            'Authorization' => 'test2'
        ])->assertStatus(404)
            ->assertJson([
                'errors' => [
                    'message' => 'not found'
                ]
            ]);
    }

    public function testUpdateSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $this->put('/api/contacts/' . $contact->id, [
            'first_name' => 'Jane',
            'last_name' => 'Doe',
            'email' => 'janedoe@pzn.com',
            'phone' => '0987654321'
        ], [
            'Authorization' => 'test'
        ])->assertStatus(200)
            ->assertJson([
                'data' => [
                    'first_name' => 'Jane',
                    'last_name' => 'Doe',
                    'email' => 'janedoe@pzn.com',
                    'phone' => '0987654321'
                ]
            ]);
    }

    public function testUpdateFailedError()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $this->put('/api/contacts/' . $contact->id, [
            'first_name' => '',
            'last_name' => 'Doe',
            'email' => 'janedoe',
            'phone' => '0987654321'
        ], [
            'Authorization' => 'test'
        ])->assertStatus(400)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'first_name' => ['The first name field is required.'],
                        'email' => ['The email field must be a valid email address.']
                    ]
                ]
            ]);
    }

    public function testDeleteSuccess()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $this->delete('/api/contacts/' . $contact->id, [], [
            'Authorization' => 'test'
        ])->assertStatus(200)
            ->assertJson([
                'data' => true
            ]);
    }

    public function testDeleteNotFound()
    {
        $this->seed([UserSeeder::class, ContactSeeder::class]);
        $contact = Contact::query()->limit(1)->first();

        $this->delete('/api/contacts/' . ($contact->id + 1), [], [
            'Authorization' => 'test'
        ])->assertStatus(404)
            ->assertJson([
                'errors' => [
                    'message' => ['not found']
                ]
            ]);
    }

    public function testSearchByFirstName()
    {
        $this->seed([UserSeeder::class, SearchSeeder::class]);

        $response = $this->get('/api/contacts?name=frizka', [
            'Authorization' => 'test'
        ])
            ->assertStatus(200)
            ->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));

        self::assertEquals(10, count($response['data']), 'Jumlah data tidak sesuai');
        self::assertEquals(20, $response['meta']['total'], 'Total data tidak sesuai');
    }

    public function testSearchByLastName()
    {
        $this->seed([UserSeeder::class, SearchSeeder::class]);

        $response = $this->get('/api/contacts?name=last', [
            'Authorization' => 'test'
        ])
            ->assertStatus(200)
            ->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));

        self::assertEquals(10, count($response['data']), 'Jumlah data tidak sesuai');
        self::assertEquals(20, $response['meta']['total'], 'Total data tidak sesuai');
    }

    public function testSearchByEmail()
    {
        $this->seed([UserSeeder::class, SearchSeeder::class]);

        $response = $this->get('/api/contacts?email=test', [
            'Authorization' => 'test'
        ])
            ->assertStatus(200)
            ->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));

        self::assertEquals(10, count($response['data']), 'Jumlah data tidak sesuai');
        self::assertEquals(20, $response['meta']['total'], 'Total data tidak sesuai');
    }

    public function testSearchByPhone()
    {
        $this->seed([UserSeeder::class, SearchSeeder::class]);

        $response = $this->get('/api/contacts?phone=11111', [
            'Authorization' => 'test'
        ])
            ->assertStatus(200)
            ->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));

        self::assertEquals(10, count($response['data']), 'Jumlah data tidak sesuai');
        self::assertEquals(20, $response['meta']['total'], 'Total data tidak sesuai');
    }

    public function testSearchNotFound()
    {
        $this->seed([UserSeeder::class, SearchSeeder::class]);

        $response = $this->get('/api/contacts?name=tdkada', [
            'Authorization' => 'test'
        ])
            ->assertStatus(200)
            ->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));

        self::assertEquals(0, count($response['data']), 'Jumlah data tidak sesuai');
        self::assertEquals(0, $response['meta']['total'], 'Total data tidak sesuai');
    }

    public function testSearchWithPage()
    {
        $this->seed([UserSeeder::class, SearchSeeder::class]);

        $response = $this->get('/api/contacts?size=5&page=2', [
            'Authorization' => 'test'
        ])
            ->assertStatus(200)
            ->json();

        Log::info(json_encode($response, JSON_PRETTY_PRINT));

        self::assertEquals(5, count($response['data']), 'Jumlah data tidak sesuai');
        self::assertEquals(20, $response['meta']['total'], 'Total data tidak sesuai');
        self::assertEquals(2, $response['meta']['current_page'], 'Total data tidak sesuai');
    }
}
