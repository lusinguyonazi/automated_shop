<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BusinessType;
use App\Models\BusinessSubType;

class BusinessTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $btypes = array(
            ["type" => "Manufacturing Business","description" => "This Includes all Millings, Bakeries, and other manufacturing business","type_sw" => "Biashara ya Uzalishaji","description_sw" => "Hii ni pamoja na biashara zote za uzalishaji kama watengeneza mkate, fanicha, sabuni, unga wa mahindi, mafuta ya alizeti, vinywaji na wanaozalisha mbogamboga na matunda, nk.","type_icon" => ""],
            ["type" => "Merchandising Business","description" => "This includes all business involving buying and selling goods eg Clothing Shops, Food and Drinks Shops etc","type_sw" => "Biashara ya Kununua na Kuuza","description_sw" => "Hii inahusisha  biashara zote za kununua na kuuza kama vile maduka ya nguo, viatu, vifaa vya ujenzi, vifaa vya umeme, vipuri, pembejeo za kilimo na mifugo, maduka ya dawa, n.k","type_icon" => ""],
            ["type" => "Service Business","description" => "This include all business providing services. Eg Car wash, Barbershops, etc","type_sw" => "Biashara ya Huduma","description_sw" => "Hii ni pamoja na biashara zote za huduma kama vile kuosha gari, karakana, saluni, stationary, zahanati, nk.","type_icon" => ""],
            ["type" => "Both 2 & 3","description" => "These includes all business that involves selling goods and also selling services like Stationary, Garages etc.","type_sw" => "Zote 2 & 3","description_sw" => "Hii ni pamoja na biashara yote ambayo inajumuisha kuuza bidhaa na uuzaji wa huduma kama stationary, garage nk.","type_icon" => ""]
        );

        foreach ($btypes as $key => $bt) {
            BusinessType::create($bt);
        }


        $bsubtypes = array(
            ['business_type_id' => 2, 'name' => 'SuperMarket', 'description' => '', 'name_sw' => 'Supermarket', 'description_sw' => ''],
            ['business_type_id' => 2, 'name' => 'Pharmacy', 'description' => '', 'name_sw' => 'Duka la Dawa', 'description_sw' => ''],
            ['business_type_id' => 2, 'name' => 'SpareParts', 'description' => '', 'name_sw' => 'Vipuri', 'description_sw' => ''],
            ['business_type_id' => 2, 'name' => 'Hardware', 'description' => '', 'name_sw' => 'Vifaa vya ujenzi', 'description_sw' => ''],
            ['business_type_id' => 2, 'name' => 'Agrovet', 'description' => '', 'name_sw' => 'Zana za Kilimo na Mifugo', 'description_sw' => '']
        );

        foreach ($bsubtypes as $key => $sbt) {
            BusinessSubType::create($sbt);
        }
    }
}
