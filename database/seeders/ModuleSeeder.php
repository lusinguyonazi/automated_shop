<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Module;

class ModuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $modules = array(
            ['name' => 'Production', 'display_name' => 'Production', 'description' => '', 'price' => 15000, 'duration' => 'Monthly']);

        foreach ($modules as $key => $value) {
            Module::create($value);
        }
    }
}
