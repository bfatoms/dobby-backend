<?php

namespace Tests\Unit;

use App\Mails\InviteUserMail;
use App\Models\User;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Mockery;
use Tests\BaseTest;

class AuthenticationTest extends BaseTest
{
    use RefreshDatabase;

    public function test_can_login()
    {
        $user = User::factory()->create();

        $response = $this->json('POST', 'api/auth/login', [
            'email' => $user['email'],
            'password' => 'password'
        ]);

        $response->assertStatus(200);

        $response->assertSee($user['email']);
        
        $response->assertSee('access_token');

        $response->assertSee('expires_in');

        $response->assertSee('token_type');

        $response->assertSee('LOGIN_SUCCESSFUL');
    }

    public function test_login_email_is_required()
    {
        $response = $this->json('POST', 'api/auth/login', [
            'password' => 'password'
        ]);

        $response->assertStatus(422);

        $response->assertSee('EMAIL_REQUIRED');
    }

    public function test_login_email_is_invalid()
    {
        $response = $this->json('POST', 'api/auth/login', [
            'email' => 'InvalidEmail@louie@test.com',
            'password' => 'password'
        ]);

        $response->assertStatus(422);

        $response->assertSee('EMAIL_MUST_BE_VALID_EMAIL_ADDRESS');
    }

    public function test_login_password_is_required()
    {
        $user = User::factory()->make();

        $response = $this->json('POST', 'api/auth/login', [
            'email' => $user['email'],
        ]);

        $response->assertStatus(422);

        $response->assertSee('PASSWORD_REQUIRED');
    }

    public function test_can_forget_password()
    {
        $user = User::factory()->create();

        $response = $this->json('POST', 'api/auth/forgot-password', [
            'email' => $user['email'],
            'browser' => "Mac OS",
            'operating_system' => "Mac OS"
        ]);

        $response->assertStatus(200);

        $response->assertSee('RESET_PASSWORD_SENT');
    }

    public function test_can_check_reset_password()
    {
        $user = User::factory()->create();

        $password_reset = $user->passwordResets()->create([
            'until' => now()->addDay(1)->toDateTimeString(),
        ]);

        $response = $this->json('GET', "api/auth/reset-password/{$password_reset['id']}/check");

        $response->assertStatus(200);

        $response->assertSee('true');

        $response->assertSee('TOKEN_VERIFIED');
    }

    public function test_reset_password_is_expired()
    {
        $user = User::factory()->create();

        $password_reset = $user->passwordResets()->create([
            'until' => now()->subDay(1)->toDateTimeString(),
        ]);

        $response = $this->json('GET', "api/auth/reset-password/{$password_reset['id']}/check");

        $response->assertStatus(422);

        $response->assertSee('false');

        $response->assertSee('TOKEN_EXPIRED');
    }

    public function testResetPasswordCheckIsInvalid()
    {
        $response = $this->json('GET', "api/auth/reset-password/INVALID/check");

        $response->assertStatus(422);

        $response->assertSee('false');

        $response->assertSee('TOKEN_INVALID');
    }

    public function testResetPasswordIsHappy()
    {
        $user = User::factory()->create();

        $password_reset = $user->passwordResets()->create([
            'until' => now()->addDay(1)->toDateTimeString(),
        ]);

        $response = $this->json('PUT', "api/auth/reset-password/{$password_reset['id']}", [
            'password' => 'p@sSW0Rd',
            'password_confirmation' => 'p@sSW0Rd',
            'browser' => 'chrome',
            'operating_system' => 'Mac OS'
        ]);

        $response->assertStatus(200);

        $response->assertSee('PASSWORD_CHANGE_SUCCESSFUL');
    }

    public function testResetPasswordBrowserIsRequired()
    {
        $user = User::factory()->create();

        $password_reset = $user->passwordResets()->create([
            'until' => now()->addDay(1)->toDateTimeString(),
        ]);

        $response = $this->json('PUT', "api/auth/reset-password/{$password_reset['id']}", [
            'password' => 'p@sSW0Rd',
            'password_confirmation' => 'p@sSW0Rd',
            'operating_system' => 'Mac OS'
        ]);

        $response->assertStatus(422);

        $response->assertSee('BROWSER_REQUIRED');
    }

    public function testResetPasswordOperatingSystemIsRequired()
    {
        $user = User::factory()->create();

        $password_reset = $user->passwordResets()->create([
            'until' => now()->addDay(1)->toDateTimeString(),
        ]);

        $response = $this->json('PUT', "api/auth/reset-password/{$password_reset['id']}", [
            'password' => 'p@sSW0Rd',
            'password_confirmation' => 'p@sSW0Rd',
            'browser' => 'chrome',
        ]);

        $response->assertStatus(422);

        $response->assertSee('OPERATING_SYSTEM_REQUIRED');
    }

    public function testResetPasswordConfirmationIsRequired()
    {
        $user = User::factory()->create();

        $password_reset = $user->passwordResets()->create([
            'until' => now()->addDay(1)->toDateTimeString(),
        ]);

        $response = $this->json('PUT', "api/auth/reset-password/{$password_reset['id']}", [
            'password' => 'p@sSW0Rd',
            'browser' => 'chrome',
            'operating_system' => 'Mac OS'
        ]);

        $response->assertStatus(422);

        $response->assertSee('PASSWORD_CONFIRMATION_IS_REQUIRED_WITH_PASSWORD');
    }

    public function testResetPasswordConfirmationMustMatch()
    {
        $user = User::factory()->create();

        $password_reset = $user->passwordResets()->create([
            'until' => now()->addDay(1)->toDateTimeString(),
        ]);

        $response = $this->json('PUT', "api/auth/reset-password/{$password_reset['id']}", [
            'password' => 'p@sSW0Rd',
            'password_confirmation' => 'password',
            'browser' => 'chrome',
            'operating_system' => 'Mac OS'
        ]);

        $response->assertStatus(422);

        $response->assertSee('PASSWORD_AND_CONFIRMATION_MUST_MATCH');
    }

    public function testRegistrationSendsInviteEmail()
    { 
        $this->actingAs($this->adminWithPermissions());

        $data = [
            'email' => 'gg@tester.test',
            'first_name' => 'Gabbi',
            'last_name' => 'Garcia',
            'password' => 'GabGarTest143',
            'origin' => 'test',
            'token' => 'asdasdas',
            'referrer' => 'web'
        ];

        $response = $this->post('api/users/invite', $data);

        $response->assertStatus(200);

        $this->assertDatabaseHas('users', ['first_name' => 'Gabbi']);
    }
}
