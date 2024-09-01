<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Taxcode;

class TaxcodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $taxcodes = array(
            ['code' => 'A', 'name' => 'Standard (A-18)', 'value' => '18'],
            ['code' => 'B', 'name' => 'Special rate(B-10)', 'value' => '10'],
            ['code' => 'C', 'name' => 'Zero Rated (C-0)', 'value' => '0'],
            ['code' => 'D', 'name' => 'Special Relief(D-SR)', 'value' => 'SR'],
            ['code' => 'E', 'name' => 'Exempted(E-EX)', 'value' => 'EX']
        );

        foreach ($taxcodes as $key => $tc) {
            Taxcode::create($tc);
        }
    }
}