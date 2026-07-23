<?php

namespace Database\Seeders;

use App\Enums\AppPermissionEnum;
use App\Enums\RoleEnum;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Limpiar caché de Spatie
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        foreach (AppPermissionEnum::cases() as $permission) {
            Permission::findOrCreate($permission->value, 'web');
        }

        $adminRole = Role::firstOrCreate(['name' => RoleEnum::ADMINISTRATOR->value]);
        $adminRole->syncPermissions(Permission::all());
    }
}
