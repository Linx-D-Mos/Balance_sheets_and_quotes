<?php

namespace Database\Seeders;

use App\Enums\RoleEnum;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
       $this->call([
            RolesAndPermissionsSeeder::class,
            ProjectStatusesSeeder::class,
            QuoteStatusesSeeder::class,
            MaterialCategoriesSeeder::class,
            GlobalSettingsSeeder::class,
            LaborRoleSeeder::class,
            FixedExpenseSeeder::class,
            ClientSeeder::class,
            EmployeeSeeder::class,
        ]);
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => 'password'
        ])->assignRole(RoleEnum::ADMINISTRATOR->value);
    }
}
