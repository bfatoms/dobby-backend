<?php

namespace Tests;

use Illuminate\Contracts\Auth\Authenticatable;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;

abstract class BaseTest extends TestCase
{
    public static $email = "";

    public static $pass = "password";

    public function comment($message)
    {
        fwrite(STDOUT, "\n".$message."\t");
    }

    public function actingAs(Authenticatable $user, $driver = null)
    {
        $token = JWTAuth::fromUser($user);

        $this->withHeader('Authorization', 'Bearer ' . $token);

        return $this;
    }

    public function adminWithPermissions()
    {
        $user = User::factory()->create();

        $permissions = config('permissions');

        foreach ($permissions as $module => $actions) {
            foreach ($actions as $action) {
                $user->permissions()->firstOrCreate([
                    'module' => $module,
                    'action' => $action
                ]);
            }
        }

        // $user->permissions()->firstOrCreateMany($permissionsToCreate);

        return $user;
    }

    public function userWithoutPermissions()
    {
        return User::factory()->create();
    }

    public function seedDatabase()
    {
        return Artisan::call('db:seed', ['--env' => 'testing']);
    }
}
