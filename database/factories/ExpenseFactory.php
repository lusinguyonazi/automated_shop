<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Expense>
 */
class ExpenseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        
        return [
            'shop_id' => 1,
             'user_id'=>2,
              'expense_category_id' => NULL,
               'expense_type' => 'Bill', 
               'amount' => $this->faker->randomNumber(7, true), 
               'no_days' => 30, 
               'amount_paid' => $this->faker->randomNumber(7, true), 
                'account' => 'Cash', 
                'exp_vat' => NULL, 
                'wht_rate' => NULL, 
                'wht_amount' => NULL, 
                'time_created' =>now(), 
                'description' => NULL, 
                'supplier_id' => NULL, 
                'order_no' => NULL, 
                'pv_no' => NULL, 
                'invoice_no' => NULL, 
                'exp_type' => 'Umeme Maji Nk', 
                'status' => 'Paid', 
                'trans_id' => NULL,
                'is_deleted' => 1, 
                'category_id' => NULL,

            //
        ];
    }
}
