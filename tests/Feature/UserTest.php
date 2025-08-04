<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function testRegisterSuccess(): void
    {
        $this->post('/api/users', [
            'username' => 'frizkaade',
            'password' => 'password',
            'name' => 'Frizka Ade Prasurya',
        ])->assertStatus(201)
            ->assertJson([
                'data' => [
                    'username' => 'frizkaade',
                    'name' => 'Frizka Ade Prasurya',
                ],
            ]);
    }

    public function testRegisterFailed(): void
    {
        $this->post('/api/users', [
            'username' => '',
            'password' => '',
            'name' => '',
        ])->assertStatus(400)
            ->assertJson([
                'errors' => [
                    'username' => [
                        'The username field is required.'
                    ],
                    'password' => [
                        'The password field is required.'
                    ],
                    'name' => [
                        'The name field is required.'
                    ],
                ],
            ]);
    }

    public function testRegisterUsernameAlreadyExists(): void
    {
        $this->testRegisterSuccess(); // Register first to create the user
        $this->post('/api/users', [
            'username' => 'frizkaade',
            'password' => 'newpassword',
            'name' => 'Frizka Ade Prasurya',
        ])->assertStatus(400)
            ->assertJson([
                'errors' => [
                    'username' => [
                        'Username already exists. Please choose another one.'
                    ],
                ],
            ]);
    }

    public function testLoginSuccess(): void
    {
        $this->seed(UserSeeder::class);
        $this->post('/api/users/login', [
            'username' => 'test',
            'password' => 'test',
        ])->dump()->assertStatus(200)
            ->assertJson([
                'data' => [
                    'username' => 'test',
                    'name' => 'test',
                ],
            ]);

        $user = User::where('username', 'test')->first();
        self::assertNotNull($user->token);
    }


    public function testLoginFailedUsernameNotFound(): void
    {
        $this->post('/api/users/login', [
            'username' => 'testUser',
            'password' => 'testUser',
        ])->dump()->assertStatus(400)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'username or password is incorrect.'
                    ]
                ],
            ]);
    }

    public function testLoginFailedPasswordWrong(): void
    {
        $this->seed(UserSeeder::class);
        $this->post('/api/users/login', [
            'username' => 'testUser',
            'password' => 'salah',
        ])->dump()->assertStatus(400)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'username or password is incorrect.'
                    ]
                ],
            ]);
    }

    public function testGetSuccess()
    {
        $this->seed(UserSeeder::class);
        $this->get('/api/users/current', [
            'Authorization' => 'test',
        ])->assertStatus(200)
            ->assertJson([
                'data' => [
                    'username' => 'test',
                    'name' => 'test',
                ],
            ]);
    }

    public function testGetUnauthorized()
    {
        $this->seed(UserSeeder::class);
        $this->get('/api/users/current', [

        ])->assertStatus(401)
            ->assertJson([
                'errors' => [
                    'message' => 'Unauthorized'
                ]
            ]);
    }

    public function testGetInvalidToken()
    {
        $this->seed(UserSeeder::class);
        $this->get('/api/users/current', [
            'Authorization' => 'invalidToken',
        ])->assertStatus(401)
            ->assertJson([
                'errors' => [
                    'message' => 'Unauthorized'
                ]
            ]);
    }

    public function testUpdatePasswordSuccess()
    {
        $this->seed(UserSeeder::class);
        $oldUser = User::where('username', 'test')->first();

        $this->patch('/api/users/current', [
            'password' => 'baru'
        ], [
            'Authorization' => 'test'
        ])->assertStatus(200)
            ->assertJson([
                'data' => [
                    'username' => 'test',
                    'name' => 'test',
                ],
            ]);
        $newUser = User::where('username', 'test')->first();
        self::assertNotEquals($oldUser->password, $newUser->password);
    }

    public function testUpdateNameSuccess()
    {
        $this->seed(UserSeeder::class);
        $oldUser = User::where('username', 'test')->first();

        $this->patch('/api/users/current', [
            'name' => 'frizka'
        ], [
            'Authorization' => 'test'
        ])->assertStatus(200)
            ->assertJson([
                'data' => [
                    'username' => 'test',
                    'name' => 'frizka',
                ],
            ]);
        $newUser = User::where('username', 'test')->first();
        self::assertNotEquals($oldUser->name, $newUser->name);
    }

    public function testUpdateFailed()
    {
        $this->seed(UserSeeder::class);
        $oldUser = User::where('username', 'testUser')->first();

        $this->patch('/api/users/current', [
            'name' => 'frizkaadefrizkaadefrizkaadefrizkaadefrizkaadefrizkaadefrizkaadefrizkaadefrizkaadefrizkaadefrizkaadefrizkaadefrizkaadefrizkaadefrizkaadefrizkaadefrizkaadefrizkaadefrizkaadefrizkaadefrizkaadefrizkaadefrizkaadefrizkaadefrizkaadefrizkaade'
        ], [
            'Authorization' => 'test'
        ])->assertStatus(400)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'name' => ['The name field must not be greater than 100 characters.']
                    ]
                ]
            ]);
    }

    public function testLogoutSuccess()
    {
        $this->seed(UserSeeder::class);

        $this->delete(uri: '/api/users/logout', headers: [
            'Authorization' => 'test'
        ])->assertStatus(200)
            ->assertJson([
                "data" => true
            ]);

        $user = User::where('username', 'test')->first();
        self::assertNull($user->token); // Ensure the token is cleared
    }

    public function testLogoutUnauthorized()
    {
        $this->seed(UserSeeder::class);

        $this->delete(uri: '/api/users/logout', headers: [
            'Authorization' => 'salah'
        ])->assertStatus(401)
            ->assertJson([
                'errors' => [
                    'message' => 'Unauthorized'
                ]
            ]);
    }
}
