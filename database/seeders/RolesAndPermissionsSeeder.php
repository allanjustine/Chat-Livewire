<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $accessAllPages = Permission::firstOrCreate(['name' => 'access-all-pages']);
        $accessUserPages = Permission::firstOrCreate(['name' => 'access-user-pages']);

        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $adminRole->syncPermissions([$accessAllPages, $accessUserPages]);

        $userRole = Role::firstOrCreate(['name' => 'user']);
        $userRole->syncPermissions([$accessUserPages]);



        $admin = User::firstOrCreate([
            'email' => 'superadmin@gmail.com',
        ], [
            'name' => 'Super Administrator',
            'username' => 'superadmin',
            'email_verified_at' => now(),
            'password' => bcrypt('password'),
        ]);

        $admin->assignRole([$adminRole, $userRole]);

        $user = User::firstOrCreate([
            'email' => 'johndoe@gmail.com',
        ], [
            'name' => 'John Doe',
            'username' => 'johndoe',
            'email_verified_at' => now(),
            'password' => bcrypt('password'),
        ]);
        $user->assignRole([$userRole]);
    }
}
