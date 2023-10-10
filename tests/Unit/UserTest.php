<?php

namespace Tests\Unit;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\AdminBaseTest;

class UserTest extends AdminBaseTest
{
    use RefreshDatabase;

    public function testUserIndexIsHappy()
    {
        User::factory()->count(3)->create();

        $response = $this->get('/api/users');

        $response->assertStatus(200);

        $response->assertSee("email");

        $response->assertSee("first_name");
    }

    public function testUserCreateIsHappy()
    {
        $user = User::factory()->make()->toArray();

        $user['password'] = 'password';

        $response = $this->post('/api/users', $user);

        $response->assertStatus(200);

        $response->assertSee("RESOURCE_CREATED");
    }

    public function testUserCreateFieldsAreRequired()
    {
        $user = User::factory()->make()->toArray();

        $user['password'] = 'password';

        $user['first_name'] = '';
        
        $user['last_name'] = '';
        
        $user['email'] = '';
        
        $response = $this->post('/api/users', $user);

        $response->assertStatus(422);

        $response->assertSee("EMAIL_REQUIRED");

        $response->assertSee("FIRST_NAME_REQUIRED");

        $response->assertSee("LAST_NAME_REQUIRED");
    }

    public function testUserCreateEmailMustBeAValidEmail()
    {
        $user = User::factory()->make()->toArray();

        $user['password'] = 'password';
        
        $user['email'] = 'louie@louie@louie@louie';
        
        $response = $this->post('/api/users', $user);

        $response->assertStatus(422);

        $response->assertSee("EMAIL_MUST_BE_A_VALID_EMAIL");
    }


    public function testUserShowIsHappy()
    {
        $user = User::factory()->create();

        $response = $this->get("/api/users/{$user['id']}");

        $response->assertStatus(200);

        $response->assertSee("RESOURCE_FOUND");
    }

    public function testUserUpdateIsHappy()
    {
        $user = User::factory()->create()->toArray();

        $user['first_name'] = 'charlene';

        $response = $this->put("/api/users/{$user['id']}", $user);

        $response->assertStatus(200);

        $response->assertSee("RESOURCE_UPDATED");
    }

    public function testUserUpdateFieldsAreRequired()
    {
        $user = User::factory()->create()->toArray();

        $user['first_name'] = '';
        
        $user['last_name'] = '';
        
        $user['email'] = '';

        $response = $this->put("/api/users/{$user['id']}", $user);

        $response->assertStatus(422);

        $response->assertSee("EMAIL_REQUIRED");

        $response->assertSee("FIRST_NAME_REQUIRED");

        $response->assertSee("LAST_NAME_REQUIRED");
    }

    public function testUserUpdateEmailMustBeAValidEmail()
    {
        $user = User::factory()->create()->toArray();

        $user['email'] = 'test@louie@louie@';

        $response = $this->put("/api/users/{$user['id']}", $user);

        $response->assertStatus(422);

        $response->assertSee("EMAIL_MUST_BE_A_VALID_EMAIL");
    }

    public function testUserTrashIsHappy()
    {
        $user = User::factory()->create();

        $response = $this->delete("/api/users/{$user['id']}");

        $response->assertStatus(200);

        $response->assertSee("RESOURCE_TRASHED");
    }

    public function testUserUploadAvatar()
    {
        $user = User::factory()->create();

        $file = UploadedFile::fake()->create('avatar.png');
        
        $response = $this->post("/api/users/{$user['id']}/avatar", [
            'file' => $file,
        ]);

        $response->assertStatus(200);
        
        $response->assertSee("url");
    }

    public function testUserUploadAvatarAgain()
    {
        $user = User::factory()->create();

        $file = UploadedFile::fake()->create('avatar.png');

        $this->post("/api/users/{$user['id']}/avatar", [
            'file' => $file
        ]);

        $file2 = UploadedFile::fake()->create('avatar2.png');

        $response = $this->post("/api/users/{$user['id']}/avatar", [
            'file' => $file2
        ]);

        $response->assertStatus(200);
        
        $response->assertSee("avatar2");
    }

    public function testUserUploadMultipleAvatar()
    {
        $user = User::factory()->create();

        $file = UploadedFile::fake()->create('avatar.png');

        $file2 = UploadedFile::fake()->create('avatar2.png');

        $response = $this->post("/api/users/{$user['id']}/avatar", [
            'file' => [$file, $file2]
        ]);

        $response->assertStatus(200);
        
        $response->assertSee("avatar2");

        $this->assertDatabaseHas('attachments', [
            'name' => 'avatar.png',
            'name' => 'avatar2.png',
        ]);
    }


    public function testUserAvatarDeleteNoAvatar()
    {
        $user = User::factory()->create();

        $response = $this->delete("/api/users/{$user['id']}/avatar");

        $response->assertStatus(422);
        
        $response->assertSee("NO_AVATAR_DELETED");
    }

    public function testUserAvatarDeleteWithAvatar()
    {
        $user = User::factory()->create();

        $file = UploadedFile::fake()->create('avatar.png');

        $this->post("/api/users/{$user['id']}/avatar", [
            'file' => $file
        ]);

        $response = $this->delete("/api/users/{$user['id']}/avatar");

        $response->assertStatus(200);
        
        $response->assertSee("AVATAR_DELETED");
    }

    public function testUserInviteIsHappy()
    {
        $data = User::factory()->make()->toArray();

        $data['password'] = "password";

        $response = $this->json('POST', 'api/users/invite', $data);
 
        $response->assertStatus(200);

        $response->assertSee("data");

        $response->assertSeeText($data['email']);
    }

    public function testUserInviteEmailAlreadyRegistered()
    {
        $data = User::factory()->make()->toArray();

        $data['password'] = "password";

        $this->json('POST', 'api/users/invite', $data);

        $response = $this->json('POST', 'api/users/invite', $data);

        $response->assertStatus(422);

        $response->assertSee("EMAIL_ALREADY_REGISTERED");
    }

    public function testUserInviteEmailVerificationIsHappy()
    {
        $user = User::factory()->create();

        $token = (string)$user['verification_token'];

        $response = $this->json('GET', "api/auth/verify/$token");

        $response->assertStatus(302);
    }

    public function testUserInviteInvalidEmail()
    {
        $data = User::factory()->make()->toArray();

        $data['email'] = "unknownEmail";

        $data['password'] = "password";

        $response = $this->json('POST', 'api/users/invite', $data);

        $response->assertStatus(422);

        $response->assertSee('EMAIL_MUST_BE_VALID_EMAIL_ADDRESS');
    }

    public function testUserInviteEmailIsRequired()
    {
        $data = User::factory()->make()->toArray();

        unset($data['email']);

        $data['password'] = "password";

        $response = $this->json('POST', 'api/users/invite', $data);

        $response->assertStatus(422);

        $response->assertSee('EMAIL_REQUIRED');
    }

    public function testUserInvitePasswordIsRequired()
    {
        $data = User::factory()->make()->toArray();

        $response = $this->json('POST', 'api/users/invite', $data);

        $response->assertStatus(422);

        $response->assertSee('PASSWORD_REQUIRED');
    }

    public function testUserInviteFirstNameIsRequired()
    {
        $data = User::factory()->make()->toArray();

        unset($data['first_name']);

        $data['password'] = "password";

        $response = $this->json('POST', 'api/users/invite', $data);

        $response->assertStatus(422);

        $response->assertSee('FIRST_NAME_REQUIRED');
    }

    public function testUserInviteLastNameIsRequired()
    {
        $data = User::factory()->make()->toArray();

        unset($data['last_name']);

        $data['password'] = "password";

        $response = $this->json('POST', 'api/users/invite', $data);

        $response->assertStatus(422);

        $response->assertSee('LAST_NAME_REQUIRED');
    }
}
