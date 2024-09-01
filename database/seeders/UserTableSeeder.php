<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use App\Models\User;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::create([
            'first_name' => 'Shabani',
            'last_name' => 'Mtaita',
            'phone' => '0789362813',
            'email' => 's.mtaita@ovaltechtz.com',
            'password' => bcrypt('Admin123'),
            'created_at' => \Carbon\Carbon::now(),
            'updated_at' => \Carbon\Carbon::now(),
        ]);

        $role = Role::where('name', 'super_admin')->first();
        $user->assignRole($role);
    }
}
