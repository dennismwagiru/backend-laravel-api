<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class UserTest extends TestCase
{
    use DatabaseMigrations;
    /**
     * A basic feature test example.
     */
    public function test_user_register(): void
    {
        $payload = [
            'name' => 'John Doe',
            'email' => 'john.doe@barnacle.com',
            'password' => 'Test@1234!',
            'password_confirmation' => 'Test@1234!'
        ];
        $response = $this->post('/api/auth/register', $payload);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'id',
                'name',
                'email',
                'created_at'
            ])
            ->assertJsonPath('name', $payload['name'])
            ->assertJsonPath('email', $payload['email'])
            ->assertJsonMissingPath('data.password');

        $this->assertDatabaseHas('users', [
            "email" => $payload['email']
        ]);
    }

    public function test_user_register_password_confirmation(): void
    {
        $payload = [
            'name' => 'John Doe',
            'email' => 'john.doe@barnacle.com',
            'password' => 'Test@1234!',
            'password_confirmation' => '1234!'
        ];
        $response = $this->post('/api/auth/register', $payload);

        $response->assertStatus(422)
            ->assertJsonPath('error', "Failed Validation")
            ->assertJsonPath('errors.password.0', "The password field confirmation does not match.");

        $this->assertDatabaseMissing('users', [
            "email" => $payload['email']
        ]);
    }

    public function test_user_register_email_format(): void {
        $payload = [
            'name' => 'John Doe',
            'email' => 'john.doebarnacle.com',
            'password' => 'Test@1234!',
            'password_confirmation' => 'Test@1234!'
        ];
        $response = $this->post('/api/auth/register', $payload);

        $response->assertStatus(422)
            ->assertJsonPath('error', "Failed Validation")
            ->assertJsonPath('errors.email.0', "The email field must be a valid email address.");

        $this->assertDatabaseMissing('users', [
            "email" => $payload['email']
        ]);
    }

    public function test_user_register_duplicate_email(): void
    {
        $payload = [
            'name' => 'John Doe',
            'email' => 'john.doe@barnacle.com',
            'password' => 'Test@1234!',
            'password_confirmation' => 'Test@1234!'
        ];

        DB::table('users')->insert(array(
            'name' => $payload['name'],
            'email' => $payload['email'],
            'password' => bcrypt($payload['password']),
        ));

        $response = $this->post('/api/auth/register', $payload);

        $response->assertStatus(422)
            ->assertJsonPath('error', "Failed Validation")
            ->assertJsonPath('errors.email.0', "The email has already been taken.");

        $this->assertDatabaseCount('users', 1);
    }

    public function test_user_login(): void {
        $payload = [
            'email' => 'john.doe@barnacle.com',
            'password' => 'Test@1234!',
        ];
        DB::table('users')->insert(array(
            'name' => 'John Doe',
            'email' => $payload['email'],
            'password' => bcrypt($payload['password']),
        ));

        $response = $this->post('/api/auth/login', $payload);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'token',
            ]);
    }

    public function test_user_login_invalid_credentials(): void {
        $payload = [
            'email' => 'john.doe@barnacle.com',
            'password' => '234!',
        ];
        $response = $this->post('/api/auth/login', $payload);

        $response->assertStatus(401)
            ->assertJsonPath('error', "Incorrect Credentials");
    }
}
