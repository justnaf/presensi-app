<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $adminUser = User::create([
            'username' => 'snafcat',
            'name' => 'Naufal',
            'email' => 'naufalaf86@gmail.com',
            'password' => Hash::make('123'),
        ]);
        // User::factory(10)->create();
        $admin = Role::create(['name' => 'Administrator']);
        Role::create(['name' => 'Pengguna']);

        $permissions = [
            'edit roles',
            'add roles',
            'delete roles',
            'read roles',
            'add institutions',
            'edit institutions',
            'delete institutions',
            'read institutions',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }
        // Sync all permissions to the admin role
        $admin->syncPermissions(Permission::all());
        $adminUser->assignRole($admin);
    }
}
