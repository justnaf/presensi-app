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
        $penggunaUser = User::create([
            'username' => 'larrry_Ap',
            'name' => 'Larry',
            'email' => 'larrry.ap@example.com',
            'password' => Hash::make('123'),
        ]);
        // User::factory(10)->create();
        $admin = Role::create(['name' => 'Administrator']);
        $penggunaRole = Role::create(['name' => 'Pengguna']);

        $permissions = [
            'edit roles',
            'view roles',
            'delete roles',
            'create roles',
            'view institutions',
            'edit institutions',
            'delete institutions',
            'create institutions',
            'view events',
            'edit events',
            'delete events',
            'create events',
            'view users',
            'edit users',
            'delete users',
            'create users',

        ];

        $userPermissions = [
            'join activities',
            'view activities',
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm]);
        }
        foreach ($userPermissions as $userperm) {
            Permission::firstOrCreate(['name' => $userperm]);
        }
        // Sync all permissions to the admin role
        $penggunaRole->syncPermissions($userPermissions);
        $admin->syncPermissions(Permission::all());
        $adminUser->assignRole($admin);
        $penggunaUser->assignRole($penggunaRole);
    }
}
