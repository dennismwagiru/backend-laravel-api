<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\PasswordResetSuccess;
use Exception;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Password;
use Tests\TestCase;

class UserAuthTest extends TestCase
{
    use WithFaker, DatabaseTransactions;

    const ROUTE_REGISTER = 'auth.register';
    const ROUTE_LOGIN = 'auth.login';
    const ROUTE_FORGOT_PASSWORD = 'auth.password.forgot';
    const ROUTE_RESET_PASSWORD = 'auth.password.reset';
    const USER_ORIGINAL_PASSWORD = 'Test@1234!';

    /**
     * Test Successful User Registration
     *
     * @return void
     */
    public function test_user_auth_register(): void
    {
        $payload = [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
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

    /**
     * Test User Registration with Mismatching Passwords
     * @return void
     */
    public function test_user_auth_register_password_confirmation(): void
    {
        $payload = [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'password' => self::USER_ORIGINAL_PASSWORD,
            'password_confirmation' => $this->faker->password
        ];
        $response = $this->post(route(self::ROUTE_REGISTER), $payload);

        $response->assertStatus(422)
            ->assertJsonPath('error', __('errors.validation'))
            ->assertJsonPath('errors.password.0', __('validation.confirmed', [
                'attribute' => 'password'
            ]));

        $this->assertDatabaseMissing('users', [
            "email" => $payload['email']
        ]);
    }

    /**
     * Test User Registration with Invalid Email Format
     * @return void
     */
    public function test_user_auth_register_email_format(): void {
        $payload = [
            'name' => $this->faker->name,
            'email' => $this->faker->name,
            'password' => self::USER_ORIGINAL_PASSWORD,
            'password_confirmation' => self::USER_ORIGINAL_PASSWORD
        ];
        $response = $this->post(route(self::ROUTE_REGISTER), $payload);

        $response->assertStatus(422)
            ->assertJsonPath('error', __('errors.validation'))
            ->assertJsonPath('errors.email.0', __('validation.email', [
                'attribute' => 'email'
            ]));

        $this->assertDatabaseMissing('users', [
            "email" => $payload['email']
        ]);
    }

    /**
     * Test User Registration with a Duplicate Email Address
     *
     * @return void
     */
    public function test_user_auth_register_duplicate_email(): void
    {
        $user = User::factory()->create();

        $response = $this->post(route(self::ROUTE_REGISTER), [
            'name' => $user->name,
            'email' => $user->email,
            'password' => self::USER_ORIGINAL_PASSWORD,
            'password_confirmation' => self::USER_ORIGINAL_PASSWORD
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('error', __('errors.validation'))
            ->assertJsonPath('errors.email.0', __('validation.unique', [
                'attribute' => 'email'
            ]));

        $this->assertDatabaseCount('users', 1);
    }

    /**
     * Test Successful User Login
     *
     * @return void
     */
    public function test_user_auth_login(): void {
        $user = User::factory()->create(
            ['password' => bcrypt(self::USER_ORIGINAL_PASSWORD)]
        );

        $response = $this->post(route(self::ROUTE_LOGIN), [
            'email' => $user->email,
            'password' => self::USER_ORIGINAL_PASSWORD,
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'token',
            ]);
    }

    /**
     * Test User Login with Invalid Credentials
     *
     * @return void
     */
    public function test_user_auth_login_invalid_credentials(): void {
        $response = $this->post(route(self::ROUTE_LOGIN), [
            'email' => $this->faker->unique()->safeEmail,
            'password' => $this->faker->name,
        ]);

        $response->assertStatus(401)
            ->assertJsonPath('error', __('auth.failed'));
    }

    /**
     * Test Forgot Password Using an Invalid Email
     *
     * @return void
     */
    public function test_user_auth_forgot_password_invalid_email(): void
    {
        $payload = [
            'email' => $this->faker->name,
        ];
        $response = $this->post(route(self::ROUTE_FORGOT_PASSWORD), $payload);

        $response->assertStatus(422)
            ->assertJsonPath('error', __('errors.validation'))
            ->assertJsonPath('errors.email.0', __('validation.enum', [
                'attribute' => 'email'
            ]));

    }

    /**
     * Test Forgot Password Using a Non-Existent Email Address
     *
     * @return void
     */
    public function test_user_auth_forgot_password_email_not_found(): void
    {
        $payload = [
            'email' => $this->faker->unique()->safeEmail,
        ];
        $response = $this->post(route(self::ROUTE_FORGOT_PASSWORD), $payload);

        $response->assertStatus(422)
            ->assertJsonPath('error', __('errors.validation'))
            ->assertJsonPath('errors.email.0', __('validation.enum', [
                'attribute' => 'email'
            ]));
    }

    /**
     * Test Successful Forgot Password
     *
     * @return void
     * @throws Exception
     */
    public function test_user_auth_forgot_password(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt(self::USER_ORIGINAL_PASSWORD)
        ]);
        $response = $this->post(route(self::ROUTE_FORGOT_PASSWORD), [
            'email' => $user->email
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('message', __('passwords.sent'));

        Notification::assertSentTo($user, ResetPassword::class);
    }

    /**
     * Test Reset Password Using an Invalid Email Address
     *
     * @return void
     */
    public function test_user_auth_reset_password_invalid_email(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt(self::USER_ORIGINAL_PASSWORD)
        ]);
        $token = Password::createToken($user);
        $password = $this->faker->password;

        $response = $this->post(route(self::ROUTE_RESET_PASSWORD, [
            'token' => $token
        ]), [
            'token' => $token,
            'email' => $this->faker->name,
            'password' => $password,
            'password_confirmation' => $password
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('error', __('errors.validation'))
            ->assertJsonPath('errors.email.0', __('validation.email', [
                'attribute' => 'email'
            ]));

        $user->refresh();

        $this->assertFalse(Hash::check($password, $user->password));

        $this->assertTrue(Hash::check(self::USER_ORIGINAL_PASSWORD,
            $user->password));
    }

    /**
     * Test Reset Password with a Non-Existent Email Address
     *
     * @return void
     */
    public function test_user_auth_reset_password_email_not_found(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt(self::USER_ORIGINAL_PASSWORD)
        ]);
        $token = Password::createToken($user);
        $password = $this->faker->password;

        $response = $this->post(route(self::ROUTE_RESET_PASSWORD, [
            'token' => $token
        ]), [
            'token' => $token,
            'email' => $this->faker->unique()->safeEmail,
            'password' => $password,
            'password_confirmation' => $password
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('error', __('errors.validation'))
            ->assertJsonPath('errors.email.0', __('validation.enum', [
                'attribute' => 'email'
            ]));

        $user->refresh();

        $this->assertFalse(Hash::check($password, $user->password));

        $this->assertTrue(Hash::check(self::USER_ORIGINAL_PASSWORD,
            $user->password));
    }

    /**
     * Test Reset Password with Mismatching Passwords
     *
     * @return void
     */
    public function test_user_auth_reset_password_password_mismatch(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt(self::USER_ORIGINAL_PASSWORD)
        ]);
        $token = Password::createToken($user);
        $password = $this->faker->password;
        $password_confirmation = $this->faker->password;

        $response = $this->post(route(self::ROUTE_RESET_PASSWORD, [
            'token' => $token
        ]), [
            'token' => $token,
            'email' => $user->email,
            'password' => $password,
            'password_confirmation' => $password_confirmation
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('error', __('errors.validation'))
            ->assertJsonPath('errors.password.0', __('validation.confirmed', [
                'attribute' => 'password'
            ]));

        $user->refresh();

        $this->assertFalse(Hash::check($password, $user->password));

        $this->assertTrue(Hash::check(self::USER_ORIGINAL_PASSWORD,
            $user->password));
    }

    /**
     * Test Reset Password with an Invalid Token
     *
     * @return void
     */
    public function test_user_auth_reset_password_invalid_token(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt(self::USER_ORIGINAL_PASSWORD)
        ]);
        $token = $this->faker->password;
        $password = $this->faker->password;

        $response = $this->post(route(self::ROUTE_RESET_PASSWORD, [
            'token' => $token
        ]), [
            'token' => $token,
            'email' => $user->email,
            'password' => $password,
            'password_confirmation' => $password
        ]);

        $response->assertStatus(400)
            ->assertJsonPath('error', __('errors.validation'))
            ->assertJsonPath('errors.email.0', __('passwords.token'));

        $user->refresh();

        $this->assertFalse(Hash::check($password, $user->password));

        $this->assertTrue(Hash::check(self::USER_ORIGINAL_PASSWORD,
            $user->password));
    }

    /**
     * Test Successful Password Reset
     *
     * @return void
     * @throws Exception
     */
    public function test_user_auth_reset_password(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt(self::USER_ORIGINAL_PASSWORD)
        ]);
        $token = Password::createToken($user);
        $password = $this->faker->password;

        $response = $this->post(route(self::ROUTE_RESET_PASSWORD, [
            'token' => $token
        ]), [
            'token' => $token,
            'email' => $user->email,
            'password' => $password,
            'password_confirmation' => $password
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('message', __('passwords.reset'));

        $user->refresh();

        $this->assertFalse(Hash::check(self::USER_ORIGINAL_PASSWORD, $user->password));

        $this->assertTrue(Hash::check($password, $user->password));

        Notification::assertSentTo($user, PasswordResetSuccess::class);
    }

}
