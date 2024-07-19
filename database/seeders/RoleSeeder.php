<?php

namespace Database\Seeders;

use App\Models\User;
use Database\Seeders\RoleSeeder\StaffClinicRole;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $seeder = new StaffClinicRole();
        $seeder->seed();
    }
}
