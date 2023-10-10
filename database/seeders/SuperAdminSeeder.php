<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            'first_name' => 'Super',
            'last_name' => 'Admin',
            'email' => 'hello@manilastyles.com',
            'password' => Hash::make('z5Y0d98JiWeQ')
        ];

        $user = User::firstOrCreate(['email' => $data['email']], $data);

        $permissions = config('testing.superadmin');

        $user->permissions()->createMany($permissions);
    }
}
