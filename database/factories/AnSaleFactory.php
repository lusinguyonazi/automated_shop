<?php

namespace Database\Factories;

use App\Models\AnSale;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AnSale>
 */
class AnSaleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */

    protected $model = AnSale::class;

    public function definition()
    {
        return [
            'shop_id' => 1, 
            'user_id' => 2, 
            'customer_id' => 1, 
            'currency' => 'USD', 
            'defcurr' => 'USD', 
            'ex_rate' => 1.0000000, 
            'sale_amount' => $this->faker->randomNumber(7, true), 
            'sale_discount' =>0.00 , 
            'sale_amount_paid' => $this->faker->randomNumber(6, true), 
            'time_paid' => now(), 
            'tax_amount' => 0.00, 
            'status' => 'paid', 
            'pay_type' => 'cash', 
            'comments' => NULL, 
            'time_created' => now(), 
            'sync_id' => NULL,  
            'sale_type' => 'cash', 
            'sale_no' => 2, 
            'grade_id' => NULL, 
            'year' => NULL,
            'is_deleted' => 1,
        ];
    }
}
