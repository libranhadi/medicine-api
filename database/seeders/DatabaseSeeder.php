<?php

namespace Database\Seeders;

use App\Models\Clinic;
use App\Models\User;
use Illuminate\Database\Seeder;
use Database\Seeders\RoleSeeder;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;
    /**
     * Seed the application's database.
     */
    public function run()
    {
        // User::factory(10)->create();
        $clinic = Clinic::where('name', 'TST_1')->first();
        if (empty($clinic)) {
            $clinic = new Clinic();
        }
        $clinic->name = "TEST";
        $clinic->code = "TST_1";
        $clinic->address = "South Jakarta";
        $clinic->save();

        $user = User::where('username', 'Staff')->first();
        if (empty($user)) {
            $user = new User();
        }
        $user->name = "Staff";
        $user->username = "staff";
        $user->email = "stf_adm@gmail.com";
        $user->password = Hash::make('password');
        $user->clinic_id = $clinic->id;
        $user->save();

        $role = Role::where("name", "Staff")->first();
        if (empty($role)) {
            $role = Role::create(['name'=> "Staff"]);
        }
        $user->assignRole($role);

        $this->call([RoleSeeder::class]);

    }
}
