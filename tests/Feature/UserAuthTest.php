<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class UserAuthTest extends TestCase
{
    use DatabaseTransactions;
    use WithFaker;

    const ROUTE_REGISTER = 'auth.register';
    const ROUTE_LOGIN = 'auth.login';
    const USER_ORIGINAL_PASSWORD = 'Test@1234!';

    /**
     * A basic feature test example.
     */
    public function test_user_auth_register(): void
    {
        $payload = [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->email,
            'password' => self::USER_ORIGINAL_PASSWORD,
            'password_confirmation' => self::USER_ORIGINAL_PASSWORD
        ];
        $response = $this->post(route(self::ROUTE_REGISTER), $payload);

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

    public function test_user_auth_register_password_confirmation(): void
    {
        $payload = [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->email,
            'password' => self::USER_ORIGINAL_PASSWORD,
            'password_confirmation' => $this->faker->password
        ];
        $response = $this->post(route(self::ROUTE_REGISTER), $payload);

        $response->assertStatus(422)
            ->assertJsonPath('error', "Failed Validation")
            ->assertJsonPath('errors.password.0', "The password field confirmation does not match.");

        $this->assertDatabaseMissing('users', [
            "email" => $payload['email']
        ]);
    }

    public function test_user_auth_register_email_format(): void {
        $payload = [
            'name' => $this->faker->name,
            'email' => $this->faker->name,
            'password' => self::USER_ORIGINAL_PASSWORD,
            'password_confirmation' => self::USER_ORIGINAL_PASSWORD
        ];
        $response = $this->post(route(self::ROUTE_REGISTER), $payload);

        $response->assertStatus(422)
            ->assertJsonPath('error', "Failed Validation")
            ->assertJsonPath('errors.email.0', "The email field must be a valid email address.");

        $this->assertDatabaseMissing('users', [
            "email" => $payload['email']
        ]);
    }

    public function test_user_auth_register_duplicate_email(): void
    {
        $payload = [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->email,
            'password' => self::USER_ORIGINAL_PASSWORD,
            'password_confirmation' => self::USER_ORIGINAL_PASSWORD
        ];

        DB::table('users')->insert(array(
            'name' => $payload['name'],
            'email' => $payload['email'],
            'password' => bcrypt($payload['password']),
        ));

        $response = $this->post(route(self::ROUTE_REGISTER), $payload);

        $response->assertStatus(422)
            ->assertJsonPath('error', "Failed Validation")
            ->assertJsonPath('errors.email.0', "The email has already been taken.");

        $this->assertDatabaseCount('users', 1);
    }

    public function test_user_auth_login(): void {
        $payload = [
            'email' => $this->faker->unique()->email,
            'password' => self::USER_ORIGINAL_PASSWORD,
        ];
        DB::table('users')->insert(array(
            'name' => $this->faker->name,
            'email' => $payload['email'],
            'password' => bcrypt($payload['password']),
        ));

        $response = $this->post(route(self::ROUTE_LOGIN), $payload);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'token',
            ]);
    }

    public function test_user_auth_login_invalid_credentials(): void {
        $payload = [
            'email' => $this->faker->unique()->email,
            'password' => $this->faker->name,
        ];
        $response = $this->post(route(self::ROUTE_LOGIN), $payload);

        $response->assertStatus(401)
            ->assertJsonPath('error', "Incorrect Credentials");
    }
}
