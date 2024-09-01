<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(RoleAndPermissionsSeeder::class);
        $this->call(UserTableSeeder::class);
        $this->call(SubscriptionSeeder::class);
        $this->call(BusinessTypeSeeder::class);
        $this->call(TaxcodeSeeder::class);
        $this->call(ModuleSeeder::class);
    }
}
